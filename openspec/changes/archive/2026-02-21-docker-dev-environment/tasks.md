## 1. Docker Configuration Files

- [x] 1.1 Create `Dockerfile.dev` — PHP 8.1-FPM on Bookworm, install nginx, supervisor, and PHP extensions (sqlite3, bcmath, mbstring, xml, zip, snmp)
- [x] 1.2 Create `docker/nginx.conf` — adapted from `debian/cmprovision`, with 8G upload limit and PHP-FPM socket config
- [x] 1.3 Create `docker/supervisord.conf` — run nginx, php-fpm, and `artisan queue:work`
- [x] 1.4 Create `.dockerignore` — exclude vendor, node_modules, .git, storage/app/images, database/*.sqlite

## 2. Entrypoint and Initialization

- [x] 2.1 Create `docker/entrypoint.sh` — handle first-run setup: .env copy, composer install, key:generate, SQLite creation, migrate --seed, permissions fix, then exec supervisord

## 3. Docker Compose

- [x] 3.1 Create `docker-compose.yml` — single service built from Dockerfile.dev, source mount at /var/lib/cmprovision, named volumes for database/ and storage/app/, port 8080:80

## 4. Verify

- [x] 4.1 Build image and start container with `docker compose up --build`
- [x] 4.2 Verify web UI loads at http://localhost:8080
- [x] 4.3 Verify source changes on host reflect in container without restart
- [x] 4.4 Verify data persists across `docker compose down` / `docker compose up`
- [x] 4.5 Verify `docker compose down -v` resets to clean state
