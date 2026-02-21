#!/bin/bash
#
# Deploy cmprovision from a git clone to the installed location at /var/lib/cmprovision.
# Run this on the Raspberry Pi from inside the cloned repo directory.
#
# Prerequisites:
#   composer install --optimize-autoloader --no-dev
#

set -e

INSTALL_DIR="/var/lib/cmprovision"

if [ ! -d "$INSTALL_DIR" ]; then
    echo "Error: $INSTALL_DIR does not exist. Is cmprovision installed?"
    exit 1
fi

if [ ! -f "artisan" ]; then
    echo "Error: Run this script from the root of the cmprovision repo."
    exit 1
fi

if [ ! -f "vendor/autoload.php" ]; then
    echo "Error: vendor/ not found. Run 'composer install --optimize-autoloader --no-dev' first."
    exit 1
fi

echo "Deploying to $INSTALL_DIR..."

sudo rsync -av \
    --exclude='.git' \
    --exclude='.gitignore' \
    --exclude='.gitattributes' \
    --exclude='node_modules/' \
    --exclude='docker/' \
    --exclude='Dockerfile*' \
    --exclude='docker-compose*' \
    --exclude='.env' \
    --exclude='database/database.sqlite' \
    --exclude='storage/logs/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/cache/*' \
    --exclude='public/uploads/*' \
    --exclude='openspec/' \
    --exclude='.claude/' \
    --exclude='CLAUDE.md' \
    --exclude='deploy.sh' \
    ./ "$INSTALL_DIR/"

sudo chown -R www-data:www-data "$INSTALL_DIR"

echo "Running migrations..."
sudo -u www-data "$INSTALL_DIR/artisan" migrate --force

echo "Clearing caches..."
sudo -u www-data "$INSTALL_DIR/artisan" config:clear
sudo -u www-data "$INSTALL_DIR/artisan" view:clear

echo "Done."
