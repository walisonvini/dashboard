FROM php:8.4-fpm AS php-base
WORKDIR /app

RUN apt-get update && apt-get install -y \
    git unzip curl libpng-dev libonig-dev libxml2-dev zip libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

FROM php-base AS php-deps
WORKDIR /app
COPY . .
RUN composer install --no-dev --optimize-autoloader --no-interaction

FROM node:23-bullseye AS frontend-builder
WORKDIR /app
COPY --from=php-deps /app/vendor /app/vendor
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM php-base AS final
WORKDIR /var/www/html

COPY . .
COPY --from=frontend-builder /app/public/build /var/www/html/public/build
COPY --from=php-deps /app/vendor /var/www/html/vendor

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

USER www-data

RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

EXPOSE 9000
CMD ["php-fpm"]
