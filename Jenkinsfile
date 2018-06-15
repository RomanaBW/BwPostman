pipeline {
  agent any
  stages {
    stage('Build') {
      steps {
        echo 'Unit-Tests'
        echo 'Smoke-Tests'
        echo 'Akzeptanz-Tests passend zu Änderungen'
        echo 'Validität von HTML'
        echo 'Code-Analyse: Testabdeckung'
        echo 'Code-Analyse: DRY'
        echo 'Code-Analyse: Komplexität'
        echo 'Code-Analyse: Warnungen'
        echo 'DB Rebase'
        echo 'Versionsnummer einbauen'
        echo 'BUildnummer einbauen'
        echo 'Installationspaket bauen'
      }
    }
    stage('AcceptanceTests') {
      steps {
        echo 'Alle Akzeptanztests'
      }
    }
    stage('Manual Tests') {
      steps {
        echo 'Benutzeroberfl�che'
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