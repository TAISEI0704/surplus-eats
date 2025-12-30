#!/usr/bin/env sh
composer install --no-interaction --prefer-dist

echo "Starting Laravel Octane with FrankenPHP..."

# Run Laravel Octane
php artisan octane:frankenphp \
    --host=0.0.0.0 \
    --port=8000 \
    --workers=4 \
    --max-requests=500