#!/bin/bash

TARGET_BASE='/vms/dockers/global_data/test_data'
TARGET_DUMP_PATH=${TARGET_BASE}'/j365_master/backups'
TARGET_DUMP_FILE='joomlatest_master_bwpm.sql'
DUMP_DB='joomlatest'
DUMP_HOST='172.18.0.19'
DUMP_USER='testerbwpm'
DUMP_PW='barbamama'

mysql -u ${DUMP_USER} -p${DUMP_PW} -h ${DUMP_HOST} ${DUMP_DB} < ${TARGET_DUMP_PATH}/${TARGET_DUMP_FILE}
