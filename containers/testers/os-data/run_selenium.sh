#!/bin/bash

# run geckodriver
java -Xmx256m -jar -Dwebdriver.gecko.driver=$SELENIUM_DIR/geckodriver $SELENIUM_DIR/selenium-server-standalone-$SELENIUMSERVER_VERSION.jar -port 4444 &

# get chromedriver
java -Xmx256m -jar -Dwebdriver.chrome.driver=$SELENIUM_DIR/chromedriver $SELENIUM_DIR/selenium-server-standalone-$SELENIUMSERVER_VERSION.jar -port 4445 &
