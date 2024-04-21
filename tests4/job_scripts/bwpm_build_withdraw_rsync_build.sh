#!/bin/bash
# Synchronize current build files to repositories without overwriting user, group, permissions at target
#
# To reach this I have to use sudo, otherwise target permissions have to be set to 0777, which breaks Joomla

src_dir="${1}"
target_dir="${2}"

options="-rlqc"

dirs[1]='playbooks'
#dirs[2]='acceptance'
#dirs[3]='job_scripts'

for part in "${dirs[@]}"; do
  sudo rsync ${options} "${src_dir}/${part}/" "${target_dir}/${part}"
  sudo chown -R romana "${target_dir}/${part}"
  sudo chgrp -R users "${target_dir}/${part}"
done

