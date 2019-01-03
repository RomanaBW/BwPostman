
FROM debian

MAINTAINER Romana Boldt <info@boldt-webservice.de>

## install for basically needed packages
RUN apt-get update && apt-get -yq install openssh-server

VOLUME ["/outerworld"]

RUN chmod -R 0777 /outerworld
RUN umask 0000 /outerworld

EXPOSE 22 25
