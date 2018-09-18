#! /bin/bash
# Loop until selenium server is available
#until $(curl --output /dev/null --silent --head --fail http://localhost:4445/wd/hub); do
#    printf '.'
#    sleep 1
#done
#printf '\n'

# run tests

codecept run acceptance Backend/TestInstallationCest -vvv --xml xmlreports/report_install.xml --html htmlreports/report_install.html
codecept run acceptance Backend/TestOptionsCest -vvv --xml xmlreports/report_options.xml --html htmlreports/report_options.html
codecept run acceptance Frontend -vvv --xml xmlreports/report_frontend.xml --html htmlreports/report_frontend.html
codecept run acceptance User2Subscriber -vvv --xml xmlreports/report_user2subscriber.xml --html htmlreports/report_user2subscriber.html
