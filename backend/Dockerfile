FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring dom xml xmlwriter zip \
    && rm -rf /var/lib/apt/lists/*

RUN printf "upload_max_filesize=8M\npost_max_size=10M\n" \
    > /usr/local/etc/php/conf.d/carhub-uploads.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

COPY . .

RUN composer dump-autoload --optimize --no-dev \
    && chown -R www-data:www-data storage bootstrap/cache

CMD ["sh", "-c", "php artisan storage:link || true && php artisan config:clear && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]