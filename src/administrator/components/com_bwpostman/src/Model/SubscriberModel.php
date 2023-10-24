<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single subscriber model for backend.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
 * @license GNU/GPL, see LICENSE.txt
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

namespace BoldtWebservice\Component\BwPostman\Administrator\Model;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use DOMDocument;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Form\Form;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Mail\MailHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Filter\InputFilter;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanSubscriberHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanMailinglistHelper;
use RuntimeException;
use SimpleXMLElement;
use stdClass;

/**
 * BwPostman subscriber model
 * Provides methods to add and edit subscribers/test-recipients
 *
 * @package		BwPostman-Admin
 *
 * @subpackage	Subscribers
 *
 * @since       0.9.1
 */
class SubscriberModel extends AdminModel
{
	/**
	 * Subscriber/Test-recipient id
	 *
	 * @var ?int
	 *
	 * @since       0.9.1
	 */
	private ?int $id = null;

	/**
	 * Subscriber/Test-recipient data
	 *
	 * @var ?array
	 *
	 * @since       0.9.1
	 */
	private ?array $data = null;

	/**
	 * property to hold permissions as array
	 *
	 * @var ?array $permissions
	 *
	 * @since       2.0.0
	 */
	public ?array $permissions;

	/**
	 * property to hold array of mailinglist ids
	 *
	 * @var array $list_id_values
	 *
	 * @since       4.0.0
	 */
	public array $list_id_values;


	/**
	 * Constructor
	 * Determines the subscriber/test-recipient ID
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function __construct()
	{
		$this->permissions = Factory::getApplication()->getUserState('com_bwpm.permissions', []);

		parent::__construct();

		$jinput = Factory::getApplication()->input;
		$cids   = $jinput->get('cid',  array(0), '');
		$this->setId((int) $cids[0]);
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	string $name    The table type to instantiate
	 * @param	string $prefix  A prefix for the table class name. Optional.
	 * @param	array  $options Configuration array for model. Optional.
	 *
	 * @return	boolean|Table	A database object
	 *
	 * @throws Exception
	 *
	 * @since  1.0.1
	 */
	public function getTable($name = 'Subscriber', $prefix = 'Administrator', $options = array())
	{
		return parent::getTable($name, $prefix, $options);
	}

	/**
	 * Method to reset the subscriber/test-recipient ID and subscriber/test-recipient data
	 *
	 * @param int $id Subscriber ID
	 *
	 * @since       0.9.1
	 */
	public function setId(int $id)
	{
		$this->id   = $id;
		$this->data = null;
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param object $record A record object.
	 *
	 * @return    boolean    True if allowed to change the state of the record. Defaults to the permission set in the
	 *                       component.
	 *
	 * @throws Exception
	 *
	 * @since    2.0.0
	 */
	protected function canEditState($record): bool
	{
		return BwPostmanHelper::canEditState('subscriber', (int) $record->id);
	}

	/**
	 * Method to get the data of a single subscriber for raw view
	 *
	 * @param int|null $sub_id Subscriber ID
	 *
	 * @return 	stdClass Subscriber
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function getSubscriberData(int $sub_id = null): stdClass
	{
		$subscriber = new stdClass();
		$db         = $this->_db;
		$pre_tbl_u  = $db->quoteName('u');
		$pre_tbl_s  = $db->quoteName('s');

		$query     = $db->getQuery(true);
		$subQuery1 = $db->getQuery(true);
		$subQuery2 = $db->getQuery(true);
		$subQuery3 = $db->getQuery(true);
		$subQuery4 = $db->getQuery(true);

		$subQuery1->select($pre_tbl_u . '.' . $db->quoteName('name'));
		$subQuery1->from($db->quoteName('#__users') . ' AS ' . $pre_tbl_u);
		$subQuery1->where($pre_tbl_u . '.' . $db->quoteName('id') . ' = ' . $pre_tbl_s . '.' . $db->quoteName('confirmed_by'));

		$subQuery2->select($pre_tbl_u . '.' . $db->quoteName('name'));
		$subQuery2->from($db->quoteName('#__users') . ' AS ' . $pre_tbl_u);
		$subQuery2->where($pre_tbl_u . '.' . $db->quoteName('id') . ' = ' . $pre_tbl_s . '.' . $db->quoteName('registered_by'));

		$subQuery3->select($pre_tbl_u . '.' . $db->quoteName('name'));
		$subQuery3->from($db->quoteName('#__users') . ' AS ' . $pre_tbl_u);
		$subQuery3->where($pre_tbl_u . '.' . $db->quoteName('id') . ' = ' . $pre_tbl_s . '.' . $db->quoteName('archived_by'));

		$subQuery4->select($pre_tbl_u . '.' . $db->quoteName('name'));
		$subQuery4->from($db->quoteName('#__users') . ' AS ' . $pre_tbl_u);
		$subQuery4->where($pre_tbl_u . '.' . $db->quoteName('id') . ' = ' . $pre_tbl_s . '.' . $db->quoteName('modified_by'));

		$query->select($pre_tbl_s . '.*');
		$query->select(
			' IF(' . $pre_tbl_s . '.' . $db->quoteName('confirmed_by') . ' = ' . 0 . ', "User", (' . $subQuery1 . ' ))
			AS ' . $db->quoteName('confirmed_by')
		);
		$query->select(
			' IF(' . $pre_tbl_s . '.' . $db->quoteName('registered_by') . ' = ' . 0 . ', "User", (' . $subQuery2 . ' ))
			AS ' . $db->quoteName('registered_by')
		);
		$query->select('(' . $subQuery3 . ') AS ' . $db->quoteName('archived_by'));
		$query->select('(' . $subQuery4 . ') AS ' . $db->quoteName('modified_by'));
		$query->select(
			' IF( ' . $pre_tbl_s . '.' . $db->quoteName('emailformat') . ' = ' . 0 . ', "Text", "HTML" )
			AS ' . $db->quoteName('emailformat')
		);
		$query->from($db->quoteName('#__bwpostman_subscribers') . ' AS ' . $pre_tbl_s);
		$query->where($pre_tbl_s . '.' . $db->quoteName('id') . ' = ' . (int) $sub_id);

		try
		{
			$db->setQuery($query);

			$subscriber = $db->loadObject();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		$mailinglist_ids = $this->getTable('SubscribersMailinglists')->getMailinglistIdsOfSubscriber((int)$sub_id);

		$subscriber->lists = $this->getTable('Mailinglist')->getCompleteMailinglistsOfSubscriber($mailinglist_ids);

		return $subscriber;
	}

	/**
	 * Method to send the activation link again
	 *
	 * @param $pks
	 *
	 * @return array
	 *
	 * @throws Exception
	 *
	 * @since       4.0.0
	 */
	public function sendconfirmmail($pks): array
	{
		$result = array();

		foreach ($pks as $pk)
		{
			$subscriber = $this->getItem($pk);
			$type = 0; // Send Registration email
			$itemid = BwPostmanSubscriberHelper::getMenuItemid('register');

			// Send registration confirmation mail
			$res = BwPostmanSubscriberHelper::sendMail($subscriber, $type, $itemid);

			if ($res === true)
			{ // Email has been sent
				$result['success'][] = $subscriber->email;
			}
			else
			{ // Email has not been sent
				$result['error'][] = $subscriber->email;
			}
		}

		return $result;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	$pk	The id of the primary key.
	 *
	 * @return    bool|object    Object on success, false on failure.
	 *
	 * @throws Exception
	 *
	 * @since	1.0,1
	 */
	public function getItem($pk = null)
	{
		$app          = Factory::getApplication();
		$data         = $app->getUserState('com_bwpostman.edit.subscriber.data');
		$mailinglists = $app->getUserState('com_bwpostman.edit.subscriber.mailinglists');

//		if (!empty($pk))
//		{
//			$pk = (int) $pk;
//		}
//		else
//		{
//			$pk = (int) $this->getState($this->getName() . '.id');
//		}

//		$log_options = array();
//		$logger = new BwLogger($log_options);
//		$logger->addEntry(new JLogEntry('Joomla state subscriber id: ' . $this->getState($this->getName() . '.id'), BwLogger::BW_DEBUG, 'subscribers'));
//		$logger->addEntry(new JLogEntry('My state subscriber id: ' . $app->getUserState('subscriber.id'), BwLogger::BW_DEBUG, 'subscribers'));

		//@SpecialNote: Workaround:$this->getState() doesn't appear reliable at J4 at new item, which is only saved (no save and close) and at a duplicated item
		//@SpecialNote: This misbehaviour leads to empty/old item, also it is stored
		if (empty($pk))
		{
			$jPk  = (int) $this->getState($this->getName() . '.id');
			$myPk = (int) $app->getUserState('subscriber.id', 0);
			$pk   = $jPk;

			if ($myPk > $jPk)
			{
				$pk = $myPk;

				if ($jPk > 0 && is_array($data))
				{
					$this->checkin($data[$jPk]);
				}
			}
		}

		if (!$data)
		{
			$item = parent::getItem((int)$pk);

			$item->list_id_values = $this->getTable('SubscribersMailinglists')->getMailinglistIdsOfSubscriber((int) $item->id);
		}
		else
		{
			$item = new stdClass();
			$item = ArrayHelper::toObject($data, $item);

			if (is_array($mailinglists))
			{
				$item->list_id_values = $mailinglists;
			}
			else
			{
				$item->list_id_values = '';
			}
		}

		if (property_exists($item, 'params'))
		{
			$registry     = new Registry($item->params);
			$item->params = $registry->toArray();
		}

		$app->setUserState('com_bwpostman.edit.subscriber.id', empty($pk) ? null : array($pk));
		$app->setUserState('com_bwpostman.edit.subscriber.data', null);
		$app->setUserState('com_bwpostman.edit.subscriber.mailinglists', null);

		return $item;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return    false|Form    A JForm object on success, false on failure
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	public function getForm($data = array(), $loadData = true)
	{
		Form::addFieldPath('JPATH_ADMINISTRATOR/components/com_bwpostman/models/fields');

		// Get the form.
		$form = $this->loadForm('com_bwpostman.subscriber', 'subscriber', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		$jinput = Factory::getApplication()->input;
		$id     = $jinput->get('id', 0, 'INT');
		$user   = Factory::getApplication()->getIdentity();

		// Check for existing subscriber.
		// Modify the form based on Edit State access controls.
		if ($id !== 0 && (!$user->authorise('bwpm.subscriber.edit.state', 'com_bwpostman.subscriber.' . $id))
			|| ($id === 0 && !$user->authorise('bwpm.edit.state', 'com_bwpostman')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('status', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a subscriber you can edit.
			$form->setFieldAttribute('state', 'filter', 'unset');
		}

		BwPostmanSubscriberHelper::customizeSubscriberDataFields($form);

		// check for new test-recipient
		if (Factory::getApplication()->getUserState('com_bwpostman.subscriber.new_test', '0') == '9')
		{
			$form->setFieldAttribute('status', 'default', '1');
		}

		$form->setValue('title', '', $form->getValue('name'));

		return $form;
	}

	/**
	* /**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	protected function loadFormData()
	{
		$recordId = Factory::getApplication()->getUserState('com_bwpostman.edit.subscriber.id');

		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_bwpostman.edit.subscriber.data', array());

		if (empty($data) || (is_object($data) && $recordId[0] != $data->id))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to store the subscriber data
	 *
	 * @param 	array   $data   associative array of data to store
	 *
	 * @return 	boolean         True on success
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function save($data): bool
	{
		//initialize variables
		$app        = Factory::getApplication();
		$date       = Factory::getDate();
		$user       = $app->getIdentity();
		$result     = true;
		$subscriber = new stdClass();

		try
		{
			// Check input values for links
			if (!BwPostmanSubscriberHelper::checkSubscriberInputFields($data))
			{
				return false;
			}

			// Get the user_id from the users-table
			$data['user_id'] = BwPostmanSubscriberHelper::getJoomlaUserIdByEmail($data['email']);

			// merge ml-arrays, single array may not exist, therefore array_merge would not give a result
			BwPostmanMailinglistHelper::mergeMailinglists($data);

			// Admin creates a new subscriber?
			if (!$data['id'])
			{
				$subsTable        = $this->getTable();
				$data['editlink'] = $subsTable->getEditlink();

				// Admin doesn't confirm the subscriber?
				if (!array_key_exists('status', $data) || $data['status'] !== 1)
				{
					$data['activation'] = $subsTable->createActivation();
				}
			}

			$time      = $date->toSql();
			$confirmed = 0;

			// New subscriber
			if (empty($data['id']))
			{
				$data['registration_date'] = $time;
				$data['registered_by']     = $user->get('id');
				$data['registration_ip']   = $data['ip'];
				$new_subscriber            = true;

				// New subscriber is confirmed by administrator
				if ($data['status'] != '0')
				{
					$data['confirmation_date'] = $time;
					$data['confirmation_ip']   = $data['ip'];
					$data['confirmed_by']      = $user->get('id');
					$data['activation']        = '';
					$confirmed                 = 1;
				}
				else
				{
					$data['confirmed_by'] = -1;
				}
			}
			// Existing subscriber
			else
			{
				$new_subscriber = false;

				if ($data['status'] == '0')
				{
					// Unconfirmed subscribers do not have a confirmed_by value
					$data['confirmed_by'] = 0;
				}
			}

			if (parent::save($data))
			{
				// Get the subscriber ID
				$subscriber_id = (int)$app->getUserState('com_bwpostman.subscriber.id', 0);
				$subsMlTable   = $this->getTable('SubscribersMailinglists');

				// Delete all entries of the subscriber from subscribers_mailinglists-Table
				$subsMlTable->deleteMailinglistsOfSubscriber($subscriber_id);

				if (!empty($data['mailinglists']))
				{
					$list_id_values = $data['mailinglists'];

					// Store subscribed mailinglists in newsletters_mailinglists-table
					$subsMlTable->storeMailinglistsOfSubscriber($subscriber_id, $list_id_values);
				}

				// New subscriber has to confirm the account by himself
				if (($new_subscriber) && (!$confirmed))
				{
					$subscriber->name       = $data['name'];
					$subscriber->firstname  = $data['firstname'];
					$subscriber->email      = $data['email'];
					$subscriber->activation = $data['activation'];

					// Send registration confirmation mail
					$itemid = BwPostmanSubscriberHelper::getMenuItemid('subscriber');
					$res    = BwPostmanSubscriberHelper::sendMail($subscriber, 4, $itemid);

					if (!$res)
					{
						$app->enqueueMessage(Text::_('COM_BWPOSTMAN_SUB_SEND_REGISTRATION_FAILED'), 'error');
						return false;
					}
				}
			}
			else
			{
				if (!empty($data['mailinglists']))
				{
					$app->setUserState('com_bwpostman.edit.subscriber.mailinglists', $data['mailinglists']);
				}

				$result = false;
			}
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $result;
	}


	/**
	 * Method to (un)archive a subscriber/test-recipient
	 * --> when unarchiving it is called by the archive-controller
	 *
	 * @param array $cid     Subscriber/Test-recipient IDs
	 * @param int   $archive Task --> 1 = archive, 0 = unarchive
	 *
	 * @return	boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function archive(array $cid = array(), int $archive = 1): bool
	{
		$app  = Factory::getApplication();
		$date = $this->_db->quote(Factory::getDate()->toSql(), false);
		$user = $app->getIdentity();
		$db   = $this->_db;
		$cid  = ArrayHelper::toInteger($cid);

		if ($archive == 1)
		{
			$userid	= (int)$user->get('id');

			// Access check.
			foreach ($cid as $id)
			{
				if (!BwPostmanHelper::canArchive('subscriber', 0, $id))
				{
					return false;
				}
			}
		}
		else
		{ //
			$userid	= -1;

			// Access check.
			foreach ($cid as $id)
			{
				if (!BwPostmanHelper::canRestore('subscriber', $id))
				{
					return false;
				}
			}

			$date = 'null';
		}

		if (count($cid))
		{
			$query	= $db->getQuery(true);

			$query->update($db->quoteName('#__bwpostman_subscribers'));
			$query->set($db->quoteName('archive_flag') . " = " . $archive);
			$query->set($db->quoteName('archive_date') . " = " . $date);
			$query->set($db->quoteName('archived_by') . " = " . $userid);
			$query->where($db->quoteName('id') . ' IN (' . implode(',', $cid) . ')');

			try
			{
				$db->setQuery($query);
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$app->enqueueMessage($e->getMessage(), 'error');
			}
		}

		return true;
	}

	/**
	 * Method to remove one or more subscribers/test-recipients
	 * --> is called by the archive-controller
	 *
	 * @param	array $pks      Subscriber/Test-recipient IDs
	 *
	 * @return	boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function delete(&$pks): bool
	{
		$pks = ArrayHelper::toInteger($pks);

		// Access check.
		foreach ($pks as $id)
		{
			if (!BwPostmanHelper::canDelete('subscriber', $id))
			{
				return false;
			}
		}

		if (count($pks))
		{
			// Delete subscriber from subscribers-table
			try
			{
				parent::delete($pks);
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			// Delete subscribed mailinglists from subscribers_mailinglists-table
			foreach ($pks as $pk)
			{
				$subsMlTable = $this->getTable('SubscribersMailinglists');
				$subsMlTable->deleteMailinglistsOfSubscriber($pk);
			}
		}

		return true;
	}

	/**
	 * Method to get the import data from the import file
	 *
	 * @param array $data         associative array of data which we need to prepare the storing to store
	 * @param array $ret_maildata associative array of subscriber email data --> we need this if the admin didn't confirm the accounts
	 *
	 * @return 	boolean true on success
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function import(array $data, array &$ret_maildata): bool
	{
		// Access check
		if (!$this->permissions['subscriber']['create'])
		{
			return false;
		}

		$app     = Factory::getApplication();
		$session = $app->getSession();
		$ext     = '';
		$dest    = '';

		$import_general_data = $session->get('import_general_data');

		// Load the session data which are needed for import operation
		if(isset($import_general_data) && is_array($import_general_data))
		{
			$dest = $import_general_data['dest'];
			$ext  = $import_general_data['ext'];
		}

		$fh = fopen($dest, 'r');

		if ($fh === false)
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_UNABLE_TO_OPEN_FILE'), 'warning');
			return false;
		}

		$date = Factory::getDate();
		$time = $date->toSql();
		$user = $app->getIdentity();

		// Load the post data
		$import_fields = $data['import_fields'];
		$db_fields     = $data['db_fields'];

		// merge ml-arrays, single array may not exist, therefore array_merge would not give a result
		$data['jform'] = $data['jform'] ?? array();
		$mailinglists = BwPostmanMailinglistHelper::mergeMailinglistsOnly($data['jform']);

		// We need the database columns for subsequent checking of the values
		$colEmail = in_array('email', $db_fields);

		if ($colEmail ===  false)
		{
			// Couldn't find an email column --> return because email is mandatory for import
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_SUB_ERROR_NO_EMAIL_COLUMN'), 'error');
			return false;
		}

		if ($ext == 'csv')
		{
			// We only need the column numbers of the csv file which are coded like "column_#"
			for ($i = 0;$i < count($import_fields);$i++)
			{
				$tmp               = explode("_", $import_fields[$i]);
				$import_fields[$i] = $tmp[1];
			}
		}

		// Create correlation of db fields and csv fields in form of $correlation table[csv_column_number] = db_column_name
		$colNumToDBName = array();

		for ($i = 0; $i < count($db_fields); $i++)
		{
			$colNumToDBName[$import_fields[$i]] = $db_fields[$i];
			$values[$db_fields[$i]] = null;
		}

		$remote_ip = $app->input->server->get('REMOTE_ADDR', '', '');

		// Add and initialize additional fields, depending on confirm-box value
		$values["id"]                = 0;
		$values["user_id"]           = 0;
		$values["registration_date"] = $time;
		$values["registered_by"]     = $user->get('id');
		$values["registration_ip"]   = $remote_ip;
		$values["confirmation_date"] = null;
		$values["confirmation_ip"]   = 0;
		$values["confirmed_by"]      = -1;
		$values["editlink"]          = '';
		$values["activation"]        = '';
		$values["emailformat"]       = $data['emailformat'];

		if (isset($data['confirm']))
		{
			$confirm          = true;
			$values["status"] = $data['confirm'];
		}
		else
		{
			$confirm          = false;
			$values["status"] = 0;
		}

		$doValidation = true;

		if (!isset($data['validate']) || $data['validate'] === 0)
		{
			$doValidation = false;
		}

		// SendEmailActivation-Array --> 2dimensions [sendmail][csv_row], [sendmail][email]
		$mail = array();
		$session->set('com_bwpostman.subscriber.import.messages', array());

		$row_nbr = 0;
		$filter = new InputFilter(array(), array(), 0, 0);

		if ($ext == 'csv')
		{ // Format = csv
			$delimiter = '';
			$enclosure = '"';
			$caption   = false;

			// Load the session data which are needed for csv import operation
			if(isset($import_general_data) && is_array($import_general_data))
			{
				if (isset($import_general_data['caption']))
				{
					$caption = $import_general_data['caption'];
				}

				if (isset($import_general_data['delimiter']))
				{
					$delimiter = stripcslashes($import_general_data['delimiter']);
				}

				if (!empty($import_general_data['enclosure']))
				{
					$enclosure = stripcslashes($import_general_data['enclosure']);
				}
			}

			$app->setUserState('com_bwpostman.subscriber.fileformat', 'csv');

			// Get data from the file and store them into an array
			$row = fgetcsv($fh, 0, $delimiter, $enclosure);

			while(is_array($row))
			{
				$intKeys = array(
					'emailformat',
					'gender',
					'status',
				);

				foreach($colNumToDBName as $key => $value)
				{
					// Reset the import values. We should do this for every import row preventively.
					$values[$key] = null;

					// Get the values from the csv and filter them
					$filterType = 'STRING';

					if (in_array(strtolower($value), $intKeys))
					{
						$filterType = 'INT';
					}

					$values[$value] = $filter->clean($row[$key], $filterType);
				}

				// Count CSV-file line numbers
				$row_nbr++;

				// If caption is set, don't read the first line of the csv-file
				if ($caption)
				{
					$caption = false;
					$row     = fgetcsv($fh, 0, $delimiter, $enclosure);
					continue;
				}

				// Save the row
				$this->save_import($values, $confirm, $doValidation, $row_nbr, $mailinglists, $ret_maildata);

				if (count($ret_maildata))
				{
					$mail = $ret_maildata;
				}

				$row = fgetcsv($fh, 0, $delimiter, $enclosure);
			} // Endif format == csv
		}
		else
		{ // Format == xml
			$app->setUserState('com_bwpostman.subscriber.fileformat', 'xml');

			// Parse the XML
			$parser = new SimpleXMLElement($dest, null, true);

			if ($parser->getName() !== "subscribers")
			{
				// TODO: There is no bwpostman xml file! Perhaps one may proceed if there are appropriate fields
				return false;
			}

			// Get all fields from the xml file for listing and selecting by the user
			$addresses = $parser->xpath("subscriber");

			$subscribers = array();

			foreach ($addresses as $subscriber)
			{
				$subscribers[] = $subscriber;
			}

			foreach ($subscribers as $subscriber)
			{
				$xml_fields = get_object_vars($subscriber);

				foreach($colNumToDBName as $key => $value)
				{
					// Reset the import values. We should do this for every import dataset preventively.
					$values[$key] = 0;

					// Get the values from the xml
					$values[$value] = $filter->clean($xml_fields[$key], 'STRING');
				}

				// Count XML-dataset numbers
				$row_nbr++;

				// Save the data
				$this->save_import($values, $confirm, $doValidation, $row_nbr, $mailinglists, $ret_maildata);

				if ($ret_maildata)
				{
					$mail[] = $ret_maildata;
				}
			}
		}

		fclose($fh); // Close the file
		unlink($dest);

		// Return the mailing data array
		$ret_maildata = $mail;

		return true;
	}

	/**
	 * Method to save single import data set
	 *
	 * @param array   $values       associative array of data to store
	 * @param boolean $confirm      Confirm --> 0 = do not confirm, 1 = confirm
	 * @param boolean $doValidation Validate email address --> 0 = do not validate, 1 = validate
	 * @param int     $row          CSV row --> we will use this only if the format is csv
	 * @param array   $mailinglists array of mailinglist IDs
	 * @param array   $ret_maildata associative object of subscriber email data
	 *
	 * @return	Boolean true on success
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function save_import(array $values, bool $confirm, bool $doValidation, int $row, array $mailinglists, array &$ret_maildata): bool
	{
		// Access check
		if (!$this->permissions['subscriber']['create'])
		{
			return false;
		}

		$session = Factory::getApplication()->getSession();

		$importMessages = $session->get('com_bwpostman.subscriber.import.messages', array());

		// First fast check if there is a valid email address
		if (!MailHelper::isEmailAddress($values['email']))
		{
			$err['row']   = $row;
			$err['email'] = $values['email'];
			$err['msg']   = Text::_('COM_BWPOSTMAN_SUB_ERROR_IMPORT_INVALID_EMAIL');
			$err['type']  = 'error';

			$importMessages['import_err'][] = $err;
			$session->set('com_bwpostman.subscriber.import.messages', $importMessages);

			return false;
		}

		// Second more detailed check if the email address is valid and exists

		if ($doValidation)
		{
			$emailValidationResult = BwPostmanSubscriberHelper::validateEmail($values['email']);

			if ($emailValidationResult !== true)
			{
				$err['row']   = $row;
				$err['email'] = $values['email'];
				$err['msg']   = Text::sprintf('COM_BWPOSTMAN_SUB_ERROR_VALIDATING_EMAIL', $values['email']);
				$err['msg']  .= false;
				$err['type']  = 'error';

				$importMessages['import_err'][] = $err;
				$session->set('com_bwpostman.subscriber.import.messages', $importMessages);

				return false;
			}
		}

		$date         = Factory::getDate();
		$time         = $date->toSql();
		$user         = Factory::getApplication()->getIdentity();

		// We may set confirmation data if the confirm-box is checked and the import value does not stand against
		// @ToDo: What if a migration is done and all fields BwPostman uses, are exported? Values like confirmation or
		// registration are present at the export,but are not processed!
		$remote_ip = Factory::getApplication()->input->server->get('REMOTE_ADDR', '', '');

		if ($confirm && $values['status'] != '0')
		{
			$values["confirmation_date"] = $time;
			$values["confirmed_by"]      = $user->get('id');
			$values["confirmation_ip"]   = $remote_ip;
		}

		$subsTable = $this->getTable();

		try
		{
			// Check if the email address exists in the subscribers table
			$subscriber = $subsTable->getSubscriberDataByEmail($values);

			if (isset($subscriber->id))
			{ // A recipient with this email address already exists
				if ($values['status'] != '9')
				{ // regular subscriber was found
					$err['row']   = $row;        // Get CSV row
					$err['email'] = $values['email'];

					if ($subscriber->archive_flag)
					{ // Subscriber already exists but is archived
						$err['msg'] = Text::_('COM_BWPOSTMAN_SUB_ERROR_IMPORT_ACCOUNTBLOCKED_BY_SYSTEM');
					}
					else
					{ // Subscriber already exists
						$err['msg'] = Text::_('COM_BWPOSTMAN_SUB_ERROR_IMPORT_ACCOUNTEXISTS');

						if ($subscriber->activation)
						{ // Account is not activated
							$err['msg'] = Text::_('COM_BWPOSTMAN_SUB_ERROR_IMPORT_ACCOUNTNOTACTIVATED');
						}
					}

					$err['id']   = $subscriber->id;
					$err['type'] = 'error';

					$importMessages['import_err'][] = $err;
					$session->set('com_bwpostman.subscriber.import.messages', $importMessages);

					return false;
				}
				else
				{ // a test-recipient with same emailformat was found
					// Check if the test-recipient in the database has the same emailformat as the one who shall be imported
					if ($subscriber->emailformat == $values['emailformat'])
					{
						$err['row']   = $row;        // Get CSV row
						$err['email'] = $values['email'];
						$err['msg']   = Text::_('COM_BWPOSTMAN_TEST_IMPORT_ERROR_ACCOUNTEXISTS');

						if ($subscriber->archive_flag == 1)
						{
							$err['msg'] = Text::_('COM_BWPOSTMAN_TEST_IMPORT_ERROR_ACCOUNTARCHIVED');
						}

						$err['id']    = $subscriber->id;
						$err['type']  = 'error';

						$importMessages['import_err'][] = $err;
						$session->set('com_bwpostman.subscriber.import.messages', $importMessages);

						return false;
					}
				}
			}

			// Check for valid status value and set the status value
			if (($values['status'] != '0') && ($values['status'] != '1') && ($values['status'] != '9'))
			{ // Wrong status value
				$warn[0]['row']   = $row;
				$warn[0]['email'] = $values['email'];

				// Set the columns and values
				if ($confirm)
				{ // Status = 1
					$warn[0]['msg'] = Text::_('COM_BWPOSTMAN_SUB_IMPORT_INVALID_STATUS_CONFIRMED');

					if (empty($values['status']))
					{
						$warn[0]['msg'] = Text::_('COM_BWPOSTMAN_SUB_IMPORT_NO_STATUS_CONFIRMED');
					}
				}
				else
				{ // Status = 0
					$warn[0]['msg'] = Text::_('COM_BWPOSTMAN_SUB_IMPORT_INVALID_STATUS_UNCONFIRMED');

					if (empty($values['status']))
					{
						$warn[0]['msg'] = Text::_('COM_BWPOSTMAN_SUB_IMPORT_NO_STATUS_UNCONFIRMED');
					}
				}

				$values["status"] = $confirm;
			}

			if ($values['status'] == '0')
			{
				$values["activation"] = $subsTable->createActivation();
			}

			// Check if the subscriber email address exists in the users-table
			$user_id = BwPostmanSubscriberHelper::getJoomlaUserIdByEmail($values['email']);

			$values["user_id"] = $user_id;

			if ($values["status"] != '9')
			{
				$values['editlink'] = $subsTable->getEditlink();
			}

			if (parent::save($values))
			{
				// Workaround because state is not reliable on Joomla 4
//				$subscriber_id = $this->getState('com_bwpostman.subscriber.id');
				$subscriber_id = $subsTable->getSubscriberIdByEmail($values['email']);

				//Save Mailinglists if selected
				if ($mailinglists && ($values['status'] != '9'))
				{
					$subsMlTable = $this->getTable('SubscribersMailinglists');
					$subsMlTable->storeMailinglistsOfSubscriber($subscriber_id, $mailinglists);
				}

				//Send Email, if confirmed is not set
				if (!$confirm && $values["status"] == 0)
				{
					$subscriber_emaildata = new stdClass();

					$subscriber_emaildata->row        = $row;
					$subscriber_emaildata->name       = $values["name"];
					$subscriber_emaildata->firstname  = $values["firstname"];
					$subscriber_emaildata->email      = $values["email"];
					$subscriber_emaildata->activation = $values["activation"];
				}

				$success['row']   = $row;
				$success['email'] = $values['email'];
				$success['msg']   = Text::sprintf('COM_BWPOSTMAN_SUB_IMPORT_EMAIL', $values['email']);
				$success['type']  = 'success';

				$importMessages['import_success'][] = $success;
				$session->set('com_bwpostman.subscriber.import.messages', $importMessages);
			}
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		if (isset($subscriber_emaildata))
		{
			if ($subscriber_emaildata)
			{
				$ret_maildata[] = $subscriber_emaildata;
			}
		}

		return true;
	}

	/**
	 * Method to export selected data
	 *
	 * @param array $data associative array of export option data
	 *
	 * @return bool|string $output     File content
	 *
	 * @throws Exception
	 * @since       0.9.1
	 */
	public function export(array $data)
	{
		// Access check
		if (!$this->permissions['com']['admin'])
		{
			return false;
		}

		$output = '';

		try
		{
			if ($data['fileformat'] == 'csv')
			{ // Fileformat = csv
				$delimiter = $data['delimiter'];
				$enclosure = $data['enclosure'];
				$newline   = "\n";

				$export_fields = $data['export_fields'];

				$export_fields_tmp = array();

				foreach ($export_fields AS $export_field)
				{
					$export_fields_tmp[] = $enclosure . $export_field . $enclosure;
				}

				$output = implode($delimiter, $export_fields_tmp) . $newline;

				$subscribers_export = $this->getSubscribersToExport($data);

				if (is_array($subscribers_export))
				{
					foreach ($subscribers_export AS $subscriber)
					{
						$subscriber_export_tmp = array();

						foreach ($subscriber AS $subscriber_tmp)
						{
							// Insert enclosure
							$subscriber_export_tmp[] = $enclosure . $subscriber_tmp . $enclosure;
						}

						// Write file
						$output .= implode($delimiter, $subscriber_export_tmp) . $newline;
					}
				}
			}
			else
			{ // Fileformat == xml
				$subscribers_export = $this->getSubscribersToExport($data);

				if (is_array($subscribers_export))
				{
					$output = $this->processXmlExport($subscribers_export);
				}
			}

			if ($subscribers_export === false)
			{
				return false;
			}
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $output;
	}

	/**
	 * Method to build the WHERE-clause for the export function
	 *
	 * @param integer $status0  Status = 0 --> account is not confirmed
	 * @param integer $status1  Status = 1 --> account is confirmed
	 * @param integer $status9  Status = 9 --> subscriber is test-recipient
	 * @param integer $archive0 Archive_flag = 0 --> subscriber is not archived
	 * @param integer $archive1 Archive_flag = 1 --> subscriber is archived
	 *
	 * @return 	String  $subQuery   WHERE-clause
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	private function buildExportSubQuery(int $status0 = 0, int $status1 = 0, int $status9 = 0, int $archive0 = 0, int $archive1 = 0): string
	{
		$db       = $this->_db;
		$subQuery = '';
		$where    = false;

		if ($status0 && $status1 && $status9)
		{
		}
		elseif ($status0 && $status1)
		{
			$subQuery = " WHERE {$db->quoteName('status')} != " . 9;
			$where = true;
		}
		elseif ($status0 && $status9)
		{
			$subQuery = " WHERE {$db->quoteName('status')} != " . 1;
			$where = true;
		}
		elseif ($status1 && $status9)
		{
			$subQuery = " WHERE {$db->quoteName('status')} != " . 0;
			$where = true;
		}
		elseif ($status0)
		{
			$subQuery = " WHERE {$db->quoteName('status')} = " . 0;
			$where = true;
		}
		elseif ($status1)
		{
			$subQuery = " WHERE {$db->quoteName('status')} = " . 1;
			$where = true;
		}
		elseif ($status9)
		{
			$subQuery = " WHERE {$db->quoteName('status')} = " . 9;
			$where = true;
		}

		if ($archive0 && $archive1)
		{
		}
		elseif ($archive0)
		{
			if ($where)
			{
				$subQuery .= " AND {$db->quoteName('archive_flag')} = " . 0;
				$where = true;
			}
			else
			{
				$subQuery = " WHERE {$db->quoteName('archive_flag')} = " . 0;
				$where = true;
			}
		}
		elseif ($archive1)
		{
			if ($where)
			{
				$subQuery .= " AND {$db->quoteName('archive_flag')} = " . 1;
				$where = true;
			}
			else
			{
				$subQuery = " WHERE {$db->quoteName('archive_flag')} = " . 1;
				$where = true;
			}
		}

		$mlToExport = Factory::getApplication()->getUserState('com_bwpostman.subscribers.mlToExport', '');

		if ($mlToExport !== '')
		{
			$subsMlTable         = $this->getTable('SubscribersMailinglists');
			$filteredSubscribers = $subsMlTable->getSubscribersOfMailinglist((int)$mlToExport);

			if ($where)
			{
				$subQuery .= " AND {$db->quoteName('id')} IN (" . implode(',', $filteredSubscribers) . ")";
			}
			else
			{
				$subQuery .= " WHERE {$db->quoteName('id')} IN (" . implode(',', $filteredSubscribers) . ")";
			}
		}

		return $subQuery;
	}

	/**
	 * Method to create the XML file content
	 *
	 * @param array $subscribers the subscribers to export
	 *
	 * @return string
	 *
	 * @since       3.0.0
	 */
	private function processXmlExport(array $subscribers): string
	{
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"  standalone="yes"?><subscribers></subscribers>');

		$xml->addAttribute('version', '1.0');
		$xml->addAttribute('datetime', date('Y-m-d H:i:s'));

		foreach ($subscribers as $subscriber)
		{
			$singleSubscriber = $xml->addChild('subscriber');

			foreach ($subscriber as $key => $value)
			{
				$singleSubscriber->addChild($key, $value);
			}
		}

		// Reformat XML string with new lines for each entry
		$dom = new DOMDocument('1.0');
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->loadXML($xml->asXML());

		return $dom->saveXML();
	}

	/**
	 * Method to perform batch operations on an item or a set of items.
	 *
	 * @param   array  $commands  		An array of commands to perform.
	 * @param   array  $pks       		An array of item ids.
	 * @param   array  $contexts		An array of contexts.
	 *
	 * @return  array|false|int  Returns array on success, false on failure.
	 *
	 * @throws Exception
	 *
	 * @since   1.0.8
	 */
	public function batch($commands, $pks, $contexts)
	{
		// Sanitize user ids.
		$old_list = Factory::getApplication()->getSession()->get('com_bwpostman.subscriber.batch_filter_mailinglist', null);
		$pks      = array_unique($pks);
		$pks      = ArrayHelper::toInteger($pks);

		// Access check
		if (!BwPostmanHelper::canEdit('subscriber', $pks))
		{
			return false;
		}

		// Remove any values of zero.
		if (array_search(0, $pks, true))
		{
			unset($pks[array_search(0, $pks, true)]);
		}

		if (empty($pks))
		{
			Factory::getApplication()->enqueueMessage(Text::_('JGLOBAL_NO_ITEM_SELECTED'), 'error');
			return false;
		}

		$done   = false;
		$result = array();

		if ($commands['mailinglist_id'] > 0)
		{
			$cmd = ArrayHelper::getValue($commands, 'batch-task', 'a');

			if ($cmd == 's')
			{
				$result[0] = $this->batchAdd($commands['mailinglist_id'], $pks);

				if (is_array($result))
				{
					$done = true;
				}
			}

			if ($cmd == 'u')
			{
				$result[0] = $this->batchRemove($commands['mailinglist_id'], $pks);

				if (is_array($result))
				{
					$done = true;
				}
			}

			if ($cmd == 'm' && $old_list)
			{
				if ($commands['mailinglist_id'] != $old_list)
				{
					$result[0] = $this->batchAdd($commands['mailinglist_id'], $pks);
					$result[1] = $this->batchRemove($old_list, $pks);

					if (is_array($result[0]) && is_array($result[1]))
					{
						$done = true;
					}
					elseif (is_array($result[0]))
					{
						Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_SUB_BATCH_RESULT_ERROR_MOVE_REMOVE'), 'error');
						return false;
					}
					else
					{
						Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_SUB_BATCH_RESULT_ERROR_MOVE_ADD'), 'error');
						return false;
					}
				}
				else
				{
					return (int) - $old_list;
				}
			}
		}

		if (!$done)
		{
			Factory::getApplication()->enqueueMessage(Text::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));
			return false;
		}

		// Clear the cache unset Session variable
		$this->cleanCache();
		Factory::getApplication()->getSession()->clear('com_bwpostman.subscriber.batch_filter_mailinglist');

		return $result;
	}

	/**
	 * Batch add subscribers to a new mailinglist.
	 *
	 * @param integer $mailinglist The new mailinglist.
	 * @param array   $pks         An array of row IDs.
	 *
	 * @return  array|false  An array of result values on success, boolean false on failure.
	 *
	 * @throws Exception
	 *
	 * @since	1.0.8
	 */
	protected function batchAdd(int $mailinglist, array $pks)
	{
		// Access check
		if (!BwPostmanHelper::canEdit('subscriber', $pks))
		{
			return false;
		}

		$result_set  = array();
		$subscribed  = 0;
		$skipped     = 0;
		$subsMlTable = $this->getTable('SubscribersMailinglists');

		// Subscribers exist so let's proceed
		while (!empty($pks))
		{
			// Pop the first id off the stack
			$pk              = array_shift($pks);
			$hasSubscription = $subsMlTable->hasSubscriptionForMailinglist($pk, $mailinglist);

			// If no subscription to this mailinglist then subscribe, else only count
			if (!$hasSubscription)
			{
				$subsMlTable->storeMailinglistsOfSubscriber($pk, array($mailinglist));
				$subscribed++;
			}
			else
			{
				$skipped++;
			}
		}

		$result_set['task']    = 'subscribe';
		$result_set['done']    = $subscribed;
		$result_set['skipped'] = $skipped;

		return $result_set;
	}

	/**
	 * Batch unsubscribe subscribers from a mailinglist.
	 *
	 * @param integer $mailinglist The mailinglist ID.
	 * @param array   $pks         An array of row IDs.
	 *
	 * @return  array|bool
	 *
	 * @throws Exception
	 *
	 * @since   1.0.8
	 */
	protected function batchRemove(int $mailinglist, array $pks)
	{
		// Access check
		if (!BwPostmanHelper::canEdit('subscriber', $pks))
		{
			return false;
		}

		$result_set   = array();
		$unsubscribed = 0;
		$skipped      = 0;
		$subsMlTable  = $this->getTable('SubscribersMailinglists');

		// Subscribers exist so let's proceed
		while (!empty($pks))
		{
			// Pop the first id off the stack
			$pk = array_shift($pks);

			// Check if subscriber has already subscribed to the desired mailinglist
			$hasSubscription = $subsMlTable->hasSubscriptionForMailinglist($pk, $mailinglist);

			// If subscription to this mailinglist, then unsubscribe, else only count
			if ($hasSubscription)
			{
				$delResult = $subsMlTable->deleteMailinglistsOfSubscriber($pk, array($mailinglist));

				if ($delResult)
				{
					$unsubscribed++;
				}
				else{
					$skipped++;
				}
			}
			else
			{
				$skipped++;
			}
		}

		$result_set['task']    = 'unsubscribe';
		$result_set['done']    = $unsubscribed;
		$result_set['skipped'] = $skipped;

		return $result_set;
	}

	/**
	 * Method to get the subscribers to export
	 *
	 * @param array $data
	 *
	 * @return mixed
	 *
	 * @throws Exception
	 *
	 * @since 3.0.0
	 */
	private function getSubscribersToExport(array $data)
	{
		$db            = $this->_db;
		$export_fields = $data['export_fields'];


		// Build the main subQuery
		$subQuery = $this->buildExportSubQuery(
			isset($data['status0']) ? (int)$data['status0'] : '0',
			isset($data['status1']) ? (int)$data['status1'] : '0',
			isset($data['status9']) ? (int)$data['status9'] : '0',
			isset($data['archive0']) ? (int)$data['archive0'] : '0',
			isset($data['archive1']) ? (int)$data['archive1'] : '0'
		);

		foreach ($export_fields as $key => $value)
		{
			$export_fields[$key] = $db->quoteName($value);
		}

		$export_fields_str = implode(",", $export_fields);

		// Build the query
		$query = $db->getQuery(true);

		$query->select($export_fields_str);
		$query->from($db->quoteName('#__bwpostman_subscribers'));
		$query .= $subQuery;

		try
		{
			$db->setQuery($query);

			$subscribersList = $db->loadAssocList();

			return $subscribersList;
		}
		catch (RuntimeException $e)
		{
			$msg = Text::_('COM_BWPOSTMAN_SUB_EXPORT_ERROR_GET_SUBSCRIBERS') . '<br />' . $e->getMessage();
			Factory::getApplication()->enqueueMessage($msg, 'error');
			return false;
		}
	}
}
