## ADDED Requirements

### Requirement: One-command dev environment startup

The dev environment SHALL start with `docker compose up` and be fully functional without manual setup steps.

#### Scenario: First run on clean checkout

- **WHEN** developer runs `docker compose up` with no prior state
- **THEN** the container installs composer dependencies
- **AND** generates an APP_KEY
- **AND** creates the SQLite database
- **AND** runs migrations and seeders
- **AND** the web UI is accessible at http://localhost:8080

#### Scenario: Subsequent runs

- **WHEN** developer runs `docker compose up` with existing state
- **THEN** the container starts without re-running initialization
- **AND** the web UI is accessible within seconds

### Requirement: Source code mounting

The container SHALL mount the host project directory so code changes are reflected immediately.

#### Scenario: Edit a Blade template

- **WHEN** developer edits a `.blade.php` file on the host
- **THEN** the change is visible on next browser refresh without container restart

#### Scenario: Edit a PHP class

- **WHEN** developer edits a PHP file on the host
- **THEN** the change is reflected on next request without container restart

### Requirement: All required PHP extensions available

The container SHALL include all PHP extensions needed by the application.

#### Scenario: SNMP feature works

- **WHEN** the Ethernet switch feature is used
- **THEN** php-snmp functions are available and functional

### Requirement: Queue worker runs in dev

The container SHALL run a Laravel queue worker for background job processing.

#### Scenario: Image upload triggers SHA256 computation

- **WHEN** developer uploads an image via the web UI
- **THEN** the queue worker picks up the ComputeSHA256 job
- **AND** the SHA256 hash is computed in the background
