
FROM romana/os-tester-acceptance_227_5719

MAINTAINER Romana Boldt <info@boldt-webservice.de>

## install for acceptance testing additionally needed packages

RUN zypper --non-interactive in chromium-57.0.2987.133 \
                                chromedriver-57.0.2987.133
#RUN zypper --non-interactive in chromium-55.0.2883.75 \
#                                chromedriver-55.0.2883.75

#set some environment variables
ENV DISPLAY=:99 COMPOSER_DIR=/opt/composer UOPZ_VERSION=2.0.7 XDEBUG_VERSION=2.5.0 SELENIUM_DIR=/opt/selenium SELENIUMSERVER_VERSION=3.0.1 GECKODRIVER_VERSION=v0.13.0 CHROMEDRIVER_VERSION=2.25

#COPY ./data/chromedriver_2.25 /data/chromedriver_2.25
#COPY ./data/chromium_56 /data/chromium_56

#RUN cp /data/chromedriver_2.25 /usr/lib64/chromium/chromedriver
#RUN cp /data/chromium_56 /usr/lib64/chromium/chromium

