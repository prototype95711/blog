
FROM php:8.2-fpm-bookworm

LABEL maintainer="blog-team" \
      description="nginx + php8.2-fpm + mariadb(mysql) + composer"

ENV DEBIAN_FRONTEND=noninteractive \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/tmp/composer

RUN apt-get update && apt-get install -y --no-install-recommends \
        nginx \
        mariadb-server \
        mariadb-client \
        supervisor \
        git \
        unzip \
        curl \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libonig-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mysqli \
        mbstring \
        opcache \
        exif \
        zip \
        gd \
        bcmath \
        pcntl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/php/custom.ini /usr/local/etc/php/conf.d/zzz-custom.ini
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/mysql/init.sql /docker-entrypoint-initdb.d/init.sql
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh \
    && mkdir -p /var/log/supervisor /run/mysqld /var/www/html/public \
    && chown -R www-data:www-data /var/www/html \
    && chown -R mysql:mysql /run/mysqld \
    && rm -f /etc/nginx/sites-enabled/default \
    && ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default \
    && rm -rf /var/lib/mysql/*

COPY composer.json composer.lock* /var/www/html/
RUN composer install --no-dev --no-interaction --no-progress --prefer-dist \
        --no-scripts --optimize-autoloader || true

COPY public /var/www/html/public
COPY src /var/www/html/src
RUN chown -R www-data:www-data /var/www/html

VOLUME ["/var/lib/mysql"]

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
