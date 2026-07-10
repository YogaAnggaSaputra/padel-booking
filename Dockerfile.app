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

# Harden env + secrets
# .env.coolify is Coolify-injected; gitignore'd by .gitignore
RUN rm -f Dockerfile.app docker-compose.yml .env.coolify \
    && mkdir -p /usr/local/bin

# Runtime prestart: generate APP_KEY if missing, make it available to PHP-FPM
# via s6-overlay env file (with-contenv reads /run/s6/basedir/env/)
RUN cat > /usr/local/bin/prestart.sh <<'SH' \
    && chmod +x /usr/local/bin/prestart.sh
#!/bin/sh
set -e
cd /var/www/html
if ! grep -qE '^APP_KEY=' .env 2>/dev/null || grep -qE '^APP_KEY=$' .env; then
    php artisan key:generate --force --quiet 2>/dev/null || true
fi
KEY=$(grep -E '^APP_KEY=' .env 2>/dev/null | tail -1 | cut -d= -f2- || true)
if [ -n "$KEY" ] && [ "$KEY" != "" ]; then
    printf '%s' "$KEY" > /run/s6/basedir/env/APP_KEY
    chmod 644 /run/s6/basedir/env/APP_KEY
fi
SH

# Wire prestart into the existing user service that runs before php-fpm
RUN if [ -d /etc/s6-overlay/s6-rc.d/user/contents.d ]; then \
        cp /usr/local/bin/prestart.sh /etc/s6-overlay/s6-rc.d/user/contents.d/prestart; \
        chmod +x /etc/s6-overlay/s6-rc.d/user/contents.d/prestart; \
    fi

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
