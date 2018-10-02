pipeline {
    agent any
    parameters {
        string(name: "VERSION_NUMBER", defaultValue: "2.1.0", description: "The new/next version number of the project.")
        string(name: "JOOMLA_VERSION", defaultValue: "3.8.12", description: "Version of Joomla to test against")
        string(name: "VAGRANT_DIR", defaultValue: "/vms-uni2/vagrant/infrastructure/farm1/J-Tester", description: "Path to the vagrant file")
        string(name: "SMOKE_IP", defaultValue: "192.168.50.10", description: "Fix IP for smoke tester")
        string(name: "ACCEPT_1_IP", defaultValue: "192.168.51.10", description: "Fix IP for acceptance tester 1")
        string(name: "ACCEPT_2_IP", defaultValue: "192.168.52.10", description: "Fix IP for acceptance tester 2")
        string(name: "ACCEPT_3_IP", defaultValue: "192.168.53.10", description: "Fix IP for acceptance tester 3")
        string(name: "ACCEPT_4_IP", defaultValue: "192.168.54.10", description: "Fix IP for acceptance tester 4")
        string(name: "ACCEPT_5_IP", defaultValue: "192.168.55.10", description: "Fix IP for acceptance tester 5")
        string(name: "ACCEPT_6_IP", defaultValue: "192.168.56.10", description: "Fix IP for acceptance tester 6")
        string(name: "BW_ARTIFACTS_BASE", defaultValue: "/repositories/artifacts/bwpostman")

    }
    stages {
        stage('Build') {
            steps {
                echo 'Create installation package'
//				sh "ansible-playbook ${WORKSPACE}/build/playbooks/build_package.yml --extra-vars 'project_base_dir=${WORKSPACE} version_number=${params.VERSION_NUMBER} build=${BUILD_NUMBER} mb4_support=true'"
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
					dir("${BW_ARTIFACTS_BASE}/j${JOOMLA_VERSION}_bwpm${VERSION_NUMBER}/${STAGE_NAME}/logs") {
						fileOperations([
								fileCopyOperation(
										excludes: '',
										flattenFiles: false,
										includes: '*.png',
										targetLocation: "${WORKSPACE}/${STAGE_NAME}")
						])
					}

					emailext(
						body: "<p>BwPostman build failed at ${STAGE_NAME},</p><br /><p>the video is at: <a href='file://${BW_ARTIFACTS_BASE}/j${JOOMLA_VERSION}_bwpm${VERSION_NUMBER}/${STAGE_NAME}/videos/${STAGE_NAME}.mp4'>${STAGE_NAME}.mp4</a></p>",
						attachmentsPattern: "${STAGE_NAME}/*.png",
						subject:"BwPostman build failed at ${STAGE_NAME}",
						to: 'info@boldt-webservice.de'
					)
				}
			}
		}

		stage('Acceptance Tests') {
			parallel {
				stage ('accept1') {
					steps {
						echo 'Dummy'
//						sleep 60
//						bwpmAccept ("${STAGE_NAME}", params.ACCEPT_1_IP, params.VERSION_NUMBER, params.JOOMLA_VERSION)
					}
//					post {
//						always {
//							bwpmAcceptPostStepAlways ("${STAGE_NAME}")
//						}
//						failure {
//							dir("${BW_ARTIFACTS_BASE}/j${JOOMLA_VERSION}_bwpm${VERSION_NUMBER}/${STAGE_NAME}/logs") {
//								fileOperations([
//										fileCopyOperation(
//												excludes: '',
//												flattenFiles: false,
//												includes: '*.png',
//												targetLocation: "${WORKSPACE}/${STAGE_NAME}")
//								])
//							}
//
//							emailext(
//								body: "<p>BwPostman build failed at ${STAGE_NAME},</p><br /><p>the video is at: <a href='file://${BW_ARTIFACTS_BASE}/j${JOOMLA_VERSION}_bwpm${VERSION_NUMBER}/${STAGE_NAME}/videos/${STAGE_NAME}.mp4'>${STAGE_NAME}.mp4</a></p>",
//								attachmentsPattern: "${STAGE_NAME}/*.png",
//								subject:"BwPostman build failed at ${STAGE_NAME}",
//								to: 'info@boldt-webservice.de'
//							)
//						}
//					}
				}

				stage ('accept2') {
					steps {
						echo 'Dummy'
//						bwpmAccept ("${STAGE_NAME}", params.ACCEPT_2_IP, params.VERSION_NUMBER, params.JOOMLA_VERSION)
					}
//					post {
//						always {
//							bwpmAcceptPostStepAlways ("${STAGE_NAME}")
//						}
//						failure {
//							emailext body: "BwPostman build failed at ${STAGE_NAME}", subject: "BwPostman build failed at ${STAGE_NAME}", to: 'info@boldt-webservice.de'
//						}
//					}
				}

				stage ('accept3') {
					steps {
						echo 'Dummy'
//						sleep 120
//						bwpmAccept ("${STAGE_NAME}", params.ACCEPT_3_IP, params.VERSION_NUMBER, params.JOOMLA_VERSION)
					}
//					post {
//						always {
//							bwpmAcceptPostStepAlways ("${STAGE_NAME}")
//						}
//						failure {
//							emailext body: "BwPostman build failed at ${STAGE_NAME}", subject: "BwPostman build failed at ${STAGE_NAME}", to: 'info@boldt-webservice.de'
//						}
//					}
				}
			}
		}

		stage ('accept4') {
			steps {
				echo 'Dummy'
//				bwpmAccept ("accept4", params.ACCEPT_4_IP, params.VERSION_NUMBER, params.JOOMLA_VERSION)
			}
//			post {
//				always {
//					bwpmAcceptPostStepAlways ("accept4")
//				}
//				failure {
//					emailext body: "BwPostman build failed at ${STAGE_NAME}", subject: "BwPostman build failed at ${STAGE_NAME}", to: 'info@boldt-webservice.de'
//				}
//			}
		}

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
