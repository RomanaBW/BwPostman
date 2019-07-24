#!/usr/bin/env bash
### Script to provide containers and tests with environment variables and run containers
 ## real comments consists of ###, to switch a flag off a single # is used

#DEBUG='--debug'
DEBUG=''

export DEBUG=$DEBUG

### export environments and project
export PROJECT_BWPM_FUNCTESTS=bwpmtests
export PROJECT_BWPM_VMTESTS=vmtests
export PROJECT_JTESTS=jtests

export TEST_PROJECT=$PROJECT_BWPM_FUNCTESTS
#export TEST_PROJECT=vmtests
#export TEST_PROJECT=jtests

export TEST_ENV=bwpm

### check for database rebase
### @ToDo: confirm that all needed cases to rebase are caught
REBASE_DB=false

if [ -f /vhosts/dev/joomla-cms/tests/_output/failed ]
then
    REBASE_DB=true
fi
#REBASE_DB=true
#REBASE_DB=false

export REBASE_DB=${REBASE_DB}

### set test job(s)
### Make sure, at least one TEST_CAT is set!!

#export TEST_CAT=all

#export TEST_CAT=lists_all
#export TEST_CAT=lists_cam
#export TEST_CAT=lists_ml
#export TEST_CAT=lists_nl
#export TEST_CAT=lists_subs
#export TEST_CAT=lists_tpl

#export TEST_CAT=details_all

#export TEST_CAT=details_cam
#export TEST_CAT=details_ml
#export TEST_CAT=details_nl
#export TEST_CAT=details_subs
#export TEST_CAT=details_tpl

#export TEST_CAT=frontend_all
#export TEST_CAT=frontend_single

#export TEST_CAT=maintenance
#export TEST_CAT=maintenance_single

#export TEST_CAT=user2subscriber_all
export TEST_CAT=user2subscriber_single


### start webserver
docker-compose -f /vms/dockers/containers/infrastructure/run-bwpm.yml -p $TEST_PROJECT up -d

### sync IDE files to container
/vms/dockers/containers/testers/os-data/sync_to_bwpm.sh

### start tester
docker-compose -f /vms/dockers/containers/testers/bwpm-tester.yml up

### stop tester
docker rm bwpm-tester

### stop webserver
#docker-compose -f /vms/dockers/containers/infrastructure/run-bwpm.yml -p $TEST_PROJECT down
