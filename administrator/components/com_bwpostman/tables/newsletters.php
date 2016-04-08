<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletters table for backend.
 *
 * @version 1.3.1 bwpm
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
 * #__bwpostman_newsletters table handler
 * Table to store the newsletters
 *
 * @package		BwPostman-Admin
 * @subpackage	Newsletters
 */
class BwPostmanTableNewsletters extends JTable
{
	/** @var int Primary Key */
	var $id = null;

	/** @var int asset_id */
	var $asset_id = null;

	/** @var string Sender name */
	var $from_name = null;

	/** @var string Sender email */
	var $from_email = null;

	/** @var string Reply-to email */
	var $reply_email = null;

	/** @var int Template-ID */
	var $template_id = null;

	/** @var int Text-Template-ID */
	var $text_template_id = null;

	/** @var int Campaign-ID */
	var $campaign_id = null;

	/** @var string Usergroups */
	var $usergroups = null;

	/** @var string Selected content */
	var $selected_content = null;

	/** @var string Subject */
	var $subject = null;

	/** @var string Newsletter description */
	var $description = null;

	/** @var int Accesslevel/Viewlevel --> 1 = Public, 2 = Registered, 3 = Special, >3 = user defined viewlevels */
	var $access = 0;

	/** @var string attachment */
	var $attachment = null;

	/** @var string HTML headline */
	var $intro_headline = null;

		/** @var string HTML intro text */
	var $intro_text = null;

	/** @var string TEXT headline */
	var $intro_text_headline = null;

	/** @var string TEXT intro text */
	var $intro_text_text = null;

/** @var string HTML-version */
	var $html_version = null;

	/** @var string Text-version */
	var $text_version = null;

	/** @var datetime creation date of the newsletter */
	var $created_date = '0000-00-00 00:00:00';

	/** @var int Author */
	var $created_by = 0;

	/** @var datetime last modification date of the newsletter */
	var $modified_time = '0000-00-00 00:00:00';

	/** @var int user ID */
	var $modified_by = 0;

	/** @var datetime Mailing date */
	var $mailing_date = '0000-00-00 00:00:00';

	/** @var int Published */
	var $published = null;

	/** @var datetime for publishing up a newsletter */
	var $publish_up = '0000-00-00 00:00:00';

	/** @var datetime for publishing down a newsletter */
	var $publish_down = '0000-00-00 00:00:00';

	/** @var int Checked-out Owner */
	var $checked_out = 0;

	/** @var datetime Checked-out time */
	var $checked_out_time = 0;

	/** @var int Archive-flag --> 0 = not archived, 1 = archived */
	var $archive_flag = 0;

	/** @var datetime Archive-date */
	var $archive_date = 0;

	/** @var int ID --> 0 = newsletter is not archived, another ID = account is archived by an administrator */
	var $archived_by = 0;

	/** @var int Number of views at the frontend */
	var $hits = null;

	/**
	 * Constructor
	 *
	 * @param 	JDatabaseDriver  $db Database object
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
	 * @since   11.1
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
	 * @since   11.1
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
	 * @since   11.1
	 */
	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		// Initialise variables.
		$assetId = null;

		// Build the query to get the asset id for the component.
		$query = $this->_db->getQuery(true);
		$query->select($this->_db->quoteName('id'));
		$query->from($this->_db->quoteName('#__assets'));
		$query->where($this->_db->quoteName('name') . " LIKE 'com_bwpostman'");

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
	 * @param array|object  $data       Named array
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
	 * @return boolean True
	 */
	public function check()
	{
		jimport ('joomla.mail.helper');

		$app	= JFactory::getApplication();
		$query	= $this->_db->getQuery(true);
		$fault	= false;

		// Check the publish down date is not earlier than publish up.
		if ($this->publish_down > $this->_db->getNullDate() && $this->publish_down < $this->publish_up)
		{
			// Swap the dates.
			$temp = $this->publish_up;
			$this->publish_up = $this->publish_down;
			$this->publish_down = $temp;
		}

		// no subject is unkind
		if ($this->subject == '') {
			$app->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_ERROR_SAVE_NO_SUBJECT'), 'error');
			$fault	= true;
		}

		// Check for existing subject
		$query->select($this->_db->quoteName('id'));
		$query->from($this->_tbl);
		$query->where($this->_db->quoteName('subject') . ' = ' . $this->_db->Quote($this->subject));

		$this->_db->setQuery($query);

		$xid = intval($this->_db->loadResult());

		if ($xid && $xid != intval($this->id)) {
			$app->enqueueMessage((JText::sprintf('COM_BWPOSTMAN_NL_WARNING_SUBJECT_DOUBLE', $this->subject)), 'warning');
		}

		// some text should be, too
		if (($this->html_version == '') && ($this->text_version == '')) {
			$app->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_ERROR_SAVE_NO_CONTENT'), 'error');
			$fault	= true;
		}

		// from name is mandatory
		if (empty($this->from_name)) {
			$app->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_ERROR_SAVE_NO_FROMNAME'), 'error');
			$fault	= true;
		}

		// from email is mandatory
		if ((empty($this->from_email))  || (!JMailHelper::isEmailAddress(trim($this->from_email)))) {
			$app->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_ERROR_SAVE_NO_FROMEMAIL'), 'error');
			$fault	= true;
		}

		// reply email is mandatory
		if ((empty($this->reply_email))  || (!JMailHelper::isEmailAddress(trim($this->reply_email)))) {
			$app->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_ERROR_SAVE_NO_REPLYEMAIL'), 'error');
			$fault	= true;
		}

		if ($fault) {
//			$app->setUserState('com_bwpostman.edit.newsletter.data', $this);
			return false;
		}
		return true;
	}

	/**
	 * Function markAsSent
	 *
	 * @param $id
	 * @return boolean True on success
	 */
	public function markAsSent($id = null)
	{
		if ($id){
			// Take the given id
			$nl_id = $id;
		}
		else {
			// Take the id loaded in this object
			if (!$this->id) return false;
			$nl_id = $this->id;
		}

		$_db	= $this->getDBO();
		$query	= $_db->getQuery(true);

		$query->update($_db->quoteName($this->_tbl));
		$query->set($_db->quoteName('mailing_date') . " = NOW()");
		$query->where($_db->quoteName('id') . ' = ' . (int) $nl_id);

		$_db->setQuery($query);

		if (!$_db->query()){
			$this->setError($_db->getErrorMsg());
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
