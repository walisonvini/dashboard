FROM php:8.4-fpm
WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git unzip curl libpng-dev libonig-dev libxml2-dev zip libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://deb.nodesource.com/setup_23.x | bash - \
    && apt-get install -y nodejs

COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --optimize-autoloader --no-interaction --prefer-dist

EXPOSE 9000
CMD ["php-fpm"]
