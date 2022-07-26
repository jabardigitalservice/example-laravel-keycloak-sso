FROM registry.digitalservice.id/proxyjds/library/php:8.1-cli-alpine
#FROM php:8.1-cli-alpine

COPY --from=registry.digitalservice.id/proxyjds/library/composer:latest /usr/bin/composer /usr/bin/composer
#COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ref: https://stackoverflow.com/a/63579640/2496217
#COPY --from=registry.digitalservice.id/proxyjds/library/mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/
#COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/

WORKDIR /app

# Install PHP extensions
#RUN install-php-extensions ldap

# ini gak bisa dibuat copy composer aja karena di composer install ada tahap eksekusi artisan. kalau di copy hanya file composer, file artisannnya tidak ada dan jadinya composer installnya gagal
COPY .  /app
RUN composer install && composer dump-autoload

CMD [ "./start.sh" ]
