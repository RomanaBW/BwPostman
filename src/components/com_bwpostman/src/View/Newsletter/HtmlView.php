<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletter single view for frontend.
 *
 * @version %%version_number%%
 * @package BwPostman-Site
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

namespace BoldtWebservice\Component\BwPostman\Site\View\Newsletter;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * Class BwPostmanViewNewsletter
 *
 * @since       0.9.1
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * Attachment enabled?
	 *
	 * @var    boolean
	 *
	 * @since       0.9.1
	 */
	public $attachment_enabled = true;

	/**
	 * Page title
	 *
	 * @var    string
	 *
	 * @since       0.9.1
	 */
	public $page_title = true;

	/**
	 * Backlink
	 *
	 * @var    string
	 *
	 * @since       0.9.1
	 */
	public $backlink = true;

	/**
	 * The current newsletter
	 *
	 * @var    object
	 *
	 * @since       0.9.1
	 */
	public $newsletter = true;

	/**
	 * Params
	 *
	 * @var    object JRegistry object
	 *
	 * @since       0.9.1
	 */
	public $params = true;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  HtmlView
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function display($tpl = null): HtmlView
	{
		$app        = Factory::getApplication();
		$id         = $app->input->getInt('id', 0);
		$params     = ComponentHelper::getParams('com_bwpostman');
		$MvcFactory = $app->bootComponent('com_bwpostman')->getMVCFactory();

		// Count how often the newsletter has been viewed
		$newsletter = $MvcFactory->createTable('Newsletter', 'Administrator');
		$newsletter->load($id);
		$newsletter->hit($id);

		// Get document object, set document title
		$document     = $app->getDocument();

		if ($params->get('page_heading', '') != '')
		{
			$document->setTitle($params->get('page_title', ''));
		}
		else
		{
			$document->setTitle($newsletter->subject);
		}

		// Get the global list params and preset them
		$globalParams				= ComponentHelper::getParams('com_bwpostman', true);
		$this->attachment_enabled	= $globalParams->get('attachment_single_enable', '1');
		$this->page_title			= $globalParams->get('subject_as_title', '1');

		$menuParams = new Registry;
		$menu = $app->getMenu()->getActive();

		if ($menu)
		{
			$menuParams->loadString($menu->getParams());
		}

		// if we came from list view to show single newsletter, then params of list view shall take effect
		if (is_object($menu))
		{
			if (stristr($menu->link, 'view=newsletter&') === false)
			{
				// Get the menu item state
				$nls_state	= $app->getUserState('com_bwpostman.newsletters.params');

				// if we have a menu state, use this and overwrite global settings
				if (is_object($nls_state))
				{
					if ($nls_state->get('attachment_enable', '1') !== null) {
						$this->attachment_enabled	= $nls_state->get('attachment_enable', '1');
					}
				}
			}
			else
			{
				// we come from single menu link, use menu params if set, otherwise global details params are used
				if ($menuParams->get('attachment_single_enable', '1') !== null)
				{
					$this->attachment_enabled	= $menuParams->get('attachment_single_enable', '1');
				}
				else
				{
					$this->attachment_enabled	= $globalParams->get('attachment_single_enable', '1');
				}
			}
		}

		if ((int)$newsletter->published === 0)
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_ERROR_NL_NOT_AVAILABLE'), 'error');
		}

		// Setting the backlink
		$backlink = $app->input->server->get('HTTP_REFERER', '', '');

		// Save a reference into the view
		$this->backlink = $backlink;
		$this->newsletter = $newsletter;
		$this->params = $params;

		// switch frontend layout
		$tpl = $this->params->get('fe_layout_detail', null);

		// Set parent display
		parent::display($tpl);

		return $this;
	}
}
