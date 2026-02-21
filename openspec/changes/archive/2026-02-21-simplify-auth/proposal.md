## Why

The app is a bench provisioning tool on a local network. The full Jetstream email/password/2FA user system is overkill — there's no need for multiple user accounts, email-based password resets, or per-user profiles. A single optional site password is sufficient to prevent unauthorized access.

## What Changes

- Add `APP_PASSWORD` environment variable — if empty, app is open; if set, requires password to access
- Create custom middleware that checks session for authentication when `APP_PASSWORD` is set
- Create a simple single-field password prompt page (no username)
- Replace `auth:sanctum` + `verified` middleware on web routes with the new middleware
- Hide user-specific navigation items (profile dropdown, API tokens link)
- Remove `auth:create-user` artisan command (no longer needed)

## Capabilities

### New Capabilities
- `site-password`: Optional site-wide password protection via `APP_PASSWORD` env var

### Modified Capabilities
<!-- None — no existing specs are changing -->

## Impact

- `routes/web.php`: Swap middleware on protected route group
- `app/Http/Middleware/`: New `SitePassword` middleware
- `resources/views/auth/login.blade.php`: Replace with single-field password form
- `resources/views/navigation-menu.blade.php`: Hide user-specific items
- `app/Console/Commands/CreateUser.php`: Remove
- `.env` / `debian/env.example`: Add `APP_PASSWORD=`
- Jetstream/Fortify: Remain installed but bypassed
- Users table: Remains but unused
- API routes: Auth effectively disabled (or open)
- `/scriptexecute`: Unchanged
