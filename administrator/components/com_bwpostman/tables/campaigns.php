<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman campaigns table for backend.
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

/**
 * #__bwpostman_campaigns table handler
 * Table for storing the campaign data
 *
 * @package		BwPostman-Admin
 * @subpackage	Campaigns
 */
class BwPostmanTableCampaigns extends JTable
{
	/** @var int Primary Key */
	var $id = null;

	/** @var int asset_id */
	var $asset_id = null;

	/** @var string Campaign title */
	var $title = null;

	/** @var string Campaign description */
	var $description = null;

	/** @var int Accesslevel/Viewlevel --> 1 = Public, 2 = Registered, 3 = Special, >3 = user defined viewlevels */
	var $access = 0;

	/** @var datetime creation date of the campaign */
	var $created_date = '0000-00-00 00:00:00';

	/** @var int user ID */
	var $created_by = 0;

	/** @var datetime last modification date of the campaign */
	var $modified_time = '0000-00-00 00:00:00';

	/** @var int user ID */
	var $modified_by = 0;

	/** @var int Checked-out owner */
	var $checked_out = 0;

	/** @var datetime Checked-out time */
	var $checked_out_time = '0000-00-00 00:00:00';

	/** @var int Archive-flag --> 0 = not archived, 1 = archived */
	var $archive_flag = 0;

	/** @var datetime Archive-date */
	var $archive_date = '0000-00-00 00:00:00';

	/** @var int ID --> 0 = campaign is not archived, another ID = account is archived by an administrator */
	var $archived_by = 0;

	/**
	 * Constructor
	 *
	 * @param 	JDatabaseDriver  $db Database object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__bwpostman_campaigns', 'id', $db);
	}

	/**
	 * Alias function
	 *
	 * @return  string
	 *
	 * @since   1.0.1
	 */
	public function getAssetName()
	{
		return self::_getAssetName();
	}

	/**
	 * Alias function
	 *
	 * @return  string
	 *
	 * @since   1.0.1
	 */
	public function getAssetTitle()
	{
		return self::_getAssetTitle();
	}

	/**
	 * Alias function
	 *
	 * @return  integer
	 *
	 * @since   1.0.1
	 */
	public function getAssetParentId()
	{
		return self::_getAssetParentId();
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form component.table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_bwpostman.campaign.' . (int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}

	/**
	 * Method to get the parent asset id for the record
	 *
	 * @param   JTable   $table  A JTable object (optional) for the asset parent
	 * @param   integer  $id     The id (optional) of the content.
	 *
	 * @return  integer
	 *
	 * @since   11.1
	 */
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		// Initialise variables.
		$assetId = null;

		// Build the query to get the asset id for the table.
		$query = $this->_db->getQuery(true);
		$query->select($this->_db->quoteName('id'));
		$query->from($this->_db->quoteName('#__assets'));
		$query->where($this->_db->quoteName('name') . " = 'com_bwpostman.campaign'");

		// Get the asset id from the database.
		$this->_db->setQuery($query);
		if ($result = $this->_db->loadResult())
		{
			$assetId = (int) $result;
		}

		// Return the asset id.
		if ($assetId)
		{
			return $assetId;
		}
		else
		{
			return parent::_getAssetParentId($table, $id);
		}
	}

	/**
	 * Overloaded bind function
	 *
	 * @access public
	 *
	 * @param array|object  $data       Named array or object
	 * @param string        $ignore     Space separated list of fields not to bind
	 * @return boolean
	 */
	public function bind($data, $ignore='')
	{
		// Bind the rules.
		if (is_object($data)) {
			if (property_exists($data, 'rules') && is_array($data->rules))
			{
				$rules = new JAccessRules($data->rules);
				$this->setRules($rules);
			}
		}
		elseif (is_array($data)) {
			if (array_key_exists('rules', $data) && is_array($data['rules']))
			{
				$rules = new JAccessRules($data['rules']);
				$this->setRules($rules);
			}
		}
		else {
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_BIND_FAILED_INVALID_SOURCE_ARGUMENT', get_class($this)));
			$this->setError($e);
			return false;
		}

		// Cast properties
		$this->id	= (int) $this->id;

		return parent::bind($data, $ignore);
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 *
	 * @return boolean True
	 */
	public function check()
{
		$app	= JFactory::getApplication();
		$query	= $this->_db->getQuery(true);
		$fault	= false;

		// Remove all HTML tags from the title and description
		$filter				= new JFilterInput(array(), array(), 0, 0);
		$this->title		= $filter->clean($this->title);
		$this->description	= $filter->clean($this->description);

		// Check for valid title
		if (trim($this->title) == '') {
			$app->enqueueMessage(JText::_('COM_BWPOSTMAN_CAM_ERROR_TITLE'), 'error');
			$fault	= true;
		}

		// Check for existing title
		$query->select($this->_db->quoteName('id'));
		$query->from($this->_db->quoteName('#__bwpostman_campaigns'));
		$query->where($this->_db->quoteName('title') . ' = ' . $this->_db->Quote($this->title));

		$this->_db->setQuery($query);

		$xid = intval($this->_db->loadResult());

		if ($xid && $xid != intval($this->id)) {
			$app->enqueueMessage((JText::sprintf('COM_BWPOSTMAN_CAM_ERROR_TITLE_DOUBLE', $this->title, $xid)), 'error');
			$fault	= true;
		}

		if ($fault) {
			$app->setUserState('com_bwpostman.edit.campaign.data', $this);
			return false;
		}
		return true;
	}

	/**
	 * Overridden JTable::store to set created/modified and user id.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.0.1
	 */
	public function store($updateNulls = false)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		if ($this->id)
		{
			// Existing mailing list
			$this->modified_time = $date->toSql();
			$this->modified_by = $user->get('id');
		}
		else
		{
			// New mailing list
			$this->created_date = $date->toSql();
			$this->created_by = $user->get('id');
		}
		$res	= parent::store($updateNulls);
		JFactory::getApplication()->setUserState('com_bwpostman.edit.campaign.id', $this->id);

		return $res;
	}
}
