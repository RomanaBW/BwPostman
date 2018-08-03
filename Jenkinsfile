pipeline {
  agent any
  parameters {
    string(name: "VERSION_NUMBER", defaultValue: "2.1.0", description: "The new/next version number of the project.")
    string(name: "VAGRANT_DIR", defaultValue: "/vms-uni2/vagrant/infrastructure/farm1/J-Tester", description: "Path to the vagrant file")
    string(name: "SMOKE_IP", defaultValue: "192.168.50.10", description: "Fix IP for smoke tester")
    string(name: "JOOMLA_VERSION", defaultValue: "3.8.10", description: "Version of Joomla to test against")
    string(name: "JOOMLA_VERSION_WOD", defaultValue: "3810", description: "Version of Joomla to test against")
    string(name: "BWPM_VERSION", defaultValue: "210", description: "Version of BwPostman without dots")
  }
  stages {
    stage('Build') {
      steps {
//        sh "ansible-playbook ${WORKSPACE}/build/playbooks/build_package.yml --extra-vars 'project_base_dir=${WORKSPACE} version_number=${params.VERSION_NUMBER} build=${BUILD_NUMBER} mb4_support=true'"
        echo 'Unit-Tests'
        echo 'Smoke-Tests'
        dir ('build/playbooks/') {
          sh "sudo -u romana ansible-playbook start-acceptance-tester.yml --extra-vars 'project_base_dir=/data/repositories/BwPostman/ version_number=${params.VERSION_NUMBER} bwpm_version=${params.BWPM_VERSION} joomla_version=${params.JOOMLA_VERSION_WOD} build=${BUILD_NUMBER} test_suite=smoke'"
        }
//        sshagent(credentials: ['romana']) {
        sh "ssh -o StrictHostKeyChecking=no romana@${params.SMOKE_IP} /data/do-tests.sh"
//        }
        dir ('build/playbooks/') {
          sh "sudo -u romana ansible-playbook stop-acceptance-tester.yml -v --extra-vars 'version_number=${params.VERSION_NUMBER} bwpm_version=${params.BWPM_VERSION} joomla_version=${params.JOOMLA_VERSION_WOD} test_suite=smoke'"
        }
        echo 'Akzeptanz-Tests passend zu Aenderungen'
        echo 'Validitaet von HTML'
        echo 'Code-Analyse: Testabdeckung'
        echo 'Code-Analyse: DRY'
        echo 'Code-Analyse: Komplexitaet'
        echo 'Code-Analyse: Warnungen'
        echo 'DB Rebase'
      }
    }
    stage('Acceptance Tests') {
      steps {
        echo 'Alle Akzeptanztests'
//        sh "ansible-playbook ${WORKSPACE}/build/playbooks/acceptance-tester.yml --extra-vars 'project_base_dir=${WORKSPACE} version_number=${params.VERSION_NUMBER} build=${BUILD_NUMBER} test_suite=accept1'"
      }
    }
    stage('Manual Tests') {
      steps {
        echo 'Benutzeroberflaeche'
        echo 'Worst-Case-Tests'
        echo 'nicht-funktionale Tests (Datenschutz, Sicherheit, ...)'
      }
    }
    stage('Release') {
      steps {
        echo 'Datum im Manifest aktualisieren'
        echo 'Upload auf Webserver'
        echo 'bei alter Webseite: Neues Paket und neues Objekt anlegen'
        echo 'Beschreibung auf Webseite aktualisieren'
        echo 'Handbuch im Web aktualisieren'
        echo 'Update-Server aktualisieren'
        echo 'JED aktualisieren'
      }
    }
  }
}
