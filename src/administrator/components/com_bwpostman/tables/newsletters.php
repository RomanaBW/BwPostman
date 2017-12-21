<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletters table for backend.
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

/**
 * #__bwpostman_newsletters table handler
 * Table to store the newsletters
 *
 * @package		BwPostman-Admin
 *
 * @subpackage	Newsletters
 *
 * @since       0.9.1
 */
class BwPostmanTableNewsletters extends JTable
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
	 * @var string Reply-to email
	 *
	 * @since       0.9.1
	 */
	public $reply_email = null;

	/**
	 * @var int Template-ID
	 *
	 * @since       1.1.0
	 */
	public $template_id = null;

	/**
	 * @var int Text-Template-ID
	 *
	 * @since       1.1.0
	 */
	public $text_template_id = null;

	/**
	 * @var int Campaign-ID
	 *
	 * @since       0.9.1
	 */
	public $campaign_id = null;

	/**
	 * @var string Usergroups
	 *
	 * @since       0.9.1
	 */
	public $usergroups = null;

	/**
	 * @var string Selected content
	 *
	 * @since       0.9.1
	 */
	public $selected_content = null;

	/**
	 * @var string Subject
	 *
	 * @since       0.9.1
	 */
	public $subject = null;

	/**
	 * @var string Newsletter description
	 *
	 * @since       0.9.1
	 */
	public $description = null;

	/**
	 * @var int access level/view level --> 1 = Public, 2 = Registered, 3 = Special, >3 = user defined viewlevels
	 *
	 * @since       0.9.1
	 */
	public $access = 1;

	/**
	 * @var string attachment
	 *
	 * @since       0.9.7
	 */
	public $attachment = null;

	/**
	 * @var string HTML headline
	 *
	 * @since       1.1.0
	 */
	public $intro_headline = null;

	/**
	 * @var string HTML intro text
	 *
	 * @since       1.1.0
	 */
	public $intro_text = null;

	/**
	 * @var string TEXT headline
	 *
	 * @since       1.1.0
	 */
	public $intro_text_headline = null;

	/**
	 * @var string TEXT intro text
	 *
	 * @since       1.1.0
	 */
	public $intro_text_text = null;

	/**
	 * @var string HTML-version
	 *
	 * @since       0.9.1
	 */
	public $html_version = null;

	/**
	 * @var string Text-version
	 *
	 * @since       0.9.1
	 */
	public $text_version = null;

	/**
	 * @var datetime creation date of the newsletter
	 *
	 * @since       0.9.1
	 */
	public $created_date = '0000-00-00 00:00:00';

	/**
	 * @var int Author
	 *
	 * @since       0.9.1
	 */
	public $created_by = 0;

	/**
	 * @var datetime last modification date of the newsletter
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
	 * @var datetime Mailing date
	 *
	 * @since       0.9.1
	 */
	public $mailing_date = '0000-00-00 00:00:00';

	/**
	 * @var int Published
	 *
	 * @since       0.9.1
	 */
	public $published = null;

	/**
	 * @var datetime for publishing up a newsletter
	 *
	 * @since       1.2.0
	 */
	public $publish_up = '0000-00-00 00:00:00';

	/**
	 * @var datetime for publishing down a newsletter
	 *
	 * @since       1.2.0
	 */
	public $publish_down = '0000-00-00 00:00:00';

	/**
	 * @var int Checked-out Owner
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
	public $archive_date = 0;

	/**
	 * @var int ID --> 0 = newsletter is not archived, another ID = account is archived by an administrator
	 *
	 * @since       0.9.1
	 */
	public $archived_by = 0;

	/**
	 * @var int Number of views at the frontend
	 *
	 * @since       0.9.1
	 */
	public $hits = null;

	/**
	 * @var int substitute links --> 0 = no, 1 = yes
	 *
	 * @since       2.0.0
	 */
	public $substitute_links = null;

	/**
	 * Constructor
	 *
	 * @param 	JDatabaseDriver  $db Database object
	 *
	 * @since       0.9.1
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__bwpostman_newsletters', 'id', $db);
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
	 * @since   1.0.1
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_bwpostman.newsletter.' . (int) $this->$k;
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
		return $this->subject;
	}

	/**
	 * Method to get the parent asset id for the record
	 *
	 * @param   JTable   $table  A JTable object (optional) for the asset parent
	 * @param   integer  $id     The id (optional) of the content.
	 *
	 * @return  integer
	 *
	 * @throws Exception
	 *
	 * @since   1.0.1
	 */
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		// Initialise variables.
		$assetId = null;
		$result  = 0;

		// Build the query to get the asset id for the component.
		$query = $this->_db->getQuery(true);
		$query->select($this->_db->quoteName('id'));
		$query->from($this->_db->quoteName('#__assets'));
		$query->where($this->_db->quoteName('name') . " LIKE 'com_bwpostman'");

		// Get the asset id from the database.
		$this->_db->setQuery($query);
		try
		{
			$result = $this->_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
		if ($result)
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
	 * @param array|object  $data       Named array
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
			throw new BwException(JText::sprintf('JLIB_DATABASE_ERROR_BIND_FAILED_INVALID_SOURCE_ARGUMENT', get_class($this)));
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
	 * @since       0.9.1
	 */
	public function check()
	{
		jimport ('joomla.mail.helper');

		$app	= JFactory::getApplication();
		$query	= $this->_db->getQuery(true);
		$fault	= false;
		$xid    = 0;

		// Check the publish down date is not earlier than publish up.
		if ($this->publish_down > $this->_db->getNullDate() && $this->publish_down < $this->publish_up)
		{
			// Swap the dates.
			$temp = $this->publish_up;
			$this->publish_up = $this->publish_down;
			$this->publish_down = $temp;
		}

		// no subject is unkind
		if ($this->subject == '')
		{
			$app->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_ERROR_SAVE_NO_SUBJECT'), 'error');
			$fault	= true;
		}

		// Check for existing subject
		$query->select($this->_db->quoteName('id'));
		$query->from($this->_tbl);
		$query->where($this->_db->quoteName('subject') . ' = ' . $this->_db->quote($this->subject));

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
			$app->enqueueMessage((JText::sprintf('COM_BWPOSTMAN_NL_WARNING_SUBJECT_DOUBLE', $this->subject)), 'warning');
		}

		// some text should be, too
		if (($this->html_version == '') && ($this->text_version == ''))
		{
			$app->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_ERROR_SAVE_NO_CONTENT'), 'error');
			$fault	= true;
		}

		// from name is mandatory
		if (empty($this->from_name))
		{
			$app->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_ERROR_SAVE_NO_FROMNAME'), 'error');
			$fault	= true;
		}

		// from email is mandatory
		if ((empty($this->from_email))  || (!JMailHelper::isEmailAddress(trim($this->from_email))))
		{
			$app->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_ERROR_SAVE_NO_FROMEMAIL'), 'error');
			$fault	= true;
		}

		// reply email is mandatory
		if ((empty($this->reply_email))  || (!JMailHelper::isEmailAddress(trim($this->reply_email))))
		{
			$app->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_ERROR_SAVE_NO_REPLYEMAIL'), 'error');
			$fault	= true;
		}

		if ($fault)
		{
//			$app->setUserState('com_bwpostman.edit.newsletter.data', $this);
			return false;
		}
		return true;
	}

	/**
	 * Function markAsSent
	 *
	 * @param $id
	 *
	 * @return boolean True on success
	 *
	 * @since       0.9.1
	 */
	public function markAsSent($id = null)
	{
		if ($id)
		{
			// Take the given id
			$nl_id = $id;
		}
		else
		{
			// Take the id loaded in this object
			if (!$this->id)
				return false;
			$nl_id = $this->id;
		}

		$_db	= $this->getDbo();
		$query	= $_db->getQuery(true);

		$query->update($_db->quoteName($this->_tbl));
		$query->set($_db->quoteName('mailing_date') . " = NOW()");
		$query->where($_db->quoteName('id') . ' = ' . (int) $nl_id);

		$_db->setQuery($query);

		try
		{
			$_db->execute();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
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
		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		$app	= JFactory::getApplication();
		$id		= $this->id;

		if ($id)
		{
			// Existing newsletter list
			$this->modified_time = $date->toSql();
			$this->modified_by = $user->get('id');
		}
		else
		{
			// New newsletter list
			$this->created_date = $date->toSql();
			$this->created_by = $user->get('id');
		}
		$res	= parent::store($updateNulls);
		$app->setUserState('com_bwpostman.newsletter.id', $this->id);

		// reset tab to basic if adding new newsletter was ok
//		if ($res && $id == 0) $app->setUserState('com_bwpostman.newsletter.tab', 'edit_basic');

		return $res;
	}
}
