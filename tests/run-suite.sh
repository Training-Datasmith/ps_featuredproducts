#!/bin/sh
set -eu

REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
PHP54_IMAGE="php@sha256:d9eaac6344eb9399f053ef6b538a237101d686d866fb484ad4834bfada6afbd9"
PHP56_IMAGE="php@sha256:6ce95208609dc66df163ab936c970b3b34cd901b85c747102c5999f08ade9143"
EXPECTED_TEST_FILES=11
EXPECTED_TEST_METHODS=52
GATE_ASSERTIONS=""
ARTIFACT="/opt/cursor/artifacts/suite-results.md"
PHAR_HOST="/opt/phpunit-phar/phpunit-4.8.36.phar"
GATE_TMPDIR="$(mktemp -d)"
trap 'rm -rf "$GATE_TMPDIR"' EXIT

TEST_FILE_LIST="
Unit/DirectAccessGuardTest.php
Unit/PsFeaturedProductsConstructorTest.php
Unit/ConfigXmlTest.php
Unit/TemplateContractTest.php
Unit/PsFeaturedProductsInstallTest.php
Unit/PsFeaturedProductsCacheHooksTest.php
Unit/PsFeaturedProductsGetContentTest.php
Unit/PsFeaturedProductsRenderFormTest.php
Unit/PsFeaturedProductsWidgetTest.php
Unit/PsFeaturedProductsGetProductsTest.php
Unit/UpgradeModuleTest.php
"

select_php_image() {
    if docker pull --platform=linux/amd64 "$PHP54_IMAGE" >/dev/null 2>&1; then
        SELECTED_IMAGE="$PHP54_IMAGE"
        SELECTED_LABEL="php-5.4-cli"
        PHP_PULL_NOTE="used PHP 5.4 digest image"
        return 0
    fi
    docker pull --platform=linux/amd64 "$PHP56_IMAGE" >/dev/null
    SELECTED_IMAGE="$PHP56_IMAGE"
    SELECTED_LABEL="php-5.6.40-cli"
    PHP_PULL_NOTE="PHP 5.4 digest pull failed (schema v1 manifest); used PHP 5.6.40 fallback"
}

verify_inventory() {
    FOUND="$(find "$REPO_ROOT/tests/Unit" -maxdepth 1 -name '*Test.php' | wc -l | tr -d ' ')"
    test "$FOUND" = "$EXPECTED_TEST_FILES"
    for rel in $TEST_FILE_LIST; do
        test -f "$REPO_ROOT/tests/$rel"
    done
}

write_temp_phpunit() {
    ORDER_FILE="$1"
    OUT_XML="$2"
    {
        echo '<?xml version="1.0" encoding="UTF-8"?>'
        echo '<phpunit bootstrap="/src/tests/bootstrap.php" colors="true">'
        echo '  <testsuites>'
        echo '    <testsuite name="ps_featuredproducts">'
        while read -r rel; do
            test -n "$rel"
            echo "      <file>/src/tests/$rel</file>"
        done < "$ORDER_FILE"
        echo '    </testsuite>'
        echo '  </testsuites>'
        echo '</phpunit>'
    } > "$OUT_XML"
}

make_seeded_order() {
    docker run --rm --platform=linux/amd64 -i "$SELECTED_IMAGE" php -r '
$files = array_filter(explode("\n", trim(stream_get_contents(STDIN))));
srand(20261008);
shuffle($files);
echo implode("\n", $files), "\n";
' < "$DEFAULT_ORDER"
}

run_phpunit_once() {
    ORDER_FILE="$1"
    RUN_LABEL="$2"
    TMP_XML="$GATE_TMPDIR/gate-${RUN_LABEL}.xml"
    write_temp_phpunit "$ORDER_FILE" "$TMP_XML"
    set +e
    OUTPUT="$(docker run --rm --network=none \
        --platform=linux/amd64 \
        -v "$REPO_ROOT":/src:ro \
        -v "$GATE_TMPDIR":/gate:ro \
        -v "$PHAR_HOST":/opt/phpunit.phar:ro \
        "$SELECTED_IMAGE" \
        php /opt/phpunit.phar --configuration "/gate/gate-${RUN_LABEL}.xml" --colors=never 2>&1)"
    PHPUNIT_STATUS=$?
    set -e
    echo "$OUTPUT"
    if [ "$PHPUNIT_STATUS" -ne 0 ]; then
        echo "phpunit failed with status $PHPUNIT_STATUS" >&2
        exit "$PHPUNIT_STATUS"
    fi
    echo "$OUTPUT" | grep -E '^OK \([0-9]+ tests, [0-9]+ assertions\)$' >/dev/null
    COUNT="$(echo "$OUTPUT" | sed -n 's/^OK (\([0-9]*\) tests,.*/\1/p')"
    ASSERTIONS="$(echo "$OUTPUT" | sed -n 's/^OK ([0-9]* tests, \([0-9]*\) assertions)$/\1/p')"
    test "$COUNT" = "$EXPECTED_TEST_METHODS"
    if [ -z "$GATE_ASSERTIONS" ]; then
        GATE_ASSERTIONS="$ASSERTIONS"
    else
        test "$GATE_ASSERTIONS" = "$ASSERTIONS"
    fi
    echo "$RUN_LABEL: OK ($COUNT tests, $ASSERTIONS assertions)"
}

mkdir -p /opt/cursor/artifacts
select_php_image
verify_inventory

VERIFIER_ID="$(docker build --platform=linux/amd64 -q -f "$REPO_ROOT/tests/verifier.Dockerfile" "$REPO_ROOT")"
docker run --rm -v /opt/phpunit-phar:/opt/phpunit-phar "$VERIFIER_ID" /usr/local/bin/fetch-and-verify-phpunit
test -f "$PHAR_HOST"

PHP_VERSION="$(docker run --rm --network=none --platform=linux/amd64 "$SELECTED_IMAGE" php -r 'echo PHP_VERSION;')"
case "$PHP_VERSION" in
    5.4.*|5.6.40) ;;
    *) echo "unexpected PHP version: $PHP_VERSION" >&2; exit 1 ;;
esac

DEFAULT_ORDER="$(mktemp)"
cat <<'EOF' > "$DEFAULT_ORDER"
Unit/DirectAccessGuardTest.php
Unit/PsFeaturedProductsConstructorTest.php
Unit/ConfigXmlTest.php
Unit/TemplateContractTest.php
Unit/PsFeaturedProductsInstallTest.php
Unit/PsFeaturedProductsCacheHooksTest.php
Unit/PsFeaturedProductsGetContentTest.php
Unit/PsFeaturedProductsRenderFormTest.php
Unit/PsFeaturedProductsWidgetTest.php
Unit/PsFeaturedProductsGetProductsTest.php
Unit/UpgradeModuleTest.php
EOF
SEEDED_ORDER_1="$(mktemp)"
make_seeded_order > "$SEEDED_ORDER_1"
SEEDED_ORDER_2="$(mktemp)"
make_seeded_order > "$SEEDED_ORDER_2"

run_phpunit_once "$DEFAULT_ORDER" "default-order-1"
run_phpunit_once "$DEFAULT_ORDER" "default-order-2"
run_phpunit_once "$SEEDED_ORDER_1" "seeded-order-1"
run_phpunit_once "$SEEDED_ORDER_2" "seeded-order-2"

BRANCH="$(git -C "$REPO_ROOT" rev-parse --abbrev-ref HEAD)"
SHA="$(git -C "$REPO_ROOT" rev-parse HEAD)"
DATE="$(date -u +%Y-%m-%dT%H:%M:%SZ)"

cat > "$ARTIFACT" <<EOF
# ps_featuredproducts suite results

- repo: Training-Datasmith/ps_featuredproducts
- date: $DATE
- branch: $BRANCH
- commit: $SHA
- php image: $SELECTED_LABEL ($SELECTED_IMAGE)
- php version: $PHP_VERSION
- phpunit: 4.8.36 (verified PHAR)
- expected test files: $EXPECTED_TEST_FILES
- expected test methods: $EXPECTED_TEST_METHODS
- gate runs: default-order x2, seeded-order x2 (srand 20261008, fresh shuffle per seeded run)
- command: tests/run-suite.sh
- tests: $EXPECTED_TEST_METHODS
- assertions: $GATE_ASSERTIONS
- failures: 0
- errors: 0
- skipped: 0
- incomplete: 0
- notes: $PHP_PULL_NOTE; PHPUnit PHAR mounted read-only from /opt/phpunit-phar
EOF
