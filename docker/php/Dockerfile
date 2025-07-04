FROM php:8.3-fpm

ARG DB_HOST
ARG DB_DATABASE
ARG DB_USERNAME
ARG DB_PASSWORD
ARG OPEN_EXCHANGE_RATES_API_KEY
ARG AWS_ACCESS_KEY_ID
ARG AWS_SECRET_ACCESS_KEY
ARG AWS_DEFAULT_REGION
ARG AWS_BUCKET
ARG AWS_ENDPOINT_URL

ENV DB_HOST=${DB_HOST}
ENV DB_DATABASE=${DB_DATABASE}
ENV DB_USERNAME=${DB_USERNAME}
ENV DB_PASSWORD=${DB_PASSWORD}
ENV AWS_ACCESS_KEY_ID=${AWS_ACCESS_KEY_ID}
ENV AWS_SECRET_ACCESS_KEY=${AWS_SECRET_ACCESS_KEY}
ENV AWS_DEFAULT_REGION=${AWS_DEFAULT_REGION}
ENV AWS_BUCKET=${AWS_BUCKET}
ENV AWS_ENDPOINT_URL=${AWS_ENDPOINT_URL}
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