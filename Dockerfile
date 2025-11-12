# Base image con PHP 8.2 y Apache
FROM php:8.2-apache

# Configuración de DocumentRoot para Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
WORKDIR /var/www/html

# Instalar dependencias del sistema y extensiones de PHP necesarias para Laravel
RUN apt-get update \
  && apt-get install -y --no-install-recommends \
      git curl unzip zip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev zlib1g-dev gnupg2 ca-certificates \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j"$(nproc)" gd pdo_mysql mbstring exif pcntl bcmath xml zip \
  && a2enmod rewrite headers \
  # Configurar DocumentRoot de Apache
  && sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar la aplicación
COPY . /var/www/html

# Instalar dependencias de PHP de Laravel en producción
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist \
  && chown -R www-data:www-data /var/www/html \
  && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
  && composer clear-cache

# Copiar entrypoint para ajustar Apache al puerto dinámico de Render
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint"]
CMD ["apache2-foreground"]
