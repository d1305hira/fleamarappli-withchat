FROM php:8.1-fpm

COPY php.ini /usr/local/etc/php/

RUN apt-get update && apt-get install -y \
  libjpeg-dev \
  libpng-dev \
  libfreetype6-dev \
  zlib1g-dev \
  libzip-dev \
  unzip \
  default-mysql-client \
  libicu-dev \
  && docker-php-ext-configure gd --with-jpeg --with-freetype \
  && docker-php-ext-install gd pdo_mysql zip bcmath intl

RUN curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/local/bin/composer \
  && composer self-update

WORKDIR /var/www
