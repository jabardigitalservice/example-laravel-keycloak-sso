#!/bin/sh

cd /app

# pastikan file database sudah ada
mkdir -p /app/database/storage
touch /app/database/storage/database.sqlite

#php -S 0.0.0.0:8000
php artisan migrate --force
php artisan serve --host 0.0.0.0
