<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single campaigns view for backend.
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

// Import VIEW object class
jimport('joomla.application.component.view');

// Require helper class
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/htmlhelper.php');


/**
 * BwPostman Campaign View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	Campaigns
 *
 * @since       0.9.1
 */
class BwPostmanViewCampaign extends JViewLegacy
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
	 * property to hold newsletters list
	 *
	 * @var array $newsletters
	 *
	 * @since       0.9.1
	 */
	public $newsletters;

	/**
	 * property to hold permissions as array
	 *
	 * @var array $permissions
	 *
	 * @since       2.0.0
	 */
	public $permissions;

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
	public function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$document	= JFactory::getDocument();

		$this->permissions = JFactory::getApplication()->getUserState('com_bwpm.permissions');

		if (!$this->permissions['view']['campaign'])
		{
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', JText::_('COM_BWPOSTMAN_CAMS')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('bwpostman', 'bwtimecontrol');

		$app->setUserState('com_bwpostman.edit.campaign.id', $app->input->getInt('id', 0));

		//check for queue entries
		$this->queueEntries	= BwPostmanHelper::checkQueueEntries();

		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');

		// Get the assigned newsletters
		$this->newsletters = $this->get('Newsletters');

		// trigger Plugin BwTimeControl event and get results
		$dispatcher->trigger('onBwPostmanCampaignPrepare', array (&$this->item, &$this->newsletters, &$document));

		$this->addToolbar();
		// Call parent display
		parent::display($tpl);
		return $this;
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
		$uri		= JUri::getInstance('SERVER');
		$userId		= JFactory::getUser()->get('id');

		// Get document object, set document title and add css
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_BWPOSTMAN_CAM_DETAILS'));
		$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');

		// Get the user browser --> if the user has MS IE load the ie-css to show the tabs in the correct way
		jimport('joomla.environment.browser');
		$browser		= JBrowser::getInstance();
		$user_browser	= $browser->getBrowser();

		if ($user_browser == 'msie')
		{
			$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend_ie.css');
		}

		// Set toolbar title depending on the state of the item: Is it a new item? --> Create; Is it an existing record? --> Edit
		$isNew = ($this->item->id < 1);

		// Set toolbar title and items
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		// For new records, check the create permission.
		if ($isNew && BwPostmanHelper::canAdd('campaign'))
		{
			JToolbarHelper::save('campaign.save');
			JToolbarHelper::apply('campaign.apply');
			JToolbarHelper::save2new('campaign.save2new');
			JToolbarHelper::save2copy('campaign.save2copy');
			JToolbarHelper::cancel('campaign.cancel');
			JToolbarHelper::title(JText::_('COM_BWPOSTMAN_CAM_DETAILS') . ': <small>[ ' . JText::_('NEW') . ' ]</small>', 'plus');
		}
		else
		{
			// Can't save the record if it's checked out.
			if (!$checkedOut)
			{
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if (BwPostmanHelper::canEdit('campaign', $this->item))
				{
					JToolbarHelper::save('campaign.save');
					JToolbarHelper::apply('campaign.apply');

					if ($this->permissions['campaign']['create'])
					{
						JToolbarHelper::save2new('campaign.save2new');
						JToolbarHelper::save2copy('campaign.save2copy');
					}
				}
			}

			// Rename the cancel button for existing items
			if (JFactory::getApplication()->getUserState('bwtimecontrol.cam_data.nl_referrer', null) == 'remove')
			{
				JToolbarHelper::cancel('campaign.save', 'JTOOLBAR_CLOSE');
			}
			else
			{
				JToolbarHelper::cancel('campaign.cancel', 'JTOOLBAR_CLOSE');
			}

			JToolbarHelper::title(JText::_('COM_BWPOSTMAN_CAM_DETAILS') . ': <small>[ ' . JText::_('EDIT') . ' ]</small>', 'edit');
		}

		JFactory::getApplication()->setUserState('bwtimecontrol.cam_data.nl_referrer', null);
		$backlink 	= JFactory::getApplication()->input->server->get('HTTP_REFERER', '', '');
		$siteURL 	= $uri->base();

		// If we came from the main page we will show a back-button
		if ($backlink == $siteURL . 'index.php?option=com_bwpostman')
		{
			JToolbarHelper::spacer();
			JToolbarHelper::divider();
			JToolbarHelper::spacer();
			JToolbarHelper::back();
		}

		JToolbarHelper::spacer();
		JToolbarHelper::divider();
		JToolbarHelper::spacer();
		$link   = BwPostmanHTMLHelper::getForumLink();
		JToolbarHelper::help(JText::_("COM_BWPOSTMAN_FORUM"), false, $link);

		JToolbarHelper::spacer();
	}
}
