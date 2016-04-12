<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single mailinglist model for backend.
 *
 * @version 1.3.2 bwpm
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

// Import MODEL object class
jimport('joomla.application.component.modeladmin');

// Require helper class
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');

/**
 * BwPostman mailinglist model
 * Provides methodes to add and edit mailinglists
 *
 * @package		BwPostman-Admin
 * @subpackage	Mailinglists
 */
class BwPostmanModelMailinglist extends JModelAdmin
{
	/**
	 * Mailinglist ID
	 *
	 * @var int
	 */
	var $_id = null;

	/**
	 * Mailinglist data
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Constructor
	 * Determines the mailinglist ID
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$jinput	= JFactory::getApplication()->input;
		$array	= $jinput->get('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	string  $type	    The table type to instantiate
	 * @param	string	$prefix     A prefix for the table class name. Optional.
	 * @param	array	$config     Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
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
	 * @param	int $id     Mailinglist ID
	 */
	public function setId($id)
	{
		$this->_id	    = $id;
		$this->_data	= null;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to delete the record. Defaults to the permission set in the component.
	 * @since	1.0.1
	 */
	protected function canDelete($record)
	{
		$user = JFactory::getUser();

		// Check general delete permission first.
		if ($user->authorise('core.delete', 'com_bwpostman'))
		{
			return true;
		}

		if (!empty($record->id)) {
			// Check specific delete permission.
			if ($user->authorise('core.delete', 'com_bwpostman.mailinglists.' . (int) $record->id))
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to change the state of the record. Defaults to the permission set in the component.
	 * @since	1.0.1
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		// Check general edit state permission first.
		if ($user->authorise('core.edit.state', 'com_bwpostman'))
		{
			return true;
		}

		if (!empty($record->id)) {
			// Check specific edit state permission.
			if ($user->authorise('core.edit.state', 'com_bwpostman.mailinglists.' . (int) $record->id))
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
		$cid	= $app->getUserState('com_bwpostman.edit.mailinglist.id', 0);
		$data	= $app->getUserState('com_bwpostman.edit.mailinglist.data', null);

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
			(!empty($pk)) ? $pk	= $pk : $pk	= (int) $cid;
			$item	= parent::getItem($pk);
		}
		else {
			$item	= new stdClass();
			foreach ($data as $key => $value) $item->$key	= $value;
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
	 * @since	1.0.1
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_bwpostman.mailinglist', 'mailinglist', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
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
			$form->setFieldAttribute('parent_id', 'action', 'core.edit');
			// Existing record. Can only edit own mailinglists in selected parent.
			$form->setFieldAttribute('parent_id', 'action', 'core.edit.own');
		}
		else
		{
			// New record. Can only create in selected parent.
			$form->setFieldAttribute('parent_id', 'action', 'core.create');
		}

		$user = JFactory::getUser();

		// Check for existing mailinglist.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (!$user->authorise('core.edit.state', 'com_bwpostman.mailinglist.'.(int) $id))
		|| ($id == 0 && !$user->authorise('core.edit.state', 'com_bwpostman'))
		)
		{
			// Disable fields for display.
			$form->setFieldAttribute('state', 'disabled', 'true');
			// Disable fields while saving.
			// The controller has already verified this is a mailinglist you can edit.
			$form->setFieldAttribute('state', 'filter', 'unset');

		}
		// Check to show campaign_id
		$campaign_id	= $jinput->get('campaign_id');
		if (empty($campaign_id)) {
			$form->setFieldAttribute('campaign_id', 'type', 'hidden');
		}

		// Check to show created data
		$c_date	= $form->getValue('created_date');
		if ($c_date == '0000-00-00 00:00:00') {
			$form->setFieldAttribute('created_date', 'type', 'hidden');
			$form->setFieldAttribute('created_by', 'type', 'hidden');
		}

		// Check to show modified data
		$m_date	= $form->getValue('modified_time');
		if ($m_date == '0000-00-00 00:00:00') {
			$form->setFieldAttribute('modified_time', 'type', 'hidden');
			$form->setFieldAttribute('modified_by', 'type', 'hidden');
		}
		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.0.1
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_bwpostman.mailinglist.edit.data', array());

		if (empty($data)) {
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
	 */
	public function archive($cid = array(), $archive = 1)
	{
		$_db	= $this->_db;
		$app	= JFactory::getApplication();
		$date	= JFactory::getDate();
		$uid	= JFactory::getUser()->get('id');

		if ($archive == 1) {
			$time = $date->toSql();

			// Access check.
			foreach ($cid as $i) {
				if (!BwPostmanHelper::allowArchive($i, 0, 'mailinglist'))
				{
					$app->enqueueMessage(JText::_('COM_BWPOSTMAN_ML_ARCHIVE_RIGHTS_MISSING'), 'error');
					return false;
				}
			}
		}
		else {
			$time	= '0000-00-00 00:00:00';
			$uid	= 0;

			// Access check.
			foreach ($cid as $i) {
				if (!BwPostmanHelper::allowRestore($i, 0, 'mailinglist'))
				{
					$app->enqueueMessage(JText::_('COM_BWPOSTMAN_ML_RESTORE_RIGHTS_MISSING'), 'error');
					return false;
				}
			}
		}

		if (count($cid))
		{
			JArrayHelper::toInteger($cid);
			$query	= $_db->getQuery(true);

			$query->update($_db->quoteName('#__bwpostman_mailinglists'));
			$query->set($_db->quoteName('archive_flag') . " = " . $_db->Quote((int) $archive));
			$query->set($_db->quoteName('archive_date') . " = " . $_db->Quote($time, false));
			$query->set($_db->quoteName('archived_by') . " = " . (int) $uid);
			$query->where($_db->quoteName('id') . ' IN (' .implode(',', $cid) . ')');

			$_db->setQuery($query);

			if (!$_db->query()) {
				$this->setError($_db->getErrorMsg());
				return false;
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
	 */
	public function delete(&$pks)
	{
		$app	= JFactory::getApplication();

		// Access check.
		foreach ($pks as $i) {
			if (!BwPostmanHelper::allowDelete($i, 0, 'mailinglist'))
			{
				return false;
			}
		}

		if (count($pks))
		{
			JArrayHelper::toInteger($pks);
			$_db	= $this->getDbo();

			$lists_table	= JTable::getInstance('mailinglists', 'BwPostmanTable');

			// Delete all entries from the mailinglists-table
			foreach ($pks as $id) {
				if (!$lists_table->delete($id))
				{
					$app->enqueueMessage(JText::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_MLS_NO_ML_DELETED'), 'error');
					return false;
				}
			}

			// Delete all entries from the subscribers_mailinglists-table
			$query = $_db->getQuery(true);
			$query->delete();
			$query->from($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
			$query->where('mailinglist_id IN (' .implode(',', $pks) . ')');
			$_db->setQuery($query);

			if (!$_db->query())
			{
				$app->enqueueMessage(JText::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_MLS_NO_SUBS_DELETED'), 'warning');
			}

			// Delete all entries from the newsletters_mailinglists-table
			$query = $_db->getQuery(true);
			$query->delete();
			$query->from($_db->quoteName('#__bwpostman_newsletters_mailinglists'));
			$query->where('mailinglist_id IN (' .implode(',', $pks) . ')');
			$_db->setQuery($query);

			if (!$_db->query())
			{
				$app->enqueueMessage(JText::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_MLS_NO_MLS_DELETED'), 'warning');
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
	 */
	public function publish(&$pks, $value = 1)
	{
		if (parent::publish($pks, $value)) {
			return true;
		}
		return false;
	}
}
