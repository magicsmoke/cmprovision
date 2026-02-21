## Context

The app uses Tailwind CSS v2.2.19 with Laravel Mix. All colors are hardcoded utility classes across ~35 view files and ~39 Jetstream components. Alpine.js v2.8.2 is already available for client-side interactivity.

## Goals / Non-Goals

**Goals:**
- Class-based dark mode toggle that works instantly
- Persistent preference via localStorage
- Dark variants on all visible UI surfaces
- No FOUC (flash of unstyled content) on page load

**Non-Goals:**
- System preference detection (`prefers-color-scheme`) — keep it simple with explicit toggle only
- Custom color palette or design refresh — just dark variants of existing colors
- Dark mode for email templates or PDF exports

## Decisions

### Decision 1: Class-based dark mode via Tailwind

Set `darkMode: 'class'` in `tailwind.config.js`. Toggle the `dark` class on the `<html>` element.

**Alternative considered:** `darkMode: 'media'` (follows OS preference). Rejected because it removes user control and can't be toggled independently of system settings.

### Decision 2: Alpine.js for toggle logic

Use Alpine.js (already in the project) to manage the toggle state. No new dependencies needed.

**Alternative considered:** Livewire-driven toggle with server-side preference storage. Rejected because it adds unnecessary server round-trips for a purely visual preference and requires auth to persist.

### Decision 3: FOUC prevention

Place a blocking `<script>` in `<head>` (before CSS loads) that reads localStorage and sets the `dark` class on `<html>` immediately. This runs synchronously before any rendering.

### Decision 4: Color mapping

| Light | Dark |
|-------|------|
| `bg-gray-100` (page bg) | `dark:bg-gray-900` |
| `bg-white` (cards/modals) | `dark:bg-gray-800` |
| `bg-gray-50` (modal footers) | `dark:bg-gray-700` |
| `bg-gray-100` (table headers) | `dark:bg-gray-700` |
| `text-gray-900` | `dark:text-gray-100` |
| `text-gray-800` | `dark:text-gray-100` |
| `text-gray-700` | `dark:text-gray-200` |
| `text-gray-600` | `dark:text-gray-300` |
| `text-gray-500` | `dark:text-gray-400` |
| `border-gray-100/200/300` | `dark:border-gray-600/700` |
| `bg-gray-500 opacity-75` (overlay) | Keep as-is (works for both) |
| Semantic buttons (blue/red/green) | Keep as-is (work on dark backgrounds) |

### Decision 5: Toggle icon

Sun/moon icon pair (inline SVG). Sun shown in dark mode (click to go light), moon shown in light mode (click to go dark). Placed in the nav bar next to the user dropdown.

## Risks / Trade-offs

- **Tailwind v2 purge may strip dark classes** → Ensure purge config scans all blade files for `dark:` prefixed classes. May need to verify purge isn't too aggressive.
- **Large number of files to modify** → Repetitive but low-risk. Each file follows the same color mapping pattern.
- **Jetstream component overrides** → We're already using `vendor/jetstream/components/` overrides so we have full control over these templates.
