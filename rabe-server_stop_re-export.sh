#!/bin/bash

sed -i "/rabe-server:$1/d" /etc/iet/ietd.conf
sed -i "/Path=\/dev\/$2/d" /etc/iet/ietd.conf

service iscsitarget restart >> $3/meta/logs/shell.log

echo "Re-export: stopped /dev/$2 as iSCSI target rabe-server:$1" >> $3/meta/logs/shell.log
