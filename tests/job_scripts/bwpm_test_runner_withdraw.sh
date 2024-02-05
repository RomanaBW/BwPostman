#!/bin/bash
### Tests with ## at the beginning are both commented out and faulty

# export sudo user
export BW_TESTER_USER="root"
export BW_TEST_WITHDRAW="yes"
export BWPM_VERSION_TO_TEST="${BW_TEST_BWPM_VERSION}"

BW_TEST_DEBUG='--debug'
#BW_TEST_DEBUG=''

export BW_NEW_TEST_RUN=true

codecept build

# Create new table backup after withdrawing data
codecept run acceptance Backend/TestMaintenanceCest::saveTablesZip "${BW_TEST_DEBUG}" --xml xmlreports/report_maintenance_save_tables_zip.xml --html htmlreports/report_maintenance_save_tables_zip.html
codecept run acceptance Backend/TestMaintenanceCest::saveTablesNoZip "${BW_TEST_DEBUG}" --xml xmlreports/report_maintenance_save_tables.xml --html htmlreports/report_maintenance_save_tables.html

# Test module overview after withdrawing data
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

