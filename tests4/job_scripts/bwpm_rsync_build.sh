#!/bin/bash
# Synchronize current files to test containers without overwriting user, group, permissions at target
#
# To reach this I have to use sudo, otherwise target permissions have to be set to 0777, which breaks Joomla

#
# @ToDo: synchronize plugin B2S (dirs[19]), but only if VirtueMart is installed

src_dir="${1}"
target_dir="${2}"

options="-rlqc"

dirs[1]='administrator/components/com_bwpostman'
dirs[2]='components/com_bwpostman'
dirs[3]='images/com_bwpostman'
dirs[4]='media/com_bwpostman'
dirs[5]='modules/mod_bwpostman'
dirs[6]='media/mod_bwpostman'
dirs[7]='modules/mod_bwpostman_overview'
dirs[8]='media/mod_bwpostman_overview'
dirs[9]='media/plg_system_bwpm_user2subscriber'
dirs[10]='media/plg_bwpostman_footerusedmailinglists'
dirs[11]='plugins/bwpostman'
dirs[12]='plugins/quickicon/bwpostman'
dirs[13]='plugins/system/bw_libregister'
dirs[14]='plugins/system/bwpm_mediaoverride'
dirs[15]='plugins/system/bwpm_user2subscriber'
dirs[16]='plugins/system/bwpm_useraccount'
dirs[17]='plugins/system/bwtestmode'
dirs[18]='plugins/system/bwtests'
#dirs[19]='plugins/vmfields'

item[1]='administrator/language/de-DE/de-DE.com_bwpostman.ini'
item[2]='administrator/language/de-DE/de-DE.com_bwpostman.sys.ini'
item[3]='administrator/language/de-DE/de-DE.plg_bwpostman_demo.ini'
item[4]='administrator/language/de-DE/de-DE.plg_bwpostman_demo.sys.ini'
item[5]='administrator/language/de-DE/de-DE.plg_bwpostman_footerusedmailinglists.ini'
item[6]='administrator/language/de-DE/de-DE.plg_bwpostman_footerusedmailinglists.sys.ini'
item[7]='administrator/language/de-DE/de-DE.plg_bwpostman_personalize.ini'
item[8]='administrator/language/de-DE/de-DE.plg_bwpostman_personalize.sys.ini'
item[9]='administrator/language/de-DE/de-DE.plg_bwtests.ini'
item[10]='administrator/language/de-DE/de-DE.plg_bwtests.sys.ini'
item[11]='administrator/language/de-DE/de-DE.plg_system_bw_libregister.ini'
item[12]='administrator/language/de-DE/de-DE.plg_system_bw_libregister.sys.ini'
item[13]='administrator/language/de-DE/de-DE.plg_system_bwpm_mediaoverride.sys.ini'
item[14]='administrator/language/de-DE/de-DE.plg_system_bwpm_user2subscriber.ini'
item[15]='administrator/language/de-DE/de-DE.plg_system_bwpm_user2subscriber.sys.ini'
item[16]='administrator/language/de-DE/de-DE.plg_vmuserfield_bwpm_buyer2subscriber.ini'
item[17]='administrator/language/de-DE/de-DE.plg_vmuserfield_bwpm_buyer2subscriber.sys.ini'
item[18]='administrator/language/en-GB/en-GB.com_bwpostman.ini'
item[19]='administrator/language/en-GB/en-GB.com_bwpostman.sys.ini'
item[20]='administrator/language/en-GB/en-GB.plg_bwpostman_demo.ini'
item[21]='administrator/language/en-GB/en-GB.plg_bwpostman_demo.sys.ini'
item[22]='administrator/language/en-GB/en-GB.plg_bwpostman_footerusedmailinglists.ini'
item[23]='administrator/language/en-GB/en-GB.plg_bwpostman_footerusedmailinglists.sys.ini'
item[24]='administrator/language/en-GB/en-GB.plg_bwpostman_personalize.ini'
item[25]='administrator/language/en-GB/en-GB.plg_bwpostman_personalize.sys.ini'
item[26]='administrator/language/en-GB/en-GB.plg_bwtests.ini'
item[27]='administrator/language/en-GB/en-GB.plg_bwtests.sys.ini'
item[28]='administrator/language/en-GB/en-GB.plg_system_bw_libregister.ini'
item[29]='administrator/language/en-GB/en-GB.plg_system_bw_libregister.sys.ini'
item[30]='administrator/language/en-GB/en-GB.plg_system_bwpm_mediaoverride.sys.ini'
item[31]='administrator/language/en-GB/en-GB.plg_system_bwpm_user2subscriber.ini'
item[32]='administrator/language/en-GB/en-GB.plg_system_bwpm_user2subscriber.sys.ini'
item[33]='administrator/language/en-GB/en-GB.plg_system_bwpm_useraccount.ini'
item[34]='administrator/language/en-GB/en-GB.plg_system_bwpm_useraccount.sys.ini'
item[35]='administrator/language/en-GB/en-GB.plg_vmuserfield_bwpm_buyer2subscriber.ini'
item[36]='administrator/language/en-GB/en-GB.plg_vmuserfield_bwpm_buyer2subscriber.sys.ini'
item[37]='language/de-DE/de-DE.com_bwpostman.ini'
item[38]='language/de-DE/de-DE.pkg_bwtimecontrol.sys.ini'
item[39]='language/de-DE/de-DE.mod_bwpostman.sys.ini'
item[40]='language/de-DE/de-DE.mod_bwpostman_overview.ini'
item[41]='language/de-DE/de-DE.mod_bwpostman_overview.sys.ini'
item[42]='language/en-GB/en-GB.com_bwpostman.ini'
item[43]='language/en-GB/en-GB.pkg_bwtimecontrol.sys.ini'
item[44]='language/en-GB/en-GB.mod_bwpostman.sys.ini'
item[45]='language/en-GB/en-GB.mod_bwpostman_overview.ini'
item[46]='language/en-GB/en-GB.mod_bwpostman_overview.sys.ini'

for part in "${dirs[@]}"; do
  sudo rsync ${options} "${src_dir}/${part}/" "${target_dir}/${part}"
  sudo chown -R www-data "${target_dir}/${part}"
  sudo chgrp -R video "${target_dir}/${part}"
done

for part in "${item[@]}"; do
  sudo rsync ${options} "${src_dir}/${part}" "${target_dir}/${part}"
  sudo chown -R www-data "${target_dir}/${part}"
  sudo chgrp -R video "${target_dir}/${part}"
done
