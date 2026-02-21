## ADDED Requirements

### Requirement: Upload progress bar
The system SHALL display a progress bar with percentage when an image upload is in progress.

#### Scenario: Upload starts
- **WHEN** the user clicks the Upload button with a file selected
- **THEN** the form buttons are replaced with a progress bar showing 0%

#### Scenario: Upload progresses
- **WHEN** the browser receives upload progress events from the server
- **THEN** the progress bar updates to reflect the current percentage (0-100%)

#### Scenario: Upload completes successfully
- **WHEN** the upload finishes and the server responds with success
- **THEN** the user is redirected to the images list page

### Requirement: Upload error feedback
The system SHALL display an error message if the upload fails.

#### Scenario: Server returns an error
- **WHEN** the upload completes but the server responds with a validation error or server error
- **THEN** an error message is displayed to the user and the form is reset to allow retrying

#### Scenario: Network failure
- **WHEN** the upload fails due to a network error
- **THEN** an error message is displayed to the user and the form is reset to allow retrying

### Requirement: No backend changes
The upload progress feature SHALL be implemented entirely client-side using the existing `POST /addImage` endpoint.

#### Scenario: Backend endpoint unchanged
- **WHEN** the upload is submitted via XHR
- **THEN** the same `POST /addImage` endpoint is used with the same `multipart/form-data` payload including the CSRF token
