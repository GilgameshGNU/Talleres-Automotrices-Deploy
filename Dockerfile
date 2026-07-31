FROM php:8.3-fpm-alpine

# Instalar dependencias del sistema requeridas (Alpine) + build tools for PECL
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    postgresql-dev \
    zip \
    unzip \
    libzip-dev \
    $PHPIZE_DEPS

# Instalar extensiones de PHP necesarias para Laravel
RUN docker-php-ext-install pdo_pgsql pdo_mysql mbstring pcntl bcmath gd zip \
    && pecl install redis && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

# Obtener Composer desde la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar el directorio de trabajo
WORKDIR /var/www

# Copiar configuración de OPcache
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Copiar el código fuente existente al contenedor
COPY . .

# Instalar las dependencias PHP dentro de la imagen
RUN composer install --no-interaction --prefer-dist --no-progress --optimize-autoloader

# Dar permisos a las carpetas críticas de Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]
