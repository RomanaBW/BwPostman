pipeline {
    agent any

//	options {
//		disableConcurrentBuilds()
//	}

    stages {
        stage('Build') {
			steps {
				script
				{
					VERSION_NUMBER = "2.3.0"
					JOOMLA_VERSION = "3.9.3"
					VAGRANT_DIR = "/vms-uni2/vagrant/infrastructure/farm1/J-Tester"
					BW_ARTIFACTS_BASE = "/repositories/artifacts/bwpostman"
					SMOKE_IP = "192.168.2.130"
					ACCEPT_1_IP = "192.168.2.131"
					ACCEPT_2_IP = "192.168.2.132"
					ACCEPT_3_IP = "192.168.2.133"
					ACCEPT_4_IP = "192.168.2.134"
					ACCEPT_5_IP = "192.168.2.135"
					ACCEPT_6_IP = "192.168.2.131"
					GIT_MESSAGE = "not specified"
				}

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


		// stage('smoke') {
		// 	steps {
		// 		bwpmAccept ("${STAGE_NAME}", params.SMOKE_IP, params.VERSION_NUMBER, params.JOOMLA_VERSION)
		// 	}
		// 	post {
		// 		always {
		// 			bwpmAcceptPostStepAlways ("${STAGE_NAME}")
		// 		}
		// 		failure {
		// 			bwpmAcceptFailure ("${STAGE_NAME}", params.VERSION_NUMBER, params.JOOMLA_VERSION)
		// 		}
		// 	}
		// }

// 		stage('accept1') {
// 			steps
// 			{
// //				echo 'Dummy'
// //				sleep 60
// 				bwpmAccept("${STAGE_NAME}", params.ACCEPT_1_IP, params.VERSION_NUMBER, params.JOOMLA_VERSION)
// 			}
// 			post
// 			{
// 				always
// 				{
// 					bwpmAcceptPostStepAlways("${STAGE_NAME}")
// 				}
// 				failure
// 				{
// 					bwpmAcceptFailure("${STAGE_NAME}", params.VERSION_NUMBER, params.JOOMLA_VERSION)
// 				}
// 			}
// 		}

// 		stage('accept2')
// 		{
// 			steps
// 			{
// //				echo 'Dummy'
// 				bwpmAccept("${STAGE_NAME}", params.ACCEPT_2_IP, params.VERSION_NUMBER, params.JOOMLA_VERSION)
// 			}
// 			post
// 			{
// 				always
// 				{
// 					bwpmAcceptPostStepAlways("${STAGE_NAME}")
// 				}
// 				failure
// 				{
// 					bwpmAcceptFailure("${STAGE_NAME}", params.VERSION_NUMBER, params.JOOMLA_VERSION)
// 				}
// 			}
// 		}

// 		stage('accept3')
// 		{
// 			steps
// 			{
// //				echo 'Dummy'
// 				bwpmAccept("${STAGE_NAME}", params.ACCEPT_3_IP, params.VERSION_NUMBER, params.JOOMLA_VERSION)
// 			}
// 			post
// 			{
// 				always
// 				{
// 					bwpmAcceptPostStepAlways("${STAGE_NAME}")
// 				}
// 				failure
// 				{
// 					bwpmAcceptFailure("${STAGE_NAME}", params.VERSION_NUMBER, params.JOOMLA_VERSION)
// 				}
// 			}
// 		}

// 		stage('accept6')
// 		{
// 			steps
// 			{
// //				echo 'Dummy'
// 				bwpmAccept("${STAGE_NAME}", params.ACCEPT_6_IP, params.VERSION_NUMBER, params.JOOMLA_VERSION)
// 			}
// 			post
// 			{
// 				always
// 				{
// 					bwpmAcceptPostStepAlways("${STAGE_NAME}")
// 				}
// 				failure
// 				{
// 					bwpmAcceptFailure("${STAGE_NAME}", params.VERSION_NUMBER, params.JOOMLA_VERSION)
// 				}
// 			}
// 		}

		// stage('accept4')
		// {
		// 	steps
		// 	{
		// 		// echo 'Dummy'
		// 		// sleep 60
		// 		bwpmAccept("${STAGE_NAME}", params.ACCEPT_4_IP, params.VERSION_NUMBER, params.JOOMLA_VERSION)
		// 	}
		// 	post
		// 	{
		// 		always
		// 		{
		// 			bwpmAcceptPostStepAlways("${STAGE_NAME}")
		// 		}
		// 		failure
		// 		{
		// 			bwpmAcceptFailure("${STAGE_NAME}", params.VERSION_NUMBER, params.JOOMLA_VERSION)
		// 		}
		// 	}
		// }

		stage('Manual Tests') {
			steps {
				echo 'Benutzeroberflaeche'
				echo 'Worst-Case-Tests'
				echo 'nicht-funktionale Tests (Datenschutz, Sicherheit, ...)'
			}
		}

// 		stage('Dev-Upload') {
// 			steps {
// 				dir("/repositories/artifacts/bwpostman/data") {
// 					fileOperations([
// 						fileCopyOperation(
// 							excludes: '',
// 						flattenFiles: false,
// 						includes: "pkg_bwpostman-${params.VERSION_NUMBER}.${currentBuild.number}.zip",
// 						targetLocation: "${WORKSPACE}/tests")
// 				])
// 				}
//
// 				dir("/repositories/artifacts/bwpostman/data") {
// 					fileOperations([
// 						fileCopyOperation(
// 							excludes: '',
// 						flattenFiles: false,
// 						includes: "CHANGELOG",
// 						targetLocation: "${WORKSPACE}/tests")
// 				])
// 				}
//
// 				script {
// 					GIT_MESSAGE = sh(returnStdout: true, script: "git log -n 1 --pretty=%B")
// 				}
//
// 				echo "tests/pkg_bwpostman-${params.VERSION_NUMBER}.${currentBuild.number}.zip"
//
// 				sshPublisher(
// 					publishers: [sshPublisherDesc(
// 					configName: 'Web Dev BwPostman',
// 					transfers: [sshTransfer(
// 					cleanRemote: false,
// 					excludes: '',
// 					execCommand: '',
// 					execTimeout: 120000,
// 					flatten: false,
// 					makeEmptyDirs: false,
// 					noDefaultExcludes: false,
// 					patternSeparator: '[, ]+',
// 					remoteDirectory: "${params.VERSION_NUMBER}.${currentBuild.number}",
// 					remoteDirectorySDF: false,
// 					removePrefix: 'tests',
// 					sourceFiles: "tests/CHANGELOG, tests/pkg_bwpostman-${params.VERSION_NUMBER}.${currentBuild.number}.zip"
// 			)],
// 				usePromotionTimestamp: false,
// 					useWorkspaceInPromotion: false,
// 					verbose: false
// 			)]
// 			)
//
// 				emailext(
// 					body: "<p>BwPostman build ${currentBuild.number} has passed smoke test, all acceptance tests and is uploaded to Boldt Webservice for manual testing purpose.</p><p>Last commit message: ${GIT_MESSAGE}</p>",
// 					subject:"BwPostman build ${currentBuild.number} successful",
// 					to: 'webmaster@boldt-webservice.de'
// 			)
// //				NUR WENN ICH GANZ SICHER BIN!!!!!!!!
// //				to: 'webmaster@boldt-webservice.de, k.klostermann@t-online.de'
//
// 			}
// 		}

		stage ('accept5')
		{
			steps
			{
				echo 'Dummy'
				bwpmAccept("accept5", params.ACCEPT_5_IP, params.VERSION_NUMBER, params.JOOMLA_VERSION)
			}
			post
			{
				always
				{
					bwpmAcceptPostStepAlways("accept5")
				}
				failure
				{
					bwpmAcceptFailure("${STAGE_NAME}", params.VERSION_NUMBER, params.JOOMLA_VERSION)
				}
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
    }
}
