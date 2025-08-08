FROM php:8.2-fpm

RUN apt-get clean && apt-get update && \
	apt-get install -y libzip-dev iputils-ping wget redis-server


RUN cat /etc/resolv.conf && curl https://github.com

RUN wget https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
	&& cp install-php-extensions /usr/local/bin/

ENV IPE_GD_WITHOUTAVIF=1

RUN chmod +x /usr/local/bin/install-php-extensions && \
	install-php-extensions @composer pgsql pdo_pgsql redis pcntl gd zip sockets


RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

WORKDIR /var/www/html

COPY --chown=www:www . /var/www/html

RUN mkdir -p /var/www/html/storage/logs \
    && chown -R www:www /var/www/html/storage/* \
    && chmod -R 775 /var/www/html/storage/*

COPY --chown=www:www . /var/www/html

RUN chown -R www:www /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache


USER www

CMD php artisan serve --host=0.0.0.0 --port=9000

EXPOSE 9000
