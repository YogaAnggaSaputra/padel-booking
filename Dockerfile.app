FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    nginx \
    supervisor \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libwebp-dev \
    libxpm-dev \
    libzip-dev \
    oniguruma-dev \
    libxml2-dev \
    postgresql-dev \
    icu-dev \
    autoconf \
    dpkg-dev dpkg \
    file \
    g++ \
    gcc \
    libc-dev \
    make \
    pkgconf \
    re2c \
  && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
  && docker-php-ext-install pdo pdo_pgsql gd zip intl mbstring exif pcntl bcmath opcache \
  && apk del autoconf dpkg-dev file g++ gcc libc-dev make pkgconf re2c \
  && rm -rf /var/cache/apk/*

COPY composer.json ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist \
    --no-interaction --no-ansi --no-security-blocking

COPY . .
RUN rm -f Dockerfile.app docker-compose.yml .env.coolify

RUN composer dump-autoload --no-scripts --no-dev --optimize \
    && mkdir -p /var/www/html/storage/app/public \
       /var/www/html/storage/framework/cache/data \
       /var/www/html/storage/framework/sessions \
       /var/www/html/storage/framework/views \
       /var/www/html/storage/logs \
       /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

WORKDIR /var/www/html

# Copy nginx config
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
