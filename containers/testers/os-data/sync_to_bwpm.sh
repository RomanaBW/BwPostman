#!/bin/bash

SRC_PATH='/home/romana/PhpstormProjects/BwPostman'
#SRC_PATH='/vhosts/dev/joomla-cms'
TARGET_FILE_PATH=${BW_TEST_WEB_BASE_DIR}/${BW_TEST_PROJECT}/files

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

if [ "${BW_TEST_REBASE_DB}" == true ]
then
    echo 'Rebase database…'

    TARGET_DUMP_PATH=${BW_TEST_WEB_BASE_DIR}/${BW_TEST_PROJECT}/backups
#    TARGET_DUMP_FILE=joomlatest_${BW_TEST_PROJECT}.sql
    BW_TEST_DB_DUMP_FILE='joomlatest_with_bwpm.sql'
    BW_TEST_DB_NAME='joomlatest'
    BW_TEST_DB_HOST='172.18.0.19'
    BW_TEST_DB_USER='tester'
    BW_TEST_DB_PW='barbamama'

    mysql -u ${BW_TEST_DB_USER} -p${BW_TEST_DB_PW} -h ${BW_TEST_DB_HOST} ${BW_TEST_DB_NAME} < ${TARGET_DUMP_PATH}/${BW_TEST_DB_DUMP_FILE}
fi
