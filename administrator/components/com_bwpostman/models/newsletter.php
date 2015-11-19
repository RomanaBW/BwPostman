<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single newsletter model for backend.
 *
 * @version 1.2.4 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2015 Boldt Webservice <forum@boldt-webservice.de>
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

require_once (JPATH_SITE.'/components/com_content/helpers/route.php');

// Import MODEL object class
jimport('joomla.application.component.modeladmin');

// Require helper class
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');

/**
 * BwPostman newsletter model
 * Provides methodes to add, edit and send newsletters
 *
 * @package		BwPostman-Admin
 * @subpackage	Newsletters
 */
class BwPostmanModelNewsletter extends JModelAdmin
{
	/**
	 * Newsletter id
	 *
	 * @var int
	 */
	var $_id = null;

	/**
	 * Newsletter data
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Constructor
	 * Determines the newsletter ID
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$jinput	= JFactory::getApplication()->input;
		$array	= $jinput->get('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	 * 
	 * @since  1.0.1
	*/
	public function getTable($type = 'Newsletters', $prefix = 'BwPostmanTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to reset the newsletter ID and data
	 *
	 * @access	public
	 * @param	int Newsletter ID
	 */
	public function setId($id)
	{
		$this->_id		= $id;
		$this->_data	= null;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to delete the record. Defaults to the permission set in the component.
	 * @since	1.0.1
	 */
	protected function canDelete($record)
	{
		$user = JFactory::getUser();

		// Check general delete permission first.
		if ($user->authorise('core.delete', 'com_bwpostman'))
		{
			return true;
		}
		
		if (!empty($record->id)) {
			// Check specific delete permission.
			if ($user->authorise('core.delete', 'com_bwpostman.newsletters.' . (int) $recordId))
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to change the state of the record. Defaults to the permission set in the component.
	 * @since	1.0.1
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		// Check general edit state permission first.
		if ($user->authorise('core.edit.state', 'com_bwpostman'))
		{
			return true;
		}
		
		if (!empty($record->id)) {
			// Check specific edit state permission.
			if ($user->authorise('core.edit.state', 'com_bwpostman.newsletters.' . (int) $recordId))
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   1.0.1
	 */
	public function getItem($pk = null)
	{
		$app		= JFactory::getApplication();
		$userId		= JFactory::getUser()->get('id');
		$_db		= $this->_db;

		// Initialise variables.
		$pk		= (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
		$table	= $this->getTable();
		$app->setUserState('com_bwpostman.edit.newsletter.id', $pk);
		
		// Get input data
		$state_data	= $app->getUserState('com_bwpostman.edit.newsletter.data');
		

		// if state exists and matches required id, use state, otherwise get data from table
		if (is_object($state_data) && $state_data->id == $pk) {
			$item	= $state_data;
		}
		else {
			// Get the data from the model
		
			// Attempt to load the row.
			$return = $table->load($pk);
	
			// Check for a table object error.
			if ($return === false && $table->getError())
				{
					$this->setError($table->getError());
					return false;
				}
				
			// Convert to the JObject before adding other data.
			$properties	= $table->getProperties(1);
			$item 		= JArrayHelper::toObject($properties, 'JObject');
		
			if (property_exists($item, 'params')) {
				$registry = new JRegistry;
				$registry->loadJSON($item->params);
				$item->params = $registry->toArray();
			}
			
			//get associated mailinglists
			$query	= $_db->getQuery(true);
			$query->select($_db->quoteName('mailinglist_id'));
			$query->from($_db->quoteName('#__bwpostman_newsletters_mailinglists'));
			$query->where($_db->quoteName('newsletter_id') . ' = ' . (int) $item->id);
			$_db->setQuery($query);
			$item->mailinglists= $_db->loadColumn();
	
			//extract associated usergroups
			$usergroups	= array();
			foreach ($item->mailinglists as $mailinglist) {
				if ((int) $mailinglist < 0) $usergroups[]	= -(int)$mailinglist;
			}
			$item->usergroups	= $usergroups;
			
			if ($pk == 0) $item->id	= 0;
			
			// get avaliable mailinglists to predefine for state
			$query	= $_db->getQuery(true);
			$query->select('id');
			$query->from($_db->quoteName('#__bwpostman_mailinglists'));
			$query->where($_db->quoteName('published') . ' = ' . (int) 1);
			$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);
			$query->where($_db->quoteName('access') . ' = ' . (int) 1);
			
			$_db->setQuery($query);
			
			$mls_avaliable	= $_db->loadColumn();
			$res_avaliable	= array_intersect($item->mailinglists, $mls_avaliable);
			
			if (count($res_avaliable) > 0) {
				$item->ml_available	= $res_avaliable;
			}
			else {
				$item->ml_available	= array();
			}
				
			// get unavaliable mailinglists to predefine for state
			$query	= $_db->getQuery(true);
			$query->select('id');
			$query->from($_db->quoteName('#__bwpostman_mailinglists'));
			$query->where($_db->quoteName('published') . ' = ' . (int) 1);
			$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);
			$query->where($_db->quoteName('access') . ' > ' . (int) 1);
			
			$_db->setQuery($query);
			
			$mls_unavaliable	= $_db->loadColumn();
			$res_unavaliable	= array_intersect($item->mailinglists, $mls_unavaliable);
			
			if (count($res_unavaliable) > 0) {
				$item->ml_unavailable	= $res_unavaliable;
			}
			else {
				$item->ml_unavailable	= array();
			}
				
			// get internal mailinglists to predefine for state
			$query	= $_db->getQuery(true);
			$query->select('id');
			$query->from($_db->quoteName('#__bwpostman_mailinglists'));
			$query->where($_db->quoteName('published') . ' = ' . (int) 0);
			$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);
//			$query->where($_db->quoteName('access') . ' = ' . (int) 1);
			
			$_db->setQuery($query);
			
			$mls_intern		= $_db->loadColumn();
			$res_intern		= array_intersect($item->mailinglists, $mls_intern);
			
			if (count($res_intern) > 0) {
				$item->ml_intern	= $res_intern;
			}
			else {
				$item->ml_intern	= array();
			}
			
			// Preset template ids
			// Old template for existing newsletters not set during update to 1.1.x, so we have to mangage this here also
			
			// preset HTML-Template for old newsletters
			if ($item->id == 0) {
				$item->template_id	= $this->_getStandardTpl('html');
			}
			elseif ($item->template_id == 0) {
				$query	= $_db->getQuery(true);
				$query->select('id');
				$query->from($_db->quoteName('#__bwpostman_templates'));
				$query->where($_db->quoteName('id') . ' = ' . (int) -1);
				
				$_db->setQuery($query);
				
				$html_tpl	= $_db->loadResult();
				
				if (is_null($html_tpl)) {
					$html_tpl	= $this->_getStandardTpl('html');
				}
				$item->template_id	= $html_tpl;
			}
			
			// preset Text-Template for old newsletters
			if ($item->id == 0) {
				$item->text_template_id	= $this->_getStandardTpl('text');
			}
			elseif ($item->text_template_id == 0) {
				$query	= $_db->getQuery(true);
				$query->select('id');
				$query->from($_db->quoteName('#__bwpostman_templates'));
				$query->where($_db->quoteName('id') . ' = ' . (int) -2);

				$_db->setQuery($query);
				
				$text_tpl	= $_db->loadResult();
				
				if (is_null($text_tpl)) {
					$text_tpl	= $this->_getStandardTpl('text');
				}
				$item->text_template_id	= $text_tpl;
			}
			// preset Old Template IDs
			if ($item->id == 0) {
				$item->template_id_old	= '';
				$item->text_template_id_old	= '';
			}
			else {
				$item->template_id_old	= $item->template_id;
				$item->text_template_id_old	= $item->text_template_id;
			}
			
		}
		JFactory::getApplication()->setUserState('com_bwpostman.edit.newsletter.data', $item);
		
		return $item;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.0.1
	 */
	public function getForm($data = array(), $loadData = true)
	{
		JForm::addFieldPath('JPATH_ADMINISTRATOR/components/com_bwpostman/models/fields');

		$params = JComponentHelper::getParams('com_bwpostman');
		$config = Jfactory::getConfig();
		$user	= JFactory::getUser();
		
		$form = $this->loadForm('com_bwpostman.newsletter', 'newsletter', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		$jinput	= JFactory::getApplication()->input;
		$id		=  $jinput->get('id', 0);

		// predefine some values
		if (!$form->getValue('from_name')) {
			if ($params->get('default_from_name') && !$form->getValue('from_name')) {
				$form->setValue('from_name', '', $params->get('default_from_name'));
			}
			else {
				$form->setValue('from_name', '', $config->get('fromname'));
			}
		}
		
		if (!$form->getValue('from_email')) {
			if ($params->get('default_from_email')) {
				$form->setValue('from_email', '', $params->get('default_from_email'));
			}
			else {
				$form->setValue('from_email', '', $config->get('mailfrom'));
			}
		}
		
		if (!$form->getValue('reply_email')) {
			if ($params->get('default_reply_email')) {
				$form->setValue('reply_email', '', $params->get('default_reply_email'));
			}
			else {
				$form->setValue('reply_email', '', $config->get('mailfrom'));
			}
		}
		
		// Check for existing newsletter.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (!$user->authorise('core.edit.state', 'com_bwpostman.newsletter.'.(int) $id))
				|| ($id == 0 && !$user->authorise('core.edit.state', 'com_bwpostman'))
			)
		{
			// Disable fields for display.
			$form->setFieldAttribute('published', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is an newsletter you can edit.
			$form->setFieldAttribute('state', 'filter', 'unset');

		}
		// Check to show created data
		$c_date	= $form->getValue('created_date');
		if ($c_date == '0000-00-00 00:00:00') {
			$form->setFieldAttribute('created_date', 'type', 'hidden');
			$form->setFieldAttribute('created_by', 'type', 'hidden');
		}
		
		// Check to show modified data
		$m_date	= $form->getValue('modified_time');
		if ($m_date == '0000-00-00 00:00:00') {
			$form->setFieldAttribute('modified_time', 'type', 'hidden');
			$form->setFieldAttribute('modified_by', 'type', 'hidden');
		}
		
		// Check to show mailing data
		$s_date	= $form->getValue('mailing_date');
		if ($s_date == '0000-00-00 00:00:00') {
			$form->setFieldAttribute('mailing_date', 'type', 'hidden');
		}

		// Hide published on tab edit_basic
		if ($jinput->get('layout') == 'edit_basic') {
			$form->setFieldAttribute('published', 'type', 'hidden');
		}
		return $form;
	}

	/**
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.0.1
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data	= JFactory::getApplication()->getUserState('com_bwpostman.edit.newsletter.data', null);
		
		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}
		
	/**
	 * Method to get the standard template.
	 *
	 * @return	string	ID of standard template.
	 * @since	1.2.0
	 */
	private function _getStandardTpl($mode	= 'html')
	{
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);
		
		// Id of the standard template
		switch ($mode) {
			case 'html':
			default:
					$query->select($_db->quoteName('id'));
					$query->from($_db->quoteName('#__bwpostman_templates'));
					$query->where($_db->quoteName('standard') . ' = ' . $_db->Quote('1'));
					$query->where($_db->quoteName('tpl_id') . ' < ' . $_db->Quote('998'));
			
					$_db->setQuery($query);
				break;

			case 'text':
					$query	= $_db->getQuery(true);
					$query->select($_db->quoteName('id')  . ' AS ' . $_db->quoteName('value'));
					$query->from($_db->quoteName('#__bwpostman_templates'));
					$query->where($_db->quoteName('standard') . ' = ' . $_db->Quote('1'));
					$query->where($_db->quoteName('tpl_id') . ' > ' . $_db->Quote('997'));
			
					$_db->setQuery($query);
				break;
		}
		
		return $_db->loadResult();
	}
	
	/**
	 * Method to get the data of a single newsletter for the preview/modalbox
	 *
	 * @access	public
	 * 
	 * @return 	object Newsletter with formatted pieces
	 */
	public function getSingleNewsletter ()
	{
		$app	= JFactory::getApplication();
		$nl_id	= (int) $app->getUserState('com_bwpostman.viewraw.newsletter.id');
		
		if (!$nl_id) (int) $app->getUserState('com_bwpostman.edit.newsletter.id');

		$item	= $app->getUserState('com_bwpostman.edit.newsletter.data');
		
		//convert to object if necessary
		if ($item && !is_object($item)) {
			$data_tmp	= new stdClass();
			foreach ($item as $key => $value) {
				$data_tmp->$key	= $value;
			}
			$item = $data_tmp;
		}

		// if old newsletter, there are no template IDs, so lets set them to the old template 
		if ($item->template_id == '0')		$item->template_id		= -1;
		if ($item->text_template_id == '0')	$item->text_template_id	= -2;
		
		if ($item->id == 0 && !empty($item->selected_content) && empty($item->html_version) && empty($item->text_version)) {
			if (!is_array($item->selected_content)) $item->selected_content = explode(',', $item->selected_content);
			$renderer	= new contentRenderer();
			$content	= $renderer->getContent((array) $item->selected_content, $item->subject, $item->template_id, $item->text_template_id);
			$item->html_version	= $content['html_version'];
			$item->text_version	= $content['text_version'];
		}

		// force two linebreaks at the end of text
		$item->text_version = rtrim($item->text_version)."\n\n";

		// Replace the links to provide the correct preview
		$item->html_formatted	= $item->html_version;
		$item->text_formatted	= $item->text_version;

		// add template data
		$this->_addTplTags($item->html_formatted, $item->template_id);
		$this->_addTextTpl($item->text_formatted, $item->text_template_id);

		// Replace the intro to provide the correct preview
		if (!empty($item->intro_headline)) $item->html_formatted		= str_replace('[%intro_headline%]', $item->intro_headline, $item->html_formatted);
		if (!empty($item->intro_text)) $item->html_formatted			= str_replace('[%intro_text%]', nl2br($item->intro_text, true), $item->html_formatted);
		if (!empty($item->intro_text_headline)) $item->text_formatted	= str_replace('[%intro_headline%]', $item->intro_text_headline, $item->text_formatted);
		if (!empty($item->intro_text_text)) $item->text_formatted		= str_replace('[%intro_text%]', $item->intro_text_text, $item->text_formatted);
		
		// only for old html templates
		if ($item->template_id < 1) {
			$item->html_formatted = $item->html_formatted . '[dummy]';
		}
		$this->_replaceTplLinks($item->html_formatted);
		$this->_addHtmlTags($item->html_formatted, $item->template_id);
		$this->_addHtmlFooter($item->html_formatted, $item->template_id);
		
		// only for old text templates
		if ($item->text_template_id < 1) {
			$item->text_formatted = $item->text_formatted . '[dummy]';
		}
		$this->_replaceTextTplLinks($item->text_formatted);
		$this->_addTextFooter($item->text_formatted, $item->text_template_id);
		
		// Replace the links to provide the correct preview
		$this->_replaceLinks($item->html_formatted);
		$this->_replaceLinks($item->text_formatted);
		
		return $item;
	}

	/**
	 * Method to get the selected content items which are used to compose a newsletter
	 *
	 * @access	public
	 * @return	array
	 */
	public function getSelectedContent()
	{
		$_db	= $this->_db;
		
		$selected_content = $this->_selectedContent();
		$selected_content_void = array ();

		if ($selected_content) {
			if (!is_array($selected_content)) $selected_content = explode(',',$selected_content);
							
			$selected_content_items = array();
				
			// We do a foreach to protect our ordering
			foreach($selected_content as $content_id){
				$subquery	= $_db->getQuery(true);
				$subquery->select($_db->quoteName('cc') . '.' . $_db->quoteName('title'));
				$subquery->from($_db->quoteName('#__categories') . ' AS ' . $_db->quoteName('cc'));
				$subquery->where($_db->quoteName('cc') . '.' . $_db->quoteName('id') . ' = ' . $_db->quoteName('c') . '.' . $_db->quoteName('catid'));
				
				$query	= $_db->getQuery(true);
				$query->select($_db->quoteName('c') . '.' . $_db->quoteName('id'));
				$query->select($_db->quoteName('c') . '.' . $_db->quoteName('title') . ', (' . $subquery) . ') AS ' . $_db->quoteName('category_name');
				$query->from($_db->quoteName('#__content') . ' AS ' . $_db->quoteName('c'));
				$query->where($_db->quoteName('c') . '.' . $_db->quoteName('id') . ' = ' . $_db->Quote($content_id));
				
				$_db->setQuery($query);
				
				$items= $_db->loadObjectList();
				 
				if(sizeof($items) > 0){
					if ($items[0]->category_name == '') {
						$selected_content_items[] = JHTML::_('select.option', $items[0]->id, "Uncategorized - " . $items[0]->title);
					} else {
						$selected_content_items[] = JHTML::_('select.option', $items[0]->id, $items[0]->category_name . " - " . $items[0]->title);
					}
				}
			}
			return $selected_content_items;

		} 
		else {
			return $selected_content_void;
		}
	}

	/**
	 * Method to get the menu item ID which will be needed for the unsubscribe link in the footer
	 *
	 * @access	public
	 * @return 	int menu item ID
	 */
	static public function getItemid($view)
	{
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);
		
		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__menu'));
		$query->where($_db->quoteName('link') . ' = ' . $_db->Quote('index.php?option=com_bwpostman&view='.$view));
		$query->where($_db->quoteName('published') . ' = ' . (int) 1);
				
		$_db->setQuery($query);
		$itemid = $_db->loadResult();

		if (empty($itemid)) {
			$query	= $_db->getQuery(true);
			
			$query->select($_db->quoteName('id'));
			$query->from($_db->quoteName('#__menu'));
			$query->where($_db->quoteName('link') . ' = ' . $_db->Quote('index.php?option=com_bwpostman&view=register'));
			$query->where($_db->quoteName('published') . ' = ' . (int) 1);
					
			$_db->setQuery($query);
			$itemid = $_db->loadResult();
		}
		return $itemid;
	}

	/**
	 * Method to get the language of an article
	 *
	 * @access	public
	 * 
	 * @param	int		article ID
	 * 
	 * @return 	mixed	language string or 0
	 * 
	 * @since	1.0.7
	 */
	static public function getArticleLanguage($id)
	{
		if (JLanguageMultilang::isEnabled()) {
			$_db	= JFactory::getDbo();
			$query	= $_db->getQuery(true);
			
			$query->select($_db->quoteName('language'));
			$query->from($_db->quoteName('#__content'));
			$query->where($_db->quoteName('id') . ' = ' . (int) $id);
					
			$_db->setQuery($query);
			$result = $_db->loadResult();
	
			return $result;
		}
		else {
			return 0;
		}
	}

	/**
	 * Method to store the newsletter data from the newsletters_tmp-table into the newsletters-table
	 *
	 * @access	public
	 * @return 	boolean
	 */
	public function save($data)
	{
		$jinput		= JFactory::getApplication()->input;
		$_db		= $this->_db;
		$query		= $_db->getQuery(true);
		
		// merge ml-arrays, single array may not exist, therefore array_merge would not give a result
		if (isset($data['ml_available']))	foreach ($data['ml_available'] as $key => $value)	$data['mailinglists'][] 	= $value;
		if (isset($data['ml_unavailable']))	foreach ($data['ml_unavailable'] as $key => $value)	$data['mailinglists'][] 	= $value;
		if (isset($data['ml_intern']))		foreach ($data['ml_intern'] as $key => $value)		$data['mailinglists'][] 	= $value;
		
		// merge usergroups into mailinglists, single array may not exist, therefore array_merge would not give a result
		if (isset($data['usergroups']) && !empty($data['usergroups']))	foreach ($data['usergroups'] as $key => $value)	$data['mailinglists'][] = '-' . $value;
		
		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('bwpostman');
		
		// if saving a new newsletter before changing tab, we have to look, if there is a content selected and set html- and text-version
		if (empty($data['html_version']) && empty($data['text_version'])) {
			$this->setError(JText::_('COM_BWPOSTMAN_NL_ERROR_CONTENT_MISSING'));
			return false;
		}
			
		if (!parent::save($data)) {
			return false;
		}
		
	// Delete all entrys of the newsletter from newsletters_mailinglists table
		if ($data['id']) {
			$query->delete($_db->quoteName('#__bwpostman_newsletters_mailinglists'));
			$query->where($_db->quoteName('newsletter_id') . ' =  ' . (int) $data['id']);
			
			$_db->setQuery($query);
			$_db->Execute($query);
		}
		else {
			//get id of new inserted data to write cross table newsletters-mailinglists and inject into form
			$data['id']	= $this->getState('newsletter.id');
			$jinput->set('id', $data['id']);
			
			// update state
			$state_data	= JFactory::getApplication()->getUserState('com_bwpostman.edit.newsletter.data');
			if (is_object($state_data)) {	// check needed because copying newsletters has no state and does not need it
				$state_data->id	= $data['id'];
				JFactory::getApplication()->setUserState('com_bwpostman.edit.newsletter.data', $state_data);
			}
		}
		
		if ($data['campaign_id'] == '-1') {
			// Store the selected BwPostman mailinglists into newsletters_mailinglists-table
			if (isset($data['mailinglists']) && $data['campaign_id'] == '-1') {
				foreach ($data['mailinglists'] AS $mailinglists_value) {
					$query	= $_db->getQuery(true);
									
					$query->insert($_db->quoteName('#__bwpostman_newsletters_mailinglists'));
					$query->columns(array(
						$_db->quoteName('newsletter_id'),
						$_db->quoteName('mailinglist_id')
						));
						$query->values(
						(int) $data['id'] . ',' .
						(int) $mailinglists_value
						);
					$_db->setQuery($query);
					$_db->execute();
				}
			}
		}
		$dispatcher->trigger('onBwPostmanAfterNewsletterModelSave', array(&$data));
		
		return true;
	}

	/**
	 * Method to (un)archive a newsletter from the newsletters-table
	 * --> when unarchiving it is called by the archive-controller
	 *
	 * @access	public
	 * @param	array Newsletter IDs
	 * @param	tinyint Task --> 1 = archive, 0 = unarchive
	 * @return	boolean
	 */
	public function archive($cid = array(), $archive = 1)
	{
		$app		= JFactory::getApplication();
		$date		= JFactory::getDate();
		$uid		= JFactory::getUser()->get('id');
		$state_data	= $app->getUserState('com_bwpostman.edit.newsletter.data');
		$_db		= $this->_db;
		$query		= $_db->getQuery(true);
		
		if ($archive == 1) {
			$time = $date->toSql();

			// Access check.
			foreach ($cid as $i) {
				$data = self::getItem($i);
				if (!BwPostmanHelper::allowArchive($i, $data->created_by, 'newsletter'))
				{
					$app->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_ARCHIVE_RIGHTS_MISSING'), 'error');
					return false;
				}
			}
		} else {
			$time	= '0000-00-00 00:00:00';
			$uid	= 0;
		
			// Access check.
			foreach ($cid as $i) {
				$data = self::getItem($i);
				if (!BwPostmanHelper::allowRestore($i, $data->created_by, 'newsletter'))
				{
					$app->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_RESTORE_RIGHTS_MISSING'), 'error');
					return false;
				}
			}
		}

		if (count($cid))
		{
			JArrayHelper::toInteger($cid);

			$query->update($_db->quoteName('#__bwpostman_newsletters'));
			$query->set($_db->quoteName('archive_flag') . " = " . (int) $archive);
			$query->set($_db->quoteName('archive_date') . " = " . $_db->Quote($time, false));
			$query->set($_db->quoteName('archived_by') . " = " . (int) $uid);
			$query->where($_db->quoteName('id') . ' IN (' .implode(',', $cid) . ')');
			
			$_db->setQuery($query);

			if (!$_db->query()) {
				$this->setError($_db->getErrorMsg());
				return false;
			}
		}
		$app->setUserState('com_bwpostman.edit.newsletter.data', $state_data);
		return true;
	}

	/**
	 * Method to copy one or more newsletters
	 * --> the assigned mailingslists will be copied, too
	 *
	 * @param 	array Newsletter-IDs
	 * @return 	boolean
	 */
	public function copy($cid = array())
	{
		$app	= JFactory::getApplication();
		$_db	= $this->_db;
		
		if (count($cid)) {
			foreach ($cid as $id){
				$newsletters	= $this->getTable('newsletters', 'BwPostmanTable');
				$query			= $_db->getQuery(true);
				
				$query->select('*');
				$query->from($_db->quoteName('#__bwpostman_newsletters'));
				$query->where($_db->quoteName('id') . ' = ' . (int) $id);
				
				$_db->setQuery($query);
				
				$newsletters_data_copy = $_db->loadObject();
				if (is_string($newsletters_data_copy->usergroups)) {
					if ($newsletters_data_copy->usergroups == '') {
						$newsletters_data_copy->usergroups = array();
					}
					else {
						$newsletters_data_copy->usergroups	= explode(',', $newsletters_data_copy->usergroups);
					}
				}
				
				if (!is_object($newsletters_data_copy)) {
					$app->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_COPY_FAILED'), 'error');
				}

				$date	= JFactory::getDate();
				$time	= $date->toSql();
				$user	= JFactory::getUser();
				$uid	= $user->get('id');

				$newsletters_data_copy->id 					= null;
				$newsletters_data_copy->asset_id			= null;
				$newsletters_data_copy->subject 			= JText::sprintf('COM_BWPOSTMAN_NL_COPY_OF', $newsletters_data_copy->subject); 
				$newsletters_data_copy->attachment	 		= null;
				$newsletters_data_copy->created_date 		= $time;
				$newsletters_data_copy->created_by			= $uid;
				$newsletters_data_copy->modified_time	 	= null;
				$newsletters_data_copy->modified_by	 		= null;
				$newsletters_data_copy->mailing_date 		= 0;
				$newsletters_data_copy->published 			= null;
				$newsletters_data_copy->checked_out 		= null;
				$newsletters_data_copy->checked_out_time 	= null;
				$newsletters_data_copy->archive_flag 		= 0;
				$newsletters_data_copy->archive_date 		= 0;
				$newsletters_data_copy->hits 				= null;
				
				$subQuery	= $_db->getQuery(true);
				
				$subQuery->select($_db->quoteName('mailinglist_id'));
				$subQuery->from($_db->quoteName('#__bwpostman_newsletters_mailinglists'));
				$subQuery->where($_db->quoteName('newsletter_id') . ' = ' . (int) $id);

				$_db->setQuery($subQuery);
				
				$newsletters_data_copy->mailinglists	= $_db->loadColumn();

				if (!$this->save(JArrayHelper::fromObject($newsletters_data_copy, false))) {
					$app->enqueueMessage($_db->getErrorMsg(), 'error');
					return false;
				}
			}
			$app->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_COPIED'), 'message');
			return true;
		}
		else {
			$app->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_ERROR_COPYING'), 'error');
			return false;
		}
	}

	/**
	 * Method to remove one or more newsletters from the newsletters-table
	 * --> is called by the archive-controller
	 *
	 * @access	public
	 * @param	array Newsletter IDs
	 * @return	boolean
	 */
	public function delete(&$pks)
	{
		$result = false;

		// Access check.
		foreach ($pks as $i) {
			$data = self::getItem($i);
			if (!BwPostmanHelper::allowDelete($i, $data->created_by, 'newsletter'))
			{
				return false;
			}
		}

		if (count($pks))
		{
			JArrayHelper::toInteger($pks);
			$cids = implode(',', $pks);

			// Delete newsletter from newsletters-table
			$nl_table = JTable::getInstance('newsletters', 'BwPostmanTable');

			foreach ($pks as $id) {
				if (!$nl_table->delete($id))
				{
					return false;
				}
			}
				
			// Delete assigned mailinglists from newsletters_mailinglists-table
			$lists_table = JTable::getInstance('newsletters_mailinglists', 'BwPostmanTable');
			
			foreach ($pks as $id) {
				if (!$lists_table->delete($id))
				{
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Method to clear the queue
	 *
	 * @access	public
	 * @return 	boolean
	 */
	public function delete_queue()
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);
		
		$query = "TRUNCATE TABLE {$_db->quoteName('#__bwpostman_sendmailqueue')} ";
		$_db->setQuery($query);
		if(!$_db->query()) {
			$this->setError($_db->getErrorMsg());
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Method to check and clean the input fields
	 * 
	 * @access	public
	 *
	 * @param 	int		Newsletter ID
	 * @param	array	errors
	 * 
	 * @return	boolean
	 */
	public function checkForm($recordId = 0, &$err)
	{
		jimport('joomla.mail.helper');

		// heal form data and get them
		$this->changeTab();
		$data	= JArrayHelper::fromObject(JFactory::getApplication()->getUserState('com_bwpostman.edit.newsletter.data'));
		
		$data['id']	= $recordId;
		$i = 0;

		//Remove all HTML tags from name, emails, subject and the text version
		$filter = new JFilterInput(array(), array(), 0, 0);
		$data['from_name'] 		= $filter->clean($data['from_name']);
		$data['from_email'] 	= $filter->clean($data['from_email']);
		$data['reply_email'] 	= $filter->clean($data['reply_email']);
		$data['subject']		= $filter->clean($data['subject']);
		$data['text_version']	= $filter->clean($data['text_version']);

		$err = array();

		// Check for valid from_name
		if (trim($data['from_name']) == '') {
			$err[$i]['err_code'] = 301;
			$err[$i]['err_msg'] = JText::_('COM_BWPOSTMAN_NL_ERROR_FROM_NAME');
			$i++;
		}

		// Check for valid from_email address
		if (trim($data['from_email']) == '') {
			$err[$i]['err_code'] = 302;
			$err[$i]['err_msg'] = JText::_('COM_BWPOSTMAN_NL_ERROR_FROM_EMAIL');
			$i++;
		} else {
			// If there is a from_email adress check if the adress is valid
			if (!JMailHelper::isEmailAddress(trim($data['from_email']))) {
				$err[$i]['err_code'] = 306;
				$err[$i]['err_msg'] = JText::_('COM_BWPOSTMAN_NL_ERROR_FROM_EMAIL_INVALID');
				$i++;
			}
		}

		// Check for valid reply_email address
		if (trim($data['reply_email']) == '') {
			$err[$i]['err_code'] = 303;
			$err[$i]['err_msg'] = JText::_('COM_BWPOSTMAN_NL_ERROR_REPLY_EMAIL');
			$i++;
		} else {
			// If there is a from_email adress check if the adress is valid
			if (!JMailHelper::isEmailAddress(trim($data['reply_email']))) {
				$err[$i]['err_code'] = 307;
				$err[$i]['err_msg'] = JText::_('COM_BWPOSTMAN_NL_ERROR_REPLY_EMAIL_INVALID');
				$i++;
			}
		}

		// Check for valid subject
		if (trim($data['subject']) == '') {
			$err[$i]['err_code'] = 304;
			$err[$i]['err_msg'] = JText::_('COM_BWPOSTMAN_NL_ERROR_SUBJECT');
			$i++;
		}

		// Check for valid html or text version
		if ((trim($data['html_version']) == '') && (trim($data['text_version']) == ''))  {
			$err[$i]['err_code'] = 305;
			$err[$i]['err_msg'] = JText::_('COM_BWPOSTMAN_NL_ERROR_HTML_AND_TEXT');
		}
		return $data;
	}

	/**
	 * Method to check if there are selected mailinglists and if they contain recipients
	 *
	 * @access	public
	 * 
	 * @param	string	Error message
	 * @param	int		newsletter id
	 * @param	boolean	Status --> 0 = do not send to unconfirmed, 1 = sent also to unconfirmed
	 * @param	int		campaign id
	 * 
	 * @return 	object Test-recipients data
	 */
	public function checkRecipients(&$ret_msg, $nl_id, $send_to_unconfirmed, $cam_id)
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);
		
		if ($cam_id != '-1') {
			// Check if there are assigned mailinglists or usergroups
			$query	= $_db->getQuery(true);
			$query->select($_db->quoteName('mailinglist_id'));
			$query->from($_db->quoteName('#__bwpostman_campaigns_mailinglists'));
			$query->where($_db->quoteName('campaign_id') . ' = ' . (int) $cam_id);
		}
		else {
			// Check if there are assigned mailinglists or usergroups of the campaign
			$query	= $_db->getQuery(true);
			$query->select($_db->quoteName('mailinglist_id'));
			$query->from($_db->quoteName('#__bwpostman_newsletters_mailinglists'));
			$query->where($_db->quoteName('newsletter_id') . ' = ' . (int) $nl_id);
		}
		
		$_db->setQuery($query);

		$mailinglists = $_db->loadObjectList();
		
		if (!$mailinglists) {
			$ret_msg = JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_NL_NO_LISTS');
			return false;
		}
			
		$check_subscribers		= 0;
		$check_allsubscribers	= 0;
		$count_users			= 0;
		$usergroup				= array();
		
		foreach ($mailinglists as $mailinglist){
			$mailinglist_id = $mailinglist->mailinglist_id;
			// Mailinglists
			if ($mailinglist_id > 0) $check_subscribers = 1;
			// All subscribers
			if ($mailinglist_id == -1) {
				$check_allsubscribers = 1;
			}
			else {
				// Usergroups
				if ((int) $mailinglist_id < 0) $usergroup[] = -(int) $mailinglist_id;
			}
		}

		// Check if the subscribers are confirmed and not archived
		if ($check_subscribers){ // Check subscribers from selected mailinglists
				
			if ($send_to_unconfirmed) {
				$status = '0,1';
			} else {
				$status = '1';
			}
			
			$subQuery1	= $_db->getQuery(true);
			$subQuery2	= $_db->getQuery(true);
			$query		= $_db->getQuery(true);
			
			if ($cam_id != '-1') {
				// Check if there are assigned mailinglists or usergroups
				$subQuery2->select($_db->quoteName('mailinglist_id'));
				$subQuery2->from($_db->quoteName('#__bwpostman_campaigns_mailinglists'));
				$subQuery2->where($_db->quoteName('campaign_id') . ' = ' . (int) $cam_id);
			}
			else {
				$subQuery2->select($_db->quoteName('mailinglist_id'));
				$subQuery2->from($_db->quoteName('#__bwpostman_newsletters_mailinglists'));
				$subQuery2->where($_db->quoteName('newsletter_id') . ' IN (' . (int) $nl_id . ')');
			}
			
			$subQuery1->select('DISTINCT' . $_db->quoteName('subscriber_id'));
			$subQuery1->from($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
			$subQuery1->where($_db->quoteName('mailinglist_id') . ' IN (' . $subQuery2 . ')');
			
			$query->select('COUNT(' . $_db->quoteName('id') . ')');
			$query->from($_db->quoteName('#__bwpostman_subscribers'));
			$query->where($_db->quoteName('id') . ' IN (' . $subQuery1 . ')');
			$query->where($_db->quoteName('status') . ' IN (' . $status . ')');
			$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);
			
			$_db->setQuery($query);
			
			$count_subscribers = $_db->loadResult();
		} 
		elseif ($check_allsubscribers){ // Check all subscribers (select option "All subscribers")
				
			if ($send_to_unconfirmed) {
				$status = '0,1,9';
			} else {
				$status = '1,9';
			}
				
			$query		= $_db->getQuery(true);
			
			$query->select('COUNT(' . $_db->quoteName('id') . ')');
			$query->from($_db->quoteName('#__bwpostman_subscribers'));
			$query->where($_db->quoteName('status') . ' IN (' . $status . ')');
			$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);
			
			$_db->setQuery($query);
			
			$count_subscribers = $_db->loadResult();
		}

		// Checks if the selected usergroups contain users
		if (is_array($usergroup) && count($usergroup)){
			$count_users = 0;
			$query		= $_db->getQuery(true);
			$sub_query	= $_db->getQuery(true);
			
			$sub_query->select($_db->quoteName('g') . '.' . $_db->quoteName('user_id'));
			$sub_query->from($_db->quoteName('#__user_usergroup_map') . ' AS ' . $_db->quoteName('g'));
			$sub_query->where($_db->quoteName('g') . '.' . $_db->quoteName('group_id') . ' IN (' . implode(',', $usergroup) . ')');
			
			$query->select('COUNT(' . $_db->quoteName('u') . '.' . $_db->quoteName('id') . ')');
			$query->from($_db->quoteName('#__users') . ' AS ' . $_db->quoteName('u'));
			$query->where($_db->quoteName('u') . '.' . $_db->quoteName('block') . ' = ' . (int) 0);
			$query->where($_db->quoteName('u') . '.' . $_db->quoteName('activation') . ' = ' . $_db->Quote(''));
			$query->where($_db->quoteName('u') . '.' . $_db->quoteName('id') . ' IN (' . $sub_query . ')');
			
			$_db->setQuery($query);
			$count_users = $_db->loadResult();
		}

		// We return only false, if no subscribers AND no joomla users are selected.
		if (!$count_users && !$count_subscribers){
			if (!$count_users) {
				$ret_msg = JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_NL_NO_USERS');
				return false;
			}
			if (!$count_subscribers) {
				$ret_msg = JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_NL_NO_SUBSCRIBERS');
				return false;
			}
		}
		return true;
	}

	/**
	 * Method to check if there are test-recipients to whom the newsletter shall be send
	 *
	 * @access	public
	 * @return 	boolean
	 */
	public function checkTestrecipients()
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);
		
		$query->select('COUNT(' . $_db->quoteName('id') . ')');
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('status') . ' = ' . (int) 9);
		$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);
		
		$_db->setQuery($query);
		
		$testrecipients = $_db->loadResult();

		if ($testrecipients) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Method to compose a newsletter out of the selected content items
	 *
	 * @access	public
	 * @return 	associcative array of Content data
	 */
	public function composeNl()
	{
		$jinput	= JFactory::getApplication()->input;
		
		$nl_content			= $jinput->get('selected_content');
		$nl_subject			= $jinput->get('subject');
		$template_id		= $jinput->get('template_id'); 
		$text_template_id	= $jinput->get('text_template_id');
		$renderer			= new contentRenderer();
		$content			= $renderer->getContent($nl_content, $nl_subject, $template_id, $text_template_id);
		
		return $content;
	}

	/**
	 * Method to fetch the content out of the selected content items
	 *
	 * @access	public
	 * @return 	associcative array of Content data
	 */
	public function changeTab()
	{
		$app				= JFactory::getApplication();
		$jinput				= JFactory::getApplication()->input;
		$form_data			= $jinput->get('jform', '', 'array');
		$layout				= $jinput->get('layout', '', 'string');
		$add_content		= $jinput->get('add_content', 0);
		$sel_content		= $jinput->get('selected_content_old', '', 'string');
		$old_template		= $jinput->get('template_id_old', '', 'string');
		$old_text_template	= $jinput->get('text_template_id_old', '', 'string');
		$state_data			= $app->getUserState('com_bwpostman.edit.newsletter.data');

		// heal form fields
		switch ($layout) {
			case 'edit_basic':
					$form_data['html_version']			= $state_data->html_version;
					$form_data['text_version']			= $state_data->text_version;
				break;
			case 'edit_html':
					$form_data['attachment']			= $state_data->attachment;
					$form_data['text_version']			= $state_data->text_version;
					$form_data['campaign_id']			= $state_data->campaign_id;
					$form_data['usergroups']			= $state_data->usergroups;
					$form_data['template_id']			= $state_data->template_id;
					$form_data['text_template_id']		= $state_data->text_template_id;
					(property_exists($state_data, 'ml_available')) ? $form_data['ml_available']		= $state_data->ml_available : '';
					(property_exists($state_data, 'ml_unavailable')) ? $form_data['ml_unavailable']	= $state_data->ml_unavailable : '';
					(property_exists($state_data, 'ml_intern')) ? $form_data['ml_intern']			= $state_data->ml_intern : '';
				break;
			case 'edit_text':
					$form_data['attachment']			= $state_data->attachment;
					$form_data['html_version']			= $state_data->html_version;
					$form_data['campaign_id']			= $state_data->campaign_id;
					$form_data['usergroups']			= $state_data->usergroups;
					$form_data['template_id']			= $state_data->template_id;
					$form_data['text_template_id']		= $state_data->text_template_id;
					(property_exists($state_data, 'ml_available')) ? $form_data['ml_available']		= $state_data->ml_available : '';
					(property_exists($state_data, 'ml_unavailable')) ? $form_data['ml_unavailable']	= $state_data->ml_unavailable : '';
					(property_exists($state_data, 'ml_intern')) ? $form_data['ml_intern']			= $state_data->ml_intern : '';
				break;
			case 'edit_preview':
					$form_data['attachment']			= $state_data->attachment;
					$form_data['html_version']			= $state_data->html_version;
					$form_data['text_version']			= $state_data->text_version;
					$form_data['campaign_id']			= $state_data->campaign_id;
					$form_data['usergroups']			= $state_data->usergroups;
					$form_data['template_id']			= $state_data->template_id;
					$form_data['text_template_id']		= $state_data->text_template_id;
					(property_exists($state_data, 'ml_available')) ? $form_data['ml_available']		= $state_data->ml_available : '';
					(property_exists($state_data, 'ml_unavailable')) ? $form_data['ml_unavailable']	= $state_data->ml_unavailable : '';
					(property_exists($state_data, 'ml_intern')) ? $form_data['ml_intern']			= $state_data->ml_intern : '';
				break;
			case 'edit_send':
					$form_data['attachment']			= $state_data->attachment;
					$form_data['html_version']			= $state_data->html_version;
					$form_data['text_version']			= $state_data->text_version;
					$form_data['campaign_id']			= $state_data->campaign_id;
					$form_data['usergroups']			= $state_data->usergroups;
					$form_data['template_id']			= $state_data->template_id;
					$form_data['text_template_id']		= $state_data->text_template_id;
					(property_exists($state_data, 'ml_available')) ? $form_data['ml_available']		= $state_data->ml_available : '';
					(property_exists($state_data, 'ml_unavailable')) ? $form_data['ml_unavailable']	= $state_data->ml_unavailable : '';
					(property_exists($state_data, 'ml_intern')) ? $form_data['ml_intern']			= $state_data->ml_intern : '';
				break;
			default:
					$form_data['html_version']			= $state_data->html_version;
					$form_data['text_version']			= $state_data->text_version;
					$form_data['campaign_id']			= $state_data->campaign_id;
					$form_data['usergroups']			= $state_data->usergroups;
					$form_data['template_id']			= $state_data->template_id;
					$form_data['text_template_id']		= $state_data->text_template_id;
					(property_exists($state_data, 'ml_available')) ? $form_data['ml_available']		= $state_data->ml_available : '';
					(property_exists($state_data, 'ml_unavailable')) ? $form_data['ml_unavailable']	= $state_data->ml_unavailable : '';
					(property_exists($state_data, 'ml_intern')) ? $form_data['ml_intern']			= $state_data->ml_intern : '';
				break;
		}
		
		if (array_key_exists('selected_content', $form_data) !== true)	$form_data['selected_content'] = array();
		if (array_key_exists('usergroups', $form_data) !== true)		$form_data['usergroups'] = array();
		
		// serialize selected_content
		$nl_content	= (array) $form_data['selected_content'];
		if (is_array($form_data['selected_content'])) $form_data['selected_content']	= implode(',', $form_data['selected_content']);
		
		// some content or template has changed?
		if ($add_content) {
			if (($sel_content != $form_data['selected_content']) || ($old_template != $form_data['template_id']) || ($old_text_template != $form_data['text_template_id'])) {
				if ($add_content == '-1'  && (count($nl_content) == 0)) $nl_content =  (array) "-1";
				
				// only render new content, if selection from article list has changed
				$renderer	= new contentRenderer();
				$content	= $renderer->getContent($nl_content, $form_data['subject'], $form_data['template_id'], $form_data['text_template_id']);
								
				$form_data['html_version']	= $content['html_version'];
				$form_data['text_version']	= $content['text_version'];
						
				// add intro to form data
				if ($sel_content != $form_data['selected_content'] || $old_template != $form_data['template_id']) {
					$tpl = self::getTemplate($form_data['template_id']);
					$form_data['intro_headline']	= $tpl->intro['intro_headline'];
					$form_data['intro_text']		= $tpl->intro['intro_text'];
				}
				if ($sel_content != $form_data['selected_content'] || $old_text_template != $form_data['text_template_id']) {
					$tpl = self::getTemplate($form_data['text_template_id']);
					$form_data['intro_text_headline']	= $tpl->intro['intro_headline'];
					$form_data['intro_text_text']		= $tpl->intro['intro_text'];
				}
				$form_data['template_id_old']		= $form_data['template_id']; 
				$form_data['text_template_id_old']	= $form_data['text_template_id'];
			}
		}
		else {
			$form_data['selected_content']	= $state_data->selected_content;
			// if change of content not confirmed don't change template_id
			$form_data['template_id']		= $state_data->template_id;
			$form_data['text_template_id']	= $state_data->text_template_id;
		}
		
		// convert form data to object to update state
		$data = new stdClass();
		foreach ($form_data as $k => $v)
		{
			$data->$k = $v;
		}
		
		$app->setUserState('com_bwpostman.edit.newsletter.data', $data);
		$app->setUserState('com_bwpostman.edit.newsletter.changeTab', true);

		return;
	}
		
	/**
	 * Method to prepare the sending of a newsletter
	 *
	 * @access	public
	 *
	 * @param	string	Error message
	 * @param 	string	Recipient --> either recipients or test-recipients
	 * @param 	int		Newsletter ID
	 * @param 	boolean	Send to unconfirmed or not
	 * @param	int		campaign id
	 * 
	 * @return	boolean	False if there occured an error
	 */
	public function sendNewsletter(&$ret_msg, $recipients, $nl_id, $unconfirmed, $cam_id)
	{
		// Prepare the newsletter content
		$id	= $this->_addSendMailContent($nl_id, $recipients);
		if ($id	=== false) {
			$ret_msg	= JText::_('COM_BWPOSTMAN_NL_ERROR_CONTENT_PREPARING');
			return false;
		}

		// Prepare the recipient queue
		if (!$this->_addSendMailQueue($ret_msg, $id, $recipients, $nl_id, $unconfirmed, $cam_id))
			return false;

		// Update the newsletters table, to prevent repeated sending of the newsletter
		if ($recipients == 'recipients') {
			$tblNewsletters = $this->getTable('newsletters', 'BwPostmanTable');
			$tblNewsletters->markAsSent($nl_id);
		}
		return true;
		
		// The actual sending of the newsletter is executed only in
		// Sendmail Queue layout.
	}

	/**
	 * Method to reset the count of sending attempts in sendmailqueue.
	 *
	 * @return unknown_type
	 */
	public function resetSendAttempts()
	{
		$tblSendmailQueue = $this->getTable('sendmailqueue', 'BwPostmanTable');
		$tblSendmailQueue->resetTrials();
	}

	/**
	 * Method to get the selected content
	 *
	 * @access	public
	 * 
	 * @return	string
	 * 
	 */
	public function _selectedContent()
	{
		$_db	= $this->_db;
		
		// Get selected content from the newsletters-Table
		$query	= $_db->getQuery(true);
		
		$query->select($_db->quoteName('selected_content'));
		$query->from($_db->quoteName('#__bwpostman_newsletters'));
		$query->where($_db->quoteName('id') . ' = ' . (int) $this->getState($this->getName() . '.id'));
		
		$_db->setQuery($query);
		$content_ids = $_db->loadResult();

		return $content_ids;
	}
	
	/**
	 * Method to replace the links in a newsletter to provide the correct preview
	 *
	 * @access	private
	 * 
	 * @param 	string HTML-/Text-version
	 * 
	 * @return 	boolean
	 * 
	 */
	static private function _replaceLinks(&$text)
	{
		$search_str = '/\s+(href|src)\s*=\s*["\']?\s*(?!http|mailto|#)([\w\s&%=?#\/\.;:_-]+)\s*["\']?/i';
		$text = preg_replace($search_str,' ${1}="'.JURI::root().'${2}"',$text);
		return true;
	}
	
	/**
	 * Method to get the template settings which are used to compose a newsletter
	 *
	 * @access	public
	 * 
	 * @return	array
	 * 
	 * @since	1.1.0
	 */
	public function getTemplate(&$template_id)
	{
		$params = JComponentHelper::getParams('com_bwpostman');
		if (is_null($template_id)) $template_id = '1';
		
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);
		$query->select($_db->quoteName('id'));
		$query->select($_db->quoteName('tpl_html'));
		$query->select($_db->quoteName('tpl_css'));
		$query->select($_db->quoteName('tpl_article'));
		$query->select($_db->quoteName('tpl_divider'));
		$query->select($_db->quoteName('tpl_id'));
		$query->select($_db->quoteName('basics'));
		$query->select($_db->quoteName('article'));
		$query->select($_db->quoteName('intro'));
		$query->from($_db->quoteName('#__bwpostman_templates'));
		$query->where($_db->quoteName('id') . ' = ' . $template_id);

		$_db->setQuery($query);
		$tpl = $_db->loadObject();

		if (is_string($tpl->basics)){
			$registry = new JRegistry;
			$registry->loadString($tpl->basics);
			$tpl->basics = $registry->toArray();
		}

		if (is_string($tpl->article)){
			$registry = new JRegistry;
			$registry->loadString($tpl->article);
			$tpl->article = $registry->toArray();
		}

		if (is_string($tpl->intro)){
			$registry = new JRegistry;
			$registry->loadString($tpl->intro);
			$tpl->intro = $registry->toArray();
		}
		
		// only for old templates
		if (empty($tpl->article)) {
			$tpl->article['show_createdate'] = $params->get('newsletter_show_createdate');
			$tpl->article['show_author'] = $params->get('newsletter_show_author');
			$tpl->article['show_readon'] = 1;
		}
		return $tpl;
	}

	/**
	 * Method to add the Template-Tags to the content
	 *
	 * @access	private
	 * 
	 * @return 	boolean
	 * 
	 * @since	1.1.0
	 */
	private function _addTplTags (&$text, &$id)
	{
		$tpl = self::getTemplate($id);
				
		$newtext		= $tpl->tpl_html."\n";

		$text			= str_replace('[%content%]', $text, $newtext);
		
		return true;

	}

	/**
	 * Method to replace edit and unsubscribe link
	 *
	 * @access	private
	 *
	 * @return 	boolean
	 *
	 * @since	1.1.0
	 */
	private function _replaceTplLinks (&$text)
	{

		// replace edit and unsubscribe link
		$replace1	= '<a href="[EDIT_HREF]">' . JText::_('COM_BWPOSTMAN_TPL_UNSUBSCRIBE_LINK_TEXT') . '</a>';
		$text		= str_replace('[%unsubscribe_link%]', $replace1, $text);
		$replace2	= '<a href="[EDIT_HREF]">' . JText::_('COM_BWPOSTMAN_TPL_EDIT_LINK_TEXT') . '</a>';
		$text		= str_replace('[%edit_link%]', $replace2, $text);

		return true;
	}

	/**
	 * Method to add the HTML-Tags and the css to the HTML-Newsletter
	 *
	 * @access	private
	 * 
	 * @param 	text HTML newsletter
	 * 
	 * @return 	boolean
	 */
	private function _addHtmlTags (&$text, &$id)
	{
		$params = JComponentHelper::getParams('com_bwpostman');
		$tpl = self::getTemplate($id);
		
		$newtext = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
		$newtext .= '<html>'."\n";  
		$newtext .= ' <head>'."\n";
		$newtext .= '   <title>Newsletter</title>'."\n";
		$newtext .= '   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'."\n";
		$newtext .= '   <meta name="robots" content="noindex,nofollow" />'."\n";
		$newtext .= '   <meta property="og:title" content="HTML Newsletter" />'."\n";
		$newtext .= '   <style type="text/css">'."\n";
		$newtext .= '   '.$tpl->tpl_css."\n";
			// only for old newsletters with template_id < 1
		if ($id < 1 && $params->get('use_css_for_html_newsletter') == 1) {
			$params	= JComponentHelper::getParams('com_bwpostman');
			$css	= $params->get('css_for_html_newsletter');
			$newtext .= '   '.$css."\n";
		}
		$newtext .= '   </style>'."\n";
		$newtext .= ' </head>'."\n";
		
		if (isset($tpl->basics['paper_bg'])) {
			$newtext .= ' <body bgcolor="'. $tpl->basics['paper_bg'] .'" emb-default-bgcolor="'. $tpl->basics['paper_bg'] .'" style="background-color:'. $tpl->basics['paper_bg'] .';color:'. $tpl->basics['legal_color'] .';">'."\n";
		}
		else {
			$newtext .= ' <body bgcolor="#ffffff" emb-default-bgcolor="#ffffff">'."\n";
		}
		$newtext .= $text."\n";
		$newtext .= ' </body>'."\n";
		$newtext .= '</html>'."\n";

 		$text =  $newtext;
		
		return true;
	}

	/**
	 * Method to add the HTML-footer to the HTML-Newsletter
	 *
	 * @access	private
	 * 
	 * @param 	text HTML newsletter
	 * 
	 * @return 	boolean
	 * 
	 */
	private function _addHTMLFooter(&$text, &$id)
	{
		$uri  				= JFactory::getURI();
		$params 			= JComponentHelper::getParams('com_bwpostman');
		$impressum			= "<br /><br />" . $params->get('legal_information_text');
		$impressum			= nl2br($impressum, true);
		
		if (strpos($text, '[%impressum%]') !== false) {
			// replace [%impressum%]
			$replace = "<br /><br />" . JText::sprintf('COM_BWPOSTMAN_NL_FOOTER_HTML', $uri->root()) . $impressum;
			$replace3  = '   <table id="legal" cellspacing="0" cellpadding="0" border="0" style="table-layout: fixed; width: 100%;"><tbody>';
			$replace3 .= '     <tr>'."\n";
			$replace3 .= '       <td  id="legal_td">'."\n";
			$replace3 .= '         <table class="one-col legal" style="border-collapse: collapse;border-spacing: 0;"><tbody>'."\n";
			$replace3 .= '          <tr>'."\n";
			$replace3 .= '           <td class="legal_td">'."\n";
			$replace3 .= $replace."<br /><br />\n";
			$replace3 .= '           </td>'."\n";
			$replace3 .= '          </tr>'."\n";
			$replace3 .= '         </tbody></table>'."\n";
			$replace3 .= '       </td>'."\n";
			$replace3 .= '     </tr>'."\n";
			$replace3 .= '   </tbody></table>'."\n";
			$text = str_replace('[%impressum%]', $replace3, $text);
		}
		// only for old newsletters with template_id < 1
		if ($id < 1) {
			$replace = JText::_('COM_BWPOSTMAN_NL_FOOTER_HTML_LINE') . JText::sprintf('COM_BWPOSTMAN_NL_FOOTER_HTML', $uri->root()) . $impressum;
			$text = str_replace("[dummy]", "<div class=\"footer-outer\"><p class=\"footer-inner\">{$replace}</p></div>", $text);
		}
		
		return true;
	}

	/**
	 * Method to add the TEXT to the TEXT-Newsletter
	 *
	 * @access	private
	 * 
	 * @param 	text Text newsletter
	 * 
	 * @return 	boolean
	 * 
	 * @since	1.1.0
	 */
	private function _addTextTpl (&$text, &$id)
	{
		$tpl	= self::getTemplate($id);

		$text	= str_replace('[%content%]', "\n" . $text, $tpl->tpl_html);
		
	return true;
	}

	/**
	 * Method to replace edit and unsubscribe link
	 *
	 * @access	private
	 *
	 * @return 	boolean
	 *
	 * @since	1.1.0
	 */
	private function _replaceTextTplLinks (&$text)
	{
		$uri  				= JFactory::getURI();
		$itemid_unsubscribe	= $this->getItemid('register');
		$itemid_edit		= $this->getItemid('edit');

		// replace edit and unsubscribe link
//		$replace1	= '+ ' . JText::_('COM_BWPOSTMAN_TPL_UNSUBSCRIBE_LINK_TEXT') . " +\n  " . $uri->root() . 'index.php?option=com_bwpostman&amp;Itemid='. $itemid_unsubscribe . '&amp;view=register&amp;task=unsubscribe&amp;email=[UNSUBSCRIBE_EMAIL]&amp;code=[UNSUBSCRIBE_CODE]';
		$replace1	= '+ ' . JText::_('COM_BWPOSTMAN_TPL_UNSUBSCRIBE_LINK_TEXT') . " +\n  " . $uri->root() . 'index.php?option=com_bwpostman&amp;Itemid='. $itemid_edit . '&amp;view=edit&amp;task=unsub&amp;editlink=[EDITLINK]';
		$text		= str_replace('[%unsubscribe_link%]', $replace1, $text);
		$replace2	= '+ ' . JText::_('COM_BWPOSTMAN_TPL_EDIT_LINK_TEXT') . " +\n  " . $uri->root() . 'index.php?option=com_bwpostman&amp;Itemid='. $itemid_edit . '&amp;view=edit&amp;editlink=[EDITLINK]';
		$text		= str_replace('[%edit_link%]', $replace2, $text);

	return true;
	}

	/**
	 * Method to add the footer Text-Newsletter
	 *
	 * @access	private
	 * 
	 * @param 	text Text newsletter
	 * 
	 * @return 	boolean
	 * 
	 */
	private function _addTextFooter (&$text, &$id)
	{
		$uri  				= JFactory::getURI();
		$itemid_unsubscribe	= $this->getItemid('register');
		$itemid_edit		= $this->getItemid('edit');
		$params 			= JComponentHelper::getParams('com_bwpostman');
		$impressum			= "\n\n" . $params->get('legal_information_text') . "\n\n";

		if (strpos($text, '[%impressum%]') !== false) {
			// replace [%impressum%]
			$replace	= "\n\n" . JText::sprintf('COM_BWPOSTMAN_NL_FOOTER_TEXT', $uri->root(), $uri->root(), $itemid_unsubscribe, $uri->root(), $itemid_edit) . $impressum;
			$text		= str_replace('[%impressum%]', $replace, $text);
		}
		// only for old newsletters with template_id < 1
		if ($id < 1) {
			$replace	= JText::_('COM_BWPOSTMAN_NL_FOOTER_TEXT_LINE') . JText::sprintf('COM_BWPOSTMAN_NL_FOOTER_TEXT', $uri->root(), $uri->root(), $itemid_unsubscribe, $uri->root(), $itemid_edit) . $impressum;
			$text		= str_replace("[dummy]", $replace, $text);
		}
		
		return true;
	}

	/**
	 * Method to get ID of actual content ID of a newsletter from content table
	 *
	 * @access	private
	 * 
	 * @param 	int 	newsletter ID
	 * 
	 * @return 	int		content ID
	 * 
	 */
	private function _getSingleContentId($nl_id)
	{
		$app	= JFactory::getApplication();
		$ret	= array();
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__bwpostman_sendmailcontent'));
		$query->where($_db->quoteName('nl_id') . ' = ' . (int) $nl_id);
		$_db->setQuery($query);

		if (!$_db->query()) {
			$app->enqueueMessage($_db->getErrorMsg(), 'error');
		}
		
		$result = $_db->loadResult(); 
		
		return $result;
	}
	
	
	/**
	 * Wenn ein Newsletter versendet werden soll, dann wird er als eine Art
	 * Archiv- und Verlaufsfunktion komplett mit Inhalt, Subject & Co. in
	 * die Tabelle sendMailContent eingefuegt
	 *
	 * @access	private
	 * 
	 * @param 	int		Newsletter ID
	 * @param 	string	Recipient --> either recipients or test-recipients
	 * 
	 * @return 	void	int content ID, if everything went fine, else boolean false
	 * 
	 */
	private function _addSendMailContent($nl_id, $recipients)
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);
		
		// Get the SendmailContent ID
		$content_id = $this->_getSingleContentId($nl_id);
		
		// We load our data from newsletters table. This data is already checked for errors
		$tblNewsletters = $this->getTable('newsletters', 'BwPostmanTable');

		if ($nl_id){
			$query->select('*');
			$query->from($_db->quoteName('#__bwpostman_newsletters'));
			$query->where($_db->quoteName('id') . ' = ' . (int) $nl_id);
			
			$_db->setQuery($query);
			
			$newsletters_data = $_db->loadObject();
		}
		else {
			return false;
		}

		// Initialize the sendmailContent
		$tblSendmailContent = $this->getTable('sendmailcontent', 'BwPostmanTable');
		if ($content_id > 0) {
			$id = $content_id;
		}
		
		// Copy the data from newsletters to sendmailContent
		$tblSendmailContent->nl_id 			= $newsletters_data->id;
		$tblSendmailContent->from_name 		= $newsletters_data->from_name;
		$tblSendmailContent->from_email 	= $newsletters_data->from_email;
		$tblSendmailContent->subject 		= $newsletters_data->subject;
		$tblSendmailContent->attachment		= $newsletters_data->attachment;
		$tblSendmailContent->cc_email 		= null;
		$tblSendmailContent->bcc_email 		= null;
		$tblSendmailContent->reply_email 	= $newsletters_data->reply_email;
		$tblSendmailContent->reply_name	 	= $newsletters_data->from_name;

		// Preprocess html version of the newsletter

		// only for old text templates
		if ($newsletters_data->template_id < 1) {
			$newsletters_data->html_version = $newsletters_data->html_version . '[dummy]';
		}
		// add template data
		if (!$this->_addTplTags($newsletters_data->html_version, $newsletters_data->template_id)) return false;

		// Replace the intro
		if (!empty($newsletters_data->intro_headline)) $newsletters_data->html_version	= str_replace('[%intro_headline%]', $newsletters_data->intro_headline, $newsletters_data->html_version);
		if (!empty($newsletters_data->intro_text)) $newsletters_data->html_version		= str_replace('[%intro_text%]', nl2br($newsletters_data->intro_text, true), $newsletters_data->html_version);

		if (!$this->_replaceTplLinks($newsletters_data->html_version)) return false;
		if (!$this->_addHtmlTags($newsletters_data->html_version, $newsletters_data->template_id)) return false;
		if (!$this->_addHtmlFooter($newsletters_data->html_version, $newsletters_data->template_id)) return false;
		if (!$this->_replaceLinks($newsletters_data->html_version)) return false;
		
		// Preprocess text version of the newsletter
		// only for old text templates
		if ($newsletters_data->text_template_id < 1) {
			$newsletters_data->text_version = $newsletters_data->text_version . '[dummy]';
		}
		// add template data
		if (!$this->_addTextTpl($newsletters_data->text_version, $newsletters_data->text_template_id)) return false;

		// Replace the intro
		if (!empty($newsletters_data->intro_text_headline)) $newsletters_data->text_version	= str_replace('[%intro_headline%]', $newsletters_data->intro_text_headline, $newsletters_data->text_version);
		if (!empty($newsletters_data->intro_text_text)) $newsletters_data->text_version		= str_replace('[%intro_text%]', $newsletters_data->intro_text_text, $newsletters_data->text_version);

		if (!$this->_replaceTextTplLinks($newsletters_data->text_version)) return false;
		if (!$this->_addTextFooter($newsletters_data->text_version, $newsletters_data->text_template_id)) return false;
		if (!$this->_replaceLinks($newsletters_data->text_version)) return false;
		
		// We have to create two entries in the sendmailContent table. One entry for the textmail body and one for the htmlmail.
		for ($mode = 0;$mode <= 1; $mode++){

			// Set the body and the id, if exists
			if ($mode == 0) {
				$tblSendmailContent->body = $newsletters_data->text_version;
			}
			else {
				$tblSendmailContent->body = $newsletters_data->html_version;
			}

			// Set the mode (0=text,1=html)
			$tblSendmailContent->mode = $mode;

			// Store the data into the sendmailcontent-table
			// First run generates a new id, which will be used also for the second run.
			if (!$tblSendmailContent->store()) {
				return false;
			}
		}

		$id = $tblSendmailContent->id;

		return $id;
	}

	/**
	 * Method to push the recipients into a queue
	 *
	 * @access	private
	 *
	 * @param	string	Error message
	 * @param 	int		Content ID -->  --> from the sendmailcontent-Table
	 * @param 	string	Recipient --> either subscribers or test-recipients
	 * @param 	int		Newsletter ID
	 * @param	boolean	Status --> 0 = do not send to unconfirmed, 1 = sent also to unconfirmed
	 * @param	int		campaign id
	 *
	 * @return 	boolean False if there occured an error
	 */
	private function _addSendMailQueue(&$ret_msg, $content_id, $recipients, $nl_id, $send_to_unconfirmed, $cam_id)
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);
		
		if (!$content_id) return false;

		if (!$nl_id){
			$ret_msg	= JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_TECHNICAL_REASON');
			return false;
		}

		switch ($recipients){
			case "recipients": // Contain subscribers and joomla users
				$tblSendmailQueue = $this->getTable('sendmailqueue', 'BwPostmanTable');
				
				if ($cam_id != '-1') {
					$query->select($_db->quoteName('mailinglist_id'));
					$query->from($_db->quoteName('#__bwpostman_campaigns_mailinglists'));
					$query->where($_db->quoteName('campaign_id') . ' = ' . (int) $cam_id);
				}
				else {
					$query->select($_db->quoteName('mailinglist_id'));
					$query->from($_db->quoteName('#__bwpostman_newsletters_mailinglists'));
					$query->where($_db->quoteName('newsletter_id') . ' = ' . (int) $nl_id);
				}
				
				$_db->setQuery($query);
				
				$mailinglists = $_db->loadObjectList();

				if (!$mailinglists) {
					$ret_msg	= JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_NO_MAILINGLISTS');
					return false;
				}

				$send_subscribers = 0;
				$send_to_all = 0;
				$users = array();

				foreach ($mailinglists as $mailinglist){
					$mailinglist_id = $mailinglist->mailinglist_id;
					// Mailinglists
					if ($mailinglist_id > 0) $send_subscribers = 1;
					// All subscribers
					if ($mailinglist_id == -1) {
						$send_to_all = 1;
					}
					else {
						// Usergroups
						if ((int) $mailinglist_id < 0) $users[] = -(int) $mailinglist_id;
					}
				}
				
				if ($send_to_all) {
					if ($send_to_unconfirmed) {
						$status = '0,1,9';
					}
					else {
						$status = '1,9';
					}
					if (!$tblSendmailQueue->pushAllSubscribers($content_id, $status)){
						$ret_msg	= JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_TECHNICAL_REASON');
						return false;
					}
				}

				if (count($users)){
					$params = JComponentHelper::getParams('com_bwpostman');
					if (!$tblSendmailQueue->pushJoomlaUser($content_id, $users, $params->get('default_emailformat'))){
						$ret_msg	= JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_TECHNICAL_REASON');
						return false;
					}
				}
				
				if ($send_subscribers){
					if ($send_to_unconfirmed) {
						$status = '0,1';
					}
					else {
						$status = '1';
					}
					if (!$tblSendmailQueue->pushAllFromNlId($nl_id, $content_id, $status, $cam_id)){
						$ret_msg	= JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_TECHNICAL_REASON');
						return false;
					}
				}
			break;

			case "testrecipients":
				$tblSubscribers		= $this->getTable('subscribers', 'BwPostmanTable');
				$testrecipients		= $tblSubscribers->loadTestrecipients();
				$tblSendmailQueue	= $this->getTable('sendmailqueue', 'BwPostmanTable');
				
				if(sizeof($testrecipients) > 0){
					foreach($testrecipients AS $testrecipient)
					$tblSendmailQueue->push($content_id, $testrecipient->emailformat, $testrecipient->email, $testrecipient->name, $testrecipient->firstname, $testrecipient->id);
				}
				break;

			default:
				$ret_msg	= JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_TECHNICAL_REASON');
		}
		return true;
	}

	/**
	 * Check number of trials
	 * 
	 * param	int		trial
	 * 
	 * @return	bool	true if no entries or there are entries with number trials less than 2, otherwise false
	 * 
	 * since 1.0.3
	 */
	public function checkTrials($trial = 2, $count = 0)
	{
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);
		
		$query->select('COUNT(' . $_db->quoteName('id') . ')');
		$query->from($_db->quoteName('#__bwpostman_sendmailqueue'));
		
		$_db->setQuery($query);

		// returns only number of entries
		if ($count != 0) return $_db->loadResult();

		// queue not empty
		if ($_db->loadResult() != 0){
			$query->where($_db->quoteName('trial') . ' < ' . (int) $trial);
			$_db->setQuery($query);
			// all queue entries have trial number 2
			if ($_db->loadResult() == 0) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Make partial send. Send only, say like 50 newsletters and the next 50 in a next call.
	 * 
	 * @param $mailsToSend
	 * @param int 	mode --> 0 = regular sending, 1 = auto sending
	 * 
	 * @return int	0 -> queue is empty, 1 -> maximum reached
	 */
	public function sendMailsFromQueue($mailsPerStep = 100)
	{
		$sendMailCounter = 0;
		echo JText::_('COM_BWPOSTMAN_NL_SENDING_PROCESS');
		ob_flush();
		flush();

		while(1){
			$ret = $this->sendMail(true);
			if ($ret == 0){                              // Queue is empty!
				return 0;
				break;
			}
			$sendMailCounter++;
			if ($sendMailCounter >= $mailsPerStep) {     // Maximum is reached.
				return 1;
				break;
			}
		}
	}

	/**
	 * Funktion zum Senden *eines* Newsletters an einen Empfaenger aus der sendMailQueue.
	 * ACHTUNG! Es wird immer mit dem ersten Eintrag angefangen zu senden.
	 *
	 * @param	bool 	true if we came from component
	 * 
	 * @return	int		(-1, if there was an error; 0, if no mail adresses left in the queue; 1, if one Mail was send).
	 */
	public function sendMail($fromComponent = false)
	{
		// intitialize
		$app				= JFactory::getApplication();
		$uri  				= JFactory::getURI();
		$itemid_unsubscribe	= $this->getItemid('register');
		$itemid_edit		= $this->getItemid('edit');
		
		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('bwpostman');
		
		$res				= false;
		$_db				= $this->_db;
		$query				= $_db->getQuery(true);
		$table_name			= '#__bwpostman_sendmailqueue';
		$recipients_data	= new stdClass();

		// getting object for queue and content
		$tblSendMailQueue	= $this->getTable('sendmailqueue', 'BwPostmanTable');
		$tblSendMailContent	= $this->getTable('sendmailcontent', 'BwPostmanTable');
		
		// trigger BwTimeControl event, if we came not from component
		// needed for changing table objects for queue and content, show/hide messages, ... 
		if (!$fromComponent) {
			$dispatcher->trigger('onBwPostmanBeforeNewsletterSend', array(&$table_name, &$tblSendMailQueue, &$tblSendMailContent));
		}

		// Get first entry from sendmailqueue
		// Nothing has been returned, so the queue should be empty
		if (!$tblSendMailQueue->pop()) return 0;
		
		// rewrite some property names
		if (property_exists($tblSendMailQueue, 'tc_content_id')) $tblSendMailQueue->content_id	= $tblSendMailQueue->tc_content_id; 
		if (property_exists($tblSendMailQueue, 'email')) $tblSendMailQueue->recipient	= $tblSendMailQueue->email; 
		
		$app->setUserState('com_bwpostman.newsletter.send.mode', $tblSendMailQueue->mode);
		
 		// Get Data from sendmailcontent, set attacchment path (TODO, store data in this class to prevent from loding every time a mail will be sent)
		$app->setUserState('bwtimecontrol.mode', $tblSendMailQueue->mode);
		$tblSendMailContent->load($tblSendMailQueue->content_id);
		
		$tblSendMailContent->attachment = JPATH_SITE . '/' . $tblSendMailContent->attachment;
		if (property_exists($tblSendMailContent, 'email')) $tblSendMailContent->content_id	= $tblSendMailContent->id;
		
		// check if subscriber is archived
		if ($tblSendMailQueue->subscriber_id) {
			$query->from('#__bwpostman_subscribers');
			$query->select('id');
			$query->select('editlink');
			$query->select('archive_flag');
			$query->select('status');
			$query->where('id = ' . (int) $tblSendMailQueue->subscriber_id);
			$_db->setQuery($query);
			if (!$_db->query()) {
				$app->enqueueMessage($_db->getErrorMsg());
				return FALSE;
			}
			$recipients_data = $_db->loadObject();
			
			// if subscriber is archived, delete entry from queue
			if ($recipients_data->archive_flag) {
				$query->clear();
				$query->from($_db->quoteName($table_name));
				$query->delete();
				$query->where($_db->quoteName('subscriber_id') .' = ' . (int) $recipient_data->id);
				$_db->setQuery((string) $query);
				
				$_db->query();
									
				return 1;
			}
		} // end archived-check

		$body = $tblSendMailContent->body;
		// Replace the links to provide the correct preview
		$footerid = 0;
		if ($tblSendMailQueue->mode == 1) { // HTML newsletter
			if ($tblSendMailQueue->subscriber_id) { // Add footer only if it is a subscriber
				$this->_replaceTplLinks($body);
				$this->_addHTMLFooter($body, $footerid);
			}
			else {
				$body = str_replace("[%edit_link%]", "", $body);
				$body = str_replace("[%unsubscribe_link%]", "", $body);
				$body = str_replace("[%impressum%]", "", $body);
				$body = str_replace("[dummy]", "", $body);
			}
		} else { // Text newsletter
			if ($tblSendMailQueue->subscriber_id) {	// Add footer only if it is a subscriber
				$this->_replaceTextTplLinks($body);
				$this->_addTextFooter($body, $footerid);
			}
			else {
				$body = str_replace("[%edit_link%]", "", $body);
				$body = str_replace("[%unsubscribe_link%]", "", $body);
				$body = str_replace("[%impressum%]", "", $body);
				$body = str_replace("[dummy]", "", $body);
			}
		}
		$this->_replaceLinks($body);
		
		$fullname = '';
		if ($tblSendMailQueue->firstname != '') $fullname = $tblSendMailQueue->firstname . ' ';
		if ($tblSendMailQueue->name != '') $fullname .= $tblSendMailQueue->name;
		$fullname = trim($fullname);
		
		// Replace the dummies
		$body = str_replace("[NAME]", $tblSendMailQueue->name, $body);
		$body = str_replace("[LASTNAME]", $tblSendMailQueue->name, $body);
		$body = str_replace("[FIRSTNAME]", $tblSendMailQueue->firstname, $body);
		$body = str_replace("[FULLNAME]", $fullname, $body);
		// do not replace CODE by testrecipients
		if (isset($recipients_data->status) && $recipients_data->status != 9) {
			if (property_exists($recipients_data, 'editlink')) {
				$body = str_replace("[UNSUBSCRIBE_HREF]", JText::sprintf('COM_BWPOSTMAN_NL_UNSUBSCRIBE_HREF', $uri->root(), $itemid_unsubscribe), $body);
				$body = str_replace("[EDIT_HREF]", JText::sprintf('COM_BWPOSTMAN_NL_EDIT_HREF', $uri->root(), $itemid_edit), $body);
				$body = str_replace("[UNSUBSCRIBE_EMAIL]", $tblSendMailQueue->recipient, $body);
				$body = str_replace("[UNSUBSCRIBE_CODE]", $recipients_data->editlink, $body);
				$body = str_replace("[EDITLINK]", $recipients_data->editlink, $body);
			}
		}
		
		// Send Mail
		// show queue working only wanted if sending newsletters from component backend directly, not in timecontrolled sending
		if ($fromComponent) {
			echo "\n<br>{$tblSendMailQueue->recipient} (".JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_TRIAL').($tblSendMailQueue->trial + 1).") ... ";
			ob_flush();
			flush();
		}

		// Get a JMail instance
		$mailer		= JFactory::getMailer();
		$sender		= array();
		$reply		= array();
		
		$sender[0]	= $tblSendMailContent->from_email;
		$sender[1]	= $tblSendMailContent->from_name;

		$reply[0]	= $tblSendMailContent->reply_email;
		$reply[1]	= $tblSendMailContent->reply_name;
				
		$mailer->setSender($sender);
		$mailer->addReplyTo($reply);
		$mailer->addRecipient($tblSendMailQueue->recipient);
		$mailer->setSubject($tblSendMailContent->subject);
		$mailer->setBody($body);
		$mailer->addAttachment($tblSendMailContent->attachment);

		if ($tblSendMailQueue->mode == 1) {
			$mailer->isHTML(true);
			if (BWPOSTMAN_NL_ENCODING)
				$mailer->Encoding = 'base64';
		}
		
		if (BWPOSTMAN_NL_SENDING)
			$res = $mailer->Send();
		
		if ($res === true) {
			if ($fromComponent) {
				echo JText::_('COM_BWPOSTMAN_NL_SENT_SUCCESSFULLY');
			}
			else {
				// Sendmail was successfull, flag "sent" in table TcContent has to be set
				$tblSendMailContent->setSent($tblSendMailContent->id);
				// and test-entries may be deleted
				if ($recipients_data->status == 9) {
					// @todo delete entry in content-table?
				}
			}
		}
		else{
			// Sendmail was not successfull, we need to add the recipient to the queue again.
			if ($fromComponent) {
				// show message only wanted if sending newsletters from component backend directly, not in timecontrolled sending
				echo JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING');
			}
			$tblSendMailQueue->push($tblSendMailQueue->content_id, $tblSendMailQueue->mode, $tblSendMailQueue->recipient, $tblSendMailQueue->name, $tblSendMailQueue->firstname, $tblSendMailQueue->subscriber_id, $tblSendMailQueue->trial + 1);
			return -1;
		}
		return 1;
	}
}

/**
 * Content Renderer Class
 * Provides methodes render the selected contents from which the newsletters shall be generated
 * --> Refering to BwPostman 1.6 beta and Communicator 2.0.0rc1 (??)
 *
 * @package		BwPostman-Admin
 * @subpackage	Newsletters
 */
class contentRenderer
{
	/**
	 * Method to get the menu item ID for the content item
	 *
	 * @access	public
	 * @return 	int menu item ID
	 */
	public function getItemid($row)
	{
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);
		
		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__menu'));
		$query->where($_db->quoteName('link') . ' = ' . $_db->Quote('index.php?option=com_bwpostman&view=' . $row));
		$query->where($_db->quoteName('published') . ' = ' . (int) 1);
		
		$_db->setQuery($query);
		
		$itemid = $_db->loadResult();

		if (empty($itemid)) {
			$query	= $_db->getQuery(true);
			
			$query->select($_db->quoteName('id'));
			$query->from($_db->quoteName('#__menu'));
			$query->where($_db->quoteName('link') . ' = ' . $_db->Quote('index.php?option=com_bwpostman&view=register'));
			$query->where($_db->quoteName('published') . ' = ' . (int) 1);
			
			$_db->setQuery($query);

			$itemid = $_db->loadResult();
		}

		return $itemid;
	}

	/**
	 * This is the main function to render the content from an ID to HTML
	 *
	 * @param array		$nl_content
	 * @param string	$nl_subject
	 * 
	 * @return string	content
	 */
	public function getContent($nl_content, $nl_subject, $template_id, $text_template_id) {
		global $params;
		
		$param = JComponentHelper::getParams('com_bwpostman');

		$app		= JFactory::getApplication();
		$model		= new BwPostmanModelNewsletter();
		$tpl		= $model->getTemplate($template_id);
		$text_tpl	= $model->getTemplate($text_template_id);

		// only for old templates
		if ($template_id < 1) {
			$content['html_version'] = '<div class="outer"><div class="header"><img class="logo" src="'.JRoute::_(JURI::root().$param->get('logo')).'" alt="" /></div><div class="content-outer"><div class="content"><div class="content-inner"><p class="nl-intro">&nbsp;</p>';
		}
		else {
			$content['html_version'] = '';
		}
				
		$content['text_version'] = '';
		
		if ($nl_content == null) {
			$content['html_version'] .= '';
			$content['text_version'] .= '';
				
		}
		else {
			foreach($nl_content as $content_id){
				if ($tpl->tpl_id && $template_id > 0){
					$content['html_version'] .= $this->replaceContentHtmlNew($content_id, $tpl);
					if (($tpl->article['divider'] == 1) && ($content_id != end($nl_content)))  $content['html_version'] = $content['html_version'] . $tpl->tpl_divider;
				}
				else {
					$content['html_version'] .= $this->replaceContentHtml($content_id, $tpl);
				}

				if ($text_tpl->tpl_id && $text_template_id > 0){
					$content['text_version'] .= $this->replaceContentTextNew($content_id, $text_tpl);
					if (($text_tpl->article['divider'] == 1) && ($content_id != end($nl_content)))  $content['text_version'] = $content['text_version'] . $text_tpl->tpl_divider . "\n\n";
				}
				else {
					$content['text_version'] .= $this->replaceContentText($content_id, $text_tpl);
				}
			}
		}
		
			// only for old templates
		if ($template_id < 1) {
			$content['html_version'] .= '</div></div></div></div>';
		}

		return $content;

	}

	public function retrieveContent($id) {
		$app	= JFactory::getApplication();
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);
		
		$query->select($_db->quoteName('a') . '.*');
		$query->select('ROUND(v.rating_sum/v.rating_count) AS ' . $_db->quoteName('rating'));
		$query->select($_db->quoteName('v') . '.' . $_db->quoteName('rating_count'));
		$query->select($_db->quoteName('u') . '.' . $_db->quoteName('name') . ' AS ' . $_db->quoteName('author'));
		$query->select($_db->quoteName('cc') . '.' . $_db->quoteName('title') . ' AS ' . $_db->quoteName('category'));
		$query->select($_db->quoteName('s') . '.' . $_db->quoteName('title') . ' AS ' . $_db->quoteName('section'));
		$query->select($_db->quoteName('g') . '.' . $_db->quoteName('title') . ' AS ' . $_db->quoteName('groups'));
		$query->select($_db->quoteName('s') . '.' . $_db->quoteName('published') . ' AS ' . $_db->quoteName('sec_pub'));
		$query->select($_db->quoteName('cc') . '.' . $_db->quoteName('published') . ' AS ' . $_db->quoteName('cat_pub'));
		$query->from($_db->quoteName('#__content') . ' AS ' . $_db->quoteName('a'));
		$query->join('LEFT', $_db->quoteName('#__categories') . ' AS ' . $_db->quoteName('cc') . ' ON ' . $_db->quoteName('cc') . '.' . $_db->quoteName('id') . ' = ' . $_db->quoteName('a') . '.' . $_db->quoteName('catid'));
		$query->join('LEFT', $_db->quoteName('#__categories') . ' AS ' . $_db->quoteName('s') . ' ON ' . $_db->quoteName('s') . '.' . $_db->quoteName('id') . ' = ' . $_db->quoteName('cc') . '.' . $_db->quoteName('parent_id') . 'AND' . $_db->quoteName('s') . '.' . $_db->quoteName('extension') . ' = ' . $_db->Quote('com_content'));
		$query->join('LEFT', $_db->quoteName('#__users') . ' AS ' . $_db->quoteName('u') . ' ON ' . $_db->quoteName('u') . '.' . $_db->quoteName('id') . ' = ' . $_db->quoteName('a') . '.' . $_db->quoteName('created_by'));
		$query->join('LEFT', $_db->quoteName('#__content_rating') . ' AS ' . $_db->quoteName('v') . ' ON ' . $_db->quoteName('a') . '.' . $_db->quoteName('id') . ' = ' . $_db->quoteName('v') . '.' . $_db->quoteName('content_id'));
		$query->join('LEFT', $_db->quoteName('#__usergroups') . ' AS ' . $_db->quoteName('g') . ' ON ' . $_db->quoteName('a') . '.' . $_db->quoteName('access') . ' = ' . $_db->quoteName('g') . '.' . $_db->quoteName('id'));
		$query->where($_db->quoteName('a') . '.' . $_db->quoteName('id') . ' = ' . (int) $id);
		
		$_db->setQuery($query);
		$row = $_db->loadObject();

		if($row) {
			$params = new JRegistry();
			$params->loadString($row->attribs, 'JSON');
				
			$params->def('link_titles',	$app->getCfg('link_titles'));
			$params->def('author', 		$params->get('newsletter_show_author'));
			$params->def('createdate', 	$params->get('newsletter_show_createdate'));
			$params->def('modifydate', 	!$app->getCfg('hideModifyDate'));
			$params->def('print', 		!$app->getCfg('hidePrint'));
			$params->def('pdf', 		!$app->getCfg('hidePdf'));
			$params->def('email', 		!$app->getCfg('hideEmail'));
			$params->def('rating', 		$app->getCfg('vote'));
			$params->def('icons', 		$app->getCfg('icons'));
			$params->def('readmore', 	$app->getCfg('readmore'));
			$params->def('item_title', 	1);
				
			$params->set('intro_only', 	1);
			$params->set('item_navigation', 0);
				
			$params->def('back_button', 	0);
			$params->def('image', 			1);
				
			$row->params = $params;
			$row->text = $row->introtext;
		}
		return $row;
	}

	public function replaceContentHtml($id, $tpl)
	{
		$app		= JFactory::getApplication();
		$content	= '';
			
		if($id != 0){

			// Editor user type check
			$access = new stdClass();
			$access->canEdit = $access->canEditOwn = $access->canPublish = 0;
				
			$row = $this->retrieveContent($id);
				
			if ($row) {
				$params		= $row->params;
				$model		= new BwPostmanModelNewsletter;
				$lang		= $model->getArticleLanguage($row->id);
				$_Itemid	= ContentHelperRoute::getArticleRoute($row->id, 0, $lang);
				$link		= JRoute::_(JURI::base());
				if ($_Itemid) $link .= $_Itemid;
				
//				$app->triggerEvent('onPrepareContent', array(&$row, &$params, 0), true);

				$intro_text = $row->text;

				if (intval($row->created) != 0) {
					$create_date = JHTML::_('date', $row->created);
				}
				$html_content = new HTML_content();

				ob_start();
				// Displays Item Title
				$html_content->Title($row, $params, $access);

				$content .= ob_get_contents();
				ob_end_clean();
				// Displays Category
				ob_start();
				//$html_content->Category($row, $params);


				// Displays Created Date
				if ($tpl->article['show_createdate'] != 0) $html_content->CreateDate($row, $params);

				// Displays Author Name
				if ($tpl->article['show_author'] != 0) $html_content->Author($row, $params);
				
				// Displays Urls
				//$html_content->URL($row, $params);
				$content .= ob_get_contents();
				ob_end_clean();

				$content .= '<div class="intro_text">'
				. $intro_text //(function_exists('ampReplace') ? ampReplace($intro_text) : $intro_text). '</td>'
				. '</div>';
			
				if ($tpl->article['show_readon'] != 0) {
					$content	.= '<div class="read_on">'
								. '		<p>'
								. '		<a href="'. str_replace('administrator/', '', $link) . '" class="readon">'
								. JText::_('READ_MORE')
								. '		</a><br/><br/>'
								. '		</p>'
								. '	</div>';
				}
								
				return stripslashes($content);
			}
		}
		else {
			return JText::sprintf('COM_BWPOSTMAN_NL_ERROR_RETRIEVING_CONTENT', $id);
		}
	}

	public function replaceContentHtmlNew($id, $tpl)
	{
		$app		= JFactory::getApplication();
		$content	= '';
	
		if($id != 0){
			// Editor user type check
			$access = new stdClass();
			$access->canEdit = $access->canEditOwn = $access->canPublish = 0;
				
			// $id = "-1" if no content is selected
			if ($id == '-1'){
				$content	.= $tpl->tpl_article;
				$content	= preg_replace( "/<table id=\"readon\".*?<\/table>/is", "", $content);
				$content	= str_replace('[%content_title%]', JText::_('COM_BWPOSTMAN_TPL_PLACEHOLDER_TITLE'), $content);
				$content	= str_replace('[%content_text%]', JText::_('COM_BWPOSTMAN_TPL_PLACEHOLDER_CONTENT'), $content);
				return stripslashes($content);
			}

			$row = $this->retrieveContent($id);
				
			if ($row) {
				$params		= $row->params;
				$model		= new BwPostmanModelNewsletter;
				$lang		= $model->getArticleLanguage($row->id);
				$_Itemid	= ContentHelperRoute::getArticleRoute($row->id, 0, $lang);
				$link		= JRoute::_(JURI::base());
				if ($_Itemid) $link .= $_Itemid;
				
//				$app->triggerEvent('onPrepareContent', array(&$row, &$params, 0), true);

				$intro_text = $row->text;

				if (intval($row->created) != 0) {
					$create_date = JHTML::_('date', $row->created);
				}
				
				$link = str_replace('administrator/', '', $link);
				
				$content		.= $tpl->tpl_article;
				$content		= str_replace('[%content_title%]', $row->title, $content);
				$content_text	= '';
				if (($tpl->article['show_createdate'] == 1) || ($tpl->article['show_author'] == 1)) :
					$content_text .= '<p>';
					if ($tpl->article['show_createdate'] == 1) :
						$content_text .= '<span><small>';
						$content_text .= JText::sprintf('COM_CONTENT_CREATED_DATE_ON', $create_date);
						$content_text .= '&nbsp;&nbsp;&nbsp;&nbsp;</small></span>';
					endif;
					if ($tpl->article['show_author'] == 1) :
						$content_text .= '<span><small>';
						$content_text .= JText::sprintf('COM_CONTENT_WRITTEN_BY',($row->created_by_alias ? $row->created_by_alias : $row->created_by));
						$content_text .= '</small></span>';
					endif;
					$content_text .= '</p>';
				endif;
				$content_text	.= $intro_text;
				$content  		= str_replace('[%content_text%]', $content_text, $content);
				$content  		= str_replace('[%readon_href%]', $link, $content);
				$content  		= str_replace('[%readon_text%]', JText::_('READ_MORE'), $content);
				
				return stripslashes($content);
			}
		}
		else {
			return JText::sprintf('COM_BWPOSTMAN_NL_ERROR_RETRIEVING_CONTENT', $id);
		}
	}
	
	public function replaceContentTextNew($id, $text_tpl){
		$app = JFactory::getApplication();

		if($id != 0){
			$row = $this->retrieveContent($id);
				
			if ($row) {
				$params = $row->params;

				$model		= new BwPostmanModelNewsletter;
				$lang		= $model->getArticleLanguage($row->id);
				$_Itemid	= ContentHelperRoute::getArticleRoute($row->id, 0, $lang);
				$link		= JRoute::_(JURI::base());
				if ($_Itemid) $link .= $_Itemid;
								
//				$app->triggerEvent('onPrepareContent', array(&$row, &$params, 0), true);

				$intro_text = $row->text;
				$intro_text = strip_tags($intro_text);

				$intro_text = $this->unHTMLSpecialCharsAll($intro_text);

				if (intval($row->created) != 0) {
					$create_date = JHTML::_('date', $row->created);
				}

				$link = str_replace('administrator/', '', $link);
		
				$content		= $text_tpl->tpl_article;
				$content		= str_replace('[%content_title%]', $row->title , $content);
				$content_text	= "\n";
				if (($text_tpl->article['show_createdate'] == 1) || ($tpl->article['show_author'] == 1)) :
					if ($text_tpl->article['show_createdate'] == 1) :
					$content_text .= JText::sprintf('COM_CONTENT_CREATED_DATE_ON', $create_date);
					$content_text .= '    ';
					endif;
					if ($text_tpl->article['show_author'] == 1) :
						$content_text .= JText::sprintf('COM_CONTENT_WRITTEN_BY',($row->created_by_alias ? $row->created_by_alias : $row->created_by));
					endif;
					$content_text .= "\n\n";
				endif;
				$content_text	.= $intro_text;
				$content		= str_replace('[%content_text%]', $content_text."\n", $content);
				$content		= str_replace('[%readon_href%]', $link."\n", $content);
				$content		= str_replace('[%readon_text%]', JText::_('READ_MORE'), $content);
		
				return stripslashes($content);
			}
		}
	}

	public function replaceContentText($id, $text_tpl){
		$app = JFactory::getApplication();
		
		if($id != 0){
			$row = $this->retrieveContent($id);
			
			if ($row) {
				$params = $row->params;
				
				$model		= new BwPostmanModelNewsletter;
				$lang		= $model->getArticleLanguage($row->id);
				$_Itemid	= ContentHelperRoute::getArticleRoute($row->id, 0, $lang);
				$link		= JRoute::_(JURI::base());
				if ($_Itemid) $link .= $_Itemid;
								
//				$app->triggerEvent('onPrepareContent', array(&$row, &$params, 0), true);

				$intro_text = $row->text;
				$intro_text = strip_tags($intro_text);

				$intro_text = $this->unHTMLSpecialCharsAll($intro_text);

				if (intval($row->created) != 0) {
					$create_date = JHTML::_('date', $row->created);
				}

				$content = "\n" . $row->title;
				
				$content_text = "";
				if (($text_tpl->article['show_createdate'] == 1) || ($tpl->article['show_author'] == 1)) :
					if ($text_tpl->article['show_createdate'] == 1) :
						$content_text .= JText::sprintf('COM_CONTENT_CREATED_DATE_ON', $create_date);
						$content_text .= '    ';
					endif;
					if ($text_tpl->article['show_author'] == 1) :
						$content_text .= JText::sprintf('COM_CONTENT_WRITTEN_BY',($row->created_by_alias ? $row->created_by_alias : $row->created_by));
					endif;
					$content_text .= "\n\n";
				endif;
				$intro_text = $content_text . $intro_text;

				$content .= "\n\n" . $intro_text . "\n\n";
				if ($text_tpl->article['show_readon'] == 1) $content .= JTEXT::_('READ_MORE') . ": \n". str_replace('administrator/', '', $link) . "\n\n";

				return stripslashes($content);
			}
		}
	}

	private function unHTMLSpecialCharsAll($text) {

		$text = $this->deHTMLEntities($text);

		return $text;
	}

	/**
	 * convert html special entities to literal characters
	 */
	private function deHTMLEntities($text) {
		$search = array(
		"'&(quot|#34);'i",
		"'&(amp|#38);'i",
		"'&(lt|#60);'i",
		"'&(gt|#62);'i",
		"'&(nbsp|#160);'i",   "'&(iexcl|#161);'i",  "'&(cent|#162);'i",   "'&(pound|#163);'i",  "'&(curren|#164);'i",
		"'&(yen|#165);'i",    "'&(brvbar|#166);'i", "'&(sect|#167);'i",   "'&(uml|#168);'i",    "'&(copy|#169);'i",
		"'&(ordf|#170);'i",   "'&(laquo|#171);'i",  "'&(not|#172);'i",    "'&(shy|#173);'i",    "'&(reg|#174);'i",
		"'&(macr|#175);'i",   "'&(neg|#176);'i",    "'&(plusmn|#177);'i", "'&(sup2|#178);'i",   "'&(sup3|#179);'i",
		"'&(acute|#180);'i",  "'&(micro|#181);'i",  "'&(para|#182);'i",   "'&(middot|#183);'i", "'&(cedil|#184);'i",
		"'&(supl|#185);'i",   "'&(ordm|#186);'i",   "'&(raquo|#187);'i",  "'&(frac14|#188);'i", "'&(frac12|#189);'i",
		"'&(frac34|#190);'i", "'&(iquest|#191);'i", "'&(Agrave|#192);'",  "'&(Aacute|#193);'",  "'&(Acirc|#194);'",
		"'&(Atilde|#195);'",  "'&(Auml|#196);'",    "'&(Aring|#197);'",   "'&(AElig|#198);'",   "'&(Ccedil|#199);'",
		"'&(Egrave|#200);'",  "'&(Eacute|#201);'",  "'&(Ecirc|#202);'",   "'&(Euml|#203);'",    "'&(Igrave|#204);'",
		"'&(Iacute|#205);'",  "'&(Icirc|#206);'",   "'&(Iuml|#207);'",    "'&(ETH|#208);'",     "'&(Ntilde|#209);'",
		"'&(Ograve|#210);'",  "'&(Oacute|#211);'",  "'&(Ocirc|#212);'",   "'&(Otilde|#213);'",  "'&(Ouml|#214);'",
		"'&(times|#215);'i",  "'&(Oslash|#216);'",  "'&(Ugrave|#217);'",  "'&(Uacute|#218);'",  "'&(Ucirc|#219);'",
		"'&(Uuml|#220);'",    "'&(Yacute|#221);'",  "'&(THORN|#222);'",   "'&(szlig|#223);'",   "'&(agrave|#224);'",
		"'&(aacute|#225);'",  "'&(acirc|#226);'",   "'&(atilde|#227);'",  "'&(auml|#228);'",    "'&(aring|#229);'",
		"'&(aelig|#230);'",   "'&(ccedil|#231);'",  "'&(egrave|#232);'",  "'&(eacute|#233);'",  "'&(ecirc|#234);'",
		"'&(euml|#235);'",    "'&(igrave|#236);'",  "'&(iacute|#237);'",  "'&(icirc|#238);'",   "'&(iuml|#239);'",
		"'&(eth|#240);'",     "'&(ntilde|#241);'",  "'&(ograve|#242);'",  "'&(oacute|#243);'",  "'&(ocirc|#244);'",
		"'&(otilde|#245);'",  "'&(ouml|#246);'",    "'&(divide|#247);'i", "'&(oslash|#248);'",  "'&(ugrave|#249);'",
		"'&(uacute|#250);'",  "'&(ucirc|#251);'",   "'&(uuml|#252);'",    "'&(yacute|#253);'",  "'&(thorn|#254);'",
		"'&(yuml|#255);'");
		$replace = array(
		"\"",
		"&",
		"<",
		">",
		" ",      chr(161), chr(162), chr(163), chr(164), chr(165), chr(166), chr(167), chr(168), chr(169),
		chr(170), chr(171), chr(172), chr(173), chr(174), chr(175), chr(176), chr(177), chr(178), chr(179),
		chr(180), chr(181), chr(182), chr(183), chr(184), chr(185), chr(186), chr(187), chr(188), chr(189),
		chr(190), chr(191), chr(192), chr(193), chr(194), chr(195), chr(196), chr(197), chr(198), chr(199),
		chr(200), chr(201), chr(202), chr(203), chr(204), chr(205), chr(206), chr(207), chr(208), chr(209),
		chr(210), chr(211), chr(212), chr(213), chr(214), chr(215), chr(216), chr(217), chr(218), chr(219),
		chr(220), chr(221), chr(222), chr(223), chr(224), chr(225), chr(226), chr(227), chr(228), chr(229),
		chr(230), chr(231), chr(232), chr(233), chr(234), chr(235), chr(236), chr(237), chr(238), chr(239),
		chr(240), chr(241), chr(242), chr(243), chr(244), chr(245), chr(246), chr(247), chr(248), chr(249),
		chr(250), chr(251), chr(252), chr(253), chr(254), chr(255));
		return $text = preg_replace($search, $replace, $text);
	}

}

/**
 * Utility class for writing the HTML for content
 * --> Refering to Communicator 2.0.0rc1
 *
 * @package 		BwPostman-Admin
 * @subpackage 	Newsletters
 */
class HTML_content {
	/**
	 * Writes Title
	 */
	public function Title(&$row, &$params, &$access) {
		if ($params->get('item_title')) {
			if ($params->get('link_titles') && $row->link_on != '') {
				?>
				<h2><a href="<?php echo $row->link_on;?>"
					class="contentpagetitle<?php echo $params->get('pageclass_sfx'); ?>">
								<?php echo $row->title;?></a></h2>
								<?php
							} else {
								?>
				<h2><?php echo $row->title;?></h2>
				<?php
			}
		}
	}

	/**
	 * Writes Category
	 */
	public function Category(&$row, &$params) {
		?>
		<span class="sc_category"><small> <?php
		echo $row->category;
		?></small></span>
		<?php
	}

	/**
	 * Writes Author name
	 */
	public function Author(&$row, &$params) {
		?>
		<span class="created_by"><small><?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY',($row->created_by_alias ? $row->created_by_alias : $row->created_by)); ?></small></span>
		<?php
	}
	

	/**
	 * Writes Create Date
	 */
	public function CreateDate(&$row, &$params) {
		$create_date = null;

		if (intval($row->created) != 0) {
			$create_date = JHTML::_('date', $row->created);
		}

		?>
		<span class="createdate"><small><?php echo JText::sprintf('COM_CONTENT_CREATED_DATE_ON', $create_date); ?>&nbsp;&nbsp;&nbsp;&nbsp;</small></span>
		<?php
	}

	/**
	 * Writes URL's
	 */
	public function URL(&$row, &$params) {
		if ($params->get('url') && $row->urls) {
			?>
			<p class="row_url"><a
				href="http://<?php echo $row->urls ; ?>" target="_blank"> <?php echo $row->urls; ?></a>
			</p>
			<?php
		}
	}

	/**
	 * Writes Modified Date
	 */
	public function ModifiedDate(&$row, &$params) {
		$mod_date = null;

		if (intval($row->modified) != 0) {
			$mod_date = JHTML::_('date', $row->modified);
		}

		if (($mod_date != '') && $params->get('modifydate')) {
			?>
			<p class="modifydate"><?php echo JTEXT::_('LAST_UPDATED'); ?>
			(<?php echo $mod_date; ?>)</p>
			<?php
		}
	}

	/**
	 * Writes Readmore Button
	 */
	public function ReadMore (&$row, &$params) {
		if ($params->get('readmore')) {
			if ($params->get('intro_only') && $row->link_text) {
				?>
				<p class="link_on"><a href="<?php echo $row->link_on;?>"
					class="readon<?php echo $params->get('pageclass_sfx'); ?>"> <?php echo $row->link_text;?></a>
				</p>
				<?php
			}
		}
	}
}