#!/bin/sh
set -eu

PHAR_URL="https://phar.phpunit.de/phpunit-4.8.36.phar"
ASC_URL="https://phar.phpunit.de/phpunit-4.8.36.phar.asc"
EXPECTED_SHA256="ab8bc3017a64cf75a2112f5e089a1d5b0c4cc2116556d60c1af5e3f584e85c5e"
EXPECTED_VALIDSIG="VALIDSIG D8406D0D82947747293778314AA394086372C20A"
OUT_DIR="/opt/phpunit-phar"
WORKDIR="$(mktemp -d)"
GNUPGHOME="$(mktemp -d)"
export GNUPGHOME
trap 'rm -rf "$WORKDIR" "$GNUPGHOME"' EXIT

mkdir -p "$OUT_DIR"
cd "$WORKDIR"

wget -q "$PHAR_URL" -O phpunit-4.8.36.phar
wget -q "$ASC_URL" -O phpunit-4.8.36.phar.asc
wget -q https://sebastian-bergmann.de/gpg.asc -O sebastian.asc
gpg --batch --import sebastian.asc

GPG_OUTPUT="$(gpg --batch --status-fd 1 --verify phpunit-4.8.36.phar.asc phpunit-4.8.36.phar 2>&1)"
echo "$GPG_OUTPUT" | grep -F "$EXPECTED_VALIDSIG" >/dev/null

ACTUAL_SHA256="$(sha256sum phpunit-4.8.36.phar | awk '{print $1}')"
test "$ACTUAL_SHA256" = "$EXPECTED_SHA256"

install -m 0644 phpunit-4.8.36.phar "$OUT_DIR/phpunit-4.8.36.phar"
