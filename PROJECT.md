# PROJECT.md

> Working reference for development on cmprovision. Not user-facing — see README.md for setup/usage.

## Context

Raspberry Pi Compute Module Provisioning System. Forked from `raspberrypi/cmprovision` (upstream appears abandoned). Laravel 8 web app that runs on a Pi 4, turning it into a mass-provisioning station for CM3, CM3+, CM4, and Pi 4 boards.

Licensed BSD 3-Clause (Copyright 2021, Raspberry Pi).

## Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                     Pi 4 Server (172.20.0.1)                    │
│                                                                 │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌───────────────┐  │
│  │  Nginx   │  │ dnsmasq  │  │ rpiboot  │  │ Laravel Queue │  │
│  │ (Web UI) │  │(DHCP/TFTP│  │(USB boot │  │  (SHA256 jobs) │  │
│  │          │  │ for CM4) │  │ for CM3) │  │               │  │
│  └────┬─────┘  └────┬─────┘  └────┬─────┘  └───────────────┘  │
│       │              │              │                            │
│  ┌────┴──────────────┴──────────────┴────────────────────┐     │
│  │              Laravel App (SQLite)                      │     │
│  │  Models: Project, Image, Script, Label, Cm, Firmware  │     │
│  │  UI: Livewire + Tailwind    API: Sanctum tokens       │     │
│  └───────────────────────────────────────────────────────┘     │
│       │ eth0                    │ USB                           │
└───────┼─────────────────────────┼───────────────────────────────┘
        │                         │
   ┌────▼─────┐             ┌────▼─────┐
   │ Ethernet │             │ USB Hub  │
   │ Switch   │             │          │
   └──┬──┬──┬─┘             └──┬──┬──┬─┘
      │  │  │                  │  │  │
     CM4 CM4 CM4            CM3 CM3 CM3
```

## Tech Stack

| Layer        | Technology                          |
|--------------|-------------------------------------|
| Framework    | Laravel 8.x (PHP 7.3+/8.0+)        |
| UI           | Livewire 2 + Alpine.js + Tailwind 2 |
| Auth         | Jetstream + Fortify + Sanctum       |
| Database     | SQLite                              |
| Web server   | Nginx + PHP-FPM                     |
| Build        | Laravel Mix (Webpack) + PostCSS     |
| Packaging    | Debian .deb package                 |
| Services     | dnsmasq (DHCP/TFTP), rpiboot (USB boot), Laravel queue worker |

## Data Model

```
┌──────────┐       ┌──────────┐       ┌──────────┐
│ Project  │──────▶│  Image   │       │ Firmware │
│          │  1:1  │          │       │ (files)  │
│          │       └──────────┘       └──────────┘
│          │
│          │──────▶┌──────────┐
│          │  M:N  │  Script  │  (pivot: project_script)
│          │       │          │  script_type: pre/postinstall
│          │       │          │  priority: execution order
│          │       └──────────┘
│          │
│          │──────▶┌──────────┐
│          │  1:1  │  Label   │  printer_type: ftp/command
│          │       └──────────┘
│          │
│          │◀──────┌──────────┐
│          │  1:N  │    Cm    │  one record per provisioned module
└──────────┘       │          │
                   │          │──────▶┌──────────┐
                   └──────────┘       │  Cmlog   │  (linked by serial)
                                      └──────────┘

┌──────────┐  ┌──────────────────┐
│   Host   │  │ EthernetSwitch   │
│(static   │  │ (non-Eloquent,   │
│ DHCP)    │  │  SNMP queries)   │
└──────────┘  └──────────────────┘

┌──────────┐
│ Setting  │  key-value store (active_project, ethernetswitch_ip, etc.)
└──────────┘
```

### Key fields on Cm

serial, mac, model, memory_in_gb, storage (bytes), firmware, image_filename, image_sha256, pre/post_script_output, script_return_code, provisioning_board, provisioning_started_at, provisioning_complete_at, temp1, temp2.

### Key fields on Project

name, device (enum: 'cm4'), storage (device path e.g. /dev/mmcblk0), image_id, label_id, label_moment (never/preinstall/postinstall), eeprom_firmware (path), eeprom_settings (text), verify (boolean).

## Provisioning Flow

This is the core domain logic. The entire system exists to execute this sequence:

```
1. Admin configures Project
   (image + scripts + firmware + label + storage target + verify flag)
   └─▶ Sets as active project in Settings

2. CM boots
   ├─ CM4: network boot via dnsmasq (DHCP + TFTP)
   └─ CM3: USB boot via rpiboot

3. CM boots scriptexecute/ environment
   (minimal Linux: kernel + initramfs from scriptexecute/)
   └─▶ Kernel cmdline triggers HTTP GET to server

4. GET /scriptexecute?serial=X&mac=Y&model=Z&memory=N&storage=N&temp=T&...
   └─▶ ScriptExecuteController::startProvisioning()
       ├─ Creates/updates Cm record
       ├─ Validates image fits in storage
       ├─ Queries Ethernet switch for port name (SNMP, if configured)
       ├─ Prints label (if preinstall)
       └─ Returns generated shell script (scriptexecute.blade.php)

5. CM executes returned script:
   a. Preinstall phase
      ├─ Flash EEPROM firmware (if selected): download, SHA256 verify, flashrom
      ├─ Run custom preinstall scripts (ordered by priority)
      └─ Upload log: POST /scriptexecute?phase=preinstall&retcode=X

   b. Image write phase
      ├─ BLKDISCARD storage
      ├─ curl image | decompress (gz/xz/bz2) | dd to storage
      └─ partprobe

   c. Postinstall phase
      ├─ Verify: read back storage, SHA256 compare (if enabled)
      ├─ Run custom postinstall scripts
      └─ Upload log: POST /scriptexecute?phase=postinstall&retcode=X

   d. Completion
      └─ GET /scriptexecute?alldone=1&temp=T&verify=Y
          ├─ Updates Cm record (timestamps, temp)
          ├─ Prints label (if postinstall)
          ├─ Dispatches CmProvisioningComplete event
          └─ CM blinks LEDs to signal done

6. CM record in database = complete audit trail
```

## Code Organization

### Controllers

| File | Purpose |
|------|---------|
| `app/Http/Controllers/ScriptExecuteController.php` | **Core provisioning endpoint.** Handles all 4 phases: start, firmware registration, log upload, completion. Unauthenticated — CMs have no credentials. |
| `app/Http/Controllers/AddImageController.php` | Image upload, dispatches SHA256 job. |

### Livewire Components (`app/Http/Livewire/`)

Each is a full CRUD UI with a corresponding blade view in `resources/views/livewire/`.

| Component | Notes |
|-----------|-------|
| `Cms.php` | List/filter/export provisioned modules |
| `Projects.php` | Project CRUD. **Complex**: handles EEPROM binary patching (extracts/modifies bootconf.txt embedded in pieeprom.bin) |
| `Images.php` | Image list |
| `Scripts.php` | Script CRUD |
| `Labels.php` | Label template/printer CRUD |
| `Firmwares.php` | Browse firmware files from storage/app/firmware/ |
| `Settings.php` | System settings, service status, static DHCP management |

### Models (`app/Models/`)

| Model | Eloquent? | Notes |
|-------|-----------|-------|
| `Project.php` | Yes | `getActive()`/`getActiveId()` retrieves active project from settings |
| `Image.php` | Yes | Deletes physical file on model deletion |
| `Script.php` | Yes | script_type + priority determines execution order |
| `Label.php` | Yes | Template variable substitution: `$mac`, `$serial`, `$provisionboard` |
| `Cm.php` | Yes | One record per provisioned device |
| `Cmlog.php` | Yes | Event log per provisioning session |
| `Host.php` | Yes | Static DHCP (MAC→IP in 172.20.0.0/16) |
| `Setting.php` | Yes | Key-value store, no timestamps |
| `Firmware.php` | No | File-based, scans storage/app/firmware/{stable,beta}/ |
| `EthernetSwitch.php` | No | SNMP queries to managed switches for port identification |
| `User.php` | Yes | Standard auth |

### Routes

| Route | Auth | Purpose |
|-------|------|---------|
| `ANY /scriptexecute` | None | Called by CMs during provisioning |
| `GET /dashboard` | Yes | Recent Cmlog entries |
| `GET /cms,/images,/projects,...` | Yes | Livewire UI pages |
| `POST /addImage` | Yes | Image upload |
| `GET/POST/PATCH/DELETE /api/*` | Sanctum | REST API with granular token abilities |

### Key Views

| File | Purpose |
|------|---------|
| `resources/views/scriptexecute.blade.php` | **The provisioning script.** Blade template that generates the shell script sent to CMs. Contains the entire write/verify/flash/label logic. |
| `resources/views/livewire/*.blade.php` | UI components |

### Background Jobs

| Job | Purpose |
|-----|---------|
| `app/Jobs/ComputeSHA256.php` | Computes compressed + uncompressed SHA256 and uncompressed size for images. Required for verify feature. |

### Deployment (`debian/`)

Packaged as a .deb (`cmprovision4`). Key files:

| File | Purpose |
|------|---------|
| `debian/control` | Package metadata and dependencies |
| `debian/postinst` | Post-install: detects PHP version, configures nginx, initializes DB, sets up systemd services |
| `debian/cmprovision-dnsmasq.service` | DHCP/TFTP for CM4 network boot |
| `debian/cmprovision-rpiboot.service` | USB boot server for CM3/CM3+ |
| `debian/010_cmprovision` | Nginx site config |

### Boot Environment (`scriptexecute/`)

Minimal Linux booted on CMs during provisioning. Contains kernel, GPU firmware, device trees, initramfs (`scriptexecute.img`), and config files. `config.txt` sets up GPIO pull-ups for jumper reading (board ID) and USB gadget mode. `cmdline.txt` passes hardware info to the provisioning script URL.

## Board Identification

Two methods to identify which physical board slot a CM occupies:

1. **Ethernet switch SNMP** (CM4): Queries managed switch MAC address table via BRIDGE-MIB, maps to port name/alias. Configured via `artisan configure:ethernet-switch`.
2. **GPIO jumpers** (CM3): Reads pins 5, 13, 21 (pull-up, jumper-to-ground = 0). Binary value stored as board ID.

## Development

```bash
composer install          # PHP dependencies
npm install               # Frontend dependencies
npm run dev               # Build CSS/JS (includes all Tailwind classes)
npm run prod              # Production build (tree-shaken Tailwind)
./artisan view:cache      # Regenerate blade cache after .blade.php changes
./artisan auth:create-user # Create web UI credentials
```

Database: `database/database.sqlite`. Migrations in `database/migrations/`.

## Gotchas

- `ScriptExecuteController` is unauthenticated by design — the isolated 172.20.0.0/16 network is the security boundary.
- `Projects.php` Livewire component contains non-trivial binary manipulation for EEPROM firmware patching (bootconf.txt extraction/injection into pieeprom.bin).
- `Firmware.php` model is file-based, not Eloquent — scans filesystem for .bin files.
- `EthernetSwitch.php` is also non-Eloquent — makes live SNMP queries.
- Image deletion cascades to physical file removal (see `Image::boot()` delete event).
- The `scriptexecute.blade.php` template is a shell script rendered by Blade — mixing PHP templating with bash. Read carefully.
- Laravel 8.x — check docs at https://laravel.com/docs/8.x/ (not current Laravel).
