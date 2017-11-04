#!/bin/bash

echo 'Test-Cat:' ${BW_TEST_CAT}
echo 'Project: ' ${BW_TEST_PROJECT}

VIDEO_NAME=/tests/tests/_output/${BW_TEST_PROJECT}/videos/${BW_TEST_CAT}.mp4
VIDEO_LOG=/tests/tests/_output/${BW_TEST_PROJECT}/videos/ffmpeg.log

echo 'Video-Name: ' $VIDEO_NAME

SCREEN_SIZE='1440x900'
DISPLAY=':45'

# start x-server and webdriver for chromium
/usr/bin/Xvfb ${DISPLAY} -ac -screen 0 ${SCREEN_SIZE}x16 &
export DISPLAY=${DISPLAY}

#/opt/selenium/chromedriver --url-base=/wd/hub --port=4445  --log-path=/data/logs/chromedriver_log.txt
#/usr/lib64/chromium/chromedriver --url-base=/wd/hub --port=4445  --log-path=/data/logs/chromedriver_log.txt

#java -jar /opt/selenium/selenium-server-standalone-3.5.3.jar -port 4445 >/dev/null 2>/dev/null &

#java -jar -Dwebdriver.chrome.driver=/usr/lib64/chromium/chromedriver /opt/selenium/selenium-server-standalone-3.0.1.jar -port 4445 >/dev/null 2>/dev/null &
java -jar -Dwebdriver.chrome.driver=/opt/selenium/chromedriver -Dwebdriver.chrome.logfile=/data/logs/chromedriver_log.txt -Dwebdriver.chrome.verboseLogging=true /opt/selenium/selenium-server-standalone-3.5.3.jar -port 4445 >/data/logs/selenium_log.txt 2>/data/logs/selenium_log.txt &
#java -jar /opt/selenium/selenium-server-standalone-3.5.3.jar -port 4445 >/data/logs/chromelog.txt 2>/data/logs/chromelog.txt &

#java -jar -Dwebdriver.chrome.driver=/usr/local/bin/chromedriver /opt/selenium/selenium-server-standalone-3.0.1.jar -port 4445 >/dev/null 2>/dev/null &
#java -jar /opt/selenium/selenium-server-standalone-3.5.3.jar -port 4445 >/data/logs/selenium_log.txt 2>/data/logs/selenium_log.txt &

# Loop until selenium server is available
printf 'Waiting Selenium Server to load\n'
sleep 5

#until $(curl --output /dev/null --silent --head --fail http://localhost:4445/wd/hub); do
#    printf '.'
#    sleep 1
#done
#printf '\n'

# start video recording
echo 'start recording'
tmux new-session -d -s BwPostmanRecording1 "ffmpeg -y -f x11grab -draw_mouse 0 -video_size ${SCREEN_SIZE} -i ${DISPLAY}.0 -vcodec libx264 -r 12 ${VIDEO_NAME} 2>${VIDEO_LOG}"

# run tests
/data/${BW_TEST_RUNNER}

# stop video recording
echo 'stop recording'
sleep 1
tmux send-keys -t BwPostmanRecording1 q
sleep 3
XVFB_PID="$(pgrep -f /usr/bin/Xvfb)"
echo "PID: ${XVFB_PID}"
kill "$(pgrep -f /usr/bin/Xvfb)"
