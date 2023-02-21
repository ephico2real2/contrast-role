FROM php:7.4-apache

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN echo "Acquire::Check-Valid-Until \"false\";\nAcquire::Check-Date \"false\";" | cat > /etc/apt/apt.conf.d/10no--check-valid-until

RUN apt-get update && \
    apt-get install -y git && \
    apt-get install -y libicu-dev &&  \
    docker-php-ext-install -j$(nproc) intl && \
    docker-php-ext-install -j$(nproc) json && \
    a2enmod rewrite && \
    rm -rf /var/lib/apt/lists/*

RUN git clone https://github.com/motdotla/dotenv.git /usr/local/src/dotenv && \
    cd /usr/local/src/dotenv && \
    git checkout tags/v16.0.3 && \
    ln -s /usr/local/src/dotenv/loader.php /usr/local/bin/dotenv

RUN a2enmod rewrite

WORKDIR /var/www/html

COPY . .

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN set -eux
RUN composer install && \
    chown -R www-data:www-data /var/www/html/

EXPOSE 80

CMD ["apache2-foreground"]
