FROM php:8.1-fpm-trixie

RUN apt-get update && apt-get install -y --no-install-recommends \
unzip \
&& docker-php-ext-install \
opcache

RUN yes | pecl install xdebug-3.4.7 \
&& echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
&& echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
&& echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
&& echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/xdebug.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV COMPOSER_MEMORY_LIMIT=-1
ENV COMPOSER_CACHE_DIR=/tmp

RUN mkdir --parents /tmp/logs

WORKDIR /var/www/
