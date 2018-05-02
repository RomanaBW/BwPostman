<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletter all view for frontend.
 *
 * @version 2.0.1 bwpm
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
 * Class BwPostmanViewNewsletters
 *
 * @since       0.9.1
 */
class BwPostmanViewNewsletters extends JViewLegacy
{
	/**
	 * property to hold state data
	 *
	 * @var array   $state
	 *
	 * @since       0.9.1
	 */
	protected $state;

	/**
	 * property to hold selected item
	 *
	 * @var object   $params
	 *
	 * @since       0.9.1
	 */
	protected $params;

	/**
	 * property to hold items
	 *
	 * @var array   $items
	 *
	 * @since       0.9.1
	 */
	protected $items;

	/**
	 * property to hold pagination object
	 *
	 * @var object  $object
	 *
	 * @since       0.9.1
	 */
	protected $pagination	= null;

	/**
	 * property to hold form object
	 *
	 * @var object  $form
	 *
	 * @since       0.9.1
	 */
	protected $form	= null;

	/**
	 * property to hold filter form object
	 *
	 * @var object  $filterForm
	 *
	 * @since       0.9.1
	 */
	protected $filterForm	= null;

	/**
	 * property to hold active filters object
	 *
	 * @var object  $activeFilters
	 *
	 * @since       0.9.1
	 */
	protected $activeFilters	= null;

	/**
	 * property to hold mailinglists object
	 *
	 * @var object  $mailinglists
	 *
	 * @since       0.9.1
	 */
	protected $mailinglists	= null;

	/**
	 * property to hold campaigns object
	 *
	 * @var object  $campaigns
	 *
	 * @since       0.9.1
	 */
	protected $campaigns	= null;

	/**
	 * property to hold usergroups object
	 *
	 * @var object  $usergroups
	 *
	 * @since       0.9.1
	 */
	protected $usergroups	= null;

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

		$app		= JFactory::getApplication();
		$menu		= $app->getMenu()->getActive();

		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');
		$form		= new stdClass;

		// Month Field
		$months = array(
			'' => JText::_('COM_BWPOSTMAN_MONTH'),
			'01' => JText::_('JANUARY_SHORT'),
			'02' => JText::_('FEBRUARY_SHORT'),
			'03' => JText::_('MARCH_SHORT'),
			'04' => JText::_('APRIL_SHORT'),
			'05' => JText::_('MAY_SHORT'),
			'06' => JText::_('JUNE_SHORT'),
			'07' => JText::_('JULY_SHORT'),
			'08' => JText::_('AUGUST_SHORT'),
			'09' => JText::_('SEPTEMBER_SHORT'),
			'10' => JText::_('OCTOBER_SHORT'),
			'11' => JText::_('NOVEMBER_SHORT'),
			'12' => JText::_('DECEMBER_SHORT')
		);
		$form->monthField = JHtml::_(
			'select.genericlist',
			$months,
			'month',
			array(
				'list.attr' => 'size="1" class="inputbox input-small" onchange="document.getElementById(\'adminForm\').submit();"',
				'list.select' => $state->get('filter.month'),
				'option.key' => null
			)
		);

		// Year Field
		$years = array();
		$years[] = JHtml::_('select.option', null, JText::_('JYEAR'));

		for ($year = date('Y'), $i = $year - 10; $i <= $year; $i++)
		{
			$years[] = JHtml::_('select.option', $i, $i);
		}

		$form->yearField = JHtml::_(
			'select.genericlist',
			$years,
			'year',
			array(
				'list.attr' => 'size="1" class="inputbox input-small" onchange="document.getElementById(\'adminForm\').submit();"',
				'list.select' => $state->get('filter.year')
			)
		);
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

		array_unshift($this->mailinglists, array ('id' => '0', 'title' => JText::_('COM_BWPOSTMAN_SUB_FILTER_MAILINGLISTS')));
		array_unshift($this->campaigns, array ('id' => '0', 'title' => JText::_('COM_BWPOSTMAN_SUB_FILTER_CAMPAIGNS')));
		array_unshift($this->usergroups, array ('id' => '0', 'title' => JText::_('COM_BWPOSTMAN_SUB_FILTER_USERGROUPS')));

		// Because the application sets a default page title, we need to get it
		// right from the menu item itself
		if (is_object($menu))
		{
			$menu_params = new JRegistry();
			$menu_params->loadString($menu->params, 'JSON');
			if (!$menu_params->get('page_heading'))
			{
				$this->params->set('page_heading',	JText::_('COM_BWPOSTMAN_NLS'));
			}
		}
		else
		{
			$this->params->set('page_heading',	JText::_('COM_BWPOSTMAN_NLS'));
		}

		// Get document object, set document title and add css
		$templateName	= $app->getTemplate();
		$css_filename	= '/templates/' . $templateName . '/css/com_bwpostman.css';

		$document = JFactory::getDocument();
		$document->setTitle($this->params->get('page_title'));

		$document->addStyleSheet(JUri::root(true) . '/components/com_bwpostman/assets/css/bwpostman.css');
		if (file_exists(JPATH_BASE . $css_filename))
		{
			$document->addStyleSheet(JUri::root(true) . $css_filename);
		}

		// Set parent display
		parent::display($tpl);

		return $this;
	}
}
