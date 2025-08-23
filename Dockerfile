FROM dunglas/frankenphp:1.9 AS builder

ENV COMPOSER_ALLOW_SUPERUSER=1
ARG USER=youenn
RUN useradd ${USER};
WORKDIR /app

RUN set -eux; \
    apt-get update \
    && apt-get install -y --no-install-recommends curl \
    && curl -fsSL https://deb.nodesource.com/setup_22.x -o nodesource_setup.sh \
    && bash nodesource_setup.sh \
    && rm nodesource_setup.sh \
    && apt-get install -y --no-install-recommends nodejs \
    && apt-get clean

RUN set -eux; \
    install-php-extensions \
    @composer \
    pcntl \
    pdo_mysql \
    redis \
    opcache \
    intl \
    zip \
    ftp \
    bcmath

COPY --link composer.json composer.lock ./
RUN composer install -v \
    --no-dev \
    --no-interaction \
    --no-autoloader \
    --no-ansi \
    --no-scripts

COPY --link package.json package-lock.json ./
RUN npm ci

COPY --link . .
RUN npm run build

RUN composer dump-autoload \
    --no-dev \
    --classmap-authoritative \
    --optimize \
    --no-ansi \
    && composer clear-cache

FROM dunglas/frankenphp:1.9

ENV COMPOSER_ALLOW_SUPERUSER=1
ARG USER=youenn
RUN useradd ${USER};
WORKDIR /app

RUN set -eux; \
    apt-get update \
    && apt-get install -y --no-install-recommends \
    acl \
    file \
    gettext \
    procps \
    cron \
    # nano \
    mariadb-client \
    && apt-get clean

RUN set -eux; \
    install-php-extensions \
    pcntl \
    pdo_mysql \
    redis \
    opcache \
    intl \
    zip \
    ftp \
    bcmath

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
ENV PHP_INI_SCAN_DIR=":$PHP_INI_DIR/app.conf.d"
COPY <<EOT $PHP_INI_DIR/app.conf.d/php.ini
    realpath_cache_size = 4096K
    realpath_cache_ttl = 600
    opcache.enable=1
    opcache.enable_cli=1
    opcache.interned_strings_buffer = 16
    opcache.max_accelerated_files = 20000
    opcache.memory_consumption = 256
    opcache.enable_file_override = 1
    opcache.validate_timestamps=0
    memory_limit = 1024M
EOT

COPY --link --chown=${USER}:${USER} --chmod=755 start-container.sh /usr/local/bin/start-container

COPY --from=builder /app /app

RUN chmod -R 775 /app/storage
RUN chmod -R 775 /app/bootstrap/cache
RUN chmod -R 775 /data
RUN chmod -R 775 /config

RUN chown -R ${USER}:${USER} /app
RUN chown -R ${USER}:${USER} /data
RUN chown -R ${USER}:${USER} /config

RUN echo "* * * * * ${USER} /usr/local/bin/php /app/artisan schedule:run >> /var/log/cron.log 2>&1" > /etc/cron.d/schedule
RUN chmod 0644 /etc/cron.d/schedule

USER ${USER}
