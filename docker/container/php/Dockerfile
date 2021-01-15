FROM php:8.0-apache

ENV APACHE_DOCUMENT_ROOT /var/www/test-task/public

RUN apt-get update && apt-get install -my \
    curl \
    wget \
    git

# PHP Modules
RUN docker-php-ext-install -j$(nproc) pdo pdo_mysql

# Composer
RUN /usr/local/bin/php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    /usr/local/bin/php composer-setup.php --install-dir=/bin --filename=composer && \
    /usr/local/bin/php -r "unlink('composer-setup.php');"

# Configuration
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN chown -R www-data:www-data /var/www

USER www-data
WORKDIR /var/www/test-task