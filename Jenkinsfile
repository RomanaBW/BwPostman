pipeline {
    agent any

//	options {
//		disableConcurrentBuilds()
//	}

	// parameters {
	// 	string(name: "VERSION_NUMBER", defaultValue: "2.3.0", description: "The new/next version number of the project.")
	// 	string(name: "JOOMLA_VERSION", defaultValue: "3.9.4", description: "Version of Joomla to test against")
	// 	string(name: "VAGRANT_DIR", defaultValue: "/vms-uni2/vagrant/infrastructure/farm1/J-Tester", description: "Path to the vagrant file")
	// 	string(name: "BW_ARTIFACTS_BASE", defaultValue: "/repositories/artifacts/bwpostman")
	// 	string(name: "GIT_MESSAGE", defaultValue: "not specified")
	// }

    stages {
        stage('Build') {
			steps {
				script
				{
					VERSION_NUMBER = "2.3.0"
					JOOMLA_VERSION = "3.9.4"
					VAGRANT_DIR = "/vms-uni2/vagrant/infrastructure/farm1/J-Tester"
					BW_ARTIFACTS_BASE = "/repositories/artifacts/bwpostman"
					SMOKE_IP = "192.168.2.130"
					ACCEPT_1_IP = "192.168.2.131"
					ACCEPT_2_IP = "192.168.2.132"
					ACCEPT_3_IP = "192.168.2.133"
					ACCEPT_4_IP = "192.168.2.134"
					ACCEPT_5_IP = "192.168.2.135"
					ACCEPT_6_IP = "192.168.2.136"
					GIT_MESSAGE = "not specified"
				}

                echo 'Create installation package'
				sh "ansible-playbook ${WORKSPACE}/build/playbooks/build_package.yml --extra-vars 'project_base_dir=${WORKSPACE} version_number=${VERSION_NUMBER} build=${BUILD_NUMBER} mb4_support=true replace_vars=false'"
				sh "ansible-playbook ${WORKSPACE}/build/playbooks/build_package.yml --extra-vars 'project_base_dir=${WORKSPACE} version_number=${VERSION_NUMBER} build=${BUILD_NUMBER} mb4_support=true replace_vars=true'"
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
				bwpmAccept ("${STAGE_NAME}", "${SMOKE_IP}", "${VERSION_NUMBER}", "${JOOMLA_VERSION}")
			}
			post {
				always {
					bwpmAcceptPostStepAlways ("${STAGE_NAME}")
				}
				failure {
					bwpmAcceptFailure ("${STAGE_NAME}", "${VERSION_NUMBER}", "${JOOMLA_VERSION}")
				}
			}
		}

		stage('accept1') {
			steps
			{
//				echo 'Dummy'
//				sleep 60
				bwpmAccept("${STAGE_NAME}", "${ACCEPT_1_IP}", "${VERSION_NUMBER}", "${JOOMLA_VERSION}")
			}
			post
			{
				always
				{
					bwpmAcceptPostStepAlways("${STAGE_NAME}")
				}
				failure
				{
					bwpmAcceptFailure("${STAGE_NAME}", "${VERSION_NUMBER}", "${JOOMLA_VERSION}")
				}
			}
		}

		stage('accept2')
		{
			steps
			{
//				echo 'Dummy'
				bwpmAccept("${STAGE_NAME}", "${ACCEPT_2_IP}", "${VERSION_NUMBER}", "${JOOMLA_VERSION}")
			}
			post
			{
				always
				{
					bwpmAcceptPostStepAlways("${STAGE_NAME}")
				}
				failure
				{
					bwpmAcceptFailure("${STAGE_NAME}", "${VERSION_NUMBER}", "${JOOMLA_VERSION}")
				}
			}
		}

		stage('accept3')
		{
			steps
			{
//				echo 'Dummy'
				bwpmAccept("${STAGE_NAME}", "${ACCEPT_3_IP}", "${VERSION_NUMBER}", "${JOOMLA_VERSION}")
			}
			post
			{
				always
				{
					bwpmAcceptPostStepAlways("${STAGE_NAME}")
				}
				failure
				{
					bwpmAcceptFailure("${STAGE_NAME}", "${VERSION_NUMBER}", "${JOOMLA_VERSION}")
				}
			}
		}

		stage('accept6')
		{
			steps
			{
//				echo 'Dummy'
				bwpmAccept("${STAGE_NAME}", "${ACCEPT_6_IP}", "${VERSION_NUMBER}", "${JOOMLA_VERSION}")
			}
			post
			{
				always
				{
					bwpmAcceptPostStepAlways("${STAGE_NAME}")
				}
				failure
				{
					bwpmAcceptFailure("${STAGE_NAME}", "${VERSION_NUMBER}", "${JOOMLA_VERSION}")
				}
			}
		}


		stage('accept4')
		{
			steps
			{
				// echo 'Dummy'
				// sleep 60
				bwpmAccept("${STAGE_NAME}", "${ACCEPT_4_IP}", "${VERSION_NUMBER}", "${JOOMLA_VERSION}")
			}
			post
			{
				always
				{
					bwpmAcceptPostStepAlways("${STAGE_NAME}")
				}
				failure
				{
					bwpmAcceptFailure("${STAGE_NAME}", "${VERSION_NUMBER}", "${JOOMLA_VERSION}")
				}
			}
		}

		stage ('accept5')
		{
			steps
			{
				echo 'Dummy'
				bwpmAccept("${STAGE_NAME}", "${ACCEPT_5_IP}", "${VERSION_NUMBER}", "${JOOMLA_VERSION}")
			}
			post
			{
				always
				{
					bwpmAcceptPostStepAlways("${STAGE_NAME}")
				}
				failure
				{
					bwpmAcceptFailure("${STAGE_NAME}", "${VERSION_NUMBER}", "${JOOMLA_VERSION}")
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

		stage('Dev-Upload') {
			steps {
				dir("/repositories/artifacts/bwpostman/data") {
					fileOperations([
						fileCopyOperation(
							excludes: '',
						flattenFiles: false,
						includes: "pkg_bwpostman-${"${VERSION_NUMBER}"}.${currentBuild.number}.zip",
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

				echo "tests/pkg_bwpostman-${"${VERSION_NUMBER}"}.${currentBuild.number}.zip"

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
					remoteDirectory: "${"${VERSION_NUMBER}"}.${currentBuild.number}",
					remoteDirectorySDF: false,
					removePrefix: 'tests',
					sourceFiles: "tests/CHANGELOG, tests/pkg_bwpostman-${"${VERSION_NUMBER}"}.${currentBuild.number}.zip"
			)],
				usePromotionTimestamp: false,
					useWorkspaceInPromotion: false,
					verbose: false
			)]
			)

				emailext(
					body: "<p>BwPostman build ${currentBuild.number} has passed smoke test, all acceptance tests and is uploaded to Boldt Webservice for manual testing purpose.</p><p>Last commit message: ${GIT_MESSAGE}</p>",
					subject:"BwPostman build ${currentBuild.number} successful",
					to: 'webmaster@boldt-webservice.de'
			)
//				@ToDo: NUR WENN ICH GANZ SICHER BIN!!!!!!!!
//				to: 'webmaster@boldt-webservice.de, k.klostermann@t-online.de'

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
