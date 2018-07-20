# Running the deployment pipeline

These playbooks are part of the deployment pipeline.

The pipeline runs in Jenkins. First of all Jenkins checks out the gitlab repository to the workspace defined in Jenkins.
Then thr following steps of the Jenkinsfile are executed.

## build_package.yml
This is the first step of the pipeline. Here the installation package is build.The first step is to inject the
current version number including the build number to the code.

Next step is to build packages of all parts of BwPostman, that means packages for the component, the modules and
the plugins.

Last step is to build the overall installation package of all these packages.

Naturally there is some work to do to get this main job done, which can be seen in the playbook.

### Variables needed

All variables are submitted by the Jenkinsfile.
 
* __project_base_dir__, normally the workspace of Jenkins
* __version_number__, the new/next version number of the project. This is really a variable
* __build__, submitted by Jenkins, running number for the current build/pipeline
* __mb4_support__, normally set to true

## insert_version_number.yml
Now I don't know if this playbook is ever used or if I've written this only for testing purpose…

## Do tests

This playbook has to
 
* start the tester VM at the __physical host__ where the VM should run (delegate_to…)
* start the webdriver at VM
* start the recording at VM
* do the tests at VM
* stop the recording at VM
* destroy the tester VM at the __physical host__ where the VM should run (delegate_to…)

### Variables needed

* __host_for_tester_vm__, the physical host where the VM should run, for now mostly localhost
* __log_path__, path for log files for (only important in case of problems concerning running webdriver or recording)
    * webdriver
    * recording
* __record_path__ path for recorded videos (very important for debugging)
* __test_src__, path where the tests reside, normally a sub folder of the workspace
* __test_suite__, the suite of tests to run (installation, lists, …), submitted by 
specific Jenkins job

I think, it would be best to hold log files and recorded videos at artifacts because they are 
produced and part of the current run. If I would do this really professional, I should archive 
these files, especially the videos, per build…

The test suites are collections of acceptance tests, where the collections are composed to spread the sum of acceptance 
tests to multiple VMs to reduce and balance runtime.

__Except installation and deinstallation tests all test suites need to import test data!__ 

## License

MIT/BSD

## Author Information

This playbook was created in 2018 by [Romana Boldt](https://www.boldt-webservice.de/).
