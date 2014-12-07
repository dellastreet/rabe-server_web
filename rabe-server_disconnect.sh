#!/bin/bash

iscsiadm --mode=node --portal=$1 --logout all >> $2/meta/logs/shell.log 2>&1
