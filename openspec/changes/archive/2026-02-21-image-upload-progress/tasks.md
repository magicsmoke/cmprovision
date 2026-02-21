## 1. XHR Upload with Progress

- [x] 1.1 Add Alpine.js component to the upload form with state for progress percentage, uploading flag, and error message
- [x] 1.2 Intercept form submission: prevent default, build FormData (file + CSRF token), submit via XHR with `upload.onprogress` handler
- [x] 1.3 On XHR success, redirect to the images list page via `window.location`
- [x] 1.4 On XHR error or network failure, display error message and reset form to allow retry

## 2. Progress Bar UI

- [x] 2.1 Add progress bar element (hidden by default) that replaces the Upload/Cancel buttons during upload
- [x] 2.2 Bind progress bar width and percentage text to Alpine.js state
- [x] 2.3 Style progress bar to match existing dark mode support (use existing Tailwind classes)
