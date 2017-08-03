<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman main view for backend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
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
defined ('_JEXEC') or die ('Restricted access');

// Import VIEW object class
jimport('joomla.application.component.view');

// Require helper classes
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/htmlhelper.php');

/**
 * BwPostman General View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	CoverPage
 *
 * @since       0.9.1
 */
class BwPostmanViewBwPostman extends JViewLegacy
{
	/**
	 * property to hold archive data
	 *
	 * @var string $archive
	 *
	 * @since       0.9.1
	 */
	public $archive;

	/**
	 * property to hold general data
	 *
	 * @var string $general
	 *
	 * @since       0.9.1
	 */
	public $general;

	/**
	 * property to hold request url
	 *
	 * @var string $request_url
	 *
	 * @since       0.9.1
	 */
	public $request_url;

	/**
	 * property to hold queue entries property
	 *
	 * @var boolean $queueEntries
	 *
	 * @since       0.9.1
	 */
	public $queueEntries;

	/**
	 * property to hold sidebar
	 *
	 * @var object  $sidebar
	 *
	 * @since       0.9.1
	 */
	public $sidebar;

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
		$uri		= JUri::getInstance('SERVER');
		$uri_string	= $uri->toString();

		//check for queue entries
		$this->queueEntries	= BwPostmanHelper::checkQueueEntries();

		// Get data from the model
		$this->archive		= $this->get('Archivedata');
		$this->general		= $this->get('Generaldata');
		$this->request_url	= $uri_string;

		// Get document object, set document title and add css
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_BWPOSTMAN'));
		$document->addStyleSheet(JUri::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');

		// Set toolbar title
		JToolbarHelper::title (JText::_('COM_BWPOSTMAN'), 'envelope');

		// Set toolbar items for the page
		if (BwPostmanHelper::canAdmin())
		{
			JToolbarHelper::preferences('com_bwpostman', '500', '900');
			JToolbarHelper::spacer();
			JToolbarHelper::divider();
			JToolbarHelper::spacer();
		}
		$link   = BwPostmanHTMLHelper::getForumLink();
		JToolbarHelper::help(JText::_("COM_BWPOSTMAN_FORUM"), false, $link);

		JToolbarHelper::spacer();

		BwPostmanHelper::addSubmenu('bwpostman');

		$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
	}
}
