pipeline {
  agent any
  parameters {
    string(name: "VERSION_NUMBER", defaultValue: "2.1.0", description: "The new/next version number of the project.")
  }
  stages {
    stage('Build') {
      steps {
        echo 'current path: ${pwd()}'
        sh 'ansible-playbook ./build/playbooks/build_package.yml --extra-vars 'workspace=${WORKSPACE} version_number=${params.VERSION_NUMBER} build=${BUILD_NUMBER} mb4_support=true'
        echo 'Unit-Tests'
        echo "Workspace: ${WORKSPACE}"
        echo "Build: ${BUILD_NUMBER}"
        echo "Versionsnummer: ${params.VERSION_NUMBER}"
        echo 'Smoke-Tests'
        echo 'Akzeptanz-Tests passend zu Aenderungen'
        echo 'Validitaet von HTML'
        echo 'Code-Analyse: Testabdeckung'
        echo 'Code-Analyse: DRY'
        echo 'Code-Analyse: Komplexitaet'
        echo 'Code-Analyse: Warnungen'
        echo 'DB Rebase'
        echo 'Versionsnummer einbauen'
        echo 'Buildnummer einbauen'
        echo 'Installationspaket bauen'
      }
    }
    stage('Acceptance Tests') {
      steps {
        echo 'Alle Akzeptanztests'
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
