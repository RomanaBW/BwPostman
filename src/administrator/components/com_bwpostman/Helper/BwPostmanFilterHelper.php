<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman input field filter helper
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

namespace BoldtWebservice\Component\BwPostman\Administrator\Helper;

defined('JPATH_PLATFORM') or die;

use Exception;

/**
 * Component helper class
 *
 * @since  1.2.0
 */
class BwPostmanFilterHelper
{
	/**
	 * Applies the global text filters to arbitrary text as per settings for current user groups
	 *
	 * @param string $text The string to filter
	 *
	 * @return  string  The filtered string
	 *
	 * @throws Exception
	 *
	 * @since   2.5
	 */
/*	public static function filterContent(string $text): string
	{
		// Filter settings
		$config     = ComponentHelper::getParams('com_config', true);
		$user       = Factory::getApplication()->getIdentity();
		$userGroups = Access::getGroupsByUser($user->get('id'));

		$filters = $config->get('filters');

		$blackListTags       = array();
		$blackListAttributes = array();

		$customListTags       = array();
		$customListAttributes = array();

		$whiteListTags       = array();
		$whiteListAttributes = array();

		$whiteList  = false;
		$blackList  = false;
		$customList = false;
		$unfiltered = false;

		// Cycle through each of the user groups the user is in.
		// Remember they are included in the Public group as well.
		foreach ($userGroups as $groupId)
		{
			// May have added a group by not saved the filters.
			if (!isset($filters->$groupId))
			{
				continue;
			}

			// Each group the user is in could have different filtering properties.
			$filterData = $filters->$groupId;
			$filterType = strtoupper($filterData->filter_type);

			if ($filterType == 'NH')
			{
				// Maximum HTML filtering.
			}
			elseif ($filterType == 'NONE')
			{
				// No HTML filtering.
				$unfiltered = true;
			}
			else
			{
				// Black or white list.
				// Preprocess the tags and attributes.
				$tags           = explode(',', $filterData->filter_tags);
				$attributes     = explode(',', $filterData->filter_attributes);
				$tempTags       = array();
				$tempAttributes = array();

				foreach ($tags as $tag)
				{
					$tag = trim($tag);

					if ($tag)
					{
						$tempTags[] = $tag;
					}
				}

				foreach ($attributes as $attribute)
				{
					$attribute = trim($attribute);

					if ($attribute)
					{
						$tempAttributes[] = $attribute;
					}
				}

				// Collect the black or white list tags and attributes.
				// Each list is cumulative.
				if ($filterType == 'BL')
				{
					$blackList           = true;
					$blackListTags       = array_merge($blackListTags, $tempTags);
					$blackListAttributes = array_merge($blackListAttributes, $tempAttributes);
				}
				elseif ($filterType == 'CBL')
				{
					// Only set to true if Tags or Attributes were added
					if ($tempTags || $tempAttributes)
					{
						$customList           = true;
						$customListTags       = array_merge($customListTags, $tempTags);
						$customListAttributes = array_merge($customListAttributes, $tempAttributes);
					}
				}
				elseif ($filterType == 'WL')
				{
					$whiteList           = true;
					$whiteListTags       = array_merge($whiteListTags, $tempTags);
					$whiteListAttributes = array_merge($whiteListAttributes, $tempAttributes);
				}
			}

		}

		// Remove duplicates before processing (because the black list uses both sets of arrays).
		$blackListTags        = array_unique($blackListTags);
		$blackListAttributes  = array_unique($blackListAttributes);
		$customListTags       = array_unique($customListTags);
		$customListAttributes = array_unique($customListAttributes);
		$whiteListTags        = array_unique($whiteListTags);
		$whiteListAttributes  = array_unique($whiteListAttributes);

		// Unfiltered assumes first priority.
		if ($unfiltered)
		{
			// Don't apply filtering.
		}
		else
		{
			// Custom blacklist precedes Default blacklist
			if ($customList)
			{
				$filter = JFilterInput::getInstance(array(), array(), 1, 1);

				// Override filter's default blacklist tags and attributes
				if ($customListTags)
				{
					$filter->tagBlacklist = $customListTags;
				}

				if ($customListAttributes)
				{
					$filter->attrBlacklist = $customListAttributes;
				}
			}
			// Black lists take second precedence.
			elseif ($blackList)
			{
				// Remove the white-listed tags and attributes from the black-list.
				$blackListTags       = array_diff($blackListTags, $whiteListTags);
				$blackListAttributes = array_diff($blackListAttributes, $whiteListAttributes);

				$filter = JFilterInput::getInstance($blackListTags, $blackListAttributes, 1, 1);

				// Remove white listed tags from filter's default blacklist
				if ($whiteListTags)
				{
					$filter->tagBlacklist = array_diff($filter->tagBlacklist, $whiteListTags);
				}
				// Remove white listed attributes from filter's default blacklist
				if ($whiteListAttributes)
				{
					$filter->attrBlacklist = array_diff($filter->attrBlacklist, $whiteListAttributes);
				}
			}
			// White lists take third precedence.
			elseif ($whiteList)
			{
				// Turn off XSS auto clean
				$filter = JFilterInput::getInstance($whiteListTags, $whiteListAttributes, 0, 0, 0);
			}
			// No HTML takes last place.
			else
			{
				$filter = JFilterInput::getInstance();
			}

			$text = $filter->clean($text, 'html');

		}
		return $text;
	}
*/
	/**
	 * Method to check, if value contains a link
	 *
	 * @param string $value The string to check
	 *
	 * @return  boolean  true if value contains link
	 *
	 * @since   3.0.0
	 */
	public static function containsLink(string $value): bool
	{
		$patterns = array(
			"/(http|https|ftp):\/\//i",
			 "/www\./i",
		);

		foreach ($patterns as $pattern)
		{
			preg_match($pattern, $value, $matches);

			if (count($matches) > 0)
			{
				return true;
			}
		}

		return false;
	}
}
