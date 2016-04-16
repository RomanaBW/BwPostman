<?php
/**
 * BwPostman Newsletter QuickIcon Plugin
 *
 * BwPostman QuickIcon Plugin for backend.
 *
 * @version			9.1.3.0 bwpm
 * @package			BwPostman-Admin
 * @author			Romana Boldt
 * @copyright		(C) 2012-2015 Boldt Webservice <forum@boldt-webservice.de>
 * @support			http://www.boldt-webservice.de/forum/bwpostman.html
 * @license			GNU/GPL v3, see LICENSE.txt
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
defined ( '_JEXEC' ) or die ( 'Restricted access' );

// Require class
//require_once (JPATH_COMPONENT_ADMINISTRATOR.'/classes/admin.class.php');
//require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');

class plgQuickiconBwPostman extends JPlugin {

	function __construct($subject, $config) {
		$app	= JFactory::getApplication();
		// Do not load if BwPostman version is not supported or BwPostmanNewsletter isn't detected
        if ($app->isSite() || JComponentHelper::getComponent('com_bwpostman', true)->enabled === false) {
            return;
        }
		
		parent::__construct ( $subject, $config );

		$this->loadLanguage('plg_quickicon_bwpostman.sys');
	}

	/**
	 * Display BwPostman backend icon in Joomla 2.5+
	 *
	 * @param string $context
	 */
	public function onGetIcons($context) {
/*		if (!$context == 'mod_quickicon' || !JFactory::getUser()->authorise('core.manage', 'com_bwpostman')) {
			return;
		}
		BwPostmanHelper::loadLanguage('com_bwpostman.sys', 'admin');
		
		if (BwPostmanHelper::installed() 
//			&& BwPostmanHelper::getConfig()->version_check 
//			&& JFactory::getUser()->authorise('core.manage', 'com_installer')
			) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select($db->qn('params'))
				->from($db->qn('#__extensions'))
				->where($db->qn('type').' = '.$db->q('component'))
				->where($db->qn('element').' = '.$db->q('com_bwpostman'));
			$db->setQuery($query);
			$cparams = new JRegistry((string) $db->loadResult());

			//$cparams = JComponentHelper::getParams('com_bwpostman');
			$liveupdate = new JRegistry($cparams->get('liveupdate', null));
			$lastCheck = $liveupdate->get('lastcheck', 0);
			$updateInfo = json_decode(trim((string) $liveupdate->get('updatedata', ''), '"'));
			$valid = abs(time() - $lastCheck) <= 24 * 3600; // 24 hours

			if (!$valid) {
				// If information is not valid, update it asynchronously.
				$ajax_url = json_encode(JURI::base().'index.php?option=com_bwpostman&view=liveupdate&task=ajax');
				$script = "window.addEvent('domready', function() {
	var com_bwpostman_updatecheck_ajax_structure = {
		onSuccess: function(msg, responseXML) {
			var updateInfo = JSON.decode(msg, true);
			if (updateInfo.html) {
				document.id('com_bwpostman_icon').getElement('img').setProperty('src',updateInfo.img);
				document.id('com_bwpostman_icon').getElement('span').set('html', updateInfo.html);
				document.id('com_bwpostman_icon').getElement('a').set('href', updateInfo.link);
			}
		},
		url: {$ajax_url}
	};
	ajax_object = new Request(com_bwpostman_updatecheck_ajax_structure);
	ajax_object.send();
});";

				$document = JFactory::getDocument();
				$document->addScriptDeclaration($script);
			}
		}

		$link = 'index.php?option=com_bwpostman';

		if(!BwPostmanHelper::installed()) {
			// Not fully installed
//dumpMessage('Test Quickicon Not Fully Installed');
			$img = 'com_bwpostman/images/icons/icon-48-bwpupdate-alert.png';
			$text = JText::_('PLG_QUICKICON_BWPOSTMAN_COMPLETE_INSTALLATION');

		} elseif (empty($updateInfo->supported)) {
			// Unsupported
//dumpMessage('Test Quickicon Unsupported');
			$img = 'com_bwpostman/images/icons/icon-48-bwpostman.png';
			$text = JText::_('COM_BWP');

		} elseif ($updateInfo->stuck) {
			// Stuck
//dumpMessage('Test Quickicon Stuck');
			$img = 'com_bwpostman/images/icons/icon-48-bwpupdate-alert.png';
			$text = JText::_('COM_BWP') . '<br />' . JText::_('PLG_QUICKICON_BWPOSTMAN_UPDATE_CRASH');

		} elseif (version_compare(BwPostmanForum::version(), $updateInfo->version, '<')) {
			// Has updates
//dumpMessage('Test Quickicon HasUpdates');
			$img = 'com_bwpostman/images/icons/icon-48-bwpupdate-update.png';
			$text = 'BwPostman ' . $updateInfo->version . '<br />' . JText::_('PLG_QUICKICON_BWPOSTMAN_UPDATE_AVAILABLE');
			$link .= '&view=liveupdate';

		} else {
			// Already in the latest release
//dumpMessage('Test Quickicon UpToDate');
			$img = 'com_bwpostman/icons/icon-48-bwpupdate-good.png';
			$text = JText::_('COM_BWP');
		}

		return array( array(
			'link' => JRoute::_($link),
			'image' => $img,
			'text' => $text,
			'access' => array('core.manage', 'com_bwpostman'),
			'id' => 'com_bwpostman_icon' ) );
*/	}
}