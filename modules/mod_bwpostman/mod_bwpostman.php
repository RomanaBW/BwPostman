<?php
/**
 * BwPostman Module
 *
 * BwPostman main part of module.
 *
 * @version 1.2.4 bwpm
 * @package BwPostman-Module
 * @author Romana Boldt
 * @copyright (C) 2012-2015 Boldt Webservice <forum@boldt-webservice.de>
 * @support http://www.boldt-webservice.de/forum/bwpostman.html
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
defined ('_JEXEC') or die ('Restricted access');

require_once (dirname(__FILE__).'/helper.php');

jimport('joomla.application.component.helper');

$app		= JFactory::getApplication();
$document	= JFactory::getDocument();

// Require component admin helper class
if (is_file(JPATH_ADMINISTRATOR.'/components/com_bwpostman/bwpostman.php')) {
	require_once (JPATH_ADMINISTRATOR.'/components/com_bwpostman/helpers/helper.php');
}
else {
	$app->enqueueMessage(JText::_('MOD_BWPOSTMANERROR_COMPONENT_NOT_INSTALLED'), 'error');
	return false;
}


// Get document object, set document title and add css
$templateName	= $app->getTemplate();
$css_filename	= '/templates/' . $templateName . '/css/mod_bwpostman.css';

$document->addStyleSheet(JURI::root(true) . '/modules/mod_bwpostman/css/bwpostman.css');
if (file_exists(JPATH_BASE . $css_filename)) {
	$document->addStyleSheet(JURI::root(true) . $css_filename);
}

if (!JComponentHelper::isEnabled('com_bwpostman', true)) {
	$app->enqueueMessage(JText('Module requires the com_bwpostman component'), 'error');
}
else {
	$user		= JFactory::getUser();
	$userid		= $user->get('id');
	$usertype	= '';

	$subscriberid	= modBwPostmanHelper::getSubscriberID();
	$captcha		= BwPostmanHelper::getCaptcha(1);

	// use module or component parameters
	if ($params->get('com_params') == 0) {
		// Moduleparams
		$paramsComponent = $params;
	}
	else {
		// Get the parameters of the component
		// --> we need these parameters because we have to ensure that both the component and the module will work with the same settings
		$paramsComponent = $app->getPageParameters('com_bwpostman');
	}

	if ($subscriberid) {
		$layout = "_linktocomponent";
	}
	else {
		$layout = "default";

		if ($userid > 0) $subscriber = modBwPostmanHelper::getUserData($userid);

		// Build the emailormat select list
		$emailformat 			= array();
		$emailformat[] 			= JHTML::_('select.option',  '0', '<span>' . JText::_('COM_BWPOSTMAN_TEXT') . '</span>');
		$emailformat[]			= JHTML::_('select.option',  '1', '<span>' . JText::_('COM_BWPOSTMAN_HTML') . '</span>');
		$lists['emailformat']	= JHTML::_('select.radiolist',  $emailformat, 'a_emailformat', 'class="checkbox" ', 'value', 'text', $paramsComponent->get('default_emailformat'));

		// Get the usertype
		$usertype	= JUserHelper::getUserGroups($userid);

		// Get the checked mailinglists from module parameters
		$mod_mls = $params->get('mod_ml_available');

		// Get the available mailinglists
		$mailinglists = modBwPostmanHelper::getMailinglists($usertype, $mod_mls);

		$n = count($mailinglists);

		// Build the mailinglist select list
		$available_mailinglists	= array();
		// only when count($mailinglists) > 0
		if ($n > 0) {
			foreach ($mailinglists AS $mailinglist) {
				$available_mailinglists[] = JHTML::_('select.option', $mailinglist->id, $mailinglist->title .':<br />'.$mailinglist->description);
			}
		}
		$lists['list']	= JHTML::_('select.genericlist', $available_mailinglists, 'list[]', 'class="inputbox" size="'.$n.'" multiple="multiple" style="padding: 6px; width: 150px;"', 'value', 'text');
	}

	$itemid = modBwPostmanHelper::getItemID();

	$path = JModuleHelper::getLayoutPath('mod_bwpostman', $layout);

	if (file_exists($path)) require ($path);
}
