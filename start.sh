#!/usr/bin/env bash
set -e

echo "Starting Laravel application..."

# Ajustar permisos necesarios
chmod -R 775 storage bootstrap/cache

# Optimizar configuración para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones solo si es necesario
echo "Running migrations..."
php artisan migrate --force --no-interaction

# Iniciar el servidor
echo "Starting server on port $PORT..."
exec php artisan serve --host 0.0.0.0 --port $PORT
