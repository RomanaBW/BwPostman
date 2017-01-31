#!/bin/bash
echo 'Test-Cat:' $TEST_CAT
echo 'Video-Name: ' /tests/tests/_output/videos/bwpostman_com_${TEST_CAT}.mp4

# start x-server and webdriver for chromium
Xvfb :45 -ac -screen 0 1440x900x24 &
export DISPLAY=:45

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
tmux new-session -d -s BwPostmanRecording1 "ffmpeg -y -f x11grab -draw_mouse 0 -video_size 1440x900 -i :45.0 -vcodec libx264 -r 12 /tests/tests/_output/videos/bwpostman_com_${TEST_CAT}.mp4 2>/tests/tests/_output/videos/ffmpeg.log"



## start x-server and webdriver for firefox
## But firefox does not work error free in container, webdriver may be erroneous
#Xvfb :44 -ac -screen 0 1920x1080x24 &
#export DISPLAY=:44

#java -jar -Dwebdriver.gecko.driver=/opt/geckodriver/geckodriver-0.11.1 /opt/selenium/selenium-server-standalone-3.0.1.jar -port 4444 >/dev/null 2>/dev/null &


# Installation
codecept run acceptance Backend/TestInstallationCest::installation --xml report_installation_installation.xml --html report_installation_installation.html
codecept run acceptance Backend/TestInstallationCest::saveOptions --xml report_installation_save_options.xml --html report_installation_save_options.html

# data restore
codecept run acceptance Backend/TestMaintenanceRestoreCest --xml report_restore.xml --html report_restore.html


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
codecept run acceptance Backend/Lists/TestCampaignsListsCest  --xml report_campaigns_lists.xml --html report_campaigns_lists.html
fi

if [ ${TEST_CAT} == lists_cam ]
then
# single tests for campaigns
codecept run acceptance Backend/Lists/TestCampaignsListsCest::SortCampaignsByTableHeader --xml report_campaigns_sort_by_tableheader.xml --html report_campaigns_report_campaigns_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestCampaignsListsCest::SortCampaignsBySelectList --xml report_campaigns_report_campaigns_sort_by_select.xml --html report_campaigns_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestCampaignsListsCest::SearchCampaigns --xml report_campaigns_search.xml --html report_campaigns_search.html
codecept run acceptance Backend/Lists/TestCampaignsListsCest::ListlimitCampaigns --xml report_campaigns_listlimit.xml --html report_campaigns_listlimit.html
codecept run acceptance Backend/Lists/TestCampaignsListsCest::PaginationCampaigns --xml report_campaigns_pagination.xml --html report_campaigns_pagination.html
fi

###
# test mailinglist lists
###

if [ ${TEST_CAT} == lists_all ]
then
# all tests for mailinglists
codecept run acceptance Backend/Lists/TestMailinglistsListsCest  --xml report_mailinglists_lists.xml --html report_mailinglists_lists.html
fi

if [ ${TEST_CAT} == lists_ml ]
then
# single tests for mailinglists
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::PublishMailinglistsByIcon --xml report_mailinglists_publish_by_icon.xml --html report_mailinglists_publish_by_icon.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::PublishMailinglistsByToolbar --xml report_mailinglists_publish_by_toolbar.xml --html report_mailinglists_publish_by_toolbar.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::SortMailinglistsByTableHeader --xml report_mailinglists_sort_by_tableheader.xml --html report_mailinglists_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::SortMailinglistsBySelectList --xml report_mailinglists_sort_by_selectlist.xml --html report_mailinglists_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::FilterMailinglistsByStatus --xml report_mailinglists_filter_by_status.xml --html report_mailinglists_filter_by_status.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::FilterMailinglistsByAccess --xml report_mailinglists_filter_by_access.xml --html report_mailinglists_filter_by_access.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::SearchMailinglists --xml report_mailinglists_search.xml --html report_mailinglists_search.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::ListlimitMailinglists --xml report_mailinglists_listlimit.xml --html report_mailinglists_listlimit.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::PaginationMailinglists --xml report_mailinglists_pagination.xml --html report_mailinglists_pagination.html
fi

###
# test newsletter lists
###

if [ ${TEST_CAT} == lists_all ]
then
# all tests for newsletters
codecept run acceptance Backend/Lists/TestNewslettersListsCest  --xml report_newsletters_lists.xml --html report_newsletters_lists.html
fi

if [ ${TEST_CAT} == lists_nl ]
then
# single tests for newsletters
codecept run acceptance Backend/Lists/TestNewslettersListsCest::SortNewslettersByTableHeader  --xml report_newsletters_sort_by_tableheader.xml --html report_newsletters_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::SortNewslettersBySelectList  --xml report_newsletters_report_newsletters_sort_by_selectlist.xml --html report_newsletters_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::FilterNewslettersByAuthor  --xml report_newsletters_filter_by_author.xml --html report_newsletters_filter_by_author.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::FilterNewslettersByCampaign  --xml report_newsletters_filter_by_campaign.xml --html report_newsletters_filter_by_campaign.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::SearchNewsletters  --xml report_newsletters_seearch.xml --html report_newsletters_seearch.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::ListlimitNewsletters  --xml report_newsletters_listlimit.xml --html report_newsletters_listlimit.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::PaginationNewsletters  --xml report_newsletters_pagination.xml --html report_newsletters_pagination.html
fi

###
# test subscriber lists
###

if [ ${TEST_CAT} == lists_all ]
then
# all tests for subscribers
codecept run acceptance Backend/Lists/TestSubscribersListsCest  --xml report_subscribers_lists.xml --html report_subscribers_lists.html
fi

if [ ${TEST_CAT} == lists_subs ]
then
# single tests for subscribers
codecept run acceptance Backend/Lists/TestSubscribersListsCest::SortSubscribersByTableHeader  --xml report_subscribers_sort_by_tableheader.xml --html report_subscribers_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::SortSubscribersBySelectList  --xml report_subscribers_sort_by_selectlist.xml --html report_subscribers_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::FilterSubscribersByMailformat  --xml report_subscribers_filter_by_mailformat.xml --html report_subscribers_filter_by_mailformat.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::FilterSubscribersByMailinglist  --xml report_subscribers_filter_by_mailinglist.xml --html report_subscribers_filter_by_mailinglist.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::SearchSubscribers  --xml report_subscribers_search.xml --html report_subscribers_search.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::ListlimitSubscribers  --xml report_subscribers_listlimit.xml --html report_subscribers_listlimit.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::PaginationSubscribers  --xml report_subscribers_pagination.xml --html report_subscribers_pagination.html
fi

###
# test template lists
###

if [ ${TEST_CAT} == lists_all ]
then
# all tests for templates
codecept run acceptance Backend/Lists/TestTemplatesListsCest  --xml report_templates_lists.xml --html report_templates_lists.html
fi

if [ ${TEST_CAT} == lists_tpl ]
then
# single tests for templates
codecept run acceptance Backend/Lists/TestTemplatesListsCest::PublishTemplatesByIcon --xml report_templates_publish_by_icon.xml --html report_templates_publish_by_icon.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::PublishTemplatesByToolbar --xml report_templates_publish_by_toolbar.xml --html report_templates_publish_by_toolbar.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::SortTemplatesByTableHeader --xml report_templates_sort_by_tableheader.xml --html report_templates_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::SortTemplatesBySelectList --xml report_templates_sort_by_selectlist.xml --html report_templates_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::FilterTemplatesByStatus --xml report_templates_filter_by_status.xml --html report_templates_filter_by_status.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::FilterTemplatesByMailformat --xml report_templates_filter_by_mailformat.xml --html report_templates_filter_by_mailformat.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::SearchTemplates --xml report_templates_search.xml --html report_templates_search.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::ListlimitTemplates --xml report_templates_listlimit.xml --html report_templates_listlimit.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::PaginationTemplates --xml report_templates_pagination.xml --html report_templates_pagination.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::SetDefaultTemplates --xml report_templates_set_default.xml --html report_templates_set_default.html
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
codecept run acceptance Backend/Details/TestCampaignsDetailsCest --xml report_campaigns_details.xml --html report_campaigns_details.html
fi

if [ ${TEST_CAT} == details_cam ]
then
# single tests for campaigns
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignCancelMainView --xml report_campaigns_cancel_main.xml --html report_campaigns_cancel_main.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignCompleteMainView --xml report_campaigns_complete_main.xml --html report_campaigns_complete_main.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignCancelListView --xml report_campaigns_cancel_list.xml --html report_campaigns_cancel_list.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignListView --xml report_campaigns_complete_list.xml --html report_campaigns_complete_list.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateCampaignTwiceListView  --xml report_campaigns_twice_list.xml --html report_campaigns_twice_list.html
fi

###
# test mailinglist details
###

if [ ${TEST_CAT} == details_all ]
then
# all tests for mailinglists
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest --xml report_mailinglists_details.xml --html report_mailinglists_details.html
fi

if [ ${TEST_CAT} == details_ml ]
then
# single tests for mailinglists
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistCancelMainView --xml report_mailinglists_cancel_main.xml --html report_mailinglists_cancel_main.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistCompleteMainView --xml report_mailinglists_complete_main.xml --html report_mailinglists_complete_main.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistCancelListView --xml report_mailinglists_cancel_list.xml --html report_mailinglists_cancel_list.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistListView --xml report_mailinglists_complete_list.xml --html report_mailinglists_complete_list.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateMailinglistTwiceListView --xml report_mailinglists_twice_list.xml --html report_mailinglists_twice_list.html
fi

###
# test newsletter details
###

if [ ${TEST_CAT} == details_all ]
then
# all tests for newsletters
codecept run acceptance Backend/Details/TestNewslettersDetailsCest --xml report_newsletters_details.xml --html report_newsletters_details.html
fi

if [ ${TEST_CAT} == details_nl ]
then
# single tests for newsletters
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterCancelMainView --xml report_newsletters_cancel_main.xml --html report_newsletters_cancel_main.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterCompleteMainView --xml report_newsletters_complete_main.xml --html report_newsletters_complete_main.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterCancelListView --xml report_newsletters_cancel_list.xml --html report_newsletters_cancel_list.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterListView --xml report_newsletters_complete_list.xml --html report_newsletters_complete_list.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateNewsletterTwiceListView --xml report_newsletters_twice_list.xml --html report_newsletters_twice_list.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::SendNewsletterToTestrecipients --xml report_newsletters_send_test.xml --html report_newsletters_send_test.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::SendCopyOfNewsletterToRealRecipients --xml report_newsletters_send_real.xml --html report_newsletters_send_real.html
fi

###
# test subscriber details
###

if [ ${TEST_CAT} == details_all ]
then
# all tests for subscribers
codecept run acceptance Backend/Details/TestSubscribersDetailsCest --xml report_subscribers_details.xml --html report_subscribers_details.html
fi

if [ ${TEST_CAT} == details_subs ]
then
# single tests for subscribers
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberCancelMainView --xml report_subscribers_cancel_main.xml --html report_subscribers_cancel_main.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberCompleteMainView --xml report_subscribers_complete_main.xml --html report_subscribers_complete_main.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberCancelListView --xml report_subscribers_cancel_list.xml --html report_subscribers_cancel_list.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberListView --xml report_subscribers_complete_list.xml --html report_subscribers_complete_list.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateSubscriberTwiceListView --xml report_subscribers_twice_list.xml --html report_subscribers_twice_list.html
fi

###
# test template details
###

if [ ${TEST_CAT} == details_all ]
then
# all tests for templates
codecept run acceptance Backend/Details/TestTemplatesDetailsCest --xml report_templates_details.xml --html report_templates_details.html
fi

if [ ${TEST_CAT} == details_tpl ]
then
# single tests for templates
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneHtmlTemplateCancelMainView --xml report_templates_html_cancel_main.xml --html report_templates_html_cancel_main.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneHtmlTemplateCompleteMainView --xml report_templates_html_complete_main.xml --html report_templates_html_complete_main.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneHtmlTemplateCancelListView --xml report_templates_html_cancel_list.xml --html report_templates_html_cancel_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneHtmlTemplateListView --xml report_templates_html_complete_list.xml --html report_templates_html_complete_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateHtmlTemplateTwiceListView --xml report_templates_html_twice_list.xml --html report_templates_html_twice_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateCancelMainView --xml report_templates_text_cancel_main.xml --html report_templates_text_cancel_main.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateCompleteMainView --xml report_templates_text_complete_main.xml --html report_templates_text_complete_main.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateCancelListView --xml report_templates_text_cancel_list.xml --html report_templates_text_cancel_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateListView --xml report_templates_text_complete_list.xml --html report_templates_text_complete_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateTextTemplateTwiceListView --xml report_templates_text_twice_list.xml --html report_templates_text_twice_list.html
fi

#################
# test frontend #
#################

if [ ${TEST_CAT} == frontend_all ]
then
# all tests for frontend
codecept run acceptance Frontend --xml report_frontend.xml --html report_frontend.html
fi

if [ ${TEST_CAT} == frontend_single ]
then
# single tests for frontend
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeSimpleActivateAndUnsubscribe --xml report_frontend_activate_and_unsubscribe.xml --html report_frontend_activate_and_unsubscribe.html
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeTwiceActivateAndUnsubscribe --xml report_frontend_activate_twice_and_unscubscribe.xml --html report_frontend_activate_twice_and_unscubscribe.html
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeTwiceActivateGetActivationAndUnsubscribe --xml report_frontend_get_code_and_unsubscribe.xml --html report_frontend_get_code_and_unsubscribe.html
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeActivateSubscribeGetEditlinkAndUnsubscribe --xml report_frontend_get_editlink_and_unsubscribe.xml --html report_frontend_get_editlink_and_unsubscribe.html
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeMissingValuesComponent --xml report_frontend_missing_values.xml --html report_frontend_missing_values.html
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeSimpleActivateChangeAndUnsubscribe --xml report_frontend_activate_change_and_unsubscribe.xml --html report_frontend_activate_change_and_unsubscribe.html
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeActivateUnsubscribeAndActivate --xml report_frontend_activate_unsubscribe_activate.xml --html report_frontend_activate_unsubscribe_activate.html
codecept run acceptance Frontend/SubscribeComponentCest::GetEditlinkWrongAddress --xml report_frontend_get_editlink_wrong_address.xml --html report_frontend_get_editlink_wrong_address.html
codecept run acceptance Frontend/SubscribeComponentCest::WrongUnsubscribeLinks --xml report_frontend_wrong_unsubscribe_link.xml --html report_frontend_wrong_unsubscribe_link.html
fi

####################
# test maintenance #
####################

if [ ${TEST_CAT} == maintenance ]
then
# all tests for maintenance
codecept run acceptance Backend/TestMaintenanceCest --xml report_maintenance.xml --html report_maintenance.html
fi

if [ ${TEST_CAT} == maintenance_single ]
then
# single tests for maintenance
codecept run acceptance Backend/TestMaintenanceCest::saveTables --xml report_maintenance_save_tables.xml --html report_maintenance_save_tables.html
codecept run acceptance Backend/TestMaintenanceCest::checkTables --xml report_maintenancecheck_tables.xml --html report_maintenance_check_tables.html
codecept run acceptance Backend/TestMaintenanceCest::restoreTables --xml report_maintenance_restore_tables.xml --html report_maintenance_restore_tables.html
codecept run acceptance Backend/TestMaintenanceCest::testBasicSettings --xml report_maintenance_basic_settings.xml --html report_maintenance_basic_settings.html
codecept run acceptance Backend/TestMaintenanceCest::testForumLink --xml report_maintenance_forum_link.xml --html report_maintenance_forum_link.html
fi

if [ ${TEST_CAT} == all ]
then
# run all tests
codecept run acceptance Backend/Lists --xml report_lists.xml --html report_lists.html
codecept run acceptance Backend/Details --xml report_details.xml --html report_details.html
codecept run acceptance Frontend --xml report_frontend.xml --html report_frontend.html
codecept run acceptance Backend/TestMaintenanceCest --xml report_maintenance.xml --html report_maintenance.html
fi

# Deinstallation
codecept run acceptance Backend/TestDeinstallationCest --xml report_deinstallation.xml --html report_deinstallation.html

# stop video recording
echo 'stop recording'
sleep 1
tmux send-keys -t BwPostmanRecording1 q
sleep 3
chmod 0777 /tests/tests/_output/videos/bwpostman_com_${TEST_CAT}.mp4
