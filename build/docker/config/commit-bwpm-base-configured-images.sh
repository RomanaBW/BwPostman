#!/bin/bash
#
# This is part of step ?? to nail down the created images with installed and adjusted Joomla, preconfigured for BwPostman
#
# Tag is php version and Joomla version (with dots), separated by a hyphen
#

PHP_VERSION=8.1.0
JOOMLA_VERSION=5.1.1

docker commit -a 'Romana Boldt info@boldt-webservice.de' bwpm-web universe3:5000/romana/bwpm-base-files:$PHP_VERSION-$JOOMLA_VERSION
docker commit -a 'Romana Boldt info@boldt-webservice.de' bwpm-db universe3:5000/romana/bwpm-base-tables:$PHP_VERSION-$JOOMLA_VERSION
