#!/bin/bash

docker compose -f compose-ide.yml -f ../../compose.bwpm.yml $1 $2
