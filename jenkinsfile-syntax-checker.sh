#!/usr/bin/env bash

JENKINS_URL="https://arbeitspferd.bet2.nil:8443"
JENKINS_API_TOKEN=116f2631c505ce191be283a5f13d39727d
JENKINS_COMMAND="pipeline-model-converter/validate"
JENKINS_FILE="./JenkinsfileContainer"

curl -X POST -F "jenkinsfile=<${JENKINS_FILE}" --user romana:${JENKINS_API_TOKEN} "${JENKINS_URL}/${JENKINS_COMMAND}"
