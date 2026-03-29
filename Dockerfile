FROM php:8.2-cli

WORKDIR /app

COPY . .

RUN apt-get update && apt-get install -y unzip git curl \
    && docker-php-ext-install pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader

# Fix permissions (IMPORTANT)
RUN chmod -R 775 storage bootstrap/cache

# Clear cache (IMPORTANT)
RUN php artisan config:clear && php artisan cache:clear

# Run migration safely + start server
CMD php artisan migrate --force || true && php artisan serve --host=0.0.0.0 --port=10000
