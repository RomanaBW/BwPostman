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

chromedriver --url-base=/wd/hub --port=4445

java -jar /opt/selenium/selenium-server-standalone-3.5.3.jar -port 4445 >/dev/null 2>/dev/null &

# Loop until selenium server is available
printf 'Waiting Selenium Server to load\n'
until $(curl --output /dev/null --silent --head --fail http://localhost:4445/wd/hub); do
    printf '.'
    sleep 1
done
printf '\n'

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
