#!/bin/bash

#PHP_EXTENSION_DIR=/usr/lib/php5/20131226 for jessie
PHP_EXTENSION_DIR=/usr/lib/php5/20100525

mkdir -p /tmp/uopz
cd /tmp/uopz

wget http://pecl.php.net/get/uopz-$UOPZ_VERSION.tgz

tar -xvzf uopz-$UOPZ_VERSION.tgz

cd uopz-$UOPZ_VERSION

phpize

./configure

make

cp modules/uopz.so $PHP_EXTENSION_DIR

echo "extension = $PHP_EXTENSION_DIR/uopz.so" >> /etc/php5/cli/php.ini
