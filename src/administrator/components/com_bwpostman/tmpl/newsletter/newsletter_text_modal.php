<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single newsletter Text modal template for backend.
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de-de" lang="de-de" dir="ltr">
	<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php echo Text::_('COM_BWPOSTMAN_NL_SHOW_TEXT'); ?></title>
	</head>

	<body id="preview_html">
		<fieldset>
					<table border="0">
						<tr>
							<td align="right">
								<strong><?php
									echo Text::_('COM_BWPOSTMAN_NL_FROM_NAME');
									echo ':'; ?>
								</strong>
							</td>
							<td><?php echo $this->item->from_name;?></td>
						</tr>
						<tr>
							<td align="right">
								<strong><?php
									echo Text::_('COM_BWPOSTMAN_NL_FROM_EMAIL');
									echo ':'; ?>
								</strong>
							</td>
							<td><?php echo $this->item->from_email;?></td>
						</tr>
						<tr>
							<td align="right">
								<strong><?php
									echo Text::_('COM_BWPOSTMAN_NL_REPLY_EMAIL');
									echo ':'; ?>
								</strong>
							</td>
							<td><?php echo $this->item->reply_email;?></td>
						</tr>
						<tr>
							<td align="right">
								<strong><?php
									echo Text::_('COM_BWPOSTMAN_NL_SUBJECT');
									echo ':'; ?>
								</strong>
							</td>
							<td><?php echo $this->item->subject;?></td>
						</tr>
					</table>
				</fieldset>

		<fieldset>
			<table width="100%" border="0">
				<tr>
					<td><textarea readonly="readonly" name="text_version"  title="text_version"
						cols="80" rows="13"
						style="width: 100%; border: 0; margin: 0; padding: 0;"><?php echo $this->item->text_formatted; ?></textarea>
					</td>
				</tr>
			</table>
		</fieldset>
	</body>
</html>
