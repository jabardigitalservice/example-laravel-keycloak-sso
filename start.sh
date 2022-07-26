#!/bin/sh

cd /app

# pastikan file database sudah ada
mkdir -p /app/database/storage
touch /app/database/storage/database.sqlite

# set default value for LARAVEL_SERVE_PORT
# ref: https://stackoverflow.com/a/48829326
LARAVEL_SERVE_PORT="${LARAVEL_SERVE_PORT:-8000}"

#php -S 0.0.0.0:8000
php artisan migrate --force
php artisan serve --host 0.0.0.0 --port ${LARAVEL_SERVE_PORT}
