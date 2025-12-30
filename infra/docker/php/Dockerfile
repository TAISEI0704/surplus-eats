FROM dunglas/frankenphp:1.10.1-php8.4-bookworm

ENV COMPOSER_ALLOW_SUPERUSER=1

ARG WEB_USER_ID
ARG WEB_GROUP_ID

RUN usermod -u $WEB_USER_ID -o www-data
RUN groupmod -g $WEB_GROUP_ID -o www-data

RUN mkdir -p /data/caddy \
    && chown -R $WEB_USER_ID:$WEB_GROUP_ID /data

COPY ./Caddyfile /etc/frankenphp/Caddyfile

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN install-php-extensions \
    intl \
    mbstring \
    pdo_pgsql \
    zip \
    bcmath \
    pcntl

WORKDIR /app

COPY ./startup-script.sh /startup-script.sh
RUN chmod +x /startup-script.sh

CMD ["/startup-script.sh"]
