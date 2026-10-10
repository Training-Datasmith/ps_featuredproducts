#!/bin/sh
set -eu

REPO_ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
TARGET="$REPO_ROOT/ps_featuredproducts.php"
PHAR_HOST="/opt/phpunit-phar/phpunit-4.8.36.phar"
EXPECTED_TEST="testAllProductsLinkUsesStoredCategoryNotRequestOverride"
BACKUP="$(mktemp)"
cp "$TARGET" "$BACKUP"
trap 'cp "$BACKUP" "$TARGET"; rm -f "$BACKUP"' EXIT

if ! test -f "$PHAR_HOST"; then
    echo "PHPUnit PHAR missing at $PHAR_HOST" >&2
    exit 1
fi

FIXED_LINE="getCategoryLink((int) Configuration::get('HOME_FEATURED_CAT'))"
BROKEN_LINE="getCategoryLink(\$this->getConfigFieldsValues()['HOME_FEATURED_CAT'])"

if ! grep -F "$FIXED_LINE" "$TARGET" >/dev/null; then
    echo "expected production fix to be present before proof" >&2
    exit 1
fi

perl -pi -e "s/getCategoryLink\\(\\(int\\) Configuration::get\\('HOME_FEATURED_CAT'\\)\\)/getCategoryLink(\\\$this->getConfigFieldsValues()['HOME_FEATURED_CAT'])/" "$TARGET"
grep -F "getConfigFieldsValues()" "$TARGET" >/dev/null

set +e
OUTPUT="$(docker run --rm --network=none --platform=linux/amd64 \
    -v "$REPO_ROOT":/src:ro \
    -v "$PHAR_HOST":/opt/phpunit.phar:ro \
    -w /src/tests \
    php@sha256:6ce95208609dc66df163ab936c970b3b34cd901b85c747102c5999f08ade9143 \
    php /opt/phpunit.phar --colors=never --filter "$EXPECTED_TEST" Unit/PsFeaturedProductsWidgetTest.php 2>&1)"
REVERT_CODE=$?
set -e
test "$REVERT_CODE" != 0
echo "$OUTPUT" | grep -Fq "$EXPECTED_TEST"
echo "$OUTPUT" | grep -E 'Failures: 1|FAILURES!' >/dev/null

cp "$BACKUP" "$TARGET"
grep -F "$FIXED_LINE" "$TARGET" >/dev/null

set +e
PASS_OUTPUT="$(docker run --rm --network=none --platform=linux/amd64 \
    -v "$REPO_ROOT":/src:ro \
    -v "$PHAR_HOST":/opt/phpunit.phar:ro \
    -w /src/tests \
    php@sha256:6ce95208609dc66df163ab936c970b3b34cd901b85c747102c5999f08ade9143 \
    php /opt/phpunit.phar --colors=never --filter "$EXPECTED_TEST" Unit/PsFeaturedProductsWidgetTest.php 2>&1)"
PASS_CODE=$?
set -e
test "$PASS_CODE" = 0
echo "$PASS_OUTPUT" | grep -E '^OK \([0-9]+ tests, [0-9]+ assertions\)$' >/dev/null
