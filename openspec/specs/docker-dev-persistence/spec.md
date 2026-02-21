## ADDED Requirements

### Requirement: Database persists across container restarts

The SQLite database SHALL persist when the container is stopped and restarted.

#### Scenario: Stop and restart container

- **WHEN** developer runs `docker compose down` then `docker compose up`
- **THEN** all previously created data (projects, images, CMs) is intact

### Requirement: Uploaded files persist across container restarts

Uploaded images and firmware files SHALL persist when the container is stopped and restarted.

#### Scenario: Stop and restart with uploaded images

- **WHEN** developer has uploaded OS images
- **AND** runs `docker compose down` then `docker compose up`
- **THEN** all uploaded images and firmware files are still available

### Requirement: Clean slate possible

The developer SHALL be able to reset to a fresh state.

#### Scenario: Full reset

- **WHEN** developer runs `docker compose down -v`
- **THEN** all persistent data (database, uploads) is removed
- **AND** next `docker compose up` performs fresh initialization
