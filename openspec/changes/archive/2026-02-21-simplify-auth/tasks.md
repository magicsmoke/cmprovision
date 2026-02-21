## 1. Middleware

- [x] 1.1 Create `App\Http\Middleware\SitePassword` — check `APP_PASSWORD` env, session flag, redirect to login if needed
- [x] 1.2 Register `site.password` alias in `app/Http/Kernel.php`

## 2. Routes

- [x] 2.1 Add `GET /login` and `POST /login` routes for password form (before Fortify's routes take effect)
- [x] 2.2 Add `POST /logout` route to clear session and redirect
- [x] 2.3 Replace `auth:sanctum, verified` middleware with `site.password` on the protected route group in `routes/web.php`

## 3. Controller

- [x] 3.1 Create `SitePasswordController` with `showForm`, `login`, and `logout` methods

## 4. Views

- [x] 4.1 Replace `login.blade.php` with single password field form using guest layout
- [x] 4.2 Hide user-specific nav items in `navigation-menu.blade.php` (profile dropdown, API tokens, user menu) behind `@auth` check

## 5. Configuration

- [x] 5.1 Add `APP_PASSWORD=` to `debian/env.example`

## 6. Verify

- [x] 6.1 Test: no `APP_PASSWORD` set → all routes accessible without login
- [x] 6.2 Test: `APP_PASSWORD=test` → redirected to password prompt, correct password grants access
- [x] 6.3 Test: wrong password shows error, stays on prompt
- [x] 6.4 Test: `/scriptexecute` remains accessible regardless of password setting
