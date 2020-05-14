<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman tabs helper class for backend, based on joomla HTML tabs.
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

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;

/**
 * Utility class for Tabs elements.
 *
 * @since	1.0.1
 */

class JHtmlBwTabs
{
	/**
	 * Creates a panes and creates the JavaScript object for it.
	 *
	 * @param	string	$group		The pane identifier.
	 * @param	array	$params		An array of option.
	 *
	 * @return	string
	 *
	 * @since	1.0.1
	 */
	public static function start($group = 'tabs', $params = array())
	{
		self::_loadBehavior($group, $params);

		return '<dl class="tabs" id="' . $group . '"><dt style="display:none;"></dt><dd style="display:none;">';
	}

	/**
	 * Close the current pane
	 *
	 * @return	string	HTML to close the pane
	 *
	 * @since	1.0.1
	 */
	public static function end()
	{
		return '</dd></dl>';
	}

	/**
	 * Begins the display of a new panel.
	 *
	 * @param	string	$text	Text to display.
	 * @param	string	$id 	Identifier of the panel.
	 * @param	string	$event 	onClick event
	 *
	 * @return	string	HTML to start a new panel
	 *
	 * @since	1.0.1
	 */
	public static function panel($text, $id, $event)
	{
		return '</dd><dt class="tabs ' . $id . '"><h3><span><a href="javascript:void(0);" onclick="' . $event . '">' . $text . '</a></span></h3></dt><dd class="tabs">';
	}

	/**
	 * Load the JavaScript behavior.
	 *
	 * @param	string	$group		The pane identifier.
	 * @param	array	$params		Array of options.
	 *
	 * @return	void
	 *
	 * @since	1.0.1
	 */
	protected static function _loadBehavior($group, $params = array())
	{
		static $loaded = array();

		if (!array_key_exists((string) $group, $loaded))
		{
			// Include MooTools framework
			HtmlHelper::_('behavior.framework', true);

			$options = '{';
				$opt['onActive'] = (isset($params['onActive'])) ? $params['onActive'] : null;
				$opt['onBackground'] = (isset($params['onBackground'])) ? $params['onBackground'] : null;
				$opt['display'] = (isset($params['startOffset'])) ? (int) $params['startOffset'] : null;
				$opt['useStorage'] = (isset($params['useCookie']) && $params['useCookie']) ? 'true' : 'false';
				$opt['titleSelector'] = "'dt.tabs'";
				$opt['descriptionSelector'] = "'dd.tabs'";

			foreach ($opt as $k => $v)
			{
				if ($v)
				{
					$options .= $k . ': ' . $v . ',';
				}
			}

			if (substr($options, -1) == ',')
			{
				$options = substr($options, 0, -1);
			}

			$options .= '}';

			$js = '	window.addEvent(\'domready\', function(){
						$$(\'dl#' . $group . '.tabs\').each(function(tabs){
							new JTabs(tabs, ' . $options . ');
						});
					});';

			$document = Factory::getDocument();
			$document->addScriptDeclaration($js);
			JHtml::_('script', 'system/tabs.js', array('version' => 'auto', 'relative' => true));

			$loaded[(string) $group] = true;
		}
	}
}
