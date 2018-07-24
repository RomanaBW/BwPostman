pipeline {
  agent any
  parameters {
    string(name: "VERSION_NUMBER", defaultValue: "2.1.0", description: "The new/next version number of the project.")
  }
  stages {
    stage('Build') {
      steps {
        echo 'Unit-Tests'
        echo 'Smoke-Tests'
        sh "cd ${WORKSPACE}/build/playbooks"
        sh "ansible-playbook acceptance-tester.yml -vvvv -i ./inventory/hosts -u romana --extra-vars 'project_base_dir=${WORKSPACE} version_number=${params.VERSION_NUMBER} build=${BUILD_NUMBER} test_suite=smoke'"
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
