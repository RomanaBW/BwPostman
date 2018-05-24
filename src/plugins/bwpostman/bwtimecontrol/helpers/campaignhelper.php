<?php
/**
 * BwPostman Newsletter BwTimeControl Plugin
 *
 * BwPostman BwTimeControl Plugin helper file for BwPostman.
 *
 * @version 2.0.0 bwplgtc
 * @package BwPostman BwTimeControl Plugin
 * @author Romana Boldt
 * @copyright (C) 2014-2018 Boldt Webservice <forum@boldt-webservice.de>
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

defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper as ArrayHelper;
use Joomla\Registry\Registry as Registry;

jimport('libraries.joomla.application.component.helper.php');

/**
 * Class BwPostmanCampaignHelper
 *
 * @since   1.2.0
 */
class BwPostmanCampaignHelper
{

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	string  $type	    The table type to instantiate
	 * @param	string	$prefix     A prefix for the table class name. Optional.
	 * @param	array	$config     Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	 *
	 * @since  1.2.0
	*/
	static public function getTable($type = 'Tc_Campaign', $prefix = 'BwPostmanTable', $config = array())
	{
		JTable::addIncludePath(JPATH_PLUGINS.'/bwpostman/bwtimecontrol/tables');
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the tc-data ID for a given campaign
	 *
	 * @access 	public
	 *
	 * @param 	int		$campaign_id    campaign ID
	 *
	 * @return 	int		tc-data ID
	 *
	 * @since	1.2.0
	 */
	static public function getTcIdFromCampaign ($campaign_id = 0)
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('tc_id'));
		$query->from($db->quoteName('#__bwpostman_tc_campaign'));
		$query->where($db->quoteName('campaign_id') . ' = ' . (int) $campaign_id);
		$db->setQuery($query);

		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());
		}

		$id = $db->loadResult();

		return $id;
	}
	/**
	 * Method to get a single record of TC-data
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   1.2.0
	 */
	static public function getItem($pk = null)
	{
		$_db    = JFactory::getDbo();

		$pk		= (!empty($pk)) ? $pk : (int) JFactory::getApplication()->getUserState('bwtimecontrol.campaign_id', 0);
		$table	= self::getTable();

		if ($pk > 0)
		{
			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				JError::raiseError(500, $_db->getErrorMsg());
				return false;
			}
		}

		// Convert to the JObject before adding other data.
		$properties = $table->getProperties(1);
		$item = ArrayHelper::toObject($properties, 'JObject');

		if (property_exists($item, 'params'))
		{
			$registry = new Registry();
			$registry->loadString($item->params);
			$item->params = $registry->toArray();
		}
		JFactory::getApplication()->setUserState('bwtimecontrol.item', $item);
		return $item;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.2.0
	 */
	static public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = self::_loadForm('bwpostman.bwtimecontrol', 'tccampaign', array('control' => 'jform', 'load_data' => $loadData));

		// @todo XML-file will not be processed

		if (empty($form)) {
			return false;
		}
		return $form;
	}

	/**
	 * Method to get a form object.
	 *
	 * @param   string   $name     The name of the form.
	 * @param   string   $source   The form source. Can be XML string if file flag is set to false.
	 * @param   array    $options  Optional array of options for the form creation.
	 * @param   boolean  $clear    Optional argument to force load a new form.
	 * @param   bool     $xpath    An optional xpath to search for the fields.
	 *
	 * @return  mixed  JForm object on success, False on error.
	 *
	 * @see     JForm
	 * @since	1.2.0
	 */
	static protected function _loadForm($name, $source = null, $options = array(), $clear = false, $xpath = false)
	{
		// Handle the optional arguments.
		$options['control'] = ArrayHelper::getValue($options, 'control', false);

		// Get the form.
		JForm::addFormPath(JPATH_PLUGINS.'/bwpostman/bwtimecontrol/forms');

		try
		{
			$form = JForm::getInstance($name, $source, $options, false, $xpath);

			if (isset($options['load_data']) && $options['load_data'])
			{
				// Get the data for the form.
				$data = self::getItem();
			}
			else
			{
				$data = array();
			}

			// Load the data into the form after the plugins have operated.
			$form->bind($data);

		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage('Error loading tc_campaign-Form', 'error');
			JError::raiseError(500, $e->getMessage()); //@todo?
			return false;
		}
		return $form;
	}

	/**
	 * Method to get selectlist for dates
	 *
	 * @access	public
	 *
	 * @param 	string		$date		sort of date --> day, hour, minute
	 * @param 	int			$length		length of list array
	 * @param 	array       $selectval  selectval selected values
	 * @param	int			$intval		interval value
	 *
	 * @return 	string				selectlist
	 *
	 * @since	1.2.0
	 */
	static public function getDateList($date = 'minute', $length = 10, $selectval, $intval)
	{
		$options	= array();
		$selectlist	= array();

		switch ($date) {
			case 'day':		for ($i = 0; $i <= $intval; $i++) {
								$options[] = $i;
							}
							break;

			case 'hour':	for ($i = 0; $i < 24; $i++) {
								$options[] = $i;
							}
							break;

			case 'minute':	for ($i = 0; $i < 60; $i += $intval) {
								$options[] = $i;
							}
							break;
		}
		$optMax = count($options);

		foreach ($selectval->$date as $key => $value) {
			$opt		= "automailing_values[" . $date . "][".$key."]";
			if ($value != '0') {
				$selected	= $value;
			}
			else {
				$selected	= 0;
			}

			$select_html		= '<select id="' . $opt . '" name="automailing_values['.$date.'][]" >';
			foreach ($options as $key2 => $value2) {

				$select_html		.= '<option value="' . $key2*$intval . '"';
				if ($selected == $key2*$intval) $select_html		.= ' selected="selected"';
				$select_html		.= '>' . $value2 . '</option>';
			}
			$select_html		.= '</select>';
			$selectlist[]	= $select_html;
		}

//dump ($selectlist, 'Helper Selectlist');

		return $selectlist;
	}

	/**
	 * Method to get selectlists for campaign letters
	 *
	 * @access	public
	 *
	 * @param 	array				campaign letters
	 * @param 	array of objects	selected values
	 *
	 * @return 	string				selectlist
	 *
	 * @since	1.2.0
	 */
	static public function getNlSelectlists($newsletters, $selectval)
	{
		$options	= array();
		$selectlist	= array();

		$no_select	= array('nl_id' => 0, 'title' => "- " . JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_NOSELECT') . " -");

		array_unshift($newsletters, $no_select);

		foreach ($selectval as $key => $value) {
			$attribs	= 'class="inputbox" size="1"';
			$opt		= "automailing_values[nl_id][".$key."]";
			if ($value != '0') {
				$selected	= $value;
			}
			else {
				$selected	= 0;
			}

			$select_html		= '<select id="' . $opt . '" name="automailing_values[nl_id][]" >';
			foreach ($newsletters as $newsletter) {
				$select_html		.= '<option value="' . $newsletter['nl_id'] . '"';
				if ($selected == $newsletter['nl_id'] && $selected != 0) $select_html		.= ' selected="selected"';
				$select_html		.= '>' . $newsletter['title'] . '</option>';
			}
			$select_html		.= '</select>';
			$selectlist[]	= $select_html;
		}
		return $selectlist;
	}

	/**
	 * Method to build HTML for the autovalues tab
	 *
	 * @access	public
	 *
	 * @param 	object	$item       autocampaign data
	 * @param 	object	$document   document
	 * @param 	object	$params     params
	 *
	 * @return 	string	HTML code for tab autovalues
	 *
	 * @since	1.2.0
	 */
	static public function buildAutovaluesTab($item, &$document, $params)
	{
		// Get form and item and initialize
		$form		= BwPostmanCampaignHelper::getForm($item->tc_id);
		$intval		= $params->get('bwtimecontrol_minute_intval');
		$daysmax	= $params->get('bwtimecontrol_days_max');
		$selectval  = new stdClass();

		$am_max_val = 0;

		// Get automailing data from table
		// Check for automailing and count campaign letters
		if ($item->automailing) {
			$selectval	= $item->automailing_values;
			$am_max_val	= count ($item->automailing_values->nl_id);
			$hide_style	= '';
		}
		else {
			$selectval->day[0]		= 0;
			$selectval->hour[0]		= 0;
			$selectval->minute[0]	= 0;
			$selectval->nl_id[0]	= 0;
			$hide_style 			= "hidden_content";
		}

		// build select lists for campaign letters
		// @todo assemble 'building newsletters select list' in one helper function! Here only the list is needed!
		$newsletters	= BwPostmanCampaignHelper::getAllCampaignLetters($item->campaign_id);
		$nl_select		= BwPostmanCampaignHelper::getNlSelectlists($newsletters, $selectval->nl_id);
		$nl_list		= array();
		$nl_item		= array();

		foreach ($newsletters as $newsletter) {
			$nl_item['nl_id']	= $newsletter['nl_id'];
			$title				= $newsletter['title'];
			// escape/remove some characters to get valid JSON-possible string. This will not change the real used newsletter subject but only the values shown in newsletter select list
			$title				= str_replace("\\", "\\\\", $title);
			$title				= str_replace("'", "", $title);
			$title				= str_replace('"', '\\"', $title);
			$nl_item['title']	= $title;

			$nl_list[]		= $nl_item;
		}
		$nl_item['nl_id']		= 0;
		$nl_item['title']		= "- " . JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_NOSELECT') . " -";

		array_unshift($nl_list, $nl_item);

		JFactory::getApplication()->setUserState('bwtimecontrol.nbr_campaign_letters', count($newsletters));

		// build select lists for date
		$day_select		= BwPostmanCampaignHelper::getDateList('day', $am_max_val+1, $selectval, $daysmax);
		$hour_select	= BwPostmanCampaignHelper::getDateList('hour', $am_max_val+1, $selectval, 1);
		$minute_select	= BwPostmanCampaignHelper::getDateList('minute', $am_max_val+1, $selectval, $intval);
		$m_max			= count($day_select);

		// set error object for JS
		$err_obj	= new stdClass();
		$err_obj->TC1	= JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_JS_ERROR_CAM_TIMECHECK1');
		$err_obj->TC2	= JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_JS_ERROR_CAM_TIMECHECK2');
		$err_obj->TC3	= JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_JS_ERROR_CAM_TIMECHECK3');
		$err_obj->TC4	= JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_JS_ERROR_CAM_TIMECHECK4');
		$err_obj->TC5	= JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_JS_ERROR_CAM_TIMECHECK5');
		$err_obj->TC6	= JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_JS_ERROR_CAM_TIMECHECK6');
		$err_obj->TC7	= JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_JS_ERROR_CAM_TIMECHECK7');

		$script1	= "var nllist	= '" . json_encode($nl_list) . "';";
		$script1	.= "var err_obj	= '" . json_encode($err_obj) . "';";

		// set additional JS and CSS
		$document->addScriptDeclaration($script1);
		$document->addScript(JUri::root() . 'plugins/bwpostman/bwtimecontrol/assets/js/bwtimecontrol.js');
		$document->addStyleDeclaration('.hidden_content {display: none;}');

		// Set additional HTML for automailing values
		$content	 = '<fieldset class="adminform">';
		$content	.= '<legend>' . JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_AUTOVALUES_TITLE') . '</legend>';
		$content	.= JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_CAM_CHANGE_NOTE');

		$content	.= '<div class="control-group">';
		$content	.= '<ul class="adminformlist unstyled">';
		foreach($form->getFieldset('basic') as $field) {
			if ($field->hidden) {
				$content	.= '<li>' . $field->input . '</li>';
			}
			else {
				$content	.= '<li class="' . $field->name  . '"><div class="control-group">' . $field->label;
				$content	.= '<div class="controls">' . $field->input . '</div></div></li>';
			}
		}
		foreach($form->getFieldset('values') as $field) {
			if ($field->hidden) {
				$content	.= '<li>' . $field->input . '</li>';
			}
			else {
				$content	.= '<li class="' . $field->name  . ' ' . $hide_style . '" name="hidden_content"><div class="control-group">' . $field->label;
				$content	.= '<div class="controls">' . $field->input . '</div></div></li>';
			}
		}
		$content	.= '</ul>';
		$content	.= '</div>';
		$content	.=	'<table id="values" name="hidden_content">';
		$content	.= '<tr class="bwptable ' . $hide_style . '" name="hidden_content">';
		$content	.= '<th width="200" align="right" class="key">' . '<span class="bwplabel">' . JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_CAM_AM_MAIL_NR_HEAD') . '</span>' . '</th>';
		$content	.= '<th class="key">' . '<span class="bwplabel">' . JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_CAM_AM_MAIL_AFTER_DAYS') . '</span>' . '</th>';
		$content	.= '<th class="key">' . '<span class="bwplabel">' . JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_CAM_AM_MAIL_AFTER_HOURS') . '</span>' . '</th>';
		$content	.= '<th class="key">' . '<span class="bwplabel">' . JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_CAM_AM_MAIL_AFTER_MINUTES') . '</span>' . '</th>';
		$content	.= '<th class="key">' . '<span class="bwplabel">' . JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_CAM_AM_MAIL_AFTER_NL') . '</span>' . '</th>';
		$content	.= '<th class="key">&nbsp;</th>';
		$content	.= '</tr>';

		for ($m = 0; $m < $m_max; $m++) {
			$content	.= '<tr class="bwptable ' . $hide_style . '" name="hidden_content">';
			$content	.= '<td width="200" align="right" class="key">' . '<span class="bwplabel">' . JText::sprintf('PLG_BWPOSTMAN_BWTIMECONTROL_CAM_AM_MAIL_NR', ($m+1)) . '</span>' . '</td>';
			$content	.= '<td>' . $day_select[$m] . '</td>';
			$content	.= '<td>' . $hour_select[$m] . '</td>';
			$content	.= '<td>' . $minute_select[$m] .= '</td>';
			$content	.= '<td>' . $nl_select[$m] . '</td>';
			$content	.= '<td>' . '<input type="button" class="btn btn-small btn-danger" value="' . JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_CAM_AM_REMOVE_NL') . '" onclick="rowDelete(' . $m . ')">' . '</td>';
			$content	.= '</tr>';
		}
		$content	.= '<tr class="bwptable ' . $hide_style . '" name="hidden_content">';
		$content	.= '<td colspan="5">' . '</td>';
		$content	.= '<td>' . '<input type="button" class="btn btn-small btn-success" value="' . JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_CAM_AM_ADD_NL');
		$content	.= '" onclick="rowInsert(' . $intval . ", '" . JText::_('JAPPLY') . "'" . ')">';
		$content	.= '</td>';
		$content	.= '</tr>';
		$content	.= '</table>';
		$content	.= '</fieldset>';
		$content	.= '<p class="' . $hide_style . '" name="hidden_content"><span class="required_description">' . JText::_('COM_BWPOSTMAN_REQUIRED') . '</span></p>';

		return $content;
	}

	/**
	 * Method to build HTML for the autovalues tab
	 *
	 * @access	public
	 *
	 * @param 	int     $cam_id 	autocampaign data
	 *
	 * @return 	string	HTML code for tab autovalues
	 *
	 * @since	1.2.0
	 */
	static public function buildAutoqueueTab($cam_id)
	{
		$content        = '';
		$request_url	= JUri::base();
		JFactory::getApplication()->setUserState('bwtimecontrol.campaign_edit.request', $request_url);
//dump ($request_url->getQuery(), 'QueueTab Request URL');

		$autoletters	= self::getAutoletters($cam_id);

		//Show tabs with sent and unsent newsletters if we edit a campaign
		if (!empty($autoletters->queued)) {
			$content	.= '<table class="adminlist" width="100%">';
			$content	.= '<thead>';
			$content	.= '<tr>';
			$content	.= '<th>' . JText::_('NUM') . '</th>';
			$content	.= '<th>' . JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_MAIL_NUMBER') . '</th>';
			$content	.= '<th>' . JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_MAIL_SUSPENDED') . '</th>';
			$content	.= '<th align="left">' . JText::_('SUBJECT') . '</th>';
			$content	.= '<th width="150">' . JText::_('COM_BWPOSTMAN_NL_MAILING_DATE') . '</th>';
			$content	.= '<th width="250">' . JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_AUTOQUEUE_RECIPIENT') . '</th>';
			$content	.= '</tr>';
			$content	.= '</thead>';
			$content	.= '<tbody>';

			$k = 0;

			for ($i=0, $n=count($autoletters->queued); $i < $n; $i++)
			{
				$item		= &$autoletters->queued[$i];
				$link_html	= 'index.php?option=com_bwpostman&amp;view=newsletter&amp;format=raw&amp;layout=newsletter_html_modal&amp;task=insideModal&amp;nl_id='. $item->id;
				$link_text	= 'index.php?option=com_bwpostman&amp;view=newsletter&amp;format=raw&amp;layout=newsletter_text_modal&amp;task=insideModal&amp;nl_id='. $item->id;

				$checked	= '';
				if ($item->suspended) {
					$checked	= 'checked="checked" ';
				}

				$content	.= '<tr class="item' . $k . '">';
				$content	.= '<td align="center">' . $item->id . '</td>';
				$content	.= '<td align="center">' . $item->mail_number . '</td>';
				$content	.= '<td align="center"><a href="' . JRoute::_('index.php?option=com_bwpostman&view=campaign&task=campaign.suspendNewletterFromSending&id='. $item->id . '&suspended=' . $item->suspended) . '"><input type="radio" name="suspended" ' . $checked . ' value="' . $item->suspended . '" /></a></td>';
				$content	.= '<td>' . $item->subject . '&nbsp;&nbsp; ';
				$content	.= '<span class="cam_preview">';
				$content	.= '<span class="editlinktip hasTip" title="';
				$content	.= JText::_('COM_BWPOSTMAN_NL_SHOW_HTML');
				$content	.= '::';
				$content	.= $item->subject;
				$content	.= '">';
				$content	.= '<a class="popup" href="'.$link_html.'" rel="{handler: \'iframe\', size: {x: 600, y: 450}}">'.JText::_('COM_BWPOSTMAN_HTML_NL').'</a>&nbsp;';
				$content	.= '</span>';
				$content	.= '<span class="editlinktip hasTip" title="';
				$content	.= JText::_('COM_BWPOSTMAN_NL_SHOW_TEXT');
				$content	.= '::';
				$content	.= $item->subject;
				$content	.= '">';
				$content	.= '<a class="popup" href="'.$link_text.'" rel="{handler: \'iframe\', size: {x: 600, y: 450}}">'.JText::_('COM_BWPOSTMAN_HTML_NL').'</a>&nbsp;';
				$content	.= '</span>';
				$content	.= '</span>';
				$content	.= '</td>';
				$content	.= '<td align="center">' . JHtml::date($item->sending_planned, JText::_('BW_DATE_FORMAT_LC5')) . '</td>';
				$content	.= '<td align="center">' . $item->email . '</td>';
				$content	.= '</td>';
				$content	.= '</tr>';

				$k = 1 - $k;
			}

			$content	.= '</tbody>';
			$content	.= '</table>';
		}
		else {
			$content = JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_AUTOQUEUE_NO_ENTRY');
		}
		//End: Show no tabs if there is no newsletter assigned
		return $content;
	}


	/**
	 * Method to handle changes to newsletter that eventually belongs to a timecontrolled campaign
	 *
	 * @access 	public
	 *
	 * @param 	int		$id             queue_id
	 * @param 	int		$suspended      suspended
	 *
	 * @return mixed
	 *
	 * @since	1.2.0
	 */
	static public function suspendNewsletterFromSending ($id, $suspended)
	{
		if ($suspended == 0) {
			$suspended = 1;
		}
		else {
			$suspended = 0;
		}

		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->update($_db->quoteName('#__bwpostman_tc_sendmailqueue'));
		$query->set($_db->quoteName('suspended') . " = " . $_db->quote($suspended));
		$query->where($_db->quoteName('id') . ' = ' . $_db->quote($id));

		$_db->setQuery($query);

		return $_db->execute();
	}

	/**
	 * Method to de-active a tc-campaign
	 *
	 * @access 	public
	 *
	 * @param 	int		$campaign_id        campaign ID
	 *
	 * @since	1.2.0
	 */
	private function _deactiveCampaign ($campaign_id)
	{
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);
		$tc_id	= self::getTcIdFromCampaign($campaign_id);

		$query->update($_db->quoteName('#__bwpostman_tc_campaign'));
		$query->set($_db->quoteName('active') . " = " . (int) 0);
		$query->where($_db->quoteName('tc_id') . ' = ' . $_db->quote($tc_id));

		$_db->setQuery($query);

		return $_db->execute();
	}

	/**
	 * Method to handle changes to newsletter that eventually belongs to a timecontrolled campaign
	 *
	 * @access 	public
	 *
	 * @since	1.2.0
	 */
	static public function processChangesOfNewsletterEdit ()
	{
		// Get data form current editing state
		$app			= JFactory::getApplication();
		$nl_data_new	= $app->getUserState('bwtimecontrol.newsletter.new_data', null);

		if ($nl_data_new === null) {
			// NL edited, but changes not saved
			return;
		}
		$campaign_new	= $nl_data_new['campaign_id'];

		// Get date from state at beginning of editing
		$nl_data_old	= $app->getUserState('bwtimecontrol.newsletter.old_data', null);

		if ($nl_data_old === null) {
			// NL copied, no old state data
			$campaign_old	= 0;
			$isCopy			= true;
		}
		else {
			$campaign_old	= $nl_data_old->campaign_id;
			$isCopy			= false;
		}

		// Get automated campaigns IDs
		$tc_id_new	= BwPostmanCampaignHelper::getTcIdFromCampaign($campaign_new);
		$tc_id_old	= BwPostmanCampaignHelper::getTcIdFromCampaign($campaign_old);

		//if none of the states have a timecontrolled campaign do nothing
		if ($tc_id_new === null && $tc_id_old === null) {
			return;
		}

		// check for changes to campaign
		if ($campaign_new != $campaign_old) {
			// If campaign has changed, process these changes
			if ($tc_id_old === null) {
				// NL comes new to campaign
				self::_addNewsletterToCampaign($campaign_new, $nl_data_new['id']);
			}
			else {
				// campaign has changed
				if ($tc_id_new != $tc_id_old) {
					if ($tc_id_new === null) {
						self::_removeNewsletterFromCampaign($campaign_old, $nl_data_old->id);
					}
					else {
						// newsletter moved from one timecontrolled campaign to another
						self::_removeNewsletterFromCampaign($campaign_old, $nl_data_old->id);
						self::_addNewsletterToCampaign($campaign_new, $nl_data_new['id']);
					}
				}
			}
		}
		$app->setUserState('bwtimecontrol.newsletter.new_data', null);
	}

	/**
	 * Method to add a newsletter to a timecontrolled campaign
	 *
	 * @access 	private
	 *
	 * @param 	int		$cam_id 	campaign id
	 * @param 	int		$nl_id  	newsletter id
	 *
	 * @since	1.2.0
	 */
	private static function _addNewsletterToCampaign ($cam_id, $nl_id)
	{
		// Get automailing values from TC-Data
		$tc_data		= self::getItem($cam_id);
		$auto_values	= json_decode($tc_data->automailing_values);
		$controller		= JControllerLegacy::getInstance('BwPostman');

		array_push($auto_values->day, (string) 0);
		array_push($auto_values->hour, (string) 0);
		array_push($auto_values->minute, (string) 0);
		array_push($auto_values->nl_id, (string) $nl_id);

		$tc_data->automailing_values	= json_encode($auto_values);

		JFactory::getApplication()->setUserState('bwtimecontrol.cam_data.nl_edit', $tc_data);
		$controller->setRedirect('index.php?option=com_bwpostman&task=campaign.edit&id=' . $cam_id);
	}

	/**
	 * Method to remove a newsletter from a timecontrolled campaign
	 *
	 * @access 	private
	 *
	 * @param 	int		$cam_id 	campaign id
	 * @param 	int		$nl_id  	newsletter id
	 *
	 * @return bool     true on success
	 *
	 * @since	1.2.0
	 */
	private static function _removeNewsletterFromCampaign ($cam_id, $nl_id)
	{
		$app	= JFactory::getApplication();

		// Get row index of newsletter in automailing values
		$tc_data		= self::getItem($cam_id);
		$auto_values	= json_decode($tc_data->automailing_values);
		$controller		= JControllerLegacy::getInstance('BwPostman');
		$row			= array_search($nl_id, $auto_values->nl_id);

		if ($row === false) {
			$app->enqueueMessage(JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_ERROR_REMOVE_NL_FROM_CAMPAIGN'), 'error');
			return false;
		}
		unset ($auto_values->day[$row]);
		unset ($auto_values->hour[$row]);
		unset ($auto_values->minute[$row]);
		unset ($auto_values->nl_id[$row]);
//dump ($auto_values, 'decoded automailing Values');

		$tc_data->automailing_values	= json_encode($auto_values);

		$app->setUserState('bwtimecontrol.cam_data.nl_edit', $tc_data);
		$app->setUserState('bwtimecontrol.cam_data.nl_referrer', 'remove');
		$app->setUserState('bwtimecontrol.newsletter.save.returnlink', 'index.php?option=com_bwpostman&view=newsletters');
		$controller->setRedirect('index.php?option=com_bwpostman&task=campaign.edit&id=' . $cam_id);

		// @todo set old flag in tc_sendmailcontent

		// @todo remove newsletter form tc_sendmailqueue

		return true;
	}


	/**
	 * Method to get all campaigns with automation
	 *
	 * @access 	public
	 *
	 * @return 	array campaign IDs
	 *
	 * @since	1.2.0 *
	 */
	public function getAllAutocampaigns ()
	{
		$automailing	= array();

		$lists_id		= self::_getAllMailinglistsIds();

		foreach ($lists_id as $list_id) {
			$campaign_id	= self::_getCampaignIdFromLists($list_id);
			if ($campaign_id > 0) {
				$result	= self::_getAutoMailingFromCampaign($campaign_id);
				if ($result == 1) $automailing[] = $campaign_id;
			}
		}

		return $automailing;
	}

	/**
	 * Method to get all newsletters assigned to given campaign
	 *
	 * @access 	public
	 *
	 * @param	int		$campaign_id        campaign id
	 *
	 * @return 	array	newsletters of this campaign
	 *
	 * @since	1.2.0 *
	 */
	static public function getAllCampaignLetters ($campaign_id)
	{
		$_db			= JFactory::getDbo();
		$query		= $_db->getQuery(true);

		$query->select($_db->quoteName('id')  . ' AS ' . $_db->quoteName('nl_id'));
		$query->select($_db->quoteName('subject')  . ' AS ' . $_db->quoteName('title'));
		$query->from($_db->quoteName('#__bwpostman_newsletters'));
		$query->where($_db->quoteName('campaign_id') . ' = ' . (int) $campaign_id);

		$_db->setQuery($query);
		$newsletters	= $_db->loadAssocList();

		return $newsletters;
	}

	/**
	 * Method to get the subscribed campaigns with automation
	 *
	 * @access 	public
	 *
	 * @param 	int $id     subscriber ID
	 *
	 * @return 	array campaign IDs
	 *
	 * @since	1.2.0 *
	 */
	public function getAutomailings ($id)
	{
		$automailing	= array();

		$lists_id		= self::_getMailinglistsIds($id);

		foreach ($lists_id as $list_id) {
			$campaign_id	= self::_getCampaignIdFromLists($list_id);
			if ($campaign_id > 0) {
				$result	= self::_getAutoMailingFromCampaign($campaign_id);
				if ($result == 1) $automailing[] = $campaign_id;
			}
		}
		return $automailing;
	}

	/**
	 * Make partial send. Send only, say like 50 newsletters and the next 50 in a next call.
	 *
	 * @access 	public
	 *
	 * @param int   $mailsPerStep
	 *
	 * @return int	0 -> queue is empty, >0 -> sent
	 *
	 * @since	1.2.0
	 */
	static protected function _sendMailsFromQueue($mailsPerStep = 100)
	{
		$controller	= JControllerLegacy::getInstance('BwPostman');
		$model		= $controller->getModel('newsletter');

		$sendMailCounter	= 0;

		while(1){
			$ret = $model->sendMail(false);
			if ($ret == 0){                              // Queue is empty!
				return $sendMailCounter;
				break;
			}
			$sendMailCounter++;
			if ($sendMailCounter >= $mailsPerStep) {     // Maximum is reached.
				return $sendMailCounter;
				break;
			}
		}
	}



	/**
	 * Method to get the existing entries for a specific subscriber and campaign
	 *
	 * @access 	public
	 *
	 * @param 	int		$id             subscriber ID
	 * @param 	int		$campaign_id    campaign ID
	 * @param 	string	$email      	email address of subscriber
	 *
	 * @return 	boolean	rows affected
	 *
	 * @since	1.2.0
	 */
	public function getSubsQueueEntries ($id = 0, $campaign_id = 0, $email = '')
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__bwpostman_tc_sendmailqueue'));
		$query->where($db->quoteName('subscriber_id') . ' = ' . (int) $id);
		$query->where($db->quoteName('campaign_id') . ' = ' . (int) $campaign_id);
		$db->setQuery($query);

		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());
		}

		$result = $db->getAffectedRows();

		if ($result > 0) {
			$rows = self::_changeMailAddress($id, $email);
			if ($rows == true) {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}

	}

	/**
	 * Method to change email address
	 *
	 * @access 	private
	 *
	 * @param 	int	    	$id
	 * @param 	string		$email
	 *
	 * @return 	int lists ID
	 *
	 * @since	1.2.0 *
	 */
	private function _changeMailAddress ($id = 0, $email = 'hugo')
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->update($db->quoteName('#__bwpostman_tc_sendmailqueue'));
		$query->set($db->quoteName('email') . ' = ' . $db->quote($email));
		$query->where($db->quoteName('subscriber_id') . ' = ' . (int) $id);
		$db->setQuery($query);

		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 * Method to get all mailinglist IDs
	 *
	 * @access 	private
	 *
	 * @return 	array   lists ID
	 *
	 * @since	1.2.0 *
	 */
	private function _getAllMailinglistsIds ()
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__bwpostman_mailinglists'));
		$db->setQuery($query);

		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());
		}

		$lists_id = $db->loadResultArray();

		return $lists_id;
	}

	/**
	 * Method to get the mailinglist IDs of single subscriber
	 *
	 * @access 	private
	 *
	 * @param 	int $id     subscriber ID
	 *
	 * @return 	int lists ID
	 *
	 * @since	1.2.0 *
	 */
	private function _getMailinglistsIds ($id)
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('list_id'));
		$query->from($db->quoteName('#__bwpostman_subscribers_mailinglists'));
		$query->where($db->quoteName('subscriber_id') . ' = ' . (int) $id);
		$db->setQuery($query);

		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());
		}

		$lists_id = $db->loadResultArray();

		return $lists_id;
	}

	/**
	 * Method to get the campaign ID form lists-table
	 *
	 * @access 	private
	 *
	 * @param 	int $id     lists ID
	 *
	 * @return 	int campaign ID
	 *
	 * @since	1.2.0 *
	 */
	private function _getCampaignIdFromLists ($id)
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('campaign_id'));
		$query->from($db->quoteName('#__bwpostman_mailinglists'));
		$query->where($db->quoteName('id') . ' = ' . (int) $id);
		$db->setQuery($query);

		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());
		}

		$campaign_id = $db->loadResult();

		return $campaign_id;
	}

	/**
	 * Method to get the automailing state form campaign-table
	 *
	 * @access 	private
	 *
	 * @param 	int $id     campaign ID
	 *
	 * @return 	int automailing state
	 *
	 * @since	1.2.0 *
	 */
	private function _getAutoMailingFromCampaign ($id)
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('automailing'));
		$query->from($db->quoteName('#__bwpostman_tc_campaign'));
		$query->where($db->quoteName('tc_id') . ' = ' . (int) $id);
		$db->setQuery($query);

		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());
		}
		$autostate	= $db->loadResult();

		return $autostate;
	}

	/**
	 * Method to delete pending mails from tc-queue
	 *
	 * @access 	public
	 *
	 * @param 	int $sid        subscriber ID
	 * @param 	int $cid        campaign ID
	 *
	 * @return 	int affected rows
	 *
	 * @since	1.2.0 *
	 */
	public function deletePendingMailsFromQueue ($sid = 0, $cid = 0)
	{
		if ($sid > 0) {
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);

			$query->delete($db->quoteName('#__bwpostman_tc_sendmailqueue'));
			$query->where($db->quoteName('subscriber_id') . ' = ' . $db->quote($sid));
			$query->where($db->quoteName('sent_time') . ' = ' . $db->quote('0000-00-00 00:00:00'));
			if ($cid != 0) $query->where($db->quoteName('campaign_id') . ' = ' . $db->quote($cid));

			$db->setQuery($query);

			if (!$db->query()) {
				JError::raiseError(500, $db->getErrorMsg());
			}
			return $db->getAffectedRows();
		}
		return false;
	}

	/**
	 * Method to get all assigned newsletters, queued sendings and done sendings
	 *
	 * @access 	public
	 *
	 * @param 	int $cam_id     campaign ID
	 *
	 * @return 	object Autoletters
	 *
	 * @since	1.2.0
	 */
	public static function getAutoletters($cam_id = 0)
	{
		$db = JFactory::getDbo();
		$autoletters	= new stdClass();

		$query = $db->getQuery (true);
		$query->select('a.*');
		$query->select(' v.' . $db->quoteName('name') . ' AS author');
		$query->from($db->quoteName('#__bwpostman_newsletters') . ' AS a');
		$query->leftJoin($db->quoteName('#__users') . ' AS v ON v.' . $db->quoteName('id') . ' = a.' . $db->quoteName('created_by'));
		$query->where($db->quoteName('campaign_id') . ' = ' . (int) $cam_id);
		$query->where($db->quoteName('archive_flag') . ' = ' . (int) 0);
		$db->setQuery($query);
		$autoletters->assigned = $db->loadObjectList();

		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->select(' c.' . $db->quoteName('subject') . ' AS subject');
		$query->from($db->quoteName('#__bwpostman_tc_sendmailqueue') . ' AS a');
		$query->leftJoin($db->quoteName('#__bwpostman_tc_sendmailcontent') . ' AS c ON c.' . $db->quoteName('id') . ' = a.' . $db->quoteName('tc_content_id'));
		$query->where('a.' . $db->quoteName('campaign_id') . ' = ' . (int) $cam_id);
		$query->where($db->quoteName('sent_time') . ' = ' . $db->quote('0000-00-00 00:00:00'));
		$query->order($db->quoteName('sending_planned') . ' ASC ');
		$db->setQuery($query);
		$autoletters->queued = $db->loadObjectList();

		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->select(' c.' . $db->quoteName('subject') . ' AS subject');
		$query->from($db->quoteName('#__bwpostman_tc_sendmailqueue') . ' AS a');
		$query->leftJoin($db->quoteName('#__bwpostman_tc_sendmailcontent') . ' AS c ON c.' . $db->quoteName('id') . ' = a.' . $db->quoteName('tc_content_id'));
		$query->where('a.' . $db->quoteName('campaign_id') . ' = ' . (int) $cam_id);
		$query->where($db->quoteName('sent_time') . ' != ' . $db->quote('0000-00-00 00:00:00'));
		$query->order($db->quoteName('sent_time') . ' DESC ');
		$db->setQuery($query);
		$autoletters->sent_queue = $db->loadObjectList();

		return $autoletters;

	}

	/**
	 * Method for a new entry in tc_content to hold main function private
	 *
	 * @access	public
	 *
	 * @return	boolean	True on success
	 *
	 * @since	1.2.0
	 */
	public static function newTcContent($nl_data) {

		return self::_addTcSendMailContent($nl_data);
	}

	/**
	 * Method to check for different array values (array_diff don't does what I want)
	 *
	 * @access	public
	 *
	 * @param 	array	$a
	 * @param 	array	$b
	 *
	 * @return	array
	 *
	 * @since	1.2.0
	 */
	static public function arr_diff($a, $b) {
		$diff	= array();

		if (count($a) >= count($b)) {
			foreach ($a as $key => $value) {
				if (isset($b[$key])) {
					if ($value != $b[$key]) $diff[$key] = $b[$key];
				}
				else {
					$diff[$key] = $value;
				}
			}
		}
		else {
			foreach ($b as $key => $value) {
				if (isset($a[$key])) {
					if ($value != $a[$key]) $diff[$key] = $a[$key];
				}
				else {
					$diff[$key] = $value;
				}
			}
		}
		return $diff;
	}

	/**
	 * Method to get all complete and not sent mails of a certain campaign
	 *
	 * @access	public
	 *
	 * @param 	int	$c_id       campaign ID
	 *
	 * @return	array of objects
	 *
	 * @since	1.2.0 *
	 */
	static public function getCompleteDueMails($c_id) {

		$db = JFactory::getDbo();

		$query1 = $db->getQuery(true);
		$query1->select('COUNT(*)');
		$query1->from($db->quoteName('#__bwpostman_newsletters'));
		$query1->where($db->quoteName('campaign_id') . ' = ' . $db->quote((int) $c_id));
		$db->setQuery($query1);

		$query2 = $db->getQuery(true);
		$query2->select($db->quoteName('subscriber_id'));
		$query2->from($db->quoteName('#__bwpostman_tc_sendmailqueue'));
		$query2->where($db->quoteName('sent_time') . ' = ' . $db->quote('0000-00-00 00:00:00'));
		$query2->where($db->quoteName('campaign_id') . ' = ' . $db->quote((int) $c_id));
		$query2->group($db->quoteName('created'));
		$query2->group($db->quoteName('subscriber_id'));
		$query2->having('COUNT(`created`) >= (' . $query1 . ')');
		$db->setQuery($query2);
//dump ($db->loadAssocList(), 'Ergebnis Query 2');
//dump (str_replace('#__','jos_',$query2), 'Query 2');

		$query3 = $db->getQuery(true);
		//		$query3->select('*'); // Wenen alle Daten benötigt werden
		//		distinct(`subscriber_id`), `created`, `campaign_id` // Wenn nur diese Daten DISTINCT benötigt werden (macht diesen Teil der Abfrage fast überflüssig...)
		$query3->select('DISTINCT (' . $db->quoteName('subscriber_id') . ')');
		$query3->select($db->quoteName('created'));
		$query3->select($db->quoteName('campaign_id'));
		$query3->from($db->quoteName('#__bwpostman_tc_sendmailqueue'));
		$query3->where($db->quoteName('subscriber_id') . ' IN (' . $query2 . ')');
		$query3->where($db->quoteName('campaign_id') . ' = ' . $db->quote((int) $c_id));
		$query3->where($db->quoteName('sent_time') . ' = ' . $db->quote('0000-00-00 00:00:00'));
		$query3->where($db->quoteName('campaign_id') . ' = ' . $db->quote((int) $c_id));
		$query3->order($db->quoteName('subscriber_id') . 'ASC');
		$query3->order($db->quoteName('created') . 'DESC');
		$query3->order($db->quoteName('mail_number') . 'ASC');

		$db->setQuery($query3);
//dump ($db->getQuery(), 'Query get');
//dump (str_replace('#__','jos_',$query3), 'Query 3');
		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());
		}

		$result = $db->loadAssocList();
//dump ($result, 'Result getCompleteDuedMails');

		return $result;
	}


	/**
	 * Method to delete complete sets
	 *
	 * @access	private
	 *
	 * @param 	array	$set_data       post data
	 *
	 * @return	boolean
	 *
	 * @since	1.2.0 *
	 */
	private static function deleteMailSetsFromQueue($set_data) {

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__bwpostman_tc_sendmailqueue'));
		$query->where($db->quoteName('created') . ' = ' . $db->quote($set_data['created']));
		$query->where($db->quoteName('subscriber_id') . ' = ' . $db->quote($set_data['subscriber_id']));
		$query->where($db->quoteName('campaign_id') . ' = ' . $db->quote($set_data['campaign_id']));
		$db->setQuery($query);

		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());
		}

	}

	/**
	 * Method to delete single mail from Content
	 *
	 * @access	private
	 *
	 * @param 	int	$mail_number        mail number
	 * @param 	int	$campaign_id        campaign id
	 *
	 * @return	boolean
	 *
	 * @since	1.2.0
	 */
	static private function deleteSingleMailFromContent($mail_number, $campaign_id) {

		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__bwpostman_tc_sendmailcontent'));
		$query->where($db->quoteName('mail_number') . ' = ' . $db->quote($mail_number + 1));
		$query->where($db->quoteName('campaign_id') . ' = ' . $db->quote($campaign_id));
		$db->setQuery($query);
//dump ($db->getQuery(), 'Query');

		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Method to process changes done while editing campaign
	 *
	 * @access	public
	 *
	 * @param 	array	$data       plugin data
	 *
	 * @return	boolean
	 *
	 * @since	1.2.0
	 */
	static public function processChanges($data)
	{
		// initialize variables
		$app		= JFactory::getApplication();

		$new_auto		= json_decode($data['automailing_values']);
		$old_cam_data	= $app->getUserState('bwtimecontrol.campaign.old_data', null);

		if (!is_object($old_cam_data->automailing_values)) {
			$old_auto	= json_decode($old_cam_data->automailing_values);
		}
		else {
			$old_auto	= $old_cam_data->automailing_values;
		}
dump ($old_cam_data, 'ProcessChanges Old Data 1');
//dump ($data, 'ProcessChanges New Data 1');
//dump ($old_auto, 'Old Auto');
//dump ($new_auto, 'New Auto');

		$diff_day		= self::arr_diff($old_auto->day, $new_auto->day);
		$diff_hour		= self::arr_diff($old_auto->hour, $new_auto->hour);
		$diff_minute	= self::arr_diff($old_auto->minute, $new_auto->minute);
		$diff_nl		= self::arr_diff($old_auto->nl_id, $new_auto->nl_id);

		$changes	= array();

		if (!empty ($diff_day))		$changes['automailing_values']['day']		= $diff_day;
		if (!empty ($diff_hour))	$changes['automailing_values']['hour']		= $diff_hour;
		if (!empty ($diff_minute))	$changes['automailing_values']['minute']	= $diff_minute;
		if (!empty ($diff_nl))		$changes['automailing_values']['nl_id']		= $diff_nl;		// @todo: Is this needed?
		($old_cam_data->chaining != $data['chaining']) ? $changes['chaining'] = 1 : $changes['chaining'] = 0;
//dump ($changes, 'ProcessChanges Check changes 1');

		if (isset($changes['automailing_values'])) {
			if (isset($changes['automailing_values']['nl_id'])) {
				// Mails have changed
//dumpMessage('Mails changed');
				if (count($old_auto->nl_id) > count($new_auto->nl_id)) {
					// mails removed from campaign, then remove them form table tc_sendmailcontent
					foreach ($diff_nl as $key=>$value) {
//dump ($key, 'Model Check Key');
						self::deleteSingleMailFromContent($key, $data['campaign_id']);
					}
				}
				if (count($old_auto->nl_id) < count($new_auto->nl_id)) {
					// mails new to campaign, then add them to table tc_sendmailcontent
					foreach ($diff_nl as $key=>$value) {
//dump ($key, 'Model Check Key');
						self::deleteSingleMailFromContent($key, $data['campaign_id']);
					}
				}
			}

			if (isset($changes['automailing_values']['day'])
			 || isset($changes['automailing_values']['hour'])
			 || isset($changes['automailing_values']['minute'])
			 || isset($changes['chaining'])) {

			//Timing has changed
//dumpMessage('Timing changed');
			// get complete sets from queue
			$complete_sets = self::getCompleteDueMails($data['campaign_id']);
//dump ($complete_sets, 'Komplettset');
			foreach ($complete_sets as $key) {
//dump ($key, 'Key');
				self::deleteMailSetsFromQueue($key);
				self::HandleQueue($key['campaign_id'], $key['subscriber_id'], $task = 'subscribe', $send_to_unconfirmed = 0);
				}
			}
		}

//dump ($changes, 'Model Check changes 3');
		return true;
	}

	/**
	 * Method to get ID of actual content (HTML and text entry) of a mail from TcContent table
	 *
	 * @access	private
	 *
	 * @param 	int 	$mail_id        mail id
	 * @param 	int 	$campaign_id    campaign id
	 *
	 * @return 	array	HTML- and text-ID
	 *
	 * @since	1.2.0 *
	 */
	static private function _getTcSingleContentId($mail_id, $campaign_id){

//dump ($mail_id, 'Mail ID');

		$ret	= array();
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->clear();
		$query->select($db->quoteName('id'));
		$query->select($db->quoteName('mode'));
		$query->from($db->quoteName('#__bwpostman_tc_sendmailcontent'));
		$query->where($db->quoteName('campaign_id') . ' = ' . $db->quote((int) $campaign_id));
		$query->where($db->quoteName('mail_number') . ' = ' . $db->quote((int) $mail_id));
		$query->where($db->quoteName('old') . ' = ' . $db->quote('0'));
		$db->setQuery($query);

		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());
		}

		$result = $db->loadAssocList();
//		echo $db->explain();
//dump ($db->getQuery(), 'Query fuer in GetTc C-ID');
dump ($result, 'Result Get TcSingleContent Content-IDs');

		foreach ($result as $key) {
			$ret[$key['mode']] = $key['id'];
		}

		return $ret;
	}


	/**
	 * Method to status of sending of actual content
	 *
	 * @access	private
	 *
	 * @param 	int 	$content_id     content ID
	 *
	 * @return 	bool	status for sent
	 *
	 * @since	1.2.0
	 */
	static private function _getTcSentStatus($content_id){


		$ret	= array();
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->clear();
		$query->select($db->quoteName('sent'));
		$query->from($db->quoteName('#__bwpostman_tc_sendmailcontent'));
		$query->where($db->quoteName('id') . ' = ' . $db->quote((int) $content_id));
		$db->setQuery($query);

		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());
		}

		$result = $db->loadResult();
//dump ($result, 'Result');

		return $result;
	}


	/**
	 * Method to store tc mail body (content for HTML and text mode)
	 *
	 * If a newsletter of an automailing campaign shall be sent, the mail will be separated into two
	 * parts to save storage:
	 * The content/body of the mail will be stored two times in a separate table, one time for HTML,
	 * the other time for text-mode with this function. With this the body is prepared for sending
	 * and only stored two times.
	 * The "rest" of the mail (recipient, sender, mode, ...) is variant for each mail and is stored
	 * with another function in table tc_sendmailqueue
	 *
	 * @access	private
	 *
	 * @param 	object	$nl_data        Newsletter data
	 *
	 * @return 	boolean true if everything went fine.
	 *
	 * @since	1.2.0
	 */
	static private function _addTcSendMailContent($nl_data)
	{
		if ($nl_data->campaign_id > 0) {
			$campaign_id	= $nl_data->campaign_id;
		}
		else {
			$campaign_id	= self::_id;
		}

		// Get the TcSendmailContent ID for HTLM- and text-version
		$tc_id = array();
		$tc_id = self::_getTcSingleContentId($nl_data->mail_id, $campaign_id);
		//new TcSendmailContent? Initialize ID
		if (!isset($tc_id[0])) {
			$tc_id[0]	= 0;
			$tc_id[1]	= 0;
//dump ($nl_data, 'Add Content NL-Data');
		}

dump ($tc_id, 'tc-ID');

		// Get Newsletter-Model
		$controller	= JControllerLegacy::getInstance('BwPostman');
		$nl_model	= $controller->getModel('newsletter');

		// Initialize the sendmailContent
		$tblTcSendmailContent = self::getTable('Tc_Sendmailcontent', 'BwPostmanTable');

		// Copy the data from newsletters to tcsendmailContent
		$tblTcSendmailContent->id 			= 0;
		$tblTcSendmailContent->nl_id 		= $nl_data->id;
		$tblTcSendmailContent->campaign_id	= $campaign_id;
		$tblTcSendmailContent->mail_number	= $nl_data->mail_id;
		$tblTcSendmailContent->old 			= 0;
		$tblTcSendmailContent->from_name 	= $nl_data->from_name;
		$tblTcSendmailContent->from_email 	= $nl_data->from_email;
		$tblTcSendmailContent->subject 		= $nl_data->subject;
		$tblTcSendmailContent->attachment	= $nl_data->attachment;
		$tblTcSendmailContent->cc_email 	= null;
		$tblTcSendmailContent->bcc_email 	= null;
		$tblTcSendmailContent->reply_email 	= $nl_data->reply_email;
		$tblTcSendmailContent->reply_name	= $nl_data->from_name;
		$html_version						= $nl_data->html_version;
		$text_version						= $nl_data->text_version;

		// Preprocess html and text version of the newsletter
		if (!BwPostmanHelper::replaceLinks($html_version)) return false;
		if (!BwPostmanHelper::replaceLinks($text_version)) return false;
		if (!$nl_model->_addHtmlTags($html_version)) return false;

		// We have to create two entries in the tcsendmailContent table. One entry for the text mail body and one for the html mail.
		for ($mode = 0;$mode <= 1; $mode++){

			// Set the body
			if ($mode == 0) {
				$tblTcSendmailContent->body = $text_version;
			}
			else{
				$tblTcSendmailContent->body = $html_version;
			}
			$tblTcSendmailContent->id 	= $tc_id[$mode];
			$tblTcSendmailContent->sent	= self::_getTcSentStatus($tc_id[$mode]);

			// Set the mode (0=text,1=html)
			$tblTcSendmailContent->mode = $mode;
//dump ($tblTcSendmailContent, 'Tabellenobjekt tc-Content 2');

			// Store the data into the tc_sendmailcontent-table
			// First run generates a new id, which will be used also for the second run.
			// Bind the data.
			if (!$tblTcSendmailContent->bind($tblTcSendmailContent))
			{
				JError::raiseError('Fehler beim Binden der tcContent-Daten', $tblTcSendmailContent->getError());
				return false;
			}

			// Check the data.
			if (!$tblTcSendmailContent->check())
			{
				JError::raiseError('Fehler beim Überprüfen der tcContent-Daten', $tblTcSendmailContent->getError());
				return false;
			}

			if (!$tblTcSendmailContent->store()) {
//dumpMessage ('tc-Content Store error');
				//				$tblTcSendmailContent->setError($error);
				JError::raiseError('Fehler beim Speichern der tcContent-Daten', $tblTcSendmailContent->getError());
				return false;
			}
		}
		// @todo: what ist this good for?
		$id = $tblTcSendmailContent->id;

		return true;
	}

	/**
	 * Method to prepare queuing campaign
	 *
	 * @access	public
	 *
	 * @param 	int 	$campaign_id            Campaign ID
	 * @param 	int 	$subscriber_id          subscriber ID, set if we come from single subscription
	 * @param 	string 	$task                   testsending --> either 0 = subscribers or 1 = test-recipients
	 * @param	int 	$send_to_unconfirmed    task to execute
	 *
	 * @return 	bool|array  False if there occurred an error
	 *
	 * @since	1.2.0
	 */
	static public function HandleQueue($campaign_id = 0, $subscriber_id = 0, $task = 'save', $send_to_unconfirmed = 0)
	{
//		self::setId($campaign_id);  // @todo: needed?

		$db				= JFactory::getDbo();
		$query			= $db->getQuery(true);
		$max_mails 		= JComponentHelper::getParams('com_bwpostman')->get('default_mails_per_pageload');
		$subscribers	= array();
		$ret			= array('', 0);
		$item			= JFactory::getApplication()->getUserState('bwtimecontrol.item', null);
dump ($item, 'HandleQueue Item');

		if ($item->active == 1) { // campaign is active
			switch ($task) {
				case 'test':		$subscribers	= self::_getTestrecipientsIDs();
dump ($subscribers, 'Test Subscribers');

									$ret[0]			= self::_fillTcSendMailQueue($campaign_id,  $subscribers, 1);
									$ret[1]			= self::_sendMailsFromQueue($max_mails);
					break;

				case 'subscribe':	$subscribers[0]['id']	= $subscriber_id;
									$ret[0]			= self::_fillTcSendMailQueue($campaign_id,  $subscribers, 0);
					break;

				case 'send':		$ret[1]			= self::_sendMailsFromQueue($max_mails);
					break;

				case 'deactivate':	$ret[0]			= self::_deactiveCampaign($campaign_id);
					break;

				case 'default':
				case 'save':		// @todo as appropriate revise sending_planned
				case 'activate':	// @todo as appropriate toggle state active
			}
		}
		else { // campaign is not active
			switch ($task) {
				case 'activate':	$subscribers	= self::_getSubscriberIDs($campaign_id);

									$query->update($db->quoteName('#__bwpostman_tc_campaign'));
									$query->set($db->quoteName('active') . ' = 1');
									$query->where($db->quoteName('tc_id') . ' = ' . (int) $campaign_id);
									$db->setQuery($query);

									if (!$db->query()) {
										JError::raiseError(500, $db->getErrorMsg());
										return FALSE;
									}
									$ret[0]			= self::_fillTcSendMailQueue($campaign_id,  $subscribers, 0);
									$ret[1]			= self::_sendMailsFromQueue($max_mails);
					break;

				case 'test':		$subscribers	= self::_getTestrecipientsIDs();
									$ret[0]			= self::_fillTcSendMailQueue($campaign_id,  $subscribers, 1);
									$ret[1]			= self::_sendMailsFromQueue($max_mails);
//dump ($subscribers, 'HandleQueue Test Recipients');
					break;

				case 'send':		$ret[1]			= self::_sendMailsFromQueue($max_mails);
					break;

				case 'save':		// fill TcSendMailContent
					break;

				case 'default':
				case 'subscribe':
				case 'deactivate':
					break;
			}
		}
//dump ($ret, 'HandleCampaign ret');
		return $ret;
	}



	/**
	 * Method to send due mails
	 *
	 *
	 * @access	public
	 *
	 * @return 	integer
	 *
	 * @since	1.2.0
	 */
	static public function sendDueNewsletters()
	{
		return self::_sendMailsFromQueue(JComponentHelper::getParams('com_bwpostman')->get('default_mails_per_pageload'));
	}


	/**
	 * Method to get due newsletters
	 *
	 * only necessary for testsending to have the list which entries we may delete form queue
	 *
	 * @access	private
	 *
	 * @return 	array|bool		due newsletters
	 *
	 * @since	1.2.0 *
	 */
	private function _getDueNewsletters()
	{

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->clear();
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__bwpostman_tc_sendmailqueue'));
		$query->where($db->quoteName('sending_planned') . ' < NOW()');
		$query->order($db->quoteName('sending_planned') . ' ASC');
		$db->setQuery($query);

		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());
			return FALSE;
		}
		return $db->loadColumn();
	}

	/**
	 * Method to get content-ids and mail_number form tc_sendMailQueue
	 *
	 * @access	private
	 *
	 * @param 	int		$campaign_id	Campaign ID
	 * @param 	int		$mode       	mode
	 *
	 * @return 	array|bool	content-id and mail-number
	 *
	 * @since	1.2.0 *
	 */
	static private function _getTcContentIDs($campaign_id = 0, $mode)
	{

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->clear();
		$query->select($db->quoteName('id'));
		$query->select($db->quoteName('mail_number'));
		$query->from($db->quoteName('#__bwpostman_tc_sendmailcontent'));
		$query->where($db->quoteName('campaign_id') . ' = ' . $db->quote((int) $campaign_id));
		$query->where($db->quoteName('mode') . ' = ' . $db->quote($mode));
		$query->where($db->quoteName('old') . ' = ' . $db->quote(0));
		$query->order($db->quoteName('mail_number') . ' ASC');
		$db->setQuery($query);

		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());
			return FALSE;
		}
		return $db->loadObjectList();

	}


	/**
	 * Method to get subscribers of a certain mailinglist
	 *
	 * @access	private
	 *
	 * @param 	int			$mailinglist_id     mailinglist ID
	 *
	 * @return 	array|bool
	 *
	 * @since	1.2.0 *
	 */
	static private function _getSubscriberIDs($mailinglist_id = 0)
	{
		if ($mailinglist_id == 0) {
			JError::raiseError(500, JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_FILL_QUEUE_NO_MAILINGLIST'));
		}
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('sl.' . $db->quoteName('subscriber_id') . ' AS id');
		$query->from($db->quoteName('#__bwpostman_subscribers_mailinglists') . ' AS sl');
		$query->leftJoin($db->quoteName('#__bwpostman_subscribers') . ' AS s ON s.' . $db->quoteName('id') . ' = sl.' . $db->quoteName('subscriber_id'));
		$query->where($db->quoteName('mailinglist_id') . ' = ' . $db->quote((int) $mailinglist_id));
		$db->setQuery($query);

		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());
			return FALSE;
		}
		return $db->loadAssocList();

	}

	/**
	 * Method to get all test-recipients
	 *
	 * @access	private
	 *
	 * @return 	array|bool		test-recipient IDs
	 *
	 * @since	1.2.0 *
	 */
	static private function _getTestrecipientsIDs()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__bwpostman_subscribers'));
		$query->where($db->quoteName('status') . ' = ' . (int) 9);
		$db->setQuery((string) $query);

		if (!$db->query()) {
			JError::raiseError(500, $db->getErrorMsg());
			return FALSE;
		}
		return $db->loadAssocList();
	}


	/**
	 * Method to get sending times form now on
	 *
	 * @access	private
	 *
	 * @param 	int 	$campaign_id        Campaign ID
	 *
	 * @return 	array	sending times
	 *
	 * @since	1.2.0
	 */
	static private function _getSendingTime($campaign_id = 0)
	{

		$item			= JFactory::getApplication()->getUserState('bwtimecontrol.item', null);
		$date 			= JFactory::getDate();
		$sending_time	= array();

		if (($item->publish_up != '0000-00-00 00:00:00') && ($item->publish_up >= $date->toSql())) {
			$start_time = JFactory::getDate($item->publish_up)->toUnix();
		}
		else {
			$start_time = $date->toUnix();
		}
		$old_time = $start_time;
//dump (JFactory::getDate('now')->toSql(), 'Now');
//dump (JFactory::getDate($start_time)->toSql(), 'Start-Time');

		$automailing_values = json_decode($item->automailing_values);
//dump ($automailing_values, 'Auto-Values');

		foreach ($automailing_values->nl_id as $key => $value) {
//dump ($key, 'Key');
//dump ($value, 'Value');

			if ($value > 0) {
				if ($item->chaining == 1) {
					$start_time		+= $automailing_values->day[$key]*3600*24 + $automailing_values->hour[$key]*3600 + $automailing_values->minute[$key]*60;
					$sending_time[]	= JFactory::getDate($start_time)->toSql();
				}
				else {
					$sending_time[] =  JFactory::getDate($start_time + $automailing_values->day[$key]*3600*24 + $automailing_values->hour[$key]*3600 + $automailing_values->minute[$key]*60)->toSQL(); // unix-timestamp
				}

			}
		}
		return $sending_time;
	}


	/**
	 * Method to push the campaign newsletters into tc_sendMailQueue
	 *
	 * If a newsletter of an automailing campaign shall be sent, the mail will be separated in two
	 * parts to save storage:
	 * The content/body of the mail will be stored two times in a separate table, one time for HTML,
	 * the other time for text-mode with another function. With this the body is prepared for sending
	 * and only stored two times.
	 * The "rest" of the mail (recipient, sender, mode, ...) is variant for each mail and is stored
	 * with this function in table tc_sendmailqueue
	 *
	 * @access	private
	 *
	 * @param 	int		$campaign_id        Campaign ID
	 * @param 	array	$subscribers        subscriber ids
	 * @param 	int		$test               test mode, regular = 0, test mode = 1
	 *
	 * @return 	boolean	False if there occurred an error
	 *
	 * @since	1.2.0
	 */
	static private function _fillTcSendMailQueue($campaign_id = 0, $subscribers, $test = 0)
	{
		$app	= JFactory::getApplication();
//		$item	= $app->getUserState('bwtimecontrol.item', null);
		// check if a campaign ID is submitted
		if ($campaign_id == 0) {
			JError::raiseError(500, JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_FILL_QUEUE_NO_ID'));
		}

		$item	= self::getItem($campaign_id);
dump ($subscribers, 'FillQueue Subscribers uebermittelt');
dump ($campaign_id, 'FillQueue C-ID');

		// check if the campaign is active
		if(!$item->active && $test == 0) {
			JError::raiseError(500, JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_FILL_QUEUE_CAM_NOT_ACTIVE'));
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		// get content ids for newsletters

		// build 2 querys to get all content-ids concerning this campaign, one for text, one for HTML
		$mails_text		= self::_getTcContentIDs($campaign_id, 0);
		$mailmax_text	= count ($mails_text);

		$mails_html		= self::_getTcContentIDs($campaign_id, 1);
		$mailmax_html	= count ($mails_html);
dump ($mails_text, 'Text-Mail-IDs');
dump ($mails_html, 'HTML-Mail-IDs');

		if ($mailmax_html != $mailmax_text) {
			JError::raiseError(500, JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_ERROR_DIFFERENT_MAIL_NUMBERS'));
		}
		else {
			$mailMax = $mailmax_html;
		}

		// calculate sending times
		$sending_time	=  self::_getSendingTime($campaign_id);
dump ($sending_time, 'FillQueue Sending_time');
dump ($mailMax, 'FillQueue Mail Max');

		$tblTcQueue					= self::getTable('Tc_Sendmailqueue', 'BwPostmanTable');
		$tblTcQueue->campaign_id	= $campaign_id;

		// for each newsletter of the campaign we have to build an entry for each recipient in the tc-queue
		for ($mail = 0; $mail < $mailMax; $mail++) {
			$tblTcQueue->mail_number		= $mails_html[$mail]->mail_number;
			$tblTcQueue->sending_planned	= $sending_time[$mail];

			foreach ($subscribers as $recipient) {
//dump ($test, 'FillQueue Test');
dump ($mail, 'FillQueue Mail Number');

				// try to get queue-data for subscribers if already exists
				$query->clear();
				$query->select($db->quoteName('id'));
				$query->from($db->quoteName('#__bwpostman_tc_sendmailqueue'));
				$query->where($db->quoteName('subscriber_id') . ' = ' . (int) $recipient['id']);
				$query->where($db->quoteName('mail_number') . ' = ' . (int) $mails_html[$mail]->mail_number);
				$query->where($db->quoteName('campaign_id') . ' = ' . (int) $campaign_id);
				$query->where($db->quoteName('sent_time') . ' = ' . $db->quote('0000-00-00 00:00:00'));
				$db->setQuery($query);

				if (!$db->query()) {
					JError::raiseError(500, $db->getErrorMsg());
				}
				$tc_queue_id	= $db->loadResult();
				if ($tc_queue_id > 0 && $test == 0) {
					$tblTcQueue->id	= $tc_queue_id;
				}
				else {
					$tblTcQueue->id	= 0;
				}

//dump ($recipient, 'Recipient');
				// get subscriber data
				$query->clear();
				$query->select('*');
				$query->from($db->quoteName('#__bwpostman_subscribers'));
				$query->where($db->quoteName('id') . ' = ' . (int) $recipient['id']);
				$db->setQuery($query);
//dump ($db->getQuery(), 'Query Queue Subscriber');

				if (!$db->query()) {
					JError::raiseError(500, $db->getErrorMsg());
				}
				$subscriber_data	= $db->loadObject();
//dump ($db->getQuery(), 'Query Subscriber Data');
				if (is_object($subscriber_data)) {
//dump ($subscriber_data, 'Subscriber Data');

					if ($subscriber_data->emailformat == 1) {
						$tblTcQueue->tc_content_id	= $mails_html[$mail]->id;
					}
					else {
						$tblTcQueue->tc_content_id	= $mails_text[$mail]->id;
					}

					$tblTcQueue->mode			= $subscriber_data->emailformat;
					$tblTcQueue->name			= $subscriber_data->name;
					$tblTcQueue->firstname		= $subscriber_data->firstname;
					$tblTcQueue->email			= $subscriber_data->email;
					$tblTcQueue->subscriber_id	= $subscriber_data->id;
dump ($tblTcQueue, 'Table Tc-Queue gefuellt');

					// Write newsletters in tc-Queue

					$ret	= $tblTcQueue->store();
					if ($ret == FALSE) {
						JError::raiseError(500, $db->getErrorMsg());
						return false;
					}
				}
dump ($ret, 'Table Tc-Queue speichern');
			}
		}
		return true;
	}

	/**
	 * Method to fill tc_sendmailcontent
	 *
	 * @access	private
	 *
	 * @param 	array 	$data       bwtimecontrol data
	 *
	 * @return 	bool	true on success
	 *
	 * @since	1.2.0
	 */
	static public function storeCampaign($data = NULL)
	{
		// Get Newsletter-Model
		$controller	= JControllerLegacy::getInstance('BwPostman');
		$model		= $controller->getModel('newsletter');

		// If automailing then we have to fill tc_sendmailContent and eventually update the campaign ID for each newsletter in the campaign
		if ($data['automailing']) {
			// get the newsletter IDs from Post data
			$nl_id = array();
			$automailing_values = json_decode($data['automailing_values']);
//dump ($automailing_values, 'Helper storeCampaign Automailing Values');

			foreach ($automailing_values->nl_id as $key) {
				if ($key > 0) {
					$nl_id[] = $key;
				}
			}
//dump ($nl_id, 'Helper storeCampaign NL-ID');

			$i = 1;
			foreach ($nl_id as $key) {
				$newsletter				= $model->getItem($key);
//dump ($newsletter, 'Helper storeCampaign Newsletter');

				$newsletter->mail_id	= $i;
				self::_addTcSendMailContent($newsletter);
				$i++;
			}
		}
		return true;
	}
}

