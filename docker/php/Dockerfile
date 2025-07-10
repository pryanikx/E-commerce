FROM php:8.3-fpm

WORKDIR /var/www/html/

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libsodium-dev

RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli \
    zip \
    bcmath \
    gd \
    sodium \
    sockets

RUN curl -sS https://getcomposer.org/installer | php -- \
     --install-dir=/usr/local/bin --filename=composer

COPY src/ecommerce/ /var/www/html/

RUN if [ ! -f .env ] && [ -f .env.example ]; then cp .env.example .env; fi

RUN mkdir -p storage/app/public/products storage/app/temp \
    && mkdir -p bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

RUN composer install --no-dev --optimize-autoloader

CMD ["sh", "-c", "php artisan key:generate && php artisan migrate && php-fpm"]