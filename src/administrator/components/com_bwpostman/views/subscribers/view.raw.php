<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single text (raw) subscribers view for backend.
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

// Import VIEW object class
jimport('joomla.application.component.view');

/**
 * BwPostman Subscriber RAW View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	Subscriber
 *
 * @since       2.4.0
 */
class BwPostmanViewSubscribers extends JViewLegacy
{
	/**
	 * property to hold permissions as array
	 *
	 * @var array $permissions
	 *
	 * @since       2.4.0
	 */
	public $permissions;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @throws Exception
	 *
	 * @since       2.4.0
	 */
	public function display($tpl = null)
	{
		$app	= JFactory::getApplication();
		$jinput	= $app->input;

		$this->permissions		= JFactory::getApplication()->getUserState('com_bwpm.permissions');

		if (!$this->permissions['view']['subscriber'])
		{
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', JText::_('COM_BWPOSTMAN_SUB')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		// Call parent display
		parent::display($tpl);
		return $this;
	}
}
