<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman main view for backend.
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

use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;

// Import VIEW object class
jimport('joomla.application.component.view');

// Require helper classes
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/htmlhelper.php');

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
	 * property to hold permissions as array
	 *
	 * @var array $permissions
	 *
	 * @since       2.0.0
	 */
	public $permissions;

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
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function display($tpl = null)
	{
		$uri		= Uri::getInstance('SERVER');
		$uri_string	= $uri->toString();

		//check for queue entries
		$this->queueEntries	= BwPostmanHelper::checkQueueEntries();

		// Get data from the model
		$this->archive		= $this->get('Archivedata');
		$this->general		= $this->get('Generaldata');
		$this->request_url	= $uri_string;
		$this->permissions	= Factory::getApplication()->getUserState('com_bwpm.permissions');

		$this->addToolbar();

		$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);

		return $this;
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
		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance('toolbar');

		// Get document object, set document title and add css
		$document = Factory::getDocument();
		$document->setTitle(Text::_('COM_BWPOSTMAN'));
		$document->addStyleSheet(Uri::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');

		// Set toolbar title
		ToolbarHelper::title(Text::_('COM_BWPOSTMAN'), 'envelope');

		// Set toolbar items for the page
		if ($this->permissions['com']['admin'])
		{
			$toolbar->preferences('com_bwpostman');
		}

		$toolbar->addButtonPath(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/toolbar');

		$manualButton = BwPostmanHTMLHelper::getManualButton('bwpostman');
		$forumButton  = BwPostmanHTMLHelper::getForumButton();


		$toolbar->appendButton($manualButton);
		$toolbar->appendButton($forumButton);
	}
}
