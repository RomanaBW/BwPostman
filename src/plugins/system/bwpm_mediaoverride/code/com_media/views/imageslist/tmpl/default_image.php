<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;

$params     = new Registry;
Factory::getApplication()->triggerEvent('onContentBeforeDisplay', array('com_media.file', &$this->_tmp_img, &$params));
?>

<li class="imgOutline thumbnail height-80 width-80 center">
	<a class="img-preview" href="javascript:ImageManager.populateFields('<?php echo $this->escape($this->_tmp_img->path_relative); ?>')" title="<?php echo $this->escape($this->_tmp_img->name); ?>" >
		<div class="height-50">
			<?php echo HTMLHelper::_('image', $this->_tmp_img->thumb, Text::sprintf('COM_MEDIA_IMAGE_TITLE', $this->escape($this->_tmp_img->title), HTMLHelper::_('number.bytes', $this->_tmp_img->size)), array('width' => $this->_tmp_img->width_60, 'height' => $this->_tmp_img->height_60)); ?>
		</div>
		<div class="small">
			<?php echo HTMLHelper::_('string.truncate', $this->escape($this->_tmp_img->name), 10, false); ?>
		</div>
	</a>
</li>
<?php
Factory::getApplication()->triggerEvent('onContentAfterDisplay', array('com_media.file', &$this->_tmp_img, &$params));
