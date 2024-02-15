<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman sendmail content table for backend.
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
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwException;
use Exception;
use JAccessRules;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\Filter\InputFilter;
use RuntimeException;

/**
 * #__bwpostman_sendmailcontent table handler
 * Table for storing the prepared data for sending a newsletter
 *
 * @package		BwPostman-Admin
 *
 * @subpackage	Newsletters
 *
 * @since       0.9.1
 */
class SendmailcontentTable extends Table
{
	/**
	 * @var int Primary Key --> every ID exists twice (once for mode text, once for mode html
	 *
	 * @since       0.9.1
	 */
	public $id = null;

	/**
	 * @var int Primary Key --> 0 = Text, 1 = HTML
	 *
	 * @since       0.9.1
	 */
	public $mode = null;

	/**
	 * @var int Newsletter-ID
	 *
	 * @since       0.9.1
	 */
	public $nl_id = null;

	/**
	 * @var string Sender name
	 *
	 * @since       0.9.1
	 */
	public $from_name = null;

	/**
	 * @var string Sender email
	 *
	 * @since       0.9.1
	 */
	public $from_email = null;

	/**
	 * @var string Subject
	 *
	 * @since       0.9.1
	 */
	public $subject = null;

	/**
	 * @var String Email-body
	 *
	 * @since       0.9.1
	 */
	public $body = '';

	/**
	 * @var string CC email
	 *
	 * @since       0.9.1
	 */
	public $cc_email = null;

	/**
	 * @var string BCC email
	 *
	 * @since       0.9.1
	 */
	public $bcc_email = null;

	/**
	 * @var string Attachment
	 *
	 * @since       0.9.1
	 */
	public $attachment = '';

	/**
	 * @var string Reply-to email
	 *
	 * @since       0.9.1
	 */
	public $reply_email = null;

	/**
	 * @var string Reply-to name
	 *
	 * @since       0.9.1
	 */
	public $reply_name = null;

	/**
	 * @var int substitute links --> 0 = no, 1 = yes
	 *
	 * @since       2.0.0
	 */
	public $substitute_links = null;

	/**
	 * Constructor
	 *
	 * @param 	DatabaseDriver  $db Database object
	 *
	 * @since       0.9.1
	 */
	public function __construct($db = null)
	{
		parent::__construct('#__bwpostman_sendmailcontent', 'id', $db);
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
		$this->id = (int) $this->id;

		return parent::bind($src, $ignore);
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 *
	 * @return boolean True
	 *
	 * @since       0.9.1
	 */
	public function check(): bool
	{
		$filter = new InputFilter(array(), array(), 0, 0);

		$this->id               = $filter->clean($this->id, 'UINT');
		$this->mode             = $filter->clean($this->mode, 'UINT');
		$this->nl_id            = $filter->clean($this->nl_id, 'UINT');
		$this->from_name        = trim($filter->clean($this->from_name));
		$this->from_email       = trim($filter->clean($this->from_email));
		$this->subject          = trim($filter->clean($this->subject));
		$this->body             = $filter->clean($this->body, 'RAW');
		$this->cc_email         = trim($filter->clean($this->cc_email));
		$this->bcc_email        = trim($filter->clean($this->bcc_email));
		$this->attachment       = trim($filter->clean($this->attachment));
		$this->reply_name       = trim($filter->clean($this->reply_name));
		$this->reply_email      = trim($filter->clean($this->reply_email));
		$this->substitute_links = $filter->clean($this->substitute_links, 'UINT');

		return true;
	}


	/**
	 * Overloaded store method
	 *
	 * @access 	public
	 *
	 * @param	boolean True to update fields even if they are null.
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function store($updateNulls = false): bool
	{
		$k     = $this->_tbl_key;
		$res   = null;
		$query = $this->_db->getQuery(true);

		if (!$this->$k)
		{
			// Find the next possible id and insert
			$query->select('IFNULL(MAX(id)+1,1) AS ' . $this->_db->quoteName('id'));
			$query->from($this->_db->quoteName($this->_tbl));

			try
			{
				$this->_db->setQuery($query);

				$res = $this->_db->loadResult();
			}
			catch (RuntimeException $exception)
			{
                BwPostmanHelper::logException($exception, 'SendmailContentTable BE');

                Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
			}

			if ($res)
			{
				$this->$k = $res;
			}
		}

		if ($this->$k)
		{
			// An id value is set
			try
			{
				$this->_db->insertObject($this->_tbl, $this);
			}
			catch (RuntimeException $exception)
			{
                BwPostmanHelper::logException($exception, 'SendmailContentTable BE');

                Factory::getApplication()->enqueueMessage(get_class($this) . '::store failed - ' . $exception->getMessage());

				return false;
			}
		}

		return true;
	}

	/**
	 * Overloaded load method
	 *
	 * @access	public
	 *
	 * @param 	int		    $keys       ID
	 * @param 	boolean	    $reset      Mode (0 = Text, 1 = HTML)
	 *
	 * @return bool|int
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function load($keys = null, $reset = true)
	{
		if (!$keys)
		{
			return 0;
		}

		// If (empty($mode)) return 0;
		$app    = Factory::getApplication();
		$mode   = $app->getUserState('com_bwpostman.newsletter.send.mode', 1);
		$db     = $this->_db;
		$query  = $db->getQuery(true);
		$result = null;

		$this->reset();

		$query->select('*');
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('id') . ' = ' . (int) $keys);
		$query->where($db->quoteName('mode') . ' = ' . (int) $mode);

		try
		{
			$db->setQuery($query);

			$result = $db->loadAssoc();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'SendmailContentTable BE');

            $app->enqueueMessage($exception->getMessage(), 'error');
		}

		return $this->bind($result);
	}

	/**
	 * Method to get  newsletter content
	 *
	 * @param int $id id of the content
	 *
	 * @return	string|null string on success, null on failure.
	 *
	 * @throws Exception
	 *
	 * @since	2.4.0
	 */
	public function getContent(int $id): ?string
	{
		$newsletter = null;

		$db    = $this->_db;
		$query = $db->getQuery(true);

		// build query
		$query->select($db->quoteName('body'));
		$query->from($db->quoteName($this->_tbl) . ' AS ' . $db->quoteName('a'));
		$query->where($db->quoteName('a') . '.' . $db->quoteName('nl_id') . ' = ' . $id);
		$query->where($db->quoteName('a') . '.' . $db->quoteName('mode') . ' = ' . 1);

		try
		{
			$db->setQuery($query);

			$newsletter = $db->loadResult();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'SendmailContentTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return $newsletter;
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
	public function hasField($key): bool
	{
		$key = $this->getColumnAlias($key);

		return property_exists($this, $key);
	}
}
