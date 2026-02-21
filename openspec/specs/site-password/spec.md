## ADDED Requirements

### Requirement: No auth when APP_PASSWORD is empty

When `APP_PASSWORD` is not set or empty, the app SHALL be fully accessible without any login.

#### Scenario: No password configured

- **WHEN** `APP_PASSWORD` env var is empty or not set
- **THEN** all web routes are accessible without authentication
- **AND** no login page is shown
- **AND** no redirect to login occurs

### Requirement: Password prompt when APP_PASSWORD is set

When `APP_PASSWORD` is set, the app SHALL require a password before granting access to protected routes.

#### Scenario: Unauthenticated request with password set

- **WHEN** `APP_PASSWORD` is set
- **AND** user has no active authenticated session
- **AND** user requests a protected route
- **THEN** user is redirected to a password prompt page

#### Scenario: Correct password entered

- **WHEN** user submits the correct password
- **THEN** session is marked as authenticated
- **AND** user is redirected to the originally requested page (or dashboard)

#### Scenario: Incorrect password entered

- **WHEN** user submits an incorrect password
- **THEN** an error message is displayed
- **AND** user remains on the password prompt page

#### Scenario: Authenticated session persists

- **WHEN** user has previously entered the correct password in this session
- **AND** user requests another protected route
- **THEN** access is granted without re-prompting

### Requirement: Password prompt is a single field

The password page SHALL show only a password field — no username, no email.

#### Scenario: Password page layout

- **WHEN** password prompt page is displayed
- **THEN** it shows a single password input field and a submit button
- **AND** no username or email field is present

### Requirement: scriptexecute remains unprotected

The `/scriptexecute` route SHALL remain accessible without any authentication regardless of `APP_PASSWORD` setting.

#### Scenario: Device access with password set

- **WHEN** `APP_PASSWORD` is set
- **AND** a CM device requests `/scriptexecute`
- **THEN** access is granted without password (uses serial number authorization)

### Requirement: User-specific UI elements hidden

Navigation items related to per-user features SHALL be hidden.

#### Scenario: Navigation bar

- **WHEN** user views the navigation bar
- **THEN** profile management, API tokens, and user dropdown are not shown
