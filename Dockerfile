FROM php:8.0.2-fpm

COPY ./ /var/www

# Install dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    htop \
    wget \
    lynx \
    curl \
    mc \
    vim \
    libmcrypt-dev \
    libicu-dev \
    libfreetype6-dev \
    libjpeg-dev \
    libpng-dev \
    libxml2-dev \
    unzip \
    libzip-dev \
    locales \
    tzdata \
    nano

RUN docker-php-ext-install mysqli \
        && docker-php-ext-install pdo_mysql

RUN docker-php-ext-install gd \
        && docker-php-ext-configure gd \
        &&  docker-php-ext-enable gd

RUN docker-php-ext-install zip \
        && docker-php-ext-configure zip \
        &&  docker-php-ext-enable zip

RUN pecl install -o -f redis \
        &&  rm -rf /tmp/pear \
        &&  docker-php-ext-enable redis

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# create dir "files" for symlink
RUN mkdir /var/files && \
    chown 1000:1000 /var/files

# Set working directory
WORKDIR /var/www/

RUN cp .env.example .env \
    && composer install

RUN php artisan key:generate \
    && php artisan cache:clear \
    && php artisan config:clear \
    && php artisan view:clear

# cleanup
RUN apt-get clean \
    && rm -fr /var/lib/apt/lists/* \
    && rm -fr /tmp/* \
    && rm -fr /var/tmp/*

#
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data
