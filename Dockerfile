FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev zip libonig-dev libxml2-dev curl \
    && docker-php-ext-install pdo pdo_mysql mbstring zip xml bcmath \
    && docker-php-ext-enable opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

COPY ./php.ini /usr/local/etc/php/conf.d/php.ini

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]