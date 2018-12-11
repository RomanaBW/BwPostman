 
FROM opensuse

MAINTAINER Romana Boldt <info@boldt-webservice.de>

## install for basically needed packages
RUN zypper --non-interactive in openssh
                                
# keep packages on newest state
RUN zypper --non-interactive up

VOLUME ["/outerworld"]

RUN chmod -R 0777 /outerworld
RUN umask 0000 /outerworld

EXPOSE 22 25
