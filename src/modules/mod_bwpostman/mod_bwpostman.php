<?php
/**
 * BwPostman Module
 *
 * BwPostman main part of module.
 *
 * @version %%version_number%%
 * @package BwPostman-Module
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
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Helper\ModuleHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanSubscriberHelper;

require_once(dirname(__FILE__) . '/helper.php');
require_once JPATH_ROOT . '/administrator/components/com_bwpostman/helpers/subscriberhelper.php';

jimport('joomla.application.component.helper');

$app      = Factory::getApplication();
$document = Factory::getDocument();
$module   = ModuleHelper::getModule('mod_bwpostman');

// Require component admin helper class
if (!is_file(JPATH_ADMINISTRATOR . '/components/com_bwpostman/bwpostman.php'))
{
	$app->enqueueMessage(Text::_('MOD_BWPOSTMANERROR_COMPONENT_NOT_INSTALLED'), 'error');
	return false;
}

require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/helpers/helper.php');

// Get document object, set document title and add css
$templateName	= $app->getTemplate();
$css_filename	= '/templates/' . $templateName . '/css/mod_bwpostman.css';

if (!ComponentHelper::isEnabled('com_bwpostman'))
{
	$app->enqueueMessage(Text::_('Module requires the com_bwpostman component'), 'error');
}
else
{
	$user     = Factory::getUser();
	$userid   = $user->get('id');
	$usertype = '';

	$subscriberid = modBwPostmanHelper::getSubscriberID();
	$captcha      = BwPostmanHelper::getCaptcha(1);

	// use module or component parameters
	if ($params->get('com_params', '1') == 0)
	{
		// Module params
		$paramsComponent = $params;
		$module_id       = $module->id;
	}
	else
	{
		// Get the parameters of the component
		// --> we need these parameters because we have to ensure that both the component and the module will work with the same settings
		$paramsComponent = ComponentHelper::getParams('com_bwpostman');
		$module_id   = '';
	}

	if ($subscriberid)
	{
		$layout = "_linktocomponent";
	}
	else
	{
		$layout = $params->get('layout', 'default');

		if ($userid > 0)
		{
			$subscriber = modBwPostmanHelper::getUserData($userid);
		}

	// Build the email format select list
	$lists['emailformat'] = $emailformat = ModBwPostmanHelper::getMailformatSelectList($paramsComponent);

		// Build the gender select list
		$lists['gender'] = BwPostmanSubscriberHelper::buildGenderList('2', 'a_gender', null, 'm_');

	// Get the checked mailinglists from module parameters
	$mod_mls = (array)$params->get('mod_ml_available', '');

	// Get the access levels for the user, preset with access level guest and public
	$publicAccess = array(1, 5);
	$userAccess   = JAccess::getAuthorisedViewLevels($userid);
	$accessTypes  = array_unique(array_merge($publicAccess, $userAccess));

	// Get the available mailinglists
	$mailinglists = modBwPostmanHelper::getMailinglists($accessTypes, $mod_mls);

		$n = count($mailinglists);

		// Build the mailinglist select list
		$available_mailinglists	= array();
		// only when count($mailinglists) > 0
		if ($n > 0)
		{
			foreach ($mailinglists AS $mailinglist)
			{
				$available_mailinglists[] = HTMLHelper::_('select.option', $mailinglist->id, $mailinglist->title . ':<br />' . $mailinglist->description);
			}
		}

		$lists['list']	= HTMLHelper::_(
			'select.genericlist',
			$available_mailinglists,
			'list[]',
			'class="inputbox" size="' . $n . '" multiple="multiple" style="padding: 6px; width: 150px;"',
			'value',
			'text'
		);
	}

	$itemid = BwPostmanSubscriberHelper::getMenuItemid('register');

	$path = ModuleHelper::getLayoutPath('mod_bwpostman', $layout);

	if (file_exists($path))
	{
		require($path);
	}
}
