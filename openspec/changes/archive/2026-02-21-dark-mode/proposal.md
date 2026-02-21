## Why

The app only supports a light theme with hardcoded Tailwind color classes. Users working in low-light environments or who prefer dark interfaces have no option. Adding dark mode improves usability and is a baseline expectation for modern web apps.

## What Changes

- Add a dark mode toggle to the navigation bar
- Persist theme preference in localStorage
- Apply dark variant Tailwind classes across all views, components, and modals
- Configure Tailwind for class-based dark mode

## Capabilities

### New Capabilities
- `dark-mode-toggle`: User can switch between light and dark themes via a nav bar control
- `dark-mode-persistence`: Theme preference persists across sessions via localStorage
- `dark-mode-theme`: All UI elements render with appropriate dark color scheme

### Modified Capabilities
<!-- None — no existing spec-level behavior is changing -->

## Impact

- `tailwind.config.js`: Add `darkMode: 'class'`
- `resources/views/layouts/app.blade.php`: Dark class on `<html>`, dark backgrounds
- `resources/views/layouts/guest.blade.php`: Dark backgrounds
- `resources/views/navigation-menu.blade.php`: Toggle control, dark nav styling
- `resources/views/livewire/*.blade.php`: Dark variants on all pages/modals (~15 files)
- `resources/views/vendor/jetstream/components/*.blade.php`: Dark variants on shared components
- `resources/views/auth/*.blade.php`: Dark variants on auth pages
- `resources/views/profile/*.blade.php`: Dark variants on profile pages