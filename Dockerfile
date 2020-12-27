FROM php:7.4-fpm

RUN apt-get update \
    && apt-get install -y git zip gnupg

WORKDIR /var/www

COPY . .

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --optimize-autoloader --no-dev --no-scripts

RUN pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install pdo_mysql

RUN chown -R www-data:www-data \
    /var/www/storage \
    /var/www/bootstrap/cache

RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - \
    && echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list \
    && apt-get update \
    && apt-get install -y yarn

RUN cd resources/js \
    && yarn install
