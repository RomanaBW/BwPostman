#!/bin/bash

COMPOSER_DATA_DIR=/data/composer

mkdir -p $COMPOSER_DIR
cd $COMPOSER_DIR

curl -sS https://getcomposer.org/installer | php

cp $COMPOSER_DATA_DIR/composer.json $COMPOSER_DIR
