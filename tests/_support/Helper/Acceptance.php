<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends \Codeception\Module
{
	public function _beforeSuite()
	{
		$connection = ssh2_connect('universe', 22, array('hostkey'=>'ssh-rsa'));
		ssh2_auth_pubkey_file($connection, 'romana', '/home/romana/.ssh/romana_rsa.pub', '/home/romana/.ssh/romana_rsa');
		ssh2_exec($connection, "mysql -u root -pSusi bwtest < /daten/vhosts/dev/joomla-cms/tests/_data/testdata_bwpostman_complete.sql");
	}

	public function _afterSuite()
	{
		$connection = ssh2_connect('universe', 22, array('hostkey'=>'ssh-rsa'));
		ssh2_auth_pubkey_file($connection, 'romana', '/home/romana/.ssh/romana_rsa.pub', '/home/romana/.ssh/romana_rsa');
		ssh2_exec($connection, "mysql -u root -pSusi bwtest < /daten/vhosts/dev/joomla-cms/tests/_data/testdata_bwpostman_truncate.sql");
	}

	public function changeBrowser($browser) {
		$this->getModule('WebDriver')->_reconfigure(array('browser' => $browser));
	}

}
