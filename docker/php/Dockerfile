FROM node:23-bullseye as frontend-builder
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

FROM php:8.4-fpm
WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git unzip curl libpng-dev libonig-dev libxml2-dev zip libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

COPY --from=frontend-builder /app/public/build /var/www/html/public/build

COPY . .

RUN composer install --optimize-autoloader --no-dev --no-interaction --prefer-dist

EXPOSE 9000
CMD ["php-fpm"]
