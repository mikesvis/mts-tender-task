FROM php:8.0-apache

ENV APACHE_DOCUMENT_ROOT /var/www/test-task/public

RUN apt-get update && apt-get install -my \
    curl \
    wget \
    git \
    zip \
    libzip-dev

RUN docker-php-ext-configure zip

# PHP Modules
RUN docker-php-ext-install -j$(nproc) pdo pdo_mysql zip pcntl opcache

# Configuration
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2enmod rewrite

RUN chown -R www-data:www-data /var/www

USER www-data
WORKDIR /var/www/test-task