<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletter all view for frontend.
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

namespace BoldtWebservice\Component\BwPostman\Site\View\Newsletters;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Pagination\Pagination;
use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use stdClass;

/**
 * Class BwPostmanViewNewsletters
 *
 * @since       0.9.1
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * property to hold state data
	 *
	 * @var array   $state
	 *
	 * @since       0.9.1
	 */
	protected array $state;

	/**
	 * property to hold selected item
	 *
	 * @var object   $params
	 *
	 * @since       0.9.1
	 */
	protected object $params;

	/**
	 * property to hold items
	 *
	 * @var array   $items
	 *
	 * @since       0.9.1
	 */
	protected array $items;

	/**
	 * property to hold item id
	 *
	 * @var integer   $Itemid
	 *
	 * @since       3.0.0
	 */
	protected int $Itemid;

	/**
	 * property to hold pagination object
	 *
	 * @var Pagination|null  $pagination
	 *
	 * @since       0.9.1
	 */
	protected $pagination	= null;

	/**
	 * property to hold form object
	 *
	 * @var ?object  $form
	 *
	 * @since       0.9.1
	 */
	protected ?object $form	= null;

	/**
	 * property to hold filter form object
	 *
	 * @var ?object  $filterForm
	 *
	 * @since       0.9.1
	 */
	protected ?object $filterForm	= null;

	/**
	 * property to hold active filters object
	 *
	 * @var ?object   $activeFilters
	 *
	 * @since       0.9.1
	 */
	protected ?object  $activeFilters	= null;

	/**
	 * property to hold mailinglists
	 *
	 * @var ?array  $mailinglists
	 *
	 * @since       0.9.1
	 */
	protected ?array $mailinglists	= null;

	/**
	 * property to hold campaigns
	 *
	 * @var array|null $campaigns
	 *
	 * @since       0.9.1
	 */
	protected ?array $campaigns	= null;

	/**
	 * property to hold usergroups object
	 *
	 * @var ?array $usergroups
	 *
	 * @since       0.9.1
	 */
	protected ?array $usergroups	= null;

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

		$app		= Factory::getApplication();
		$menu		= $app->getMenu()->getActive();

		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');
		$form		= new stdClass;

		if ($state->params->get('date_filter_enable', '1') != 'hide')
		{
			// get all possible mailingdate options
			$date_options = $this->get('DateOptions');
			$months = $date_options['months'];
			$form->monthField = HtmlHelper::_(
				'select.genericlist',
				$months,
				'month',
				array(
					'list.attr' => 'class="form-select" size="1" onchange="document.getElementById(\'adminForm\').submit();"',
					'list.select' => $state->get('filter.month'),
					'option.key' => null
				)
			);
			$years = $date_options['years'];
			$form->yearField = HtmlHelper::_(
				'select.genericlist',
				$years,
				'year',
				array(
					'list.attr' => 'class="form-select" size="1" onchange="document.getElementById(\'adminForm\').submit();"',
					'list.select' => $state->get('filter.year')
				)
			);
		}
		$form->limitField = $pagination->getLimitBox();

		$this->items			= &$items;
		$this->state			= &$state;
		$this->pagination		= &$pagination;
		$this->form				= &$form;
		$this->params			= $this->state->params;
		$this->filterForm		= $this->get('FilterForm');
		$this->activeFilters	= $this->get('ActiveFilters');
		$this->mailinglists		= $this->get('AccessibleMailinglists');
		$this->campaigns		= $this->get('AccessibleCampaigns');
		$this->usergroups		= $this->get('AccessibleUsergroups');
		$this->Itemid			= $this->get('MenuItemid');

		if (is_array($this->mailinglists))
		{
			array_unshift($this->mailinglists, array ('id' => '0', 'title' => Text::_('COM_BWPOSTMAN_SUB_FILTER_MAILINGLISTS')));
		}

		if (is_array($this->campaigns))
		{
			array_unshift($this->campaigns, array ('id' => '0', 'title' => Text::_('COM_BWPOSTMAN_SUB_FILTER_CAMPAIGNS')));
		}

		if (is_array($this->usergroups))
		{
			array_unshift($this->usergroups, array ('id' => '0', 'title' => Text::_('COM_BWPOSTMAN_SUB_FILTER_USERGROUPS')));
		}

		// Because the application sets a default page title, we need to get it
		// right from the menu item itself
		if (is_object($menu))
		{
			$menu_params = new Registry();
			$menu_params->loadString($menu->getParams());
			if (!$menu_params->get('page_heading', ''))
			{
				$this->params->set('page_heading',	Text::_('COM_BWPOSTMAN_NLS'));
			}
		}
		else
		{
			$this->params->set('page_heading',	Text::_('COM_BWPOSTMAN_NLS'));
		}

		// switch frontend layout
		$tpl = $this->params->get('fe_layout_list', null);

		// Set parent display
		parent::display($tpl);

		return $this;
	}
}
