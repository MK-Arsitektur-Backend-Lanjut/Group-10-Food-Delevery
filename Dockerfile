FROM php:8.2-cli

WORKDIR /var/www

# Install dependency
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip \
    && docker-php-ext-install pdo pdo_mysql

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project
COPY . .

# Install Laravel dependency
RUN composer install

# Jalankan Laravel
CMD php artisan serve --host=0.0.0.0 --port=8000