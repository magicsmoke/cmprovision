## Why

When uploading compressed disk images (which can be large), the user gets zero feedback after clicking "Upload" — the button disables and the page appears frozen. There's no way to tell if the upload is progressing, stalled, or how long it will take.

## What Changes

- Intercept the existing upload form submission with JavaScript and use `XMLHttpRequest` to submit it instead, enabling access to upload progress events.
- Display a progress bar with percentage during the upload.
- Show appropriate states: uploading (with progress), success, and error.
- No backend changes — the existing `POST /addImage` endpoint stays as-is.

## Capabilities

### New Capabilities
- `upload-progress`: Client-side upload progress feedback via XHR progress events, including a visual progress bar with percentage.

### Modified Capabilities

_(none — no spec-level requirement changes to existing capabilities)_

## Impact

- `resources/views/livewire/addimage.blade.php` — form submission and UI changes
- Potentially a small JS addition (inline or in `resources/js/`) for the XHR logic
- No backend, API, or dependency changes
