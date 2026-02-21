### Requirement: Preference Persistence

The user's theme preference persists across page loads and sessions.

#### Scenario: Save preference

- **WHEN** user selects a theme
- **THEN** the preference is stored in localStorage

#### Scenario: Returning visit

- **WHEN** user loads any page
- **THEN** the previously selected theme is applied
- **AND** no flash of the wrong theme occurs (FOUC prevention)

#### Scenario: No saved preference

- **WHEN** user visits for the first time (no localStorage value)
- **THEN** the light theme is applied by default
