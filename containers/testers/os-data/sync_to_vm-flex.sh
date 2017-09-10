#!/bin/bash

SRC_PATH='/home/romana/PhpstormProjects/BwPostman'
#SRC_PATH='/vhosts/dev/joomla-cms'
TARGET_BASE='/vms/dockers/global_data/test_data'
#TARGET_FILE_PATH=${TARGET_BASE}'/j365vm_flex/files'
TARGET_FILE_PATH=${TARGET_BASE}'/j372vm322_flex/files'

# sync actual files to test environment
rsync -rlq ${SRC_PATH}/administrator/components/ ${TARGET_FILE_PATH}/administrator/components
rsync -rlq ${SRC_PATH}/administrator/language/ ${TARGET_FILE_PATH}/administrator/language
rsync -rlq ${SRC_PATH}/components/ ${TARGET_FILE_PATH}/components
rsync -rlq ${SRC_PATH}/images/ ${TARGET_FILE_PATH}/images
rsync -rlq ${SRC_PATH}/language/ ${TARGET_FILE_PATH}/language
rsync -rlq ${SRC_PATH}/media/ ${TARGET_FILE_PATH}/media
rsync -rlq ${SRC_PATH}/modules/ ${TARGET_FILE_PATH}/modules
rsync -rlq ${SRC_PATH}/plugins/bwpostman/ ${TARGET_FILE_PATH}/plugins/bwpostman
rsync -rlq ${SRC_PATH}/plugins/quickicon/ ${TARGET_FILE_PATH}/plugins/quickicon
rsync -rlq ${SRC_PATH}/plugins/system/ ${TARGET_FILE_PATH}/plugins/system
rsync -rlq ${SRC_PATH}/plugins/vmuserfield/ ${TARGET_FILE_PATH}/plugins/vmuserfield

if [ ${REBASE_DB} == true ]
then
    echo 'Rebase database…'

#    TARGET_DUMP_PATH=${TARGET_BASE}'/j365vm_flex/backups'
    TARGET_DUMP_PATH=${TARGET_BASE}'/j372vm322_flex/backups'
    TARGET_DUMP_FILE='joomlatest_master_vm_flex.sql'
    DUMP_DB='joomlatest'
    DUMP_HOST='172.18.0.27'
    DUMP_USER='testervmflex'
    DUMP_PW='barbamama'

#    echo "SQL-Befehl: -u ${DUMP_USER} -p${DUMP_PW} -h ${DUMP_HOST} ${DUMP_DB} < ${TARGET_DUMP_PATH}/${TARGET_DUMP_FILE}"
    mysql -u ${DUMP_USER} -p${DUMP_PW} -h ${DUMP_HOST} ${DUMP_DB} < ${TARGET_DUMP_PATH}/${TARGET_DUMP_FILE}
fi
