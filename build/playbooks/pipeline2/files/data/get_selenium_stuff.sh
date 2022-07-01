#!/bin/bash

SELENIUM_FILE=selenium-server-standalone.jar
GECKO_FILE=geckodriver-${GECKODRIVER_VERSION}-linux64.tar.gz
CHROME_FILE=chromedriver_linux64.zip

mkdir -p ${SELENIUM_DIR}
cd ${SELENIUM_DIR}

# get selenium standalone server
wget -O ${SELENIUM_FILE} http://selenium-release.storage.googleapis.com/${SELENIUMSERVER_VERSION%.*}/selenium-server-standalone-${SELENIUMSERVER_VERSION}.jar
chmod a+x ${SELENIUM_FILE}

# get geckodriver
wget -O ${GECKO_FILE} https://github.com/mozilla/geckodriver/releases/download/${GECKODRIVER_VERSION}/${GECKO_FILE}
tar -xzf ${GECKO_FILE}
rm ${GECKO_FILE}
