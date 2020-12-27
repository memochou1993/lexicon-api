FROM php:7.4-fpm

RUN apt-get update \
    && apt-get install -y git zip gnupg nginx

WORKDIR /var/www

COPY . .
COPY docker/nginx/conf.d /etc/nginx/conf.d
COPY docker/entrypoint.sh /etc/entrypoint.sh

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --optimize-autoloader --no-dev --no-scripts

RUN pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install pdo_mysql

RUN rm -rf /var/www/html \
    && rm /etc/nginx/sites-enabled/default

RUN chown -R www-data:www-data \
    /var/www/storage \
    /var/www/bootstrap/cache

EXPOSE 80

ENTRYPOINT ["sh", "/etc/entrypoint.sh"]
