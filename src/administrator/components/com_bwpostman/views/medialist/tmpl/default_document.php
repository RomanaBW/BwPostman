<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman media list sub-template document for backend, based on joomla com_media.
 *
 * @version 2.0.2 bwpm
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

defined('_JEXEC') or die('Restricted access');

use Joomla\Registry\Registry as JRegistry;

$user	= JFactory::getUser();
$params	= new JRegistry;

JHtml::_('bootstrap.tooltip');
$dispatcher	= JEventDispatcher::getInstance();
$dispatcher->trigger('onContentBeforeDisplay', array('com_media.file', &$this->_tmp_doc, &$params));
?>

<li class="imgOutline thumbnail height-80 width-80 center">
	<a class="doc-preview" href="javascript:ImageManager.populateFields('<?php echo $this->_tmp_doc->path_relative; ?>')"
			title="<?php echo $this->_tmp_doc->name; ?>" >
		<div class="height-50">
			<?php echo JHtml::_('image', $this->_tmp_doc->icon_32, $this->_tmp_doc->name, null, true, true)
				? JHtml::_('image', $this->_tmp_doc->icon_32, $this->_tmp_doc->title, null, true)
				: JHtml::_('image', 'media/con_info.png', $this->_tmp_doc->name, null, true); ?>
		</div>
		<div class="small">
			<?php echo JHtml::_('string.truncate', $this->_tmp_doc->name, 10, false); ?>
		</div>
	</a>
</li>
<?php
$dispatcher->trigger('onContentAfterDisplay', array('com_media.file', &$this->_tmp_doc, &$params));


