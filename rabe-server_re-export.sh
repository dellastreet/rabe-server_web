#!/bin/bash

TARGET=$(cat /etc/iet/ietd.conf | grep '^Target')

if [[ -z $TARGET ]]; then
	COUNTER=0
else
	LUN=$(tail -1 /etc/iet/ietd.conf | awk '{print $2}')
	COUNTER=$[ $LUN + 1 ]
fi

echo "Target rabe-server:$1" >> /etc/iet/ietd.conf
echo "Lun $COUNTER Path=/dev/$2,Type=fileio,IOMode=ro" >> /etc/iet/ietd.conf

service iscsitarget restart >> $3/meta/logs/shell.log

echo "Re-export: started /dev/$2 as iSCSI target rabe-server:$1 (read-only)" >> $3/meta/logs/shell.log
