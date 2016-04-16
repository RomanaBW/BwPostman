<?php
/**
 * BwTimeControl Plugin for BwPostman Newsletter Component
 *
 * BwTimeControl automailing values for campaigns table for backend.
 *
 * @version 1.2.0 bwplgtc
 * @package BwPostman BwTimeControl Plugin
 * @author Romana Boldt
 * @copyright (C) 2014 Boldt Webservice <forum@boldt-webservice.de>
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
 * #__bwpostman_tc_campaign table handler
 * Table for storing the automailing campaign data
 *
 * @package		BwPostman BwTimeControl Plugin
 */
class BwPostmanTableTc_Campaign extends JTable
{
	/** @var int Primary Key */
	var $tc_id = null;

	/** @var int campaign_id */
	var $campaign_id = null;

	/** @var boolean automailing */
	var $automailing = FALSE;

	/** @var JSON encoded automailing values */
	var $automailing_values = null;

	/** @var boolean chaining */
	var $chaining = TRUE;

	/** @var string mail ordering */
	var $mail_ordering = null;

	/** @var boolean active */
	var $active = FALSE;

	/** @var int Checked-out owner */
	var $checked_out = 0;

	/** @var datetime Checked-out time */
	var $checked_out_time = '0000-00-00 00:00:00';

	/** @var date publish up time */
	var $publish_up = '0000-00-00 00:00:00';

	/** @var datetime publish down time */
	var $publish_down = '0000-00-00 00:00:00';

	/** @var date creation date of the newsletter */
	var $created = '0000-00-00 00:00:00';
	
	/** @var int Author */
	var $created_by = 0;

	/** @var date last modification date of the newsletter */
	var $modified = '0000-00-00 00:00:00';
	
	/** @var int user ID */
	var $modified_by = 0;
	
	/** @var tinyint Archive-flag --> 0 = not archived, 1 = archived */
	var $archive_flag = 0;

	/** @var datetime Archive-date */
	var $archive_date = '0000-00-00 00:00:00';

	/**
	 * Constructor
	 *
	 * @param 	db Database object
	 *
	 * @since   1.2.0
	 */
	function __construct(& $db)
	{
		parent::__construct('#__bwpostman_tc_campaign', 'tc_id', $db);
	}

	/**
	 * Overloaded bind function
	 *
	 * @access 	public
	 * @param 	array Named array
	 * @param 	string Space separated list of fields not to bind
	 * @return 	boolean
	 *
	 * @since   1.2.0
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
		$this->tc_id	= (int) $this->tc_id;
		
		$result	= parent::bind($data, $ignore);

		return $result;
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True
	 *
	 * @since   1.2.0
	 */
	function check()
	{
		$app		= 	JFactory::getApplication();
		$sum_time	= array();
		$am_values	= json_decode ($this->automailing_values);
		$num_rows	= count($am_values->nl_id);
		$num_nls	= $app->getUserState('bwtimecontrol.nbr_campaign_letters', 0);
		
		// check for reasonable times
		for ($i = 0; $i < $num_rows; $i++) {
			// build times per row in minutes
			$sum_time[$i] = $am_values->day[$i]*24*60 + $am_values->hour[$i]*60 + $am_values->minute[$i];
			// if not first row...
			if ($i > 0) {
				// ...check for no selected sending time
				if ($sum_time[$i] == 0 && $i > 0) {
					$app->enqueueMessage(JText::sprintf('PLG_BWPOSTMAN_BWTIMECONTROL_TABLEERROR_CAM_TIMECHECK1', $i+1), 'error');
					return false; 
				}
				// ...check for unreasonable non-chained sending times
				if ($this->chaining == 0  && $i > 0 && $sum_time[$i] <= $sum_time[$i-1]) {
					$app->enqueueMessage(JText::sprintf('PLG_BWPOSTMAN_BWTIMECONTROL_TABLEERROR_CAM_TIMECHECK4', $i+1), 'error');
					return false;
				} 
			}
		}
		
		// check for appropriate line numbers
		if ($num_rows < $num_nls) {
			$app->enqueueMessage(JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_TABLEERROR_CAM_TIMECHECK6'), 'error');
			return false;
		}
		elseif ($num_rows > $num_nls) {
			$app->enqueueMessage(JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_TABLEERROR_CAM_TIMECHECK7'), 'error');
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
	 * @since   1.2.0
	 */
	public function store($updateNulls = false)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();
	
		if ($this->tc_id)
		{
			// Existing mailing list
			$this->modified = $date->toSql();
			$this->modified_by = $user->get('id');
		}
		else
		{
			// New mailing list
			$this->created = $date->toSql();
			$this->created_by = $user->get('id');
		}
		$res	= parent::store($updateNulls);
//		JFactory::getApplication()->setUserState('com_bwpostman.edit.campaign.id', $this->id);

		return $res;
	}

}