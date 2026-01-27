#!/bin/bash
set -e

cd /var/www/html

# Use environment variables from ECS Secrets Manager
DB_HOST=${DB_HOST:-localhost}
DB_USER=${DB_USERNAME:-root}
DB_PASS=${DB_PASSWORD:-""}

echo "⏳ Waiting for MySQL at ${DB_HOST}..."
TIMEOUT=60
ELAPSED=0
until mysql -h "${DB_HOST}" -u "${DB_USER}" --password="${DB_PASS}" --ssl-mode=DISABLED -e "SELECT 1;" 2>&1 | tee /tmp/mysql_error.log | grep -q "1"; do
  if [ $ELAPSED -ge $TIMEOUT ]; then
    echo "❌ MySQL connection timeout after ${TIMEOUT}s"
    echo "Last error:"
    cat /tmp/mysql_error.log
    exit 1
  fi
  sleep 2
  ELAPSED=$((ELAPSED + 2))
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
