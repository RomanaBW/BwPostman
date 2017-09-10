
FROM opensuse

MAINTAINER Romana Boldt <info@boldt-webservice.de>

## add repo for php 5.6 and refresh
RUN zypper --non-interactive ar -cfG -p 98 -t rpm-md http://download.opensuse.org/repositories/devel:/languages:/php/openSUSE_Leap_42.2/devel:languages:php.repo
RUN zypper --non-interactive ar -cfG -p 98 -t rpm-md http://ftp.gwdg.de/pub/linux/misc/packman/suse/openSUSE_Leap_42.2/ Packman
RUN zypper --non-interactive refresh

## install for acceptance testing additionally needed packages
RUN zypper --non-interactive in curl \
                                ffmpeg \
                                ffx264 \
                                firefox \
                                free-ttf-fonts \
                                gcc \
                                git \
                                glibc-i18ndata \
                                glibc-locale \
                                java-1_8_0-openjdk \
                                libx264-148 \
                                make \
                                mc \
                                php5 \
                                php5-bcmath \
                                php5-bz2 \
                                php5-ctype \
                                php5-curl \
                                php5-devel \
                                php5-dom \
                                php5-fastcgi \
                                php5-fpm \
                                php5-iconv \
                                php5-json \
                                php5-mbstring \
                                php5-mcrypt \
                                php5-openssl \
                                php5-pcntl \
                                php5-pdo \
                                php5-pdo_mysql \
                                php5-pear \
                                php5-phar \
                                php5-soap \
                                php5-tokenizer \
                                php5-xmlwriter \
                                php5-zlib \
                                php5-zip \
                                tar \
                                tmux \
                                unzip \
                                wget \
                                x11vnc \
                                xvfb-run \
                                xorg-x11-fonts \
                                xorg-x11-fonts-core \
                                xorg-x11-server \
                                xorg-x11-Xvnc \
                                zlib-devel

# keep packages on newest state
RUN zypper --non-interactive up

#set some environment variables
ENV DISPLAY=:99 COMPOSER_DIR=/opt/composer UOPZ_VERSION=2.0.7 XDEBUG_VERSION=2.5.0 SELENIUM_DIR=/opt/selenium SELENIUMSERVER_VERSION=3.0.1 GECKODRIVER_VERSION=v0.11.1 CHROMEDRIVER_VERSION=2.27

COPY ./data /data
RUN /data/installer.sh
