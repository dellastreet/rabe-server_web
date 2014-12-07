#!/bin/bash

mkdir -p $1/$2/evidence
mkdir -p $1/$2/meta/logs
touch $1/$2/meta/logs/shell.log
chown www-data $1/$2/meta/logs/shell.log
