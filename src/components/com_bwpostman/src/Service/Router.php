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

namespace BoldtWebservice\Component\BwPostman\Site\Service;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Component\Router\RouterBase;

/**
 * Routing class of com_bwpostman
 *
 * @since  4.0.0
 */
class Router extends RouterBase
{
	/**
	 * Build the route for the com_bwpostman component
	 *
	 * @param   array  $query  An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   4.0.0
	 */
	public function build(&$query)
	{
		$segments = array();

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

		$total = \count($segments);

		for ($i = 0; $i < $total; $i++)
		{
			$segments[$i] = str_replace(':', '-', $segments[$i]);
		}

		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array  $segments  The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 * @since   4.0.0
	 */
	public function parse(&$segments)
	{
		$total = \count($segments);
		$vars = array();

		for ($i = 0; $i < $total; $i++)
		{
			$segments[$i] = preg_replace('/-/', ':', $segments[$i], 1);
		}

		// View is always the first element of the array
		$count = \count($segments);

		if ($count)
		{
			$count--;
			$segment = array_shift($segments);

			if (\is_numeric($segment))
			{
				$vars['id'] = $segment;
			}
			else
			{
				$vars['task'] = $segment;
			}
		}

		if ($count)
		{
			$segment = array_shift($segments);

			if (\is_numeric($segment))
			{
				$vars['id'] = $segment;
			}
		}

		return $vars;
	}
}
