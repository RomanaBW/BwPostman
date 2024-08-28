#!/bin/bash
#
# This is part of step ?? to nail down the created images with installed and adjusted Joomla, preconfigured for BwPostman
#
# Tag is php version and Joomla version (with dots), separated by a hyphen
#

PHP_VERSION=8.1.0
JOOMLA_VERSION=5.1.3

docker commit -a 'Romana Boldt info@boldt-webservice.de' push-web universe3:5000/romana/bwpm-configured-files-$PHP_VERSION:$JOOMLA_VERSION
docker commit -a 'Romana Boldt info@boldt-webservice.de' push-db universe3:5000/romana/bwpm-configured-tables-$PHP_VERSION:$JOOMLA_VERSION

docker commit -a 'Romana Boldt info@boldt-webservice.de' push-web universe3:5000/romana/bwpm-configured-files-$PHP_VERSION:latest
docker commit -a 'Romana Boldt info@boldt-webservice.de' push-db universe3:5000/romana/bwpm-configured-tables-$PHP_VERSION:latest

#docker push universe3:5000/romana/bwpm-configured-files:$PHP_VERSION-$JOOMLA_VERSION
#docker push universe3:5000/romana/bwpm-configured-tables:$PHP_VERSION-$JOOMLA_VERSION

docker push universe3:5000/romana/bwpm-configured-files-$PHP_VERSION:$JOOMLA_VERSION
docker push universe3:5000/romana/bwpm-configured-tables-$PHP_VERSION:$JOOMLA_VERSION

docker push universe3:5000/romana/bwpm-configured-files-$PHP_VERSION:latest
docker push universe3:5000/romana/bwpm-configured-tables-$PHP_VERSION:latest
