## Context

cmprovision runs on a Pi 4 and flashes CM4 modules via network boot. A separate service (cm4_serial_prov) runs on the same Pi at port 8000, assigning YYWWSSSS serial numbers keyed by MAC address. Currently, serials are assigned on the CM4's first boot via `provision_serial.sh`. This change moves serial assignment into the provisioning pipeline as a postinstall script managed through the cmprovision Scripts UI.

The postinstall script runs in cmprovision's minimal scriptexecute environment (not the flashed OS). Available tools are limited — no `jq`, no `hostnamectl`, no `sed -i`. The script must use POSIX-compatible shell and tools already present in the scriptexecute initramfs (curl, grep, cut, mount, umount, basic coreutils).

## Goals / Non-Goals

**Goals:**
- Assign a product serial number during provisioning by calling the cm4_serial_prov API
- Write `/etc/device-serial` and `/etc/hostname` to the flashed rootfs
- Update `/etc/hosts` with the new hostname
- Print a label via the cm4_serial_prov API on successful assignment
- Fail the entire provision if any step fails

**Non-Goals:**
- Modifying the cm4_serial_prov server (it already supports eth0 MAC — any unique MAC works)
- Removing `provision_serial.sh` from the base image (it safely no-ops when `/etc/device-serial` exists)
- Modifying cmprovision's core code — this is a UI-managed postinstall script

## Decisions

### Decision 1: Use eth0 MAC instead of wlan0

The scriptexecute environment boots via ethernet — wlan0 drivers aren't loaded. eth0 MAC is available at `/sys/class/net/eth0/address` and is equally valid as a unique device identifier for the serial prov API. Existing devices were provisioned with wlan0 MAC, so new devices will have a different MAC association, but nothing downstream depends on which MAC was used.

### Decision 2: Parse JSON response with grep/cut

The scriptexecute environment doesn't include `jq`. The API response is simple (`{"serial": "26061", "mac": "...", ...}`), so `grep -o` with `cut` is sufficient. No need to add `jq` to the initramfs.

### Decision 3: Mount rootfs, write files, unmount

The image is already written and `partprobe` has run by the time postinstall scripts execute. The script mounts `$PART2` (rootfs, ext4) to a temporary mountpoint, writes the serial and hostname files, then unmounts. The environment variables `$PART1` and `$PART2` are already exported by the scriptexecute template.

### Decision 4: Fail provision on any error

`set -e` in the script plus explicit checks after the API call and file writes. A non-zero exit code from a postinstall script already causes cmprovision to mark the provision as failed (scriptexecute.blade.php lines 86-89).

### Decision 5: Hostname format matches existing convention

`provision_serial.sh` uses `ov-pcu-YYWW-SEQ` where YYWW is the first 4 chars and SEQ is everything after. The postinstall script uses the same format.

## Risks / Trade-offs

- **[cm4_serial_prov server down]** → Provision fails. Since both services run on the same Pi, this is unlikely but protects against silent failures. The operator would see the failure in the cmprovision UI.
- **[Rootfs partition layout changes]** → Script assumes `$PART2` is ext4 rootfs with `/etc/`. This is standard for Raspberry Pi OS images. If a different image layout is used, the script would need updating.
- **[Duplicate MAC on re-provision]** → The cm4_serial_prov API is idempotent — same MAC returns same serial. Re-provisioning the same CM4 gets the same serial, which is correct behavior.
