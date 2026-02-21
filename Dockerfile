# Stage 1: Build frontend assets
FROM node:16-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json webpack.mix.js tailwind.config.js postcss.config.js* ./
COPY resources ./resources

RUN npm ci && npm run prod

# Stage 2: Install PHP dependencies
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
COPY database ./database
COPY app ./app

RUN composer install --optimize-autoloader --no-dev --no-interaction --no-scripts

# Stage 3: Production image
FROM php:8.1-fpm-bullseye

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    supervisor \
    dnsmasq \
    sqlite3 \
    libsqlite3-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_sqlite zip bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy PHP configuration
COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini

# Copy nginx configuration
COPY docker/nginx.conf /etc/nginx/sites-available/default
RUN rm -f /etc/nginx/sites-enabled/default \
    && ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Copy supervisord configuration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set working directory
WORKDIR /var/www/html

# Copy application code
COPY --chown=www-data:www-data . .

# Copy built frontend assets from stage 1
COPY --from=assets --chown=www-data:www-data /app/public/js ./public/js
COPY --from=assets --chown=www-data:www-data /app/public/css ./public/css
COPY --from=assets --chown=www-data:www-data /app/public/mix-manifest.json ./public/mix-manifest.json

# Copy PHP vendor dependencies from stage 2
COPY --from=vendor --chown=www-data:www-data /app/vendor ./vendor

# Create required directories and set permissions
RUN mkdir -p storage/framework/{sessions,views,cache} \
    storage/logs \
    bootstrap/cache \
    database \
    && chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache

# Copy entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
CMD ["supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
