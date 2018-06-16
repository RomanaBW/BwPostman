pipeline {
  agent any
  stages {
    stage('Build') {
      steps {
        echo 'Unit-Tests'
        echo "Workspace: $WORKSPACE"
        echo "Build: $BUILD_NUMBER"
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
