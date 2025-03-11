FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && \
    apt-get install -y libpng-dev zip unzip git netcat-openbsd && \
    docker-php-ext-install pdo pdo_mysql && \
    pecl install redis && docker-php-ext-enable redis

# Set working directory
WORKDIR /var/www

# Start PHP-FPM (No `php artisan serve` needed)
CMD ["php-fpm"]
