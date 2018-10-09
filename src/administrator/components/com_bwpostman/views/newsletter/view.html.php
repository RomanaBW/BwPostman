<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single html newsletters view for backend.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
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

// Import VIEW object class
jimport('joomla.application.component.view');

// Require helper class
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/htmlhelper.php');


/**
 * BwPostman Newsletter View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	Newsletters
 *
 * @since       0.9.1
 */
class BwPostmanViewNewsletter extends JViewLegacy
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
	 * property to hold state
	 *
	 * @var array|object  $state
	 *
	 * @since       0.9.1
	 */
	protected $state;

	/**
	 * property to hold queue entries property
	 *
	 * @var boolean $queueEntries
	 *
	 * @since       0.9.1
	 */
	public $queueEntries;

	/**
	 * property to hold params
	 *
	 * @var object $params
	 *
	 * @since       0.9.1
	 */
	public $params;

	/**
	 * property to hold content_exists
	 *
	 * @var boolean $content_exists
	 *
	 * @since       0.9.1
	 */
	public $content_exists;

	/**
	 * property to hold selected_content_old
	 *
	 * @var string $selected_content_old
	 *
	 * @since       0.9.1
	 */
	public $selected_content_old;

	/**
	 * property to hold old id of template
	 *
	 * @var boolean $template_id_old
	 *
	 * @since       0.9.1
	 */
	public $template_id_old;

	/**
	 * property to old id of text template
	 *
	 * @var boolean $text_template_id_old
	 *
	 * @since       0.9.1
	 */
	public $text_template_id_old;

	/**
	 * @var string
	 *
	 * @since       2.0.0
	 */
	public $template;

	/**
	 * property to hold permissions as array
	 *
	 * @var array $permissions
	 *
	 * @since       2.0.0
	 */
	public $permissions;

	/**
	 * @var boolean
	 *
	 * @since       2.0.0
	 */
	public $substitute;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function display($tpl=null)
	{
		// Initialize variables
		$dispatcher = JEventDispatcher::getInstance();
		$app		= JFactory::getApplication();

		$this->permissions		= JFactory::getApplication()->getUserState('com_bwpm.permissions');

		if (!$this->permissions['view']['newsletter'])
		{
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', JText::_('COM_BWPOSTMAN_NLS')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		//check for queue entries
		$this->queueEntries = BwPostmanHelper::checkQueueEntries();

		JPluginHelper::importPlugin('bwpostman');
		if (JPluginHelper::isEnabled('bwpostman', 'substitutelinks'))
		{
			$this->substitute = true;
		}

		// Get input data
		$jinput   = $app->input;
		$referrer = $jinput->get->get('referrer', '', 'string');

		$this->form     = $this->get('Form');
		$this->item     = $this->get('Item');
		$this->state    = $this->get('State');
		$this->template = $app->getTemplate();
		$this->params   = JComponentHelper::getParams('com_bwpostman');

		$dispatcher->trigger('onBwPostmanBeforeNewsletterEdit', array(&$this->item, $referrer));

		// set some needed flags
		// flag, if rendered content exists or not
		if ($this->item->html_version || $this->item->text_version)
		{
			$this->content_exists = true;
		}
		else
		{
			$this->content_exists = false;
		}

		// flag for selected content before editing
		if (is_array($this->item->selected_content))
		{
			$this->selected_content_old = implode(',', $this->item->selected_content);
		}
		elseif (isset($this->item->selected_content))
		{
			$this->selected_content_old = $this->item->selected_content;
		}
		else
		{
			$this->selected_content_old = '';
		}

		// flags for template ids before editing
		$this->template_id_old      = $this->item->template_id_old;
		$this->text_template_id_old = $this->item->text_template_id_old;

		$this->addToolbar();

		// reset temporary state
		$app->setUserState('com_bwpostman.edit.newsletter.changeTab', false);

		// Call parent display
		return parent::display($tpl);
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
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$userId		= JFactory::getUser()->get('id');
		$layout		= JFactory::getApplication()->input->get('layout', '');

		// Get document object, set document title and add css
		$document	= JFactory::getDocument();
		$document->setTitle('COM_BWPOSTMAN_NL_DETAILS');
		$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');
		JHtml::_('jquery.framework');
		$document->addScript(JUri::root(true) . '/administrator/components/com_bwpostman/assets/js/bwpostman_nl.js');

		// Set toolbar title and items
		$checkedOut		= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		$isNew = ($this->item->id == 0);

		// If we come from sent newsletters, we have to do other stuff than normal
		if ($layout == 'edit_publish')
		{
			JToolbarHelper::save('newsletter.publish_save');
			JToolbarHelper::apply('newsletter.publish_apply');

			JToolbarHelper::cancel('newsletter.cancel');
			JToolbarHelper::title(JText::_('COM_BWPOSTMAN_NL_PUBLISHING_DETAILS') . ': <small>[ ' . JText::_('NEW') . ' ]</small>', 'plus');
		}
		else
		{
			// For new records, check the create permission.
			if ($isNew && $this->permissions['newsletter']['create'])
			{
				JToolbarHelper::title(JText::_('COM_BWPOSTMAN_NL_DETAILS') . ': <small>[ ' . JText::_('EDIT') . ' ]</small>', 'edit');
				JToolbarHelper::save('newsletter.save');
				JToolbarHelper::apply('newsletter.apply');
				JToolbarHelper::save2new('newsletter.save2new');

				$task		= JFactory::getApplication()->input->get('task', '', 'string');
				// If we came from the main page we will show a back button
				if ($task == 'add')
				{
					JToolbarHelper::back();
				}
				else
				{
					JToolbarHelper::cancel('newsletter.cancel');
				}
			}
			else
			{
				// Can't save the record if it's checked out.
				if (!$checkedOut)
				{
					// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
					if (BwPostmanHelper::canEdit('newsletter', $this->item->id))
					{
						JToolbarHelper::save('newsletter.save');
						JToolbarHelper::apply('newsletter.apply');

						if ($this->permissions['newsletter']['create'])
						{
							JToolbarHelper::save2new('newsletter.save2new');
						}
					}
				}

				// Rename the cancel button for existing items
				JToolbarHelper::cancel('newsletter.cancel', 'COM_BWPOSTMAN_CLOSE');
				JToolbarHelper::title(JText::_('COM_BWPOSTMAN_NL_DETAILS') . ': <small>[ ' . JText::_('EDIT') . ' ]</small>', 'edit');
			}
		}

		JToolbarHelper::divider();
		JToolbarHelper::spacer();
		$link   = BwPostmanHTMLHelper::getForumLink();
		JToolbarHelper::help(JText::_("COM_BWPOSTMAN_FORUM"), false, $link);

		JToolbarHelper::spacer();
	}
}
