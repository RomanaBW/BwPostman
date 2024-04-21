#!/usr/bin/env bash
### Script to provide containers and tests with environment variables and run containers
 ## real comments consists of ###, to switch a flag off a single # is used

#DEBUG='--debug'
DEBUG=''

export DEBUG=$DEBUG

### export environments and project
export BW_BASE_DIR=/vhosts/dev/joomla-cms
export BW_TEST_DIR=/vhosts/dev/joomla-cms/tests

export BW_TEST_PHP_VERSION=71_v1
export BW_TEST_JOOMLA_VERSION=365
export BW_TEST_BWPM_VERSION=200
export BW_TEST_VM_VERSION=321
export BW_TEST_BWPM_INSTALL=0


### export environments and project
export PROJECT_BWPM_FUNCTESTS=bwpmtests
export PROJECT_BWPM_VMTESTS=vmtests
export PROJECT_JTESTS=jtests

export TEST_PROJECT=vmtests
#export TEST_PROJECT=vmtests
#export TEST_PROJECT=jtests

export TEST_ENV=shopflex

export BW_TEST_PROJECT=j365vm_flex

### Create project specific test configuration file
\cp ${BW_BASE_DIR}/codeception.tpl.yml ${BW_BASE_DIR}/codeception.yml
sed -i "s/@BW_TEST_PROJECT@/${BW_TEST_PROJECT}/" ${BW_BASE_DIR}/codeception.yml

\cp ${BW_TEST_DIR}/acceptance.suite.j365vm_flex.yml ${BW_TEST_DIR}/acceptance.suite.yml

### check for database rebase
### @ToDo: confirm that all needed cases to rebase are caught
REBASE_DB=false

if [ -f ${BW_TEST_DIR}/_output/${BW_TEST_PROJECT}/failed ]
then
    REBASE_DB=true
fi
#REBASE_DB=true
#REBASE_DB=false

export REBASE_DB=${REBASE_DB}

### set test job(s)
### Make sure, at least one TEST_CAT is set!!

#export TEST_CAT=all

#export TEST_CAT=buyer2subscriber_all
export TEST_CAT=buyer2subscriber_single


### start webserver
docker-compose -f /vms/dockers/containers/infrastructure/run-j-vm-flex.yml -p $TEST_PROJECT up -d

### sync IDE files to container
echo 'Do sync'
/vms/dockers/containers/testers/os-data/sync_to_vm-flex.sh

### start tester
echo 'Start Tester'
docker-compose -f /vms/dockers/containers/testers/bwpm-vm-flex-tester.yml up

### stop tester (service name!)
docker rm tester-vm-flex

### stop webserver
#docker-compose -f /vms/dockers/containers/infrastructure/run-bwpm.yml -p $TEST_PROJECT down
