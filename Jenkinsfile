pipeline {
  agent any
  parameters {
    string(name: "VERSION_NUMBER", defaultValue: "2.1.0", description: "The new/next version number of the project.")
    string(name: "JOOMLA_VERSION", defaultValue: "3.8.10", description: "Version of Joomla to test against")
    string(name: "VAGRANT_DIR", defaultValue: "/vms-uni2/vagrant/infrastructure/farm1/J-Tester", description: "Path to the vagrant file")
    string(name: "SMOKE_IP", defaultValue: "192.168.50.10", description: "Fix IP for smoke tester")
    string(name: "ACCEPT_1_IP", defaultValue: "192.168.51.10", description: "Fix IP for acceptance tester 1")
    string(name: "ACCEPT_2_IP", defaultValue: "192.168.52.10", description: "Fix IP for acceptance tester 2")
    string(name: "ACCEPT_3_IP", defaultValue: "192.168.53.10", description: "Fix IP for acceptance tester 3")
    string(name: "ACCEPT_4_IP", defaultValue: "192.168.54.10", description: "Fix IP for acceptance tester 4")
    string(name: "ACCEPT_5_IP", defaultValue: "192.168.55.10", description: "Fix IP for acceptance tester 5")
    string(name: "ACCEPT_6_IP", defaultValue: "192.168.56.10", description: "Fix IP for acceptance tester 6")
  }
  stages {
    stage('Build') {
      steps {
        echo 'Create installation package'
//        sh "ansible-playbook ${WORKSPACE}/build/playbooks/build_package.yml --extra-vars 'project_base_dir=${WORKSPACE} version_number=${params.VERSION_NUMBER} build=${BUILD_NUMBER} mb4_support=true'"

        echo 'Unit-Tests'

        echo 'start Smoke-Tester'
        dir ('build/playbooks/') {
          sh "sudo -u romana ansible-playbook start-acceptance-tester.yml --extra-vars 'project_workspace=${WORKSPACE} project_base_dir=/data/repositories/BwPostman/ version_number=${params.VERSION_NUMBER} joomla_version=${params.JOOMLA_VERSION} build=${BUILD_NUMBER} test_suite=smoke'"
        }
        echo 'do Smoke-Tests'
        sh "ssh -o StrictHostKeyChecking=no jenkins@${params.SMOKE_IP} /data/do-tests.sh"
        echo 'stop Smoke-Tester'
        dir ('build/playbooks/') {
          sh "sudo -u romana ansible-playbook stop-acceptance-tester.yml -v --extra-vars 'version_number=${params.VERSION_NUMBER} joomla_version=${params.JOOMLA_VERSION} test_suite=smoke'"
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
      parallel {
        stage ('Acceptance Tester 1') {
          steps {
            catchError {
              echo 'start acceptance tester 1'
              dir ('build/playbooks/') {
                sh "sudo -u romana ansible-playbook start-acceptance-tester.yml --extra-vars 'project_base_dir=/data/repositories/BwPostman/ version_number=${params.VERSION_NUMBER} joomla_version=${params.JOOMLA_VERSION} build=${BUILD_NUMBER} test_suite=accept1'"
              }
              echo 'do Acceptance Tests Part 1'
              sh "ssh -o StrictHostKeyChecking=no jenkins@${params.ACCEPT_1_IP} /data/do-tests.sh"
              echo 'stop acceptance tester 1'
              dir ('build/playbooks/') {
                sh "sudo -u romana ansible-playbook stop-acceptance-tester.yml -v --extra-vars 'version_number=${params.VERSION_NUMBER} joomla_version=${params.JOOMLA_VERSION} test_suite=accept1'"
              }
            }
            echo currentBuild.result
          }
        }
        stage ('Acceptance Tester 2') {
          steps {
            catchError {
              echo 'start acceptance tester 2'
              dir ('build/playbooks/') {
                sh "sudo -u romana ansible-playbook start-acceptance-tester.yml --extra-vars 'project_base_dir=/data/repositories/BwPostman/ version_number=${params.VERSION_NUMBER} joomla_version=${params.JOOMLA_VERSION} build=${BUILD_NUMBER} test_suite=accept2'"
              }
              echo 'do Acceptance Tests Part 2'
              sh "ssh -o StrictHostKeyChecking=no jenkins@${params.ACCEPT_2_IP} /data/do-tests.sh"
              echo 'stop acceptance tester 2'
              dir ('build/playbooks/') {
                sh "sudo -u romana ansible-playbook stop-acceptance-tester.yml -v --extra-vars 'version_number=${params.VERSION_NUMBER} joomla_version=${params.JOOMLA_VERSION} test_suite=accept2'"
              }
            }
            echo currentBuild.result
          }
        }
        stage ('Acceptance Tester 3') {
          steps {
            catchError {
              echo 'start acceptance tester 3'
              dir ('build/playbooks/') {
                sh "sudo -u romana ansible-playbook start-acceptance-tester.yml --extra-vars 'project_base_dir=/data/repositories/BwPostman/ version_number=${params.VERSION_NUMBER} joomla_version=${params.JOOMLA_VERSION} build=${BUILD_NUMBER} test_suite=accept3'"
              }
              echo 'do Acceptance Tests Part 3'
              sh "ssh -o StrictHostKeyChecking=no jenkins@${params.ACCEPT_3_IP} /data/do-tests.sh"
              echo 'stop acceptance tester 3'
              dir ('build/playbooks/') {
                sh "sudo -u romana ansible-playbook stop-acceptance-tester.yml -v --extra-vars 'version_number=${params.VERSION_NUMBER} joomla_version=${params.JOOMLA_VERSION} test_suite=accept3'"
              }
            }
            echo currentBuild.result
          }
        }
        stage ('Acceptance Tester 4') {
          steps {
            catchError {
              echo 'start acceptance tester 4'
              dir ('build/playbooks/') {
                sh "sudo -u romana ansible-playbook start-acceptance-tester.yml --extra-vars 'project_base_dir=/data/repositories/BwPostman/ version_number=${params.VERSION_NUMBER} joomla_version=${params.JOOMLA_VERSION} build=${BUILD_NUMBER} test_suite=accept4'"
              }
              echo 'do Acceptance Tests Part 4'
              sh "ssh -o StrictHostKeyChecking=no jenkins@${params.ACCEPT_4_IP} /data/do-tests.sh"
              echo 'stop acceptance tester 4'
              dir ('build/playbooks/') {
                sh "sudo -u romana ansible-playbook stop-acceptance-tester.yml -v --extra-vars 'version_number=${params.VERSION_NUMBER} joomla_version=${params.JOOMLA_VERSION} test_suite=accept4'"
              }
            }
            echo currentBuild.result
          }
        }
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
