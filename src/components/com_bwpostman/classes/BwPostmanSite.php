<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman main class for frontend.
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

namespace BoldtWebservice\Component\BwPostman\Site\Classes;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\Plugin\PluginHelper;

/**
 * Class BwPostman
 *
 * @since       0.9.1
 */
class BwPostmanSite
{
	/**
	 * Method to write the BwPostman footer
	 *
	 * @return string
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public static function footer(): string
	{

		PluginHelper::importPlugin('bwpostman', 'copyright');

		$copyright = '<span>BwPostman by </span><a href="https://www.boldt-webservice.de" target="_blank">Boldt Webservice</a>';

		$arguments = array(&$copyright);
//		$arguments = array('eventClass' => '0815','copyright' => &$copyright);

//		$event = AbstractEvent::create('onPrepareBwpostman', $arguments);
//
//		$app->getDispatcher()->dispatch('onPrepareBwpostman', $event);

		return $arguments[0];
	}
}
