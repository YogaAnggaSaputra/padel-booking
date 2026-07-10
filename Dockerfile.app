FROM serversideup/php:8.4-fpm-nginx-alpine

USER root
RUN install-php-extensions pdo_pgsql redis bcmath opcache gd exif zip intl pcntl \
    && rm -rf /var/cache/apk/*

COPY composer.json /var/www/html/

# Install with --ignore-platform-req for any missing optional extensions
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist \
    --no-interaction --no-ansi --ignore-platform-req=ext-sodium

COPY . /var/www/html/
RUN rm -f /var/www/html/Dockerfile.app /var/www/html/docker-compose.yml /var/www/html/.env.coolify

RUN composer dump-autoload --no-scripts --no-dev --optimize \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

WORKDIR /var/www/html

EXPOSE 80
