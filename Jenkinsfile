pipeline {
    agent any

//	options {
//		disableConcurrentBuilds()
//	}

	parameters {
        string(name: "VERSION_NUMBER", defaultValue: "2.2.0", description: "The new/next version number of the project.")
        string(name: "JOOMLA_VERSION", defaultValue: "3.9.1", description: "Version of Joomla to test against")
        string(name: "VAGRANT_DIR", defaultValue: "/vms-uni2/vagrant/infrastructure/farm1/J-Tester", description: "Path to the vagrant file")
        string(name: "SMOKE_IP", defaultValue: "192.168.2.130", description: "Fix IP for smoke tester")
        string(name: "ACCEPT_1_IP", defaultValue: "192.168.2.131", description: "Fix IP for acceptance tester 1")
        string(name: "ACCEPT_2_IP", defaultValue: "192.168.2.132", description: "Fix IP for acceptance tester 2")
        string(name: "ACCEPT_3_IP", defaultValue: "192.168.2.133", description: "Fix IP for acceptance tester 3")
        string(name: "ACCEPT_4_IP", defaultValue: "192.168.2.134", description: "Fix IP for acceptance tester 4")
        string(name: "ACCEPT_5_IP", defaultValue: "192.168.2.135", description: "Fix IP for acceptance tester 5")
        string(name: "ACCEPT_6_IP", defaultValue: "192.168.2.136", description: "Fix IP for acceptance tester 6")
        string(name: "BW_ARTIFACTS_BASE", defaultValue: "/repositories/artifacts/bwpostman")
		string(name: "GIT_MESSAGE", defaultValue: "not specified")
    }

    stages {
        stage('Build') {
            steps {
                echo 'Create installation package'
				sh "ansible-playbook ${WORKSPACE}/build/playbooks/build_package.yml --extra-vars 'project_base_dir=${WORKSPACE} version_number=${params.VERSION_NUMBER} build=${BUILD_NUMBER} mb4_support=true'"
            }
        }

		stage('Unit-Testing') {
			steps {
				echo 'Unit-Tests'
				echo 'Validitaet von HTML'
				echo 'Code-Analyse: Testabdeckung'
				echo 'Code-Analyse: DRY'
				echo 'Code-Analyse: Komplexitaet'
				echo 'Code-Analyse: Warnungen'
			}
		}


		stage('smoke') {
			steps {
				bwpmAccept ("${STAGE_NAME}", params.SMOKE_IP, params.VERSION_NUMBER, params.JOOMLA_VERSION)
			}
			post {
				always {
					bwpmAcceptPostStepAlways ("${STAGE_NAME}")
				}
				failure {
					bwpmAcceptFailure ("${STAGE_NAME}", params.VERSION_NUMBER, params.JOOMLA_VERSION)
				}
			}
		}

		stage('Acceptance Tests 1') {
			parallel {
				stage ('accept3') {
					steps {
//						echo 'Dummy'
//						sleep 60
						bwpmAccept ("${STAGE_NAME}", params.ACCEPT_3_IP, params.VERSION_NUMBER, params.JOOMLA_VERSION)
					}
					post {
						always {
							bwpmAcceptPostStepAlways ("${STAGE_NAME}")
					}
						failure {
							bwpmAcceptFailure ("${STAGE_NAME}", params.VERSION_NUMBER, params.JOOMLA_VERSION)
						}
					}
				}
				stage ('accept4') {
					steps {
						echo 'Dummy'
//						sleep 60
//						bwpmAccept ("${STAGE_NAME}", params.ACCEPT_4_IP, params.VERSION_NUMBER, params.JOOMLA_VERSION)
					}
//					post {
//						always {
//							bwpmAcceptPostStepAlways ("${STAGE_NAME}")
//						}
//						failure {
//							bwpmAcceptFailure ("${STAGE_NAME}", params.VERSION_NUMBER, params.JOOMLA_VERSION)
//						}
//					 }
				}
			}
		}

		stage('Dev-Upload') {
			steps {
				dir("/repositories/artifacts/bwpostman/data") {
					fileOperations([
						fileCopyOperation(
							excludes: '',
							flattenFiles: false,
							includes: "pkg_bwpostman-${params.VERSION_NUMBER}.${currentBuild.number}.zip",
							targetLocation: "${WORKSPACE}/tests")
					])
				}

				dir("/repositories/artifacts/bwpostman/data") {
					fileOperations([
						fileCopyOperation(
							excludes: '',
							flattenFiles: false,
							includes: "CHANGELOG",
							targetLocation: "${WORKSPACE}/tests")
					])
				}

				script {
					GIT_MESSAGE = sh(returnStdout: true, script: "git log -n 1 --pretty=%B")
				}

				echo "tests/pkg_bwpostman-${params.VERSION_NUMBER}.${currentBuild.number}.zip"

				sshPublisher(
					publishers: [sshPublisherDesc(
					configName: 'Web Dev BwPostman',
					transfers: [sshTransfer(
						cleanRemote: false,
						excludes: '',
						execCommand: '',
						execTimeout: 120000,
						flatten: false,
						makeEmptyDirs: false,
						noDefaultExcludes: false,
						patternSeparator: '[, ]+',
						remoteDirectory: "${params.VERSION_NUMBER}.${currentBuild.number}",
						remoteDirectorySDF: false,
						removePrefix: 'tests',
						sourceFiles: "tests/CHANGELOG, tests/pkg_bwpostman-${params.VERSION_NUMBER}.${currentBuild.number}.zip"
					)],
					usePromotionTimestamp: false,
						useWorkspaceInPromotion: false,
						verbose: false
					)]
				)

				emailext(
					body: "<p>BwPostman build ${currentBuild.number} has passed smoke test, first acceptance tests and is uploaded to Boldt Webservice for testing purpose.</p><p>Last commit message: ${GIT_MESSAGE}</p>",
					subject:"BwPostman build ${currentBuild.number} successful",
					to: 'webmaster@boldt-webservice.de'
			)
//				to: 'webmaster@boldt-webservice.de, k.klostermann@t-online.de'

			}
		}

		stage('Acceptance Tests 2')
		{
			parallel
			{
				stage('accept1')
				{
					steps
					{
//						echo 'Dummy'
//						sleep 60
						bwpmAccept("${STAGE_NAME}", params.ACCEPT_1_IP, params.VERSION_NUMBER, params.JOOMLA_VERSION)
					}
					post
					{
						always
						{
							bwpmAcceptPostStepAlways("${STAGE_NAME}")
						}
						failure
						{
							bwpmAcceptFailure("${STAGE_NAME}", params.VERSION_NUMBER, params.JOOMLA_VERSION)
						}
					}
				}
//			}
//		}

//		stage('Acceptance Tests 3')
//		{
//			parallel
//			{
				stage('accept2')
				{
					steps
					{
//						echo 'Dummy'
						bwpmAccept("${STAGE_NAME}", params.ACCEPT_2_IP, params.VERSION_NUMBER, params.JOOMLA_VERSION)
					}
					post
					{
						always
						{
							bwpmAcceptPostStepAlways("${STAGE_NAME}")
						}
						failure
						{
							bwpmAcceptFailure("${STAGE_NAME}", params.VERSION_NUMBER, params.JOOMLA_VERSION)
						}
					}
				}
			}
		}

//		stage ('accept5') {
//			steps {
//				echo 'Dummy'
//				bwpmAccept ("accept5", params.ACCEPT_5_IP, params.VERSION_NUMBER, params.JOOMLA_VERSION)
//			}
//			post {
//				always {
//					bwpmAcceptPostStepAlways ("accept5")
//				}
//				failure {
//					bwpmAcceptFailure ("${STAGE_NAME}", params.VERSION_NUMBER, params.JOOMLA_VERSION)
//				}
//			}
//		}

		stage('Pre-Release') {
			steps {
				echo 'Upload auf Github Master-Branch'
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
				echo 'Upload auf Webserver'
				echo 'bei alter Webseite: Neues Paket und neues Objekt anlegen'
				echo 'Update-Server aktualisieren'
				echo 'JED aktualisieren'
				echo 'Upload auf Github Release-Branch'
			}
		}

		stage('Post-Release') {
			steps {
				echo 'Beschreibung auf Webseite aktualisieren'
				echo 'Handbuch im Web aktualisieren'
				echo 'PDF-Handbuch aktualisieren und Upload'
			}
        }
    }
}
