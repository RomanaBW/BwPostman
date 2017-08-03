#!/bin/bash

#PHP_EXTENSION_DIR=/usr/lib/php5/20131226 for jessie
PHP_EXTENSION_DIR=/usr/lib/php5/20100525

mkdir -p /tmp/xdebug
cd /tmp/xdebug

wget http://xdebug.org/files/xdebug-$XDEBUG_VERSION.tgz

tar -xvzf xdebug-$XDEBUG_VERSION.tgz

cd xdebug-$XDEBUG_VERSION

phpize

./configure

make

cp modules/xdebug.so $PHP_EXTENSION_DIR

echo "zend_extension = $PHP_EXTENSION_DIR/xdebug.so" >> /etc/php5/cli/php.ini
