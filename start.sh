set -e

echo "Starting Laravel application..."

chmod -R 775 storage bootstrap/cache

php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Running migrations..."
php artisan migrate --force --no-interaction

echo "Starting server on port $PORT..."
exec php artisan serve --host 0.0.0.0 --port $PORT
