<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single mailinglists view for backend.
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

namespace BoldtWebservice\Component\BwPostman\Administrator\View\Mailinglist;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHTMLHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * BwPostman Mailinglist View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	Mailinglists
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
	 * property to hold request url
	 *
	 * @var object  $request_url
	 *
	 * @since       0.9.1
	 */
	protected $request_url;

	/**
	 * property to hold template
	 *
	 * @var object $template
	 *
	 * @since       0.9.1
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
		$template	= $app->getTemplate();
		$uri		= Uri::getInstance();
		$uri_string	= str_replace('&', '&amp;', $uri->toString());

		$this->permissions		= $app->getUserState('com_bwpm.permissions', []);

		if (!$this->permissions['view']['mailinglist'])
		{
			$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', Text::_('COM_BWPOSTMAN_MLS')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		$app->setUserState('com_bwpostman.edit.mailinglist.id', $app->input->getInt('id', 0));

		//check for queue entries
		$this->queueEntries	= BwPostmanHelper::checkQueueEntries();

		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');

		// Save a reference into view
		$this->request_url	= $uri_string;
		$this->template		= $template;

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

		$this->document->getWebAssetManager()->useScript('com_bwpostman.admin-bwpm_mailinglist');

		// Set toolbar title depending on the state of the item: Is it a new item? --> Create; Is it an existing record? --> Edit
		$isNew = ($this->item->id < 1);

		// Set toolbar title and items
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		// For new records, check the create permission.
		if ($isNew && BwPostmanHelper::canAdd('mailinglist'))
		{
			ToolbarHelper::title(Text::_('COM_BWPOSTMAN_ML_DETAILS') . ': <small>[ ' . Text::_('NEW') . ' ]</small>', 'plus');

			$toolbar->apply('mailinglist.apply');

			$saveGroup = $toolbar->dropdownButton('save-group');

			$saveGroup->configure(
				function (Toolbar $childBar)
				{
					$childBar->save('mailinglist.save');
					$childBar->save2new('mailinglist.save2new');
				}
			);
			$toolbar->cancel('mailinglist.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			// Can't save the record if it's checked out.
			if (!$checkedOut)
			{
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if (BwPostmanHelper::canEdit('mailinglist'))
				{
					ToolbarHelper::title(Text::_('COM_BWPOSTMAN_ML_DETAILS') . ': <small>[ ' . Text::_('EDIT') . ' ]</small>', 'edit');

					$toolbar->apply('mailinglist.apply');

					$saveGroup = $toolbar->dropdownButton('save-group');

					$saveGroup->configure(
						function (Toolbar $childBar)
						{
							$childBar->save('mailinglist.save');
							$childBar->save2new('mailinglist.save2new');
							$childBar->save2copy('mailinglist.save2copy');
						}
					);
					$toolbar->cancel('mailinglist.cancel');
				}
			}
		}

		$backlink   = '';
		if (key_exists('HTTP_REFERER', $_SERVER))
		{
			$backlink 	= $app->input->server->get('HTTP_REFERER', '', '');
		}

		$siteURL 	= $uri->base() . 'index.php?option=com_bwpostman&view=bwpostman';

		// If we came from the cover page we will show a back-button
		if ($backlink == $siteURL)
		{
			$toolbar->back();
		}

		$toolbar->addButtonPath(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/toolbar');

		$manualButton = BwPostmanHTMLHelper::getManualButton('mailinglist');
		$forumButton  = BwPostmanHTMLHelper::getForumButton();

		$toolbar->appendButton($manualButton);
		$toolbar->appendButton($forumButton);
	}
}
