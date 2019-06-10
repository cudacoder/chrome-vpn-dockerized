FROM php:7.0-cli-alpine

COPY fs /

RUN apk add --no-cache git \
        && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
        && composer global require hirak/prestissimo \
        && cd /usr/src/init && composer install

WORKDIR /usr/src/init
CMD php init.php