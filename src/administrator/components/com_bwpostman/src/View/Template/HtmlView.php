<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman template view for backend.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
 * @author Karl Klostermann
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

namespace BoldtWebservice\Component\BwPostman\Administrator\View\Template;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Environment\Browser;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHTMLHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanTplHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * BwPostman template View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	template
 *
 * @since       1.1.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * property to hold form data
	 *
	 * @var array   $form
	 *
	 * @since       1.1.0
	 */
	protected $form;

	/**
	 * property to hold selected item
	 *
	 * @var object   $item
	 *
	 * @since       1.1.0
	 */
	protected $item;

	/**
	 * property to hold state
	 *
	 * @var array|object  $state
	 *
	 * @since       1.1.0
	 */
	protected $state;

	/**
	 * property to hold queue entries
	 *
	 * @var boolean $queueEntries
	 *
	 * @since       1.1.0
	 */
	public $queueEntries;

	/**
	 * property to hold template
	 *
	 * @var boolean $template
	 *
	 * @since       1.1.0
	 */
	public $template;

	/**
	 * property to hold request url
	 *
	 * @var string $request_url
	 *
	 * @since       1.1.0
	 */
	public $request_url;

	/**
	 * @var string $request_url
	 *
	 * @since       2.0.0
	 */
	public $headTag = '';

	/**
	 * @var string $request_url
	 *
	 * @since       2.0.0
	 */
	public $bodyTag = '';

	/**
	 * @var string $request_url
	 *
	 * @since       2.0.0
	 */
	public $articleTagBegin = '';

	/**
	 * @var string $request_url
	 *
	 * @since       2.0.0
	 */
	public $articleTagEnd = '';

	/**
	 * @var string $request_url
	 *
	 * @since       2.0.0
	 */
	public $readonTag = '';

	/**
	 * @var string $request_url
	 *
	 * @since       2.0.0
	 */
	public $legalTagBegin = '';

	/**
	 * @var string $request_url
	 *
	 * @since       2.0.0
	 */
	public $legalTagEnd = '';

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
	 * @since   1.1.0
	 */
	public function display($tpl = null): HtmlView
	{
		$app		= Factory::getApplication();
		$template	= $app->getTemplate();
		$uri		= Uri::getInstance();
		$uri_string	= str_replace('&', '&amp;', $uri->toString());

		$this->permissions		= $app->getUserState('com_bwpm.permissions', []);

		if (!$this->permissions['view']['template'])
		{
			$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', Text::_('COM_BWPOSTMAN_TPLS')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		$app->setUserState('com_bwpostman.edit.template.id', $app->input->getInt('id', 0));

		//check for queue entries
		$this->queueEntries	= BwPostmanHelper::checkQueueEntries();

		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');

		// Save a reference into view
		$this->request_url	= $uri_string;
		$this->template		= $template;

		$this->addToolbar();

		// call user-made html template
		if ($this->item->tpl_id == '0')
		{
			$tpl = 'html';
		}

		// call user-made text template
		if ($this->item->tpl_id == '998')
		{
			$tpl = 'text';
		}

		// call standard text template
		if ($this->item->tpl_id > '999')
		{
			$tpl = 'text_std';
		}

		// get standard tags
		$this->headTag         = BwPostmanTplHelper::getHeadTag();
		$this->bodyTag         = BwPostmanTplHelper::getBodyTag();
		$this->articleTagBegin = BwPostmanTplHelper::getArticleTagBegin();
		$this->articleTagEnd   = BwPostmanTplHelper::getArticleTagEnd();
		$this->readonTag       = BwPostmanTplHelper::getReadonTag();
		$this->legalTagBegin   = BwPostmanTplHelper::getLegalTagBegin();
		$this->legalTagEnd     = BwPostmanTplHelper::getLegalTagEnd();

		// Call parent display
		parent::display($tpl);

		return $this;
	}

	/**
	 * Add the page title, styles and toolbar.
	 *
	 * @throws Exception
	 *
	 * @since	1.1.0
	 */
	protected function addToolbar()
	{
		$app    = Factory::getApplication();
		$app->input->set('hidemainmenu', true);
		$uri		= Uri::getInstance();
		$userId		= $app->getIdentity()->get('id');

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance();

		// Set toolbar title depending on the state of the item: Is it a new item? --> Create; Is it an existing record? --> Edit
		$isNew          = ($this->item->id < 1);
		$checkedOut		= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		// Set toolbar title and items

		// For new records, check the create permission.
		if ($isNew && $this->permissions['template']['create'])
		{
			ToolbarHelper::title(Text::_('COM_BWPOSTMAN_TPL_DETAILS') . ': <small>[ ' . Text::_('NEW') . ' ]</small>', 'plus');

			$toolbar->apply('template.apply');

			$saveGroup = $toolbar->dropdownButton('save-group');

			$saveGroup->configure(
				function (Toolbar $childBar)
				{
					$childBar->save('template.save');
					$childBar->save2new('template.save2new');
				}
			);

			$toolbar->cancel('template.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			// Can't save the record if it's checked out.
			if (!$checkedOut)
			{
				ToolbarHelper::title(
					Text::_('COM_BWPOSTMAN_TPL_DETAILS') . ':  <strong>' . $this->item->title .
					'  </strong><small>[ ' . Text::_('EDIT') . ' ]</small> ',
					'edit'
				);

				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if (BwPostmanHelper::canEdit('template', $this->item))
				{
					$toolbar->apply('template.apply');

					$saveGroup = $toolbar->dropdownButton('save-group');

					$saveGroup->configure(
						function (Toolbar $childBar)
						{
							$childBar->save('template.save');
							if ($this->permissions['template']['create'])
							{
								$childBar->save2new('template.save2new');
								$childBar->save2copy('template.save2copy');
							}
						}
					);

					$toolbar->cancel('template.cancel');
				}
			}
		}

		$backlink 	= $app->input->server->get('HTTP_REFERER', '', '');
		$siteURL 	= $uri->base() . 'index.php?option=com_bwpostman&view=bwpostman';

		// If we came from the cover page we will show a back-button
		if ($backlink == $siteURL)
		{
			$toolbar->back();
		}

		$toolbar->addButtonPath(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/toolbar');

		$manualButton = BwPostmanHTMLHelper::getManualButton('template');
		$forumButton  = BwPostmanHTMLHelper::getForumButton();

		$toolbar->appendButton($manualButton);
		$toolbar->appendButton($forumButton);
	}
}
