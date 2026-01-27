#!/bin/bash
set -e

cd /var/www/html

echo "🚀 Starting Laravel application..."

# Install dependencies if needed
if [ ! -d "vendor" ]; then
  echo "📦 Installing Composer dependencies..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
  echo "✅ Composer dependencies installed"
else
  echo "✅ Composer dependencies already present"
fi

# Laravel setup
if [ ! -f ".env" ]; then
  echo "📝 Creating .env from .env.example..."
  cp .env.example .env || echo "⚠️  Failed to copy .env.example"
else
  echo "✅ .env file already exists"
fi

# These commands may fail if DB is not ready yet - that's OK, Laravel will handle it
echo "🔑 Generating application key..."
php artisan key:generate --force || echo "⚠️  Key generation failed (may already exist)"

echo "🧹 Clearing configuration cache..."
php artisan config:clear || echo "⚠️  Config clear failed"

echo "⚡ Caching configuration..."
php artisan config:cache || echo "⚠️  Config cache failed"

echo "🛣️  Caching routes..."
php artisan route:cache || echo "⚠️  Route cache failed"

echo "👁️  Caching views..."
php artisan view:cache || echo "⚠️  View cache failed"

# Run migrations & seeding (will fail gracefully if DB not ready)
echo "🗄️  Running database migrations..."
php artisan migrate --force || echo "⚠️  Migrations failed (database may not be ready)"

echo "🌱 Seeding database..."
php artisan db:seed --force || echo "⚠️  Seeding failed (database may not be ready)"

# Ensure storage and cache directories exist with correct permissions
echo "📁 Setting up storage directories..."
mkdir -p storage/logs storage/framework/{sessions,views,cache} bootstrap/cache /var/run/supervisor
chown -R www-data:www-data storage bootstrap/cache /var/run/supervisor
chmod -R 775 storage bootstrap/cache /var/run/supervisor
echo "✅ Storage directories configured"

echo "✅ Startup complete! Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
