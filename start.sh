#!/bin/sh
# Wait for MySQL to be ready
echo "Waiting for MySQL to be available..."
until nc -z -v -w30 mysql 3306
do
  echo "Waiting for database connection..."
  sleep 5
done
echo "MySQL is up!"

# Run migrations
php artisan migrate --force

# Start PHP-FPM
php-fpm
