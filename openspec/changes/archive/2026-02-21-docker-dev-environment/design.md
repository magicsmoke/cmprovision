## Context

The project is a Laravel 8 app deployed as a Debian package on Raspberry Pi hardware. It depends on PHP-FPM, nginx, SQLite, and several PHP extensions (including php-snmp). The existing deployment is defined in `debian/` with systemd services, postinst scripts, and nginx config. Developers on macOS cannot easily run the application.

## Goals / Non-Goals

**Goals:**
- One-command dev environment startup on macOS (and Linux)
- Source code mounted from host — edit locally, see changes immediately
- All PHP extensions available including SNMP
- Queue worker running for background jobs
- Database and uploads persist across restarts

**Non-Goals:**
- Production Docker deployment
- Hardware services (dnsmasq, rpiboot) — no CM hardware in dev
- SSL/TLS — dev only, plain HTTP
- Mail sending — not used by the app currently
- Matching exact production PHP version — use PHP 8.1 for broader extension support

## Decisions

### Decision 1: Single container with supervisor

Run nginx, php-fpm, and the queue worker in a single container managed by supervisord.

**Alternative considered:** Multi-container with docker-compose (separate nginx, php, queue). Rejected because it adds complexity for no dev benefit — this isn't a scaling concern, and shared filesystem access between containers is messier.

### Decision 2: PHP 8.1-FPM on Debian Bookworm base

Use `php:8.1-fpm-bookworm` as the base image. Bookworm provides easy access to all required extensions via apt. PHP 8.1 is within the project's `^8.0` requirement.

**Alternative considered:** Alpine-based image. Rejected because php-snmp and other extensions are harder to install on Alpine, and image size isn't a concern for dev.

### Decision 3: Source mount with selective volume overrides

Mount the entire project directory into the container at `/var/lib/cmprovision`. Use named volumes for `database/` and `storage/app/` to persist data independently of the source tree and avoid permission conflicts.

The `vendor/` directory is installed inside the container (not mounted from host) to avoid platform-specific compiled extensions conflicting between macOS and Linux.

### Decision 4: Entrypoint handles initialization

A bash entrypoint script detects first-run conditions and handles setup:
- If `vendor/` is empty → `composer install`
- If no `APP_KEY` in `.env` → copy `.env.example`, generate key
- If SQLite file doesn't exist → create, migrate, seed
- Fix storage permissions
- Then exec supervisord

This avoids manual setup steps and makes `docker compose up` truly one-command.

### Decision 5: Port 8080 on host

Map container port 80 to host port 8080 to avoid conflicts with any host web server. Access at `http://localhost:8080`.

### Decision 6: Dev-only Dockerfile

Name it `Dockerfile.dev` to make clear it's not for production. The `docker-compose.yml` references it explicitly.

### Decision 7: npm runs on host

Frontend build (`npm run watch`) runs on macOS, not in the container. The compiled assets in `public/css/` and `public/js/` are visible to the container via the source mount. This gives faster rebuilds and avoids Node.js in the container.

## Risks / Trade-offs

- **Vendor directory inside container** → `composer install` runs on every fresh container start (no vendor volume). This takes ~30 seconds but ensures Linux-native extensions. → Mitigation: only runs when vendor/ is empty.
- **SQLite file permissions** → Docker on macOS uses a Linux VM, so file ownership can be tricky. → Mitigation: entrypoint explicitly sets www-data ownership on database and storage directories.
- **PHP version mismatch with production** → Dev uses 8.1, production may use 7.3+. → Mitigation: acceptable for dev; CI should test against production PHP version.
- **No dnsmasq/rpiboot** → Cannot test provisioning flow end-to-end in dev. → Mitigation: this is expected; hardware testing requires a real Pi.
