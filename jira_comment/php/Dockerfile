FROM php:7.4-apache

RUN apt-get update && \
    apt-get install -y git && \
    rm -rf /var/lib/apt/lists/*

RUN git clone https://github.com/motdotla/dotenv.git /usr/local/src/dotenv && \
    cd /usr/local/src/dotenv && \
    git checkout 0.9.0 && \
    ln -s /usr/local/src/dotenv/loader.php /usr/local/bin/dotenv

RUN a2enmod rewrite

WORKDIR /var/www/html

COPY . .

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer install

EXPOSE 80

CMD ["apache2-foreground"]