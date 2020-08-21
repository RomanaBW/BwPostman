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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;

// Import VIEW object class
jimport('joomla.application.component.view');

/**
 * BwPostman template View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	template
 *
 * @since       1.1.0
 */
class BwPostmanViewTemplate extends JViewLegacy
{
	/**
	 * property to hold preview data
	 *
	 * @var string  $pre
	 *
	 * @since       1.1.0
	 */
	protected $pre;

	/**
	 * property to hold permissions as array
	 *
	 * @var array $permissions
	 *
	 * @since       2.0.0
	 */
	public $permissions;

	/**
	 * Display
	 *
	 * @param	string $tpl Template
	 *
	 * @since	1.1.0
	 *
	 * @throws Exception
	 *
	 * @return void
	 */
	public function display($tpl = null)
	{
		$app		= Factory::getApplication();

		$this->permissions		= Factory::getApplication()->getUserState('com_bwpm.permissions');

		if (!$this->permissions['view']['template'])
		{
			$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', Text::_('COM_BWPOSTMAN_TPLS')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		// load template data and decode object
		$pre = Factory::getApplication()->getUserState('com_bwpostman.edit.template.tpldata');

		$this->pre	= $pre;

		// clear preview data
		Factory::getApplication()->setUserState('com_bwpostman.edit.template.tpldata', null);

		// Call parent display
		parent::display($tpl);
	}
}
