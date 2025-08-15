#!/usr/bin/env bash
set -e

echo "Waiting for MySQL..."
until php -r "new PDO('mysql:host=db;dbname=parser', 'parser_user', 'parser_pass');" 2>/dev/null; do
    sleep 2
done

# Проверяем vendor после того, как том с проектом смонтирован
if [ ! -d /var/www/vendor ]; then
    echo "Installing composer dependencies..."
    composer install --optimize-autoloader
fi

# Генерируем ключ приложения, если его нет
if ! php artisan key:show >/dev/null 2>&1; then
    echo "Generating APP_KEY..."
    php artisan key:generate
fi

echo "Apply migrations..."
php artisan migrate --force

echo "Starting php-fpm..."
exec php-fpm