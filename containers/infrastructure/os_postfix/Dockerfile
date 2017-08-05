
FROM romana/os_base

MAINTAINER Romana Boldt <info@boldt-webservice.de>

## install for basically needed packages
RUN zypper --non-interactive in postfix

# keep packages on newest state
RUN zypper --non-interactive up

# add mailtrap user
RUN useradd mailtrap

# first configuration of postfix
RUN newaliases & \
    postmap /etc/postfix/transport & \
    /etc/postfix/system/config_postfix
COPY data/config/etc/ /etc/
COPY data/config/etc/minit/minit /sbin/minit
ENTRYPOINT ["/sbin/minit"]

