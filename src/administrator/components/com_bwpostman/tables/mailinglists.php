<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman mailinglists table for backend.
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

use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Filter\InputFilter;
use Joomla\CMS\Access\Access;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * #__bwpostman_mailinglists table handler
 * Table for storing the mailinglist data
 *
 * @package		BwPostman-Admin
 *
 * @subpackage	Mailinglists
 *
 * @since       0.9.1
 */
class BwPostmanTableMailinglists extends JTable
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
	 * @var string Mailinglist title
	 *
	 * @since       0.9.1
	 */
	public $title = null;

	/**
	 * @var string Mailinglist description
	 *
	 * @since       0.9.1
	 */
	public $description = null;

	/**
	 * @var int campaign ID
	 *
	 * @since       0.9.1
	 */
	public $campaign_id = 0;

	/**
	 * @var int access level/view level --> 1 = Public, 2 = Registered, 3 = Special, >3 = user defined viewlevels
	 *
	 * @since       0.9.1
	 */
	public $access = 1;

	/**
	 * @var int Published
	 *
	 * @since       0.9.1
	 */
	public $published = 0;

	/**
	 * @var datetime creation date of the mailinglist
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
	 * @var datetime last modification date of the mailinglist
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
	public $checked_out_time = 0;

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
	public $archive_date = null;

	/**
	 * @var int ID --> 0 = mailinglist is not archived, another ID = account is archived by an administrator
	 *
	 * @since       0.9.1
	 */
	public $archived_by = 0;

	/**
	 * Constructor
	 *
	 * @param 	JDatabaseDriver  $db Database object
	 *
	 * @since       0.9.1
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__bwpostman_mailinglists', 'id', $db);
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
	 * @return  string
	 *
	 * @since   1.0.1
	 */
	public function getAssetParentId()
	{
		return self::_getAssetParentId();
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_bwpostman.mailinglist.' . (int) $this->$k;
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
		$asset->loadByName('com_bwpostman.mailinglist');
		return (int)$asset->id;
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
		$db	= $this->_db;
		$query	= $db->getQuery(true);
		$fault	= false;
		$xid    = 0;

		// Remove all HTML tags from the title and description
		$filter				= new InputFilter(array(), array(), 0, 0);
		$this->title		= trim($filter->clean($this->title));
		$this->description	= $filter->clean($this->description);

		// Check for valid title
		if ($this->title === '')
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_ML_ERROR_TITLE'), 'error');
			$fault	= true;
		}

		// Check for valid title
		if (trim($this->description) == '')
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_ML_ERROR_DESCRIPTION'), 'error');
			$fault	= true;
		}

		// Check for existing title
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('title') . ' = ' . $db->quote($this->title));

		$db->setQuery($query);

		try
		{
			$xid = intval($this->_db->loadResult());
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		if ($xid && $xid != intval($this->id)) {
			$app->enqueueMessage((Text::sprintf('COM_BWPOSTMAN_ML_ERROR_TITLE_DOUBLE', $this->title, $xid)), 'error');
			return false;
		}

		if ($fault)
		{
			$app->setUserState('com_bwpostman.edit.mailinglist.data', $this);
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
		Factory::getApplication()->setUserState('com_bwpostman.edit.mailinglist.id', $this->id);

		return $res;
	}

	/**
	 * Method to get the mailinglists by restriction of archive, published and access
	 *
	 * @param array     $mailinglists
	 * @param string    $condition
	 * @param integer   $archived
	 * @param boolean   $restricted
	 *
	 * @return array
	 *
	 * @throws Exception
	 *
	 * @since 2.4.0 (here, before since 2.3.0 at mailinglist helper)
	 */
	public function getMailinglistsByRestriction($mailinglists, $condition = 'available', $archived = 0, $restricted = true)
	{
		$mls   = null;
		$restrictedMls = array();

		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->select('id');
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('archive_flag') . ' = ' . (int) $archived);

		if ((int)$archived === 0)
		{
			switch ($condition)
			{
				case 'available':
					$query->where($db->quoteName('published') . ' = ' . 1);
					$query->where($db->quoteName('access') . ' = ' . 1);
					break;
				case 'unavailable':
					$query->where($db->quoteName('published') . ' = ' . 1);
					$query->where($db->quoteName('access') . ' > ' . 1);
					break;
				case 'internal':
					$query->where($db->quoteName('published') . ' = ' . 0);
					break;
			}
		}

		$db->setQuery($query);

		try
		{
			$mls = $db->loadColumn();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		if ($restricted === true)
		{
			$resultingMls = array_intersect($mailinglists, $mls);
		}
		else
		{
			$resultingMls = $mls;
		}

		if (count($resultingMls) > 0)
		{
			$restrictedMls = $resultingMls;
		}

		return $restrictedMls;
	}

	/**
	 * Method to get the data of a single Mailinglist for raw view
	 *
	 * @param 	int $ml_id      Mailinglist ID
	 *
	 * @return 	object Mailinglist
	 *
	 * @throws Exception
	 *
	 * @since 2.4.0 here
	 */
	public function getSingleMailinglist($ml_id = null)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('a') . '.*');
		$query->from($db->quoteName($this->_tbl) . ' AS ' . $db->quoteName('a'));
		$query->where($db->quoteName('a') . '.' . $db->quoteName('id') . ' = ' . (int) $ml_id);
		// Join over the asset groups.
		$query->select($db->quoteName('ag') . '.' . $db->quoteName('title') . ' AS ' . $db->quoteName('access_level'));
		$query->join(
			'LEFT',
			$db->quoteName('#__viewlevels') . ' AS ' . $db->quoteName('ag') . ' ON ' .
			$db->quoteName('ag') . '.' . $db->quoteName('id') . ' = ' . $db->quoteName('a') . '.' . $db->quoteName('access')
		);

		$db->setQuery($query);
		try
		{
			$mailinglist = $db->loadObject();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $mailinglist;
	}

	/**
	 * Method to get all mailinglists which the user is authorized to see
	 *
	 * @param   integer     $id
	 * @param   integer     $userId the user ID f this subscriber
	 *
	 * @return 	object Mailinglists
	 *
	 * @throws Exception
	 *
	 * @since       2.4.0 (here, before since 2.0.0 at subscriber helper)
	 */
	public function getAuthorizedMailinglists($id, $userId)
	{
		$app		    = Factory::getApplication();
		$mailinglists   = null;
		$db		    = $this->_db;
		$query		    = $db->getQuery(true);

		// get authorized viewlevels
		$accesslevels	= Access::getAuthorisedViewLevels($userId);

		$query->select('*');
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('published') . ' = ' . 1);
		$query->where($db->quoteName('archive_flag') . ' = ' . 0);

		if (!in_array('3', $accesslevels))
		{
			// A user shall only see mailinglists which are public or - if registered - accessible for his view level and published
			$query->where($db->quoteName('access') . ' IN (' . implode(',', $accesslevels) . ')');
		}

		$query->order($db->quoteName('title') . 'ASC');

		try
		{
			$db->setQuery($query);
			$mailinglists = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		// Does the subscriber has internal mailinglists?
		$selected	= $app->getUserState('com_bwpostman.subscriber.selected_lists', '');

		if (is_array($selected))
		{
			$ml_ids		= array();
			$add_mls	= array();

			// compare available mailinglists with selected mailinglists, get difference
			foreach ($mailinglists as $value)
			{
				$ml_ids[]	= $value->id;
			}

			$get_mls	= array_diff($selected, $ml_ids);

			// if there are internal mailinglists selected, get them ...
			if (is_array($get_mls) && !empty($get_mls))
			{
				$query->clear();
				$query->select('*');
				$query->from($db->quoteName($this->_tbl));
				$query->where($db->quoteName('id') . ' IN (' . implode(',', $get_mls) . ')');
				$query->order($db->quoteName('title') . 'ASC');

				try
				{
					$db->setQuery($query);
					$add_mls = $db->loadObjectList();
				}
				catch (RuntimeException $e)
				{
					$app->enqueueMessage($e->getMessage(), 'error');
				}
			}
		}

		// ...and add them to the mailinglists array
		if (!empty($add_mls))
		{
			$mailinglists	= array_merge($mailinglists, $add_mls);
		}

		return $mailinglists;
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
