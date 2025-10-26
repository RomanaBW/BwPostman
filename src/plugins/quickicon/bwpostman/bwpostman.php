<?php
/**
 * BwPostman Newsletter QuickIcon Plugin
 *
 * BwPostman QuickIcon Plugin for backend.
 *
 * @version			9.1.3.0 bwpm
 * @package			BwPostman-Admin
 * @author			Romana Boldt
 * @copyright		(C) 2012-2018 Boldt Webservice <forum@boldt-webservice.de>
 * @support			https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;

defined('_JEXEC') or die('Restricted access');

// Require class
//require_once (JPATH_COMPONENT_ADMINISTRATOR.'/classes/admin.class.php');
//require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');

/**
 * Class PlgQuickiconBwPostman
 *
 * @since       0.9
 */
class PlgQuickiconBwPostman extends CMSPlugin
{
	/**
	 * Constructor.
	 *
	 * @param	object $subject
	 * @param	array  $config An optional associative array of configuration settings.
	 *
	 * @throws Exception
	 *
	 * @since	0.9
	 *@see		JController
	 *
	 */
	public function __construct($subject, array $config)
	{
		$app	= Factory::getApplication();
		// Do not load if BwPostman version is not supported or BwPostmanNewsletter isn't detected
		if ($app->isClient('site') || ComponentHelper::getComponent('com_bwpostman', true)->enabled === 0) {
			return;
		}

		parent::__construct($subject, $config);

		$this->loadLanguage('plg_quickicon_bwpostman.sys');
	}

	/**
	 * Display BwPostman backend icon in Joomla 2.5+
	 *
	 * @param string $context
	 *
	 * @since 0.9
	 */
	public function onGetIcons(string $context)
	{
	}
}
