#!/bin/bash
#
# This is part of step 3 to nail down the created images with installed and adjusted Joomla
#
# Tag is php version and Joomla version (with dots), separated by a hyphen
#

docker commit -a 'Romana Boldt info@boldt-webservice.de' bwpm-config-previous-joomla universe3:5000/romana/bwpm-base-files:7.4.0-4.4.9
docker commit -a 'Romana Boldt info@boldt-webservice.de' bwpm-config-previous-db universe3:5000/romana/bwpm-base-tables:7.4.0-4.4.9
