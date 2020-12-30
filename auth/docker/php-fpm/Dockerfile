FROM php:7.2-fpm-alpine

COPY wait-for-it.sh /usr/bin/wait-for-it

RUN chmod +x /usr/bin/wait-for-it
RUN apk --update --no-cache add git
RUN docker-php-ext-install pdo_mysql
RUN apk add --no-cache libpng libpng-dev && docker-php-ext-install gd && apk del libpng-dev

COPY --from=composer /usr/bin/composer /usr/bin/composer
CMD composer install ; -- bin/console doctrine:migrations:migrate ; bin/console assets:install; php-fpm

COPY --chown=www-data:www-data . /var/www

WORKDIR /var/www

EXPOSE 9000
