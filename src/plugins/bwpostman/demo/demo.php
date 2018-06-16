<?php
/**
 * BwPostman Demo Plugin
 *
 * BwPostman Demo Plugin main file for BwPostman.
 *
 * @version 2.0.2 bwpmpd
 * @package BwPostman Demo Plugin
 * @author Romana Boldt
 * @copyright (C) 2016-2018 Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/bwpostman.html
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

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

if (!JComponentHelper::isEnabled('com_bwpostman')) {
	JFactory::getApplication()->enqueueMessage(
		JText::_('PLG_BWPOSTMAN_PLUGIN_DEMO_ERROR') . ', ' . JText::_('PLG_BWPOSTMAN_PLUGIN_DEMO_COMPONENT_NOT_INSTALLED'),
		'error'
	);
	return false;
}

/**
 * Class plgBwPostmanDemo
 *
 * @since       2.0.0
 */
class plgBwPostmanDemo extends JPlugin
{
	// only a dummy
}

