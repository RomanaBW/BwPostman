<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletters lists table for backend.
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Filter\InputFilter;
use RuntimeException;

/**
 * #__bwpostman_newsletters_mailinglists table handler
 * Table for storing the mailinglists to which a newsletter shall be send
 *
 * @package		BwPostman-Admin
 *
 * @subpackage	Newsletters
 *
 * @since       0.9.1
 */
class NewslettersMailinglistsTable extends Table
{
	/**
	 * @var int Primary Key Newsletter-ID
	 *
	 * @since       0.9.1
	 */
	public $newsletter_id = null;

	/**
	 * @var int Primary Key Mailinglist-ID
	 *
	 * @since       0.9.1
	 */
	public $mailinglist_id = null;

	/**
	 * Constructor
	 *
	 * @param 	DatabaseDriver  $db Database object
	 *
	 * @since       0.9.1
	 */
	public function __construct($db = null)
	{
		parent::__construct('#__bwpostman_newsletters_mailinglists', 'newsletter_id', $db);
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
	 * @since  3.0.0
	 */
	public function check(): bool
	{
		// Remove all HTML tags from the title and description
		$filter = new InputFilter(array(), array(), 0, 0);

		$this->newsletter_id  = $filter->clean($this->newsletter_id, 'UINT');
		$this->mailinglist_id = $filter->clean($this->mailinglist_id, 'UINT');

		return true;
	}

	/**
	 * Method to duplicate the mailinglist entries of a newsletter to a new one
	 *
	 * @access	public
	 *
	 * @param int $oldid ID of the existing newsletter
	 * @param int $newid ID of the copied newsletter
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function copyLists(int $oldid, int $newid): bool
	{
		$lists    = array();
		$db       = $this->_db;
		$query    = $db->getQuery(true);
		$subQuery = $db->getQuery(true);

		$subQuery->select($db->quote($newid) . ' AS ' . $db->quoteName('newsletter_id'));
		$subQuery->select($db->quoteName('mailinglist_id'));
		$subQuery->from($db->quoteName($this->_tbl));
		$subQuery->where($db->quoteName('newsletter_id') . ' = ' . $oldid);

		try
		{
			$db->setQuery($subQuery);

			$lists = $db->loadAssocList();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'NlMlTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		foreach ($lists as $list)
		{
			$query->clear();
			$query->insert($db->quoteName($this->_tbl));
			$query->columns(
				array(
				$db->quoteName('newsletter_id'),
				$db->quoteName('mailinglist_id')
				)
			);
			$query->values(
				(int) $list['newsletter_id'] . ',' .
					(int) $list['mailinglist_id']
			);

			try
			{
				$db->setQuery($query);
				$db->execute();
			}
			catch (RuntimeException $exception)
			{
                BwPostmanHelper::logException($exception, 'NlMlTable BE');

                Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_NL_COPY_MAILINGLISTS_FAILED'), 'error');
			}
		}

		return true;
	}

	/**
	 * Returns the identity (primary key) value of this record
	 *
	 * @return  array|string
	 *
	 * @since  3.0.0
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
	 * @since   3.0.0
	 */
	public function hasField($key): bool
	{
		$key = $this->getColumnAlias($key);

		return property_exists($this, $key);
	}

	/**
	 * Method to delete the entries of a newsletter
	 *
	 * @access	public
	 *
	 * @param integer $nlId ID of the newsletter
	 *
	 * @throws Exception
	 *
	 * @since       3.0.0
	 */
	public function deleteNewsletter(int $nlId)
	{
		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->delete($db->quoteName($this->_tbl));
		$query->where($db->quoteName('newsletter_id') . ' =  ' . $nlId);

		try
		{
			$db->setQuery($query);
			$db->execute();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'NlMlTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}
	}

	/**
	 * Method to insert a newsletter
	 *
	 * @access	public
	 *
	 * @param integer $nlId ID of the newsletter
	 * @param integer $mlId ID of the mailinglist
	 *
	 * @throws Exception
	 *
	 * @since       3.0.0
	 */
	public function insertNewsletter(int $nlId, int $mlId)
	{
		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->insert($db->quoteName($this->_tbl));
		$query->columns(
			array(
				$db->quoteName('newsletter_id'),
				$db->quoteName('mailinglist_id')
			)
		);
		$query->values(
			$nlId . ',' .
			$mlId
		);

		try
		{
			$db->setQuery($query);
			$db->execute();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'NlMlTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}
	}

	/**
	 * Method to get associated mailing lists by newsletter
	 *
	 * @param integer $id newsletter id
	 *
	 * @return array
	 *
	 * @throws Exception
	 *
	 * @since 3.0.0 (here, before since 2.3.0 at BE newsletter model)
	 */
	public function getAssociatedMailinglistsByNewsletter(int $id): array
	{
		$mailinglists = array();

		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->select($db->quoteName('mailinglist_id'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('newsletter_id') . ' = ' . $id);

		try
		{
			$db->setQuery($query);

			$mailinglists = $db->loadColumn();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'NlMlTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return $mailinglists;
	}

	/**
	 * Method to remove the mailinglist from the cross table #__bwpostman_newsletters_mailinglists
	 *
	 * @param integer $id
	 *
	 * @return bool
	 *
	 * @throws Exception
	 *
	 * @since  3.0.0 (here, before since 2.0.0 at mailinglist model)
	 */
	public function deleteMailinglistNewsletters(int $id): bool
	{
		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->delete($db->quoteName($this->_tbl));
		$query->where($db->quoteName('mailinglist_id') . ' =  ' . $db->quote($id));

		try
		{
			$db->setQuery($query);
			$db->execute();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'NlMlTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
			return false;
		}

		return true;
	}
}

