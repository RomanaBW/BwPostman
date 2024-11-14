# Pipeline for testing BwPostman

The pipeline for testing BwPostman processes multiple steps:
1. Set some parameters needed later
2. Build installation packages of the extension
	1. One without replacing variables at code files for version number and others. This is a package used for manual 
	testing. Without ever-changing version one is able to run a fast diff against predecessors to see the changes. But the 
	package name contains version and build of this run.
	2. One with replaced variables at code files. This is the delivery package used for smoke tests. To get constant 
	identification of the package, the package name does not contain any changing values. If all went well this package 
	could be published.
3. Do smoke tests with codeception
4. Do acceptance tests with codeception
5. Do deinstallation test with codeception
6. Upload both installation packages to webspace. Here the package with replaced variables gets the version number at 
	the package name.
7. Notify specific persons about test results with screenshots and to video on failures

Step 1 is a simple Jenkinsfile task.

Step 2 uses an ansible playbook.

Steps 3 to 5 also use ansible playbooks. The tests need selenium and codeception, so they have to run at a complete 
infrastructure containing web server, database and tester. To get this infrastructure, I use containers. The efforts with 
virtual machines, which I used so far were not satisfying, too much false positives, too many dependencies on environmental
facts. Getting this infrastructure from scratch is described below.

Step 6 and 7 are again simple Jenkinsfile tasks.  

## Groundwork: Tester container creation

There are ansible playbooks for these jobs. The images are stored at local docker registry **universe3:5000**.

All manually changing variables should be kept (beneath other variables) at the variable file **vars/tester-image-vars.yml**. 
Changing variables sounds crazy, I know, but most variables are build on some manually entered values. These are automated 
changing variables, in opposite to the manual changing variables. Normally I would prefer to name them parameters, but 
ansible makes no difference…  

There are some containers, which have to be build successively:
1. Create base images for web server and database and store them at local docker registry
	1. **create-push-apache-image.yml**, currently based on php:apache and locally stored at **universe3:5000/romana/php:apache**.
	2. **create-push-mariadb-image.yml**, currently based on bitnami/mariadb:10.5-debian-10 and locally stored at **universe3:5000/romana/mariadb:10.5-debian-10**
2. Install Joomla to previously created images. The playbooks and the images are
	1. **create-push-joomla-files-image.yml**, image is **universe3:5000/romana/joomla-bare-files:{{ joomla_version }}**
	2. **create-push-joomla-tables-image.yml**, image is **universe3:5000/romana/joomla-bare-tables:{{ joomla_version }}**
    **!!Attention: Joomla version now has also be changed at vars/codecept_paths_Jn.yml!!!!!!**
	This task does a little bit more. All changes to the tables are also settled here, as there are
		1. Install all tables of joomla
		2. Add needed Users, assign them to desired user groups
		3. Enable user registration
		4. Modify *created_by* to some articles like Joomla does in the installation script
		5. Set UTF8 conversion like Joomla does in the installation script
		6. Reset post installation messages
		7. Disable update notification
		8. Disable sending statistics 
3. Install needed extensions (JCE, the plugin Testmode and VirtueMart) to previously created images
	1. **install-required-extensions-push.yml**, the images are
		1. **universe3:5000/romana/joomla-jce-files:{{ joomla_version }}**
		2. **universe3:5000/romana/joomla-jce-tables:{{ joomla_version }}**
	2. **install-virtuemart-push.yml**, the images are
		1. **universe3:5000/romana/joomla-vm-files:{{ joomla_version }}**
		2. **universe3:5000/romana/joomla-vm-tables:{{ joomla_version }}**
		
Step 1.ii is only to do, if one want to change web server or database version.

Step 1.i, 2 and 3 are to process every time, Joomla gets an update

Step 3 may be processed without step 2, if version of Joomla does not change but the version of installed extensions.

There is a playbook **create-complete-containers.yml**, that runs all above-mentioned playbooks at one run. There is also 
the possibility to run Step 1.ii, if extra-vars _update_database _ is set.
	
	@ToDo:
	At step 3 there could also inserted a Joomla check for the database/tables (Extensions->Manage->Database) to be sure.

**These  three steps are groundwork and have to be done before the pipeline starts. They are not part of the pipeline but 
the basis, on which the pipeline is based on.**

	Note:
	The versions of JCE and plugin Testmode aren't really important, as long as there are no errors concerning these 
	both extensions. 
	The new version of JCE has not to be known, there is a general URI to download the latest version.
	The plugin Testmode isn't really changing. By all means I can't imagine, there is a change on this plugin. Perhaps 
	a new feature, but this plugin does not do much except existing.
	Really important seems the version of Virtuemart, because this is something, that really concerns the functionality 
	of the plugin B2S.

## First tests (smoke)

The first tests are installation tests and configure the extension, such as set default options, create users, fill tables 
with test values, set permissions. Because the following tests needs an installed and configured BwPostman, here a further
set of containers is created, if the smoke is successful, to fasten these following tests.

The used playbook is **run-smoke-tests.yml**. The resulting images are
 * **universe3:5000/romana/joomla-bwpm-files:{{ joomla_version }}_{{ bwpm_version }}**
 * **universe3:5000/romana/joomla-bwpm-tables:{{ joomla_version }}_{{ bwpm_version }}**
 * **universe3:5000/romana/joomla-bwpm-tester:{{ joomla_version }}_{{ bwpm_version }}**

Here also the tests are copied to the tester container. Because later I want to parallelize the tests, it is a good thing 
to copy the tests to the image. All is in place. 

## Acceptance tests

These are the main tests for the functionality of the extension. The appropriate playbook is **run-acceptance-tests.yml**.
This playbook needs the test suite as **--extra-vars "accept1"**. Accept1 is an example. Here no new containers are produced, 
we are at the top of the tests.

For the moment there are the test suites
* accept1
* accept2
* accept3
* accept4
* accept5
* accept6
* uninstall

From the theory, these suites should be able to run all in parallel.
