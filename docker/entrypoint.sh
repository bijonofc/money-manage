#!/usr/bin/env bash
set -e

echo "=========================================="
echo " Starting Money Manage Deployment..."
echo "=========================================="

# 1. Configure Port for Render or Local
PORT="${PORT:-80}"
echo "==> Configuring Nginx to listen on port ${PORT}..."
if [ -f /etc/nginx/http.d/default.conf ]; then
    sed -i "s/LISTEN_PORT/${PORT}/g" /etc/nginx/http.d/default.conf
fi
if [ -f /etc/nginx/conf.d/default.conf ]; then
    sed -i "s/LISTEN_PORT/${PORT}/g" /etc/nginx/conf.d/default.conf
fi

# 2. Permissions for Laravel writable directories
echo "==> Ensuring directory permissions for storage and bootstrap/cache..."
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 3. Create Storage Symlink
echo "==> Creating storage symlink..."
php artisan storage:link --force || true

# 4. Optional Database Migrations
if [ "${AUTO_MIGRATE}" = "true" ] || [ "${RUN_MIGRATIONS}" = "true" ]; then
    echo "==> Running database migrations..."
    php artisan migrate --force || echo "Migration warning: could not run migrations, continuing..."
fi

# 5. Production Caching Optimizations
if [ "${APP_ENV}" = "production" ] && [ -n "${APP_KEY}" ]; then
    echo "==> Warming up Laravel cache (config, route, view)..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
else
    echo "==> Clearing Laravel cache..."
    php artisan config:clear || true
    php artisan route:clear || true
    php artisan view:clear || true
fi

echo "==> Application ready! Starting Nginx and PHP-FPM via Supervisord..."
exec /usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf
