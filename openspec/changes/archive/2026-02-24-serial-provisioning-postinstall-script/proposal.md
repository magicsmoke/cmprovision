## Why

Serial numbers are currently assigned on first boot via `provision_serial.sh`, which requires the CM4 to boot its flashed image, connect to the serial provisioning server, and shut down. This adds an extra manufacturing step and delays label printing until after first boot. Moving serial assignment to the provisioning (flashing) stage eliminates that step — the serial is baked into the image and the label prints immediately when flashing completes.

## What Changes

- Create a cmprovision postinstall script that calls the cm4_serial_prov API (`POST $SERVER:8000/api/assign`) with the CM4's eth0 MAC address during provisioning
- Write the assigned serial to `/etc/device-serial` on the flashed rootfs
- Set the hostname (`/etc/hostname`) to `ov-pcu-YYWW-SEQ` format on the flashed rootfs
- Trigger label printing via the cm4_serial_prov API (`POST $SERVER:8000/api/print/{serial}`)
- Fail the provision if serial assignment or file writes fail

## Capabilities

### New Capabilities
- `serial-provisioning`: Postinstall script that assigns a product serial number from the cm4_serial_prov API and bakes it into the flashed image during provisioning

### Modified Capabilities

## Impact

- **Scripts**: New postinstall script added via the cmprovision Scripts UI
- **External dependency**: Requires cm4_serial_prov server running on the same Pi at `$SERVER:8000`
- **First-boot behavior**: `provision_serial.sh` on the CM4 becomes a no-op (it already skips when `/etc/device-serial` exists) — can be removed from future base images
- **Label printing**: Product labels (Brother QL, QR code) now print at flash time instead of first boot
