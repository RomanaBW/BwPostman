<?php
/**
 * BwPostman Buyer2Subscriber Plugin
 *
 * Plugin to automated subscription at VirtueMart registration
 *
 * BwPostman Buyer2Subscriber Plugin installation script.
 *
 * @version %%version_number%%
 * @package BwPostman Buyer2Subscriber Plugin
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/forum/bwpostman.html
 * @license GNU/GPL v3, see LICENSE.txt
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die('Restricted access');

use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Installer\InstallerAdapter;

/**
 * Installation script for the plugin
 *
 * @since   2.0.0
 */
class PlgVmUserfieldBwPm_Buyer2SubscriberInstallerScript
{
	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	protected $min_bwpostman_version    = '1.3.2';

	/**
	 * @var integer
	 *
	 * @since 2.0.0
	 */
	protected $user_id    = 0;

	/**
	 * @var integer
	 *
	 * @since 2.0.0
	 */
	protected $now_date    = '0000-00-00 00:00:00';

	/**
	 * @var integer
	 *
	 * @since 2.0.0
	 */
	protected $vm_vendor_id    = 0;

	/**
	 * @var integer
	 *
	 * @since 2.0.0
	 */
	protected $userfield_insert_ordering_position    = 0;

	/**
	 * @var integer
	 *
	 * @since 2.0.0
	 */
	protected $userfield_value_ordering    = 0;

	/**
	 * Called before any type of action
	 *
	 * @param   string  			$type		Which action is happening (install|uninstall|discover_install|update)
	 * @param   InstallerAdapter	$parent		The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 *
	 * @throws Exception
	 *
	 * @since       0.9.6.3
	 */

	public function preflight($type, InstallerAdapter $parent)
	{
		if ($type == 'install')
		{
			// check prerequisites
			$BwPostmanComponentVersion  = $this->getComponentVersion();

			if (version_compare($BwPostmanComponentVersion, $this->min_bwpostman_version, 'lt'))
			{
				Factory::getApplication()->enqueueMessage(
					Text::sprintf('PLG_BWPOSTMAN_PLUGIN_BUYER2SUBSCRIBER_COMPONENT_BWPOSTMAN_NEEDED', $this->min_bwpostman_version),
					'error'
				);
				return false;
			}

			$plugin_installed   = $this->user2SubscriberPluginInstalled();

			if (!$plugin_installed)
			{
				Factory::getApplication()->enqueueMessage(Text::_('PLG_BWPOSTMAN_PLUGIN_BUYER2SUBSCRIBER_PLUGIN_USER2SUBSCRIBER_NEEDED'), 'error');
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to get component version
	 *
	 * @return string
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function getComponentVersion()
	{
		$version    = '0.0.0';
		$_db        = Factory::getContainer()->get(DatabaseInterface::class);
		$query      = $_db->getQuery(true);

		$query->select($_db->quoteName('manifest_cache'));
		$query->from($_db->quoteName('#__extensions'));
		$query->where($_db->quoteName('element') . " = " . $_db->quote('com_bwpostman'));

		try
		{
			$_db->setQuery($query);

			$manifest   = json_decode($_db->loadResult(), true);
			$version    = $manifest['version'];
		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $version;
	}

	/**
	 * Method to set status of User2Subscriber plugin activation (property)
	 *
	 * @return bool
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function user2SubscriberPluginInstalled()
	{
		$plugin_installed  = false;
		$plugin_id         = null;

		$_db        = Factory::getContainer()->get(DatabaseInterface::class);
		$query      = $_db->getQuery(true);

		$query->select($_db->quoteName('extension_id'));
		$query->from($_db->quoteName('#__extensions'));
		$query->where($_db->quoteName('element') . ' = ' . $_db->quote('bwpm_user2subscriber'));

		try
		{
			$_db->setQuery($query);

			$plugin_id  = $_db->loadResult();
		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		if ($plugin_id !== null)
		{
			$plugin_installed   = true;
		}

		return $plugin_installed;
	}

	/**
	 * Called after any type of action
	 *
	 * @param   string  			$type		Which action is happening (install|uninstall|discover_install)
	 * @param   InstallerAdapter	$parent		The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */

	public function postflight($type, InstallerAdapter $parent)
	{
		if ($type == 'install') {
			// create custom user fields for VM for message, subscription, gender, special and newsletter format
			$this->setInitialValues();

			$this->insertBwPostmanMessageFieldToVm();
			$this->insertBwPostmanSubscriptionFieldToVm();
			$this->insertBwPostmanNewsletterFormatFieldToVm();
			$this->insertBwPostmanGenderFieldToVm();

			$this->setUserfieldInsertOrderingPosition('company');

			$this->insertBwPostmanAdditionalFieldToVm();
		}

		return true;
	}

	/**
	 * Called on deinstallation
	 *
	 * @param   InstallerAdapter  $adapter  The object responsible for running this script
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function uninstall(InstallerAdapter $adapter)
	{
		$bw_userfield_ids = $this->getBwUserfieldIDs();

		if (count($bw_userfield_ids) > 0)
		{
			$this->deleteBwUserfields($bw_userfield_ids);
			$this->deleteBwUserfieldValues($bw_userfield_ids);
		}
	}

	/**
	 * Sets widely used variables as properties
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function setInitialValues()
	{
		$this->user_id  = Factory::getApplication()->getIdentity()->get('id');
		$this->now_date = Factory::getDate()->toSql();

		$this->vm_vendor_id = $this->getVmVendorId();

		$this->setUserfieldInsertOrderingPosition('password2');
	}

	/**
	 * Gets vendor ID of VM vendor
	 *
	 * @return int   $vm_vendor_id
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function getVmVendorId()
	{
		$vm_vendor_id   = 0;

		$_db   = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $_db->getQuery(true);

		$query->select($_db->quoteName('virtuemart_vendor_id'));
		$query->from($_db->quoteName('#__virtuemart_vmusers'));
		$query->where($_db->quoteName('user_is_vendor') . ' = ' . (int) 1);

		try
		{
			$_db->setQuery($query);

			$vm_vendor_id  = $_db->loadResult();
		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}

		return $vm_vendor_id;
	}

	/**
	 * Gets the ordering position of a given userfield of VM and sets it as property
	 *
	 * @param   string $field_name
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function setUserfieldInsertOrderingPosition($field_name)
	{
		$_db    = Factory::getContainer()->get(DatabaseInterface::class);
		$query  = $_db->getQuery(true);

		$query->select($_db->quoteName('ordering'));
		$query->from($_db->quoteName('#__virtuemart_userfields'));
		$query->where($_db->quoteName('name') . ' = ' . $_db->quote($field_name));

		$result = 0;

		try
		{
			$_db->setQuery($query);

			$result = $_db->loadResult();
		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}

		$this->userfield_insert_ordering_position = ++$result;
	}

	/**
	 * Method to store a userfield needed by plugin to VM userfield table at current position
	 * (userfield_insert_ordering_position) with its userfield_value entries
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function insertBwPostmanMessageFieldToVm()
	{
		$this->freeOrderingPosition();

		$_db   = Factory::getContainer()->get(DatabaseInterface::class);
		$nullDate = $_db->getNullDate();

		$userfield_values = array(
			$_db->quote(0) . ',' .
			$_db->quote($this->vm_vendor_id) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote('bw_newsletter_message') . ',' .
			$_db->quote('PLG_BWPOSTMAN_PLUGIN_BUYER2SUBSCRIBER_MESSAGE_LABEL') . ',' .
			$_db->quote('PLG_BWPOSTMAN_PLUGIN_BUYER2SUBSCRIBER_MESSAGE_DESC') . ',' .
			$_db->quote('hidden') . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(null) . ',' .
			$_db->quote('') . ',' .
			$_db->quote(1) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(1) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(1) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote('') . ',' .
			$_db->quote($this->userfield_insert_ordering_position) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(1) . ',' .
			$_db->quote($this->now_date) . ',' .
			$_db->quote($this->user_id) . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote('0') . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote('0')
		);

		$message_field_id   = $this->writeUserfieldToVmTable($userfield_values);

		$userfield_values_values = array(
			$_db->quote(0) . ',' .
			$_db->quote($message_field_id) . ',' .
			$_db->quote('bw_newsletter_message') . ',' .
			$_db->quote('') . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(++$this->userfield_value_ordering) . ',' .
			$_db->quote($this->now_date) . ',' .
			$_db->quote($this->user_id) . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote(0)
		);

		$this->writeUserfieldValuesToVmTable($userfield_values_values);

		++$this->userfield_insert_ordering_position;
	}

	/**
	 * Method to store a userfield needed by plugin to VM userfield table at current position
	 * (userfield_insert_ordering_position) with its userfield_value entries
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function insertBwPostmanSubscriptionFieldToVm()
	{
		$this->freeOrderingPosition();

		$_db   = Factory::getContainer()->get(DatabaseInterface::class);
		$nullDate = $_db->getNullDate();

		$userfield_values = array(
			$_db->quote(0) . ',' .
			$_db->quote($this->vm_vendor_id) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote('bw_newsletter_subscription') . ',' .
			$_db->quote('PLG_BWPOSTMAN_PLUGIN_BUYER2SUBSCRIBER_SUBSCRIPTION_CHECKBOX_LABEL') . ',' .
			$_db->quote('PLG_BWPOSTMAN_PLUGIN_BUYER2SUBSCRIBER_SUBSCRIPTION_CHECKBOX_DESC') . ',' .
			$_db->quote('hidden') . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote('') . ',' .
			$_db->quote('0') . ',' .
			$_db->quote(1) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(1) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote('') . ',' .
			$_db->quote($this->userfield_insert_ordering_position) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(1) . ',' .
			$_db->quote($this->now_date) . ',' .
			$_db->quote($this->user_id) . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote('0') . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote('0')
		);

		$subscription_field_id = $this->writeUserfieldToVmTable($userfield_values);

		// subscription option No
		$userfield_values_values = array(
			$_db->quote(0) . ',' .
			$_db->quote($subscription_field_id) . ',' .
			$_db->quote('JNO') . ',' .
			$_db->quote('0') . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(++$this->userfield_value_ordering) . ',' .
			$_db->quote($this->now_date) . ',' .
			$_db->quote($this->user_id) . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote(0)
		);

		$this->writeUserfieldValuesToVmTable($userfield_values_values);

		// subscription option Yes
		$userfield_values_values = array(
			$_db->quote(0) . ',' .
			$_db->quote($subscription_field_id) . ',' .
			$_db->quote('JYES') . ',' .
			$_db->quote('1') . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(++$this->userfield_value_ordering) . ',' .
			$_db->quote($this->now_date) . ',' .
			$_db->quote($this->user_id) . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote(0)
		);

		$this->writeUserfieldValuesToVmTable($userfield_values_values);

		++$this->userfield_insert_ordering_position;
	}

	/**
	 * Method to store a userfield needed by plugin to VM userfield table at current position
	 * (userfield_insert_ordering_position) with its userfield_value entries
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function insertBwPostmanNewsletterFormatFieldToVm()
	{
		$this->freeOrderingPosition();

		$_db   = Factory::getContainer()->get(DatabaseInterface::class);
		$nullDate = $_db->getNullDate();

		$userfield_values = array(
			$_db->quote(0) . ',' .
			$_db->quote($this->vm_vendor_id) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote('bw_newsletter_format') . ',' .
			$_db->quote('PLG_BWPOSTMAN_PLUGIN_BUYER2SUBSCRIBER_MAILFORMAT_LABEL') . ',' .
			$_db->quote('PLG_BWPOSTMAN_PLUGIN_BUYER2SUBSCRIBER_MAILFORMAT_DESC') . ',' .
			$_db->quote('hidden') . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(null) . ',' .
			$_db->quote('1') . ',' .
			$_db->quote(1) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(1) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote('') . ',' .
			$_db->quote($this->userfield_insert_ordering_position) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(1) . ',' .
			$_db->quote($this->now_date) . ',' .
			$_db->quote($this->user_id) . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote('0') . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote('0')
		);

		$format_field_id = $this->writeUserfieldToVmTable($userfield_values);

		// format option text
		$userfield_values_values = array(
			$_db->quote(0) . ',' .
			$_db->quote($format_field_id) . ',' .
			$_db->quote('PLG_BWPOSTMAN_TEXT') . ',' .
			$_db->quote('0') . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(++$this->userfield_value_ordering) . ',' .
			$_db->quote($this->now_date) . ',' .
			$_db->quote($this->user_id) . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote(0)
		);

		$this->writeUserfieldValuesToVmTable($userfield_values_values);

		// format option HTML
		$userfield_values_values = array(
			$_db->quote(0) . ',' .
			$_db->quote($format_field_id) . ',' .
			$_db->quote('PLG_BWPOSTMAN_HTML') . ',' .
			$_db->quote('1') . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(++$this->userfield_value_ordering) . ',' .
			$_db->quote($this->now_date) . ',' .
			$_db->quote($this->user_id) . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote(0)
		);

		$this->writeUserfieldValuesToVmTable($userfield_values_values);

		++$this->userfield_insert_ordering_position;
	}

	/**
	 * Method to store a userfield needed by plugin to VM userfield table at current position
	 * (userfield_insert_ordering_position) with its userfield_value entries
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function insertBwPostmanGenderFieldToVm()
	{
		$this->freeOrderingPosition();

		$_db   = Factory::getContainer()->get(DatabaseInterface::class);
		$nullDate = $_db->getNullDate();

		$userfield_values = array(
			$_db->quote(0) . ',' .
			$_db->quote($this->vm_vendor_id) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote('bw_gender') . ',' .
			$_db->quote('PLG_BWPOSTMAN_PLUGIN_BUYER2SUBSCRIBER_GENDER') . ',' .
			$_db->quote('PLG_BWPOSTMAN_PLUGIN_BUYER2SUBSCRIBER_SUBS_FIELD_GENDER_DESC') . ',' .
			$_db->quote('hidden') . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(null) . ',' .
			$_db->quote('') . ',' .
			$_db->quote(1) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(1) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote('') . ',' .
			$_db->quote($this->userfield_insert_ordering_position) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(1) . ',' .
			$_db->quote($this->now_date) . ',' .
			$_db->quote($this->user_id) . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote('0') . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote('0')
		);

		$gender_field_id = $this->writeUserfieldToVmTable($userfield_values);

		// gender option select
		$userfield_values_values = array(
			$_db->quote(0) . ',' .
			$_db->quote($gender_field_id) . ',' .
			$_db->quote('PLG_BWPOSTMAN_PLUGIN_BUYER2SUBSCRIBER_SUB_SELECT_GENDER') . ',' .
			$_db->quote('') . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(++$this->userfield_value_ordering) . ',' .
			$_db->quote($this->now_date) . ',' .
			$_db->quote($this->user_id) . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote(0)
		);

		$this->writeUserfieldValuesToVmTable($userfield_values_values);

		// gender option female
		$userfield_values_values = array(
			$_db->quote(0) . ',' .
			$_db->quote($gender_field_id) . ',' .
			$_db->quote('PLG_BWPOSTMAN_PLUGIN_BUYER2SUBSCRIBER_FEMALE') . ',' .
			$_db->quote('1') . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(++$this->userfield_value_ordering) . ',' .
			$_db->quote($this->now_date) . ',' .
			$_db->quote($this->user_id) . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote(0)
		);

		$this->writeUserfieldValuesToVmTable($userfield_values_values);

		// gender option male
		$userfield_values_values = array(
			$_db->quote(0) . ',' .
			$_db->quote($gender_field_id) . ',' .
			$_db->quote('PLG_BWPOSTMAN_PLUGIN_BUYER2SUBSCRIBER_MALE') . ',' .
			$_db->quote('0') . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(++$this->userfield_value_ordering) . ',' .
			$_db->quote($this->now_date) . ',' .
			$_db->quote($this->user_id) . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote(0)
		);

		$this->writeUserfieldValuesToVmTable($userfield_values_values);

		++$this->userfield_insert_ordering_position;
	}

	/**
	 * Method to store a userfield needed by plugin to VM userfield table at current position
	 * (userfield_insert_ordering_position) with its userfield_value entries
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function insertBwPostmanAdditionalFieldToVm()
	{
		$this->freeOrderingPosition();

		$_db   = Factory::getContainer()->get(DatabaseInterface::class);
		$nullDate = $_db->getNullDate();

		$userfield_values = array(
			$_db->quote(0) . ',' .
			$_db->quote($this->vm_vendor_id) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote('bw_newsletter_additional') . ',' .
			$_db->quote('PLG_BWPOSTMAN_PLUGIN_BUYER2SUBSCRIBER_SUBS_FIELD_SPECIAL_LABEL') . ',' .
			$_db->quote('COM_BWPOSTMAN_SUBS_FIELD_SPECIAL_DESC') . ',' .
			$_db->quote('hidden') . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(null) . ',' .
			$_db->quote('') . ',' .
			$_db->quote(1) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(1) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote('') . ',' .
			$_db->quote($this->userfield_insert_ordering_position) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(1) . ',' .
			$_db->quote($this->now_date) . ',' .
			$_db->quote($this->user_id) . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote('0') . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote('0')
		);

		$additional_field_id = $this->writeUserfieldToVmTable($userfield_values);

		// additional option
		$userfield_values_values = array(
			$_db->quote(0) . ',' .
			$_db->quote($additional_field_id) . ',' .
			$_db->quote('bw_newsletter_additional') . ',' .
			$_db->quote('') . ',' .
			$_db->quote(0) . ',' .
			$_db->quote(++$this->userfield_value_ordering) . ',' .
			$_db->quote($this->now_date) . ',' .
			$_db->quote($this->user_id) . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote(0) . ',' .
			$_db->quote($nullDate) . ',' .
			$_db->quote(0)
		);

		$this->writeUserfieldValuesToVmTable($userfield_values_values);

		++$this->userfield_insert_ordering_position;
	}

	/**
	 * Checks if insert position for ordering is used. If so, calls increment ordering
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function freeOrderingPosition()
	{
		$result = null;

		$_db   = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $_db->getQuery(true);

		$query->select($_db->quoteName('ordering'));
		$query->from($_db->quoteName('#__virtuemart_userfields'));
		$query->where($_db->quoteName('ordering') . ' = ' . $_db->quote($this->userfield_insert_ordering_position));

		try
		{
			$_db->setQuery($query);

			$result = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		if ($result !== null)
		{
			$this->incrementOrderingColumnAtInstallation();
		}
	}

	/**
	 * Increments ordering column at userfield table of VM from current position and above
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function incrementOrderingColumnAtInstallation()
	{
		$_db   = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $_db->getQuery(true);

		$query->update($_db->quoteName('#__virtuemart_userfields'));
		$query->set($_db->quoteName('ordering') . " = " . $_db->quoteName('ordering') . '+1');
		$query->where($_db->quoteName('ordering') . ' >= ' . $_db->quote($this->userfield_insert_ordering_position));

		try
		{
			$_db->setQuery($query);
			$_db->execute();
		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}
	}

	/**
	 * Database query for inserting userfield
	 *
	 * @param   array   $values
	 *
	 * @return  int
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function writeUserfieldToVmTable($values)
	{
		$_db   = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $_db->getQuery(true);

		$query->insert($_db->quoteName('#__virtuemart_userfields'));
		$query->columns(
			array(
				$_db->quoteName('virtuemart_userfield_id'),
				$_db->quoteName('virtuemart_vendor_id'),
				$_db->quoteName('userfield_jplugin_id'),
				$_db->quoteName('name'),
				$_db->quoteName('title'),
				$_db->quoteName('description'),
				$_db->quoteName('type'),
				$_db->quoteName('maxlength'),
				$_db->quoteName('size'),
				$_db->quoteName('required'),
				$_db->quoteName('cols'),
				$_db->quoteName('rows'),
				$_db->quoteName('value'),
				$_db->quoteName('default'),
				$_db->quoteName('registration'),
				$_db->quoteName('shipment'),
				$_db->quoteName('account'),
				$_db->quoteName('cart'),
				$_db->quoteName('readonly'),
				$_db->quoteName('calculated'),
				$_db->quoteName('sys'),
				$_db->quoteName('userfield_params'),
				$_db->quoteName('ordering'),
				$_db->quoteName('shared'),
				$_db->quoteName('published'),
				$_db->quoteName('created_on'),
				$_db->quoteName('created_by'),
				$_db->quoteName('modified_on'),
				$_db->quoteName('modified_by'),
				$_db->quoteName('locked_on'),
				$_db->quoteName('locked_by'),
			)
		);
		$query->values(implode(',', $values));

		try
		{
			$_db->setQuery($query);
			$_db->execute();
		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}

		$inserted_id = $_db->insertid();

		return $inserted_id;
	}

	/**
	 * Database query for inserting userfield_values
	 *
	 * @param   array   $values
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function writeUserfieldValuesToVmTable($values)
	{
		$_db   = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $_db->getQuery(true);

		$query->insert($_db->quoteName('#__virtuemart_userfield_values'));
		$query->columns(
			array(
				$_db->quoteName('virtuemart_userfield_value_id'),
				$_db->quoteName('virtuemart_userfield_id'),
				$_db->quoteName('fieldtitle'),
				$_db->quoteName('fieldvalue'),
				$_db->quoteName('sys'),
				$_db->quoteName('ordering'),
				$_db->quoteName('created_on'),
				$_db->quoteName('created_by'),
				$_db->quoteName('modified_on'),
				$_db->quoteName('modified_by'),
				$_db->quoteName('locked_on'),
				$_db->quoteName('locked_by'),
			)
		);
		$query->values(implode(',', $values));

		try
		{
			$_db->setQuery($query);
			$_db->execute();
		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}
	}


	/**
	 * Gets IDs of all still installed userfields used by plugin
	 *
	 * @return  array   $bw_userfield_ids
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function getBwUserfieldIDs()
	{
		$bw_userfield_ids   = array();

		$_db   = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $_db->getQuery(true);

		$bw_userfield_names = array(
			$_db->quote('bw_newsletter_message'),
			$_db->quote('bw_newsletter_subscription'),
			$_db->quote('bw_newsletter_format'),
			$_db->quote('bw_gender'),
			$_db->quote('bw_newsletter_additional'),
		);

		$query->select($_db->quoteName('virtuemart_userfield_id'));
		$query->from($_db->quoteName('#__virtuemart_userfields'));
		$query->where($_db->quoteName('name') . ' IN (' . implode(',', $bw_userfield_names) . ')');

		try
		{
			$_db->setQuery($query);

			$bw_userfield_ids   = $_db->loadColumn();
		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}

		return $bw_userfield_ids;
	}

	/**
	 * Main method to delete all userfields still installed used by plugin and closes arising gap
	 *
	 * @param   array   $bw_userfield_ids
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function deleteBwUserfields($bw_userfield_ids)
	{
		foreach ($bw_userfield_ids as $item_id)
		{
			$ordering_number = $this->getOrderingNumberOfDeletedItem($item_id);

			$this->deleteItemAtUninstall($item_id);
			if ($ordering_number !== null)
			{
				$this->decrementOrderingColumnAtUninstall($ordering_number);
			}
		}
	}

	/**
	 * Gets ordering number of userfield to delete to close gap
	 *
	 * @param $item_id
	 *
	 * @return int
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function getOrderingNumberOfDeletedItem($item_id)
	{
		$ordering_number = null;

		$_db   = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $_db->getQuery(true);

		$query->select($_db->quoteName('ordering'));
		$query->from($_db->quoteName('#__virtuemart_userfields'));
		$query->where($_db->quoteName('virtuemart_userfield_id') . ' = ' . $_db->quote($item_id));

		try
		{
			$_db->setQuery($query);

			$ordering_number = $_db->loadResult();
		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}

		return $ordering_number;
	}

	/**
	 * Deletes single userfield used by plugin
	 *
	 * @param $item
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function deleteItemAtUninstall($item)
	{
		$_db   = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $_db->getQuery(true);

		$query->delete($_db->quoteName('#__virtuemart_userfields'));
		$query->where($_db->quoteName('virtuemart_userfield_id') . ' = ' . $_db->quote($item));

		try
		{
			$_db->setQuery($query);
			$_db->execute();
		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}
	}

	/**
	 * Closes ordering gap caused by deletion of userfield by decrementing all ordering values above deleted userfield
	 *
	 * @param   int     $ordering_number
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function decrementOrderingColumnAtUninstall($ordering_number)
	{
		$_db   = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $_db->getQuery(true);

		$query->update($_db->quoteName('#__virtuemart_userfields'));
		$query->set($_db->quoteName('ordering') . " = " . $_db->quoteName('ordering') . '-1');
		$query->where($_db->quoteName('ordering') . ' >= ' . $_db->quote($ordering_number));

		try
		{
			$_db->setQuery($query);
			$_db->execute();
		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}
	}

	/**
	 * Deletes related userfield values of a userfield
	 *
	 * @param   array   $bw_userfield_ids
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	protected function deleteBwUserfieldValues($bw_userfield_ids)
	{
		$_db   = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $_db->getQuery(true);

		$query->delete($_db->quoteName('#__virtuemart_userfield_values'));
		$query->where($_db->quoteName('virtuemart_userfield_id') . ' IN (' . implode(',', $bw_userfield_ids) . ')');

		try
		{
			$_db->setQuery($query);
			$_db->execute();
		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}
	}
}
