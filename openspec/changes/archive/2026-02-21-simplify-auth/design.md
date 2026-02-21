## Context

The app uses Laravel Jetstream (Livewire stack) with Fortify for authentication. Registration is already disabled — users are created via `php artisan auth:create-user`. The app runs on a local network as a bench provisioning tool. Full user account management is unnecessary.

Current auth middleware chain: `auth:sanctum`, `verified` (no-op). Routes defined in `routes/web.php:29-41`.

## Goals / Non-Goals

**Goals:**
- Optional site-wide password via `APP_PASSWORD` env var
- No auth when password is empty, simple password prompt when set
- Minimal changes — bypass Jetstream rather than remove it
- Hide user-specific UI (profile, API tokens, user dropdown)

**Non-Goals:**
- Removing Jetstream/Fortify packages (too much churn for no benefit)
- Removing the users table or migrations (harmless to keep)
- Changing API route auth (effectively unused)
- Changing `/scriptexecute` (already open)

## Decisions

### Decision 1: Custom middleware replaces auth:sanctum

Create `App\Http\Middleware\SitePassword` middleware. In `routes/web.php`, replace `auth:sanctum, verified` with `site.password` on the protected route group.

The middleware logic:
1. If `APP_PASSWORD` env is empty → pass through (no auth)
2. If `APP_PASSWORD` is set and `session('site_authenticated')` is true → pass through
3. Otherwise → redirect to password prompt route

**Alternative considered:** Modifying Fortify's login flow to accept password-only. Rejected because it means fighting the framework — Fortify expects a user record.

### Decision 2: Plaintext password in .env

Store `APP_PASSWORD` as plaintext in `.env`. Compare with `===` (not bcrypt). Acceptable for a local network bench tool.

### Decision 3: Reuse login.blade.php

Replace the contents of `resources/views/auth/login.blade.php` with a single password field form. Reuse the existing guest layout (`layouts/guest.blade.php`) and authentication card components for visual consistency. No new view files needed.

### Decision 4: New route for password submission

Add routes:
- `GET /login` → show password form (only when `APP_PASSWORD` is set)
- `POST /login` → validate password, set session flag, redirect

These replace the Fortify-provided login routes. Fortify's login routes can be disabled or overridden since our routes file is loaded after Fortify's service provider.

### Decision 5: Hide nav items via conditional

In `navigation-menu.blade.php`, wrap user-specific sections (profile dropdown, API tokens link) in a condition that hides them. Since there's no user object when using site password, check `Auth::check()` — if false, hide user-specific items.

### Decision 6: Keep CreateUser command but mark as legacy

The `auth:create-user` command isn't harmful to keep. It won't be needed for the new auth flow but could be useful if someone re-enables Jetstream auth later. Leave it.

## Risks / Trade-offs

- **No per-user audit trail** → The app already tracks provisioning by CM serial number, not by user. No loss.
- **Shared password means shared session** → Acceptable for a bench tool. Anyone at the bench should have access.
- **API routes become effectively open** → API auth still references `auth:sanctum` but with no user accounts being created, tokens can't be generated. If API access is needed later, it can be addressed separately.
- **Fortify route conflict** → Our custom `/login` routes must take precedence over Fortify's. Laravel loads `routes/web.php` after service providers, so our routes override Fortify's. Verified this is standard Laravel behavior.
