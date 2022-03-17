<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman HTML Content Class.
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Utility class for writing the HTML for content
 * --> Referring to Communicator 2.0.0rc1
 *
 * @package 		BwPostman-Admin
 * @subpackage 	Newsletters
 *
 * @since       2.3.0 here (moved from newsletter model)
 */
class HtmlContent
{
	/**
	 * Writes Title
	 *
	 * @param object $row
	 * @param object $params
	 *
	 * @return  void
	 *
	 * @since       0.9.1
	 */
	public function Title(object $row, object $params)
	{
		?>
		<h2><?php echo $row->title; ?></h2>
		<?php
	}

	/**
	 * Writes p-tag for Author and CreateDate
	 *
	 * @return  void
	 *
	 * @since       2.0.0
	 */
	public function ArticleInfoBegin()
	{
		?>
		<p class="article-info">
		<?php
	}

	/**
	 * Writes p-tag for Author and CreateDate
	 *
	 * @return  void
	 *
	 * @since       2.0.0
	 */
	public function ArticleInfoEnd()
	{
		?>
		</p>
		<?php
	}

	/**
	 * Writes Author name
	 *
	 * @param object $row
	 *
	 * @return  void
	 *
	 * @since       0.9.1
	 */
	public function Author(object $row)
	{
		?>
		<span class="created_by">
			<small>
				<?php echo Text::sprintf('COM_CONTENT_WRITTEN_BY',
					($row->created_by_alias ?: $row->author)); ?>
			</small>
		</span>
		<?php
	}

	/**
	 * Writes Create Date
	 *
	 * @param object $row
	 *
	 * @return  void
	 *
	 * @since       0.9.1
	 */
	public function CreateDate(object $row)
	{
		$create_date = null;

		if (intval($row->created) != 0)
		{
			$create_date = HtmlHelper::_('date', $row->created);
		}

		?>
		<span class="createdate">
			<small><?php echo Text::sprintf('COM_CONTENT_CREATED_DATE_ON',
					$create_date); ?>&nbsp;&nbsp;&nbsp;&nbsp;</small>
		</span>
		<?php
	}
}
