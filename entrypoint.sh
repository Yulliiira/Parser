#!/usr/bin/env bash
set -e

echo "Waiting for MySQL..."
until php -r "new PDO('mysql:host=db;dbname=parser', 'parser_user', 'parser_pass');" 2>/dev/null; do
    sleep 2
done

echo "Apply migrations..."
php artisan migrate --force

echo "Starting php-fpm..."
exec php-fpm
