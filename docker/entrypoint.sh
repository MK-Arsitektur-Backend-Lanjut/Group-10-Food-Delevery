#!/bin/sh
set -e

echo "[entrypoint] Running migrations..."
php artisan migrate --force --no-interaction

echo "[entrypoint] Starting PHP-FPM..."
php-fpm -D

# Wait for FPM to be ready
sleep 2

echo "[entrypoint] Starting Nginx..."
exec nginx -c /etc/nginx/nginx.conf -g "daemon off;"
