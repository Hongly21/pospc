#!/bin/bash
set -e

echo "Installing dependencies..."
composer install --no-dev --prefer-dist --optimize-autoloader

echo "Installing Node dependencies..."
npm ci --production

echo "Building assets..."
npm run build

echo "Generating app key..."
php artisan key:generate

echo "Running migrations..."
php artisan migrate --force

echo "Build completed successfully!"
