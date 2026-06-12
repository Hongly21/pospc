# FROM php:8.2-fpm

# # 1. Install system dependencies AND Node.js/npm for frontend build
# RUN apt-get update && apt-get install -y \
#     git curl zip unzip libpng-dev libonig-dev libxml2-dev nodejs npm

# # Install PHP extensions
# RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# # Install Composer
# COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# # Set working directory
# WORKDIR /var/www





# # Copy project files
# COPY . .

# # Install PHP dependencies
# RUN composer install --no-dev --optimize-autoloader

# # ---------------------------------------------------
# # 2. THE FIX: Install Node packages and Build CSS
# # ---------------------------------------------------
# RUN npm install
# RUN npm run build

# # Set permissions
# RUN chmod -R 777 storage bootstrap/cache

# # Generate storage link (for images)
# RUN php artisan storage:link || true

# # Expose port
# EXPOSE 8000

# # Start Laravel + run migrations automatically
# CMD php artisan config:clear && \
#     php artisan migrate --force && \
#     (php artisan db:seed --class=RBACSeeder --force || true) && \
#     (php artisan db:seed --class=AdminSeeder --force || true) && \
#     php artisan serve --host=0.0.0.0 --port=8000








FROM php:8.2-fpm

# ១. ដំឡើង System Dependencies, Node.js, និង Nginx + Supervisor សម្រាប់គ្រប់គ្រង Process
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev nodejs npm \
    nginx supervisor

# ដំឡើង PHP extensions របស់ Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# ដំឡើង Composer ជំនាន់ចុងក្រោយ
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# កំណត់ទីតាំង Folder ធ្វើការងារក្នុង Container
WORKDIR /var/www/html

# ចម្លងឯកសារគម្រោងទាំងអស់ចូលទៅក្នុងម៉ាស៊ីន Container
COPY . .

# ដំឡើង PHP Dependencies សម្រាប់ផលិតកម្ម (Production)
RUN composer install --no-dev --optimize-autoloader

# ២. ដំឡើង Node.js Packages និង Build CSS/JS (Vite or Mix) សម្រាប់ Frontend
RUN npm install
RUN npm run build

# បង្កើត Link និម្មិតសម្រាប់ប្រព័ន្ធផ្ទុកឯកសារ (Fallback)
RUN php artisan storage:link || true

# ៣. ចម្លងឯកសារកំណត់រចនាសម្ព័ន្ធ Nginx និង Supervisor ចូលទៅក្នុងប្រព័ន្ធកុងតឺន័រ
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisor.conf /etc/superuser/conf.d/supervisord.conf

# កំណត់សិទ្ធិ (Permissions) ទៅលើ Folder សំខាន់ៗរបស់ Laravel
RUN chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data /var/www/html

# បើកដំណើរការ Port 80 (ស្តង់ដារ Web Server របស់ Render)
EXPOSE 80

# ៤. រត់ការកំណត់ (Clear Cache, Migration, Seeders) រួចបញ្ជាឱ្យ Supervisor ដំណើរការ Nginx+PHP ព្រមគ្នា
CMD php artisan config:clear && \
    php artisan cache:clear && \
    php artisan view:clear && \
    php artisan migrate --force && \
    (php artisan db:seed --class=RBACSeeder --force || true) && \
    (php artisan db:seed --class=AdminSeeder --force || true) && \
    /usr/bin/supervisord -c /etc/superuser/conf.d/supervisord.conf
