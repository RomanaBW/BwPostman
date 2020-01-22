<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Workflow\Administrator\View\Mailinglists;

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

\JLoader::register('BwPostmanHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php');
\JLoader::register('BwPostmanHTMLHelper', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/htmlhelper.php');

/**
 * View to edit a mailinglist.
 *
 * @since  2.4.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * property to hold selected items
	 *
	 * @var array   $items
	 *
	 * @since       2.4.0
	 */
	protected $items;

	/**
	 * property to hold pagination object
	 *
	 * @var object  $pagination
	 *
	 * @since       2.4.0
	 */
	protected $pagination;

	/**
	 * property to hold state
	 *
	 * @var array|object  $state
	 *
	 * @since       2.4.0
	 */
	protected $state;

	/**
	 * property to hold filter form
	 *
	 * @var object  $filterForm
	 *
	 * @since       2.4.0
	 */
	public $filterForm;

	/**
	 * property to hold active filters
	 *
	 * @var object  $activeFilters
	 *
	 * @since       2.4.0
	 */
	public $activeFilters;

	/**
	 * property to hold total value
	 *
	 * @var string $total
	 *
	 * @since       2.4.0
	 */
	public $total;

	/**
	 * property to hold permissions as array
	 *
	 * @var array $permissions
	 *
	 * @since       2.0.0
	 */
	public $permissions;

	/**
	 * property to hold sidebar
	 *
	 * @var object  $sidebar
	 *
	 * @since       2.4.0
	 */
	public $sidebar;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @throws \Exception
	 *
	 * @since 2.4.0
	 */
	public function display($tpl = null)
	{
		$app	= Factory::getApplication();

		$this->permissions		= Factory::getApplication()->getUserState('com_bwpm.permissions');

		if (!$this->permissions['view']['mailinglist'])
		{
			$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', Text::_('COM_BWPOSTMAN_MLS')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		// Get data from the model
		$this->state			= $this->get('State');
		$this->items			= $this->get('Items');
		$this->filterForm		= $this->get('FilterForm');
		$this->activeFilters	= $this->get('ActiveFilters');
		$this->pagination		= $this->get('Pagination');
		$this->total			= $this->get('total');

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @throws \Exception
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		// Get document object, set document title and add css
		$document = Factory::getDocument();
		$document->setTitle(Text::_('COM_BWPOSTMAN_MLS'));
		$document->addStyleSheet(Uri::root(true) . '/administrator/components/com_bwpostman/assets/css/bwpostman_backend.css');

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance('toolbar');

		// Set toolbar title
		ToolbarHelper::title(Text::_('COM_BWPOSTMAN_MLS'), 'list');

		// Set toolbar items for the page
		if ($this->permissions['mailinglist']['create'])
		{
			$toolbar->addNew('mailinglist.add');
		}

		if (\BwPostmanHelper::canEdit('mailinglist'))
		{
			$toolbar->edit('mailinglist.edit');
		}

		if (\BwPostmanHelper::canEditState('mailinglist'))
		{
			$dropdown = $toolbar->dropdownButton('status-group')
				->text('JTOOLBAR_CHANGE_STATUS')
				->toggleSplit(false)
				->icon('fa fa-globe')
				->buttonClass('btn btn-info')
				->listCheck(true);

			$childBar = $dropdown->getChildToolbar();
			$childBar->publish('mailinglists.publish')->listCheck(true);
			$childBar->unpublish('mailinglists.unpublish')->listCheck(true);

			if (\BwPostmanHelper::canArchive('mailinglist'))
			{
				$childBar->archive('mailinglist.archive')->listCheck(true);
			}

			if (\BwPostmanHelper::canEdit('mailinglist', 0) || \BwPostmanHelper::canEditState('mailinglist', 0))
			{
				$childBar->checkin('mailinglists.checkin')->listCheck(true);
			}
		}

		$toolbar->addButtonPath(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/toolbar');

		$manualLink = \BwPostmanHTMLHelper::getManualLink('mailinglists');
		$forumLink  = \BwPostmanHTMLHelper::getForumLink();

//		$toolbar->appendButton('ExtLink', 'users', Text::_('COM_BWPOSTMAN_FORUM'), $forumLink);
//		$toolbar->appendButton('ExtLink', 'book', Text::_('COM_BWPOSTMAN_MANUAL'), $manualLink);
	}
}
