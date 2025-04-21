FROM php:8.2-fpm

# Instala dependencias necesarias para Symfony y MongoDB
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    zlib1g-dev \
    libonig-dev \
    libxml2-dev \
    libssl-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip \
    && pecl install mongodb && docker-php-ext-enable mongodb

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
