#!/bin/sh
set -e

# Generate .env from environment variables if it doesn't exist
if [ ! -f /var/www/html/.env ]; then
    php artisan env:generate 2>/dev/null || true

    # Create a minimal .env if env:generate is not available
    if [ ! -f /var/www/html/.env ]; then
        cat > /var/www/html/.env <<'ENVFILE'
APP_NAME=${APP_NAME:-CMProvisioning}
APP_ENV=${APP_ENV:-production}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}
APP_KEY=

DB_CONNECTION=${DB_CONNECTION:-sqlite}
DB_DATABASE=${DB_DATABASE:-/var/www/html/database/database.sqlite}

QUEUE_CONNECTION=${QUEUE_CONNECTION:-database}
SESSION_DRIVER=${SESSION_DRIVER:-database}
CACHE_DRIVER=${CACHE_DRIVER:-file}

LOG_CHANNEL=${LOG_CHANNEL:-stack}
LOG_LEVEL=${LOG_LEVEL:-info}
ENVFILE
    fi

    # Generate application key
    php artisan key:generate --force
fi

# Ensure storage directories exist with correct permissions
mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache database

# Create SQLite database if it doesn't exist
if [ ! -f /var/www/html/database/database.sqlite ]; then
    touch /var/www/html/database/database.sqlite
    chown www-data:www-data /var/www/html/database/database.sqlite
    chmod 664 /var/www/html/database/database.sqlite
    echo "Initializing database..."
    php artisan migrate --seed --force
else
    echo "Running pending migrations..."
    php artisan migrate --force
fi

# Cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
