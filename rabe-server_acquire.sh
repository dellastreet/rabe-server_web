#!/bin/bash

ewfacquire -C $3 -d sha1 -D "$8 of $6" -e "$1 $2" -E $5 -l $4/$5/meta/logs/acquisition_error.log -N "iSCSI $7:$8" -S "4.5 GiB" -t $4/$5/evidence/$8 -u /dev/$9 > $4/$5/meta/logs/acquisition.log 2>&1 &

echo "Acquire: started $7:$8 of $6" >> $4/$5/meta/logs/shell.log
