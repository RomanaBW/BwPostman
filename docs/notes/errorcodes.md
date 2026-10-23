# Fehlercodes, wie sie im (alten) Vorgehen bei der Wiederherstellung verwendet werden:

## Check
| step | occurrence                                      | note                                               |
|------|-------------------------------------------------|----------------------------------------------------|
| 1    | $this→createRestorePoint($sessionContent, 1300) |                                                    |
| 2    | $this→getNeededTables($session, 1320);          |                                                    |
| 3    | $this→getInstalledTableNames($session, 1330);   |                                                    |
| 4    | $this→convertTableNames($session, 1340);        |                                                    |
| 5    | $this→checkTableColumns($session, 1350);        |                                                    |
| 6    | throw new BwException($error, 1360);            | $this->checkAssetAndUserIds($sessionContent, '…'); |
| 7    | throw new BwException($error, 1370);            | $this->deleteRestorePoint($sessionContent);        |

## Restore
| step | occurrence                                                  | note                                                       |
|------|-------------------------------------------------------------|------------------------------------------------------------|
| 1    | throw new BwException($error, 1010);                        | !is_array($table_names)                                    |
|      | throw new BwException($error, 1011);                        | $model->parseTablesData($file)                             |
| 2    | throw new BwException($error, 1020);                        | $model->outputGeneralInformation();                        |
| 3    | $this->getNeededTables($session, 1030);                     |                                                            |
| 4    | throw new BwException($error, 1040);                        | $assetGroupsProcessed === false                            |
|      | throw new BwException($error, 1041);                        | $model->healAssetUserGroups(…)                             |
| 5    | $this->createRestorePoint($sessionContent, 1050);           |                                                            |
| 6    | throw new BwException($error, 1060);                        | $subAssetsDeleted === false                                |
|      | throw new BwException($error, 1061);                        | $assetTableHealed === false                                |
|      | throw new BwException($error, 1062);                        | $model->deleteSubAssets(); oder $model->healAssetsTable(); |
| 7    | throw new BwException($error, 1070);                        | $tablesRenewed !== true                                    |
|      | throw new BwException($error, 1071);                        | $model->anewBwPostmanTables($table_names);                 |
| 8    | $this->getInstalledTableNames($session, 1080);              |                                                            |
| 9    | $this->convertTableNames($session, 1090);                   |                                                            |
| 10   | $this->checkTableColumns($session, 1100, $versionOfBackup); |                                                            |
|      | throw new BwException($error, 1101);                        |                                                            |
| 11   | throw new BwException($error, 1110);                        | $this->checkAssetAndUserIds($sessionContent, '…');         |

