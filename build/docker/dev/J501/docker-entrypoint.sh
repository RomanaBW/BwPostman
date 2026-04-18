#!/bin/bash
set -e

if [ -d /var/www/html/tmp/bwpm_setup ];
  then
    echo "file to adjust db is present"
  else
    echo "file to adjust db does not exist"
fi


if [ -n "$JOOMLA_DB_PASSWORD_FILE" ] && [ -f "$JOOMLA_DB_PASSWORD_FILE" ]; then
        JOOMLA_DB_PASSWORD=$(cat "$JOOMLA_DB_PASSWORD_FILE")
fi

if [ -n "$JOOMLA_ADMIN_PASSWORD_FILE" ] && [ -f "$JOOMLA_ADMIN_PASSWORD_FILE" ]; then
        JOOMLA_ADMIN_PASSWORD=$(cat "$JOOMLA_ADMIN_PASSWORD_FILE")
fi

WORK_DIR=$(pwd)

echo >&2
echo >&2 "========================================================================"
echo >&2

if [ -f /var/www/html/administrator/cache/autoload_psr4.php ];
  then
  # Clear class cache of Joomla
  echo >&2 "Clear class cache of Joomla…"
  echo >&2

  rm /var/www/html/administrator/cache/autoload_psr4.php

  echo >&2 "Class cache of Joomla cleared."
  echo >&2
fi

# Do some settings for BwPostman if not already done
if [ ! -d /var/www/html/tmp/bwpm_setup ];
  then
  echo >&2 "Replace installation Joomla configuration with one for this project..."
  cp /usr/src/files/configuration.php configuration.php

  echo >&2 "Joomla configuration rewritten."
  echo >&2

  echo >&2 "Add users and groups, map users to groups for BwPostman…"
  echo >&2

  echo >&2 "DB-Params: $JOOMLA_DB_HOST $JOOMLA_DB_USER $JOOMLA_DB_PASSWORD $JOOMLA_DB_NAME $JOOMLA_DB_TYPE"
  echo >&2

  php /usr/src/files/adjustdb_bwpm.php "$JOOMLA_DB_HOST" "$JOOMLA_DB_USER" "$JOOMLA_DB_PASSWORD" "$JOOMLA_DB_NAME" "$JOOMLA_DB_TYPE"

  echo >&2 "…BwPostman user groups added, users added, mapping users to groups adjusted."
  mkdir /var/www/html/tmp/bwpm_setup

  echo >&2 "Remove file adjustdb_bwpm.php if work is done…"
  echo >&2

  rm /usr/src/files/adjustdb_bwpm.php

  echo >&2 "…file adjustdb_bwpm.php removed."
  echo >&2
fi

  # Ensure files of Joomla are writable
  echo >&2 "Ensure files of Joomla are writable…"

  chown -R www-data:www-data ./

  echo >&2 "Files are writable."
  echo >&2


echo >&2
echo >&2 "The Joomla installation and BwPostman should now work as expected."
echo >&2
echo >&2 "========================================================================"
echo >&2

exec "$@"
