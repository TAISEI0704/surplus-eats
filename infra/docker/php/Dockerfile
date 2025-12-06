FROM php:8.3-apache

ENV COMPOSER_ALLOW_SUPERUSER=1

ARG WEB_USER_ID
ARG WEB_GROUP_ID

RUN usermod -u $WEB_USER_ID -o www-data
RUN groupmod -g $WEB_GROUP_ID -o www-data

RUN apt-get update -y && \
    apt-get install -y libpq-dev zip unzip && \
    docker-php-ext-install pdo pdo_pgsql && \
    apt-get clean

RUN a2enmod rewrite

RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./infra/docker/php/000-default.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html