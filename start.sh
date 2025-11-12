#!/usr/bin/env bash
set -eux

# Ajustar permisos necesarios
chmod -R 775 storage bootstrap/cache

# Limpiar y regenerar cachés de Laravel
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Ejecutar migraciones (opcional, solo si usas BD)
php artisan migrate --force || true

# Iniciar el servidor
php artisan serve --host 0.0.0.0 --port $PORT
