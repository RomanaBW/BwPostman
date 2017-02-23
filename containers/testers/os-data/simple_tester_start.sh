#!/bin/bash
#DEBUG='--debug'
DEBUG=''

echo 'Test-Cat:' $TEST_CAT
echo 'Video-Name: ' /tests/tests/_output/videos/bwpostman_com_${TEST_CAT}.mp4

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
tmux new-session -d -s BwPostmanRecording1 "ffmpeg -y -f x11grab -draw_mouse 0 -video_size ${screen_size} -i ${display}.0 -vcodec libx264 -r 12 /tests/tests/_output/videos/bwpostman_com_${TEST_CAT}.mp4 2>/tests/tests/_output/videos/ffmpeg.log"


## start x-server and webdriver for firefox
## But firefox does not work error free in container, webdriver may be erroneous

#java -jar -Dwebdriver.gecko.driver=/opt/geckodriver/geckodriver-0.11.1 /opt/selenium/selenium-server-standalone-3.0.1.jar -port 4444 >/dev/null 2>/dev/null &


# Installation
codecept run acceptance Backend/TestInstallationCest::installation ${DEBUG} --xml xmlreports/report_installation_installation.xml --html htmlreports/report_installation_installation.html
codecept run acceptance Backend/TestOptionsCest::saveDefaults ${DEBUG} --xml xmlreports/report_option_save_defaults.xml --html htmlreports/report_option_save_defaults.html

# data restore
codecept run acceptance Backend/TestMaintenanceRestoreCest ${DEBUG} --xml xmlreports/report_restore.xml --html htmlreports/report_restore.html

# set permissions
codecept run acceptance Backend/TestOptionsCest::setPermissions ${DEBUG} --xml xmlreports/report_option_set_permissions.xml --html htmlreports/report_option_set_permissions.html


# run specific tests
################
# test backend #
################

######
# test lists
######

###
# test campaigns lists
###

if [ ${TEST_CAT} == lists_all ]
then
# all tests for campaigns
codecept run acceptance Backend/Lists/TestCampaignsListsCest  ${DEBUG} --xml xmlreports/report_campaigns_lists.xml --html htmlreports/report_campaigns_lists.html
fi

if [ ${TEST_CAT} == lists_cam ]
then
# single tests for campaigns
codecept run acceptance Backend/Lists/TestCampaignsListsCest::SortCampaignsByTableHeader ${DEBUG} --xml xmlreports/report_campaigns_sort_by_tableheader.xml --html htmlreports/report_campaigns_report_campaigns_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestCampaignsListsCest::SortCampaignsBySelectList ${DEBUG} --xml xmlreports/report_campaigns_report_campaigns_sort_by_select.xml --html htmlreports/report_campaigns_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestCampaignsListsCest::SearchCampaigns ${DEBUG} --xml xmlreports/report_campaigns_search.xml --html htmlreports/report_campaigns_search.html
codecept run acceptance Backend/Lists/TestCampaignsListsCest::ListlimitCampaigns ${DEBUG} --xml xmlreports/report_campaigns_listlimit.xml --html htmlreports/report_campaigns_listlimit.html
codecept run acceptance Backend/Lists/TestCampaignsListsCest::PaginationCampaigns ${DEBUG} --xml xmlreports/report_campaigns_pagination.xml --html htmlreports/report_campaigns_pagination.html
fi

###
# test mailinglist lists
###

if [ ${TEST_CAT} == lists_all ]
then
# all tests for mailinglists
codecept run acceptance Backend/Lists/TestMailinglistsListsCest  ${DEBUG} --xml xmlreports/report_mailinglists_lists.xml --html htmlreports/report_mailinglists_lists.html
fi

if [ ${TEST_CAT} == lists_ml ]
then
# single tests for mailinglists
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::PublishMailinglistsByIcon ${DEBUG} --xml xmlreports/report_mailinglists_publish_by_icon.xml --html htmlreports/report_mailinglists_publish_by_icon.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::PublishMailinglistsByToolbar ${DEBUG} --xml xmlreports/report_mailinglists_publish_by_toolbar.xml --html htmlreports/report_mailinglists_publish_by_toolbar.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::SortMailinglistsByTableHeader ${DEBUG} --xml xmlreports/report_mailinglists_sort_by_tableheader.xml --html htmlreports/report_mailinglists_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::SortMailinglistsBySelectList ${DEBUG} --xml xmlreports/report_mailinglists_sort_by_selectlist.xml --html htmlreports/report_mailinglists_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::FilterMailinglistsByStatus ${DEBUG} --xml xmlreports/report_mailinglists_filter_by_status.xml --html htmlreports/report_mailinglists_filter_by_status.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::FilterMailinglistsByAccess ${DEBUG} --xml xmlreports/report_mailinglists_filter_by_access.xml --html htmlreports/report_mailinglists_filter_by_access.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::SearchMailinglists ${DEBUG} --xml xmlreports/report_mailinglists_search.xml --html htmlreports/report_mailinglists_search.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::ListlimitMailinglists ${DEBUG} --xml xmlreports/report_mailinglists_listlimit.xml --html htmlreports/report_mailinglists_listlimit.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::PaginationMailinglists ${DEBUG} --xml xmlreports/report_mailinglists_pagination.xml --html htmlreports/report_mailinglists_pagination.html
fi

###
# test newsletter lists
###

if [ ${TEST_CAT} == lists_all ]
then
# all tests for newsletters
codecept run acceptance Backend/Lists/TestNewslettersListsCest  ${DEBUG} --xml xmlreports/report_newsletters_lists.xml --html htmlreports/report_newsletters_lists.html
fi

if [ ${TEST_CAT} == lists_nl ]
then
# single tests for newsletters
codecept run acceptance Backend/Lists/TestNewslettersListsCest::SortNewslettersByTableHeader  ${DEBUG} --xml xmlreports/report_newsletters_sort_by_tableheader.xml --html htmlreports/report_newsletters_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::SortNewslettersBySelectList  ${DEBUG} --xml xmlreports/report_newsletters_report_newsletters_sort_by_selectlist.xml --html htmlreports/report_newsletters_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::FilterNewslettersByAuthor  ${DEBUG} --xml xmlreports/report_newsletters_filter_by_author.xml --html htmlreports/report_newsletters_filter_by_author.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::FilterNewslettersByCampaign  ${DEBUG} --xml xmlreports/report_newsletters_filter_by_campaign.xml --html htmlreports/report_newsletters_filter_by_campaign.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::SearchNewsletters  ${DEBUG} --xml xmlreports/report_newsletters_seearch.xml --html htmlreports/report_newsletters_seearch.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::ListlimitNewsletters  ${DEBUG} --xml xmlreports/report_newsletters_listlimit.xml --html htmlreports/report_newsletters_listlimit.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::PaginationNewsletters  ${DEBUG} --xml xmlreports/report_newsletters_pagination.xml --html htmlreports/report_newsletters_pagination.html
fi

###
# test subscriber lists
###

if [ ${TEST_CAT} == lists_all ]
then
# all tests for subscribers
codecept run acceptance Backend/Lists/TestSubscribersListsCest  ${DEBUG} --xml xmlreports/report_subscribers_lists.xml --html htmlreports/report_subscribers_lists.html
fi

if [ ${TEST_CAT} == lists_subs ]
then
# single tests for subscribers
codecept run acceptance Backend/Lists/TestSubscribersListsCest::SortSubscribersByTableHeader  ${DEBUG} --xml xmlreports/report_subscribers_sort_by_tableheader.xml --html htmlreports/report_subscribers_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::SortSubscribersBySelectList  ${DEBUG} --xml xmlreports/report_subscribers_sort_by_selectlist.xml --html htmlreports/report_subscribers_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::FilterSubscribersByMailformat  ${DEBUG} --xml xmlreports/report_subscribers_filter_by_mailformat.xml --html htmlreports/report_subscribers_filter_by_mailformat.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::FilterSubscribersByMailinglist  ${DEBUG} --xml xmlreports/report_subscribers_filter_by_mailinglist.xml --html htmlreports/report_subscribers_filter_by_mailinglist.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::SearchSubscribers  ${DEBUG} --xml xmlreports/report_subscribers_search.xml --html htmlreports/report_subscribers_search.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::ListlimitSubscribers  ${DEBUG} --xml xmlreports/report_subscribers_listlimit.xml --html htmlreports/report_subscribers_listlimit.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::PaginationSubscribers  ${DEBUG} --xml xmlreports/report_subscribers_pagination.xml --html htmlreports/report_subscribers_pagination.html
fi

###
# test template lists
###

if [ ${TEST_CAT} == lists_all ]
then
# all tests for templates
codecept run acceptance Backend/Lists/TestTemplatesListsCest  ${DEBUG} --xml xmlreports/report_templates_lists.xml --html htmlreports/report_templates_lists.html
fi

if [ ${TEST_CAT} == lists_tpl ]
then
# single tests for templates
codecept run acceptance Backend/Lists/TestTemplatesListsCest::PublishTemplatesByIcon ${DEBUG} --xml xmlreports/report_templates_publish_by_icon.xml --html htmlreports/report_templates_publish_by_icon.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::PublishTemplatesByToolbar ${DEBUG} --xml xmlreports/report_templates_publish_by_toolbar.xml --html htmlreports/report_templates_publish_by_toolbar.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::SortTemplatesByTableHeader ${DEBUG} --xml xmlreports/report_templates_sort_by_tableheader.xml --html htmlreports/report_templates_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::SortTemplatesBySelectList ${DEBUG} --xml xmlreports/report_templates_sort_by_selectlist.xml --html htmlreports/report_templates_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::FilterTemplatesByStatus ${DEBUG} --xml xmlreports/report_templates_filter_by_status.xml --html htmlreports/report_templates_filter_by_status.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::FilterTemplatesByMailformat ${DEBUG} --xml xmlreports/report_templates_filter_by_mailformat.xml --html htmlreports/report_templates_filter_by_mailformat.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::SearchTemplates ${DEBUG} --xml xmlreports/report_templates_search.xml --html htmlreports/report_templates_search.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::ListlimitTemplates ${DEBUG} --xml xmlreports/report_templates_listlimit.xml --html htmlreports/report_templates_listlimit.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::PaginationTemplates ${DEBUG} --xml xmlreports/report_templates_pagination.xml --html htmlreports/report_templates_pagination.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::SetDefaultTemplates ${DEBUG} --xml xmlreports/report_templates_set_default.xml --html htmlreports/report_templates_set_default.html
fi

######
# test details
######

###
# test campaign details
###

if [ ${TEST_CAT} == details_all ]
then
# all tests for campaigns
codecept run acceptance Backend/Details/TestCampaignsDetailsCest ${DEBUG} --xml xmlreports/report_campaigns_details.xml --html htmlreports/report_campaigns_details.html
fi

if [ ${TEST_CAT} == details_cam ]
then
# single tests for campaigns
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignCancelMainView ${DEBUG} --xml xmlreports/report_campaigns_cancel_main.xml --html htmlreports/report_campaigns_cancel_main.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignCompleteMainView ${DEBUG} --xml xmlreports/report_campaigns_complete_main.xml --html htmlreports/report_campaigns_complete_main.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignCancelListView ${DEBUG} --xml xmlreports/report_campaigns_cancel_list.xml --html htmlreports/report_campaigns_cancel_list.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignListView ${DEBUG} --xml xmlreports/report_campaigns_complete_list.xml --html htmlreports/report_campaigns_complete_list.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateCampaignTwiceListView  ${DEBUG} --xml xmlreports/report_campaigns_twice_list.xml --html htmlreports/report_campaigns_twice_list.html
fi

###
# test mailinglist details
###

if [ ${TEST_CAT} == details_all ]
then
# all tests for mailinglists
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest ${DEBUG} --xml xmlreports/report_mailinglists_details.xml --html htmlreports/report_mailinglists_details.html
fi

if [ ${TEST_CAT} == details_ml ]
then
# single tests for mailinglists
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistCancelMainView ${DEBUG} --xml xmlreports/report_mailinglists_cancel_main.xml --html htmlreports/report_mailinglists_cancel_main.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistCompleteMainView ${DEBUG} --xml xmlreports/report_mailinglists_complete_main.xml --html htmlreports/report_mailinglists_complete_main.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistCancelListView ${DEBUG} --xml xmlreports/report_mailinglists_cancel_list.xml --html htmlreports/report_mailinglists_cancel_list.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistListView ${DEBUG} --xml xmlreports/report_mailinglists_complete_list.xml --html htmlreports/report_mailinglists_complete_list.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateMailinglistTwiceListView ${DEBUG} --xml xmlreports/report_mailinglists_twice_list.xml --html htmlreports/report_mailinglists_twice_list.html
fi

###
# test newsletter details
###

if [ ${TEST_CAT} == details_all ]
then
# all tests for newsletters
codecept run acceptance Backend/Details/TestNewslettersDetailsCest ${DEBUG} --xml xmlreports/report_newsletters_details.xml --html htmlreports/report_newsletters_details.html
fi

if [ ${TEST_CAT} == details_nl ]
then
# single tests for newsletters
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterCancelMainView ${DEBUG} --xml xmlreports/report_newsletters_cancel_main.xml --html htmlreports/report_newsletters_cancel_main.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterCompleteMainView ${DEBUG} --xml xmlreports/report_newsletters_complete_main.xml --html htmlreports/report_newsletters_complete_main.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterCancelListView ${DEBUG} --xml xmlreports/report_newsletters_cancel_list.xml --html htmlreports/report_newsletters_cancel_list.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterListView ${DEBUG} --xml xmlreports/report_newsletters_complete_list.xml --html htmlreports/report_newsletters_complete_list.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateNewsletterTwiceListView ${DEBUG} --xml xmlreports/report_newsletters_twice_list.xml --html htmlreports/report_newsletters_twice_list.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CopyNewsletter ${DEBUG} --xml xmlreports/report_newsletters_copy.xml --html htmlreports/report_newsletters_copy.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::SendNewsletterToTestrecipients ${DEBUG} --xml xmlreports/report_newsletters_send_test.xml --html htmlreports/report_newsletters_send_test.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::SendNewsletterToRealRecipients ${DEBUG} --xml xmlreports/report_newsletters_send_real.xml --html htmlreports/report_newsletters_send_real.html
fi

###
# test subscriber details
###

if [ ${TEST_CAT} == details_all ]
then
# all tests for subscribers
codecept run acceptance Backend/Details/TestSubscribersDetailsCest ${DEBUG} --xml xmlreports/report_subscribers_details.xml --html htmlreports/report_subscribers_details.html
fi

if [ ${TEST_CAT} == details_subs ]
then
# single tests for subscribers
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberCancelMainView ${DEBUG} --xml xmlreports/report_subscribers_cancel_main.xml --html htmlreports/report_subscribers_cancel_main.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberCompleteMainView ${DEBUG} --xml xmlreports/report_subscribers_complete_main.xml --html htmlreports/report_subscribers_complete_main.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberCancelListView ${DEBUG} --xml xmlreports/report_subscribers_cancel_list.xml --html htmlreports/report_subscribers_cancel_list.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberListView ${DEBUG} --xml xmlreports/report_subscribers_complete_list.xml --html htmlreports/report_subscribers_complete_list.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateSubscriberTwiceListView ${DEBUG} --xml xmlreports/report_subscribers_twice_list.xml --html htmlreports/report_subscribers_twice_list.html
fi

###
# test template details
###

if [ ${TEST_CAT} == details_all ]
then
# all tests for templates
codecept run acceptance Backend/Details/TestTemplatesDetailsCest ${DEBUG} --xml xmlreports/report_templates_details.xml --html htmlreports/report_templates_details.html
fi

if [ ${TEST_CAT} == details_tpl ]
then
# single tests for templates
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneHtmlTemplateCancelMainView ${DEBUG} --xml xmlreports/report_templates_html_cancel_main.xml --html htmlreports/report_templates_html_cancel_main.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneHtmlTemplateCompleteMainView ${DEBUG} --xml xmlreports/report_templates_html_complete_main.xml --html htmlreports/report_templates_html_complete_main.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneHtmlTemplateCancelListView ${DEBUG} --xml xmlreports/report_templates_html_cancel_list.xml --html htmlreports/report_templates_html_cancel_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneHtmlTemplateListView ${DEBUG} --xml xmlreports/report_templates_html_complete_list.xml --html htmlreports/report_templates_html_complete_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateHtmlTemplateTwiceListView ${DEBUG} --xml xmlreports/report_templates_html_twice_list.xml --html htmlreports/report_templates_html_twice_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateCancelMainView ${DEBUG} --xml xmlreports/report_templates_text_cancel_main.xml --html htmlreports/report_templates_text_cancel_main.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateCompleteMainView ${DEBUG} --xml xmlreports/report_templates_text_complete_main.xml --html htmlreports/report_templates_text_complete_main.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateCancelListView ${DEBUG} --xml xmlreports/report_templates_text_cancel_list.xml --html htmlreports/report_templates_text_cancel_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateListView ${DEBUG} --xml xmlreports/report_templates_text_complete_list.xml --html htmlreports/report_templates_text_complete_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateTextTemplateTwiceListView ${DEBUG} --xml xmlreports/report_templates_text_twice_list.xml --html htmlreports/report_templates_text_twice_list.html
fi

#################
# test frontend #
#################

if [ ${TEST_CAT} == frontend_all ]
then
# all tests for frontend
codecept run acceptance Frontend ${DEBUG} --xml xmlreports/report_frontend.xml --html htmlreports/report_frontend.html
fi

if [ ${TEST_CAT} == frontend_single ]
then
# single tests for frontend
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeSimpleActivateAndUnsubscribe ${DEBUG} --xml xmlreports/report_frontend_activate_and_unsubscribe.xml --html htmlreports/report_frontend_activate_and_unsubscribe.html
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeTwiceActivateAndUnsubscribe ${DEBUG} --xml xmlreports/report_frontend_activate_twice_and_unscubscribe.xml --html htmlreports/report_frontend_activate_twice_and_unscubscribe.html
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeTwiceActivateGetActivationAndUnsubscribe ${DEBUG} --xml xmlreports/report_frontend_get_code_and_unsubscribe.xml --html htmlreports/report_frontend_get_code_and_unsubscribe.html
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeActivateSubscribeGetEditlinkAndUnsubscribe ${DEBUG} --xml xmlreports/report_frontend_get_editlink_and_unsubscribe.xml --html htmlreports/report_frontend_get_editlink_and_unsubscribe.html
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeMissingValuesComponent ${DEBUG} --xml xmlreports/report_frontend_missing_values.xml --html htmlreports/report_frontend_missing_values.html
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeSimpleActivateChangeAndUnsubscribe ${DEBUG} --xml xmlreports/report_frontend_activate_change_and_unsubscribe.xml --html htmlreports/report_frontend_activate_change_and_unsubscribe.html
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeActivateUnsubscribeAndActivate ${DEBUG} --xml xmlreports/report_frontend_activate_unsubscribe_activate.xml --html htmlreports/report_frontend_activate_unsubscribe_activate.html
codecept run acceptance Frontend/SubscribeComponentCest::GetEditlinkWrongAddress ${DEBUG} --xml xmlreports/report_frontend_get_editlink_wrong_address.xml --html htmlreports/report_frontend_get_editlink_wrong_address.html
codecept run acceptance Frontend/SubscribeComponentCest::WrongUnsubscribeLinks ${DEBUG} --xml xmlreports/report_frontend_wrong_unsubscribe_link.xml --html htmlreports/report_frontend_wrong_unsubscribe_link.html
fi

####################
# test maintenance #
####################

if [ ${TEST_CAT} == maintenance ]
then
# all tests for maintenance
codecept run acceptance Backend/TestMaintenanceCest ${DEBUG} --xml xmlreports/report_maintenance.xml --html htmlreports/report_maintenance.html
fi

if [ ${TEST_CAT} == maintenance_single ]
then
# single tests for maintenance
codecept run acceptance Backend/TestMaintenanceCest::saveTables ${DEBUG} --xml xmlreports/report_maintenance_save_tables.xml --html htmlreports/report_maintenance_save_tables.html
codecept run acceptance Backend/TestMaintenanceCest::checkTables ${DEBUG} --xml xmlreports/report_maintenancecheck_tables.xml --html htmlreports/report_maintenance_check_tables.html
codecept run acceptance Backend/TestMaintenanceCest::restoreTables ${DEBUG} --xml xmlreports/report_maintenance_restore_tables.xml --html htmlreports/report_maintenance_restore_tables.html
codecept run acceptance Backend/TestMaintenanceCest::testBasicSettings ${DEBUG} --xml xmlreports/report_maintenance_basic_settings.xml --html htmlreports/report_maintenance_basic_settings.html
codecept run acceptance Backend/TestMaintenanceCest::testForumLink ${DEBUG} --xml xmlreports/report_maintenance_forum_link.xml --html htmlreports/report_maintenance_forum_link.html
fi

if [ ${TEST_CAT} == all ]
then
# run all tests
codecept run acceptance Backend/Lists ${DEBUG} --xml xmlreports/report_lists.xml --html htmlreports/report_lists.html
codecept run acceptance Backend/Details ${DEBUG} --xml xmlreports/report_details.xml --html htmlreports/report_details.html
codecept run acceptance Frontend ${DEBUG} --xml xmlreports/report_frontend.xml --html htmlreports/report_frontend.html
codecept run acceptance Backend/TestMaintenanceCest ${DEBUG} --xml xmlreports/report_maintenance.xml --html htmlreports/report_maintenance.html
fi

# Deinstallation
codecept run acceptance Backend/TestDeinstallationCest ${DEBUG} --xml xmlreports/report_deinstallation.xml --html htmlreports/report_deinstallation.html

# stop video recording
echo 'stop recording'
sleep 1
tmux send-keys -t BwPostmanRecording1 q
sleep 3
XVFB_PID="$(pgrep -f /usr/bin/Xvfb)"
echo "PID: ${XVFB_PID}"
kill "$(pgrep -f /usr/bin/Xvfb)"
#chmod 0777 /tests/tests/_output/videos/bwpostman_com_${TEST_CAT}.mp4
