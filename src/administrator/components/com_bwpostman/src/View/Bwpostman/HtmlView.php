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

namespace BoldtWebservice\Component\BwPostman\Administrator\View\Bwpostman;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use JHtmlSidebar;
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHTMLHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * BwPostman General View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	CoverPage
 *
 * @since       0.9.1
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * property to hold archive data
	 *
	 * @var array $archive
	 *
	 * @since       0.9.1
	 */
	public $archive;

	/**
	 * property to hold general data
	 *
	 * @var array $general
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
	 * @return  HtmlView  A string if successful, otherwise a JError object.
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function display($tpl = null): HtmlView
	{
		$uri		= Uri::getInstance();
		$uri_string	= $uri->toString();

		//check for queue entries
		$this->queueEntries	= BwPostmanHelper::checkQueueEntries();

		// Get data from the model
		$this->archive     = $this->get('Archivedata');
		$this->checkArchiveStatisticsData();
		$this->general     = $this->get('Generaldata');
		$this->checkGeneralStatisticsData();
		$this->request_url = $uri_string;
		$this->permissions = Factory::getApplication()->getUserState('com_bwpm.permissions', []);

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
		$toolbar = Toolbar::getInstance();

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
	/**
	 * Check array of general statistics data
	 *
	 * @throws Exception
	 *
	 * @since       3.0.1
	 */
	protected function checkGeneralStatisticsData()
	{
		if (!key_exists('nl_unsent', $this->general))
		{
			$this->general['nl_unsent'] = '';
		}

		if (!key_exists('nl_sent', $this->general))
		{
			$this->general['nl_sent'] = '';
		}

		if (!key_exists('sub', $this->general))
		{
			$this->general['sub'] = '';
		}

		if (!key_exists('test', $this->general))
		{
			$this->general['test'] = '';
		}

		if (!key_exists('cam', $this->general))
		{
			$this->general['cam'] = '';
		}

		if (!key_exists('ml_published', $this->general))
		{
			$this->general['ml_published'] = '';
		}

		if (!key_exists('ml_unpublished', $this->general))
		{
			$this->general['ml_unpublished'] = '';
		}

		if (!key_exists('html_templates', $this->general))
		{
			$this->general['html_templates'] = '';
		}

		if (!key_exists('text_templates', $this->general))
		{
			$this->general['text_templates'] = '';
		}
	}

	/**
	 * Check array of archive statistics data
	 *
	 * @throws Exception
	 *
	 * @since       3.0.1
	 */
	protected function checkArchiveStatisticsData()
	{
		if (!key_exists('arc_nl', $this->archive))
		{
			$this->archive['arc_nl'] = '';
		}

		if (!key_exists('arc_sub', $this->archive))
		{
			$this->archive['arc_sub'] = '';
		}

		if (!key_exists('sub', $this->archive))
		{
			$this->archive['sub'] = '';
		}

		if (!key_exists('arc_cam', $this->archive))
		{
			$this->archive['arc_cam'] = '';
		}

		if (!key_exists('arc_ml', $this->archive))
		{
			$this->archive['arc_ml'] = '';
		}

		if (!key_exists('arc_html_templates', $this->archive))
		{
			$this->archive['arc_html_templates'] = '';
		}

		if (!key_exists('arc_text_templates', $this->archive))
		{
			$this->archive['arc_text_templates'] = '';
		}
	}
}
