#!/bin/bash
### Tests with ## at the beginning are both commented out and faulty

#BW_TEST_DEBUG='--debug'
#BW_TEST_DEBUG=''

export BW_NEW_TEST_RUN=true

##########################
# test component backend #
##########################

######
# test lists
######

###
# test campaigns lists
###

if [ "${BW_TEST_CAT}" == lists_all ]
then
# all tests for campaigns
codecept run acceptance Backend/Lists/TestCampaignsListsCest  ${BW_TEST_DEBUG} --xml xmlreports/report_campaigns_lists.xml --html htmlreports/report_campaigns_lists.html
fi

if [ "${BW_TEST_CAT}" == lists_cam ]
then
# single tests for campaigns
codecept run acceptance Backend/Lists/TestCampaignsListsCest::SortCampaignsByTableHeader ${BW_TEST_DEBUG} --xml xmlreports/report_campaigns_sort_by_tableheader.xml --html htmlreports/report_campaigns_report_campaigns_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestCampaignsListsCest::SortCampaignsBySelectList ${BW_TEST_DEBUG} --xml xmlreports/report_campaigns_report_campaigns_sort_by_select.xml --html htmlreports/report_campaigns_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestCampaignsListsCest::SearchCampaigns ${BW_TEST_DEBUG} --xml xmlreports/report_campaigns_search.xml --html htmlreports/report_campaigns_search.html
codecept run acceptance Backend/Lists/TestCampaignsListsCest::ListlimitCampaigns ${BW_TEST_DEBUG} --xml xmlreports/report_campaigns_listlimit.xml --html htmlreports/report_campaigns_listlimit.html
codecept run acceptance Backend/Lists/TestCampaignsListsCest::PaginationCampaigns ${BW_TEST_DEBUG} --xml xmlreports/report_campaigns_pagination.xml --html htmlreports/report_campaigns_pagination.html
fi

###
# test mailinglist lists
###

if [ "${BW_TEST_CAT}" == lists_all ]
then
# all tests for mailinglists
codecept run acceptance Backend/Lists/TestMailinglistsListsCest  ${BW_TEST_DEBUG} --xml xmlreports/report_mailinglists_lists.xml --html htmlreports/report_mailinglists_lists.html
fi

if [ "${BW_TEST_CAT}" == lists_ml ]
then
# single tests for mailinglists
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::PublishMailinglistsByIcon ${BW_TEST_DEBUG} --xml xmlreports/report_mailinglists_publish_by_icon.xml --html htmlreports/report_mailinglists_publish_by_icon.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::PublishMailinglistsByToolbar ${BW_TEST_DEBUG} --xml xmlreports/report_mailinglists_publish_by_toolbar.xml --html htmlreports/report_mailinglists_publish_by_toolbar.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::SortMailinglistsByTableHeader ${BW_TEST_DEBUG} --xml xmlreports/report_mailinglists_sort_by_tableheader.xml --html htmlreports/report_mailinglists_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::SortMailinglistsBySelectList ${BW_TEST_DEBUG} --xml xmlreports/report_mailinglists_sort_by_selectlist.xml --html htmlreports/report_mailinglists_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::FilterMailinglistsByStatus ${BW_TEST_DEBUG} --xml xmlreports/report_mailinglists_filter_by_status.xml --html htmlreports/report_mailinglists_filter_by_status.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::FilterMailinglistsByAccess ${BW_TEST_DEBUG} --xml xmlreports/report_mailinglists_filter_by_access.xml --html htmlreports/report_mailinglists_filter_by_access.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::SearchMailinglists ${BW_TEST_DEBUG} --xml xmlreports/report_mailinglists_search.xml --html htmlreports/report_mailinglists_search.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::ListlimitMailinglists ${BW_TEST_DEBUG} --xml xmlreports/report_mailinglists_listlimit.xml --html htmlreports/report_mailinglists_listlimit.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::PaginationMailinglists ${BW_TEST_DEBUG} --xml xmlreports/report_mailinglists_pagination.xml --html htmlreports/report_mailinglists_pagination.html
fi

###
# test newsletter lists
###

if [ "${BW_TEST_CAT}" == lists_all ]
then
# all tests for newsletters
codecept run acceptance Backend/Lists/TestNewslettersListsCest  ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_lists.xml --html htmlreports/report_newsletters_lists.html
fi

if [ "${BW_TEST_CAT}" == lists_nl ]
then
# single tests for newsletters
codecept run acceptance Backend/Lists/TestNewslettersListsCest::SortNewslettersByTableHeader  ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_sort_by_tableheader.xml --html htmlreports/report_newsletters_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::SortNewslettersBySelectList  ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_report_newsletters_sort_by_selectlist.xml --html htmlreports/report_newsletters_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::FilterNewslettersByAuthor  ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_filter_by_author.xml --html htmlreports/report_newsletters_filter_by_author.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::FilterNewslettersByCampaign  ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_filter_by_campaign.xml --html htmlreports/report_newsletters_filter_by_campaign.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::SearchNewsletters  ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_seearch.xml --html htmlreports/report_newsletters_seearch.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::ListlimitNewsletters  ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_listlimit.xml --html htmlreports/report_newsletters_listlimit.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::PaginationNewsletters  ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_pagination.xml --html htmlreports/report_newsletters_pagination.html

codecept run acceptance Backend/Lists/TestNewslettersListsCest::SortSentNewslettersByTableHeader  ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_sort_sent_by_tableheader.xml --html htmlreports/report_newsletters_sort_sent_by_tableheader.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::SortSentNewslettersBySelectList  ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_report_newsletters_sort_sent_by_selectlist.xml --html htmlreports/report_newsletters_sort_sent_by_selectlist.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::FilterSentNewslettersByAuthor  ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_filter_sent_by_author.xml --html htmlreports/report_newsletters_filter_sent_by_author.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::FilterSentNewslettersByCampaign  ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_filter_sent_by_campaign.xml --html htmlreports/report_newsletters_filter_sent_by_campaign.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::FilterSentNewslettersByStatus  ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_filter_sent_by_status.xml --html htmlreports/report_newsletters_filter_sent_by_status.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::SearchSentNewsletters  ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_sent_search.xml --html htmlreports/report_newsletters_sent_search.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::ListlimitSentNewsletters  ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_sent_listlimit.xml --html htmlreports/report_newsletters_sent_listlimit.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::PaginationSentNewsletters  ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_sent_pagination.xml --html htmlreports/report_newsletters_sent_pagination.html
fi

###
# test subscriber lists
###

if [ "${BW_TEST_CAT}" == lists_all ]
then
# all tests for subscribers
codecept run acceptance Backend/Lists/TestSubscribersListsCest  ${BW_TEST_DEBUG} --xml xmlreports/report_subscribers_lists.xml --html htmlreports/report_subscribers_lists.html
fi

if [ "${BW_TEST_CAT}" == lists_subs ]
then
# single tests for subscribers
codecept run acceptance Backend/Lists/TestSubscribersListsCest::SortSubscribersByTableHeader  ${BW_TEST_DEBUG} --xml xmlreports/report_subscribers_sort_by_tableheader.xml --html htmlreports/report_subscribers_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::SortSubscribersBySelectList  ${BW_TEST_DEBUG} --xml xmlreports/report_subscribers_sort_by_selectlist.xml --html htmlreports/report_subscribers_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::FilterSubscribersByMailformat  ${BW_TEST_DEBUG} --xml xmlreports/report_subscribers_filter_by_mailformat.xml --html htmlreports/report_subscribers_filter_by_mailformat.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::FilterSubscribersByMailinglist  ${BW_TEST_DEBUG} --xml xmlreports/report_subscribers_filter_by_mailinglist.xml --html htmlreports/report_subscribers_filter_by_mailinglist.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::SearchSubscribers  ${BW_TEST_DEBUG} --xml xmlreports/report_subscribers_search.xml --html htmlreports/report_subscribers_search.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::ListlimitSubscribers  ${BW_TEST_DEBUG} --xml xmlreports/report_subscribers_listlimit.xml --html htmlreports/report_subscribers_listlimit.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::PaginationSubscribers  ${BW_TEST_DEBUG} --xml xmlreports/report_subscribers_pagination.xml --html htmlreports/report_subscribers_pagination.html
fi

###
# test template lists
###

if [ "${BW_TEST_CAT}" == lists_all ]
then
# all tests for templates
codecept run acceptance Backend/Lists/TestTemplatesListsCest  ${BW_TEST_DEBUG} --xml xmlreports/report_templates_lists.xml --html htmlreports/report_templates_lists.html
fi

if [ "${BW_TEST_CAT}" == lists_tpl ]
then
# single tests for templates
codecept run acceptance Backend/Lists/TestTemplatesListsCest::PublishTemplatesByIcon ${BW_TEST_DEBUG} --xml xmlreports/report_templates_publish_by_icon.xml --html htmlreports/report_templates_publish_by_icon.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::PublishTemplatesByToolbar ${BW_TEST_DEBUG} --xml xmlreports/report_templates_publish_by_toolbar.xml --html htmlreports/report_templates_publish_by_toolbar.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::SortTemplatesByTableHeader ${BW_TEST_DEBUG} --xml xmlreports/report_templates_sort_by_tableheader.xml --html htmlreports/report_templates_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::SortTemplatesBySelectList ${BW_TEST_DEBUG} --xml xmlreports/report_templates_sort_by_selectlist.xml --html htmlreports/report_templates_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::FilterTemplatesByStatus ${BW_TEST_DEBUG} --xml xmlreports/report_templates_filter_by_status.xml --html htmlreports/report_templates_filter_by_status.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::FilterTemplatesByMailformat ${BW_TEST_DEBUG} --xml xmlreports/report_templates_filter_by_mailformat.xml --html htmlreports/report_templates_filter_by_mailformat.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::SearchTemplates ${BW_TEST_DEBUG} --xml xmlreports/report_templates_search.xml --html htmlreports/report_templates_search.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::ListlimitTemplates ${BW_TEST_DEBUG} --xml xmlreports/report_templates_listlimit.xml --html htmlreports/report_templates_listlimit.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::PaginationTemplates ${BW_TEST_DEBUG} --xml xmlreports/report_templates_pagination.xml --html htmlreports/report_templates_pagination.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::SetDefaultTemplates ${BW_TEST_DEBUG} --xml xmlreports/report_templates_set_default.xml --html htmlreports/report_templates_set_default.html
fi

##########################
# test component details #
##########################

###
# test campaign details
###

if [ "${BW_TEST_CAT}" == details_all ]
then
# all tests for campaigns
codecept run acceptance Backend/Details/TestCampaignsDetailsCest ${BW_TEST_DEBUG} --xml xmlreports/report_campaigns_details.xml --html htmlreports/report_campaigns_details.html
fi

if [ "${BW_TEST_CAT}" == details_cam ]
then
# single tests for campaigns
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignCancelMainView ${BW_TEST_DEBUG} --xml xmlreports/report_campaigns_cancel_main.xml --html htmlreports/report_campaigns_cancel_main.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignCompleteMainView ${BW_TEST_DEBUG} --xml xmlreports/report_campaigns_complete_main.xml --html htmlreports/report_campaigns_complete_main.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignCancelListView ${BW_TEST_DEBUG} --xml xmlreports/report_campaigns_cancel_list.xml --html htmlreports/report_campaigns_cancel_list.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignCompleteListView ${BW_TEST_DEBUG} --xml xmlreports/report_campaigns_complete_list.xml --html htmlreports/report_campaigns_complete_list.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignListViewRestore ${BW_TEST_DEBUG} --xml xmlreports/report_campaigns_restore_list.xml --html htmlreports/report_campaigns_restore_list.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateCampaignTwiceListView  ${BW_TEST_DEBUG} --xml xmlreports/report_campaigns_twice_list.xml --html htmlreports/report_campaigns_twice_list.html
fi

###
# test mailinglist details
###

if [ "${BW_TEST_CAT}" == details_all ]
then
# all tests for mailinglists
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest ${BW_TEST_DEBUG} --xml xmlreports/report_mailinglists_details.xml --html htmlreports/report_mailinglists_details.html
fi

if [ "${BW_TEST_CAT}" == details_ml ]
then
# single tests for mailinglists
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistCancelMainView ${BW_TEST_DEBUG} --xml xmlreports/report_mailinglists_cancel_main.xml --html htmlreports/report_mailinglists_cancel_main.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistCompleteMainView ${BW_TEST_DEBUG} --xml xmlreports/report_mailinglists_complete_main.xml --html htmlreports/report_mailinglists_complete_main.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistCancelListView ${BW_TEST_DEBUG} --xml xmlreports/report_mailinglists_cancel_list.xml --html htmlreports/report_mailinglists_cancel_list.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistCompleteListView ${BW_TEST_DEBUG} --xml xmlreports/report_mailinglists_complete_list.xml --html htmlreports/report_mailinglists_complete_list.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistListViewRestore ${BW_TEST_DEBUG} --xml xmlreports/report_mailinglists_restore_list.xml --html htmlreports/report_mailinglists_restore_list.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateMailinglistTwiceListView ${BW_TEST_DEBUG} --xml xmlreports/report_mailinglists_twice_list.xml --html htmlreports/report_mailinglists_twice_list.html
fi

###
# test newsletter details
###

if [ "${BW_TEST_CAT}" == details_all ]
then
# all tests for newsletters
codecept run acceptance Backend/Details/TestNewslettersDetailsCest ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_details.xml --html htmlreports/report_newsletters_details.html
fi

if [ "${BW_TEST_CAT}" == details_nl ]
then
# single tests for newsletters
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterCancelMainView ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_cancel_main.xml --html htmlreports/report_newsletters_cancel_main.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterCompleteMainView ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_complete_main.xml --html htmlreports/report_newsletters_complete_main.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterCancelListView ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_cancel_list.xml --html htmlreports/report_newsletters_cancel_list.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterCompleteListView ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_complete_list.xml --html htmlreports/report_newsletters_complete_list.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterListViewRestore ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_restore_list.xml --html htmlreports/report_newsletters_restore_list.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateNewsletterTwiceListView ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_twice_list.xml --html htmlreports/report_newsletters_twice_list.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CopyNewsletter ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_copy.xml --html htmlreports/report_newsletters_copy.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::SendNewsletterToTestrecipients ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_send_test.xml --html htmlreports/report_newsletters_send_test.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::SendNewsletterToRealRecipients ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_send_real.xml --html htmlreports/report_newsletters_send_real.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::SendNewsletterToRealUsergroup ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_send_usergroup.xml --html htmlreports/report_newsletters_send_usergroup.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::SendNewsletterToUnconfirmed ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_send_unconfirmed.xml --html htmlreports/report_newsletters_send_unconfirmed.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::EditSentNewsletter ${BW_TEST_DEBUG} --xml xmlreports/report_newsletters_edit_sent.xml --html htmlreports/report_newsletters_edit_sent.html
fi

###
# test subscriber details
###

if [ "${BW_TEST_CAT}" == details_all ]
then
# all tests for subscribers
codecept run acceptance Backend/Details/TestSubscribersDetailsCest ${BW_TEST_DEBUG} --xml xmlreports/report_subscribers_details.xml --html htmlreports/report_subscribers_details.html
fi

if [ "${BW_TEST_CAT}" == details_subs ]
then
# single tests for subscribers
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberCancelMainView ${BW_TEST_DEBUG} --xml xmlreports/report_subscribers_cancel_main.xml --html htmlreports/report_subscribers_cancel_main.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberCompleteMainView ${BW_TEST_DEBUG} --xml xmlreports/report_subscribers_complete_main.xml --html htmlreports/report_subscribers_complete_main.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberCancelListView ${BW_TEST_DEBUG} --xml xmlreports/report_subscribers_cancel_list.xml --html htmlreports/report_subscribers_cancel_list.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberCompleteListView ${BW_TEST_DEBUG} --xml xmlreports/report_subscribers_complete_list.xml --html htmlreports/report_subscribers_complete_list.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberListViewRestore ${BW_TEST_DEBUG} --xml xmlreports/report_subscribers_restore_list.xml --html htmlreports/report_subscribers_restore_list.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateSubscriberTwiceListView ${BW_TEST_DEBUG} --xml xmlreports/report_subscribers_twice_list.xml --html htmlreports/report_subscribers_twice_list.html
fi

###
# test template details
###

if [ "${BW_TEST_CAT}" == details_all ]
then
# all tests for templates
codecept run acceptance Backend/Details/TestTemplatesDetailsCest ${BW_TEST_DEBUG} --xml xmlreports/report_templates_details.xml --html htmlreports/report_templates_details.html
fi

if [ "${BW_TEST_CAT}" == details_tpl ]
then
# single tests for templates
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneHtmlTemplateCancelMainView ${BW_TEST_DEBUG} --xml xmlreports/report_templates_html_cancel_main.xml --html htmlreports/report_templates_html_cancel_main.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneHtmlTemplateCompleteMainView ${BW_TEST_DEBUG} --xml xmlreports/report_templates_html_complete_main.xml --html htmlreports/report_templates_html_complete_main.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneHtmlTemplateCancelListView ${BW_TEST_DEBUG} --xml xmlreports/report_templates_html_cancel_list.xml --html htmlreports/report_templates_html_cancel_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneHtmlTemplateListView ${BW_TEST_DEBUG} --xml xmlreports/report_templates_html_complete_list.xml --html htmlreports/report_templates_html_complete_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateHtmlTemplateTwiceListView ${BW_TEST_DEBUG} --xml xmlreports/report_templates_html_twice_list.xml --html htmlreports/report_templates_html_twice_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateCancelMainView ${BW_TEST_DEBUG} --xml xmlreports/report_templates_text_cancel_main.xml --html htmlreports/report_templates_text_cancel_main.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateCompleteMainView ${BW_TEST_DEBUG} --xml xmlreports/report_templates_text_complete_main.xml --html htmlreports/report_templates_text_complete_main.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateCancelListView ${BW_TEST_DEBUG} --xml xmlreports/report_templates_text_cancel_list.xml --html htmlreports/report_templates_text_cancel_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateCompleteListView ${BW_TEST_DEBUG} --xml xmlreports/report_templates_text_complete_list.xml --html htmlreports/report_templates_text_complete_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateRestoreListView ${BW_TEST_DEBUG} --xml xmlreports/report_templates_text_restore_list.xml --html htmlreports/report_templates_text_restore_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateTextTemplateTwiceListView ${BW_TEST_DEBUG} --xml xmlreports/report_templates_text_twice_list.xml --html htmlreports/report_templates_text_twice_list.html
fi

###########################
# test component frontend #
###########################

if [ "${BW_TEST_CAT}" == frontend_all ]
then
# all tests for frontend
codecept run acceptance Frontend ${BW_TEST_DEBUG} --xml xmlreports/report_frontend.xml --html htmlreports/report_frontend.html
fi

if [ "${BW_TEST_CAT}" == frontend_single ]
then
# single tests for frontend
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeSimpleActivateAndUnsubscribe ${BW_TEST_DEBUG} --xml xmlreports/report_frontend_activate_and_unsubscribe.xml --html htmlreports/report_frontend_activate_and_unsubscribe.html
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeTwiceActivateAndUnsubscribe ${BW_TEST_DEBUG} --xml xmlreports/report_frontend_activate_twice_and_unscubscribe.xml --html htmlreports/report_frontend_activate_twice_and_unscubscribe.html
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeTwiceActivateGetActivationAndUnsubscribe ${BW_TEST_DEBUG} --xml xmlreports/report_frontend_get_code_and_unsubscribe.xml --html htmlreports/report_frontend_get_code_and_unsubscribe.html
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeActivateSubscribeGetEditlinkAndUnsubscribe ${BW_TEST_DEBUG} --xml xmlreports/report_frontend_get_editlink_and_unsubscribe.xml --html htmlreports/report_frontend_get_editlink_and_unsubscribe.html
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeMissingValuesComponent ${BW_TEST_DEBUG} --xml xmlreports/report_frontend_missing_values.xml --html htmlreports/report_frontend_missing_values.html
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeSimpleActivateChangeAndUnsubscribe ${BW_TEST_DEBUG} --xml xmlreports/report_frontend_activate_change_and_unsubscribe.xml --html htmlreports/report_frontend_activate_change_and_unsubscribe.html
codecept run acceptance Frontend/SubscribeComponentCest::SubscribeActivateUnsubscribeAndActivate ${BW_TEST_DEBUG} --xml xmlreports/report_frontend_activate_unsubscribe_activate.xml --html htmlreports/report_frontend_activate_unsubscribe_activate.html
codecept run acceptance Frontend/SubscribeComponentCest::GetEditlinkWrongAddress ${BW_TEST_DEBUG} --xml xmlreports/report_frontend_get_editlink_wrong_address.xml --html htmlreports/report_frontend_get_editlink_wrong_address.html
codecept run acceptance Frontend/SubscribeComponentCest::WrongUnsubscribeLinks ${BW_TEST_DEBUG} --xml xmlreports/report_frontend_wrong_unsubscribe_link.xml --html htmlreports/report_frontend_wrong_unsubscribe_link.html
fi

##############################
# test component maintenance #
##############################

if [ "${BW_TEST_CAT}" == maintenance ]
then
# all tests for maintenance
codecept run acceptance Backend/TestMaintenanceCest ${BW_TEST_DEBUG} --xml xmlreports/report_maintenance.xml --html htmlreports/report_maintenance.html
fi

if [ "${BW_TEST_CAT}" == maintenance_single ]
then
# single tests for maintenance
codecept run acceptance Backend/TestMaintenanceCest::saveTables ${BW_TEST_DEBUG} --xml xmlreports/report_maintenance_save_tables.xml --html htmlreports/report_maintenance_save_tables.html
codecept run acceptance Backend/TestMaintenanceCest::checkTables ${BW_TEST_DEBUG} --xml xmlreports/report_maintenancecheck_tables.xml --html htmlreports/report_maintenance_check_tables.html
codecept run acceptance Backend/TestMaintenanceCest::restoreTables ${BW_TEST_DEBUG} --xml xmlreports/report_maintenance_restore_tables.xml --html htmlreports/report_maintenance_restore_tables.html
codecept run acceptance Backend/TestMaintenanceCest::testBasicSettings ${BW_TEST_DEBUG} --xml xmlreports/report_maintenance_basic_settings.xml --html htmlreports/report_maintenance_basic_settings.html
codecept run acceptance Backend/TestMaintenanceCest::testForumLink ${BW_TEST_DEBUG} --xml xmlreports/report_maintenance_forum_link.xml --html htmlreports/report_maintenance_forum_link.html
fi

###############################
# test plugin User2Subscriber #
###############################

if [ "${BW_TEST_CAT}" == user2subscriber_all ]
then
# all tests for plugin user2subscriber
codecept run acceptance User2Subscriber/User2SubscriberCest ${BW_TEST_DEBUG} --xml xmlreports/report_user2Subscriber.xml --html htmlreports/report_user2Subscriber.html
fi

if [ "${BW_TEST_CAT}" == user2subscriber_single ]
then
# single tests for plugin user2subscriber
codecept run acceptance User2Subscriber/User2SubscriberCest::setupUser2Subscriber ${BW_TEST_DEBUG} --xml xmlreports/report_user2Subscriber_activate.xml --html htmlreports/report_user2Subscriber_activate.html

codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithoutSubscription ${BW_TEST_DEBUG} --xml xmlreports/report_u2s_no_subscription.xml --html htmlreports/report_u2s_no_subscription.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionSwitchSubscriptionWithoutSubscription ${BW_TEST_DEBUG} --xml xmlreports/report_u2s_yes_no_subscription.xml --html htmlreports/report_u2s_yes_no_subscription.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithoutActivationExtended ${BW_TEST_DEBUG} --xml xmlreports/report_u2s_no_activation_ext.xml --html htmlreports/report_u2s_no_activation_ext.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithActivationByFrontend ${BW_TEST_DEBUG} --xml xmlreports/report_u2s_activation_FE.xml --html htmlreports/report_u2s_activation_FE.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithExistingSubscriptionSameList ${BW_TEST_DEBUG} --xml xmlreports/report_u2s_subs_same_list.xml --html htmlreports/report_u2s_subs_same_list.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithExistingSubscriptionDifferentList ${BW_TEST_DEBUG} --xml xmlreports/report_u2s_subs_diff_list.xml --html htmlreports/report_u2s_subs_diff_list.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithActivationByBackend ${BW_TEST_DEBUG} --xml xmlreports/report_u2s_activation_BE.xml --html htmlreports/report_u2s_activation_BE.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithTextFormat ${BW_TEST_DEBUG} --xml xmlreports/report_u2s_text_format.xml --html htmlreports/report_u2s_text_format.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithoutFormatSelectionHTML ${BW_TEST_DEBUG} --xml xmlreports/report_u2s_no_format_select_html.xml --html htmlreports/report_u2s_no_format_select_html.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithoutFormatSelectionText ${BW_TEST_DEBUG} --xml xmlreports/report_u2s_no_format_select_text.xml --html htmlreports/report_u2s_no_format_select_text.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithAnotherMailinglist ${BW_TEST_DEBUG} --xml xmlreports/report_u2s_another_mailinglist.xml --html htmlreports/report_u2s_another_mailinglist.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithTwoMailinglists ${BW_TEST_DEBUG} --xml xmlreports/report_u2s_two_mailinglists.xml --html htmlreports/report_u2s_two_mailinglists.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithoutMailinglists ${BW_TEST_DEBUG} --xml xmlreports/report_u2s_no_mailinglists.xml --html htmlreports/report_u2s_no_mailinglists.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithMailChangeYes ${BW_TEST_DEBUG} --xml xmlreports/report_u2s_with_mail_change.xml --html htmlreports/report_u2s_with_mail_change.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithoutActivationWithMailChangeYes ${BW_TEST_DEBUG} --xml xmlreports/report_u2s_.xml --html htmlreports/report_u2s_.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithMailChangeNo ${BW_TEST_DEBUG} --xml xmlreports/report_u2s_no_activation_mail_change.xml --html htmlreports/report_u2s_no_activation_mail_change.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithDeleteNo ${BW_TEST_DEBUG} --xml xmlreports/report_u2s_delete_no.xml --html htmlreports/report_u2s_delete_no.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberOptionsPluginDeactivated ${BW_TEST_DEBUG} --xml xmlreports/report_u2s_plugin_deactivated.xml --html htmlreports/report_u2s_plugin_deactivated.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberOptionsMessage ${BW_TEST_DEBUG} --xml xmlreports/report_u2s_options_message.xml --html htmlreports/report_u2s_options_message.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberOptionsSwitchShowFormat ${BW_TEST_DEBUG} --xml xmlreports/report_u2s_switch_show_format.xml --html htmlreports/report_u2s_switch_show_format.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberPredefinedFormat ${BW_TEST_DEBUG} --xml xmlreports/report_u2s_predefined_format.xml --html htmlreports/report_u2s_predefined_format.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberOptionsAutoUpdate ${BW_TEST_DEBUG} --xml xmlreports/report_u2s_auto_update.xml --html htmlreports/report_u2s_auto_update.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberOptionsAutoDelete ${BW_TEST_DEBUG} --xml xmlreports/report_u2s_auto_delete.xml --html htmlreports/report_u2s_auto_delete.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberOptionsMailinglists ${BW_TEST_DEBUG} --xml xmlreports/report_u2s_options_mailinglists.xml --html htmlreports/report_u2s_options_mailinglists.html
fi

if [ "${BW_TEST_CAT}" == all ]
then
# run all tests
codecept run acceptance Backend/Lists ${BW_TEST_DEBUG} --xml xmlreports/report_lists.xml --html htmlreports/report_lists.html
codecept run acceptance Backend/Details ${BW_TEST_DEBUG} --xml xmlreports/report_details.xml --html htmlreports/report_details.html
codecept run acceptance Frontend ${BW_TEST_DEBUG} --xml xmlreports/report_frontend.xml --html htmlreports/report_frontend.html
codecept run acceptance Backend/TestMaintenanceCest ${BW_TEST_DEBUG} --xml xmlreports/report_maintenance.xml --html htmlreports/report_maintenance.html
codecept run acceptance User2Subscriber ${BW_TEST_DEBUG} --xml xmlreports/report_user2Subscriber.xml --html htmlreports/report_user2Subscriber.html
fi
