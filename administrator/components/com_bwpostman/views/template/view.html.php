<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman template view for backend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Karl Klostermann
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

// Require helper class
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');

// Import VIEW object class
jimport('joomla.application.component.view');

/**
 * BwPostman template View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	template
 *
 * @since       1.1.0
 */
class BwPostmanViewTemplate extends JViewLegacy
{
	/**
	 * property to hold form data
	 *
	 * @var array   $form
	 *
	 * @since       1.1.0
	 */
	protected $form;

	/**
	 * property to hold selected item
	 *
	 * @var object   $item
	 *
	 * @since       1.1.0
	 */
	protected $item;

	/**
	 * property to hold state
	 *
	 * @var array|object  $state
	 *
	 * @since       1.1.0
	 */
	protected $state;

	/**
	 * property to hold queue entries
	 *
	 * @var boolean $queueEntries
	 *
	 * @since       1.1.0
	 */
	public $queueEntries;

	/**
	 * property to hold template
	 *
	 * @var boolean $template
	 *
	 * @since       1.1.0
	 */
	public $template;

	/**
	 * property to hold request url
	 *
	 * @var string $request_url
	 *
	 * @since       1.1.0
	 */
	public $request_url;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @see     fetch()
	 *
	 * @since   1.1.0
	 */
	public function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$template	= $app->getTemplate();
		$uri		= JUri::getInstance();
		$uri_string	= str_replace('&', '&amp;', $uri->toString());

		$app->setUserState('com_bwpostman.edit.template.id', JFactory::getApplication()->input->getInt('id', 0));

		//check for queue entries
		$this->queueEntries	= BwPostmanHelper::checkQueueEntries();

		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');

		// Save a reference into view
		$this->request_url	= $uri_string;
		$this->template		= $template;

		$this->addToolbar();

		// call user-made html template
		if ($this->item->tpl_id == '0')  $tpl = 'html';
		// call user-made text template
		if ($this->item->tpl_id == '998') $tpl = 'text';
		// call standard text template
		if ($this->item->tpl_id > '999')  $tpl = 'text_std';

		// Call parent display
		parent::display($tpl);
	}

	/**
	 * Add the page title, styles and toolbar.
	 *
	 * @since	1.1.0
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$uri		= JUri::getInstance();
		$userId		= JFactory::getUser()->get('id');

		// Get document object, set document title and add css
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('BWP_TPL_DETAILS'));
		$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');

		// Get the user browser --> if the user has msie load the ie-css to show the tabs in the correct way
		jimport('joomla.environment.browser');
		$browser = JBrowser::getInstance();
		$user_browser = $browser->getBrowser();

		if ($user_browser == 'msie')
		{
			$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend_ie.css');
		}

		// Set toolbar title depending on the state of the item: Is it a new item? --> Create; Is it an existing record? --> Edit
		$isNew          = ($this->item->id < 1);
		$checkedOut		= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		// Set toolbar title and items

		// For new records, check the create permission.
		if ($isNew && BwPostmanHelper::canAdd('template'))
		{
			JToolbarHelper::save('template.save');
			JToolbarHelper::apply('template.apply');
			JToolbarHelper::cancel('template.cancel');

			JToolbarHelper::title(JText::_('COM_BWPOSTMAN_TPL_DETAILS').': <small>[ ' . JText::_('NEW').' ]</small>', 'plus');
		}
		else
		{
			// Can't save the record if it's checked out.
			if (!$checkedOut)
			{
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if (BwPostmanHelper::canEdit('template', $this->item))
				{
					JToolbarHelper::save('template.save');
					JToolbarHelper::apply('template.apply');
				}
			}
			// If checked out, we can still copy
			if (BwPostmanHelper::canAdd('template'))
			{
				JToolbarHelper::save2copy('template.save2copy');
			}
			// Rename the cancel button for existing items
			JToolbarHelper::cancel('template.cancel', 'JTOOLBAR_CLOSE');
			JToolbarHelper::title(JText::_('COM_BWPOSTMAN_TPL_DETAILS').':  <strong>' . $this->item->title  . '  </strong><small>[ ' . JText::_('EDIT').' ]</small> ', 'edit');
		}

		$backlink 	= $_SERVER['HTTP_REFERER'];
		$siteURL 	= $uri->base();

		// If we came from the cover page we will show a back-button
		if ($backlink == $siteURL.'index.php?option=com_bwpostman')
		{
			JToolbarHelper::spacer();
			JToolbarHelper::divider();
			JToolbarHelper::spacer();
			JToolbarHelper::back();
		}
		JToolbarHelper::divider();
		JToolbarHelper::spacer();
		JToolbarHelper::help(JText::_("COM_BWPOSTMAN_FORUM"), false, 'http://www.boldt-webservice.de/forum/bwpostman.html');
		JToolbarHelper::spacer();
	}
}
