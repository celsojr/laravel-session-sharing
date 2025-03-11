#!/bin/sh
# Wait for MySQL to be ready
echo "Waiting for MySQL to be available..."
until nc -z -v -w30 mysql 3306
do
  echo "Waiting for database connection..."
  sleep 5
done
echo "MySQL is up!"

# Run migrations and start the server
php artisan migrate --force
php artisan serve --host=0.0.0.0 --port=8000
