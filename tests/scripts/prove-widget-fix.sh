#!/bin/sh
set -eu

REPO_ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
TARGET="$REPO_ROOT/ps_featuredproducts.php"
BACKUP="$(mktemp)"
cp "$TARGET" "$BACKUP"
trap 'cp "$BACKUP" "$TARGET"; rm -f "$BACKUP"' EXIT

FIXED_LINE="getCategoryLink((int) Configuration::get('HOME_FEATURED_CAT'))"
BROKEN_LINE="getCategoryLink(\$this->getConfigFieldsValues()['HOME_FEATURED_CAT'])"

if ! grep -F "$FIXED_LINE" "$TARGET" >/dev/null; then
    echo "expected production fix to be present before proof" >&2
    exit 1
fi

perl -pi -e "s/getCategoryLink\\(\\(int\\) Configuration::get\\('HOME_FEATURED_CAT'\\)\\)/getCategoryLink(\\\$this->getConfigFieldsValues()['HOME_FEATURED_CAT'])/" "$TARGET"
grep -F "getConfigFieldsValues()" "$TARGET" >/dev/null

set +e
docker run --rm --network=none --platform=linux/amd64 \
    -v "$REPO_ROOT":/src \
    -v /opt/phpunit-phar/phpunit-4.8.36.phar:/opt/phpunit.phar:ro \
    -w /src/tests \
    php@sha256:6ce95208609dc66df163ab936c970b3b34cd901b85c747102c5999f08ade9143 \
    php /opt/phpunit.phar --colors=never --filter testAllProductsLinkUsesStoredCategoryNotRequestOverride Unit/PsFeaturedProductsWidgetTest.php
REVERT_CODE=$?
set -e
test "$REVERT_CODE" != 0

cp "$BACKUP" "$TARGET"
grep -F "$FIXED_LINE" "$TARGET" >/dev/null

docker run --rm --network=none --platform=linux/amd64 \
    -v "$REPO_ROOT":/src \
    -v /opt/phpunit-phar/phpunit-4.8.36.phar:/opt/phpunit.phar:ro \
    -w /src/tests \
    php@sha256:6ce95208609dc66df163ab936c970b3b34cd901b85c747102c5999f08ade9143 \
    php /opt/phpunit.phar --colors=never --filter testAllProductsLinkUsesStoredCategoryNotRequestOverride Unit/PsFeaturedProductsWidgetTest.php
