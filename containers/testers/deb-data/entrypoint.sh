#!/bin/bash

## install selenium and webdriver
#zypper --non-interactive up #> /data/logs/update_log.txt #> /dev/null

# start x-server, vnc-server and webdriver
#xvnc -ac -port 5900 & # alte Installation
#Xvfb &
java -Xmx256m -jar -Dwebdriver.chrome.driver=/opt/selenium/chromedriver /opt/selenium/selenium-server-standalone-3.0.1.jar -port 4445 &> /dev/null & #&> /data/logs/driver_log.txt

#xvfb-run --listen-tcp --server-num 44 -f /tmp/xvfb.auth -s "-ac -screen 99 1920x1080x24" java -Xmx256m -jar -Dwebdriver.chrome.driver=/usr/lib64/chromium/chromedriver /opt/selenium/selenium-server-standalone-3.0.1.jar -port 4445 & #> /dev/null &
#x11vnc -storepasswd @Miriam01# /tmp/vncpass
#x11vnc -rfbport 4544 -rfbauth /tmp/vncpass -display :99 -forever -auth /tmp/xvfb.auth
#tmux new-session -d -s SeleniumRecording1 'ffmpeg -f x11grab -video_size 1920x1080 -i 172.18.0.10:44 -codec:v libx264 -r 12 /tests/BwPostman/_output/selenium_1.mp4'


# change to test directory
#cd /tests/BwPostman/

if [ ${TEST_CAT} == single ]
then
# run specific tests
codecept run acceptance 001_Backend/001_TestInstallationCest --debug --xml report_installation.xml --html report_installation.html
codecept run acceptance 001_Backend/002_TestMaintenanceRestoreCest --xml report_restore.xml --html report_restore.html
#codecept run acceptance Backend/Details/TestNewslettersDetailsCest:SendNewsletterToTestrecipients --xml report_single.xml --html report_single.html
#codecept run acceptance Backend/Details/TestNewslettersDetailsCest:SendCopyOfNewsletterToRealRecipients --xml report_single.xml --html report_single.html
#codecept run acceptance Backend/003_Lists --xml report_single.xml --html report_single.html
#codecept run acceptance Backend/004_TestMaintenanceCest --xml report_single.xml --html report_single.html
#codecept run acceptance Backend/Details --xml report_single.xml --html report_single.html
#codecept run acceptance Frontend --xml report_single.xml --html report_single.html
codecept run acceptance 007_TestDeinstallationCest --xml report_deinstallation.xml --html report_deinstallation.html
fi

#/bin/bash

if [ ${TEST_CAT} == all ]
then
# run all tests
codecept run acceptance -g 001_installation -g 002_restore -g 003_be_lists -g 004_maintenance -g 005_be_details -g 006_fe_subscription -g 007_deinstallation --xml report_all.xml --html report_all.html
fi

#tmux send-keys -t SelenuimRecording1 q
