FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    nginx \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    oniguruma-dev

RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN composer install --no-dev --optimize-autoloader

EXPOSE 80

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]