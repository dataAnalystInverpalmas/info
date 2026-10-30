FROM php:8.2-apache

RUN apt-get update && \
    apt-get install -y \
        libzip-dev \
        unzip \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install mysqli zip pdo_mysql gd bcmath && \
    a2enmod rewrite headers && \
    rm -rf /var/lib/apt/lists/*