#!/bin/bash


mkdir -p /tmp/xdebug
cd /tmp/xdebug

wget http://xdebug.org/files/xdebug-$XDEBUG_VERSION.tgz

tar -xvzf xdebug-$XDEBUG_VERSION.tgz

cd xdebug-$XDEBUG_VERSION

phpize

./configure

make

#cp modules/xdebug.so /usr/lib/php/20160303
cp modules/xdebug.so /usr/lib/php/20170718

#echo "zend_extension = /usr/lib/php/20160303/xdebug.so" >> /etc/php/7.1/cli/php.ini
echo "zend_extension = /usr/lib/php/20170718/xdebug.so" >> /etc/php/7.2/cli/php.ini
