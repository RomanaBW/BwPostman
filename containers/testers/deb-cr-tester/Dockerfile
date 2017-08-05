
FROM romana/deb-tester

MAINTAINER Romana Boldt <info@boldt-webservice.de>

## install for acceptance testing additionally needed packages
RUN apt-get update && apt-get install  -y --force-yes \
                                chromium

#set some environment variables
ENV DISPLAY=:99 COMPOSER_DIR=/opt/composer UOPZ_VERSION=2.0.7 XDEBUG_VERSION=2.5.0RC1 SELENIUM_DIR=/opt/selenium SELENIUMSERVER_VERSION=3.0.1 GECKODRIVER_VERSION=v0.11.1 CHROMEDRIVER_VERSION=2.24

COPY ./data /data
