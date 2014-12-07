#!/bin/bash

PID=$(ps -aux | grep "ewfacquire -C $3 -d sha1 -D $8 of $6 -e $1 $2 -E $5 -l $4/$5/meta/logs/acquisition_error.log -N iSCSI $7:$8 -S 4.5 GiB -t $4/$5/evidence/$8 -u /dev/$9" | head -1 | awk '{print $2}')

kill -INT $PID >> $4/$5/meta/logs/shell.log

echo "Acquire: stopped $7:$8 of $6" >> $4/$5/meta/logs/shell.log
