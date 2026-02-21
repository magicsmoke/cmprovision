## Context

The image upload form (`addimage.blade.php`) is a modal that uses a traditional `<form method="post">` submission to `POST /addImage`. The only feedback is disabling the submit button via `onsubmit`. For large compressed disk images (potentially multiple GiB), this leaves the user with no indication of progress.

The form lives inside a Livewire component but doesn't use Livewire's file upload system — it submits directly to `AddImageController::store()`.

## Goals / Non-Goals

**Goals:**
- Show a real progress bar with percentage during image upload
- Show clear error state if the upload fails
- Keep the existing backend endpoint unchanged

**Non-Goals:**
- Post-upload processing feedback (SHA256 computation) — out of scope
- Switching to Livewire's file upload system
- Drag-and-drop or multi-file upload

## Decisions

### Use XHR with `upload.onprogress` instead of native form submission

**Rationale:** `XMLHttpRequest.upload.onprogress` gives us `loaded`/`total` bytes, which translates directly to a percentage progress bar. The `fetch` API does not expose upload progress. No backend changes needed — we just intercept the form submit and send the same `FormData` via XHR.

**Alternative considered:** Livewire `wire:model` file uploads — rejected because Livewire introduces its own temporary upload step and size limits, adding complexity for no benefit here. The backend endpoint works fine as-is.

### Inline the JS in the Blade template

**Rationale:** This is a small, self-contained piece of behavior (~30 lines) tied directly to this one form. No need for a separate JS file or build step. Alpine.js is already available in the page and can manage the reactive state (progress percentage, upload status) cleanly.

**Alternative considered:** Separate JS module in `resources/js/` — overkill for this scope.

### Use Alpine.js for state management

**Rationale:** Alpine.js is already loaded on every page via `app.js`. It's the natural choice for managing reactive UI state (progress value, uploading/error/done states) within a Blade template. Avoids manual DOM manipulation.

### Progress bar replaces the upload button area during upload

**Rationale:** Once the upload starts, the Upload/Cancel buttons are no longer relevant. Replacing them with the progress bar keeps the UI clean and focused. On completion, redirect to the images list (same as current behavior via the controller's redirect).

## Risks / Trade-offs

- **XHR doesn't follow redirects the way a form POST does** → After XHR completes successfully, we'll redirect via `window.location` to match the controller's redirect response.
- **Very large files may hit PHP/nginx limits before progress completes** → This is an existing constraint (already documented in the form notes). No change in behavior.
- **Browser compatibility** → `XMLHttpRequest.upload.onprogress` is supported in all modern browsers. Not a concern.
