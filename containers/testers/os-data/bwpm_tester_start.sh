#!/bin/bash
### Tests with ## at the beginning are both commented out and faulty




echo 'Test-Cat:' $TEST_CAT
echo 'Project: ' $TEST_PROJECT
echo 'Test-Env: ' $TEST_ENV

VIDEO_NAME=/tests/tests/_output/videos/${TEST_PROJECT}_${TEST_CAT}.mp4
VIDEO_LOG=/tests/tests/_output/videos/ffmpeg_${TEST_PROJECT}.log

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
codecept run acceptance Backend/Lists/TestCampaignsListsCest  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_campaigns_lists.xml --html htmlreports/report_campaigns_lists.html
fi

if [ ${TEST_CAT} == lists_cam ]
then
# single tests for campaigns
codecept run acceptance Backend/Lists/TestCampaignsListsCest::SortCampaignsByTableHeader ${DEBUG} --env $TEST_ENV --xml xmlreports/report_campaigns_sort_by_tableheader.xml --html htmlreports/report_campaigns_report_campaigns_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestCampaignsListsCest::SortCampaignsBySelectList ${DEBUG} --env $TEST_ENV --xml xmlreports/report_campaigns_report_campaigns_sort_by_select.xml --html htmlreports/report_campaigns_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestCampaignsListsCest::SearchCampaigns ${DEBUG} --env $TEST_ENV --xml xmlreports/report_campaigns_search.xml --html htmlreports/report_campaigns_search.html
codecept run acceptance Backend/Lists/TestCampaignsListsCest::ListlimitCampaigns ${DEBUG} --env $TEST_ENV --xml xmlreports/report_campaigns_listlimit.xml --html htmlreports/report_campaigns_listlimit.html
codecept run acceptance Backend/Lists/TestCampaignsListsCest::PaginationCampaigns ${DEBUG} --env $TEST_ENV --xml xmlreports/report_campaigns_pagination.xml --html htmlreports/report_campaigns_pagination.html
fi

###
# test mailinglist lists
###

if [ ${TEST_CAT} == lists_all ]
then
# all tests for mailinglists
codecept run acceptance Backend/Lists/TestMailinglistsListsCest  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_mailinglists_lists.xml --html htmlreports/report_mailinglists_lists.html
fi

if [ ${TEST_CAT} == lists_ml ]
then
# single tests for mailinglists
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::PublishMailinglistsByIcon ${DEBUG} --env $TEST_ENV --xml xmlreports/report_mailinglists_publish_by_icon.xml --html htmlreports/report_mailinglists_publish_by_icon.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::PublishMailinglistsByToolbar ${DEBUG} --env $TEST_ENV --xml xmlreports/report_mailinglists_publish_by_toolbar.xml --html htmlreports/report_mailinglists_publish_by_toolbar.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::SortMailinglistsByTableHeader ${DEBUG} --env $TEST_ENV --xml xmlreports/report_mailinglists_sort_by_tableheader.xml --html htmlreports/report_mailinglists_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::SortMailinglistsBySelectList ${DEBUG} --env $TEST_ENV --xml xmlreports/report_mailinglists_sort_by_selectlist.xml --html htmlreports/report_mailinglists_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::FilterMailinglistsByStatus ${DEBUG} --env $TEST_ENV --xml xmlreports/report_mailinglists_filter_by_status.xml --html htmlreports/report_mailinglists_filter_by_status.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::FilterMailinglistsByAccess ${DEBUG} --env $TEST_ENV --xml xmlreports/report_mailinglists_filter_by_access.xml --html htmlreports/report_mailinglists_filter_by_access.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::SearchMailinglists ${DEBUG} --env $TEST_ENV --xml xmlreports/report_mailinglists_search.xml --html htmlreports/report_mailinglists_search.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::ListlimitMailinglists ${DEBUG} --env $TEST_ENV --xml xmlreports/report_mailinglists_listlimit.xml --html htmlreports/report_mailinglists_listlimit.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::PaginationMailinglists ${DEBUG} --env $TEST_ENV --xml xmlreports/report_mailinglists_pagination.xml --html htmlreports/report_mailinglists_pagination.html
fi

###
# test newsletter lists
###

if [ ${TEST_CAT} == lists_all ]
then
# all tests for newsletters
codecept run acceptance Backend/Lists/TestNewslettersListsCest  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_lists.xml --html htmlreports/report_newsletters_lists.html
fi

if [ ${TEST_CAT} == lists_nl ]
then
# single tests for newsletters
codecept run acceptance Backend/Lists/TestNewslettersListsCest::SortNewslettersByTableHeader  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_sort_by_tableheader.xml --html htmlreports/report_newsletters_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::SortNewslettersBySelectList  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_report_newsletters_sort_by_selectlist.xml --html htmlreports/report_newsletters_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::FilterNewslettersByAuthor  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_filter_by_author.xml --html htmlreports/report_newsletters_filter_by_author.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::FilterNewslettersByCampaign  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_filter_by_campaign.xml --html htmlreports/report_newsletters_filter_by_campaign.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::SearchNewsletters  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_seearch.xml --html htmlreports/report_newsletters_seearch.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::ListlimitNewsletters  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_listlimit.xml --html htmlreports/report_newsletters_listlimit.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::PaginationNewsletters  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_pagination.xml --html htmlreports/report_newsletters_pagination.html

codecept run acceptance Backend/Lists/TestNewslettersListsCest::SortSentNewslettersByTableHeader  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_sort_sent_by_tableheader.xml --html htmlreports/report_newsletters_sort_sent_by_tableheader.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::SortSentNewslettersBySelectList  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_report_newsletters_sort_sent_by_selectlist.xml --html htmlreports/report_newsletters_sort_sent_by_selectlist.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::FilterSentNewslettersByAuthor  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_filter_sent_by_author.xml --html htmlreports/report_newsletters_filter_sent_by_author.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::FilterSentNewslettersByCampaign  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_filter_sent_by_campaign.xml --html htmlreports/report_newsletters_filter_sent_by_campaign.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::FilterSentNewslettersByStatus  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_filter_sent_by_statos.xml --html htmlreports/report_newsletters_filter_sent_by_status.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::SearchSentNewsletters  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_sent_search.xml --html htmlreports/report_newsletters_sent_search.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::ListlimitSentNewsletters  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_sent_listlimit.xml --html htmlreports/report_newsletters_sent_listlimit.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::PaginationSentNewsletters  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_sent_pagination.xml --html htmlreports/report_newsletters_sent_pagination.html
fi

###
# test subscriber lists
###

if [ ${TEST_CAT} == lists_all ]
then
# all tests for subscribers
codecept run acceptance Backend/Lists/TestSubscribersListsCest  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_subscribers_lists.xml --html htmlreports/report_subscribers_lists.html
fi

if [ ${TEST_CAT} == lists_subs ]
then
# single tests for subscribers
codecept run acceptance Backend/Lists/TestSubscribersListsCest::SortSubscribersByTableHeader  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_subscribers_sort_by_tableheader.xml --html htmlreports/report_subscribers_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::SortSubscribersBySelectList  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_subscribers_sort_by_selectlist.xml --html htmlreports/report_subscribers_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::FilterSubscribersByMailformat  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_subscribers_filter_by_mailformat.xml --html htmlreports/report_subscribers_filter_by_mailformat.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::FilterSubscribersByMailinglist  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_subscribers_filter_by_mailinglist.xml --html htmlreports/report_subscribers_filter_by_mailinglist.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::SearchSubscribers  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_subscribers_search.xml --html htmlreports/report_subscribers_search.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::ListlimitSubscribers  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_subscribers_listlimit.xml --html htmlreports/report_subscribers_listlimit.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::PaginationSubscribers  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_subscribers_pagination.xml --html htmlreports/report_subscribers_pagination.html
fi

###
# test template lists
###

if [ ${TEST_CAT} == lists_all ]
then
# all tests for templates
codecept run acceptance Backend/Lists/TestTemplatesListsCest  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_templates_lists.xml --html htmlreports/report_templates_lists.html
fi

if [ ${TEST_CAT} == lists_tpl ]
then
# single tests for templates
codecept run acceptance Backend/Lists/TestTemplatesListsCest::PublishTemplatesByIcon ${DEBUG} --env $TEST_ENV --xml xmlreports/report_templates_publish_by_icon.xml --html htmlreports/report_templates_publish_by_icon.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::PublishTemplatesByToolbar ${DEBUG} --env $TEST_ENV --xml xmlreports/report_templates_publish_by_toolbar.xml --html htmlreports/report_templates_publish_by_toolbar.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::SortTemplatesByTableHeader ${DEBUG} --env $TEST_ENV --xml xmlreports/report_templates_sort_by_tableheader.xml --html htmlreports/report_templates_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::SortTemplatesBySelectList ${DEBUG} --env $TEST_ENV --xml xmlreports/report_templates_sort_by_selectlist.xml --html htmlreports/report_templates_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::FilterTemplatesByStatus ${DEBUG} --env $TEST_ENV --xml xmlreports/report_templates_filter_by_status.xml --html htmlreports/report_templates_filter_by_status.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::FilterTemplatesByMailformat ${DEBUG} --env $TEST_ENV --xml xmlreports/report_templates_filter_by_mailformat.xml --html htmlreports/report_templates_filter_by_mailformat.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::SearchTemplates ${DEBUG} --env $TEST_ENV --xml xmlreports/report_templates_search.xml --html htmlreports/report_templates_search.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::ListlimitTemplates ${DEBUG} --env $TEST_ENV --xml xmlreports/report_templates_listlimit.xml --html htmlreports/report_templates_listlimit.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::PaginationTemplates ${DEBUG} --env $TEST_ENV --xml xmlreports/report_templates_pagination.xml --html htmlreports/report_templates_pagination.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::SetDefaultTemplates ${DEBUG} --env $TEST_ENV --xml xmlreports/report_templates_set_default.xml --html htmlreports/report_templates_set_default.html
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
codecept run acceptance Backend/Details/TestCampaignsDetailsCest ${DEBUG} --env $TEST_ENV --xml xmlreports/report_campaigns_details.xml --html htmlreports/report_campaigns_details.html
fi

if [ ${TEST_CAT} == details_cam ]
then
# single tests for campaigns
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignCancelMainView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_campaigns_cancel_main.xml --html htmlreports/report_campaigns_cancel_main.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignCompleteMainView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_campaigns_complete_main.xml --html htmlreports/report_campaigns_complete_main.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignCancelListView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_campaigns_cancel_list.xml --html htmlreports/report_campaigns_cancel_list.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignCompleteListView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_campaigns_complete_list.xml --html htmlreports/report_campaigns_complete_list.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignListViewRestore ${DEBUG} --env $TEST_ENV --xml xmlreports/report_campaigns_restore_list.xml --html htmlreports/report_campaigns_restore_list.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateCampaignTwiceListView  ${DEBUG} --env $TEST_ENV --xml xmlreports/report_campaigns_twice_list.xml --html htmlreports/report_campaigns_twice_list.html
fi

###
# test mailinglist details
###

if [ ${TEST_CAT} == details_all ]
then
# all tests for mailinglists
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest ${DEBUG} --env $TEST_ENV --xml xmlreports/report_mailinglists_details.xml --html htmlreports/report_mailinglists_details.html
fi

if [ ${TEST_CAT} == details_ml ]
then
# single tests for mailinglists
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistCancelMainView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_mailinglists_cancel_main.xml --html htmlreports/report_mailinglists_cancel_main.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistCompleteMainView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_mailinglists_complete_main.xml --html htmlreports/report_mailinglists_complete_main.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistCancelListView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_mailinglists_cancel_list.xml --html htmlreports/report_mailinglists_cancel_list.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistCompleteListView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_mailinglists_complete_list.xml --html htmlreports/report_mailinglists_complete_list.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistListViewRestore ${DEBUG} --env $TEST_ENV --xml xmlreports/report_mailinglists_restore_list.xml --html htmlreports/report_mailinglists_restore_list.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateMailinglistTwiceListView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_mailinglists_twice_list.xml --html htmlreports/report_mailinglists_twice_list.html
fi

###
# test newsletter details
###

if [ ${TEST_CAT} == details_all ]
then
# all tests for newsletters
codecept run acceptance Backend/Details/TestNewslettersDetailsCest ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_details.xml --html htmlreports/report_newsletters_details.html
fi

if [ ${TEST_CAT} == details_nl ]
then
# single tests for newsletters
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterCancelMainView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_cancel_main.xml --html htmlreports/report_newsletters_cancel_main.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterCompleteMainView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_complete_main.xml --html htmlreports/report_newsletters_complete_main.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterCancelListView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_cancel_list.xml --html htmlreports/report_newsletters_cancel_list.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterCompleteListView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_complete_list.xml --html htmlreports/report_newsletters_complete_list.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterListViewRestore ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_restore_list.xml --html htmlreports/report_newsletters_restore_list.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateNewsletterTwiceListView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_twice_list.xml --html htmlreports/report_newsletters_twice_list.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CopyNewsletter ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_copy.xml --html htmlreports/report_newsletters_copy.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::SendNewsletterToTestrecipients ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_send_test.xml --html htmlreports/report_newsletters_send_test.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::SendNewsletterToRealRecipients ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_send_real.xml --html htmlreports/report_newsletters_send_real.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::EditSentNewsletter ${DEBUG} --env $TEST_ENV --xml xmlreports/report_newsletters_edit_sent.xml --html htmlreports/report_newsletters_edit_sent.html
fi

###
# test subscriber details
###

if [ ${TEST_CAT} == details_all ]
then
# all tests for subscribers
codecept run acceptance Backend/Details/TestSubscribersDetailsCest ${DEBUG} --env $TEST_ENV --xml xmlreports/report_subscribers_details.xml --html htmlreports/report_subscribers_details.html
fi

if [ ${TEST_CAT} == details_subs ]
then
# single tests for subscribers
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberCancelMainView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_subscribers_cancel_main.xml --html htmlreports/report_subscribers_cancel_main.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberCompleteMainView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_subscribers_complete_main.xml --html htmlreports/report_subscribers_complete_main.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberCancelListView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_subscribers_cancel_list.xml --html htmlreports/report_subscribers_cancel_list.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberCompleteListView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_subscribers_complete_list.xml --html htmlreports/report_subscribers_complete_list.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberListViewRestore ${DEBUG} --env $TEST_ENV --xml xmlreports/report_subscribers_restore_list.xml --html htmlreports/report_subscribers_restore_list.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateSubscriberTwiceListView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_subscribers_twice_list.xml --html htmlreports/report_subscribers_twice_list.html
fi

###
# test template details
###

if [ ${TEST_CAT} == details_all ]
then
# all tests for templates
codecept run acceptance Backend/Details/TestTemplatesDetailsCest ${DEBUG} --env $TEST_ENV --xml xmlreports/report_templates_details.xml --html htmlreports/report_templates_details.html
fi

if [ ${TEST_CAT} == details_tpl ]
then
# single tests for templates
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneHtmlTemplateCancelMainView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_templates_html_cancel_main.xml --html htmlreports/report_templates_html_cancel_main.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneHtmlTemplateCompleteMainView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_templates_html_complete_main.xml --html htmlreports/report_templates_html_complete_main.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneHtmlTemplateCancelListView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_templates_html_cancel_list.xml --html htmlreports/report_templates_html_cancel_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneHtmlTemplateListView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_templates_html_complete_list.xml --html htmlreports/report_templates_html_complete_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateHtmlTemplateTwiceListView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_templates_html_twice_list.xml --html htmlreports/report_templates_html_twice_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateCancelMainView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_templates_text_cancel_main.xml --html htmlreports/report_templates_text_cancel_main.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateCompleteMainView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_templates_text_complete_main.xml --html htmlreports/report_templates_text_complete_main.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateCancelListView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_templates_text_cancel_list.xml --html htmlreports/report_templates_text_cancel_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateCompleteListView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_templates_text_complete_list.xml --html htmlreports/report_templates_text_complete_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateRestoreListView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_templates_text_restore_list.xml --html htmlreports/report_templates_text_restore_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateTextTemplateTwiceListView ${DEBUG} --env $TEST_ENV --xml xmlreports/report_templates_text_twice_list.xml --html htmlreports/report_templates_text_twice_list.html
fi

#################
# test frontend #
#################

if [ ${TEST_CAT} == frontend_all ]
then
# all tests for frontend
codecept run acceptance Frontend ${DEBUG} --env $TEST_ENV --xml xmlreports/report_frontend.xml --html htmlreports/report_frontend.html
fi

if [ ${TEST_CAT} == frontend_single ]
then
# single tests for frontend
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeSimpleActivateAndUnsubscribe ${DEBUG} --env $TEST_ENV --xml xmlreports/report_frontend_activate_and_unsubscribe.xml --html htmlreports/report_frontend_activate_and_unsubscribe.html
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeTwiceActivateAndUnsubscribe ${DEBUG} --env $TEST_ENV --xml xmlreports/report_frontend_activate_twice_and_unscubscribe.xml --html htmlreports/report_frontend_activate_twice_and_unscubscribe.html
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeTwiceActivateGetActivationAndUnsubscribe ${DEBUG} --env $TEST_ENV --xml xmlreports/report_frontend_get_code_and_unsubscribe.xml --html htmlreports/report_frontend_get_code_and_unsubscribe.html
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeActivateSubscribeGetEditlinkAndUnsubscribe ${DEBUG} --env $TEST_ENV --xml xmlreports/report_frontend_get_editlink_and_unsubscribe.xml --html htmlreports/report_frontend_get_editlink_and_unsubscribe.html
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeMissingValuesComponent ${DEBUG} --env $TEST_ENV --xml xmlreports/report_frontend_missing_values.xml --html htmlreports/report_frontend_missing_values.html
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeSimpleActivateChangeAndUnsubscribe ${DEBUG} --env $TEST_ENV --xml xmlreports/report_frontend_activate_change_and_unsubscribe.xml --html htmlreports/report_frontend_activate_change_and_unsubscribe.html
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeActivateUnsubscribeAndActivate ${DEBUG} --env $TEST_ENV --xml xmlreports/report_frontend_activate_unsubscribe_activate.xml --html htmlreports/report_frontend_activate_unsubscribe_activate.html
codecept run acceptance Frontend/SubscribeComponentCest::GetEditlinkWrongAddress ${DEBUG} --env $TEST_ENV --xml xmlreports/report_frontend_get_editlink_wrong_address.xml --html htmlreports/report_frontend_get_editlink_wrong_address.html
codecept run acceptance Frontend/SubscribeComponentCest::WrongUnsubscribeLinks ${DEBUG} --env $TEST_ENV --xml xmlreports/report_frontend_wrong_unsubscribe_link.xml --html htmlreports/report_frontend_wrong_unsubscribe_link.html
fi

####################
# test maintenance #
####################

if [ ${TEST_CAT} == maintenance ]
then
# all tests for maintenance
codecept run acceptance Backend/TestMaintenanceCest ${DEBUG} --env $TEST_ENV --xml xmlreports/report_maintenance.xml --html htmlreports/report_maintenance.html
fi

if [ ${TEST_CAT} == maintenance_single ]
then
# single tests for maintenance
codecept run acceptance Backend/TestMaintenanceCest::saveTables ${DEBUG} --env $TEST_ENV --xml xmlreports/report_maintenance_save_tables.xml --html htmlreports/report_maintenance_save_tables.html
codecept run acceptance Backend/TestMaintenanceCest::checkTables ${DEBUG} --env $TEST_ENV --xml xmlreports/report_maintenancecheck_tables.xml --html htmlreports/report_maintenance_check_tables.html
codecept run acceptance Backend/TestMaintenanceCest::restoreTables ${DEBUG} --env $TEST_ENV --xml xmlreports/report_maintenance_restore_tables.xml --html htmlreports/report_maintenance_restore_tables.html
codecept run acceptance Backend/TestMaintenanceCest::testBasicSettings ${DEBUG} --env $TEST_ENV --xml xmlreports/report_maintenance_basic_settings.xml --html htmlreports/report_maintenance_basic_settings.html
codecept run acceptance Backend/TestMaintenanceCest::testForumLink ${DEBUG} --env $TEST_ENV --xml xmlreports/report_maintenance_forum_link.xml --html htmlreports/report_maintenance_forum_link.html
fi

###############################
# test plugin User2Subscriber #
###############################

if [ ${TEST_CAT} == user2subscriber_all ]
then
# all tests for plugin user2subscriber
codecept run acceptance User2Subscriber/User2SubscriberCest ${DEBUG} --env $TEST_ENV --xml xmlreports/report_user2Subscriber.xml --html htmlreports/report_user2Subscriber.html
fi

if [ ${TEST_CAT} == user2subscriber_single ]
then
# single tests for plugin user2subscriber
#codecept run acceptance User2Subscriber/User2SubscriberCest::setupUser2Subscriber ${DEBUG} --env $TEST_ENV --xml xmlreports/report_user2Subscriber_activate.xml --html htmlreports/report_user2Subscriber_activate.html

codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithoutSubscription ${DEBUG} --env $TEST_ENV --xml xmlreports/report_u2s_no_subscription.xml --html htmlreports/report_u2s_no_subscription.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionSwitchSubscriptionWithoutSubscription ${DEBUG} --env $TEST_ENV --xml xmlreports/report_u2s_yes_no_subscription.xml --html htmlreports/report_u2s_yes_no_subscription.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithoutActivationExtended ${DEBUG} --env $TEST_ENV --xml xmlreports/report_u2s_no_activation_ext.xml --html htmlreports/report_u2s_no_activation_ext.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithActivationByFrontend ${DEBUG} --env $TEST_ENV --xml xmlreports/report_u2s_activation_FE.xml --html htmlreports/report_u2s_activation_FE.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithExistingSubscriptionSameList ${DEBUG} --env $TEST_ENV --xml xmlreports/report_u2s_subs_same_list.xml --html htmlreports/report_u2s_subs_same_list.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithExistingSubscriptionDifferentList ${DEBUG} --env $TEST_ENV --xml xmlreports/report_u2s_subs_diff_list.xml --html htmlreports/report_u2s_subs_diff_list.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithActivationByBackend ${DEBUG} --env $TEST_ENV --xml xmlreports/report_u2s_activation_BE.xml --html htmlreports/report_u2s_activation_BE.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithTextFormat ${DEBUG} --env $TEST_ENV --xml xmlreports/report_u2s_text_format.xml --html htmlreports/report_u2s_text_format.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithoutFormatSelectionHTML ${DEBUG} --env $TEST_ENV --xml xmlreports/report_u2s_no_format_select_html.xml --html htmlreports/report_u2s_no_format_select_html.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithoutFormatSelectionText ${DEBUG} --env $TEST_ENV --xml xmlreports/report_u2s_no_format_select_text.xml --html htmlreports/report_u2s_no_format_select_text.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithAnotherMailinglist ${DEBUG} --env $TEST_ENV --xml xmlreports/report_u2s_another_mailinglist.xml --html htmlreports/report_u2s_another_mailinglist.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithTwoMailinglists ${DEBUG} --env $TEST_ENV --xml xmlreports/report_u2s_two_mailinglists.xml --html htmlreports/report_u2s_two_mailinglists.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithoutMailinglists ${DEBUG} --env $TEST_ENV --xml xmlreports/report_u2s_no_mailinglists.xml --html htmlreports/report_u2s_no_mailinglists.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithMailChangeYes ${DEBUG} --env $TEST_ENV --xml xmlreports/report_u2s_with_mail_change.xml --html htmlreports/report_u2s_with_mail_change.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithoutActivationWithMailChangeYes ${DEBUG} --env $TEST_ENV --xml xmlreports/report_u2s_.xml --html htmlreports/report_u2s_.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithMailChangeNo ${DEBUG} --env $TEST_ENV --xml xmlreports/report_u2s_no_activation_mail_change.xml --html htmlreports/report_u2s_no_activation_mail_change.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithDeleteNo ${DEBUG} --env $TEST_ENV --xml xmlreports/report_u2s_delete_no.xml --html htmlreports/report_u2s_delete_no.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberOptionsPluginDeactivated ${DEBUG} --env $TEST_ENV --xml xmlreports/report_u2s_plugin_deactivated.xml --html htmlreports/report_u2s_plugin_deactivated.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberOptionsMessage ${DEBUG} --env $TEST_ENV --xml xmlreports/report_u2s_options_message.xml --html htmlreports/report_u2s_options_message.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberOptionsSwitchShowFormat ${DEBUG} --env $TEST_ENV --xml xmlreports/report_u2s_switch_show_format.xml --html htmlreports/report_u2s_switch_show_format.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberPredefinedFormat ${DEBUG} --env $TEST_ENV --xml xmlreports/report_u2s_predefined_format.xml --html htmlreports/report_u2s_predefined_format.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberOptionsAutoUpdate ${DEBUG} --env $TEST_ENV --xml xmlreports/report_u2s_auto_update.xml --html htmlreports/report_u2s_auto_update.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberOptionsAutoDelete ${DEBUG} --env $TEST_ENV --xml xmlreports/report_u2s_auto_delete.xml --html htmlreports/report_u2s_auto_delete.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberOptionsMailinglists ${DEBUG} --env $TEST_ENV --xml xmlreports/report_u2s_options_mailinglists.xml --html htmlreports/report_u2s_options_mailinglists.html
fi

if [ ${TEST_CAT} == all ]
then
# run all tests
codecept run acceptance Backend/Lists ${DEBUG} --env $TEST_ENV --xml xmlreports/report_lists.xml --html htmlreports/report_lists.html
codecept run acceptance Backend/Details ${DEBUG} --env $TEST_ENV --xml xmlreports/report_details.xml --html htmlreports/report_details.html
codecept run acceptance Frontend ${DEBUG} --env $TEST_ENV --xml xmlreports/report_frontend.xml --html htmlreports/report_frontend.html
codecept run acceptance Backend/TestMaintenanceCest ${DEBUG} --env $TEST_ENV --xml xmlreports/report_maintenance.xml --html htmlreports/report_maintenance.html
codecept run acceptance User2Subscriber ${DEBUG} --env $TEST_ENV --xml xmlreports/report_user2Subscriber.xml --html htmlreports/report_user2Subscriber.html
fi

# stop video recording
echo 'stop recording'
sleep 1
tmux send-keys -t BwPostmanRecording1 q
sleep 3
XVFB_PID="$(pgrep -f /usr/bin/Xvfb)"
echo "PID: ${XVFB_PID}"
kill "$(pgrep -f /usr/bin/Xvfb)"
