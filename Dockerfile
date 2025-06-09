# syntax=docker/dockerfile:1
FROM php:8.4-cli AS development-php
ARG USER_UID=1000
ARG USER_GID=1000
WORKDIR /
SHELL ["/bin/bash", "-c"]
ENV COMPOSER_HOME="/app/build/composer" \
    PATH="/app/bin:/app/vendor/bin:/app/build/composer/bin:$PATH" \
    PHP_PEAR_PHP_BIN="php -d error_reporting=E_ALL&~E_DEPRECATED" \
    SALT_BUILD_STAGE="development" \
    XDEBUG_MODE="off"

# Create a non-root user to run the application
RUN groupadd --gid $USER_GID dev && useradd --uid $USER_UID --gid $USER_GID --groups www-data --shell /bin/bash dev

# Update the package list, upgrade, and install required packages
RUN --mount=type=cache,target=/var/lib/apt,sharing=locked apt-get update \
    && apt-get upgrade --yes \
    && apt-get install --yes --quiet --no-install-recommends \
        curl \
        git \
        jq \
        less \
        libgmp-dev \
        libicu-dev \
        librabbitmq-dev \
        libzip-dev \
        unzip \
        vim-tiny \
        zip \
        zlib1g-dev \
    && ln -s /usr/bin/vim.tiny /usr/bin/vim

# Install PHP Extensions
RUN --mount=type=tmpfs,target=/tmp/pear <<-EOF
  set -eux
  export MAKEFLAGS="-j$(nproc)"
  docker-php-ext-install -j$(nproc) bcmath exif gmp intl opcache pcntl pdo_mysql zip
  pecl install amqp igbinary redis xdebug
  docker-php-ext-enable amqp igbinary redis xdebug
  cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
  cat <<-EOL >> "$PHP_INI_DIR/php.ini"
    date.timezone = UTC
    error_reporting = E_ALL & ~E_DEPRECATED
    max_execution_time = 600
    memory_limit = 1G
    variables_order = EGPCS
    xdebug.log_level=0
  EOL
EOF

COPY --link --from=composer/composer /usr/bin/composer /usr/local/bin/composer
WORKDIR /app
USER dev

FROM node:alpine AS prettier
ENV NPM_CONFIG_PREFIX=/home/node/.npm-global
ENV PATH=$PATH:/home/node/.npm-global/bin
WORKDIR /app
RUN npm install --global --save-dev --save-exact npm@latest prettier
ENTRYPOINT ["prettier"]
