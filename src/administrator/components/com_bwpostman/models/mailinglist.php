<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single mailinglist model for backend.
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Import MODEL and Helper object class
jimport('joomla.application.component.modeladmin');

use Joomla\Utilities\ArrayHelper as ArrayHelper;

// Require helper class
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php');

/**
 * BwPostman mailinglist model
 * Provides methods to add and edit mailinglists
 *
 * @package		BwPostman-Admin
 *
 * @subpackage	Mailinglists
 *
 * @since       0.9.1
 */
class BwPostmanModelMailinglist extends JModelAdmin
{
	/**
	 * Mailinglist ID
	 *
	 * @var integer
	 *
	 * @since       0.9.1
	 */
	private $id = null;

	/**
	 * Mailinglist data
	 *
	 * @var array
	 *
	 * @since       0.9.1
	 */
	private $data = null;

	/**
	 * Constructor
	 * Determines the mailinglist ID
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function __construct()
	{
		parent::__construct();

		$jinput	= JFactory::getApplication()->input;
		$array	= $jinput->get('cid',  0, '');
		$this->setId((int) $array[0]);
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	string  $type	    The table type to instantiate
	 * @param	string	$prefix     A prefix for the table class name. Optional.
	 * @param	array	$config     Configuration array for model. Optional.
	 *
	 * @return	boolean|JTable	A database object
	 *
	 * @since  1.0.1
	 */
	public function getTable($type = 'Mailinglists', $prefix = 'BwPostmanTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to reset the mailinglist ID and mailinglist data
	 *
	 * @access	public
	 *
	 * @param	int $id     Mailinglist ID
	 *
	 * @since       0.9.1
	 */
	public function setId($id)
	{
		$this->id   = $id;
		$this->data = null;
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to change the state of the record.
	 *
	 * @since	1.0.1
	 */
	protected function canEditState($record)
	{
		$permission = BwPostmanHelper::canEditState('mailinglist', (int) $record->id);

		return $permission;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @throws Exception
	 *
	 * @since   1.0.1
	 */
	public function getItem($pk = null)
	{
		$app	= JFactory::getApplication();
		$data	= $app->getUserState('com_bwpostman.edit.mailinglist.data', null);

		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');

		if (!$data)
		{
			$item	= parent::getItem($pk);
		}
		else
		{
			$item	= new stdClass();
			foreach ($data as $key => $value)
			{
				$item->$key	= $value;
			}
		}

		return $item;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_bwpostman.mailinglist', 'mailinglist', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}

		$jinput = JFactory::getApplication()->input;

		// The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
		if ($jinput->get('a_id'))
		{
			$id = $jinput->get('a_id', 0);
		}
		// The back end uses id so we use that the rest of the time and set it to 0 by default.
		else
		{
			$id = $jinput->get('id', 0);
		}

		// Determine correct permissions to check.
		if ($this->getState('mailinglist.id'))
		{
			$id = $this->getState('mailinglist.id');
			// Existing record. Can only edit in selected parent.
			$form->setFieldAttribute('parent_id', 'action', 'bwpm.edit');
			// Existing record. Can only edit own mailinglists in selected parent.
			$form->setFieldAttribute('parent_id', 'action', 'bwpm.edit.own');
		}
		else
		{
			// New record. Can only create in selected parent.
			$form->setFieldAttribute('parent_id', 'action', 'bwpm.create');
		}

		$user = JFactory::getUser();

		// Check for existing mailinglist.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (!$user->authorise('bwpm.edit.state', 'com_bwpostman.mailinglist.' . (int) $id))
			|| ($id == 0 && !$user->authorise('bwpm.mailinglist.edit.state', 'com_bwpostman')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('state', 'disabled', 'true');
			// Disable fields while saving.
			// The controller has already verified this is a mailinglist you can edit.
			$form->setFieldAttribute('state', 'filter', 'unset');
		}

		// Check to show campaign_id
		$campaign_id	= $jinput->get('campaign_id');
		if (empty($campaign_id))
		{
			$form->setFieldAttribute('campaign_id', 'type', 'hidden');
		}

		// Check to show created data
		$c_date	= $form->getValue('created_date');
		if ($c_date == '0000-00-00 00:00:00')
		{
			$form->setFieldAttribute('created_date', 'type', 'hidden');
			$form->setFieldAttribute('created_by', 'type', 'hidden');
		}

		// Check to show modified data
		$m_date	= $form->getValue('modified_time');
		if ($m_date == '0000-00-00 00:00:00')
		{
			$form->setFieldAttribute('modified_time', 'type', 'hidden');
			$form->setFieldAttribute('modified_by', 'type', 'hidden');
		}

		return $form;
	}

	/**
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
		$recordId = JFactory::getApplication()->getUserState('com_bwpostman.edit.mailinglist.id');

		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_bwpostman.edit.mailinglist.data', array());

		if (empty($data) || $recordId != $data->id)
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to (un)archive a mailinglist
	 * --> when unarchiving it is called by the archive-controller
	 *
	 * @access	public
	 *
	 * @param	array   $cid        Mailinglist IDs
	 * @param	int     $archive    Task --> 1 = archive, 0 = unarchive
	 *
	 * @return	boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function archive($cid = array(0), $archive = 1)
	{
		$_db	= $this->_db;
		$date	= JFactory::getDate();
		$uid	= JFactory::getUser()->get('id');

		if ($archive == 1)
		{
			$time = $date->toSql();

			// Access check.
			foreach ($cid as $id)
			{
				if (!BwPostmanHelper::canArchive('mailinglist', 0, (int) $id))
				{
					return false;
				}
			}
		}
		else
		{
			// Access check.
			foreach ($cid as $id)
			{
				if (!BwPostmanHelper::canRestore('mailinglist', (int) $id))
				{
					return false;
				}
			}

			$time	= '0000-00-00 00:00:00';
			$uid	= 0;
		}

		if (count($cid))
		{
			ArrayHelper::toInteger($cid);
			$query	= $_db->getQuery(true);

			$query->update($_db->quoteName('#__bwpostman_mailinglists'));
			$query->set($_db->quoteName('archive_flag') . " = " . $_db->quote((int) $archive));
			$query->set($_db->quoteName('archive_date') . " = " . $_db->quote($time, false));
			$query->set($_db->quoteName('archived_by') . " = " . (int) $uid);
			$query->where($_db->quoteName('id') . ' IN (' . implode(',', $cid) . ')');

			$_db->setQuery($query);
			try
			{
				$_db->execute();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}

		return true;
	}

	/**
	 * Method to remove one or more mailinglists
	 * --> is called by the archive-controller
	 *
	 * @access	public
	 *
	 * @param	array &$pks     Mailinglist IDs
	 *
	 * @return	boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function delete(&$pks)
	{
		$app	= JFactory::getApplication();

		if (count($pks))
		{
			ArrayHelper::toInteger($pks);
			// Access check.
			foreach ($pks as $id)
			{
				if (!BwPostmanHelper::canDelete('mailinglist', (int) $id))
				{
					return false;
				}
			}

			$lists_table	= JTable::getInstance('mailinglists', 'BwPostmanTable');

			// Delete all entries from the mailinglists-table
			foreach ($pks as $id)
			{
				if (!$lists_table->delete($id))
				{
					$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_ARC_ERROR_REMOVING_MLS_NO_ML_DELETED', $id), 'error');
					return false;
				}

				if (!$this->deleteMailinglistsCampaignsEntry($id))
				{
					$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_ARC_ERROR_REMOVING_MLS_NO_ML_CAM_DELETED', $id), 'error');
					return false;
				}

				if (!$this->deleteMailinglistSubscribers($id))
				{
					$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_ARC_ERROR_REMOVING_MLS_NO_SUBS_DELETED', $id), 'error');
					return false;
				}

				// Delete all entries from the newsletters_mailinglists-table
				if (!$this->deleteMailinglistNewsletters($id))
				{
					$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_ARC_ERROR_REMOVING_MLS_NO_NLS_DELETED', $id), 'error');
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Method to (un)publish a mailinglist
	 *
	 * @access	public
	 *
	 * @param	array   &$pks   Mailinglist IDs
	 * @param	int     $value  Task --> 1 = publish, 0 = unpublish
	 *
	 * @return	boolean
	 *
	 * @since       0.9.1
	 */
	public function publish(&$pks, $value = 1)
	{
		if (parent::publish($pks, $value))
		{
			return true;
		}

		return false;
	}

	/**
	 * @param $id
	 *
	 * @return bool
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function deleteMailinglistsCampaignsEntry($id)
	{
		$_db            = $this->getDbo();
		$query          = $_db->getQuery(true);

		$query->delete($_db->quoteName('#__bwpostman_campaigns_mailinglists'));
		$query->where($_db->quoteName('mailinglist_id') . ' =  ' . $_db->quote($id));

		$_db->setQuery($query);

		try
		{
			$_db->execute();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}

		return true;
	}

	/**
	 * @param $id
	 *
	 * @return bool
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function deleteMailinglistSubscribers($id)
	{
		$_db            = $this->getDbo();
		$query          = $_db->getQuery(true);

		$query->delete($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
		$query->where($_db->quoteName('mailinglist_id') . ' =  ' . $_db->quote($id));

		$_db->setQuery($query);

		try
		{
			$_db->execute();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}

		return true;
	}

	/**
	 * @param $id
	 *
	 * @return bool
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function deleteMailinglistNewsletters($id)
	{
		$_db            = $this->getDbo();
		$query          = $_db->getQuery(true);

		$query->delete($_db->quoteName('#__bwpostman_newsletters_mailinglists'));
		$query->where($_db->quoteName('mailinglist_id') . ' =  ' . $_db->quote($id));

		$_db->setQuery($query);

		try
		{
			$_db->execute();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}

		return true;
	}
}
