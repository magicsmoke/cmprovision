## 1. Create Postinstall Script

- [x] 1.1 Write the postinstall shell script that reads eth0 MAC, calls the serial prov API, mounts rootfs, writes `/etc/device-serial`, `/etc/hostname`, updates `/etc/hosts`, unmounts, and triggers label printing — all with error handling that exits non-zero on failure

## 2. Add Script to cmprovision

- [x] 2.1 Add the script as a postinstall script via database seeder (or document manual addition via Scripts UI) so it's available in the cmprovision project configuration
