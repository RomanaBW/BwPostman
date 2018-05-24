<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman media list default template for backend, based on joomla com_media.
 *
 * @version 2.0.1 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2018 Boldt Webservice <forum@boldt-webservice.de>
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

defined('_JEXEC') or die;
?>
<?php
if (count($this->images) > 0 || count($this->folders) > 0 || count($this->documents) > 0)
{ ?>
	<ul class="manager thumbnails">
		<?php
		for ($i = 0, $n = count($this->folders); $i < $n; $i++) :
			$this->setFolder($i);
			echo $this->loadTemplate('folder');
		endfor; ?>

		<?php for ($i = 0, $n = count($this->images); $i < $n; $i++) :
			$this->setImage($i);
			echo $this->loadTemplate('image');
		endfor; ?>

		<?php for ($i = 0, $n = count($this->documents); $i < $n; $i++) :
			$this->setDocument($i);
			echo $this->loadTemplate('document');
		endfor; ?>
	</ul>

<?php
}
else
{ ?>
	<div id="media-noimages">
		<div class="alert alert-info"><?php echo JText::_('COM_BWPOSTMAN_MEDIA_NO_MEDIA_FOUND'); ?></div>
	</div>
<?php
} ?>

