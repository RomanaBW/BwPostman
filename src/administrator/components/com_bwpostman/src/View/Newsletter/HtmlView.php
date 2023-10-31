<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single html newsletters view for backend.
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

namespace BoldtWebservice\Component\BwPostman\Administrator\View\Newsletter;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Toolbar\Button\LinkButton;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Log\LogEntry;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHTMLHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwLogger;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * BwPostman Newsletter View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	Newsletters
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
	 * property to hold params
	 *
	 * @var object $params
	 *
	 * @since       0.9.1
	 */
	public $params;

	/**
	 * property to hold content_exists
	 *
	 * @var boolean $content_exists
	 *
	 * @since       0.9.1
	 */
	public $content_exists;

	/**
	 * property to hold selected_content_old
	 *
	 * @var string $selected_content_old
	 *
	 * @since       0.9.1
	 */
	public $selected_content_old;

	/**
	 * property to hold old id of template
	 *
	 * @var boolean $template_id_old
	 *
	 * @since       0.9.1
	 */
	public $template_id_old;

	/**
	 * property to old id of text template
	 *
	 * @var boolean $text_template_id_old
	 *
	 * @since       0.9.1
	 */
	public $text_template_id_old;

	/**
	 * @var string
	 *
	 * @since       2.0.0
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
	 * @var boolean
	 *
	 * @since       2.0.0
	 */
	public $substitute;

	/**
	 * @var string   $delay_message
	 *
	 * @since       2.4.0
	 */
	protected $delay_message;

	/**
	 * @var integer   $delay
	 *
	 * @since       2.4.0
	 */
	protected $delay;

	/**
	 * @var object   $logger
	 *
	 * @since       2.4.0
	 */
	protected $logger;

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
	public function display($tpl=null): HtmlView
	{
		// Initialize variables
		$app = Factory::getApplication();
		$app->setUserState('bwpostman.send.alsoUnconfirmed', false);
		// reset trial error
		$app->setUserState('com_bwpostman.newsletter.trial.error', false);

		$log_options  = array();
		$this->logger = BwLogger::getInstance($log_options);

		$this->permissions = $app->getUserState('com_bwpm.permissions', []);

		if (!$this->permissions['view']['newsletter'])
		{
			$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', Text::_('COM_BWPOSTMAN_NLS')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		//check for queue entries
		$this->queueEntries = BwPostmanHelper::checkQueueEntries();

		PluginHelper::importPlugin('bwpostman');
		if (PluginHelper::isEnabled('bwpostman', 'substitutelinks'))
		{
			$this->substitute = true;
		}

		// Get input data
		$jinput   = $app->input;
		$referrer = $jinput->get->get('referrer', '', 'string');
		$task	= $jinput->get('task', 'edit');

		if ($task == 'startsending')
		{
			$this->buildDelayMessage();
		}
		else
		{
			$this->form     = $this->get('Form');
			$this->item     = $this->get('Item');
			$this->state    = $this->get('State');
			$this->template = $app->getTemplate();
			$this->params   = ComponentHelper::getParams('com_bwpostman');

			$app->triggerEvent('onBwPostmanBeforeNewsletterEdit', array(&$this->item, $referrer));

			$this->setContentFlags();
		}

		$this->addToolbar();

		// reset temporary state
		$app->setUserState('com_bwpostman.edit.newsletter.changeTab', false);

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
		$userId		= $app->getIdentity()->get('id');
		$layout		= $app->input->get('layout', '');

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance();

		// Get document object, set document title and add css
		$document	= $app->getDocument();
		$document->setTitle(Text::_('COM_BWPOSTMAN_NL_DETAILS'));
		$document->getWebAssetManager()->useScript('com_bwpostman.admin-bwpm_nl');

		// Set toolbar title and items
		if ($layout == 'nl_send')
		{
			$options['text'] = "COM_BWPOSTMAN_BACK";
			$options['name'] = 'back';
			$options['url'] = "index.php?option=com_bwpostman&view=newsletters";
			$options['icon'] = "icon-arrow-left";

			$button = new LinkButton('back');

			ToolbarHelper::title(Text::_('COM_BWPOSTMAN_ACTION_SEND'), 'envelope');

			$button->setOptions($options);

			$toolbar->appendButton($button);
		}
		// If we come from sent newsletters, we have to do other stuff than normal
		elseif ($layout == 'edit_publish')
		{
			ToolbarHelper::title(Text::_('COM_BWPOSTMAN_NL_PUBLISHING_DETAILS') . ': <small>[ ' . Text::_('NEW') . ' ]</small>', 'plus');

			$toolbar->apply('newsletter.publish_apply');
			$toolbar->save('newsletter.publish_save');

			$toolbar->cancel('newsletter.cancel');
		}
		else
		{
			$checkedOut		= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

			$isNew = ($this->item->id == 0);

			// For new records, check the create permission.
			ToolbarHelper::title(Text::_('COM_BWPOSTMAN_NL_DETAILS') . ': <small>[ ' . Text::_('EDIT') . ' ]</small>', 'edit');

			if ($isNew && $this->permissions['newsletter']['create'])
			{

				$toolbar->apply('newsletter.apply');

				$saveGroup = $toolbar->dropdownButton('save-group');

				$saveGroup->configure(
					function (Toolbar $childBar)
					{
						$childBar->save('newsletter.save');
						$childBar->save2new('newsletter.save2new');
					}
				);

				$task		= $app->input->get('task', '', 'string');
				// If we came from the main page we will show a back button
				if ($task == 'add')
				{
					$toolbar->back();
				}
				else
				{
					$toolbar->cancel('newsletter.cancel', 'JTOOLBAR_CANCEL');
				}
			}
			else
			{
				// Can't save the record if it's checked out.
				if (!$checkedOut)
				{
					// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
					if (BwPostmanHelper::canEdit('newsletter', $this->item->id))
					{
						$toolbar->apply('newsletter.apply');

						if ($this->permissions['newsletter']['create'])
						{
							$saveGroup = $toolbar->dropdownButton('save-group');

							$saveGroup->configure(
								function (Toolbar $childBar)
								{
									$childBar->save('newsletter.save');
									$childBar->save2new('newsletter.save2new');
									$childBar->save2copy('newsletter.save2copy');
								}
							);
						}
					}
				}

				// Rename the cancel button for existing items
				$toolbar->cancel('newsletter.cancel', 'COM_BWPOSTMAN_CLOSE');
			}
		}

		$toolbar->addButtonPath(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/toolbar');

		$manualButton = BwPostmanHTMLHelper::getManualButton('newsletter');
		$forumButton  = BwPostmanHTMLHelper::getForumButton();

		$toolbar->appendButton($manualButton);
		$toolbar->appendButton($forumButton);
	}

	/**
	 * Build the delay message needed at task startsending
	 *
	 * @return  void
	 *
	 * @since       2.4.0
	 */
	private function buildDelayMessage()
	{
		// Get the params
		$params      = ComponentHelper::getParams('com_bwpostman');
		$this->delay = (int) $params->get('mails_per_pageload_delay', '10') * (int) $params->get('mails_per_pageload_delay_unit', '1000');
		$this->logger->addEntry(new LogEntry('View raw delay: ' . $this->delay, BwLogger::BW_DEBUG, 'send'));

		if ((int) $params->get('mails_per_pageload_delay_unit', '1000') == 1000)
		{
			if ((int) $params->get('mails_per_pageload_delay', '10') == 1)
			{
				$this->delay_message = Text::sprintf(
					'COM_BWPOSTMAN_MAILS_DELAY_MESSAGE',
					Text::sprintf('COM_BWPOSTMAN_MAILS_DELAY_TEXT_1_SECONDS', $this->delay / 1000)
				);
			}
			else
			{
				$this->delay_message = Text::sprintf(
					'COM_BWPOSTMAN_MAILS_DELAY_MESSAGE',
					Text::sprintf('COM_BWPOSTMAN_MAILS_DELAY_TEXT_N_SECONDS', $this->delay / 1000)
				);
			}
		}
		else
		{
			if ((int) $params->get('mails_per_pageload_delay', '10') == 1)
			{
				$this->delay_message = Text::sprintf(
					'COM_BWPOSTMAN_MAILS_DELAY_MESSAGE',
					Text::sprintf('COM_BWPOSTMAN_MAILS_DELAY_TEXT_1_MINUTES', $this->delay / 1000)
				);
			}
			else
			{
				$this->delay_message = Text::sprintf(
					'COM_BWPOSTMAN_MAILS_DELAY_MESSAGE',
					Text::sprintf('COM_BWPOSTMAN_MAILS_DELAY_TEXT_N_MINUTES', $this->delay / 1000)
				);
			}
		}
	}

	/**
	 * Set some flags for the content
	 *
	 * @return  void
	 *
	 * @since       2.4.0
	 */
	private function setContentFlags()
	{
		// flag, if rendered content exists or not
		if ($this->item->html_version || $this->item->text_version)
		{
			$this->content_exists = true;
		}
		else
		{
			$this->content_exists = false;
		}

		// flag for selected content before editing
		if (is_array($this->item->selected_content))
		{
			$this->selected_content_old = implode(',', $this->item->selected_content);
		}
		elseif (isset($this->item->selected_content))
		{
			$this->selected_content_old = $this->item->selected_content;
		}
		else
		{
			$this->selected_content_old = '';
		}

		// flags for template ids before editing
		$this->template_id_old      = 0;
		$this->text_template_id_old = 0;

		if (property_exists($this->item, 'template_id_old'))
		{
			$this->template_id_old      = $this->item->template_id_old;
		}

		if (property_exists($this->item, 'text_template_id_old'))
		{
			$this->text_template_id_old = $this->item->text_template_id_old;
		}
	}
}
