# Welche Container verwenden welche Images und Volumes?


| Wer            | verwendet        | baut      | Volumes?                                                                                                     |
|----------------|------------------|-----------|--------------------------------------------------------------------------------------------------------------|
| Smoke          | bwpm-configured- | bwpm_enh_ | (eher nicht)                                                                                                 |
| Acceptance ff. | bwpm_enh_        |           | sicher nicht                                                                                                 |
| Dev            | bwpm-configured- |           | bwpm_dev_current_db                                                                                          |
|                |                  |           | externe Konfiguration /home/romana/PhpstormProjects/BwPostman_J4/build/docker/dev/J62x/configuration.php     |
|                |                  |           | und viele andere,bindet BwPostman aus der IDE ein                                                            |
| Test           | bwpm-configured- |           | bwpm_test_db_62                                                                                              |
|                |                  |           | bwpm-test-joomla_62                                                                                          |
|                |                  |           | externe Konfiguration /home/romana/PhpstormProjects/BwPostman_J4/build/docker/test_current/configuration.php |
|                |                  |           | externe Logs home/romana/PhpstormProjects/BwPostman_J4/build/docker/test_current/logs/bwpostman              |
| Fast           | bwpm_enh_        |           | bwpm_fast_current_db                                                                                         |
|                |                  |           | /home/container-files/j6current                                                                              |
|                |                  |           | externe Konfiguration /home/romana/PhpstormProjects/BwPostman_J4/build/docker/dev/J62x/configuration.php     |
|                |                  |           | und viele andere,bindet BwPostman aus der IDE ein                                                            |

## bwpm-configured-
Diese Images werden durch die Playbooks create-current.yml und create-previous.yml erzeugt.

Diese Playbooks werden üblicherweise dann laufen gelassen, wenn eine neue Version von Joomla veröffentlicht wurde oder
es eine neue Version von BwPostman gibt. Sie sind jeweils ein Sammel-Playbook, das die einzelnen Playbooks in der nötigen 
Reihenfolge ausführt. Die einzelnen Playbooks wiederum führen die erforderlichen Schritte für die Erstellung 
der diversen Test-Umgebungen durch. Wichtig sind hier vor allem die Compose-Dateien, die die Umgebungen konfigurieren.

## bwpm_enh_
Diese Images werden tatsächlich nur durch die Pipeline erzeugt. Bevor sie verwendet werden können, muss also die Pipeline 
einmal ausgeführt werden und Smoke muss grün sein.
