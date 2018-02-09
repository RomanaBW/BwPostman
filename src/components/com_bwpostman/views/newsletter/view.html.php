<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletter single view for frontend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Site
 * @author Romana Boldt
 * @copyright (C) 2012-2017 Boldt Webservice <forum@boldt-webservice.de>
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

use Joomla\Registry\Registry as JRegistry;

// Import VIEW object class
jimport('joomla.application.component.view');

/**
 * Class BwPostmanViewNewsletter
 *
 * @since       0.9.1
 */
class BwPostmanViewNewsletter extends JViewLegacy
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
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since       0.9.1
	 */
	public function display($tpl = null)
	{
		$app	= JFactory::getApplication();
		$id		= (int) $app->input->get('id', 0);
		$params	= JComponentHelper::getParams('com_bwpostman');

		// Count how often the newsletter has been viewed
		$newsletter = JTable::getInstance('newsletters', 'BwPostmanTable');
		$newsletter->load($id);
		$newsletter->hit($id);

		// Get document object, set document title and add css
		$templateName	= $app->getTemplate();
		$css_filename	= '/templates/' . $templateName . '/css/com_bwpostman.css';

		$document = JFactory::getDocument();
		if ($params->get('page_heading') != '')
		{
			$document->setTitle($params->get('page_title'));
		}
		else
		{
			$document->setTitle($newsletter->subject);
		}
		$document->addStyleSheet(JUri::root(true) . '/components/com_bwpostman/assets/css/bwpostman.css');
		if (file_exists(JPATH_BASE . $css_filename))
			$document->addStyleSheet(JUri::root(true) . $css_filename);

		// Get the global list params and preset them
		$globalParams				= JComponentHelper::getParams('com_bwpostman', true);
		$this->attachment_enabled	= $globalParams->get('attachment_single_enable');
		$this->page_title			= $globalParams->get('subject_as_title');

		$menuParams = new JRegistry;
		$menu = $app->getMenu()->getActive();

		if ($menu)
		{
			$menuParams->loadString($menu->params);
		}

		// if we came from list view to show single newsletter, then params of list view shall take effect
		if (is_object($menu))
		{
			if (stristr($menu->link, 'view=newsletter&') == false)
			{
				// Get the menu item state
				$nls_state	= $app->getUserState('com_bwpostman.newsletters.params');

				// if we have a menu state, use this and overwrite global settings
				if (is_object($nls_state))
				{
					if ($nls_state->get('attachment_enable') !== null) {
						$this->attachment_enabled	= $nls_state->get('attachment_enable');
					}
				}
			}
			else
			{ // we come from single menu link, use menu params if set, otherwise global details params are used
				if ($menuParams->get('attachment_single_enable') !== null)
				{
					$this->attachment_enabled	= $menuParams->get('attachment_single_enable');
				}
				else
				{
					$this->attachment_enabled	= $globalParams->get('attachment_single_enable');
				}
			}
		}

		if ($newsletter->published == 0)
		{
			$app->enqueueMessage(JText::_('COM_BWPOSTMAN_ERROR_NL_NOT_AVAILABLE'), 'error');
		}

		// Setting the backlink
		$backlink = $_SERVER['HTTP_REFERER'];

		// Save a reference into the view
		$this->backlink = $backlink;
		$this->newsletter = $newsletter;
		$this->params = $params;

		// Set parent display
		parent::display($tpl);
	}
}
