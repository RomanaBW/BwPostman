#!/bin/bash
#
# This is part of step 3 to nail down the created images with installed and adjusted Joomla
#
# Tag is php version and Joomla version (with dots), separated by a hyphen
#

docker commit -a 'Romana Boldt info@boldt-webservice.de' bwpm-config-current-joomla universe3:5000/romana/bwpm-base-files:8.1.0-5.2.2
docker commit -a 'Romana Boldt info@boldt-webservice.de' bwpm-config-current-db universe3:5000/romana/bwpm-base-tables:8.1.0-5.2.2
