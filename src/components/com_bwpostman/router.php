<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman main router for frontend.
 *
 * @version %%version_number%%
 * @package BwPostman-Site
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/bwpostman.html
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

/**
 *
 * Method to build sef route
 *
 * @param $query
 *
 * @return array
 *
 * @since
 */
function BwPostmanBuildRoute(&$query)
{
	$segments = array();

	if (isset($query['view']))
	{
		$segments[] = $query['view'];
		unset($query['view']);
	}

	if (isset($query['task']))
	{
		$segments[] = $query['task'];
		unset($query['task']);
	}

	if (isset($query['id']))
	{
		$segments[] = $query['id'];
		unset($query['id']);
	}

	return $segments;
}

/**
 * Method to decode SEF URI segments for BwPostman
 *
 * @access 	public
 *
 * @param 	array $segments     SEF URI segments array
 *
 * @return 	array $vars         associative array
 *
 * @since
 */
function BwPostmanParseRoute($segments)
{
	$vars = array();

	if (isset($segments[0]))
	{
		$vars['view'] = $segments[0];
	}

	if (isset($segments[1]))
	{
		$vars['task'] = $segments[1];
	}

	if (isset($segments[1]))
	{
		$vars['id'] = $segments[1];
	}

	return $vars;
}
