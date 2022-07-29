#!/bin/sh

cd /app

# pastikan file database sudah ada
mkdir -p /app/database/storage

SQLITE_DB_DATABASE="${DB_DATABASE:-storage/database.sqlite}"
touch "/app/database/${SQLITE_DB_DATABASE}"

# set default value for LARAVEL_SERVE_PORT
# ref: https://stackoverflow.com/a/48829326
LARAVEL_SERVE_PORT="${PORT:-8000}"

#php -S 0.0.0.0:8000
php artisan migrate:fresh --seed --force
php artisan serve --host 0.0.0.0 --port ${LARAVEL_SERVE_PORT}
