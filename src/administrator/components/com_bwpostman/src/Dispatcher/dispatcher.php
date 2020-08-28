<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman main component for backend.
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

namespace BoldtWebservice\Component\BwPostman\Administrator\Dispatcher;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// constants and autoload
require_once __DIR__ . '../includes/includes.php';

use Joomla\CMS\Dispatcher\ComponentDispatcher;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;

/**
 * BwPostman Component Dispatcher
 *
 * @package 	BwPostman-Admin
 *
 * @since       4.0.0
 */
class BwpostmanDispatcher extends ComponentDispatcher
{
	public function dispatch()
	{
//		if ($this->input->get('view') === 'contacts' && $this->input->get('layout') === 'modal')
//		{
//			if (!$this->app->getIdentity()->authorise('core.create', 'com_bwpostman'))
//			{
//				$this->app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'warning');
//
//				return;
//			}
//
//			$this->app->getLanguage()->load('com_bwpostman', JPATH_ADMINISTRATOR);
//		}

		parent::dispatch();
	}
}
try
{
	// Get the user object
	$user = Factory::getUser();
	$app  = Factory::getApplication();
	// Access check.
	if ((!$user->authorise('core.manage', 'com_bwpostman')))
	{
		$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'warning');

		return false;
	}

	// Preload user permissions
	BwPostmanHelper::setPermissionsState();

	// Get an instance of the controller
	$controller = JControllerLegacy::getInstance('BwPostman');

	// Perform the Request task
	$jinput = Factory::getApplication()->input;
	$task   = $jinput->getCmd('task');
	$controller->execute($task);

	// Redirect if set by the controller
	$controller->redirect();
}
catch (Exception $exception)
{
	Text::_('JERROR_AN_ERROR_HAS_OCCURRED');
}
