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

namespace BoldtWebservice\Component\BwPostman\Administrator\View\Campaign;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Environment\Browser;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHTMLHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * BwPostman Campaign View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	Campaigns
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
	 * @return  HtmlView  A string if successful, otherwise a JError object.
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function display($tpl = null): HtmlView
	{
		$app		= Factory::getApplication();

		$this->permissions = $app->getUserState('com_bwpm.permissions', []);

		if (!$this->permissions['view']['campaign'])
		{
			$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', Text::_('COM_BWPOSTMAN_CAMS')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

//		$dispatcher = JEventDispatcher::getInstance();
//		JPluginHelper::importPlugin('bwpostman', 'bwtimecontrol');

		$app->setUserState('com_bwpostman.edit.campaign.id', $app->input->getInt('id', 0));

		//check for queue entries
		$this->queueEntries	= BwPostmanHelper::checkQueueEntries();

		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');

		// Get the assigned newsletters
		$this->newsletters = $this->get('NewslettersOfCampaign');

		// trigger Plugin BwTimeControl event and get results
//		$dispatcher->trigger('onBwPostmanCampaignPrepare', array (&$this->item, &$this->newsletters, &$document));

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
		$app    = Factory::getApplication();
		$app->input->set('hidemainmenu', true);
		$uri		= Uri::getInstance();
		$userId		= $app->getIdentity()->get('id');

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance();

		$this->document->getWebAssetManager()->useScript('com_bwpostman.admin-bwpm_campaign');

		// Set toolbar title depending on the state of the item: Is it a new item? --> Create; Is it an existing record? --> Edit
		$isNew = ($this->item->id < 1);

		// Set toolbar title and items
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		// For new records, check the create permission.
		if ($isNew && BwPostmanHelper::canAdd('campaign'))
		{
			ToolbarHelper::title(Text::_('COM_BWPOSTMAN_CAM_DETAILS') . ': <small>[ ' . Text::_('NEW') . ' ]</small>', 'plus');

			$toolbar->apply('campaign.apply');

			$saveGroup = $toolbar->dropdownButton('save-group');

			$saveGroup->configure(
				function (Toolbar $childBar)
				{
					$childBar->save('campaign.save');
					$childBar->save2new('campaign.save2new');
				}
			);

			$toolbar->cancel('campaign.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			ToolbarHelper::title(Text::_('COM_BWPOSTMAN_CAM_DETAILS') . ': <small>[ ' . Text::_('EDIT') . ' ]</small>',
				'edit');

			// Can't save the record if it's checked out.
			if (!$checkedOut)
			{
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if (BwPostmanHelper::canEdit('campaign', $this->item))
				{
					$toolbar->apply('campaign.apply');

					$saveGroup = $toolbar->dropdownButton('save-group');

					$saveGroup->configure(
						function (Toolbar $childBar) {
							$childBar->save('campaign.save');

							if ($this->permissions['campaign']['create'])
							{
								$childBar->save2new('campaign.save2new');
								$childBar->save2copy('campaign.save2copy');
							}
						}
					);
				}

				// Rename the cancel button for existing items
				if ($app->getUserState('bwtimecontrol.cam_data.nl_referrer', '') == 'remove')
				{
					$toolbar->cancel('campaign.save');
				}
				else
				{
					$toolbar->cancel('campaign.cancel');
				}
			}
		}

		$app->setUserState('bwtimecontrol.cam_data.nl_referrer', null);
		$backlink 	= $app->input->server->get('HTTP_REFERER', '', '');
		$siteURL 	= $uri->base() . 'index.php?option=com_bwpostman&view=bwpostman';

		// If we came from the cover page we will show a back-button
		if ($backlink == $siteURL)
		{
			$toolbar->back();
		}

		$toolbar->addButtonPath(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/toolbar');

		$manualButton = BwPostmanHTMLHelper::getManualButton('campaign');
		$forumButton  = BwPostmanHTMLHelper::getForumButton();

		$toolbar->appendButton($manualButton);
		$toolbar->appendButton($forumButton);
	}
}
