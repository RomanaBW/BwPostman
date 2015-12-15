<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletter single view for frontend.
 *
 * @version 1.2.4 bwpm
 * @package BwPostman-Site
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

class BwPostmanViewNewsletter extends JViewLegacy
{
	/**
	 * Display
	 *
	 * @access	public
	 * @param 	string Template
	 */
	public function display($tpl = null)
	{
		$app	= JFactory::getApplication();
		$id		= (int) $app->input->get('id', 0);
		$params	= $app->getPageParameters();

		// Count how often the newsletter has been viewed
		$newsletter = JTable::getInstance('newsletters', 'BwPostmanTable');
		$newsletter->load($id);
		$newsletter->hit($id);

		// Get document object, set document title and add css
		$templateName	= $app->getTemplate();
		$css_filename	= 'templates/' . $templateName . '/css/com_bwpostman.css';

		$document = JFactory::getDocument();
		if ($params->get('page_heading') != '') {
			$document->setTitle($params->get('page_title'));
		}
		else {
			$document->setTitle($newsletter->subject);
		}
		$document->addStyleSheet(JURI::root(true) . 'components/com_bwpostman/assets/css/bwpostman.css');
		if (file_exists(JPATH_BASE . $css_filename)) $document->addStyleSheet(JURI::root(true) . $css_filename);

		// Get the global list params and preset them
		$globalParams				= JComponentHelper::getParams('com_bwpostman', true);
		$this->attachment_enabled	= $globalParams->get('attachment_single_enable');
		$this->page_title			= $globalParams->get('subject_as_title');

		$menuParams = new JRegistry;

		if ($menu = $app->getMenu()->getActive()) {
			$menuParams->loadString($menu->params);
		}

		// if we came from list view to show single newsletter, then params of list view shall take effect
		if (is_object($menu)) {
			if (stristr($menu->link, 'view=newsletter&') == false) {
				// Get the menu item state
				$nls_state	= $app->getUserState('com_bwpostman.newsletters.params');

				// if we have a menu state, use this and overwrite global settings
				if ($nls_state->get('attachment_enable') !== null) {
					$this->attachment_enabled	= $nls_state->get('attachment_enable');
				}
			}
			else { // we come from single menu link, use menu params if set, otherwise global details params are used
				if ($menuParams->get('attachment_single_enable') !== null) {
					$this->attachment_enabled	= $menuParams->get('attachment_single_enable');
				}
				else {
					$this->attachment_enabled	= $globalParams->get('attachment_single_enable');
				}
			}
		}

		if ($newsletter->published == 0) {
			$app->enqueueMessage(JText::_('COM_BWPOSTMAN_ERROR_NL_NOT_AVAILABLE'), 'error');
		}

		// Setting the backlink
		$backlink = $_SERVER['HTTP_REFERER'];

		// Save a reference into the view
		$this->assignRef('backlink', $backlink);
		$this->assignRef('newsletter', $newsletter);
		$this->assignRef('params', $params);

		// Set parent display
		parent::display($tpl);
	}
}
