#!/bin/bash
### Tests with ## at the beginning are both commented out and faulty


echo 'Test-Cat:' $BW_TEST_CAT
echo 'Project: ' $TEST_PROJECT
echo 'Test-Env: ' $TEST_ENV
echo 'Bw_Project:' $BW_TEST_PROJECT

VIDEO_NAME=/tests/tests/_output/${BW_TEST_PROJECT}/videos/${BW_TEST_CAT}.mp4
VIDEO_LOG=/tests/tests/_output/${BW_TEST_PROJECT}/videos/ffmpeg.log

echo 'Video-Name: ' $VIDEO_NAME

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
tmux new-session -d -s BwPostmanRecording1 "ffmpeg -y -f x11grab -draw_mouse 0 -video_size ${screen_size} -i ${display}.0 -vcodec libx264 -r 12 ${VIDEO_NAME} 2>${VIDEO_LOG}"


###############################
# test plugin Buyer2Subscriber #
###############################

if [ "${BW_TEST_CAT}" == buyer2subscriber_all ]
then
# all tests for plugin buyer2subscriber
codecept run acceptance Buyer2Subscriber/Buyer2SubscriberCest ${DEBUG} --env $TEST_ENV --xml xmlreports/buyer2subscriber.xml --html htmlreports/buyer2subscriber.html
fi

if [ "${BW_TEST_CAT}" == buyer2subscriber_single ]
then
# single tests for plugin buyer2subscriber
codecept run acceptance Buyer2Subscriber/Buyer2SubscriberCest::orderWithB2SPluginDeactivated ${DEBUG} --env $TEST_ENV --xml xmlreports/b2s_not_active.xml --html htmlreports/b2s_not_active.html

codecept run acceptance Buyer2Subscriber/Buyer2SubscriberCest::orderWithU2SPluginDeactivated ${DEBUG} --env $TEST_ENV --xml xmlreports/b2s_u2s_not_active.xml --html htmlreports/b2s_u2s_not_active.html

codecept run acceptance Buyer2Subscriber/Buyer2SubscriberCest::orderWithComponentDeactivated ${DEBUG} --env $TEST_ENV --xml xmlreports/b2s_com_not_active.xml --html htmlreports/b2s_com_not_active.html

codecept run acceptance Buyer2Subscriber/Buyer2SubscriberCest::orderWithoutSubscriptionNoExistingSubscription ${DEBUG} --env $TEST_ENV --xml xmlreports/b2s_without_subs_no_existing.xml --html htmlreports/b2s_without_subs_no_existing.html

codecept run acceptance Buyer2Subscriber/Buyer2SubscriberCest::orderWithoutSubscriptionExistingSubscription ${DEBUG} --env $TEST_ENV --xml xmlreports/b2s_without_subs_with_existing.xml --html htmlreports/b2s_without_subs_with_existing.html

codecept run acceptance Buyer2Subscriber/Buyer2SubscriberCest::orderWithSubscriptionWithoutRequiredField ${DEBUG} --env $TEST_ENV --xml xmlreports/b2s_without_required.xml --html htmlreports/b2s_without_required.html

codecept run acceptance Buyer2Subscriber/Buyer2SubscriberCest::orderWithSubscriptionNoExistingSubscription ${DEBUG} --env $TEST_ENV --xml xmlreports/b2s_without_subs.xml --html htmlreports/b2s_without_subs.html

codecept run acceptance Buyer2Subscriber/Buyer2SubscriberCest::orderWithSubscriptionExistingSubscriptionSameML ${DEBUG} --env $TEST_ENV --xml xmlreports/b2s_with_subs.xml --html htmlreports/b2s_with_subs.html

codecept run acceptance Buyer2Subscriber/Buyer2SubscriberCest::orderWithSubscriptionExistingSubscriptionDifferentML ${DEBUG} --env $TEST_ENV --xml xmlreports/b2s_without_subs_diff_ml.xml --html htmlreports/b2s_without_subs_diff_ml.html

codecept run acceptance Buyer2Subscriber/Buyer2SubscriberCest::Buyer2SubscriberOptionsMessage ${DEBUG} --env $TEST_ENV --xml xmlreports/b2s_option_message.xml --html htmlreports/b2s_option_message.html

codecept run acceptance Buyer2Subscriber/Buyer2SubscriberCest::Buyer2SubscriberOptionsMailinglists ${DEBUG} --env $TEST_ENV --xml xmlreports/b2s_option_ml.xml --html htmlreports/b2s_option_ml.html
fi


# stop video recording
echo 'stop recording'
sleep 1
tmux send-keys -t BwPostmanRecording1 q
sleep 3
XVFB_PID="$(pgrep -f /usr/bin/Xvfb)"
echo "PID: ${XVFB_PID}"
kill "$(pgrep -f /usr/bin/Xvfb)"
