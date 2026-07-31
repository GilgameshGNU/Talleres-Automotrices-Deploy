FROM php:8.3-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev libpq-dev \
    zip unzip libzip-dev \
    && docker-php-ext-install pdo_pgsql pdo_mysql mbstring pcntl bcmath gd zip \
    && pecl install redis && docker-php-ext-enable redis \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Apache: usar public/ como document root
ENV APACHE_DOCUMENT_ROOT=/var/www/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf

COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY . .

RUN composer install --no-interaction --prefer-dist --no-progress --optimize-autoloader
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Script de arranque: configura el puerto dinámico de Railway y corre migraciones
COPY docker/start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

CMD ["/usr/local/bin/start.sh"]
