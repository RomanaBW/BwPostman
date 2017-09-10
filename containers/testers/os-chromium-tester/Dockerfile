
FROM romana/os-tester-acceptance

MAINTAINER Romana Boldt <info@boldt-webservice.de>

## install for acceptance testing additionally needed packages    /ftp://rpmfind.net/linux/opensuse/update/leap/42.2/oss/x86_64/chromium-58.0.3029.81-104.9.1.x86_64.rpm

RUN zypper --non-interactive ar -cfG -p 98 -t rpm-md http://dl.google.com/linux/chrome/rpm/stable/x86_64/ Chrome
RUN zypper --non-interactive refresh

RUN zypper --non-interactive in google-chrome-stable
#RUN zypper --non-interactive in dbus-1-glib \
#							    gobject-introspection \
#							    libXss1 \
#							    libgthread-2_0-0 \
#							    libminizip1 \
#							    libre2-0 \
#							    libsnappy1 \
#							    libwebpdemux1 \
#							    gconf2

#RUN cd data
#RUN ls -l

#set some environment variables
ENV DISPLAY=:99 COMPOSER_DIR=/opt/composer UOPZ_VERSION=5.0.2 XDEBUG_VERSION=2.5.3 SELENIUM_DIR=/opt/selenium SELENIUMSERVER_VERSION=3.5.3 GECKODRIVER_VERSION=v0.13.0 CHROMEDRIVER_VERSION=2.32

#COPY ./data/chromedriver-58.0.3029.96-104.12.1.x86_64.rpm /data/chromedriver.rpm
#COPY ./data/chromium-58.0.3029.96-104.12.1.x86_64.rpm /data/chromium.rpm

COPY ./data/chromedriver /usr/local/bin/chromedriver

#RUN cd data
#RUN ls -l

#RUN rpm -i ./data/chromium.rpm
#RUN rpm -i ./data/chromedriver.rpm

