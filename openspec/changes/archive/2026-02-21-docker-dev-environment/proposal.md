## Why

The project cannot be easily run on macOS for development. It depends on Debian-specific packages (php-fpm, php-snmp, dnsmasq, nginx) and assumes a Linux filesystem layout under `/var/lib/cmprovision/`. A Docker-based dev environment eliminates host OS dependencies and provides a one-command setup.

## What Changes

- Add `Dockerfile.dev` with PHP 8.1-FPM, nginx, and all required PHP extensions
- Add `docker-compose.yml` with a single service, source mount, and port mapping
- Add `docker/supervisord.conf` to run nginx, php-fpm, and queue worker in one container
- Add `docker/nginx.conf` adapted from the existing `debian/cmprovision` nginx config
- Add `docker/entrypoint.sh` for first-run setup (composer install, key generation, migration, permissions)
- Add `.dockerignore` to exclude vendor, node_modules, and other non-essential files
- Hardware services (dnsmasq, rpiboot) are excluded — not needed for UI/logic development

## Capabilities

### New Capabilities
- `docker-dev-setup`: Single-command Docker dev environment with source mounting, auto-initialization, and all PHP extensions
- `docker-dev-persistence`: SQLite database and storage persist across container restarts via Docker volumes

### Modified Capabilities
<!-- None — no existing spec-level behavior is changing -->

## Impact

- New files at project root: `Dockerfile.dev`, `docker-compose.yml`, `.dockerignore`
- New directory: `docker/` containing `supervisord.conf`, `nginx.conf`, `entrypoint.sh`
- No changes to existing application code
