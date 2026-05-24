FROM php:8.3-apache

RUN docker-php-ext-install pdo pdo_mysql mysqli

RUN a2enmod rewrite

COPY ./public /var/www/html/public

WORKDIR /var/www/html
