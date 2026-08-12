FROM php:8.3-apache

RUN docker-php-ext-install pdo pdo_mysql mysqli

RUN a2enmod rewrite

RUN apt-get update \
    && apt-get install -y unzip \
    && rm -rf /var/lib/apt/lists/*

COPY ./public /var/www/html/public

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
