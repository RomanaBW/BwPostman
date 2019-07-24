#!/usr/bin/env bash
### Script to provide containers and tests with environment variables and run containers
 ## real comments consists of ###, to switch a flag off a single # is used

### set internal variables

### export environments and project
export BW_BASE_DIR=/vhosts/dev/joomla-cms
export BW_TEST_DIR=/vhosts/dev/joomla-cms/tests
export BW_TEST_CONTAINER_DIR=/vms/dockers/containers
export BW_TEST_WEB_BASE_DIR=/vms/dockers/global_data/test_data

export BW_TEST_PHP_VERSION=71_v1
export BW_TEST_JOOMLA_VERSION=370
export BW_TEST_BWPM_VERSION=200
export BW_TEST_VM_VERSION=''
export BW_TEST_BWPM_INSTALL=false

export BW_TEST_LOG_PATH=tests/_output/j370_bwpm200
export BW_TEST_URL=172.18.0.20
export BW_TEST_DB_HOST=172.18.0.19
export BW_TEST_DB_NAME=joomlatest
export BW_TEST_DB_USER=tester
export BW_TEST_DB_PW=barbamama

BW_TEST_DEBUG='--debug'
#BW_TEST_DEBUG=''
export BW_TEST_DEBUG

BW_TEST_PROJECT=j${BW_TEST_JOOMLA_VERSION}

if [ "${BW_TEST_BWPM_VERSION}" != '' ]; then
    BW_TEST_PROJECT=${BW_TEST_PROJECT}_bwpm${BW_TEST_BWPM_VERSION}
fi

if [ "${BW_TEST_VM_VERSION}" != '' ]; then
    BW_TEST_PROJECT=${BW_TEST_PROJECT}_vm${BW_TEST_VM_VERSION}
fi

NET_NAME=$(echo ${BW_TEST_PROJECT} | sed -e 's/_//g')

export BW_TEST_PROJECT=${BW_TEST_PROJECT}
export BW_TEST_NET_NAME=${NET_NAME}


### Create project specific output dir if not exists
if [ ! -d ${BW_TEST_DIR}/_output/${BW_TEST_PROJECT} ]
then
    sudo mkdir ${BW_TEST_DIR}/_output/${BW_TEST_PROJECT}
    sudo mkdir ${BW_TEST_DIR}/_output/${BW_TEST_PROJECT}/videos
    sudo mkdir ${BW_TEST_DIR}/_output/${BW_TEST_PROJECT}/htmlreports
    sudo mkdir ${BW_TEST_DIR}/_output/${BW_TEST_PROJECT}/xmlreports
    sudo chown -R romana ${BW_TEST_DIR}/_output/${BW_TEST_PROJECT}
    sudo chgrp -R users ${BW_TEST_DIR}/_output/${BW_TEST_PROJECT}
    sudo chmod -R 0777 ${BW_TEST_DIR}/_output/${BW_TEST_PROJECT}
fi
### Create project specific test configuration file
\cp ${BW_BASE_DIR}/codeception.tpl.yml ${BW_BASE_DIR}/codeception.yml
sed -i "s/@BW_TEST_PROJECT@/${BW_TEST_PROJECT}/" ${BW_BASE_DIR}/codeception.yml

\cp ${BW_TEST_DIR}/acceptance.suite.tpl.yml ${BW_TEST_DIR}/acceptance.suite.yml
sed -i "s/@BW_TEST_PROJECT@/${BW_TEST_PROJECT}/" ${BW_TEST_DIR}/acceptance.suite.yml
echo 'Prepare acceptance.suite.yml'
sed -i "s/@BW_TEST_URL@/${BW_TEST_URL}/" ${BW_TEST_DIR}/acceptance.suite.yml
sed -i "s/@BW_TEST_DB_HOST@/${BW_TEST_DB_HOST}/" ${BW_TEST_DIR}/acceptance.suite.yml
sed -i "s/@BW_TEST_DB_NAME@/${BW_TEST_DB_NAME}/" ${BW_TEST_DIR}/acceptance.suite.yml
sed -i "s/@BW_TEST_DB_USER@/${BW_TEST_DB_USER}/" ${BW_TEST_DIR}/acceptance.suite.yml
sed -i "s/@BW_TEST_DB_PW@/${BW_TEST_DB_PW}/" ${BW_TEST_DIR}/acceptance.suite.yml

### check for database rebase
### @ToDo: confirm that all needed cases to rebase are caught
BW_TEST_REBASE_DB=false

if [ -f ${BW_TEST_DIR}/_output/${BW_TEST_PROJECT}/failed ]
then
    BW_TEST_REBASE_DB=true
fi

if [ "${BW_TEST_BWPM_INSTALL}" == true ]
then
    BW_TEST_REBASE_DB=true
fi
#BW_TEST_REBASE_DB=true
#BW_TEST_REBASE_DB=false

export BW_TEST_REBASE_DB=${BW_TEST_REBASE_DB}

### start webserver
docker-compose -f ${BW_TEST_CONTAINER_DIR}/infrastructure/run-bwpm.yml -p ${BW_TEST_PROJECT} up -d

### sync IDE files to container
${BW_TEST_CONTAINER_DIR}/testers/os-data/sync_to_bwpm.sh

### start tester
docker-compose -f ${BW_TEST_CONTAINER_DIR}/testers/bwpm-tester.yml up

### stop tester
docker rm bwpm-tester

### stop webserver
#docker-compose -f ${BW_TEST_CONTAINER_DIR}/infrastructure/run-bwpm.yml -p ${BW_TEST_PROJECT} down
