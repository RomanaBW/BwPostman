#!/bin/bash

# start x-server and webdriver for chromium
Xvfb :45 -ac -screen 0 1920x1080x24 &
export DISPLAY=:45

java -jar -Dwebdriver.chrome.driver=/usr/lib64/chromium/chromedriver /opt/selenium/selenium-server-standalone-3.0.1.jar -port 4445 >/dev/null 2>/dev/null &

## start x-server and webdriver for firefox
## But firefox does not work error free in container, webdriver may be erroneous
#Xvfb :44 -ac -screen 0 1920x1080x24 &
#export DISPLAY=:44

#java -jar -Dwebdriver.gecko.driver=/opt/geckodriver/geckodriver-0.11.1 /opt/selenium/selenium-server-standalone-3.0.1.jar -port 4444 >/dev/null 2>/dev/null &


if [ ${TEST_CAT} == single ]
then
# run specific tests
# Installation
codecept run acceptance Backend/TestInstallationCest --xml report_installation.xml --html report_installation.html
# data restore
codecept run acceptance Backend/TestMaintenanceRestoreCest --xml report_restore.xml --html report_restore.html

# test backend
# test lists
#codecept run acceptance Backend/Lists --xml report_lists.xml --html report_lists.html

codecept run acceptance Backend/Lists/TestCampaignsListsCest  --xml report_campaigns_lists.xml --html report_campaigns_lists.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest  --xml report_mailinglists_lists.xml --html report_mailinglists_lists.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest  --xml report_newsletters_lists.xml --html report_newsletters_lists.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest  --xml report_subscribers_lists.xml --html report_subscribers_lists.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest  --xml report_templates_lists.xml --html report_templates_lists.html

# test maintenance
codecept run acceptance Backend/TestMaintenanceCest --xml report_maintenance.xml --html report_maintenance.html

# test details
#codecept run acceptance Backend/Details --xml report_details.xml --html report_details.html

codecept run acceptance Backend/Details/TestCampaignsDetailsCest --xml report_campaigns_details.xml --html report_campaigns_details.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest --xml report_mailinglists_details.xml --html report_mailinglists_details.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest --xml report_newsletters_details.xml --html report_newsletters_details.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest --xml report_subscribers_details.xml --html report_subscribers_details.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest --xml report_templates_details.xml --html report_templates_details.html

#codecept run acceptance Backend/Details/TestNewslettersDetailsCest --xml report_newsletters_details.xml --html report_newsletters_details.html
#codecept run acceptance Backend/Details/TestNewslettersDetailsCest:SendCopyOfNewsletterToRealRecipients --xml report_newsletters_send_real.xml --html report_newsletters_send_real.html
#codecept run acceptance Backend/Details/TestTemplatesDetailsCest --xml report_templates_details.xml --html report_templates_details.html
#codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateHtmlTemplateTwiceListView --debug --xml report_templates_details_twice_list.xml --html report_templates_details_twice_list.html

# test frontend
codecept run acceptance Frontend --xml report_frontend.xml --html report_frontend.html

codecept run acceptance Backend/TestDeinstallationCest --xml report_deinstallation.xml --html report_deinstallation.html
fi

#/bin/bash

if [ ${TEST_CAT} == all ]
then
# run all tests
codecept run acceptance Backend/TestInstallationCest --xml report_installation.xml --html report_installation.html
codecept run acceptance Backend/TestMaintenanceRestoreCest --xml report_restore.xml --html report_restore.html
codecept run acceptance Backend/Lists --xml report_lists.xml --html report_lists.html
codecept run acceptance Backend/TestMaintenanceCest --xml report_maintenance.xml --html report_maintenance.html
codecept run acceptance Backend/Details --xml report_details.xml --html report_details.html
codecept run acceptance Frontend --xml report_frontend.xml --html report_frontend.html
codecept run acceptance Backend/TestDeinstallationCest --xml report_deinstallation.xml --html report_deinstallation.html
fi

#tmux send-keys -t SelenuimRecording1 q
