FROM php:8.2-apache

WORKDIR /var/www

EXPOSE 80 443

RUN apt-get update \
 && apt-get install -y zlib1g-dev libzip-dev ssl-cert nano vim curl \
 && docker-php-ext-install zip mysqli pdo_mysql \
 && a2enmod rewrite \
 && a2enmod ssl \
 && a2ensite default-ssl \
 && chmod -R 777 /etc/ssl/certs \
 && sed -i 's!/var/www/html!/var/www/public!g' /etc/apache2/apache2.conf \
        /etc/apache2/sites-available/000-default.conf \
        /etc/apache2/sites-available/default-ssl.conf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer