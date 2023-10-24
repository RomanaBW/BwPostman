<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single html subscribers view for backend.
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

namespace BoldtWebservice\Component\BwPostman\Administrator\View\Subscriber;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Button\LinkButton;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHTMLHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * BwPostman Subscriber View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	Subscribers
 *
 * @since       0.9.1
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * property to hold form data
	 *
	 * @var array   $form
	 *
	 * @since       0.9.1
	 */
	protected $form;

	/**
	 * property to hold selected item
	 *
	 * @var object   $item
	 *
	 * @since       0.9.1
	 */
	protected $item;

	/**
	 * property to hold row object
	 *
	 * @var object   $row
	 *
	 * @since       0.9.1
	 */
	protected $row;

	/**
	 * property to hold state
	 *
	 * @var array|object  $state
	 *
	 * @since       0.9.1
	 */
	protected $state;

	/**
	 * property to hold obligation values
	 *
	 * @var array   $obligation
	 *
	 * @since       0.9.1
	 */
	protected $obligation;

	/**
	 * property to hold queue entries
	 *
	 * @var boolean $queueEntries
	 *
	 * @since       0.9.1
	 */
	public $queueEntries;

	/**
	 * property to hold template
	 *
	 * @var boolean $template
	 *
	 * @since       0.9.1
	 */
	public $template;

	/**
	 * property to hold import
	 *
	 * @var array $import
	 *
	 * @since       0.9.1
	 */
	public $import;

	/**
	 * property to hold lists
	 *
	 * @var array $lists
	 *
	 * @since       0.9.1
	 */
	public $lists;

	/**
	 * property to hold request url
	 *
	 * @var string $request_url
	 *
	 * @since       0.9.1
	 */
	public $request_url;

	/**
	 * property to hold raw format of request url
	 *
	 * @var string $request_url_raw
	 *
	 * @since       0.9.1
	 */
	public $request_url_raw;

	/**
	 * property to hold permissions as array
	 *
	 * @var array $permissions
	 *
	 * @since       2.0.0
	 */
	public $permissions;

	/**
	 * property to hold result
	 *
	 * @var string $result
	 *
	 * @since       0.9.1
	 */
	public $result;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  HtmlView  A string if successful, otherwise a JError object.
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function display($tpl=null): HtmlView
	{
		$app	= Factory::getApplication();
		$jinput	= $app->input;
		$params = ComponentHelper::getParams('com_bwpostman');

		$this->permissions		= $app->getUserState('com_bwpm.permissions', []);

		if (!$this->permissions['view']['subscriber'])
		{
			$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', Text::_('COM_BWPOSTMAN_SUB')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		//check for queue entries
		$this->queueEntries	= BwPostmanHelper::checkQueueEntries();

		$layout = $jinput->get('layout', '');

		switch ($layout)
		{
			case 'export':
				self::displayExportForm();
				break;
			case 'import':
			case 'import1':
			case 'import2':
				self::displayImportForm();
				break;
			case 'edit':
			default:
				// get template name
				$this->template	= $app->getTemplate();

				// Get the data from the model
				$this->form		= $this->get('Form');
				$this->item		= $this->get('Item');
				$this->state	= $this->get('State');

				if ($this->item->id)
				{
					$app->setUserState('com_bwpostman.subscriber.new_test', $this->item->status);
					$app->setUserState('com_bwpostman.subscriber.subscriber_id', $this->item->id);
				}

				// Get show fields
				if (!$params->get('show_name_field', '1') && !$params->get('name_field_obligation', '1'))
				{
					$this->form->setFieldAttribute('name', 'type', 'hidden');
				}

				if (!$params->get('show_firstname_field', '1') && !$params->get('firstname_field_obligation', '1'))
				{
					$this->form->setFieldAttribute('firstname', 'type', 'hidden');
				}

				if (!$params->get('show_gender', '1'))
				{
					$this->form->setFieldAttribute('gender', 'type', 'hidden');
				}

				if (!$params->get('show_special', '1') && !$params->get('special_field_obligation', '0'))
				{
					$this->form->setFieldAttribute('special', 'type', 'hidden');
				}

				if (!$params->get('show_emailformat', '1'))
				{
					$this->form->setFieldAttribute('emailformat', 'type', 'hidden');
				}
				else
				{
					$this->form->setFieldAttribute('default_emailformat', 'default', $params->get('default_emailformat', '1'));
				}

				// Set required fields
				$this->obligation['name']		    = $params->get('name_field_obligation', '1');
				$this->obligation['firstname']  	= $params->get('firstname_field_obligation', '1');
				$this->obligation['special']	    = $params->get('special_field_obligation', '0');
				$this->obligation['special_label']	= Text::_($params->get('special_label', ''));

				if ($params->get('name_field_obligation', '1'))
				{
					$this->form->setFieldAttribute('name', 'required', 'true');
				}

				if ($params->get('firstname_field_obligation', '1'))
				{
					$this->form->setFieldAttribute('firstname', 'required', 'true');
				}

				if ($params->get('special_field_obligation', '0'))
				{
					$this->form->setFieldAttribute('special', 'required', 'true');
				}

				// Set label and description/tooltip for additional field
				if ($params->get('special_desc', '') != '')
				{
					$this->form->setFieldAttribute('special', 'description', Text::_($params->get('special_desc', '')));
				}

				if ($params->get('special_label', '') != '')
				{
					$this->form->setFieldAttribute('special', 'label', Text::_($params->get('special_label', '')));
				}

			$this->addToolbar();
		}

		parent::display($tpl);

		return $this;
	}

	/**
	 * View Import Forms
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	private function displayImportForm()
	{
		$app		= Factory::getApplication();
		$params 	= ComponentHelper::getParams('com_bwpostman');
		$session 	= $app->getSession();
		$template	= $app->getTemplate();
		$uri		= Uri::getInstance();
		$uri_string	= str_replace('&', '&amp;', $uri->toString());

		$import					= array();
		$lists					= array();
		$session_delimiter		= ';';
		$session_enclosure		= '"';
		$result                 = true;

		$app->setUserState('com_bwpostman.subscriber.import', true);

		// Get the data from the model
		$this->form		= $this->get('Form');
		$this->state	= $this->get('State');

		// Get general import data from the session (fileformat, filename ...)
		$import_general_data = $session->get('import_general_data');
		if(isset($import_general_data) && is_array($import_general_data))
		{
			$import = $import_general_data;
		}

		// get the fileformat select list for the layouts import1 and import2
		$lists['fileformat']	= BwPostmanHTMLHelper::getFileFormatList();

		// Get the csv-delimiter select list for the layouts import1 and import2
		// Delimiter which is stored in the session
		if (isset($import['delimiter']))
		{
			$session_delimiter = $import['delimiter'];
		}

		$lists['delimiter']	= BwPostmanHTMLHelper::getDelimiterList($session_delimiter);

		// Get the csv-enclosure select list for the layouts import1 and import2
		// Enclosure which is stored in the session
		if (isset($import['enclosure']))
		{
			$session_enclosure = $import['enclosure'];
		}

		$lists['enclosure']	= BwPostmanHTMLHelper::getEnclosureList($session_enclosure);

		// Get the import database fields list for the layout import2
		$lists['db_fields']	= BwPostmanHTMLHelper::getDbFieldsList();

		// Build the select list for the importfile fields from the session object for the layout import2
		$import_fields = $session->get('import_fields');
		if (isset($import_fields))
		{
			$lists['import_fields']	= HTMLHelper::_(
				'select.genericlist',
				$import_fields,
				'import_fields[]',
				'class="custom-select w-auto" size="10" multiple multiple="multiple"',
				'value',
				'text'
			);
		}

		// Get the emailformat select list for the layout import2
		$lists['emailformat']	= BwPostmanHTMLHelper::getMailFormatList($params->get('default_emailformat', '1'));

		// Get import result data from the session for the layout import2
		$import_result = $session->get('com_bwpostman.subscriber.import.messages', null);

		if(isset($import_result) && is_array($import_result))
		{
			$result = $import_result;
		}

//		Cleanup session messages
		$session->set('com_bwpostman.subscriber.import.messages', null);

		// Save a reference into view
		$this->import       = $import;
		$this->lists        = $lists;
		$this->request_url  = $uri_string;
		$this->result       = $result;
		$this->template     = $template;

		if ($this->getLayout() === 'import2')
		{
			$session->clear('com_bwpostman.subscriber.import.messages');
			$session->clear('import_fields');
			$session->clear('import_general_data');
		}

			$this->addToolbar();
	}

	/**
	 * View Export Form
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	private function displayExportForm()
	{
		$app = Factory::getApplication();

		$template	= $app->getTemplate();
		$uri		= Uri::getInstance();
		$uri_string	= str_replace('&', '&amp;', $uri->toString());

		// Get the select lists for the export_fields, file format, delimiter, enclosure
		$lists['export_fields']	= BwPostmanHTMLHelper::getExportFieldsList();
		$lists['fileformat']	= BwPostmanHTMLHelper::getFileFormatList();
		$lists['delimiter']		= BwPostmanHTMLHelper::getDelimiterList();
		$lists['enclosure']		= BwPostmanHTMLHelper::getEnclosureList();

		// We need a RAW-view for the export function
		$uri->setVar('format', 'raw');

		// Save a reference into view
		$this->lists            = $lists;
		$this->request_url_raw  = $uri_string;
		$this->template         = $template;

		$this->addToolbar();
	}

	/**
	 * Add the page title, styles and toolbar.
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	protected function addToolbar()
	{
		$app    = Factory::getApplication();
		$app->input->set('hidemainmenu', true);
		$uri    = Uri::getInstance();
		$userId = $app->getIdentity()->get('id');
		$layout = $app->input->get('layout', '');
		$tester = false;
		$status = 1;

		if (is_object($this->item)) {
			$status	= $this->item->status;
		}

		if ($app->getUserState('com_bwpostman.subscriber.new_test', $status) == '9') {
			$tester	= true;
		}

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance();

		$this->document->getWebAssetManager()->useScript('com_bwpostman.admin-bwpm_subscriber');

		switch ($layout)
		{
			case 'export':
				// Set toolbar items
				ToolbarHelper::title(Text::_('COM_BWPOSTMAN_SUB_EXPORT_SUBS'), 'upload');

				$toolbar->cancel('subscriber.cancel');
				break;

			case 'import':
				ToolbarHelper::title(Text::_('COM_BWPOSTMAN_SUB_IMPORT_SUBS'), 'download');

				$toolbar->cancel('subscriber.cancel');
				break;

			case 'import1':
				ToolbarHelper::title(Text::_('COM_BWPOSTMAN_SUB_IMPORT_SUBS'), 'download');

				$options['text'] = "COM_BWPOSTMAN_BACK";
				$options['name'] = 'back';
				$options['url'] = "index.php?option=com_bwpostman&view=subscriber&layout=import";
				$options['icon'] = "icon-arrow-left";

				$button = new LinkButton('back');
				$button->setOptions($options);

				$toolbar->appendButton($button);
				$toolbar->cancel('subscriber.cancel');
				break;

			case 'import2':
				ToolbarHelper::title(Text::_('COM_BWPOSTMAN_SUB_IMPORT_RESULT'), 'info');

				$toolbar->cancel('subscriber.cancel');
				break;

			case 'edit':
			default:
				if ($tester) {
					$title	= (Text::_('COM_BWPOSTMAN_TEST_DETAILS'));
				}
				else {
					$title	= (Text::_('COM_BWPOSTMAN_SUB_DETAILS'));
				}

				// Set toolbar title and items
				$checkedOut = 0;
				if (property_exists($this->item, 'checked_out'))
				{
					$checkedOut		= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
				}

				// Set toolbar title depending on the state of the item: Is it a new item? --> Create; Is it an existing record? --> Edit
				// For new records, check the create permission.
				if ($this->item->id < 1 && $this->permissions['subscriber']['create'])
				{
					ToolbarHelper::title($title . ': <small>[ ' . Text::_('NEW') . ' ]</small>', 'plus');

					$toolbar->apply('subscriber.apply');

					$saveGroup = $toolbar->dropdownButton('save-group');

					$saveGroup->configure(
						function (Toolbar $childBar)
						{
							$childBar->save('subscriber.save');
							$childBar->save2new('subscriber.save2new');
							$childBar->save2copy('subscriber.save2copy');
						}
					);

				}
				else
				{
					// Can't save the record if it's checked out.
					if (!$checkedOut) {
						ToolbarHelper::title($title . ': <small>[ ' . Text::_('EDIT') . ' ]</small>', 'edit');

						// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
						if (BwPostmanHelper::canEdit('subscriber', $this->item))
						{
							$toolbar->apply('subscriber.apply');

							if ($this->permissions['subscriber']['create'])
							{
								$saveGroup = $toolbar->dropdownButton('save-group');

								$saveGroup->configure(
									function (Toolbar $childBar)
									{
										$childBar->save('subscriber.save');
										$childBar->save2new('subscriber.save2new');
										$childBar->save2copy('subscriber.save2copy');
									}
								);
							}
						}
					}

					// Rename the cancel button for existing items
				}

				$toolbar->cancel('subscriber.cancel');

				$backlink = $app->input->server->get('HTTP_REFERER', '', '');
				$siteURL  = $uri->base() . 'index.php?option=com_bwpostman&view=bwpostman';

				// If we came from the cover page we will show a back-button
				if ($backlink == $siteURL)
				{
					$toolbar->back();
				}
		}

		$toolbar->addButtonPath(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/toolbar');

		$manualButton = BwPostmanHTMLHelper::getManualButton('subscriber');
		$forumButton  = BwPostmanHTMLHelper::getForumButton();

		$toolbar->appendButton($manualButton);
		$toolbar->appendButton($forumButton);
	}
}
