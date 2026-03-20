#!/bin/sh
set -e

echo "🚀 Starting Doctors Booking App..."

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    echo "⚙️  Generating application key..."
    php artisan key:generate --force
fi

# Wait for database to be ready (extra safety on top of healthcheck)
echo "⏳ Waiting for database connection..."
until php artisan db:monitor --databases=mysql 2>/dev/null; do
    echo "   Database not ready yet — retrying in 3s..."
    sleep 3
done

echo "✅ Database connected."

# Run migrations
echo "🗄️  Running migrations..."
php artisan migrate --force

# Seed only if DB is empty
echo "🌱 Checking if seeding needed..."
USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | tail -1 | tr -d '[:space:]')
echo "👥 User count: $USER_COUNT"
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo "🌱 Seeding database..."
    php artisan db:seed --force
else
    echo "✅ Database already seeded ($USER_COUNT users found)"
fi

# 🔥 FIX: Change OWNERSHIP before changing permissions
echo "🔧 Fixing storage permissions..."
chown -R www-data:www-data /var/www/storage
chown -R www-data:www-data /var/www/bootstrap/cache
chmod -R 775 /var/www/storage
chmod -R 775 /var/www/bootstrap/cache

# Clear and cache config for performance
echo "⚡ Optimizing..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🎉 Ready! Starting nginx + php-fpm..."

# Fix Nginx port for Railway (Railway assigns a random $PORT)
sed -i "s/listen 80;/listen ${PORT:-80};/" /etc/nginx/sites-available/default

# Start supervisor (runs both nginx and php-fpm)
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf