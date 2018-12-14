
FROM romana/os-tester-acceptance

MAINTAINER Romana Boldt <info@boldt-webservice.de>

RUN zypper --non-interactive in chromium \
								chromedriver \
							    libminizip1 \
							    libre2-0 \
								dbus-1-glib \
							    gobject-introspection \
							    libXss1 \
							    libgthread-2_0-0 \
							    libsnappy1 \
							    libwebpdemux1 \
							    gconf2

#set some environment variables
ENV DISPLAY=:99 COMPOSER_DIR=/opt/composer UOPZ_VERSION=5.0.2 XDEBUG_VERSION=2.5.3 SELENIUM_DIR=/opt/selenium SELENIUMSERVER_VERSION=3.5.3 GECKODRIVER_VERSION=v0.13.0 CHROMEDRIVER_VERSION=2.32

COPY ./data/chromedriver /usr/local/bin/chromedriver
