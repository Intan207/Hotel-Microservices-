FROM php:8.1-apache

RUN docker-php-ext-install pdo pdo_mysql

COPY ./public/ /var/www/html/

EXPOSE 80
