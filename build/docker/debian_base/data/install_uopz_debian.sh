#!/bin/bash

mkdir -p /tmp/uopz
cd /tmp/uopz

wget http://pecl.php.net/get/uopz-$UOPZ_VERSION.tgz

tar -xvzf uopz-$UOPZ_VERSION.tgz

cd uopz-$UOPZ_VERSION

phpize

./configure

make

#cp modules/uopz.so /usr/lib/php/20160303
cp modules/uopz.so /usr/lib/php/20170718

#echo "extension = /usr/lib/php/20160303/uopz.so" >> /etc/php/7.1/cli/php.ini
echo "extension = /usr/lib/php/20170718/uopz.so" >> /etc/php/7.2/cli/php.ini
