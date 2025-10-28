<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman subscribers check for only filtered subscribers to export.
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

<head>
	<style>
		.btn {
			margin-right: .5rem;
			background: var(--white);
			border-color: var(--whiteoffset);
			color: var(--atum-text-dark);
			padding: 0 22px;
			font-size: 1rem;
			line-height: 2.375rem;
			box-shadow: 1px 1px 1px 0
			rgba(0,0,0,.25);
		}
		.btn-success {
			color:#fff;
			background-color:#2f7d32;
			border-color:#2f7d32;
		}
		.btn-success:hover {
			color:#fff;
			background-color:#256127;
			border-color:#215823;
		}
		.btn-success:focus {
			box-shadow:0 0 0 .2rem rgba(78,145,81,.5);
		}
		p {
			text-align:center;
			margin:1.5rem;
		}
		.text {
			font-size:1.2rem;
		}
	</style>
	<title></title>
</head>
<body>
<form name="popupForm" action="#" method="get">
	<fieldset>
		<div id="modal-upload">
			<p class="text"><?php echo Text::_("COM_BWPOSTMAN_SUB_CONFIRM_EXPORT_FILTERED"); ?></p>
			<p>
				<input class="btn btn-success" type="button" name="submitbutton" onClick="window.parent.OnlyFiltered('1')" value="<?php echo Text::_("COM_BWPOSTMAN_YES");?>" />
				<input class="btn btn-secondary" type="button" name="submitbutton" onClick="window.parent.OnlyFiltered('0')" value="<?php echo Text::_("COM_BWPOSTMAN_NO");?>" />
			</p>
		</div>
	</fieldset>
</form>
</body>
