FROM php:8.3.1RC3-apache

LABEL org.opencontainers.image.authors="sq6kpo@gmail.com"

RUN apt update && apt install -y zlib1g-dev libpng-dev libjpeg-dev libfreetype6-dev && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-configure gd --with-jpeg --with-freetype && docker-php-ext-install gd
RUN docker-php-ext-configure mysqli && docker-php-ext-install mysqli

RUN echo "max_execution_time=120" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY html /var/www/html

RUN chown -R www-data:www-data /var/www/html
#USER www-data

