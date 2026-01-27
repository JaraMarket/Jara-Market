#!/bin/bash
set -e

cd /var/www/html

# Note: MySQL connection will be handled by Laravel when needed
# No need to wait for MySQL during container startup

# Install dependencies if needed
if [ ! -d "vendor" ]; then
  echo "📦 Installing Composer dependencies..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Laravel setup
if [ ! -f ".env" ]; then
  cp .env.example .env
fi

php artisan key:generate --force || true
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations & seeding
php artisan migrate --force || true
php artisan db:seed --force || true

# Ensure storage, cache, and log permissions
mkdir -p storage/logs bootstrap/cache /var/run/supervisor
chown -R www-data:www-data storage bootstrap/cache /var/run/supervisor
chmod -R 775 storage bootstrap/cache /var/run/supervisor

# Make sure all log files can be deleted by the app
find storage/logs -type f -exec chmod 664 {} \; || true

echo "🚀 Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
