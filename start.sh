#!/bin/bash
set -e

cd /var/www/html

echo "⏳ Waiting for MySQL..."
until mysql -h db -u root --password="" -e "SELECT 1;" >/dev/null 2>&1; do
  sleep 2
done
echo "✅ MySQL is up."

# Install dependencies if needed
if [ ! -d "vendor" ]; then
  echo "📦 Installing Composer dependencies..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Laravel setup
if [ ! -f ".env" ]; then
  cp .env.example .env
fi

php artisan key:generate --force
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations & seeding
php artisan migrate --force
php artisan db:seed --force

# Ensure storage & cache permissions
mkdir -p storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "🚀 Starting Supervisor..."
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
