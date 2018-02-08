<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single campaign model for backend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2017 Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/bwpostman.html
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
jimport('joomla.application.component.helper');

/**
 * BwPostman campaign model
 * Provides methods to add and edit campaigns
 *
 * @package		BwPostman-Admin
 *
 * @subpackage	Campaigns
 *
 * @since       0.9.1
 */
class BwPostmanModelCampaign extends JModelAdmin
{
	/**
	 * Campaign ID
	 *
	 * @var integer
	 *
	 * @since       0.9.1
	 */
	private $id = null;

	/**
	 * Campaign data
	 *
	 * @var array
	 *
	 * @since       0.9.1
	 */
	private $data = null;

	/**
	 * Constructor
	 * Determines the campaign ID
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function __construct()
	{
		$jinput	= JFactory::getApplication()->input;

		parent::__construct();

		$array = $jinput->get('cid',  0, '');
		$this->setId((int) $array[0]);
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	string  $type   	The table type to instantiate
	 * @param	string	$prefix     A prefix for the table class name. Optional.
	 * @param	array	$config     Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	 *
	 * @since  1.0.1
	 */
	public function getTable($type = 'Campaigns', $prefix = 'BwPostmanTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to reset the campaign ID and campaign data
	 *
	 * @access	public
	 *
	 * @param	int $id     Campaign ID
	 *
	 * @since       0.9.1
	 */
	public function setId($id)
	{
		$this->id   = $id;
		$this->data = null;
	}

	/**
	 * Method to test whether the state of a record can be changed
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to change state of the record.
	 *
	 * @since	1.0.1
	 */
	protected function canEditState($record)
	{
		$permission = BwPostmanHelper::canEditState('campaign', $record->id);

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
		$cid	= $app->getUserState('com_bwpostman.edit.campaign.id', 0);
		$data	= $app->getUserState('com_bwpostman.edit.campaign.data', null);
		$task   = $app->input->getCmd('task', '');
		$_db	= $this->_db;
		$id     = 0;

		if (is_object($data) && property_exists($data, 'id'))
		{
			$id = $data->id;
		}
		elseif (is_array($data) && key_exists('id', $data))
		{
			$id = $data['id'];
		}

		if (!$data || ($id != $pk)) {
			// Initialise variables.
			if (is_array($cid)) {
				if (!empty($cid)) {
					$cid = (int) $cid[0];
				}
				else {
					$cid = 0;
				}
			}

			if (empty($pk)) $pk	= $cid;

			$item	= parent::getItem($pk);

			//get associated mailinglists
			$query	= $_db->getQuery(true);
			$query->select($_db->quoteName('mailinglist_id'));
			$query->from($_db->quoteName('#__bwpostman_campaigns_mailinglists'));
			$query->where($_db->quoteName('campaign_id') . ' = ' . (int) $item->id);
			$_db->setQuery($query);
			try
			{
				$item->mailinglists = $_db->loadColumn();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			//extract associated usergroups
			$usergroups	= array();
			foreach ($item->mailinglists as $mailinglist)
			{
				if ((int) $mailinglist < 0)
				{
					$usergroups[]	= -(int) $mailinglist;
				}
			}

			$item->usergroups	= $usergroups;

			if ($pk == 0)
			{
				$item->id	= 0;
			}

			// get available mailinglists to predefine for state
			$query	= $_db->getQuery(true);
			$query->select('id');
			$query->from($_db->quoteName('#__bwpostman_mailinglists'));
			$query->where($_db->quoteName('published') . ' = ' . (int) 1);
			$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);
			$query->where($_db->quoteName('access') . ' = ' . (int) 1);

			$_db->setQuery($query);

			$mls_available  = array();

			try
			{
				$mls_available	= $_db->loadColumn();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			$res_available	= array_intersect($item->mailinglists, $mls_available);

			if (count($res_available) > 0)
			{
				$item->ml_available	= $res_available;
			}
			else
			{
				$item->ml_available	= array();
			}

			// get unavailable mailinglists to predefine for state
			$query	= $_db->getQuery(true);
			$query->select('id');
			$query->from($_db->quoteName('#__bwpostman_mailinglists'));
			$query->where($_db->quoteName('published') . ' = ' . (int) 1);
			$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);
			$query->where($_db->quoteName('access') . ' > ' . (int) 1);

			$_db->setQuery($query);

			$mls_unavailable	= $_db->loadColumn();
			$res_unavailable	= array_intersect($item->mailinglists, $mls_unavailable);

			if (count($res_unavailable) > 0)
			{
				$item->ml_unavailable	= $res_unavailable;
			}
			else
			{
				$item->ml_unavailable	= array();
			}

			// get internal mailinglists to predefine for state
			$query	= $_db->getQuery(true);
			$query->select('id');
			$query->from($_db->quoteName('#__bwpostman_mailinglists'));
			$query->where($_db->quoteName('published') . ' = ' . (int) 0);
			$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);

			$_db->setQuery($query);

			$mls_intern = array();
			try
			{
				$mls_intern		= $_db->loadColumn();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			$res_intern		= array_intersect($item->mailinglists, $mls_intern);

			if (count($res_intern) > 0)
			{
				$item->ml_intern	= $res_intern;
			}
			else
			{
				$item->ml_intern	= array();
			}
		}
		else
		{
			$item	= new stdClass();
			foreach ($data as $key => $value)
			{
				$item->$key	= $value;
			}
		}

		$app->setUserState('com_bwpostman.edit.campaign.data', $item);
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
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_bwpostman.campaign', 'Campaign', array('control' => 'jform', 'load_data' => $loadData));

		// @todo XML-file will not be processed

		if (empty($form))
		{
			return false;
		}

		// Check to show created data
		$c_date	= $form->getValue('created_date');
		if ($c_date == '0000-00-00 00:00:00' || $c_date == null)
		{
			$form->setFieldAttribute('created_date', 'type', 'hidden');
			$form->setFieldAttribute('created_by', 'type', 'hidden');
		}

		// Check to show modified data
		$m_date	= $form->getValue('modified_time');
		if ($m_date == '0000-00-00 00:00:00' || $m_date == null)
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
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// @todo XML-file will not be processed

		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_bwpostman.campaign.edit.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get all newsletters which are assigned to the campaign
	 *
	 * @access 	public
	 *
	 * @return 	object Newsletters
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function getNewsletters()
	{
		$_db			= $this->_db;
		$query			= $_db->getQuery(true);
		$newsletters	= new stdClass();
		$id				= $this->getState('campaign.id');

		$query->select($_db->quoteName('a') . '.*');
		$query->select($_db->quoteName('v') . '.' . $_db->quoteName('name') . ' AS author');
		$query->from($_db->quoteName('#__bwpostman_newsletters') . ' AS a');
		$query->leftJoin(
			$_db->quoteName('#__users') . ' AS ' . $_db->quoteName('v')
			. ' ON ' . $_db->quoteName('v') . '.' . $_db->quoteName('id') . ' = ' . $_db->quoteName('a') . '.' . $_db->quoteName('created_by')
		);
		$query->where($_db->quoteName('campaign_id') . ' = ' . $_db->quote((int) $id));
		$query->where($_db->quoteName('mailing_date') . ' != ' . $_db->quote('0000-00-00 00:00:00'));
		$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);

		$_db->setQuery($query);

		try
		{
			$newsletters->sent = $_db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		$query = $_db->getQuery(true);
		$query->select($_db->quoteName('a') . '.*');
		$query->select($_db->quoteName('v') . '.' . $_db->quoteName('name') . ' AS author');
		$query->from($_db->quoteName('#__bwpostman_newsletters') . ' AS a');
		$query->leftJoin(
			$_db->quoteName('#__users') . ' AS ' . $_db->quoteName('v')
			. ' ON ' . $_db->quoteName('v') . '.' . $_db->quoteName('id') . ' = ' . $_db->quoteName('a') . '.' . $_db->quoteName('created_by')
		);
		$query->where($_db->quoteName('campaign_id') . ' = ' . $_db->quote((int) $id));
		$query->where($_db->quoteName('mailing_date') . ' = ' . $_db->quote('0000-00-00 00:00:00'));
		$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);

		$_db->setQuery($query);

		try
		{
			$newsletters->unsent = $_db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		$query = $_db->getQuery(true);
		$query->select($_db->quoteName('a') . '.' . $_db->quoteName('id') . ' AS nl_id');
		$query->select($_db->quoteName('a') . '.' . $_db->quoteName('subject'));
		$query->from($_db->quoteName('#__bwpostman_newsletters') . ' AS a');
		$query->leftJoin(
			$_db->quoteName('#__users') . ' AS ' . $_db->quoteName('v')
			. ' ON ' . $_db->quoteName('v') . '.' . $_db->quoteName('id') . ' = ' . $_db->quoteName('a') . '.' . $_db->quoteName('created_by')
		);
		$query->where($_db->quoteName('campaign_id') . ' = ' . (int) $id);
		$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);
		$query->order($_db->quoteName('a.subject') . ' ASC');

		$_db->setQuery($query);

		try
		{
			$newsletters->all = $_db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $newsletters;
	}

	/**
	 * Method to (un)archive a campaign and if the user want also the assigned newsletters
	 * --> when unarchiving it is called by the archive-controller
	 *
	 * @access	public
	 *
	 * @param	array   $cid        Campaign IDs
	 * @param	int     $archive    Task --> 1 = archive, 0 = unarchive
	 * @param	int     $archive_nl Archive/Unarchive assigned newsletters (0 = No, 1 = Yes)
	 *
	 * @throws Exception
	 *
	 * @return	boolean
	 *
	 * @since
	 */
	public function archive($cid = array(0), $archive = 1, $archive_nl = 1)
	{
		$date	= JFactory::getDate();
		$uid	= JFactory::getUser()->get('id');
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		ArrayHelper::toInteger($cid);

		if ($archive == 1)
		{
			$time = $date->toSql();

			// Access check.
			if (!BwPostmanHelper::canArchive('campaign', $cid))
			{
				return false;
			}
		}
		else
		{
			// Access check.
			if (!BwPostmanHelper::canRestore('campaign', $cid))
			{
				return false;
			}

			$time	= '0000-00-00 00:00:00';
			$uid	= 0;
		}

		if (count($cid))
		{
			$query->update($_db->quoteName('#__bwpostman_campaigns'));
			$query->set($_db->quoteName('archive_flag') . ' = ' . (int) $archive);
			$query->set($_db->quoteName('archive_date') . ' = ' . $_db->quote($time, false));
			$query->where($_db->quoteName('id') . ' IN (' . implode(',', $cid) . ')');

			$_db->setQuery($query);

			try
			{
				$_db->execute();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_CAM_RESTORE_RIGHTS_MISSING'), 'error');
			}

			// Archive_nl = 1 if the user want to (un)archive the assigned newsletters
			if ($archive_nl)
			{
				$query->clear();
				$query->update($_db->quoteName('#__bwpostman_newsletters'));
				$query->set($_db->quoteName('archive_flag') . ' = ' . (int) $archive);
				$query->set($_db->quoteName('archive_date') . ' = ' . $_db->quote($time, false));
				$query->set($_db->quoteName('archived_by') . " = " . (int) $uid);
				$query->where($_db->quoteName('campaign_id') . ' IN (' . implode(',', $cid) . ')');

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
		}

		return true;
	}

	/**
	 * Method to override save function
	 *
	 * @param	array	$data	A campaign object.
	 *
	 * @return	boolean	True if allowed to save the record. Defaults to the permission set in the component.
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	public function save($data)
	{
		// merge ml-arrays, single array may not exist, therefore array_merge would not give a result
		if (isset($data['ml_available']))
		{
			foreach ($data['ml_available'] as $key => $value)
			{
				$data['mailinglists'][] 	= $value;
			}
		}

		if (isset($data['ml_unavailable']))
		{
			foreach ($data['ml_unavailable'] as $key => $value)
			{
				$data['mailinglists'][] 	= $value;
			}
		}

		if (isset($data['ml_intern']))
		{
			foreach ($data['ml_intern'] as $key => $value)
			{
				$data['mailinglists'][] 	= $value;
			}
		}

		// merge usergroups into mailinglists, single array may not exist, therefore array_merge would not give a result
		if (isset($data['usergroups']) && !empty($data['usergroups']))
		{
			foreach ($data['usergroups'] as $key => $value)
			{
				$data['mailinglists'][] = '-' . $value;
			}
		}

		if (isset($data['mailinglists']))
		{
			$res	= parent::save($data);

			if ($res)
			{
				$jinput		= JFactory::getApplication()->input;
				$_db		= $this->_db;
				$query		= $_db->getQuery(true);

				// Delete all entries of the newsletter from newsletters_mailinglists table
				if ($data['id'])
				{
					$query->delete($_db->quoteName('#__bwpostman_campaigns_mailinglists'));
					$query->where($_db->quoteName('campaign_id') . ' =  ' . (int) $data['id']);

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
				else
				{
					//get id of new inserted data to write cross table newsletters-mailinglists and inject into form
					$data['id']	= $this->getState('campaign.id');
					$jinput->set('id', $data['id']);

					// update state
					$state_data	= JFactory::getApplication()->getUserState('com_bwpostman.edit.campaign.data');
					$state_data->id	= $data['id'];
					JFactory::getApplication()->setUserState('com_bwpostman.edit.campaign.data', $state_data);

				}

				// Store the selected BwPostman mailinglists into campaigns_mailinglists-table
				foreach ($data['mailinglists'] AS $mailinglists_value)
				{
					$query	= $_db->getQuery(true);

					$query->insert($_db->quoteName('#__bwpostman_campaigns_mailinglists'));
					$query->columns(
						array(
							$_db->quoteName('campaign_id'),
							$_db->quoteName('mailinglist_id')
						)
					);
					$query->values(
						(int) $data['id'] . ',' .
						(int) $mailinglists_value
					);
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

				$dispatcher = JEventDispatcher::getInstance();

				JPluginHelper::importPlugin('bwpostman');

				$dispatcher->trigger('onBwPostmanCampaignSave', array ($data));
			}
		}
		else
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_CAM_ERROR_NO_RECIPIENTS_SELECTED'), 'error');
			$res	= false;
		}

		return $res;
	}

	/**
	 * Method to remove one or more campaigns and if the user want also the assigned newsletters
	 * --> is called by the archive-controller
	 *
	 * @access	public
	 *
	 * @param	array &$pks     Campaign IDs
	 *
	 * @return	boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function delete(&$pks)
	{
		$jinput	    = JFactory::getApplication()->input;
		$remove_nl	= $jinput->get('remove_nl', false);
		$app	    = JFactory::getApplication();

		if (count($pks))
		{
			ArrayHelper::toInteger($pks);

			// Access check.
			if (!BwPostmanHelper::canDelete('campaign', $pks))
			{
				return false;
			}

			// Delete campaigns from campaigns table
			$cams_table = JTable::getInstance('campaigns', 'BwPostmanTable');

			foreach ($pks as $id)
			{
				if (!$cams_table->delete($id))
				{
					$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_ARC_ERROR_REMOVING_CAMS_NO_CAM_DELETED', $id), 'error');
					return false;
				}

				// Remove campaigns mailinglists entries
				if (!$this->deleteCampaignsMailinglistsEntry($id))
				{
					$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_ARC_ERROR_REMOVING_CAMS_NO_ML_DELETED', $id), 'error');
					return false;
				}

				// Remove_nl = 1 if the user want to delete the assigned newsletters
				if ($remove_nl)
				{
					if (!$this->deleteCampaignsNewsletters($id))
					{
						$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_ARC_ERROR_REMOVING_MLS_NO_NLS_DELETED', $id), 'error');
						return false;
					}
				}
			}
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
	private function deleteCampaignsMailinglistsEntry($id)
	{
		$_db            = $this->getDbo();
		$query          = $_db->getQuery(true);

		$query->delete($_db->quoteName('#__bwpostman_campaigns_mailinglists'));
		$query->where($_db->quoteName('campaign_id') . ' =  ' . $_db->quote($id));

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
	private function deleteCampaignsNewsletters($id)
	{
		$_db            = $this->getDbo();
		$query          = $_db->getQuery(true);

		$query->delete($_db->quoteName('#__bwpostman_newsletters'));
		$query->where($_db->quoteName('campaign_id') . ' =  ' . $_db->quote($id));

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
