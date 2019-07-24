#!/bin/bash
### Tests with ## at the beginning are both commented out and faulty

#BW_TEST_DEBUG='--debug'
#BW_TEST_DEBUG=''

export BW_NEW_TEST_RUN=true

##########################
# test installation      #
##########################


if [ ${BW_TEST_CAT} == install_single ]
then
# run specific tests
codecept run acceptance Backend/TestInstallationCest ${BW_TEST_DEBUG} --xml report_installation.xml --html report_installation.html
codecept run acceptance Backend/TestMaintenanceRestoreCest ${BW_TEST_DEBUG} --xml report_restore.xml --html report_restore.html
codecept run acceptance Backend/TestMaintenanceCest ${BW_TEST_DEBUG} --xml report_single.xml --html report_single.html
codecept run acceptance Backend/TestDeinstallationCest ${BW_TEST_DEBUG} --xml report_deinstallation.xml --html report_deinstallation.html
fi

if [ ${BW_TEST_CAT} == install_all ]
then
# run all tests
codecept run acceptance ${BW_TEST_DEBUG} --xml report_all.xml --html report_all.html
fi

# Installation
if [ ${JOOMLA_VERSION} != 370 ]
then
codecept run acceptance Backed/TestInstallationCest::installation ${BW_TEST_DEBUG} --xml xmlreports/report_installation_installation.xml --html htmlreports/report_installation_installation.html
fi
codecept run acceptance Backend/TestOptionsCest::saveDefaults ${BW_TEST_DEBUG} --xml xmlreports/report_option_save_defaults.xml --html htmlreports/report_option_save_defaults.html

# data restore
codecept run acceptance Backend/TestMaintenanceRestoreCest ${BW_TEST_DEBUG} --xml xmlreports/report_restore.xml --html htmlreports/report_restore.html

if [ ${TEST_CAT} == all ]
then
# set permissions
codecept run acceptance Backend/TestOptionsCest::setPermissions ${BW_TEST_DEBUG} --xml xmlreports/report_option_set_permissions.xml --html htmlreports/report_option_set_permissions.html
fi
