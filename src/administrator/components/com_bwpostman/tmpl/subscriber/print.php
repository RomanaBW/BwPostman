<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single subscriber template for backend.
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
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php echo Text::_('COM_BWPOSTMAN_SUB_DATA_TITLE'); ?></title>
	<style>
		@page {
			margin-top: 1.5cm;
			margin-bottom: 2cm;
			margin-left: 2cm;
			margin-right: 1.5cm;
		}
		body {
			font-size: 14px;
			margin: 0;
			padding: 0;
		}
		table {
			width: 100%;
			border-collapse:collapse;
		}
		td {
			border: 1px solid;
			padding: 5px;
		}
		.left {
			width: 50%;
		}
		.sub-heading {
			font-size: 1.2em;
			font-weight: bold;
			margin-top: 30px;
		}
		p.heading {
			font-size   : 1.5em;
			font-weight : bold;
		}
		.btn {
			background-color: #46a546;
			border: 1px solid rgba(0, 0, 0, 0.2);
			color: #fff;
			border-radius: 3px;
			box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
			cursor: pointer;
			display: inline-block;
			font-size: 13px;
			line-height: 18px;
			margin-bottom: 0;
			margin-right: 20px;
			padding: 4px 30px;
			text-align: center;
			vertical-align: middle;
			text-decoration: none;
			float: right;
		}
		.btn:hover, .btn:focus {
			background-color: #2f6f2f;
			text-shadow: none;
		}
		@media print {
			.btn {
				display :none;
			}
		}
	</style>
</head>

<body id="preview_html">
<p class="heading"><?php echo Text::_('COM_BWPOSTMAN_SUB_DATA_TITLE'); ?>
	<a class="btn" href="javascript:window.print()"><?php echo Text::_('COM_BWPOSTMAN_PRINT'); ?></a>
</p>
<p class="date"><?php echo Text::_('COM_BWPOSTMAN_SUB_DATA_PRINTDATE') . ': ' . date("d.m.Y"); ?></p>
<p class="sub-heading"><?php echo Text::_('COM_BWPOSTMAN_SUB_DATA_REG'); ?></p>
<table>
	<tr>
		<td class="left">
			<strong><?php echo $this->form->getLabel('gender'); ?></strong>
		</td>
		<td>
			<?php
			if ($this->sub->gender === '1')
			{
				echo Text::_('COM_BWPOSTMAN_FEMALE');
			}
			elseif ($this->sub->gender === '0')
			{
				echo Text::_('COM_BWPOSTMAN_MALE');
			}
			else
			{
				echo Text::_('COM_BWPOSTMAN_NO_GENDER');
			}
			?>
		</td>
	</tr>
	<tr>
		<td class="left">
			<strong><?php echo $this->form->getLabel('firstname'); ?></strong>
		</td>
		<td>
			<?php echo $this->sub->firstname; ?>
		</td>
	</tr>
	<tr>
		<td class="left">
			<strong><?php echo $this->form->getLabel('name'); ?></strong>
		</td>
		<td>
			<?php echo $this->sub->name; ?>
		</td>
	</tr>
	<tr>
		<td class="left">
			<strong><?php echo $this->form->getLabel('email'); ?></strong>
		</td>
		<td>
			<?php echo $this->sub->email; ?>
		</td>
	</tr>
	<tr>
		<td class="left">
			<strong><?php echo $this->form->getLabel('special'); ?></strong>
		</td>
		<td>
			<?php echo $this->sub->special; ?>
		</td>
	</tr>
	<tr>
		<td class="left">
			<strong><?php echo Text::_('COM_BWPOSTMAN_SUBS_FIELD_EMAILFORMAT_LABEL'); ?></strong>
		</td>
		<td>
			<?php echo $this->sub->emailformat; ?>
		</td>
	</tr>
	<tr>
		<td class="left">
			<?php echo Text::_('COM_BWPOSTMAN_SUB_EXPORT_STATUS'); ?>
		</td>
		<td>
			<?php
			if ($this->sub->status === '1' && $this->sub->archive_flag === '0')
			{
				echo mb_strtolower(Text::_('COM_BWPOSTMAN_ARC_SUB_CONFIRMED'));
			}
			elseif ($this->sub->status === '0' && $this->sub->archive_flag === '0')
			{
				echo mb_strtolower(Text::_('COM_BWPOSTMAN_ARC_SUB_UNCONFIRMED'));
			}
			elseif ($this->sub->archive_flag !== '0')
			{
				echo mb_strtolower(Text::_('ARCHIVED'));
			}
			else
			{
				echo Text::_('COM_BWPOSTMAN_ARC_SUB_TEST');
			}
			?>
		</td>
	</tr>
</table>

<span><small><?php echo Text::_('COM_BWPOSTMAN_SUB_DATA_REQUIRED'); ?></small></span>
<p class="sub-heading"><?php echo Text::_('COM_BWPOSTMAN_SUB_DATA_AUTO'); ?></p>
<table>
	<tr>
		<td class="left">
			<strong><?php echo Text::_('COM_BWPOSTMAN_SUBS_FIELD_REGISTRATION_DATE_LABEL'); ?></strong>
		</td>
		<td>
			<?php echo $this->sub->registration_date; ?>
		</td>
	</tr>
	<?php
	if ($this->item->registered_by != '')
	{
		?>
		<tr>
			<td class="left">
				<strong><?php echo Text::_('COM_BWPOSTMAN_SUBS_FIELD_REGISTRATION_BY_LABEL'); ?></strong>
			</td>
			<td>
				<?php echo $this->sub->registered_by; ?>
			</td>
		</tr>
		<?php
	}
	?>
	<tr>
		<td class="left">
			<strong><?php echo Text::_('COM_BWPOSTMAN_SUBS_FIELD_REGISTRATION_IP_LABEL'); ?></strong>
		</td>
		<td>
			<?php echo $this->sub->registration_ip; ?>
		</td>
	</tr>
	<tr>
		<td class="left">
			<strong><?php echo Text::_('COM_BWPOSTMAN_SUBS_FIELD_CONFIRMATION_DATE_LABEL'); ?></strong>
		</td>
		<td>
			<?php
			if ($this->sub->status === '1')
			{
				echo $this->sub->confirmation_date;
			}
			else
			{
				echo Text::_('COM_BWPOSTMAN_ARC_SUB_UNCONFIRMED');
			}
			?>
		</td>
	</tr>
	<?php
	if ($this->item->status === '1')
	{
		if ($this->sub->confirmed_by != '')
		{
			?>
			<tr>
				<td class="left">
					<strong><?php echo Text::_('COM_BWPOSTMAN_SUBS_FIELD_CONFIRMATION_BY_LABEL'); ?></strong>
				</td>
				<td>
					<?php echo $this->sub->confirmed_by; ?>
				</td>
			</tr>
			<?php
		}
		?>
		<tr>
			<td class="left">
				<strong><?php echo Text::_('COM_BWPOSTMAN_SUBS_FIELD_CONFIRMATION_IP_LABEL'); ?></strong>
			</td>
			<td>
				<?php echo $this->sub->confirmation_ip; ?>
			</td>
		</tr>
		<?php
	}
	if ($this->sub->modified_by != '')
	{
		?>
		<tr>
			<td class="left">
				<strong><?php echo Text::_('COM_BWPOSTMAN_FIELD_MODIFIED_BY_DESC'); ?></strong>
			</td>
			<td>
				<?php echo $this->sub->modified_by; ?>
			</td>
		</tr>
		<tr>
			<td class="left">
				<strong><?php echo Text::_('COM_BWPOSTMAN_FIELD_MODIFIED_TIME_LABEL'); ?></strong>
			</td>
			<td>
				<?php echo $this->sub->modified_time; ?>
			</td>
		</tr>
		<?php
	}
	if ($this->sub->archive_flag !== '0')
	{
		?>
		<tr>
			<td class="left">
				<strong><?php echo Text::_('COM_BWPOSTMAN_SUB_ARCHIVE_DATE'); ?></strong>
			</td>
			<td>
				<?php echo $this->sub->archive_date; ?>
			</td>
		</tr>
		<tr>
			<td class="left">
				<strong><?php echo Text::_('COM_BWPOSTMAN_SUB_ARCHIVED_BY'); ?></strong>
			</td>
			<td>
				<?php echo $this->sub->archived_by; ?>
			</td>
		</tr>
		<?php
	}
	?>
</table>
<p class="sub-heading"><?php echo Text::_('COM_BWPOSTMAN_SUB_ML_SUBSCRIBED'); ?></p>
<table>
	<tr>
		<td class="left desc">
			<strong><?php echo Text::_('COM_BWPOSTMAN_ML_TITLE'); ?></strong>
		</td>
		<td>
			<strong><?php echo Text::_('COM_BWPOSTMAN_ML_DESCRIPTION'); ?></strong>
		</td>
	</tr>
	<?php
	$lists = $this->sub->lists;
	if (!empty($lists))
	{
		foreach ($lists AS $list)
		{
			?>
			<tr>
				<td class="left-desc">
					<?php echo $list->title; ?>
				</td>
				<td>
					<?php echo $list->description; ?>
				</td>
			</tr>
			<?php
		}
	}
	else
	{
		?>
		<tr>
			<td class="left-desc">
				<?php echo Text::_('COM_BWPOSTMAN_NO_DATA_FOUND'); ?>
			</td>
			<td>
				<?php echo Text::_('COM_BWPOSTMAN_NO_DATA_FOUND'); ?>
			</td>
		</tr>
		<?php
	}
	?>
</table>
</body>
</html>
