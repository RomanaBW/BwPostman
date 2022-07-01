#!/bin/bash


mkdir -p /tmp/xdebug
cd /tmp/xdebug

wget https://xdebug.org/files/xdebug-$XDEBUG_VERSION.tgz

tar -xvzf xdebug-$XDEBUG_VERSION.tgz

cd xdebug-$XDEBUG_VERSION

phpize

./configure

make

cp modules/xdebug.so /usr/lib64/php7/extensions

#echo "zend_extension = /usr/lib64/php7/extensions/xdebug.so" >> /etc/php7/cli/php.ini
