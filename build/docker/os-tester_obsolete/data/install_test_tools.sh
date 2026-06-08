#!/bin/bash

# install test tools (recently phpUnit, dbUnit, Mockery)

cd $COMPOSER_DIR

php composer.phar install
