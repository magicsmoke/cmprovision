#!/bin/bash
set -e

APP_DIR=/var/lib/cmprovision

# Ensure storage directories exist with correct permissions
mkdir -p "$APP_DIR/storage/app/images" \
         "$APP_DIR/storage/app/firmware/stable" \
         "$APP_DIR/storage/app/firmware/beta" \
         "$APP_DIR/storage/framework/cache/data" \
         "$APP_DIR/storage/framework/sessions" \
         "$APP_DIR/storage/framework/views" \
         "$APP_DIR/storage/logs" \
         "$APP_DIR/database" \
         "$APP_DIR/bootstrap/cache"

chown -R www-data:www-data "$APP_DIR/storage" "$APP_DIR/database" "$APP_DIR/bootstrap/cache"

# Install composer dependencies if vendor is empty
if [ ! -f "$APP_DIR/vendor/autoload.php" ]; then
    echo "Installing composer dependencies..."
    composer install --working-dir="$APP_DIR" --no-interaction
fi

# Create .env if it doesn't exist
if [ ! -f "$APP_DIR/.env" ]; then
    echo "Creating .env from debian/env.example..."
    cp "$APP_DIR/debian/env.example" "$APP_DIR/.env"
    chown www-data:www-data "$APP_DIR/.env"

    echo "Generating application key..."
    php "$APP_DIR/artisan" key:generate
fi

# Ensure queue connection is database (not sync)
sed -i "s/QUEUE_CONNECTION=sync/QUEUE_CONNECTION=database/" "$APP_DIR/.env"

# Create SQLite database if it doesn't exist
if [ ! -f "$APP_DIR/database/database.sqlite" ]; then
    echo "Creating SQLite database..."
    touch "$APP_DIR/database/database.sqlite"
    chown www-data:www-data "$APP_DIR/database/database.sqlite"
    chmod 600 "$APP_DIR/database/database.sqlite"

    echo "Running migrations and seeders..."
    php "$APP_DIR/artisan" migrate --seed
else
    echo "Running pending migrations..."
    php "$APP_DIR/artisan" migrate --force
fi

# Clear caches for dev
php "$APP_DIR/artisan" config:clear
php "$APP_DIR/artisan" view:clear

# Remove default nginx site if it exists
rm -f /etc/nginx/sites-enabled/default 2>/dev/null || true
ln -sf /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

echo "Starting services..."
exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
