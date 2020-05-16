<?php
/**
 * BwPostman Mediaoverride Plugin
 *
 * BwPostman Mediaoverride Plugin main file for BwPostman.
 *
 * @version %%version_number%%
 * @package BwPostman Mediaoverride Plugin
 * @author Romana Boldt, Karl Klostermann
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
 * @license GNU/GPL v3, see LICENSE.txt
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

use Joomla\CMS\Factory;
/**
 * Class MediaOverride
 *
 * @since  2.3.0
 */
class plgSystemBWPM_MediaOverride extends JPlugin {
	/**
	 * plgSystemBWPM_MediaOverride constructor.
	 *
	 * @param object $subject
	 * @param array  $config
	 *
	 * @since   2.3.0
	 */
	public function __construct(&$subject, $config = array()) {
		parent::__construct($subject, $config);
	}

	/**
	 * Method to set status of component activation property
	 *
	 * @return void
	 *
	 * @throws \Exception
	 *
	 * @since 2.3.0
	 */
	public function onAfterRoute() {
		$app = Factory::getApplication();
		$jinput	= $app->input;

		if($jinput->get('option') == 'com_media' && $jinput->get('asset') == 'com_bwpostman_nl' && $app->isAdmin())
		{
			require_once(dirname(__FILE__) . '/code/com_media/views/imageslist/view.html.php');
		}
	}
}
