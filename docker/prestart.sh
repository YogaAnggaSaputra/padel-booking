#!/bin/sh
# Pre-start: ensure APP_KEY is set before services start
cd /var/www/html
php artisan key:generate --force --quiet 2>/dev/null || true
# Write key into s6-overlay env so PHP-FPM sees it via with-contenv
KEY=$(grep APP_KEY .env | tail -1 | cut -d= -f2-)
if [ -n "$KEY" ] && [ "$KEY" != "base64:" ]; then
    echo "$KEY" > /run/s6/basedir/env/APP_KEY
    chmod 644 /run/s6/basedir/env/APP_KEY
fi
exit 0
