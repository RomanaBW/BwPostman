#!/bin/bash

SELENIUM_FILE=selenium-server-standalone-$SELENIUMSERVER_VERSION.jar
GECKO_FILE=geckodriver-$GECKODRIVER_VERSION-linux64.tar.gz
CHROME_FILE=chromedriver_linux64.zip

mkdir -p $SELENIUM_DIR
cd $SELENIUM_DIR

# get selenium standalone server
wget -O $SELENIUM_FILE https://goo.gl/Lyo36k
chmod a+x $SELENIUM_FILE

# get geckodriver
wget -O $GECKO_FILE https://github.com/mozilla/geckodriver/releases/download/$GECKODRIVER_VERSION/$GECKO_FILE
tar -xzf $GECKO_FILE
rm $GECKO_FILE

# get chromedriver
wget -O $CHROME_FILE http://chromedriver.storage.googleapis.com/$CHROMEDRIVER_VERSION/$CHROME_FILE
unzip -o $CHROME_FILE
rm $CHROME_FILE
