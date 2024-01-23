<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman archive view for backend.
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

namespace BoldtWebservice\Component\BwPostman\Administrator\View\Archive;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Button\PopupButton;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHTMLHelper;

/**
 * BwPostman Archive View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	Archive
 *
 * @since       0.9.1
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * property to hold selected items
	 *
	 * @var array   $items
	 *
	 * @since       0.9.1
	 */
	protected $items;

	/**
	 * property to hold pagination object
	 *
	 * @var object  $pagination
	 *
	 * @since       0.9.1
	 */
	protected $pagination;

	/**
	 * property to hold state
	 *
	 * @var array|object  $state
	 *
	 * @since       0.9.1
	 */
	protected $state;

	/**
	 * property to hold user permissions
	 *
	 * @var array  $permissions
	 *
	 * @since       2.0.0
	 */
	protected $permissions;

	/**
	 * property to hold filter form
	 *
	 * @var object  $filterForm
	 *
	 * @since       0.9.1
	 */
	public $filterForm;

	/**
	 * property to hold active filters
	 *
	 * @var object  $activeFilters
	 *
	 * @since       0.9.1
	 */
	public $activeFilters;

	/**
	 * property to hold request url
	 *
	 * @var string $request_url
	 *
	 * @since       0.9.1
	 */
	public $request_url;

	/**
	 * property to hold sidebar
	 *
	 * @var object  $sidebar
	 *
	 * @since       0.9.1
	 */
	public $sidebar;

	/**
	 * Display
	 *
	 * @access	public
	 *
	 * @param	string $tpl Template
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function display($tpl = null)
	{
		$app	= Factory::getApplication();

		$this->permissions		= $app->getUserState('com_bwpm.permissions', []);

		if (!$this->permissions['view']['archive'])
		{
			$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', Text::_('COM_BWPOSTMAN_ARC')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		// Get data from the model
		$this->items 			= $this->get('Items');
		$this->pagination		= $this->get('Pagination');
		$this->filterForm		= $this->getModel()->getFilterForm(array(), true, $this->_layout);
		$this->activeFilters	= $this->get('ActiveFilters');
		$this->state			= $this->get('State');

		$request_result = $this->checkForAllowedTabs();

		if ($request_result === false)
		{
			$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', Text::_('COM_BWPOSTMAN_ARC')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		if ($request_result === 'redirect')
		{
			$app->redirect($this->request_url);
		}

		$this->addToolbar();

		$wa = $this->document->getWebAssetManager();
		$wa->useScript('com_bwpostman.admin-bwpm_confirm_unarchive');
		$wa->useScript('com_bwpostman.admin-bwpm_confirm_delete_cam_nls');

		// Call parent display
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	protected function addToolbar()
	{
		$app    = Factory::getApplication();
		$jinput	= $app->input;

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance();

		// Set toolbar title
		ToolbarHelper::title(Text::_('COM_BWPOSTMAN_ARC'), 'list');

		// Set toolbar items for the page (depending on the tab which we are in)
		$layout = $jinput->get('layout', 'newsletters');
		switch ($layout)
		{ // Which tab are we in?
			case "newsletters":
				if (BwPostmanHelper::canRestore('newsletter'))
				{
					$toolbar->unarchive('archive.unarchive', 'COM_BWPOSTMAN_UNARCHIVE')->listCheck(true);
				}

				if (BwPostmanHelper::canDelete('newsletter'))
				{
					ToolbarHelper::deleteList(Text::_('COM_BWPOSTMAN_ARC_CONFIRM_REMOVING_NL'), 'archive.delete');
					//@ToDo: This one does not create a confirmation popup
//					$toolbar->delete('archive.delete', 'COM_BWPOSTMAN_ARC_CONFIRM_REMOVING_NL')->listCheck(true);
				}
				break;
			case "subscribers":
				if (BwPostmanHelper::canRestore('subscriber'))
				{
					$toolbar->unarchive('archive.unarchive', 'COM_BWPOSTMAN_UNARCHIVE')->listCheck(true);
				}

				if (BwPostmanHelper::canDelete('subscriber'))
				{
					ToolbarHelper::deleteList(Text::_('COM_BWPOSTMAN_ARC_CONFIRM_REMOVING_SUB'), 'archive.delete');
					//@ToDo: This one does not create a confirmation popup
//					$toolbar->delete('archive.delete', 'COM_BWPOSTMAN_ARC_CONFIRM_REMOVING_SUB')->listCheck(true);
				}
				break;
			case "campaigns":
				// Special unarchive and delete button because we need a confirm dialog with 3 options
				if (BwPostmanHelper::canRestore('campaign'))
				{
					$options['url'] = "index.php?option=com_bwpostman&amp;view=archive&amp;format=raw&amp;layout=campaigns_confirmunarchive";
					$options['icon'] = "icon-unarchive";
					$options['text'] = "COM_BWPOSTMAN_UNARCHIVE";
					$options['bodyHeight'] = 50;
					$options['name'] = 'unarchive';

					$button = new PopupButton('unarchive');
					$button->setOptions($options);
					$button->listCheck(true);

					$toolbar->AppendButton($button);
				}

				if (BwPostmanHelper::canDelete('campaign'))
				{
					$options['url'] = "index.php?option=com_bwpostman&amp;view=archive&amp;format=raw&amp;layout=campaigns_confirmdelete";
					$options['icon'] = "icon-delete";
					$options['text'] = "delete";
					$options['bodyHeight'] = 50;
					$options['name'] = 'delete';

					$button = new PopupButton('delete');
					$button->setOptions($options);
					$button->listCheck(true);

					$toolbar->AppendButton($button);
				}
				break;
			case "mailinglists":
				if (BwPostmanHelper::canRestore('mailinglist'))
				{
					$toolbar->unarchive('archive.unarchive', 'COM_BWPOSTMAN_UNARCHIVE')->listCheck(true);
				}

				if (BwPostmanHelper::canDelete('mailinglist'))
				{
					ToolbarHelper::deleteList(Text::_('COM_BWPOSTMAN_ARC_CONFIRM_REMOVING_ML'), 'archive.delete');
					//@ToDo: This one does not create a confirmation popup
//					$toolbar->delete('archive.delete', 'COM_BWPOSTMAN_ARC_CONFIRM_REMOVING_ML')->listCheck(true);
				}
				break;
			case "templates":
				if (BwPostmanHelper::canRestore('template'))
				{
					$toolbar->unarchive('archive.unarchive', 'COM_BWPOSTMAN_UNARCHIVE')->listCheck(true);
				}

				if (BwPostmanHelper::canDelete('template'))
				{
					ToolbarHelper::deleteList(Text::_('COM_BWPOSTMAN_ARC_CONFIRM_REMOVING_TPL'), 'archive.delete');
					//@ToDo: This one does not create a confirmation popup
//					$toolbar->delete('archive.delete', 'COM_BWPOSTMAN_ARC_CONFIRM_REMOVING_TPL')->listCheck(true);
				}
				break;
		}


		$manualButton = BwPostmanHTMLHelper::getManualButton('archive');
		$forumButton  = BwPostmanHTMLHelper::getForumButton();

		$toolbar->appendButton($manualButton);
		$toolbar->appendButton($forumButton);
	}

	/**
	 * Check permission for archive tab and set layout to allowed one, if needed
	 *
	 * @since       2.0.0
	 */
	private function checkForAllowedTabs()
	{
		$uri        = Uri::getInstance();
		$uriString = $uri->toString();
		$uriShort  = substr($uriString, strrpos($uriString, '/') + 1, strlen($uriString));

		$layout = $this->extractLayout($uriShort);
		if ($layout == false)
		{
			return false;
		}

		$allowedLayouts = $this->getAllowedLayouts();

		if (!count($allowedLayouts))
		{
			return false;
		}

		if (!in_array($layout, $allowedLayouts))
		{
			$this->request_url = str_replace($layout, $allowedLayouts[0], $uriShort);
			return 'redirect';
		}

		$this->request_url = $uriShort;

		return true;
	}

	/**
	 *
	 * @param string $uri_string
	 *
	 * @return string|bool $layout  requested layout or false on error
	 *
	 * @since 1.3.2
	 */
	private function extractLayout(string $uri_string)
	{
		$uri_array = explode('&', $uri_string);

		if (count($uri_array) != 3)
		{
			return false;
		}

		$layout_arr = explode('=', $uri_array[2]);

		if (count($layout_arr) != 2)
		{
			return false;
		}

		return $layout_arr[1];
	}

	/**
	 *
	 * @return array $allowedLayouts  allowed layouts
	 *
	 * @since 1.3.2
	 */
	private function getAllowedLayouts(): array
	{
		$allowedLayouts = array();

		// check for allowed layouts
		$allLayouts    = array('newsletter', 'subscriber', 'campaign', 'mailinglist', 'template');
		foreach ($allLayouts as $item)
		{
			$allowedView	= $this->permissions['view'][$item];
			if ($allowedView)
			{
				$allowedLayouts[] = $item . 's';
			}
		}

		return $allowedLayouts;
	}
}
