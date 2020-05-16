<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman campaigns table for backend.
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

use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Filter\InputFilter;

/**
 * #__bwpostman_campaigns table handler
 * Table for storing the campaign data
 *
 * @package		BwPostman-Admin
 *
 * @subpackage	Campaigns
 *
 * @since       0.9.1
 */
class BwPostmanTableCampaigns extends JTable
{
	/**
	 * @var int Primary Key
	 *
	 * @since       0.9.1
	 */
	public $id = null;

	/**
	 * @var int asset_id
	 *
	 * @since       1.0.1
	 */
	public $asset_id = null;

	/**
	 * @var string Campaign title
	 *
	 * @since       0.9.1
	 */
	public $title = null;

	/**
	 * @var string Campaign description
	 *
	 * @since       0.9.1
	 */
	public $description = null;

	/**
	 * @var int Access level/View level --> 1 = Public, 2 = Registered, 3 = Special, >3 = user defined viewlevels
	 *
	 * @since       0.9.1
	 */
	public $access = 1;

	/**
	 * @var datetime creation date of the campaign
	 *
	 * @since       0.9.1
	 */
	public $created_date = '0000-00-00 00:00:00';

	/**
	 * @var int user ID
	 *
	 * @since       0.9.1
	 */
	public $created_by = 0;

	/**
	 * @var datetime last modification date of the campaign
	 *
	 * @since       0.9.1
	 */
	public $modified_time = '0000-00-00 00:00:00';

	/**
	 * @var int user ID
	 *
	 * @since       0.9.1
	 */
	public $modified_by = 0;

	/**
	 * @var int Checked-out owner
	 *
	 * @since       0.9.1
	 */
	public $checked_out = 0;

	/**
	 * @var datetime Checked-out time
	 *
	 * @since       0.9.1
	 */
	public $checked_out_time = '0000-00-00 00:00:00';

	/**
	 * @var int Archive-flag --> 0 = not archived, 1 = archived
	 *
	 * @since       0.9.1
	 */
	public $archive_flag = 0;

	/**
	 * @var datetime Archive-date
	 *
	 * @since       0.9.1
	 */
	public $archive_date = '0000-00-00 00:00:00';

	/**
	 * @var int ID --> 0 = campaign is not archived, another ID = account is archived by an administrator
	 *
	 * @since       0.9.1
	 */
	public $archived_by = 0;

	/**
	 * Constructor
	 *
	 * @param 	DatabaseDriver  $db Database object
	 *
	 * @since       0.9.1
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
	 * @throws Exception
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
	 * @since   1.0.1
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
	 * @since   1.0.1
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}

	/**
	 * Method to get the parent asset id for the record
	 *
	 * @param   Table   $table  A Table object (optional) for the asset parent
	 * @param   integer  $id     The id (optional) of the content.
	 *
	 * @return  integer
	 *
	 * @since   11.1
	 */
	protected function _getAssetParentId(Table $table = null, $id = null)
	{
		$asset = Table::getInstance('Asset');
		$asset->loadByName('com_bwpostman.campaign');
		return $asset->id;
	}

	/**
	 * Overloaded bind function
	 *
	 * @access public
	 *
	 * @param array|object  $data       Named array or object
	 * @param string        $ignore     Space separated list of fields not to bind
	 *
	 * @throws BwException
	 *
	 * @return boolean
	 *
	 * @since       0.9.1
	 */
	public function bind($data, $ignore='')
	{
		// Bind the rules.
		if (is_object($data))
		{
			if (property_exists($data, 'rules') && is_array($data->rules))
			{
				$rules = new JAccessRules($data->rules);
				$this->setRules($rules);
			}
		}
		elseif (is_array($data))
		{
			if (array_key_exists('rules', $data) && is_array($data['rules']))
			{
				$rules = new JAccessRules($data['rules']);
				$this->setRules($rules);
			}
		}
		else
		{
			throw new BwException(Text::sprintf('JLIB_DATABASE_ERROR_BIND_FAILED_INVALID_SOURCE_ARGUMENT', get_class($this)));
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
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function check()
	{
		$app	= Factory::getApplication();
		$query	= $this->_db->getQuery(true);
		$fault	= false;
		$xid    = 0;

		// Remove all HTML tags from the title and description
		$filter				= new InputFilter(array(), array(), 0, 0);
		$this->title		= $filter->clean($this->title);
		$this->description	= $filter->clean($this->description);

		// Check for valid title
		if (trim($this->title) == '')
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_CAM_ERROR_TITLE'), 'error');
			$fault	= true;
		}

		// Check for existing title
		$query->select($this->_db->quoteName('id'));
		$query->from($this->_db->quoteName('#__bwpostman_campaigns'));
		$query->where($this->_db->quoteName('title') . ' = ' . $this->_db->quote($this->title));

		$this->_db->setQuery($query);

		try
		{
			$xid = intval($this->_db->loadResult());
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		if ($xid && $xid != intval($this->id))
		{
			$app->enqueueMessage((Text::sprintf('COM_BWPOSTMAN_CAM_ERROR_TITLE_DOUBLE', $this->title, $xid)), 'error');
			$fault	= true;
		}

		if ($fault)
		{
			$app->setUserState('com_bwpostman.edit.campaign.data', $this);
			return false;
		}

		return true;
	}

	/**
	 * Overridden Table::store to set created/modified and user id.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws Exception
	 *
	 * @since   1.0.1
	 */
	public function store($updateNulls = false)
	{
		$date = Factory::getDate();
		$user = Factory::getUser();

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
		Factory::getApplication()->setUserState('com_bwpostman.edit.campaign.id', $this->id);

		return $res;
	}

	/**
	 * Returns the identity (primary key) value of this record
	 *
	 * @return  mixed
	 *
	 * @since  2.4.0
	 */
	public function getId()
	{
		$key = $this->getKeyName();

		return $this->$key;
	}

	/**
	 * Check if the record has a property (applying a column alias if it exists)
	 *
	 * @param string $key key to be checked
	 *
	 * @return  boolean
	 *
	 * @since   2.4.0
	 */
	public function hasField($key)
	{
		$key = $this->getColumnAlias($key);

		return property_exists($this, $key);
	}
}
