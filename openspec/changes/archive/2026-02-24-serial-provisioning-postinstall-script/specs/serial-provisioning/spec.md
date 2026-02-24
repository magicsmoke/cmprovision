## ADDED Requirements

### Requirement: Script assigns serial number via API

The postinstall script SHALL read the eth0 MAC address from `/sys/class/net/eth0/address` and call `POST http://$SERVER:8000/api/assign` with `{"mac": "<mac>"}` to obtain a product serial number.

#### Scenario: Successful serial assignment
- **WHEN** the cm4_serial_prov API returns a response containing a serial number
- **THEN** the script extracts the serial and proceeds to write it to the rootfs

#### Scenario: API unreachable or returns error
- **WHEN** the API call fails or returns an empty/null serial
- **THEN** the script exits with a non-zero return code, failing the provision

### Requirement: Script writes serial to flashed rootfs

The postinstall script SHALL mount `$PART2` (ext4 rootfs), write the serial number to `/etc/device-serial`, set `/etc/hostname` to `ov-pcu-YYWW-SEQ` format, and update `/etc/hosts` with the new hostname.

#### Scenario: Successful file writes
- **WHEN** the rootfs mounts successfully and files are written
- **THEN** `/etc/device-serial` contains the serial string, `/etc/hostname` contains `ov-pcu-YYWW-SEQ`, and `/etc/hosts` has a `127.0.1.1` entry with the new hostname

#### Scenario: Mount or write failure
- **WHEN** the rootfs cannot be mounted or any file write fails
- **THEN** the script exits with a non-zero return code, failing the provision

### Requirement: Script triggers label printing

The postinstall script SHALL call `POST http://$SERVER:8000/api/print/{serial}` after successfully writing files to the rootfs to trigger label printing on the Brother QL printer.

#### Scenario: Label print triggered
- **WHEN** serial assignment and file writes succeed
- **THEN** the script calls the print endpoint and logs the result

#### Scenario: Label print fails
- **WHEN** the print API call fails
- **THEN** the script exits with a non-zero return code, failing the provision

### Requirement: Script unmounts rootfs after writes

The postinstall script SHALL unmount `$PART2` after writing files, regardless of whether subsequent steps (label printing) succeed or fail.

#### Scenario: Cleanup after successful writes
- **WHEN** files have been written to the rootfs
- **THEN** the rootfs is unmounted before proceeding to label printing
