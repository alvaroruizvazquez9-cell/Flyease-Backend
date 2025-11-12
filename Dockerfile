FROM php:8.2-fpm

# Dependencias
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    && docker-php-ext-install pdo_mysql mbstring exif bcmath gd

# Instalación Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# copia de proyecto al container
WORKDIR /var/www
COPY . .

# Dependencias Laravel
RUN composer install --no-dev --optimize-autoloader

# KEY
RUN php artisan key:generate

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=8000