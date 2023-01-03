FROM php:8.1-apache-buster as build
WORKDIR /var/www/html

RUN apt-get upgrade && apt-get update && apt-get install nano
RUN apt install -y libgmp-dev git zip unzip
RUN apt install -y libzip-dev
RUN apt install -y libsodium-dev
RUN apt-get update && apt-get install -y tzdata
RUN ln -sfn /usr/share/zoneinfo/Europe/Madrid /etc/localtime
RUN docker-php-ext-configure opcache --enable-opcache && \
    docker-php-ext-install pdo pdo_mysql gmp bcmath zip sodium

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
&& curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
&& php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" \
&& php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer --snapshot \
&& rm -f /tmp/composer-setup.*

RUN cd /usr/local/etc/php/conf.d/ && \
  echo 'memory_limit = -1' >> /usr/local/etc/php/conf.d/docker-php-ram-limit.ini

RUN a2enmod rewrite
RUN echo "xdebug.mode=debug" >> /usr/local/etc/php/php.ini
RUN echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/php.ini
RUN echo "xdebug.client_port=9005" >> /usr/local/etc/php/php.ini
RUN echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/php.ini
RUN echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/php.ini
RUN echo "memory_limit=512M"  >> /usr/local/etc/php/php.ini
RUN echo "max_execution_time=900" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /var/www/html

COPY ./opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY ./000-default.conf /etc/apache2/sites-available/000-default.conf
COPY . /var/www/html

RUN export COMPOSER_PROCESS_TIMEOUT=900

WORKDIR /var/www/html
# Copy composer and vendor
RUN composer install --prefer-dist --optimize-autoloader --no-interaction

RUN chown -R www-data:www-data /var/www/html/
RUN chmod -R u+rwx /var/www/html/

# Expose port
EXPOSE 80

