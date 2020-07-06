<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletters table for backend.
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
use Joomla\CMS\Mail\MailHelper;
use Joomla\Filter\InputFilter;

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
	 * @var int $is_template
	 *
	 * @since       2.2.0
	 */
	public $is_template = null;

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
		parent::__construct($this->_tbl, 'id', $db);
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
		$asset->loadByName('com_bwpostman.newsletter');
		return (int)$asset->id;
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
		jimport('joomla.mail.helper');

		$app	= Factory::getApplication();
		$db     = $this->_db;
		$query	= $db->getQuery(true);
		$fault	= false;
		$xid    = 0;

		// Check the publish down date is not earlier than publish up.
		if ($this->publish_down > $db->getNullDate() && $this->publish_down < $this->publish_up)
		{
			// Swap the dates.
			$temp = $this->publish_up;
			$this->publish_up = $this->publish_down;
			$this->publish_down = $temp;
		}

		// Sanitize values
		$filter				= new InputFilter(array(), array(), 0, 0);
		$this->subject		= trim($filter->clean($this->subject));
		$this->from_name	= trim($filter->clean($this->from_name));
		$this->from_email	= trim($filter->clean($this->from_email));
		$this->reply_email	= trim($filter->clean($this->reply_email));

		// no subject is unkind
		if ($this->subject === '')
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_NL_ERROR_SAVE_NO_SUBJECT'), 'error');
			$fault	= true;
		}

		// Check for existing subject
		$query->select($db->quoteName('id'));
		$query->from($this->_tbl);
		$query->where($db->quoteName('subject') . ' = ' . $db->quote($this->subject));

		$db->setQuery($query);

		try
		{
			$xid = intval($db->loadResult());
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		if ($xid && $xid != intval($this->id))
		{
			$app->enqueueMessage((Text::sprintf('COM_BWPOSTMAN_NL_WARNING_SUBJECT_DOUBLE', $this->subject)), 'warning');
		}

		// some text should be, too
		if (($this->html_version == '') && ($this->text_version == ''))
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_NL_ERROR_SAVE_NO_CONTENT'), 'error');
			$fault	= true;
		}

		// from name is mandatory
		if (empty($this->from_name))
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_NL_ERROR_SAVE_NO_FROMNAME'), 'error');
			$fault	= true;
		}

		// from email is mandatory
		if ((empty($this->from_email))  || (!MailHelper::isEmailAddress(trim($this->from_email))))
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_NL_ERROR_SAVE_NO_FROMEMAIL'), 'error');
			$fault	= true;
		}

		// reply email is mandatory
		if ((empty($this->reply_email))  || (!MailHelper::isEmailAddress(trim($this->reply_email))))
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_NL_ERROR_SAVE_NO_REPLYEMAIL'), 'error');
			$fault	= true;
		}

		if ($fault)
		{
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
	 * @throws Exception
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
			{
				return false;
			}

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
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return true;
	}

	/**
	 * Function change isTemplate
	 *
	 * @param $id
	 *
	 * @return boolean | int false on failure, on success set value
	 *
	 * @throws Exception
	 *
	 * @since       2.2.0
	 */
	public function changeIsTemplate($id = null)
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
			{
				return false;
			}

			$nl_id = $this->id;
		}

		$newIsTemplate = ($this->is_template + 1) % 2;

		$_db	= $this->getDbo();
		$query	= $_db->getQuery(true);

		$query->update($_db->quoteName($this->_tbl));
		$query->set($_db->quoteName('is_template') . " = " . $newIsTemplate);
		$query->where($_db->quoteName('id') . ' = ' . (int) $nl_id);

		$_db->setQuery($query);

		try
		{
			$_db->execute();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $newIsTemplate;
	}

	/**
	 * Method check if newsletter is content template
	 *
	 * @param   integer  $id        ID of newsletter
	 *
	 * @return	boolean           state of is_template
	 *
	 * @throws Exception
	 *
	 * @since	2.4.0 (here, originally since 2.2.0 at model newsletter)
	 */
	public function isTemplate($id)
	{
		$db	= $db;
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('is_template'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('id') . ' = ' . $db->quote($id));

		$db->setQuery($query);
		try
		{
			$isTemplate = (integer)$db->loadResult();

			if ($isTemplate === 1)
			{
				return true;
			}
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return false;
	}


	/**
	 * Method to get the ID of the standard template for HTML or text mode
	 *
	 * @param   string  $mode       HTML or text
	 *
	 * @return	string	            ID of standard template
	 *
	 * @throws Exception
	 *
	 * @since	2.4.0 (here, originally since 1.2.0 at model newsletter)
	 */
	public function getStandardTpl($mode	= 'html')
	{
		$tpl    = new stdClass();
		$db	= $db;
		$query	= $db->getQuery(true);

		// Id of the standard template
		switch ($mode)
		{
			case 'html':
			default:
				$query->select($db->quoteName('id'));
				$query->from($db->quoteName('#__bwpostman_templates'));
				$query->where($db->quoteName('standard') . ' = ' . $db->quote('1'));
				$query->where($db->quoteName('tpl_id') . ' < ' . $db->quote('998'));
				break;

			case 'text':
				$query->select($db->quoteName('id') . ' AS ' . $db->quoteName('value'));
				$query->from($db->quoteName('#__bwpostman_templates'));
				$query->where($db->quoteName('standard') . ' = ' . $db->quote('1'));
				$query->where($db->quoteName('tpl_id') . ' > ' . $db->quote('997'));
				break;
		}

		$db->setQuery($query);

		try
		{
			$tpl    = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $tpl;
	}

	/**
	 * Method to set archive/unarchive a newsletter
	 *
	 * @param array   $cid      array of items to archive/unarchive
	 * @param integer $archive  archive/unarchive flag
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws Exception
	 *
	 * @since   2.4.0
	 */
	public function archive($cid, $archive)
	{
		$uid		= Factory::getUser()->get('id');

		if ($archive == 1)
		{
			$time = Factory::getDate()->toSql();
		}
		else
		{
			$time = $db->getNullDate();
			$uid	= 0;
		}

		$db		= $db;
		$query		= $db->getQuery(true);

		$query->update($db->quoteName($this->_tbl));
		$query->set($db->quoteName('archive_flag') . " = " . (int) $archive);
		$query->set($db->quoteName('archive_date') . " = " . $db->quote($time, false));
		$query->set($db->quoteName('archived_by') . " = " . (int) $uid);
		$query->where($db->quoteName('id') . ' IN (' . implode(',', $cid) . ')');

		$db->setQuery($query);
		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		return true;
	}

	/**
	 * Method to get the complete data of a specific newsletter
	 *
	 * @param integer $nlId id of the newsletter
	 *
	 * @return	object|boolean
	 *
	 * @throws Exception
	 *
	 * @since 2.4.0
	 */
	public function getNewsletterData($nlId)
	{
		$db	= $db;
		$query	= $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('id') . ' = ' . (int) $nlId);

		$db->setQuery($query);

		try
		{
			$newslettersData = $db->loadObject();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		return $newslettersData;
	}

	/**
	 * Method to get the selected content of a specific newsletter
	 *
	 * @param integer $nlId id of the newsletter
	 *
	 * @return	string
	 *
	 * @throws Exception
	 *
	 * @since 2.4.0 here
	 */
	public function getSelectedContentOfNewsletter($nlId)
	{
		$content_ids    = '';
		$db	        = $db;

		// Get selected content from the newsletters-Table
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('selected_content'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('id') . ' = ' . $nlId);

		$db->setQuery($query);
		try
		{
			$content_ids = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $content_ids;
	}

	/**
	 * Method to get the campaign id of a specific newsletter
	 *
	 * @param int $nlId
	 *
	 * @return 	integer
	 *
	 * @throws Exception
	 *
	 * @since 2.4.0 (here, before since 2.3.0 at newsletter helper)
	 */
	public function getCampaignId($nlId)
	{
		$campaignId = -1;

		$db	= Factory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('campaign_id'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('id') . ' = ' . $db->Quote($nlId));

		$db->setQuery($query);

		try
		{
			$campaignId = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return (int)$campaignId;
	}

	/**
	 * Method to get the number of newsletters depending on provided sending and archive state
	 *
	 * @param boolean $sent
	 * @param boolean $archived
	 *
	 * @return 	integer|boolean number of newsletters or false
	 *
	 * @throws Exception
	 *
	 * @since 2.4.0 (here, before since 2.3.0 at newsletter helper)
	 */
	public function getNbrOfNewsletters($sent, $archived)
	{
		$archiveFlag = 0;
		$mailingDateOperator = "=";

		if ($sent)
		{
			$mailingDateOperator = "!=";
		}

		if ($archived)
		{
			$archiveFlag = 1;
		}

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select('COUNT(*)');
		$query->from($db->quoteName($this->_tbl));

		if (!$archived)
		{
			$query->where($db->quoteName('mailing_date') . $mailingDateOperator . $db->quote('0000-00-00 00:00:00'));
		}

		$query->where($db->quoteName('archive_flag') . ' = ' . $archiveFlag);

		$db->setQuery($query);

		try
		{
			return $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
		return false;
	}

	/**
	 * Method to get the newsletters of a specific campaign depending on provided campaign id, sending and archive state
	 *
	 * @param integer $camId
	 * @param boolean $sent
	 * @param boolean $all
	 *
	 * @return 	array
	 *
	 * @throws Exception
	 *
	 * @since 2.4.0 here
	 */
	public function getSelectedNewslettersOfCampaign($camId, $sent, $all)
	{
		$newsletters = array();
		$archiveFlag = 0;
		$mailingDateOperator = "=";

		if ($sent)
		{
			$mailingDateOperator = "!=";
		}

		if ($all)
		{
			$archiveFlag = 1;
		}

		$db    = $db;
		$query = $db->getQuery(true);

		$query->select($db->quoteName('a') . '.*');
		$query->select($db->quoteName('v') . '.' . $db->quoteName('name') . ' AS author');
		$query->from($db->quoteName($this->_tbl) . ' AS a');
		$query->leftJoin(
			$db->quoteName('#__users') . ' AS ' . $db->quoteName('v')
			. ' ON ' . $db->quoteName('v') . '.' . $db->quoteName('id') . ' = ' . $db->quoteName('a') . '.' . $db->quoteName('created_by')
		);
		$query->where($db->quoteName('campaign_id') . ' = ' . $db->quote((int) $camId));
		$query->where($db->quoteName('archive_flag') . ' = ' . (int)0);

		if (!$archiveFlag)
		{
			$query->where($db->quoteName('mailing_date') . $mailingDateOperator . $db->quote('0000-00-00 00:00:00'));
		}

		$db->setQuery($query);

		try
		{
			$newsletters = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $newsletters;
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
		$date	= Factory::getDate();
		$user	= Factory::getUser();
		$app	= Factory::getApplication();
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
