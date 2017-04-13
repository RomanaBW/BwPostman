
FROM debian:wheezy

MAINTAINER Romana Boldt <info@boldt-webservice.de>

## install for acceptance testing additionally needed packages
#RUN echo deb http://www.deb-multimedia.org testing main non-free \
#                  >>/etc/apt/sources.list

RUN echo "deb http://ppa.launchpad.net/webupd8team/java/ubuntu trusty main" | tee -a /etc/apt/sources.list
RUN apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys EEA14886


#RUN apt-get update && apt-get install -y --force-yes \
#                                deb-multimedia-keyring

#RUN apt-get update -y --force-yes
#RUN apt-get dist-upgrade -y --force-yes

RUN echo debconf shared/accepted-oracle-license-v1-1 select true | \
    debconf-set-selections

RUN echo debconf shared/accepted-oracle-license-v1-1 seen true | \
    debconf-set-selections

RUN apt-get update && apt-get install -y --force-yes \
                                curl \
                                gcc \
                                git \
                                libav-tools \
                                make \
                                mc \
                                oracle-java8-installer \
                                php5 \
                                php5-cli \
                                php5-curl \
                                php5-dev \
                                php5-fpm \
                                php5-json \
                                php5-mcrypt \
                                php5-mysql \
                                tar \
                                tmux \
                                wget \
                                x11vnc

#set some environment variables
ENV DISPLAY=:99 COMPOSER_DIR=/opt/composer UOPZ_VERSION=2.0.7 XDEBUG_VERSION=2.5.0RC1 SELENIUM_DIR=/opt/selenium SELENIUMSERVER_VERSION=3.0.1 GECKODRIVER_VERSION=v0.11.1 CHROMEDRIVER_VERSION=2.25

COPY ./data /data
RUN /data/installer.sh
