FROM debian:bookworm-slim@sha256:7c7b2c966bc9ee8cedfeef67e0e279108992c77681fa595db4a9d65c06ccc587 AS debian-keyring

FROM debian:bookworm-slim@sha256:7c7b2c966bc9ee8cedfeef67e0e279108992c77681fa595db4a9d65c06ccc587
COPY --from=debian-keyring /usr/share/keyrings/debian-archive-keyring.gpg /usr/share/keyrings/debian-archive-keyring.gpg
RUN apt-get update \
    && apt-get install -y --no-install-recommends ca-certificates gnupg wget \
    && rm -rf /var/lib/apt/lists/*
COPY tests/scripts/fetch-and-verify-phpunit.sh /usr/local/bin/fetch-and-verify-phpunit
RUN chmod 0755 /usr/local/bin/fetch-and-verify-phpunit
