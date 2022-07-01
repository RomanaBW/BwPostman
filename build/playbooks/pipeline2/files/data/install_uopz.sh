#!/bin/bash

mkdir -p /tmp/uopz
cd /tmp/uopz

wget https://pecl.php.net/get/uopz-$UOPZ_VERSION.tgz

tar -xvzf uopz-$UOPZ_VERSION.tgz

cd uopz-$UOPZ_VERSION

phpize

./configure

make

cp modules/uopz.so /usr/lib64/php7/extensions

echo "extension = /usr/lib64/php7/extensions/uopz.so" >> /etc/php7/cli/php.ini
