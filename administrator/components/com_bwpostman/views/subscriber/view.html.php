<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single html subscribers view for backend.
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

// Import VIEW object class
jimport('joomla.application.component.view');

// Require helper class
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/htmlhelper.php');

/**
 * BwPostman Subscriber View
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Subscribers
 */
class BwPostmanViewSubscriber extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;
	/**
	 * Display
	 * --> load View depending on the layout
	 *
	 * @access	public
	 * @param 	string Template
	 */
	public function display($tpl=null)
	{
		$app	= JFactory::getApplication();
		$jinput	= JFactory::getApplication()->input;
		$model	= $this->getModel();
		$params = JComponentHelper::getParams('com_bwpostman');

		//check for queue entries
		$this->queueEntries	= BwPostmanHelper::checkQueueEntries();

		$layout = $jinput->get('layout', '');

		switch ($layout) {
			case 'export':
				self::_displayExportForm($tpl);
				break;
			case 'import':
			case 'import1':
			case 'import2':
				self::_displayImportForm($tpl);
				break;
			case 'edit':
			default:
				// get templatename
				$this->template	= $app->getTemplate();

				// Get the data from the model
				$this->form		= $this->get('Form');
				$this->item		= $this->get('Item');
				$this->state	= $this->get('State');

				$this->canDo	= BwPostmanHelper::getActions($this->item->id, 'subscriber');
				if ($this->item->id) {
					$app->setUserState('com_bwpostman.subscriber.new_test', $this->item->status);
					$app->setUserState('com_bwpostman.subscriber.subscriber_id', $this->item->id);
				}

				// Get show fields
				if (!$params->get('show_name_field'))		$this->form->setFieldAttribute('name', 'type', 'hidden');
				if (!$params->get('show_firstname_field'))	$this->form->setFieldAttribute('firstname', 'type', 'hidden');
				if (!$params->get('show_emailformat')) {
					$this->form->setFieldAttribute('emailformat', 'type', 'hidden');
				}
				else {
					$this->form->setFieldAttribute('default_emailformat', 'default', $params->get('default_emailformat'));
				}

				// Set required fields
				$this->obligation['name']		= $params->get('name_field_obligation');
				$this->obligation['firstname']	= $params->get('firstname_field_obligation');
				if ($params->get('name_field_obligation')) 		$this->form->setFieldAttribute('name', 'required', 'true');
				if ($params->get('firstname_field_obligation'))	$this->form->setFieldAttribute('firstname', 'required', 'true');

				$this->addToolbar();
		}
		parent::display($tpl);
	}

	/**
	 * View Import Forms
	 *
	 * @access	private
	 * @param	string Template
	 */
	private function _displayImportForm($tpl)
	{
		$app		= JFactory::getApplication();
		$_db		= JFactory::getDBO();
		$params 	= JComponentHelper::getParams('com_bwpostman');
		$session 	= JFactory::getSession();
		$template	= $app->getTemplate();
		$uri		= JFactory::getURI();
		$uri_string	= str_replace('&', '&amp;', $uri->toString());

		$import					= array();
		$lists					= array();
		$session_delimiter		= ';';
		$session_enclosure		= '"';

		$app->setUserState('com_bwpostman.subscriber.import', true);

		// Get the data from the model
		$this->form		= $this->get('Form');
		$this->state	= $this->get('State');

		// Get general import data from the session (fileformat, filename ...)
		$import_general_data = $session->get('import_general_data');
		if(isset($import_general_data) && is_array($import_general_data)) $import = $import_general_data;

		// get the fileformat select list for the layouts import1 and import2
		$lists['fileformat']	= BwPostmanHTMLHelper::getFileFormatList(isset ($import['fileformat']) ? $import['fileformat'] : '');

		// Get the csv-delimiter select list for the layouts import1 and import2
		// Delimiter which is stored in the session
		if (isset($import['delimiter'])) $session_delimiter = $import['delimiter'];

		$lists['delimiter']	= BwPostmanHTMLHelper::getDelimiterList($session_delimiter);

		// Get the csv-enclosure select list for the layouts import1 and import2
		// Enclosure which is stored in the session
		if (isset($import['enclosure'])) $session_enclosure = $import['enclosure'];

		$lists['enclosure']	= BwPostmanHTMLHelper::getEnclosureList($session_enclosure);

		// Get the import database fields list for the layout import2
		$lists['db_fields']	= BwPostmanHTMLHelper::getDbFieldsList();

		// Build the select list for the importfile fields from the session object for the layout import2
		$import_fields = $session->get('import_fields');
		if (isset($import_fields)) {
			$lists['import_fields']	= JHTML::_('select.genericlist', $import_fields, 'import_fields[]', 'class="inputbox" size="10" multiple="multiple" style="padding: 6px; width: 260px;"', 'value', 'text');
		}

		// Get the emailformat select list for the layout import2
		$lists['emailformat']	= BwPostmanHTMLHelper::getMailFormatList($params->get('default_emailformat'));

		// Get import result data from the session for the layout import2
		$import_result = $session->get('import_result');
		if(isset($import_result) && is_array($import_result)){
			$result = $import_result;
		}

		// Save a reference into view
		$this->assignRef('import', $import);
		$this->assignRef('lists', $lists);
		$this->assignRef('request_url',	$uri_string);
		$this->assignRef('result', $result);
		$this->assignRef('template', $template);

		$this->addToolbar();
	}

	/**
	 * View Export Form
	 *
	 * @access	private
	 * @param 	string Template
	 */
	private function _displayExportForm($tpl)
	{
		$app = JFactory::getApplication();

		$_db		= JFactory::getDBO();
		$document	= JFactory::getDocument();
		$template	= $app->getTemplate();
		$uri		= JFactory::getURI();
		$uri_string	= str_replace('&', '&amp;', $uri->toString());

		// Get the select lists for the export_fields, file format, delimiter, enclosure
		$lists['export_fields']	= BwPostmanHTMLHelper::getExportFieldsList();
		$lists['fileformat']	= BwPostmanHTMLHelper::getFileFormatList();
		$lists['delimiter']		= BwPostmanHTMLHelper::getDelimiterList();
		$lists['enclosure']		= BwPostmanHTMLHelper::getEnclosureList();

		// We need a RAW-view for the export function
		$uri->setVar('format','raw');

		// Save a reference into view
		$this->assignRef('lists', $lists);
		$this->assignRef('request_url_raw',	$uri_string);
		$this->assignRef('template', $template);

		$this->addToolbar();
	}

	/**
	 * View Validation Form
	 *
	 * @access	private
	 * @param 	string Template
	 */
	private function _displayValidationForm($tpl)
	{
		$app		= JFactory::getApplication();
		$session 	= JFactory::getSession();
		$uri		= JFactory::getURI();
		$uri_string	= $uri->toString();

		// Get the result data from the session
		$validation_res = $session->get('validation_res');
		if (isset($validation_res) && (is_array($validation_res))) {
			$row    = new stdClass();
			foreach ($validation_res AS $key => $value) {
				$row->$key = $value;
			}
		}

		// Save a reference into view
		$this->assignRef('request_url',	str_replace('&', '&amp;', $uri_string));
		$this->assignRef('row', $row);

		// Get document object, set document title and add css
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_BWPOSTMAN_SUB_VALIDATION_RESULT'));
		$document->addStyleSheet(JURI::base(true) . '/components/com_bwpostman/assets/css/bwpostman_backend.css');

		// Set toolbar items
		JToolBarHelper::title(JText::_('COM_BWPOSTMAN_SUB_VALIDATION_RESULT'), 'subscribers');
		JToolBarHelper::cancel('subscriber.finishValidation', 'COM_BWPOSTMAN_SUB_VALIDATION_FINISH');

		// Set parent display
		parent::display($tpl);
	}

	/**
	 * Add the page title, styles and toolbar.
	 *
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$uri		= JFactory::getURI();
		$userId		= JFactory::getUser()->get('id');
		$layout		= JFactory::getApplication()->input->get('layout', '');
		$tester		= false;
		$status 	= 1;

		if (is_object($this->item)) {
			$status	= $this->item->status;
		}

		if (JFactory::getApplication()->getUserState('com_bwpostman.subscriber.new_test', $status) == '9') {
			$tester	= true;
		}

		// Get document object, set document title and add css
		$document	= JFactory::getDocument();
		$document->addStyleSheet(JURI::base(true) . '/components/com_bwpostman/assets/css/bwpostman_backend.css');

		$alt 	= "COM_BWPOSTMAN_BACK";
		$bar	= JToolBar::getInstance('toolbar');

		switch ($layout) {
			case 'export':
				// Get document object, set document title and add css
				$document->setTitle(JText::_('COM_BWPOSTMAN_SUB_EXPORT_SUBS'));

				// Set toolbar items
				JToolBarHelper::title(JText::_('COM_BWPOSTMAN_SUB_EXPORT_SUBS'), 'upload');
				JToolBarHelper::cancel('subscriber.cancel');
				break;

			case 'import':
				// Set toolbar items
				$document->setTitle(JText::_('COM_BWPOSTMAN_SUB_IMPORT_SUBS'));
				JToolBarHelper::title(JText::_('COM_BWPOSTMAN_SUB_IMPORT_SUBS'), 'download');
				JToolBarHelper::cancel('subscriber.cancel');
				break;

			case 'import1':
				$document->setTitle(JText::_('COM_BWPOSTMAN_SUB_IMPORT_SUBS'));
				$backlink 	= 'index.php?option=com_bwpostman&view=subscriber&layout=import';
				JToolBarHelper::title(JText::_('COM_BWPOSTMAN_SUB_IMPORT_SUBS'), 'download');
				$bar->appendButton('Link', 'arrow-left', $alt, $backlink);
				JToolBarHelper::cancel('subscriber.cancel');
				break;

			case 'import2':
				$document->setTitle(JText::_('COM_BWPOSTMAN_SUB_IMPORT_RESULT'));
				$backlink = 'index.php?option=com_bwpostman&view=subscriber&layout=import1';
				JToolBarHelper::title(JText::_('COM_BWPOSTMAN_SUB_IMPORT_RESULT'), 'info');
				$bar->appendButton('Link', 'arrow-left', $alt, $backlink);
				JToolBarHelper::cancel('subscriber.cancel');
				break;

			case 'edit':
			default:
				if ($tester) {
					$title	= (JText::_('COM_BWPOSTMAN_TEST_DETAILS'));
				}
				else {
					$title	= (JText::_('COM_BWPOSTMAN_SUB_DETAILS'));
				}
				$document->setTitle($title);

				// Set toolbar title and items
				$canDo			= BwPostmanHelper::getActions($this->item->id, 'subscriber');
				$checkedOut		= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
				$this->canDo	= $canDo;

				// Set toolbar title depending on the state of the item: Is it a new item? --> Create; Is it an existing record? --> Edit
				// For new records, check the create permission.
				if ($this->item->id < 1 && $canDo->get('core.create')) {
					JToolBarHelper::save('subscriber.save');
					JToolBarHelper::apply('subscriber.apply');
					JToolBarHelper::cancel('subscriber.cancel');
					JToolBarHelper::title($title .': <small>[ ' . JText::_('NEW').' ]</small>', 'plus');
				}
				else {
					// Can't save the record if it's checked out.
					if (!$checkedOut) {
						// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
						if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId)) {
							JToolBarHelper::save('subscriber.save');
							JToolBarHelper::apply('subscriber.apply');
						}
					}
					// Rename the cancel button for existing items
					JToolBarHelper::cancel('subscriber.cancel', 'JTOOLBAR_CLOSE');
					JToolBarHelper::title($title .': <small>[ ' . JText::_('EDIT').' ]</small>', 'edit');
				}

				$backlink 	= $_SERVER['HTTP_REFERER'];
				$siteURL 	= $uri->base();

				// If we came from the cover page we will show a back-button
				if ($backlink == $siteURL.'index.php?option=com_bwpostman') {
					JToolBarHelper::spacer();
					JToolBarHelper::divider();
					JToolBarHelper::spacer();
					JToolBarHelper::back();
				}
		}
		JToolBarHelper::spacer();
		JToolBarHelper::divider();
		JToolBarHelper::spacer();
		JToolBarHelper::help(JText::_("COM_BWPOSTMAN_FORUM"), false, 'http://www.boldt-webservice.de/forum/bwpostman.html');
	}
}
