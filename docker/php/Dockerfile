FROM php:8.3-fpm

ARG DB_HOST
ARG DB_DATABASE
ARG DB_USERNAME
ARG DB_PASSWORD
ARG OPEN_EXCHANGE_RATES_API_KEY

ENV DB_HOST=${DB_HOST}
ENV DB_DATABASE=${DB_DATABASE}
ENV DB_USERNAME=${DB_USERNAME}
ENV DB_PASSWORD=${DB_PASSWORD}
ENV OPEN_EXCHANGE_RATES_API_KEY=${OPEN_EXCHANGE_RATES_API_KEY}

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

# Копируем entrypoint.sh из правильного места
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]