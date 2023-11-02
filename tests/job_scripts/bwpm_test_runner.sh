#!/bin/bash
### Tests with ## at the beginning are both commented out and faulty

# export sudo user
export BW_TESTER_USER="user1"
export BWPM_VERSION_TO_TEST="${BW_TEST_BWPM_VERSION}"

BW_TEST_DEBUG='--debug'
#BW_TEST_DEBUG=''

export BW_NEW_TEST_RUN=true

codecept build

##########################
# test component backend #
##########################

######
# test lists
######

###
# test campaigns lists
###

if [[ "${BW_TEST_CAT}" == lists_all ]]
then
# all tests for campaigns
codecept run acceptance Backend/Lists/TestCampaignsListsCest  "${BW_TEST_DEBUG}" --xml xmlreports/report_campaigns_lists.xml --html htmlreports/report_campaigns_lists.html
fi

if [[ "${BW_TEST_CAT}" == lists_cam ]]
then
# single tests for campaigns
codecept run acceptance Backend/Lists/TestCampaignsListsCest::SortCampaignsByTableHeader "${BW_TEST_DEBUG}" --xml xmlreports/report_campaigns_sort_by_tableheader.xml --html htmlreports/report_campaigns_report_campaigns_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestCampaignsListsCest::SortCampaignsBySelectList "${BW_TEST_DEBUG}" --xml xmlreports/report_campaigns_report_campaigns_sort_by_select.xml --html htmlreports/report_campaigns_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestCampaignsListsCest::SearchCampaigns "${BW_TEST_DEBUG}" --xml xmlreports/report_campaigns_search.xml --html htmlreports/report_campaigns_search.html
codecept run acceptance Backend/Lists/TestCampaignsListsCest::ListlimitCampaigns "${BW_TEST_DEBUG}" --xml xmlreports/report_campaigns_listlimit.xml --html htmlreports/report_campaigns_listlimit.html
codecept run acceptance Backend/Lists/TestCampaignsListsCest::PaginationCampaigns "${BW_TEST_DEBUG}" --xml xmlreports/report_campaigns_pagination.xml --html htmlreports/report_campaigns_pagination.html
fi

###
# test mailinglist lists
###

if [[ "${BW_TEST_CAT}" == lists_all ]]
then
# all tests for mailinglists
codecept run acceptance Backend/Lists/TestMailinglistsListsCest  "${BW_TEST_DEBUG}" --xml xmlreports/report_mailinglists_lists.xml --html htmlreports/report_mailinglists_lists.html
fi

if [[ "${BW_TEST_CAT}" == lists_ml ]]
then
# single tests for mailinglists
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::PublishMailinglistsByIcon "${BW_TEST_DEBUG}" --xml xmlreports/report_mailinglists_publish_by_icon.xml --html htmlreports/report_mailinglists_publish_by_icon.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::PublishMailinglistsByToolbar "${BW_TEST_DEBUG}" --xml xmlreports/report_mailinglists_publish_by_toolbar.xml --html htmlreports/report_mailinglists_publish_by_toolbar.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::SortMailinglistsByTableHeader "${BW_TEST_DEBUG}" --xml xmlreports/report_mailinglists_sort_by_tableheader.xml --html htmlreports/report_mailinglists_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::SortMailinglistsBySelectList "${BW_TEST_DEBUG}" --xml xmlreports/report_mailinglists_sort_by_selectlist.xml --html htmlreports/report_mailinglists_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::FilterMailinglistsByStatus "${BW_TEST_DEBUG}" --xml xmlreports/report_mailinglists_filter_by_status.xml --html htmlreports/report_mailinglists_filter_by_status.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::FilterMailinglistsByAccess "${BW_TEST_DEBUG}" --xml xmlreports/report_mailinglists_filter_by_access.xml --html htmlreports/report_mailinglists_filter_by_access.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::SearchMailinglists "${BW_TEST_DEBUG}" --xml xmlreports/report_mailinglists_search.xml --html htmlreports/report_mailinglists_search.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::ListlimitMailinglists "${BW_TEST_DEBUG}" --xml xmlreports/report_mailinglists_listlimit.xml --html htmlreports/report_mailinglists_listlimit.html
codecept run acceptance Backend/Lists/TestMailinglistsListsCest::PaginationMailinglists "${BW_TEST_DEBUG}" --xml xmlreports/report_mailinglists_pagination.xml --html htmlreports/report_mailinglists_pagination.html
fi

###
# test newsletter lists
###

if [[ "${BW_TEST_CAT}" == lists_all ]]
then
# all tests for newsletters
codecept run acceptance Backend/Lists/TestNewslettersListsCest  "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_lists.xml --html htmlreports/report_newsletters_lists.html
fi

if [[ "${BW_TEST_CAT}" == lists_nl ]]
then
# single tests for newsletters
codecept run acceptance Backend/Lists/TestNewslettersListsCest::SortNewslettersByTableHeader  "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_sort_by_tableheader.xml --html htmlreports/report_newsletters_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::SortNewslettersBySelectList  "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_sort_by_selectlist.xml --html htmlreports/report_newsletters_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::SetNewsletterIsTemplate  "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_set_is_template.xml --html htmlreports/report_newsletters_set_is_template.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::FilterNewslettersByAuthor  "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_filter_by_author.xml --html htmlreports/report_newsletters_filter_by_author.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::FilterNewslettersByCampaign  "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_filter_by_campaign.xml --html htmlreports/report_newsletters_filter_by_campaign.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::FilterNewslettersByIsTemplate "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_filter_by_is_template.xml --html htmlreports/report_newsletters_filter_by_is_template.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::SearchNewsletters  "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_seearch.xml --html htmlreports/report_newsletters_seearch.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::ListlimitNewsletters  "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_listlimit.xml --html htmlreports/report_newsletters_listlimit.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::PaginationNewsletters  "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_pagination.xml --html htmlreports/report_newsletters_pagination.html

codecept run acceptance Backend/Lists/TestNewslettersListsCest::SortSentNewslettersByTableHeader  "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_sort_sent_by_tableheader.xml --html htmlreports/report_newsletters_sort_sent_by_tableheader.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::SortSentNewslettersBySelectList  "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_report_newsletters_sort_sent_by_selectlist.xml --html htmlreports/report_newsletters_sort_sent_by_selectlist.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::FilterSentNewslettersByAuthor  "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_filter_sent_by_author.xml --html htmlreports/report_newsletters_filter_sent_by_author.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::FilterSentNewslettersByCampaign  "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_filter_sent_by_campaign.xml --html htmlreports/report_newsletters_filter_sent_by_campaign.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::FilterSentNewslettersByStatus  "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_filter_sent_by_status.xml --html htmlreports/report_newsletters_filter_sent_by_status.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::SearchSentNewsletters  "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_sent_search.xml --html htmlreports/report_newsletters_sent_search.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::ListlimitSentNewsletters  "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_sent_listlimit.xml --html htmlreports/report_newsletters_sent_listlimit.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::PaginationSentNewsletters  "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_sent_pagination.xml --html htmlreports/report_newsletters_sent_pagination.html

codecept run acceptance Backend/Lists/TestNewslettersListsCest::ResetSendingTrialsAndSendAnewQueue  "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_queue_send_anew.xml --html htmlreports/report_newsletters_queue_send_anew.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::ListlimitQueue  "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_queue_listlimit.xml --html htmlreports/report_newsletters_queue_listlimit.html
codecept run acceptance Backend/Lists/TestNewslettersListsCest::PaginationQueue  "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_queue_pagination.xml --html htmlreports/report_newsletters_queue_pagination.html
fi

###
# test subscriber lists
###

if [[ "${BW_TEST_CAT}" == lists_all ]]
then
# all tests for subscribers
codecept run acceptance Backend/Lists/TestSubscribersListsCest  "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_lists.xml --html htmlreports/report_subscribers_lists.html
fi

if [[ "${BW_TEST_CAT}" == lists_subs ]]
then
# single tests for subscribers
codecept run acceptance Backend/Lists/TestSubscribersListsCest::SortSubscribersByTableHeader  "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_sort_by_tableheader.xml --html htmlreports/report_subscribers_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::SortSubscribersBySelectList  "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_sort_by_selectlist.xml --html htmlreports/report_subscribers_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::FilterSubscribersByMailformat  "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_filter_by_mailformat.xml --html htmlreports/report_subscribers_filter_by_mailformat.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::FilterSubscribersByMailinglist  "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_filter_by_mailinglist.xml --html htmlreports/report_subscribers_filter_by_mailinglist.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::SearchSubscribers  "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_search.xml --html htmlreports/report_subscribers_search.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::ListlimitSubscribers  "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_listlimit.xml --html htmlreports/report_subscribers_listlimit.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::PaginationSubscribers  "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_pagination.xml --html htmlreports/report_subscribers_pagination.html

codecept run acceptance Backend/Lists/TestSubscribersListsCest::SortUnconfirmedSubscribersByTableHeader  "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_unconfirmed_sort_by_tableheader.xml --html htmlreports/report_subscribers_unconfirmed_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::SortUnconfirmedSubscribersBySelectList  "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_unconfirmed_sort_by_selectlist.xml --html htmlreports/report_subscribers_unconfirmed_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::FilterUnconfirmedSubscribersByMailformat  "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_unconfirmed_filter_by_mailformat.xml --html htmlreports/report_subscribers_unconfirmed_filter_by_mailformat.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::FilterUnconfirmedSubscribersByMailinglist  "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_unconfirmed_filter_by_mailinglist.xml --html htmlreports/report_subscribers_unconfirmed_filter_by_mailinglist.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::SearchUnconfirmedSubscribers  "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_unconfirmed_search.xml --html htmlreports/report_subscribers_unconfirmed_search.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::ListlimitUnconfirmedSubscribers  "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_unconfirmed_listlimit.xml --html htmlreports/report_subscribers_unconfirmed_listlimit.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::PaginationUnconfirmedSubscribers  "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_unconfirmed_pagination.xml --html htmlreports/report_subscribers_unconfirmed_pagination.html

codecept run acceptance Backend/Lists/TestSubscribersListsCest::ImportSubscribersByCSV "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_import_csv.xml --html htmlreports/report_subscribers_import_csv.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::ImportSubscribersByXML "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_import_xml.xml --html htmlreports/report_subscribers_import_xml.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::ExportSubscribersToCSVCA "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_export_csv_ca.xml --html htmlreports/report_subscribers_export_csv_ca.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::ExportSubscribersToCSVUA "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_export_csv_ua.xml --html htmlreports/report_subscribers_export_csv_ua.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::ExportSubscribersToCSVAll "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_export_csv_all.xml --html htmlreports/report_subscribers_export_csv_all.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::ExportSubscribersToCSVFilteredYes "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_export_csv_filtered_yes.xml --html htmlreports/report_subscribers_export_csv_filtered_yes.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::ExportSubscribersToCSVFilteredNo "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_export_csv_filtered_no.xml --html htmlreports/report_subscribers_export_csv_filtered_no.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::ExportSubscribersToXML "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_export_xml.xml --html htmlreports/report_subscribers_export_xml.html

codecept run acceptance Backend/Lists/TestSubscribersListsCest::BatchSubscribeUnsubscribeOkay "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_batch_subscribe_okay_xml.xml --html htmlreports/report_subscribers_batch_subscribe_okay_xml.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::BatchSubscribeUnsubscribeAlready "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_batch_subscribe_already_xml.xml --html htmlreports/report_subscribers_batch_subscribe_already_xml.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::BatchSubscribeUnsubscribeNo "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_batch_subscribe_no_xml.xml --html htmlreports/report_subscribers_batch_subscribe_no_xml.html
codecept run acceptance Backend/Lists/TestSubscribersListsCest::BatchMove "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_batch_move_xml.xml --html htmlreports/report_subscribers_batch_move_xml.html
fi

###
# test template lists
###

if [[ "${BW_TEST_CAT}" == lists_all ]]
then
# all tests for templates
codecept run acceptance Backend/Lists/TestTemplatesListsCest  "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_lists.xml --html htmlreports/report_templates_lists.html
fi

if [[ "${BW_TEST_CAT}" == lists_tpl ]]
then
# single tests for templates
codecept run acceptance Backend/Lists/TestTemplatesListsCest::PublishTemplatesByIcon "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_publish_by_icon.xml --html htmlreports/report_templates_publish_by_icon.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::PublishTemplatesByToolbar "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_publish_by_toolbar.xml --html htmlreports/report_templates_publish_by_toolbar.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::SortTemplatesByTableHeader "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_sort_by_tableheader.xml --html htmlreports/report_templates_sort_by_tableheader.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::SortTemplatesBySelectList "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_sort_by_selectlist.xml --html htmlreports/report_templates_sort_by_selectlist.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::FilterTemplatesByStatus "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_filter_by_status.xml --html htmlreports/report_templates_filter_by_status.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::FilterTemplatesByMailformat "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_filter_by_mailformat.xml --html htmlreports/report_templates_filter_by_mailformat.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::SearchTemplates "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_search.xml --html htmlreports/report_templates_search.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::ListlimitTemplates "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_listlimit.xml --html htmlreports/report_templates_listlimit.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::PaginationTemplates "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_pagination.xml --html htmlreports/report_templates_pagination.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::SetDefaultTemplates "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_set_default.xml --html htmlreports/report_templates_set_default.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::ImportTemplates "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_import.xml --html htmlreports/report_templates_import.html
codecept run acceptance Backend/Lists/TestTemplatesListsCest::ExportTemplates "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_export.xml --html htmlreports/report_templates_export.html
fi

##########################
# test component details #
##########################

###
# test campaign details
###

if [[ "${BW_TEST_CAT}" == details_all ]]
then
# all tests for campaigns
codecept run acceptance Backend/Details/TestCampaignsDetailsCest "${BW_TEST_DEBUG}" --xml xmlreports/report_campaigns_details.xml --html htmlreports/report_campaigns_details.html
fi

if [[ "${BW_TEST_CAT}" == details_cam ]]
then
# single tests for campaigns
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignCancelMainView "${BW_TEST_DEBUG}" --xml xmlreports/report_campaigns_cancel_main.xml --html htmlreports/report_campaigns_cancel_main.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignCompleteMainView "${BW_TEST_DEBUG}" --xml xmlreports/report_campaigns_complete_main.xml --html htmlreports/report_campaigns_complete_main.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignCancelListView "${BW_TEST_DEBUG}" --xml xmlreports/report_campaigns_cancel_list.xml --html htmlreports/report_campaigns_cancel_list.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignCompleteListView "${BW_TEST_DEBUG}" --xml xmlreports/report_campaigns_complete_list.xml --html htmlreports/report_campaigns_complete_list.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignSaveNewMainView "${BW_TEST_DEBUG}" --xml xmlreports/report_campaigns_save_new_list.xml --html htmlreports/report_campaigns_save_new_list.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignSaveCopyMainView "${BW_TEST_DEBUG}" --xml xmlreports/report_campaigns_save_copy_list.xml --html htmlreports/report_campaigns_save_copy_list.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateOneCampaignListViewRestore "${BW_TEST_DEBUG}" --xml xmlreports/report_campaigns_restore_list.xml --html htmlreports/report_campaigns_restore_list.html
codecept run acceptance Backend/Details/TestCampaignsDetailsCest::CreateCampaignTwiceListView  "${BW_TEST_DEBUG}" --xml xmlreports/report_campaigns_twice_list.xml --html htmlreports/report_campaigns_twice_list.html
fi

###
# test mailinglist details
###

if [[ "${BW_TEST_CAT}" == details_all ]]
then
# all tests for mailinglists
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest "${BW_TEST_DEBUG}" --xml xmlreports/report_mailinglists_details.xml --html htmlreports/report_mailinglists_details.html
fi

if [[ "${BW_TEST_CAT}" == details_ml ]]
then
# single tests for mailinglists
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistCancelMainView "${BW_TEST_DEBUG}" --xml xmlreports/report_mailinglists_cancel_main.xml --html htmlreports/report_mailinglists_cancel_main.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistCompleteMainView "${BW_TEST_DEBUG}" --xml xmlreports/report_mailinglists_complete_main.xml --html htmlreports/report_mailinglists_complete_main.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistCancelListView "${BW_TEST_DEBUG}" --xml xmlreports/report_mailinglists_cancel_list.xml --html htmlreports/report_mailinglists_cancel_list.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistCompleteListView "${BW_TEST_DEBUG}" --xml xmlreports/report_mailinglists_complete_list.xml --html htmlreports/report_mailinglists_complete_list.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistSaveNewListView "${BW_TEST_DEBUG}" --xml xmlreports/report_mailinglists_save_new_list.xml --html htmlreports/report_mailinglists_save_new_list.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistSaveCopyListView "${BW_TEST_DEBUG}" --xml xmlreports/report_mailinglists_save_copy_list.xml --html htmlreports/report_mailinglists_save_copy_list.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateOneMailinglistListViewRestore "${BW_TEST_DEBUG}" --xml xmlreports/report_mailinglists_restore_list.xml --html htmlreports/report_mailinglists_restore_list.html
codecept run acceptance Backend/Details/TestMailinglistsDetailsCest::CreateMailinglistTwiceListView "${BW_TEST_DEBUG}" --xml xmlreports/report_mailinglists_twice_list.xml --html htmlreports/report_mailinglists_twice_list.html
fi

###
# test newsletter details
###

if [[ "${BW_TEST_CAT}" == details_all ]]
then
# all tests for newsletters
codecept run acceptance Backend/Details/TestNewslettersDetailsCest "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_details.xml --html htmlreports/report_newsletters_details.html
fi

if [[ "${BW_TEST_CAT}" == details_nl ]]
then
# single tests for newsletters
#codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterCancelMainView "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_cancel_main.xml --html htmlreports/report_newsletters_cancel_main.html
#codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterCompleteMainView "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_complete_main.xml --html htmlreports/report_newsletters_complete_main.html
#codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterCancelListView "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_cancel_list.xml --html htmlreports/report_newsletters_cancel_list.html
#codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterCompleteListViewDefault "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_complete_list.xml --html htmlreports/report_newsletters_complete_list.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterCompleteListViewCustomfield "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_complete_list_Customfield.xml --html htmlreports/report_newsletters_complete_list_Customfield.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterCompleteListViewTemplate "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_complete_template_list.xml --html htmlreports/report_newsletters_complete_template_list.html
#codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterSaveNewListView "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_save_new_list.xml --html htmlreports/report_newsletters_save_new_list.html
#codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterSaveCopyListViewDefault "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_save_copy_list.xml --html htmlreports/report_newsletters_save_copy_list.html
codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterSaveCopyListViewTemplate "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_save_copy_template_list.xml --html htmlreports/report_newsletters_save_copy_template_list.html
#codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterWithFileUpload "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_file_upload.xml --html htmlreports/report_newsletters_file_upload.html
#codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateOneNewsletterListViewRestore "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_restore_list.xml --html htmlreports/report_newsletters_restore_list.html
#codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CreateNewsletterTwiceListView "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_twice_list.xml --html htmlreports/report_newsletters_twice_list.html
#codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CopyNewsletterOnly "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_copy.xml --html htmlreports/report_newsletters_copy.html
#codecept run acceptance Backend/Details/TestNewslettersDetailsCest::CopyNewsletterTemplate "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_copy_template.xml --html htmlreports/report_newsletters_copy_template.html

#codecept run acceptance Backend/Details/TestNewslettersDetailsCest::SendNewsletterToTestrecipients "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_send_test.xml --html htmlreports/report_newsletters_send_test.html
#codecept run acceptance Backend/Details/TestNewslettersDetailsCest::SendNewsletterToRealRecipientsPublishOptionNo "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_send_real_publish_option_no.xml --html htmlreports/report_newsletters_send_real_publish_option_no.html
#codecept run acceptance Backend/Details/TestNewslettersDetailsCest::SendNewsletterToRealRecipientsPublishOptionYes "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_send_real_publish_option_yes.xml --html htmlreports/report_newsletters_send_real_publish_option_yes.html
#codecept run acceptance Backend/Details/TestNewslettersDetailsCest::SendPublishNewsletterToRealRecipientsPublishOptionNo "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_send_publish_real_publish_option_no.xml --html htmlreports/report_newsletters_send_publish_real_publish_option_no.html
#codecept run acceptance Backend/Details/TestNewslettersDetailsCest::SendPublishNewsletterToRealRecipientsPublishOptionYes "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_send_publish_real_publish_option_yes.xml --html htmlreports/report_newsletters_send_publish_real_publish_option_yes.html
#codecept run acceptance Backend/Details/TestNewslettersDetailsCest::SendNewsletterToUnconfirmed "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_send_unconfirmed.xml --html htmlreports/report_newsletters_send_unconfirmed.html
#codecept run acceptance Backend/Details/TestNewslettersDetailsCest::SendNewsletterToRealUsergroup "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_send_usergroup.xml --html htmlreports/report_newsletters_send_usergroup.html
#codecept run acceptance Backend/Details/TestNewslettersDetailsCest::SendNewsletterIsTemplate "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_send_is_template.xml --html htmlreports/report_newsletters_send_is_template.html
#codecept run acceptance Backend/Details/TestNewslettersDetailsCest::EditSentNewsletter "${BW_TEST_DEBUG}" --xml xmlreports/report_newsletters_edit_sent.xml --html htmlreports/report_newsletters_edit_sent.html
fi

###
# test subscriber details
###

if [[ "${BW_TEST_CAT}" == details_all ]]
then
# all tests for subscribers
codecept run acceptance Backend/Details/TestSubscribersDetailsCest "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_details.xml --html htmlreports/report_subscribers_details.html
fi

if [[ "${BW_TEST_CAT}" == details_subs ]]
then
# single tests for regular subscribers
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberCancelMainView "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_cancel_main.xml --html htmlreports/report_subscribers_cancel_main.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberCompleteMainView "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_complete_main.xml --html htmlreports/report_subscribers_complete_main.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberCancelListView "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_cancel_list.xml --html htmlreports/report_subscribers_cancel_list.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberCompleteListView "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_complete_list.xml --html htmlreports/report_subscribers_complete_list.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberSaveNewListView "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_save_new_list.xml --html htmlreports/report_subscribers_save_new_list.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberSaveCopyListView "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_save_copy_list.xml --html htmlreports/report_subscribers_save_copy_list.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberListViewRestore "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_restore_list.xml --html htmlreports/report_subscribers_restore_list.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberUnactivatedCompleteListView "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_unactivated_list.xml --html htmlreports/report_subscribers_unactivated_list.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateSubscriberTwiceListView "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_twice_list.xml --html htmlreports/report_subscribers_twice_list.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::TestSubscriberPrintDataButton "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_print_data_button.xml --html htmlreports/report_subscribers_print_data_button.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneSubscriberAbuseListView "${BW_TEST_DEBUG}" --xml xmlreports/report_subscribers_abuse_fields.xml --html htmlreports/report_subscribers_abuse_fields.html

# single tests for test recipients
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneTesterCancelMainView "${BW_TEST_DEBUG}" --xml xmlreports/report_tester_cancel_main.xml --html htmlreports/report_tester_cancel_main.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateOneTesterCancelListView "${BW_TEST_DEBUG}" --xml xmlreports/report_tester_cancel_list.xml --html htmlreports/report_tester_cancel_list.html
codecept run acceptance Backend/Details/TestSubscribersDetailsCest::CreateTestersListView "${BW_TEST_DEBUG}" --xml xmlreports/report_testers_list.xml --html htmlreports/report_testers_list.html
fi

###
# test template details
###

if [[ "${BW_TEST_CAT}" == details_all ]]
then
# all tests for templates
codecept run acceptance Backend/Details/TestTemplatesDetailsCest "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_details.xml --html htmlreports/report_templates_details.html
fi

if [[ "${BW_TEST_CAT}" == details_tpl ]]
then
# single tests for templates
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneHtmlTemplateCancelMainView "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_html_cancel_main.xml --html htmlreports/report_templates_html_cancel_main.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneHtmlTemplateCompleteMainView "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_html_complete_main.xml --html htmlreports/report_templates_html_complete_main.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneHtmlTemplateCancelListView "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_html_cancel_list.xml --html htmlreports/report_templates_html_cancel_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneHtmlTemplateListView "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_html_complete_list.xml --html htmlreports/report_templates_html_complete_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneHtmlTemplateSaveNewListView "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_html_save_new_list.xml --html htmlreports/report_templates_html_save_new_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneHtmlTemplateSaveCopyListView "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_html_save_copy_list.xml --html htmlreports/report_templates_html_save_copy_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateHtmlTemplateTwiceListView "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_html_twice_list.xml --html htmlreports/report_templates_html_twice_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateCancelMainView "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_text_cancel_main.xml --html htmlreports/report_templates_text_cancel_main.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateCompleteMainView "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_text_complete_main.xml --html htmlreports/report_templates_text_complete_main.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateCancelListView "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_text_cancel_list.xml --html htmlreports/report_templates_text_cancel_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateCompleteListView "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_text_complete_list.xml --html htmlreports/report_templates_text_complete_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateSaveNewListView "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_text_save_new_list.xml --html htmlreports/report_templates_text_save_new_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateSaveCopyListView "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_text_save_copy_list.xml --html htmlreports/report_templates_text_save_copy_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateOneTextTemplateRestoreListView "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_text_restore_list.xml --html htmlreports/report_templates_text_restore_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::CreateTextTemplateTwiceListView "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_text_twice_list.xml --html htmlreports/report_templates_text_twice_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::DefaultTemplateSaveNewListView "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_default_save_new_list.xml --html htmlreports/report_templates_default_save_new_list.html
codecept run acceptance Backend/Details/TestTemplatesDetailsCest::DefaultTemplateSaveCopyListView "${BW_TEST_DEBUG}" --xml xmlreports/report_templates_default_save_copy_list.xml --html htmlreports/report_templates_default_save_copy_list.html
fi

###########################
# test component frontend #
###########################

if [[ "${BW_TEST_CAT}" == frontend_all ]]
then
# all tests for frontend
codecept run acceptance Frontend "${BW_TEST_DEBUG}" --xml xmlreports/report_frontend.xml --html htmlreports/report_frontend.html
fi

if [[ "${BW_TEST_CAT}" == frontend_single ]]
then
# single tests for frontend
#codecept run acceptance Frontend/SubscribeComponentCest::SubscribeSimpleActivateAndUnsubscribe "${BW_TEST_DEBUG}" --xml xmlreports/report_frontend_activate_and_unsubscribe.xml --html htmlreports/report_frontend_activate_and_unsubscribe.html
#codecept run acceptance Frontend/SubscribeComponentCest::SubscribeSimpleActivateAndUnsubscribeLoggedIn "${BW_TEST_DEBUG}" --xml xmlreports/report_frontend_activate_and_unsubscribe_logged_in.xml --html htmlreports/report_frontend_activate_and_unsubscribe_logged_in.html
#codecept run acceptance Frontend/SubscribeComponentCest::SubscribeTwiceActivateAndUnsubscribe "${BW_TEST_DEBUG}" --xml xmlreports/report_frontend_activate_twice_and_unscubscribe.xml --html htmlreports/report_frontend_activate_twice_and_unscubscribe.html
#codecept run acceptance Frontend/SubscribeComponentCest::SubscribeTwiceActivateGetActivationAndUnsubscribe "${BW_TEST_DEBUG}" --xml xmlreports/report_frontend_get_code_and_unsubscribe.xml --html htmlreports/report_frontend_get_code_and_unsubscribe.html
#codecept run acceptance Frontend/SubscribeComponentCest::SubscribeActivateSubscribeGetEditlinkAndUnsubscribe "${BW_TEST_DEBUG}" --xml xmlreports/report_frontend_get_editlink_and_unsubscribe.xml --html htmlreports/report_frontend_get_editlink_and_unsubscribe.html
#codecept run acceptance Frontend/SubscribeComponentCest::SubscribeMissingValuesComponent "${BW_TEST_DEBUG}" --xml xmlreports/report_frontend_missing_values.xml --html htmlreports/report_frontend_missing_values.html
#codecept run acceptance Frontend/SubscribeComponentCest::SubscribeSimpleActivateChangeAndUnsubscribe "${BW_TEST_DEBUG}" --xml xmlreports/report_frontend_activate_change_and_unsubscribe.xml --html htmlreports/report_frontend_activate_change_and_unsubscribe.html
#codecept run acceptance Frontend/SubscribeComponentCest::SubscribeActivateUnsubscribeAndActivate "${BW_TEST_DEBUG}" --xml xmlreports/report_frontend_activate_unsubscribe_activate.xml --html htmlreports/report_frontend_activate_unsubscribe_activate.html
#codecept run acceptance Frontend/SubscribeComponentCest::GetEditlinkWrongAddress "${BW_TEST_DEBUG}" --xml xmlreports/report_frontend_get_editlink_wrong_address.xml --html htmlreports/report_frontend_get_editlink_wrong_address.html
#codecept run acceptance Frontend/SubscribeComponentCest::WrongUnsubscribeLinks "${BW_TEST_DEBUG}" --xml xmlreports/report_frontend_wrong_unsubscribe_link.xml --html htmlreports/report_frontend_wrong_unsubscribe_link.html
#codecept run acceptance Frontend/SubscribeComponentCest::SubscribeAbuseFields "${BW_TEST_DEBUG}" --xml xmlreports/report_frontend_abuse_fields.xml --html htmlreports/report_frontend_abuse_fields.html
#codecept run acceptance Frontend/SubscribeComponentCest::SubscribeUnreachableMailAddress "${BW_TEST_DEBUG}" --xml xmlreports/report_frontend_unreachable_mail.xml --html htmlreports/report_frontend_unreachable_mail.html
#
#codecept run acceptance Frontend/SubscribeComponentCest::SubscribeShowFieldsComponent "${BW_TEST_DEBUG}" --xml xmlreports/report_frontend_show_fields.xml --html htmlreports/report_frontend_show_fields.html
#codecept run acceptance Frontend/SubscribeComponentCest::CheckMailinglistDescriptionComponent "${BW_TEST_DEBUG}" --xml xmlreports/report_frontend_show_desc.xml --html htmlreports/report_frontend_show_desc.html
#codecept run acceptance Frontend/SubscribeComponentCest::CheckIntroTextComponent "${BW_TEST_DEBUG}" --xml xmlreports/report_frontend_check_intro.xml --html htmlreports/report_frontend_check_intro.html
codecept run acceptance Frontend/SubscribeComponentCest::CheckDisclaimerContentPopupComponent "${BW_TEST_DEBUG}" --xml xmlreports/report_frontend_check_disclaimer_selection_popup.xml --html htmlreports/report_frontend_check_disclaimer_selection_popup.html
#codecept run acceptance Frontend/SubscribeComponentCest::CheckDisclaimerContentNewWindowComponent "${BW_TEST_DEBUG}" --xml xmlreports/report_frontend_check_disclaimer_selection_new.xml --html htmlreports/report_frontend_check_disclaimer_selection_new.html
#codecept run acceptance Frontend/SubscribeComponentCest::CheckDisclaimerContentSameWindowComponent "${BW_TEST_DEBUG}" --xml xmlreports/report_frontend_check_disclaimer_selection_same.xml --html htmlreports/report_frontend_check_disclaimer_selection_same.html
#codecept run acceptance Frontend/SubscribeComponentCest::CheckSecurityQuestionComponent "${BW_TEST_DEBUG}" --xml xmlreports/report_frontend_check_security_question.xml --html htmlreports/report_frontend_check_security_question.html

# !!!Following test don't work automated because of some technical reasons!!!
########codecept run acceptance Frontend/SubscribeComponentCest::SubscribeActivationNoSenderData "${BW_TEST_DEBUG}" --xml xmlreports/report_frontend_no_activation_mail.xml --html htmlreports/report_frontend_no_activation_mail.html
fi

##############################
# test component maintenance #
##############################

if [[ "${BW_TEST_CAT}" == maintenance ]]
then
# all tests for maintenance
codecept run acceptance Backend/TestMaintenanceCest "${BW_TEST_DEBUG}" --xml xmlreports/report_maintenance.xml --html htmlreports/report_maintenance.html
fi

if [[ "${BW_TEST_CAT}" ==  maintenance_single ]]
then
# single tests for maintenance
codecept run acceptance Backend/TestMaintenanceCest::saveTablesZip "${BW_TEST_DEBUG}" --xml xmlreports/report_maintenance_save_tables_zip.xml --html htmlreports/report_maintenance_save_tables_zip.html
codecept run acceptance Backend/TestMaintenanceCest::saveTablesNoZip "${BW_TEST_DEBUG}" --xml xmlreports/report_maintenance_save_tables.xml --html htmlreports/report_maintenance_save_tables.html
codecept run acceptance Backend/TestMaintenanceCest::checkTables "${BW_TEST_DEBUG}" --xml xmlreports/report_maintenance_check_tables.xml --html htmlreports/report_maintenance_check_tables.html
codecept run acceptance Backend/TestMaintenanceCest::restoreTablesWithErrors "${BW_TEST_DEBUG}" --xml xmlreports/report_maintenance_restore_tables_errors.xml --html htmlreports/report_maintenance_restore_tables_errors.html
codecept run acceptance Backend/TestMaintenanceCest::restoreTablesWithModsSimple "${BW_TEST_DEBUG}" --xml xmlreports/report_maintenance_restore_tables_mods_simple.xml --html htmlreports/report_maintenance_restore_tables_mods_simple.html
codecept run acceptance Backend/TestMaintenanceCest::restoreTablesWithModsVersion "${BW_TEST_DEBUG}" --xml xmlreports/report_maintenance_restore_tables_mods_version.xml --html htmlreports/report_maintenance_restore_tables_mods_version.html
codecept run acceptance Backend/TestMaintenanceCest::restoreTablesNoZip "${BW_TEST_DEBUG}" --xml xmlreports/report_maintenance_restore_tables.xml --html htmlreports/report_maintenance_restore_tables.html
codecept run acceptance Backend/TestMaintenanceCest::restoreTablesZip "${BW_TEST_DEBUG}" --xml xmlreports/report_maintenance_restore_tables_zip.xml --html htmlreports/report_maintenance_restore_tables_zip.html
codecept run acceptance Backend/TestMaintenanceCest::testBasicSettings "${BW_TEST_DEBUG}" --xml xmlreports/report_maintenance_basic_settings.xml --html htmlreports/report_maintenance_basic_settings.html
codecept run acceptance Backend/TestMaintenanceCest::testForumLink "${BW_TEST_DEBUG}" --xml xmlreports/report_maintenance_forum_link.xml --html htmlreports/report_maintenance_forum_link.html
fi

###############################
# test plugin User2Subscriber #
###############################

if [[ "${BW_TEST_CAT}" == user2subscriber_all ]]
then
# all tests for plugin user2subscriber
codecept run acceptance User2Subscriber/User2SubscriberCest "${BW_TEST_DEBUG}" --xml xmlreports/report_user2Subscriber.xml --html htmlreports/report_user2Subscriber.html
fi

if [[ "${BW_TEST_CAT}" == user2subscriber_single ]]
then
# single tests for plugin user2subscriber
codecept run acceptance User2Subscriber/User2SubscriberCest::setupUser2Subscriber "${BW_TEST_DEBUG}" --xml xmlreports/report_user2Subscriber_activate.xml --html htmlreports/report_user2Subscriber_activate.html

#codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithoutSubscription "${BW_TEST_DEBUG}" --xml xmlreports/report_u2s_no_subscription.xml --html htmlreports/report_u2s_no_subscription.html
#codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionSwitchSubscriptionWithoutSubscription "${BW_TEST_DEBUG}" --xml xmlreports/report_u2s_yes_no_subscription.xml --html htmlreports/report_u2s_yes_no_subscription.html
#codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithoutActivationExtended "${BW_TEST_DEBUG}" --xml xmlreports/report_u2s_no_activation_ext.xml --html htmlreports/report_u2s_no_activation_ext.html
#codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithActivationByFrontend "${BW_TEST_DEBUG}" --xml xmlreports/report_u2s_activation_FE.xml --html htmlreports/report_u2s_activation_FE.html
#codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithExistingSubscriptionSameList "${BW_TEST_DEBUG}" --xml xmlreports/report_u2s_subs_same_list.xml --html htmlreports/report_u2s_subs_same_list.html
#codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithExistingSubscriptionDifferentList "${BW_TEST_DEBUG}" --xml xmlreports/report_u2s_subs_diff_list.xml --html htmlreports/report_u2s_subs_diff_list.html
#codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithActivationByBackend "${BW_TEST_DEBUG}" --xml xmlreports/report_u2s_activation_BE.xml --html htmlreports/report_u2s_activation_BE.html
#codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithTextFormat "${BW_TEST_DEBUG}" --xml xmlreports/report_u2s_text_format.xml --html htmlreports/report_u2s_text_format.html
#codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithoutFormatSelectionHTML "${BW_TEST_DEBUG}" --xml xmlreports/report_u2s_no_format_select_html.xml --html htmlreports/report_u2s_no_format_select_html.html
#codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithoutFormatSelectionText "${BW_TEST_DEBUG}" --xml xmlreports/report_u2s_no_format_select_text.xml --html htmlreports/report_u2s_no_format_select_text.html
#codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithAnotherMailinglist "${BW_TEST_DEBUG}" --xml xmlreports/report_u2s_another_mailinglist.xml --html htmlreports/report_u2s_another_mailinglist.html
#codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithTwoMailinglists "${BW_TEST_DEBUG}" --xml xmlreports/report_u2s_two_mailinglists.xml --html htmlreports/report_u2s_two_mailinglists.html
codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithoutMailinglists "${BW_TEST_DEBUG}" --xml xmlreports/report_u2s_no_mailinglists.xml --html htmlreports/report_u2s_no_mailinglists.html
#codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithMailChangeYes "${BW_TEST_DEBUG}" --xml xmlreports/report_u2s_with_mail_change.xml --html htmlreports/report_u2s_with_mail_change.html
#codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithoutActivationWithMailChangeYes "${BW_TEST_DEBUG}" --xml xmlreports/report_u2s_no_activation_mail_change.xml --html htmlreports/report_u2s_no_activation_mail_change.html
#codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithMailChangeNo "${BW_TEST_DEBUG}" --xml xmlreports/report_u2s_without_mail_change.xml --html htmlreports/report_u2s_without_mail_change.html
#codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberFunctionWithDeleteNo "${BW_TEST_DEBUG}" --xml xmlreports/report_u2s_delete_no.xml --html htmlreports/report_u2s_delete_no.html
#codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberOptionsPluginDeactivated "${BW_TEST_DEBUG}" --xml xmlreports/report_u2s_plugin_deactivated.xml --html htmlreports/report_u2s_plugin_deactivated.html
#codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberOptionsMessage "${BW_TEST_DEBUG}" --xml xmlreports/report_u2s_options_message.xml --html htmlreports/report_u2s_options_message.html
#codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberOptionsSwitchShowFormat "${BW_TEST_DEBUG}" --xml xmlreports/report_u2s_switch_show_format.xml --html htmlreports/report_u2s_switch_show_format.html
#codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberPredefinedFormat "${BW_TEST_DEBUG}" --xml xmlreports/report_u2s_predefined_format.xml --html htmlreports/report_u2s_predefined_format.html
#codecept run acceptance User2Subscriber/User2SubscriberCest::User2SubscriberOptionsMailinglists "${BW_TEST_DEBUG}" --xml xmlreports/report_u2s_options_mailinglists.xml --html htmlreports/report_u2s_options_mailinglists.html
fi

if [[ "${BW_TEST_CAT}" == options_all ]]
then
# all tests options of BwPostman
codecept run acceptance Backend/TestOptionsCest "${BW_TEST_DEBUG}" --xml xmlreports/report_options.xml --html htmlreports/report_options.html
fi

if [[ "${BW_TEST_CAT}" == options_single ]]
then
# single tests for setting general options
#codecept run acceptance Backend/TestOptionsCest::saveDefaults "${BW_TEST_DEBUG}" --xml xmlreports/report_options_save_defaults.xml --html htmlreports/report_options_save_defaults.html
codecept run acceptance Backend/TestOptionsCest::setPermissions "${BW_TEST_DEBUG}" --xml xmlreports/report_options_set_permissions.xml --html htmlreports/report_options_set_permissions.html
#codecept run acceptance Backend/TestOptionsCest::checkBasicOptionSenderName "${BW_TEST_DEBUG}" --xml xmlreports/report_options_senderName.xml --html htmlreports/report_options_senderName.html
#codecept run acceptance Backend/TestOptionsCest::checkBasicOptionSenderEmail "${BW_TEST_DEBUG}" --xml xmlreports/report_options_senderMail.xml --html htmlreports/report_options_senderMail.html
#codecept run acceptance Backend/TestOptionsCest::checkBasicOptionReplyEmail "${BW_TEST_DEBUG}" --xml xmlreports/report_options_replyMail.xml --html htmlreports/report_options_replyMail.html
#codecept run acceptance Backend/TestOptionsCest::checkBasicOptionExcludedCategories "${BW_TEST_DEBUG}" --xml xmlreports/report_options_excludedCategories.xml --html htmlreports/report_options_excludedCategories.html
#codecept run acceptance Backend/TestOptionsCest::checkBasicOptionNewslettersPerStep "${BW_TEST_DEBUG}" --xml xmlreports/report_options_NlsPerStep.xml --html htmlreports/report_options_NlsPerStep.html
#codecept run acceptance Backend/TestOptionsCest::checkBasicOptionDelayTime "${BW_TEST_DEBUG}" --xml xmlreports/report_options_delayTime.xml --html htmlreports/report_options_delayTime.html
#codecept run acceptance Backend/TestOptionsCest::checkBasicOptionDelayUnit "${BW_TEST_DEBUG}" --xml xmlreports/report_options_delayUnit.xml --html htmlreports/report_options_delayUnit.html
#codecept run acceptance Backend/TestOptionsCest::checkBasicOptionPublishNewsletterAtSending "${BW_TEST_DEBUG}" --xml xmlreports/report_options_publishAtSending.xml --html htmlreports/report_options_publishAtSending.html
#codecept run acceptance Backend/TestOptionsCest::checkBasicOptionCompressBackup "${BW_TEST_DEBUG}" --xml xmlreports/report_options_compressBackup.xml --html htmlreports/report_options_compressBackup.html
#codecept run acceptance Backend/TestOptionsCest::checkBasicOptionShowBoldtWebserviceLink "${BW_TEST_DEBUG}" --xml xmlreports/report_options_showBwLink.xml --html htmlreports/report_options_showBwLink.html

#codecept run acceptance Backend/TestOptionsCest::checkRegistrationOptionIntroText "${BW_TEST_DEBUG}" --xml xmlreports/report_options_introText.xml --html htmlreports/report_options_introText.html
#codecept run acceptance Backend/TestOptionsCest::checkRegistrationOptionShowGender "${BW_TEST_DEBUG}" --xml xmlreports/report_options_showGender.xml --html htmlreports/report_options_showGender.html
#codecept run acceptance Backend/TestOptionsCest::checkRegistrationOptionLastNameMandatory "${BW_TEST_DEBUG}" --xml xmlreports/report_options_LastNameMandatory.xml --html htmlreports/report_options_LastNameMandatory.html
#codecept run acceptance Backend/TestOptionsCest::checkRegistrationOptionShowLastName "${BW_TEST_DEBUG}" --xml xmlreports/report_options_ShowLastName.xml --html htmlreports/report_options_ShowLastName.html
#codecept run acceptance Backend/TestOptionsCest::checkRegistrationOptionFirstNameMandatory "${BW_TEST_DEBUG}" --xml xmlreports/report_options_FirstNameMandatory.xml --html htmlreports/report_options_FirstNameMandatory.html
#codecept run acceptance Backend/TestOptionsCest::checkRegistrationOptionShowFirstName "${BW_TEST_DEBUG}" --xml xmlreports/report_options_.xml --html htmlreports/report_options_.html
#codecept run acceptance Backend/TestOptionsCest::checkRegistrationOptionShowAdditionalField "${BW_TEST_DEBUG}" --xml xmlreports/report_options_ShowFirstName.xml --html htmlreports/report_options_ShowFirstName.html
#codecept run acceptance Backend/TestOptionsCest::checkRegistrationOptionAdditionalFieldMandatory "${BW_TEST_DEBUG}" --xml xmlreports/report_options_AdditionalFieldMandatory.xml --html htmlreports/report_options_AdditionalFieldMandatory.html
#codecept run acceptance Backend/TestOptionsCest::checkRegistrationOptionAdditionalFieldLabel "${BW_TEST_DEBUG}" --xml xmlreports/report_options_AdditionalFieldLabel.xml --html htmlreports/report_options_AdditionalFieldLabel.html
#codecept run acceptance Backend/TestOptionsCest::checkRegistrationOptionAdditionalFieldTooltip "${BW_TEST_DEBUG}" --xml xmlreports/report_options_AdditionalFieldTooltip.xml --html htmlreports/report_options_AdditionalFieldTooltip.html
#codecept run acceptance Backend/TestOptionsCest::checkRegistrationOptionShowMailFormat "${BW_TEST_DEBUG}" --xml xmlreports/report_options_ShowMailFormat.xml --html htmlreports/report_options_ShowMailFormat.html
#codecept run acceptance Backend/TestOptionsCest::checkRegistrationOptionPresetMailFormat "${BW_TEST_DEBUG}" --xml xmlreports/report_options_PresetMailFormat.xml --html htmlreports/report_options_PresetMailFormat.html
#codecept run acceptance Backend/TestOptionsCest::checkRegistrationOptionShowMailinglistDescription "${BW_TEST_DEBUG}" --xml xmlreports/report_options_ShowMailinglistDescription.xml --html htmlreports/report_options_ShowMailinglistDescription.html
#codecept run acceptance Backend/TestOptionsCest::checkRegistrationOptionLengthOfDescription "${BW_TEST_DEBUG}" --xml xmlreports/report_options_LengthOfDescription.xml --html htmlreports/report_options_LengthOfDescription.html
#codecept run acceptance Backend/TestOptionsCest::checkRegistrationOptionDisplayDisclaimer "${BW_TEST_DEBUG}" --xml xmlreports/report_options_DisplayDisclaimer.xml --html htmlreports/report_options_DisplayDisclaimer.html
#codecept run acceptance Backend/TestOptionsCest::checkRegistrationOptionDisclaimerLinkTarget "${BW_TEST_DEBUG}" --xml xmlreports/report_options_DisclaimerLinkTarget.xml --html htmlreports/report_options_DisclaimerLinkTarget.html
#codecept run acceptance Backend/TestOptionsCest::checkRegistrationOptionDisclaimerCurrentWindow "${BW_TEST_DEBUG}" --xml xmlreports/report_options_DisclaimerCurrentWindow.xml --html htmlreports/report_options_DisclaimerCurrentWindow.html
#codecept run acceptance Backend/TestOptionsCest::checkRegistrationOptionDisclaimerPopup "${BW_TEST_DEBUG}" --xml xmlreports/report_options_DisclaimerPopup.xml --html htmlreports/report_options_DisclaimerPopup.html
#codecept run acceptance Backend/TestOptionsCest::checkRegistrationOptionSecureRegistrationForm "${BW_TEST_DEBUG}" --xml xmlreports/report_options_SecureRegistrationForm.xml --html htmlreports/report_options_SecureRegistrationForm.html
#
#codecept run acceptance Backend/TestOptionsCest::checkActivationOptionTitleForActivation "${BW_TEST_DEBUG}" --xml xmlreports/report_options_TitleForActivation.xml --html htmlreports/report_options_TitleForActivation.html
#codecept run acceptance Backend/TestOptionsCest::checkActivationOptionTextForActivation "${BW_TEST_DEBUG}" --xml xmlreports/report_options_TextForActivation.xml --html htmlreports/report_options_TextForActivation.html
#codecept run acceptance Backend/TestOptionsCest::checkActivationOptionTextAgreement "${BW_TEST_DEBUG}" --xml xmlreports/report_options_TextAgreement.xml --html htmlreports/report_options_TextAgreement.html
#codecept run acceptance Backend/TestOptionsCest::checkActivationOptionActivationToWebmaster "${BW_TEST_DEBUG}" --xml xmlreports/report_options_ActivationToWebmaster.xml --html htmlreports/report_options_ActivationToWebmaster.html
#codecept run acceptance Backend/TestOptionsCest::checkActivationOptionActivationSenderName "${BW_TEST_DEBUG}" --xml xmlreports/report_options_ActivationSenderName.xml --html htmlreports/report_options_ActivationSenderName.html
#codecept run acceptance Backend/TestOptionsCest::checkActivationOptionActivationSenderMail "${BW_TEST_DEBUG}" --xml xmlreports/report_options_ActivationSenderMail.xml --html htmlreports/report_options_ActivationSenderMail.html
#
#codecept run acceptance Backend/TestOptionsCest::checkUnsubscriptionOptionUnsubscriptionWithOneClick "${BW_TEST_DEBUG}" --xml xmlreports/report_options_UnsubscriptionWithOneClick.xml --html htmlreports/report_options_UnsubscriptionWithOneClick.html
##codecept run acceptance Backend/TestOptionsCest::checkUnsubscriptionOptionUnsubscriptionToWebmaster "${BW_TEST_DEBUG}" --xml xmlreports/report_options_UnsubscriptionToWebmaster.xml --html htmlreports/report_options_UnsubscriptionToWebmaster.html
#codecept run acceptance Backend/TestOptionsCest::checkUnsubscriptionOptionUnsubscriptionSenderName "${BW_TEST_DEBUG}" --xml xmlreports/report_options_UnsubscriptionSenderName.xml --html htmlreports/report_options_UnsubscriptionSenderName.html
#codecept run acceptance Backend/TestOptionsCest::checkUnsubscriptionOptionUnsubscriptionSenderMail "${BW_TEST_DEBUG}" --xml xmlreports/report_options_UnsubscriptionSenderMail.xml --html htmlreports/report_options_.UnsubscriptionSenderMail.html
#
#codecept run acceptance Backend/TestOptionsCest::checkListsOptionSearchField "${BW_TEST_DEBUG}" --xml xmlreports/report_options_SearchField.xml --html htmlreports/report_options_SearchField.html
#codecept run acceptance Backend/TestOptionsCest::checkListsOptionDateFilter "${BW_TEST_DEBUG}" --xml xmlreports/report_options_DateFilter.xml --html htmlreports/report_options_DateFilter.html
#codecept run acceptance Backend/TestOptionsCest::checkListsOptionMailinglistsFilter "${BW_TEST_DEBUG}" --xml xmlreports/report_options_MailinglistsFilter.xml --html htmlreports/report_options_MailinglistsFilter.html
#codecept run acceptance Backend/TestOptionsCest::checkListsOptionCampaignFilter "${BW_TEST_DEBUG}" --xml xmlreports/report_options_CampaignFilter.xml --html htmlreports/report_options_CampaignFilter.html
#codecept run acceptance Backend/TestOptionsCest::checkListsOptionUsergroupFilter "${BW_TEST_DEBUG}" --xml xmlreports/report_options_UsergroupFilter.xml --html htmlreports/report_options_UsergroupFilter.html
#codecept run acceptance Backend/TestOptionsCest::checkListsOptionEnableAttachment "${BW_TEST_DEBUG}" --xml xmlreports/report_options_EnableAttachmentListsView.xml --html htmlreports/report_options_EnableAttachmentListsView.html
#codecept run acceptance Backend/TestOptionsCest::checkListsOptionCheckAccess "${BW_TEST_DEBUG}" --xml xmlreports/report_options_CheckAccess.xml --html htmlreports/report_options_CheckAccess.html
#codecept run acceptance Backend/TestOptionsCest::checkListsOptionNumberNewslettersToList "${BW_TEST_DEBUG}" --xml xmlreports/report_options_NumberNewslettersToList.xml --html htmlreports/report_options_NumberNewslettersToList.html
#
#codecept run acceptance Backend/TestOptionsCest::checkSingleOptionEnableAttachment "${BW_TEST_DEBUG}" --xml xmlreports/report_options_EnableAttachmentSingleView.xml --html htmlreports/report_options_EnableAttachmentSingleView.html
#codecept run acceptance Backend/TestOptionsCest::checkSingleOptionShowSubjectAsPageTitle "${BW_TEST_DEBUG}" --xml xmlreports/report_options_ShowSubjectAsPageTitle.xml --html htmlreports/report_options_ShowSubjectAsPageTitle.html
fi

if [[ "${BW_TEST_CAT}" == access_single ]]
then
# single tests for permissions
#codecept run acceptance Backend/Access/TestAccessCest::TestAccessRightsForListViewButtonsFromMainView "${BW_TEST_DEBUG}" --xml xmlreports/report_acceptance_main_buttons_list.xml --html htmlreports/report_acceptance_main_buttons_list.html
#codecept run acceptance Backend/Access/TestAccessCest::TestAccessRightsForAddButtonsFromMainView "${BW_TEST_DEBUG}" --xml xmlreports/report_acceptance_main_buttons_add.xml --html htmlreports/report_acceptance_main_buttons_add.html
#codecept run acceptance Backend/Access/TestAccessCest::TestAccessRightsForActionsInListsByButtonsPart1 "${BW_TEST_DEBUG}" --xml xmlreports/report_acceptance_list_action_buttons1.xml --html htmlreports/report_list_action_buttons1.html
#codecept run acceptance Backend/Access/TestAccessCest::TestAccessRightsForActionsInListsByButtonsPart2 "${BW_TEST_DEBUG}" --xml xmlreports/report_acceptance_list_action_buttons2.xml --html htmlreports/report_list_action_buttons2.html
#codecept run acceptance Backend/Access/TestAccessCest::TestAccessRightsForActionsInListsByButtonsPart3 "${BW_TEST_DEBUG}" --xml xmlreports/report_acceptance_list_action_buttons3.xml --html htmlreports/report_list_action_buttons3.html
codecept run acceptance Backend/Access/TestAccessCest::TestAccessRightsForActionsInListsByButtonsPart4 "${BW_TEST_DEBUG}" --xml xmlreports/report_acceptance_list_action_buttons4.xml --html htmlreports/report_list_action_buttons4.html
fi

######################################
# test plugin FooterUsedMailinglists #
######################################

if [[ "${BW_TEST_CAT}" == footerusedmailinglists_all ]]
then
# all tests for plugin FooterUsedMailinglists
codecept run acceptance FooterUsedMailinglists/FooterUsedMailinglistsCest "${BW_TEST_DEBUG}" --xml xmlreports/report_footerUsedMailinglists.xml --html htmlreports/report_footerUsedMailinglists.html
fi

if [[ "${BW_TEST_CAT}" == footerusedmailinglists_single ]]
then
# single tests for plugin FooterUsedMailinglists
codecept run acceptance FooterUsedMailinglists/FooterUsedMailinglistsCest::DontShowAnyRecipients "${BW_TEST_DEBUG}" --xml xmlreports/report_footerUsedMailinglists_DontShow.xml --html htmlreports/report_footerUsedMailinglists_DontShow.html
codecept run acceptance FooterUsedMailinglists/FooterUsedMailinglistsCest::ShowRecipientsAvailableMailinglistOnly "${BW_TEST_DEBUG}" --xml xmlreports/report_footerUsedMailinglists_availableMl_only.xml --html htmlreports/report_footerUsedMailinglists_availableMl_only.html
codecept run acceptance FooterUsedMailinglists/FooterUsedMailinglistsCest::ShowRecipientsUnavailableMailinglistOnly "${BW_TEST_DEBUG}" --xml xmlreports/report_footerUsedMailinglists_unavailableMl_only.xml --html htmlreports/report_footerUsedMailinglists_unavailableMl_only.html
codecept run acceptance FooterUsedMailinglists/FooterUsedMailinglistsCest::ShowRecipientsInternalMailinglistOnly "${BW_TEST_DEBUG}" --xml xmlreports/report_footerUsedMailinglists_internalMl_only.xml --html htmlreports/report_footerUsedMailinglists_internalMl_only.html
codecept run acceptance FooterUsedMailinglists/FooterUsedMailinglistsCest::ShowRecipientsUsergroupOnly "${BW_TEST_DEBUG}" --xml xmlreports/report_footerUsedMailinglists_usergroups_only.xml --html htmlreports/report_footerUsedMailinglists_uergroups_only.html
codecept run acceptance FooterUsedMailinglists/FooterUsedMailinglistsCest::ShowRecipientsAvailableMailinglistNbr "${BW_TEST_DEBUG}" --xml xmlreports/report_footerUsedMailinglists_availableMl_nbr.xml --html htmlreports/report_footerUsedMailinglists_availableMl_nbr.html
codecept run acceptance FooterUsedMailinglists/FooterUsedMailinglistsCest::ShowRecipientsUnavailableMailinglistNbr "${BW_TEST_DEBUG}" --xml xmlreports/report_footerUsedMailinglists_unavailableMl_nbr.xml --html htmlreports/report_footerUsedMailinglists_unavailableMl_nbr.html
codecept run acceptance FooterUsedMailinglists/FooterUsedMailinglistsCest::ShowRecipientsInternalMailinglistNbr "${BW_TEST_DEBUG}" --xml xmlreports/report_footerUsedMailinglists_internalMl_nbr.xml --html htmlreports/report_footerUsedMailinglists_internalMl_nbr.html
codecept run acceptance FooterUsedMailinglists/FooterUsedMailinglistsCest::ShowRecipientsUsergroupNbr "${BW_TEST_DEBUG}" --xml xmlreports/report_footerUsedMailinglists_usergroups_nbr.xml --html htmlreports/report_footerUsedMailinglists_usergroups_nbr.html
codecept run acceptance FooterUsedMailinglists/FooterUsedMailinglistsCest::ShowRecipientsMultipleMailinglistsAndUsergroupsNbr "${BW_TEST_DEBUG}" --xml xmlreports/report_footerUsedMailinglists_and_usergroups_nbr.xml --html htmlreports/report_footerUsedMailinglists_and_usergroups_nbr.html
codecept run acceptance FooterUsedMailinglists/FooterUsedMailinglistsCest::ShowRecipientsMultipleMailinglistsAndUsergroupsNbrSummarized "${BW_TEST_DEBUG}" --xml xmlreports/report_footerUsedMailinglists_and_usergroups_nbr_sum.xml --html htmlreports/report_footerUsedMailinglists_and_usergroups_nbr_sum.html
codecept run acceptance FooterUsedMailinglists/FooterUsedMailinglistsCest::ShowRecipientsOnlyNbrAndSummarized "${BW_TEST_DEBUG}" --xml xmlreports/report_footer_noUsedMailinglists_no_usergroups_but_sum.xml --html htmlreports/report_footer_noUsedMailinglists_no_usergroups_but_sum.html
codecept run acceptance FooterUsedMailinglists/FooterUsedMailinglistsCest::ShowRecipientsOnlySummarized "${BW_TEST_DEBUG}" --xml xmlreports/report_footerUsedMailinglists_only_sum.xml --html htmlreports/report_footerUsedMailinglists_only_sum.html
fi

if [[ "${BW_TEST_CAT}" == registration_module ]]
then
# single tests for registration module
codecept run acceptance Module_Register/SubscribeModuleCest::setupRegistrationModule "${BW_TEST_DEBUG}" --xml xmlreports/report_modRegister_setup.xml --html htmlreports/report_modRegister_setup.html

#codecept run acceptance Module_Register/SubscribeModuleCest::SubscribeModuleSimpleActivateAndUnsubscribeCO "${BW_TEST_DEBUG}" --xml xmlreports/report_modRegister_activate_and_unsubscribe_co.xml --html htmlreports/report_modRegister_activate_and_unsubscribe_co.html
#codecept run acceptance Module_Register/SubscribeModuleCest::SubscribeModuleSimpleActivateAndUnsubscribeMO "${BW_TEST_DEBUG}" --xml xmlreports/report_modRegister_activate_and_unsubscribe_mo.xml --html htmlreports/report_modRegister_activate_and_unsubscribe_mo.html
#codecept run acceptance Module_Register/SubscribeModuleCest::SubscribeModuleSimpleActivateAndUnsubscribePopupMO "${BW_TEST_DEBUG}" --xml xmlreports/report_modRegister_activate_and_unsubscribe_popup_mo.xml --html htmlreports/report_modRegister_activate_and_unsubscribe_popup_mo.html
#codecept run acceptance Module_Register/SubscribeModuleCest::SubscribeModuleSimpleActivateAndUnsubscribeBigPopupMO "${BW_TEST_DEBUG}" --xml xmlreports/report_modRegister_activate_and_unsubscribe_big_popup_mo.xml --html htmlreports/report_modRegister_activate_and_unsubscribe_big_popup_mo.html
#codecept run acceptance Module_Register/SubscribeModuleCest::SubscribeModulePopupOverPopup "${BW_TEST_DEBUG}" --xml xmlreports/report_modRegister_popup_over_popup.xml --html htmlreports/report_modRegister_popup_over_popup.html
#codecept run acceptance Module_Register/SubscribeModuleCest::EditSubscriptionByModule "${BW_TEST_DEBUG}" --xml xmlreports/report_modRegister_edit_subscription.xml --html htmlreports/report_modRegister_edit_subscription.html
#codecept run acceptance Module_Register/SubscribeModuleCest::SubscribeMissingValuesModule "${BW_TEST_DEBUG}" --xml xmlreports/report_modRegister_missing_values.xml --html htmlreports/report_modRegister_missing_values.html
#codecept run acceptance Module_Register/SubscribeModuleCest::SubscribeShowFieldsModule "${BW_TEST_DEBUG}" --xml xmlreports/report_modRegister_show_fields.xml --html htmlreports/report_modRegister_show_fields.html
#codecept run acceptance Module_Register/SubscribeModuleCest::CheckMailinglistDescriptionModule "${BW_TEST_DEBUG}" --xml xmlreports/report_modRegister_show_desc.xml --html htmlreports/report_modRegister_show_desc.html
#codecept run acceptance Module_Register/SubscribeModuleCest::CheckIntroTextModule "${BW_TEST_DEBUG}" --xml xmlreports/report_modRegister_check_intro.xml --html htmlreports/report_modRegister_check_intro.html
codecept run acceptance Module_Register/SubscribeModuleCest::CheckDisclaimerContentPopupModule "${BW_TEST_DEBUG}" --xml xmlreports/report_modRegister_check_disclaimer_selection_popup.xml --html htmlreports/report_modRegister_check_disclaimer_selection_popup.html
#codecept run acceptance Module_Register/SubscribeModuleCest::CheckDisclaimerContentNewWindowModule "${BW_TEST_DEBUG}" --xml xmlreports/report_modRegister_check_disclaimer_selection_new.xml --html htmlreports/report_modRegister_check_disclaimer_selection_new.html
#codecept run acceptance Module_Register/SubscribeModuleCest::CheckDisclaimerContentSameWindowModule "${BW_TEST_DEBUG}" --xml xmlreports/report_modRegister_check_disclaimer_selection_same.xml --html htmlreports/report_modRegister_check_disclaimer_selection_same.html
#codecept run acceptance Module_Register/SubscribeModuleCest::CheckSecurityQuestionModule "${BW_TEST_DEBUG}" --xml xmlreports/report_modRegister_check_security_question.xml --html htmlreports/report_modRegister_check_security_question.html
#codecept run acceptance Module_Register/SubscribeModuleCest::CheckSelectableMailinglistsModule "${BW_TEST_DEBUG}" --xml xmlreports/report_modRegister_check_mailinglists_number.xml --html htmlreports/report_modRegister_check_mailinglists_number.html
#codecept run acceptance Module_Register/SubscribeModuleCest::SubscribeAbuseFieldsModule "${BW_TEST_DEBUG}" --xml xmlreports/report_frontend_modRegister_fields.xml --html htmlreports/report_modRegister_abuse_fields.html
#codecept run acceptance Module_Register/SubscribeModuleCest::SubscribeUnreachableMailAddressModule "${BW_TEST_DEBUG}" --xml xmlreports/report_modRegister_unreachable_mail.xml --html htmlreports/report_modRegister_unreachable_mail.html
fi

if [[ "${BW_TEST_CAT}" == overview_module ]]
then
# single tests for registration module
codecept run acceptance ModuleOverview/ModuleOverviewCest::setupOverviewModule "${BW_TEST_DEBUG}" --xml xmlreports/report_modOverview_setup.xml --html htmlreports/report_modOverview_setup.html

codecept run acceptance ModuleOverview/ModuleOverviewCest::OverviewModuleCheckNumberOfMonthsAll "${BW_TEST_DEBUG}" --xml xmlreports/report_modOverview_check_number_of_months_all.xml --html htmlreports/report_modOverview_check_number_of_months_all.html
codecept run acceptance ModuleOverview/ModuleOverviewCest::OverviewModuleCheckNumberOfMonthsRestricted "${BW_TEST_DEBUG}" --xml xmlreports/report_modOverview_check_number_of_months_restricted.xml --html htmlreports/report_modOverview_check_number_of_months_restricted.html
codecept run acceptance ModuleOverview/ModuleOverviewCest::OverviewModuleCheckNumberOfMonthsOnlyNotArchived "${BW_TEST_DEBUG}" --xml xmlreports/report_modOverview_check_number_of_months_not_archived.xml --html htmlreports/report_modOverview_check_number_of_months_not_archived.html
codecept run acceptance ModuleOverview/ModuleOverviewCest::OverviewModuleCheckNumberOfMonthsNotArchivedNotExpired "${BW_TEST_DEBUG}" --xml xmlreports/report_modOverview_check_number_of_months_not_archived_not_expired.xml --html htmlreports/report_modOverview_check_number_of_months_not_archived_not_expired.html
codecept run acceptance ModuleOverview/ModuleOverviewCest::OverviewModuleCheckNumberOfMonthsNotArchivedButExpired "${BW_TEST_DEBUG}" --xml xmlreports/report_modOverview_check_number_of_months_not_expored.xml --html htmlreports/report_modOverview_check_number_of_months_not_expored.html
codecept run acceptance ModuleOverview/ModuleOverviewCest::OverviewModuleCheckNumberOfMonthsOnlyArchived "${BW_TEST_DEBUG}" --xml xmlreports/report_modOverview_check_number_of_months_only_archived.xml --html htmlreports/report_modOverview_check_number_of_months_only_archived.html
codecept run acceptance ModuleOverview/ModuleOverviewCest::OverviewModuleCheckNumberOfMonthsOnlyExpired "${BW_TEST_DEBUG}" --xml xmlreports/report_modOverview_check_number_of_months_only_expired.xml --html htmlreports/report_modOverview_check_number_of_months_only_expired.html
codecept run acceptance ModuleOverview/ModuleOverviewCest::OverviewModuleCheckNumberOfMonthsArchivedAndExpired "${BW_TEST_DEBUG}" --xml xmlreports/report_modOverview_check_number_of_months_archived_and_expired.xml --html htmlreports/report_modOverview_check_number_of_months_archived_and_expired.html
codecept run acceptance ModuleOverview/ModuleOverviewCest::OverviewModuleCheckNumberOfMonthsArchivedOrExpired "${BW_TEST_DEBUG}" --xml xmlreports/report_modOverview_check_number_of_months_archived_or_expired.xml --html htmlreports/report_modOverview_check_number_of_months_archived_or_expired.html
codecept run acceptance ModuleOverview/ModuleOverviewCest::OverviewModuleCheckNumberOfMonthsOnlyAllMailinglists "${BW_TEST_DEBUG}" --xml xmlreports/report_modOverview_check_number_of_months_mailinglists.xml --html htmlreports/report_modOverview_check_number_of_months_mailinglists.html
codecept run acceptance ModuleOverview/ModuleOverviewCest::OverviewModuleCheckNumberOfMonthsOnlyAllUsergroups "${BW_TEST_DEBUG}" --xml xmlreports/report_modOverview_check_number_of_months_usergroups.xml --html htmlreports/report_modOverview_check_number_of_months_usergroups.html
codecept run acceptance ModuleOverview/ModuleOverviewCest::OverviewModuleCheckNumberOfMonthsOnlyAllCampaigns "${BW_TEST_DEBUG}" --xml xmlreports/report_modOverview_check_number_of_months_campaigns.xml --html htmlreports/report_modOverview_check_number_of_months_campaigns.html
fi

if [[ "${BW_TEST_CAT}" == user_account ]]
then
# single tests for userAccount plugin
codecept run acceptance UserAccount/UserAccountCest::SyncSubscriberWithAccount "${BW_TEST_DEBUG}" --xml xmlreports/report_userAccount_check.xml --html htmlreports/report_userAccount_check.html
fi

if [[ "${BW_TEST_CAT}" == all ]]
then
# run all tests
codecept run acceptance Backend/Lists "${BW_TEST_DEBUG}" --xml xmlreports/report_lists.xml --html htmlreports/report_lists.html
codecept run acceptance Backend/Details "${BW_TEST_DEBUG}" --xml xmlreports/report_details.xml --html htmlreports/report_details.html
codecept run acceptance Frontend "${BW_TEST_DEBUG}" --xml xmlreports/report_frontend.xml --html htmlreports/report_frontend.html
codecept run acceptance Backend/TestMaintenanceCest "${BW_TEST_DEBUG}" --xml xmlreports/report_maintenance.xml --html htmlreports/report_maintenance.html
codecept run acceptance User2Subscriber "${BW_TEST_DEBUG}" --xml xmlreports/report_user2Subscriber.xml --html htmlreports/report_user2Subscriber.html
codecept run acceptance Backend/Options "${BW_TEST_DEBUG}" --xml xmlreports/report_options.xml --html htmlreports/report_options.html
codecept run acceptance Backend/Access "${BW_TEST_DEBUG}" --xml xmlreports/report_access.xml --html htmlreports/report_access.html

codecept run acceptance FooterUsedMailinglists "${BW_TEST_DEBUG}" --xml xmlreports/report_footerUsedMailinglists.xml --html htmlreports/report_footerUsedMailinglists.html
codecept run acceptance Module_Register "${BW_TEST_DEBUG}" --xml xmlreports/report_modRegister.xml --html htmlreports/report_modRegister.html
codecept run acceptance ModuleOverview "${BW_TEST_DEBUG}" --xml xmlreports/report_modOverview.xml --html htmlreports/report_modOverview.html
codecept run acceptance UserAccount "${BW_TEST_DEBUG}" --xml xmlreports/report_userAccount.xml --html htmlreports/report_userAccount.html
fi
