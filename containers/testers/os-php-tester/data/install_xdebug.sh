#!/bin/bash

mkdir -p /tmp/xdebug
cd /tmp/xdebug

wget http://xdebug.org/files/xdebug-$XDEBUG_VERSION.tgz

tar -xvzf xdebug-$XDEBUG_VERSION.tgz

cd xdebug-$XDEBUG_VERSION

phpize

./configure

make

cp modules/xdebug.so /usr/lib64/php5/extensions

echo "zend_extension = /usr/lib64/php5/extensions/xdebug.so" >> /etc/php5/cli/php.ini
