#!/bin/bash

iscsiadm --mode=node --portal=$1 --targetname=$2 --login >> $3/meta/logs/shell.log 2>&1
