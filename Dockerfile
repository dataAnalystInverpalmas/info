# Usar la imagen base PHP 8.5 con apache
FROM php:8.5-apache

# Instalar dependencias y extensiones necesarias
RUN apt-get update && \
    apt-get install -y \
        libzip-dev \
        unzip \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install mysqli zip pdo_mysql gd bcmath && \
    a2enmod rewrite headers
 
