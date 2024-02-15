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

namespace BoldtWebservice\Component\BwPostman\Administrator\Table;

use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwException;
use DateTime;
use Exception;
use JAccessRules;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Versioning\VersionableTableInterface;
use Joomla\Database\DatabaseDriver;
use Joomla\Filter\InputFilter;
use Joomla\CMS\Access\Access;
use Joomla\Utilities\ArrayHelper;
use RuntimeException;

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
class MailinglistTable extends Table implements VersionableTableInterface
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
	public $modified_time = null;

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
	public $checked_out_time = null;

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
	 * @param 	DatabaseDriver  $db Database object
	 *
	 * @since       0.9.1
	 */
	public function __construct($db = null)
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
	public function getAssetName(): string
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
	public function getAssetTitle(): ?string
	{
		return self::_getAssetTitle();
	}

	/**
	 * Alias function
	 *
	 * @return  int
	 *
	 * @throws Exception
	 *
	 * @since   1.0.1
	 */
	public function getAssetParentId(): int
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
	protected function _getAssetName(): string
	{
		$k = $this->_tbl_key;
		return 'com_bwpostman.mailinglist.' . $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	protected function _getAssetTitle(): ?string
	{
		return $this->title;
	}

	/**
	 * Method to get the parent asset id for the record
	 *
	 * @param Table|null $table A Table object (optional) for the asset parent
	 * @param null       $id    The id (optional) of the content.
	 *
	 * @return  integer
	 *
	 * @throws Exception
	 * @since   11.1
	 */
	protected function _getAssetParentId(Table $table = null, $id = null): int
	{
//		$MvcFactory = Factory::getApplication()->bootComponent('com_bwpostman')->getMVCFactory();
//		$asset      = $MvcFactory->createTable('Asset', 'Administrator');
		$asset = Table::getInstance('Asset');

		$asset->loadByName('com_bwpostman.mailinglist');
		return (int)$asset->id;
	}

	/**
	 * Overloaded bind function
	 *
	 * @access public
	 *
	 * @param   array|object  $src     An associative array or object to bind to the Table instance.
	 * @param   array|string  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return boolean
	 *
	 * @throws BwException
	 *
	 * @since       0.9.1
	 */
	public function bind($src, $ignore=''): bool
	{
		// Bind the rules.
		if (is_object($src))
		{
			if (property_exists($src, 'rules') && is_array($src->rules))
			{
				$rules = new JAccessRules($src->rules);
				$this->setRules($rules);
			}
		}
		elseif (is_array($src))
		{
			if (array_key_exists('rules', $src) && is_array($src['rules']))
			{
				$rules = new JAccessRules($src['rules']);
				$this->setRules($rules);
			}
		}
		else
		{
			throw new BwException(Text::sprintf('JLIB_DATABASE_ERROR_BIND_FAILED_INVALID_SOURCE_ARGUMENT', get_class($this)));
		}

		// Cast properties
		$this->id	= (int) $this->id;

		return parent::bind($src, $ignore);
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
	public function check(): bool
	{
		$app   = Factory::getApplication();
		$db    = $this->_db;
		$query = $db->getQuery(true);
		$fault = false;
		$xid   = 0;

		// Remove all HTML tags from the title and description
		$filter = new InputFilter(array(), array(), 0, 0);

		$this->id               = $filter->clean($this->id, 'UINT');
		$this->asset_id         = $filter->clean($this->asset_id, 'UINT');
		$this->title            = trim($filter->clean($this->title));
		$this->description      = $filter->clean($this->description);
		$this->campaign_id      = $filter->clean($this->campaign_id, 'UINT');
		$this->access           = $filter->clean($this->access, 'UINT');
		$this->published        = $filter->clean($this->published, 'UINT');
		$this->created_date     = $filter->clean($this->created_date);
		$this->created_by       = $filter->clean($this->created_by, 'INT');
		$this->modified_time    = $filter->clean($this->modified_time);
		$this->modified_by      = $filter->clean($this->modified_by, 'INT');
		$this->checked_out      = $filter->clean($this->checked_out, 'INT');
		$this->checked_out_time = $filter->clean($this->checked_out_time);
		$this->archive_flag     = $filter->clean($this->archive_flag, 'UINT');
		$this->archive_date     = $filter->clean($this->archive_date);
		$this->archived_by      = $filter->clean($this->archived_by, 'INT');

		// Check for valid title
		if ($this->title === '')
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_ML_ERROR_TITLE'), 'error');
			$fault = true;
		}

		// Check for valid title
		if (trim($this->description) == '')
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_ML_ERROR_DESCRIPTION'), 'error');
			$fault = true;
		}

		// Check for existing title
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('title') . ' = ' . $db->quote($this->title));

		try
		{
			$db->setQuery($query);

			$xid = intval($db->loadResult());
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'MailinglistTable BE');

            $app->enqueueMessage($exception->getMessage(), 'error');
		}

		if ($xid && $xid !== intval($this->id)) {
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
	public function store($updateNulls = false): bool
	{
		$date = Factory::getDate();
		$user = Factory::getApplication()->getIdentity();

		if ($this->id)
		{
			// Existing mailing list
			$this->modified_time = $date->toSql();
			$this->modified_by   = $user->get('id');
		}
		else
		{
			// New mailing list
			$this->created_date = $date->toSql();
			$this->created_by   = $user->get('id');
		}

		// Ensure nulldate columns have correct nulldate
		$nulldateCols = array(
			'modified_time',
			'checked_out_time',
			'archive_date',
		);

		foreach ($nulldateCols as $nulldateCol)
		{
			if ($this->$nulldateCol === '' || $this->$nulldateCol === $this->_db->getNullDate())
			{
				$this->$nulldateCol = null;
			}
		}

		$res = parent::store($updateNulls);
		Factory::getApplication()->setUserState('com_bwpostman.edit.mailinglist.id', $this->id);

		return $res;
	}

	/**
	 * Method to get the mailinglists by restriction of archive, published and access
	 *
	 * @param array   $mailinglists
	 * @param string  $condition
	 * @param integer $archived
	 * @param boolean $restricted
	 *
	 * @return array
	 *
	 * @throws Exception
	 *
	 * @since 2.4.0 (here, before since 2.3.0 at mailinglist helper)
	 */
	public function getMailinglistsByRestriction(array $mailinglists, string $condition = 'available', int $archived = 0, bool $restricted = true): ?array
	{
		$mls = null;
		$restrictedMls = array();

		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->select('id');
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('archive_flag') . ' = ' . $archived);

		if ($archived === 0)
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

		try
		{
			$db->setQuery($query);

			$mls = $db->loadColumn();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'MailinglistTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		if ($restricted === true)
		{
			$resultingMls = array_intersect(ArrayHelper::toInteger($mailinglists), $mls);
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
	 * Method to get all mailinglists which the user is authorized to see
	 *
	 * @param integer $userId the user ID f this subscriber
	 *
	 * @return    array Mailinglists
	 *
	 * @throws Exception
	 * @since       2.4.0 (here, before since 2.0.0 at subscriber helper)
	 */
	public function getAuthorizedMailinglists(int $userId): ?array
	{
		$app          = Factory::getApplication();
		$mailinglists = null;
		$db           = $this->_db;
		$query        = $db->getQuery(true);

		// Get the access levels for the user, preset with access level guest and public
		$publicAccess = array(1, 5);
		$userAccess   = Access::getAuthorisedViewLevels($userId);
		$accesslevels  = array_unique(array_merge($publicAccess, $userAccess));

		$query->select('*');
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('access') . ' IN (' . implode(',', $accesslevels) . ')');
		$query->where($db->quoteName('published') . ' = ' . 1);
		$query->where($db->quoteName('archive_flag') . ' = ' . 0);
		$query->order($db->quoteName('title') . 'ASC');

		try
		{
			$db->setQuery($query);

			$mailinglists = $db->loadObjectList();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'MailinglistTable BE');

            $app->enqueueMessage($exception->getMessage(), 'error');
		}

		// Does the subscriber already has internal mailinglists?
		$selected = $app->getUserState('com_bwpostman.subscriber.selected_lists', '');

		if (is_array($selected))
		{
			$ml_ids  = array();
			$add_mls = array();

			// compare available mailinglists with selected mailinglists, get difference
			foreach ($mailinglists as $value)
			{
				$ml_ids[] = $value->id;
			}

			$get_mls = array_diff(ArrayHelper::toInteger($selected), $ml_ids);

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
				catch (RuntimeException $exception)
				{
                    BwPostmanHelper::logException($exception, 'MailinglistTable BE');

                    $app->enqueueMessage($exception->getMessage(), 'error');
				}
			}
		}

		// ...and add them to the mailinglists array
		if (!empty($add_mls))
		{
			$mailinglists = array_merge($mailinglists, $add_mls);
		}

		return $mailinglists;
	}

	/**
	 * Method to get the data of the mailinglists a user is subscribed to from their mailinglist ids
	 *
	 * @param array $mailinglist_ids
	 *
	 * @return array|null
	 *
	 * @throws Exception
	 *
	 * @since 2.4.0
	 */
	public function getCompleteMailinglistsOfSubscriber(array $mailinglist_ids): ?array
	{
		$lists = array();

		if (!empty($mailinglist_ids))
		{
			$mailinglists = implode(',', $mailinglist_ids);
		}
		else
		{
			$mailinglists = 0;
		}

		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->select($db->quoteName('id'));
		$query->select($db->quoteName('title'));
		$query->select($db->quoteName('description'));
		$query->select($db->quoteName('archive_flag'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('id') . ' IN  (' . $mailinglists . ')');
		$query->where($db->quoteName('archive_flag') . ' = ' . 0);

		try
		{
			$db->setQuery($query);

			$lists = $db->loadObjectList();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'MailinglistTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return $lists;
	}

	/**
	 * Method to get id and title of all provided mailinglist ids
	 *
	 * @param array $mls ids of mailinglists to get the title for
	 *
	 * @return 	array|null mailinglists
	 *
	 * @throws Exception
	 *
	 * @since  2.4.0
	 */
	public function getMailinglistsIdTitle(array $mls): ?array
	{
		$mailinglists = array();
		$db     = $this->_db;
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('id'));
		$query->select($db->quoteName('title'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('id') . ' IN (' . implode(',', $mls) . ')');

		try
		{
			$db->setQuery($query);

			$mailinglists = $db->loadAssocList();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'MailinglistTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return $mailinglists;
	}

	/**
	 * Method to get id as value and title as text of all mailinglists
	 *
	 * @return 	array|null mailinglists
	 *
	 * @throws Exception
	 *
	 * @since  2.4.0 (here, before since 1.0.8 at subscribers model)
	 */
	public function getMailinglistsValueText(): ?array
	{
		$mailinglists = array();
		$db     = $this->_db;
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('id') . ' AS value');
		$query->select($db->quoteName('title') . ' AS text');
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('archive_flag') . ' = ' . 0);
		$query->order('title');

		try
		{
			$db->setQuery($query);

			$mailinglists = $db->loadObjectList();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'MailinglistTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return $mailinglists;
	}

	/**
	 * Method to get id and title of all mailinglists
	 *
	 * @access 	public
	 *
	 * @return 	array|null mailinglists
	 *
	 * @throws Exception
	 *
	 * @since  2.4.0 (here, before since 1.0.8 at subscribers model)
	 */
	public function getPublishedMailinglistsIds(): ?array
	{
		$mailinglists = array();
		$db     = $this->_db;
		$query	= $db->getQuery(true);

		$query->select('id');
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('published') . ' = ' . 1);

		try
		{
			$this->_db->setQuery($query);

			$mailinglists = $db->loadColumn();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'MailinglistTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return $mailinglists;
	}

	/**
	 * Method to get all published mailing lists which the user is authorized to see
	 *
	 * @param array $viewLevels the access levels to search for
	 *
	 * @return 	array|null	ID and title of allowed mailinglists
	 *
	 * @throws Exception
	 *
	 * @since  2.4.0 (here, before since 1.0.1 at FE newsletters model)
	 */
	public function getAllowedMailinglists(array $viewLevels): ?array
	{
		$mailinglists   = null;
		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->select('id');
		$query->from($db->quoteName('#__bwpostman_mailinglists'));
		$query->where($db->quoteName('access') . ' IN (' . implode(',', $viewLevels) . ')');
		$query->where($db->quoteName('published') . ' = ' . 1);

		try
		{
			$db->setQuery($query);

			$mailinglists = $db->loadAssocList();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'MailinglistTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		$allowed = array();

		foreach ($mailinglists as $item)
		{
			$allowed[] = $item['id'];
		}

		return $allowed;
	}

	/**
	 * Returns the identity (primary key) value of this record
	 *
	 * @return  array|string
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
	public function hasField($key): bool
	{
		$key = $this->getColumnAlias($key);

		return property_exists($this, $key);
	}

	/**
	 * Get the type alias for the history table
	 *
	 * The type alias generally is the internal component name with the
	 * content type. Ex.: com_content.article
	 *
	 * @return  string  The alias as described above
	 *
	 * @since   4.0.0
	 */
	public function getTypeAlias(): string
	{
		return 'com_bwpostman.mailinglist';
	}
}
