#!/bin/bash
DEBUG='--debug'
#DEBUG=''

echo 'Test-Cat:' $TEST_CAT
echo 'Video-Name: ' /tests/tests/_output/videos/bwpostman_b2s_${TEST_CAT}.mp4

screen_size='1440x900'
display=':45'

# start x-server and webdriver for chromium
/usr/bin/Xvfb ${display} -ac -screen 0 ${screen_size}x16 &
export DISPLAY=${display}

java -jar -Dwebdriver.chrome.driver=/usr/lib64/chromium/chromedriver /opt/selenium/selenium-server-standalone-3.0.1.jar -port 4445 >/dev/null 2>/dev/null &

# Loop until selenium server is available
printf 'Waiting Selenium Server to load\n'
until $(curl --output /dev/null --silent --head --fail http://localhost:4445/wd/hub); do
    printf '.'
    sleep 1
done
printf '\n'

# start video recording
echo 'start recording'
tmux new-session -d -s BwPostmanRecording1 "ffmpeg -y -f x11grab -draw_mouse 0 -video_size ${screen_size} -i ${display}.0 -vcodec libx264 -r 12 /tests/tests/_output/videos/bwpostman_b2s_${TEST_CAT}.mp4 2>/tests/tests/_output/videos/ffmpeg_b2s.log"

# Preparation

# Installation
#codecept run acceptance Backend/TestInstallationCest::installation ${DEBUG} --env shop --xml xmlreports/report_installation_installation.xml --html htmlreports/report_installation_installation.html
#codecept run acceptance Backend/TestOptionsCest::saveDefaults ${DEBUG} --env shop --xml xmlreports/report_option_save_defaults.xml --html htmlreports/report_option_save_defaults.html

# data restore
#codecept run acceptance Backend/TestMaintenanceRestoreCest ${DEBUG} --env shop --xml xmlreports/report_restore.xml --html htmlreports/report_restore.html

# activate plugin user2subscriber
#codecept run acceptance User2Subscriber/User2SubscriberCest::setupUser2Subscriber ${DEBUG} --env shop --xml xmlreports/report_user2Subscriber_activate.xml --html htmlreports/report_user2Subscriber_activate.html


# run specific tests

###############################
# test plugin Buyer2Subscriber #
###############################

if [ ${TEST_CAT} == buyer2subscriber_all ]
then
# all tests for plugin buyer2subscriber
codecept run acceptance Buyer2Subscriber/Buyer2SubscriberCest ${DEBUG} --env shop --xml xmlreports/report_buyer2Subscriber.xml --html htmlreports/report_buyer2Subscriber.html
fi

if [ ${TEST_CAT} == buyer2subscriber_single ]
then
# single tests for plugin buyer2subscriber

#codecept run acceptance Buyer2Subscriber/Buyer2SubscriberCest::installWithoutInstalledComponent ${DEBUG} --env shop --xml xmlreports/b2s_install_no_component.xml --html htmlreports/b2s_install_no_component.html
codecept run acceptance Buyer2Subscriber/Buyer2SubscriberCest::installWithPrerequisites ${DEBUG} --env shop --xml xmlreports/b2s_install_with_prerequisites.xml --html htmlreports/b2s_install_with_prerequisites.html
codecept run acceptance Buyer2Subscriber/Buyer2SubscriberCest::activateBuyer2Subscriber ${DEBUG} --env shop --xml xmlreports/b2s_activate_b2s.xml --html htmlreports/b2s_activate_b2s.html

#codecept run acceptance Buyer2Subscriber/Buyer2SubscriberCest:: ${DEBUG} --env shop --xml xmlreports/b2s_install_no_component.xml --html htmlreports/b2s_install_no_component.html
#codecept run acceptance Buyer2Subscriber/Buyer2SubscriberCest:: ${DEBUG} --env shop --xml xmlreports/b2s_install_no_component.xml --html htmlreports/b2s_install_no_component.html
#codecept run acceptance Buyer2Subscriber/Buyer2SubscriberCest:: ${DEBUG} --env shop --xml xmlreports/b2s_install_no_component.xml --html htmlreports/b2s_install_no_component.html
#codecept run acceptance Buyer2Subscriber/Buyer2SubscriberCest:: ${DEBUG} --env shop --xml xmlreports/b2s_install_no_component.xml --html htmlreports/b2s_install_no_component.html
#codecept run acceptance Buyer2Subscriber/Buyer2SubscriberCest:: ${DEBUG} --env shop --xml xmlreports/b2s_install_no_component.xml --html htmlreports/b2s_install_no_component.html
fi


# Deinstallation
codecept run acceptance Backend/TestDeinstallationCest ${DEBUG} --env shop --xml xmlreports/report_deinstallation.xml --html htmlreports/report_deinstallation.html

# stop video recording
echo 'stop recording'
sleep 1
tmux send-keys -t BwPostmanRecording1 q
sleep 3
XVFB_PID="$(pgrep -f /usr/bin/Xvfb)"
echo "PID: ${XVFB_PID}"
kill "$(pgrep -f /usr/bin/Xvfb)"
#chmod 0777 /tests/tests/_output/videos/bwpostman_com_${TEST_CAT}.mp4
