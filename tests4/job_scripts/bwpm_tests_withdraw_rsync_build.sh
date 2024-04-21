#!/bin/bash
# Synchronize current tests to test containers without overwriting user, group, permissions at target
#
# To reach this I have to use sudo, otherwise target permissions have to be set to 0777, which breaks Joomla

src_dir="${1}"
target_dir="${2}"

runner_target_dir="/vms/dockers/containers/testers/os-data"
runner_name="bwpm_test_runner_withdraw.sh"

options="-rlqc"

dirs[1]='_support'
dirs[2]='acceptance'
dirs[3]='job_scripts'

for part in "${dirs[@]}"; do
  sudo rsync ${options} "${src_dir}/${part}/" "${target_dir}/${part}"
  sudo chown -R romana "${target_dir}/${part}"
  sudo chgrp -R users "${target_dir}/${part}"
done

sudo rsync ${options} "${src_dir}/job_scripts/${runner_name}" "${runner_target_dir}/${runner_name}"
  sudo chown -R romana "${runner_target_dir}/${runner_name}"
  sudo chgrp -R users "${runner_target_dir}/${runner_name}"
  sudo chmod -R 0775 "${runner_target_dir}/${runner_name}"

