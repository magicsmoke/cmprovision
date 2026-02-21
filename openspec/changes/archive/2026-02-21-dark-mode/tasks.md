## 1. Infrastructure

- [x] 1.1 Add `darkMode: 'class'` to `tailwind.config.js`
- [x] 1.2 Add FOUC-prevention script to `<head>` in `layouts/app.blade.php`
- [x] 1.3 Add FOUC-prevention script to `<head>` in `layouts/guest.blade.php`

## 2. Toggle

- [x] 2.1 Add dark mode toggle (sun/moon SVG + Alpine.js) to `navigation-menu.blade.php`

## 3. Layouts

- [x] 3.1 Add dark classes to `layouts/app.blade.php`
- [x] 3.2 Add dark classes to `layouts/guest.blade.php`

## 4. Core Pages (Livewire)

- [x] 4.1 Add dark classes to `dashboard.blade.php`
- [x] 4.2 Add dark classes to `cms.blade.php`
- [x] 4.3 Add dark classes to `images.blade.php`
- [x] 4.4 Add dark classes to `projects.blade.php`
- [x] 4.5 Add dark classes to `scripts.blade.php`
- [x] 4.6 Add dark classes to `labels.blade.php`
- [x] 4.7 Add dark classes to `firmware.blade.php`
- [x] 4.8 Add dark classes to `settings.blade.php`

## 5. Modals (Livewire)

- [x] 5.1 Add dark classes to `viewcm.blade.php`
- [x] 5.2 Add dark classes to `editproject.blade.php`
- [x] 5.3 Add dark classes to `addimage.blade.php`
- [x] 5.4 Add dark classes to `editscript.blade.php`
- [x] 5.5 Add dark classes to `editlabel.blade.php`
- [x] 5.6 Add dark classes to `addstaticip.blade.php`
- [x] 5.7 Add dark classes to `viewlog.blade.php`

## 6. Jetstream Components

- [x] 6.1 Add dark classes to shared components (buttons, inputs, modals, dropdowns, etc.)

## 7. Auth Pages

- [x] 7.1 Add dark classes to auth views (login, register, forgot-password, etc.)

## 8. Profile Pages

- [x] 8.1 Add dark classes to profile views

## 9. Other Pages

- [x] 9.1 Add dark classes to `welcome.blade.php`, `scriptexecute.blade.php`, `terms.blade.php`, `policy.blade.php`

## 10. Verify

- [x] 10.1 Build CSS with `npm run dev` and verify no purge issues (run in Docker)
- [x] 10.2 Visual check: toggle works, no FOUC, all surfaces themed (manual)
