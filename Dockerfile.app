FROM serversideup/php:8.4-fpm-nginx-alpine

USER root
RUN install-php-extensions pdo_pgsql redis bcmath opcache gd exif zip intl pcntl \
    && rm -rf /var/cache/apk/*

COPY composer.json /var/www/html/

RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist \
    --no-interaction --no-ansi --no-security-blocking

COPY . /var/www/html/
RUN rm -f /var/www/html/Dockerfile.app /var/www/html/docker-compose.yml /var/www/html/.env.coolify

RUN composer dump-autoload --no-scripts --no-dev --optimize \
    && mkdir -p /var/www/html/storage/app/public \
       /var/www/html/storage/framework/cache/data \
       /var/www/html/storage/framework/sessions \
       /var/www/html/storage/framework/views \
       /var/www/html/storage/logs \
       /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

WORKDIR /var/www/html

EXPOSE 80
