FROM node:23 AS node-builder
WORKDIR /app

COPY package*.json ./

RUN npm ci

COPY . .
RUN npm run build

FROM php:8.4-fpm
WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git unzip curl libpng-dev libonig-dev libxml2-dev zip libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

COPY . .

COPY --from=node-builder /app/public/build /var/www/html/public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

EXPOSE 9000
CMD ["php-fpm"]
