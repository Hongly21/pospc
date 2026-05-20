FROM php:8.2-fpm

# 1. Install system dependencies AND Node.js/npm for frontend build
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev nodejs npm

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# ---------------------------------------------------
# 2. THE FIX: Install Node packages and Build CSS
# ---------------------------------------------------
RUN npm install
RUN npm run build

# Set permissions
RUN chmod -R 777 storage bootstrap/cache

# Generate storage link (for images)
RUN php artisan storage:link || true

# Expose port
EXPOSE 8000

# Start Laravel + run migrations automatically
CMD php artisan config:clear && \
    php artisan migrate --force && \
    (php artisan db:seed --class=RBACSeeder --force || true) && \
    (php artisan db:seed --class=AdminSeeder --force || true) && \
    php artisan serve --host=0.0.0.0 --port=8000
