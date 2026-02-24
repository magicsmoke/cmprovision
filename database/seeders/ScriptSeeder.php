<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScriptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $script = <<<'EOF'
#!/bin/sh
set -e

parted -s $STORAGE "resizepart 2 -1" "quit"
resize2fs -f $PART2

mkdir -p /mnt/boot /mnt/root
mount -t ext4 $PART2 /mnt/root
umount /mnt/root
mount -t vfat $PART1 /mnt/boot
sed -i 's| init=/usr/lib/raspi-config/init_resize\.sh||' /mnt/boot/cmdline.txt
umount /mnt/boot
EOF;

        DB::table('scripts')->insert([
            'name' => 'Resize ext4 partition',
            'script_type' => 'postinstall',
            'priority' => 50,
            'bg' => false,
            'script' => $script
        ]);

        $script = <<<'EOF'
#!/bin/sh
set -e

mkdir -p /mnt/boot
mount -t vfat $PART1 /mnt/boot
echo "dtoverlay=dwc2,dr_mode=host" >> /mnt/boot/config.txt
umount /mnt/boot
EOF;

        DB::table('scripts')->insert([
            'name' => 'Add dtoverlay=dwc2 to config.txt',
            'script_type' => 'postinstall',
            'priority' => 100,
            'bg' => false,
            'script' => $script
        ]);


        $script = <<<'EOF'
#!/bin/sh
set +e

MAXSIZEKB=`mmc extcsd read /dev/mmcblk0 | grep MAX_ENH_SIZE_MULT -A 1 | grep -o '[0-9]\+ '`
mmc enh_area set -y 0 $MAXSIZEKB /dev/mmcblk0
if [ $? -eq 0 ]; then
    reboot -f
fi
EOF;

        DB::table('scripts')->insert([
            'name' => 'Format eMMC as pSLC (one time settable only)',
            'script_type' => 'preinstall',
            'priority' => 100,
            'bg' => false,
            'script' => $script
        ]);

        $script = <<<'EOF'
#!/bin/sh
set -e

PROV_API="http://$SERVER:8000"

# Read eth0 MAC address
MAC=$(cat /sys/class/net/eth0/address)
if [ -z "$MAC" ]; then
    echo "ERROR: Could not read eth0 MAC address"
    exit 1
fi
echo "eth0 MAC: $MAC"

# Assign serial number via provisioning API
echo "Requesting serial from $PROV_API ..."
RESPONSE=$(curl --silent --show-error --fail --max-time 10 \
    -X POST \
    -H "Content-Type: application/json" \
    -d "{\"mac\": \"$MAC\"}" \
    "$PROV_API/api/assign")

if [ -z "$RESPONSE" ]; then
    echo "ERROR: Empty response from serial provisioning API"
    exit 1
fi

# Parse serial from JSON response (no jq in scriptexecute environment)
SERIAL=$(echo "$RESPONSE" | grep -o '"serial":"[^"]*"' | cut -d'"' -f4)
if [ -z "$SERIAL" ]; then
    # Try with spaces around colon (API may format either way)
    SERIAL=$(echo "$RESPONSE" | grep -o '"serial": "[^"]*"' | cut -d'"' -f4)
fi

if [ -z "$SERIAL" ]; then
    echo "ERROR: Could not parse serial from response: $RESPONSE"
    exit 1
fi
echo "Serial assigned: $SERIAL"

# Build hostname: ov-pcu-YYWW-SEQ
YYWW=$(echo "$SERIAL" | cut -c1-4)
SEQ=$(echo "$SERIAL" | cut -c5-)
HOSTNAME="ov-pcu-${YYWW}-${SEQ}"
echo "Hostname: $HOSTNAME"

# Mount rootfs and write files
mkdir -p /mnt/root
mount -t ext4 $PART2 /mnt/root

echo "$SERIAL" > /mnt/root/etc/device-serial
echo "$HOSTNAME" > /mnt/root/etc/hostname

if grep -q "127\.0\.1\.1" /mnt/root/etc/hosts; then
    sed -i "s/^127\.0\.1\.1.*$/127.0.1.1\t${HOSTNAME}/" /mnt/root/etc/hosts
else
    echo "127.0.1.1	${HOSTNAME}" >> /mnt/root/etc/hosts
fi

umount /mnt/root

echo "Serial $SERIAL written to rootfs"

# Print label (non-fatal — don't fail provisioning if printing fails)
echo "Printing label ..."
if curl --silent --show-error --fail --max-time 30 \
    -X POST \
    "$PROV_API/api/print/$SERIAL"; then
    echo "Label printed"
else
    echo "WARNING: Label printing failed (exit code $?) — continuing anyway"
fi
EOF;

        DB::table('scripts')->insert([
            'name' => 'Assign serial number and print label',
            'script_type' => 'postinstall',
            'priority' => 200,
            'bg' => false,
            'script' => $script
        ]);
    }
}
