<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single campaign model for backend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2016 Boldt Webservice <forum@boldt-webservice.de>
 * @support http://www.boldt-webservice.de/forum/bwpostman.html
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
defined ('_JEXEC') or die ('Restricted access');

// Import MODEL and Helper object class
jimport('joomla.application.component.modeladmin');

use Joomla\Utilities\ArrayHelper as ArrayHelper;

// Require helper class
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');
jimport('joomla.application.component.helper');

/**
 * BwPostman campaign model
 * Provides methodes to add and edit campaigns
 *
 * @package		BwPostman-Admin
 *
 * @subpackage	Campaigns
 */
class BwPostmanModelCampaign extends JModelAdmin
{
	/**
	 * Campaign ID
	 *
	 * @var int
	 */
	private $_id = null;

	/**
	 * Automailing?
	 *
	 * @var bool
	 */
	private $_am = FALSE;

	/**
	 * Campaign data
	 *
	 * @var array
	 */
	private $_data = null;

	/**
	 * Constructor
	 * Determines the campaign ID
	 *
	 */
	public function __construct()
	{
		$jinput	= JFactory::getApplication()->input;

		parent::__construct();

		$array = $jinput->get('cid',  0, '');
		$this->setId((int)$array[0]);
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
	 */
	public function setId($id)
	{
		$this->_id		= $id;
		$this->_data	= null;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since	1.0.1
	 */
	protected function canDelete($record)
	{
		$user = JFactory::getUser();

		// Check general delete permission first.
		if ($user->authorise('bwpm.delete', 'com_bwpostman'))
		{
			return true;
		}

		if (!empty($record->id))
		{
			// Check specific delete permission.
			if ($user->authorise('bwpm.campaign.delete', 'com_bwpostman.campaign.' . (int) $record->id))
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   1.0.1
	 */
	public function getItem($pk = null)
	{
		$app	= JFactory::getApplication();
		$cid	= $app->getUserState('com_bwpostman.edit.campaign.id', 0);
		$data	= $app->getUserState('com_bwpostman.edit.campaign.data', null);
		$_db	= $this->_db;

		if (!$data) {
			// Initialise variables.
			if (is_array($cid)) {
				if (!empty($cid)) {
					$cid = $cid[0];
				}
				else {
					$cid = 0;
				}
			}
			if (empty($pk)) $pk	= (int) $cid;
			$item	= parent::getItem($pk);

					//get associated mailinglists
			$query	= $_db->getQuery(true);
			$query->select($_db->quoteName('mailinglist_id'));
			$query->from($_db->quoteName('#__bwpostman_campaigns_mailinglists'));
			$query->where($_db->quoteName('campaign_id') . ' = ' . (int) $item->id);
			$_db->setQuery($query);
			try
			{
				$item->mailinglists= $_db->loadColumn();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			//extract associated usergroups
			$usergroups	= array();
			foreach ($item->mailinglists as $mailinglist)
			{
				if ((int) $mailinglist < 0) $usergroups[]	= -(int)$mailinglist;
			}
			$item->usergroups	= $usergroups;

			if ($pk == 0) $item->id	= 0;

			// get avaliable mailinglists to predefine for state
			$query	= $_db->getQuery(true);
			$query->select('id');
			$query->from($_db->quoteName('#__bwpostman_mailinglists'));
			$query->where($_db->quoteName('published') . ' = ' . (int) 1);
			$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);
			$query->where($_db->quoteName('access') . ' = ' . (int) 1);

			$_db->setQuery($query);

			$mls_avaliable  = array();

			try
			{
				$mls_avaliable	= $_db->loadColumn();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
			$res_avaliable	= array_intersect($item->mailinglists, $mls_avaliable);

			if (count($res_avaliable) > 0)
			{
				$item->ml_available	= $res_avaliable;
			}
			else
			{
				$item->ml_available	= array();
			}

			// get unavaliable mailinglists to predefine for state
			$query	= $_db->getQuery(true);
			$query->select('id');
			$query->from($_db->quoteName('#__bwpostman_mailinglists'));
			$query->where($_db->quoteName('published') . ' = ' . (int) 1);
			$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);
			$query->where($_db->quoteName('access') . ' > ' . (int) 1);

			$_db->setQuery($query);

			$mls_unavaliable	= $_db->loadColumn();
			$res_unavaliable	= array_intersect($item->mailinglists, $mls_unavaliable);

			if (count($res_unavaliable) > 0)
			{
				$item->ml_unavailable	= $res_unavaliable;
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
//			$query->where($_db->quoteName('access') . ' = ' . (int) 1);

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
			foreach ($data as $key => $value) $item->$key	= $value;
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
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// @todo XML-file will not be processed

		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_bwpostman.campaign.edit.data', array());

		if (empty($data)) $data = $this->getItem();

		return $data;
	}

	/**
	 * Method to get all newsletters which are assigned to the campaign
	 *
	 * @access 	public
	 * @return 	object Newsletters
	 */
	public function getNewsletters()
	{
		$_db			= $this->_db;
		$query			= $_db->getQuery (true);
		$newsletters	= new stdClass();
		$id				= $this->getState('campaign.id');

		$query->select($_db->quoteName('a') . '.*');
		$query->select($_db->quoteName('v') . '.' . $_db->quoteName('name') . ' AS author');
		$query->from($_db->quoteName('#__bwpostman_newsletters') . ' AS a');
		$query->leftJoin($_db->quoteName('#__users') . ' AS ' . $_db->quoteName('v') . ' ON ' . $_db->quoteName('v') . '.' . $_db->quoteName('id') . ' = ' . $_db->quoteName('a') . '.' . $_db->quoteName('created_by'));
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
		$query->leftJoin($_db->quoteName('#__users') . ' AS ' . $_db->quoteName('v') . ' ON ' . $_db->quoteName('v') .'.' .  $_db->quoteName('id') . ' = ' . $_db->quoteName('a') . '.' . $_db->quoteName('created_by'));
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
		$query->leftJoin($_db->quoteName('#__users') . ' AS ' . $_db->quoteName('v') . ' ON ' . $_db->quoteName('v') . '.' . $_db->quoteName('id') . ' = ' . $_db->quoteName('a') . '.' . $_db->quoteName('created_by'));
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
	 * @return	boolean
	 */
	public function archive($cid = array(), $archive = 1, $archive_nl = 1)
	{
		$app	= JFactory::getApplication();
		$date	= JFactory::getDate();
		$uid	= JFactory::getUser()->get('id');
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		if ($archive == 1)
		{
			$time = $date->toSql();

			// Access check.
			foreach ($cid as $i)
			{
				if (!BwPostmanHelper::allowArchive($i, 0, 'campaign'))
				{
					$app->enqueueMessage(JText::_('COM_BWPOSTMAN_CAM_ARCHIVE_RIGHTS_MISSING'), 'error');
					return false;
				}
			}
		}
		else
		{
			$time	= '0000-00-00 00:00:00';
			$uid	= 0;

			// Access check.
			foreach ($cid as $i)
			{
				if (!BwPostmanHelper::allowRestore($i, 0, 'campaign'))
				{
					$app->enqueueMessage(JText::_('COM_BWPOSTMAN_CAM_RESTORE_RIGHTS_MISSING'), 'error');
					return false;
				}
			}
		}

		if (count($cid))
		{
			ArrayHelper::toInteger($cid);

			$query->update($_db->quoteName('#__bwpostman_campaigns'));
			$query->set($_db->quoteName('archive_flag') . ' = ' . (int) $archive);
			$query->set($_db->quoteName('archive_date') . ' = ' . $_db->quote($time, false));
			$query->where($_db->quoteName('id') . ' IN (' .implode(',', $cid) . ')');

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
				$query->where($_db->quoteName('campaign_id') . ' IN (' .implode(',', $cid) . ')');

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
	 * @return	boolean	True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since	1.0.1
	 */
	public function save($data)
	{
		// merge ml-arrays, single array may not exist, therefore array_merge would not give a result
		if (isset($data['ml_available']))
			foreach ($data['ml_available'] as $key => $value)
				$data['mailinglists'][] 	= $value;
		if (isset($data['ml_unavailable']))
			foreach ($data['ml_unavailable'] as $key => $value)
				$data['mailinglists'][] 	= $value;
		if (isset($data['ml_intern']))
			foreach ($data['ml_intern'] as $key => $value)
				$data['mailinglists'][] 	= $value;

		// merge usergroups into mailinglists, single array may not exist, therefore array_merge would not give a result
		if (isset($data['usergroups']) && !empty($data['usergroups']))
			foreach ($data['usergroups'] as $key => $value)
				$data['mailinglists'][] = '-' . $value;

		if (isset($data['mailinglists']))
		{
			$res	= parent::save($data);

			if ($res)
			{
				$jinput		= JFactory::getApplication()->input;
				$_db		= $this->_db;
				$query		= $_db->getQuery(true);

				// Delete all entrys of the newsletter from newsletters_mailinglists table
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
					$query->columns(array(
							$_db->quoteName('campaign_id'),
							$_db->quoteName('mailinglist_id')
					));
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
	 */
	public function delete(&$pks)
	{
		$jinput	= JFactory::getApplication()->input;
		$remove_nl	= $jinput->get('remove_nl', false);

		// Access check.
		foreach ($pks as $i)
		{
			if (!BwPostmanHelper::allowDelete($i, 0, 'campaign'))
			{
				return false;
			}
		}

		if (count($pks))
		{
			ArrayHelper::toInteger($pks);

			// Delete campaigns from campaigns-table
			$cams_table = JTable::getInstance('campaigns', 'BwPostmanTable');

			foreach ($pks as $id)
			{
				if (!$cams_table->delete($id))
				{
					return false;
				}
			}

			// Remove_nl = 1 if the user want to delete the assigned newsletters
			$nl_ids = array();
			if ($remove_nl)
			{
				// Delete newsletter from newsletters-table
				$nl_table	= JTable::getInstance('newsletters', 'BwPostmanTable');
				$_db		= $this->getDbo();
				$query		= $_db->getQuery(true);

				$query->select($_db->quoteName('id'));
				$query->from($_db->quoteName('#__bwpostman_newsletters'));
				$query->where($_db->quoteName('campaign_id') . ' IN (' .implode(',', $pks) . ')');
				$_db->setQuery($query);
				try
				{
					$nl_ids = $_db->loadColumn();
				}
				catch (RuntimeException $e)
				{
					JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				}

				foreach ($nl_ids as $id)
				{
					if (!$nl_table->delete($id))
					{
						return false;
					}
				}
			}
		}
		return true;
	}
}
