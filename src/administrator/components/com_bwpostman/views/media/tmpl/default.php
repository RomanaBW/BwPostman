<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman media template for backend, based on joomla com_media.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2017 Boldt Webservice <forum@boldt-webservice.de>
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

// Load tooltip instance without HTML support because we have a HTML tag in the tip
JHtml::_('bootstrap.tooltip', '.noHtmlTip', array('html' => false));
JHtml::_('formbehavior.chosen', 'select');

$user  = JFactory::getUser();
$jinput = JFactory::getApplication()->input;
?>

<script type='text/javascript'>
var image_base_path = '<?php $params = JComponentHelper::getParams('com_media');
echo $params->get('file_path', 'images'); ?>/';
</script>


<form action="index.php?option=com_bwpostman&amp;asset=<?php echo $jinput->getCmd('asset'); ?>&amp;author=<?php echo $jinput->getCmd('author'); ?>"
		class="form-vertical" id="imageForm" method="post" enctype="multipart/form-data">
	<div id="messages" style="display: none;">
		<span id="message"></span><?php echo JHtml::_('image', 'media/dots.gif', '...', array('width' => 22, 'height' => 12), true) ?>
	</div>

	<div class="well">
		<div class="row">
			<div class="span9 control-group">
				<div class="control-label">
					<label class="control-label" for="folder"><?php echo JText::_('COM_BWPOSTMAN_MEDIA_DIRECTORY') ?></label>
				</div>
				<div class="controls">
					<?php echo $this->folderList; ?>
					<button class="btn" type="button" id="upbutton" title="<?php echo JText::_('COM_BWPOSTMAN_MEDIA_DIRECTORY_UP') ?>">
						<?php echo JText::_('COM_BWPOSTMAN_MEDIA_UP') ?>
					</button>
				</div>
			</div>
			<div class="pull-right">
				<button class="btn btn-primary" type="button"
						onclick="<?php
						if ($this->state->get('field.id'))
						{ ?>
							window.parent.jInsertFieldValue(document.id('f_url').value,'<?php echo $this->state->get('field.id');?>');
							<?php
						}
						else
						{ ?>
							ImageManager.onok();<?php
						}
						?>
						window.parent.SqueezeBox.close();">
						<?php echo JText::_('COM_BWPOSTMAN_MEDIA_INSERT') ?>
				</button>
				<button class="btn" type="button" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('JCANCEL') ?></button>
			</div>
		</div>
	</div>

	<iframe id="imageframe" name="imageframe"
			src="index.php?option=com_bwpostman&amp;view=mediaList&amp;tmpl=component&amp;folder=&amp;
			asset=<?php echo $jinput->getCmd('asset');?>&amp;author=<?php echo $jinput->getCmd('author');?>">

	</iframe>

	<div class="well">
		<div class="row">
			<div class="span6 control-group">
				<div class="control-label">
					<label for="f_url"><?php echo JText::_('COM_BWPOSTMAN_MEDIA_MEDIA_URL') ?></label>
				</div>
				<div class="controls">
					<input type="text" id="f_url" value="" />
				</div>
			</div>
		</div>

		<input type="hidden" id="dirPath" name="dirPath" />
		<input type="hidden" id="f_file" name="f_file" />
		<input type="hidden" id="tmpl" name="component" />
	</div>
</form>

<?php if ($user->authorise('core.create', 'com_media'))
{ ?>
	<form action="
		<?php echo JUri::base(); ?>index.php?option=com_bwpostman&amp;task=file.upload&amp;tmpl=component&amp;
			<?php echo $this->session->getName() . '=' . $this->session->getId(); ?>&amp;<?php echo JSession::getFormToken();?>=1&amp;
			asset=<?php echo $jinput->getCmd('asset');?>&amp;author=<?php echo $jinput->getCmd('author');?>&amp;view=media"
			id="uploadForm" class="form-horizontal" name="uploadForm" method="post" enctype="multipart/form-data">
		<div id="uploadform" class="well">
			<fieldset id="upload-noflash" class="actions">
				<div class="control-group">
					<div class="control-label">
						<label for="upload-file" class="control-label"><?php echo JText::_('COM_BWPOSTMAN_MEDIA_UPLOAD_FILE'); ?></label>
					</div>
					<div class="controls">
						<input type="file" id="upload-file" name="Filedata[]" multiple />
						<button class="btn btn-primary" id="upload-submit">
							<i class="icon-upload icon-white"></i>
							<?php echo JText::_('COM_BWPOSTMAN_MEDIA_START_UPLOAD'); ?>
						</button>
						<p class="help-block">
							<?php
							echo
							$this->config->get('upload_maxsize') == '0'
								? JText::_('COM_BWPOSTMAN_MEDIA_UPLOAD_FILES_NOLIMIT')
								: JText::sprintf('COM_BWPOSTMAN_MEDIA_UPLOAD_FILES', $this->config->get('upload_maxsize')); ?>
						</p>
					</div>
				</div>
			</fieldset>
		</div>
		<?php JFactory::getSession()->set(
			'com_bwpostman.media.return_url',
			'index.php?option=com_bwpostman&view=media&tmpl=component&fieldid=' . $jinput->getCmd('fieldid', '')
			. '&e_name=' . $jinput->getCmd('e_name') . '&asset=' . $jinput->getCmd('asset') . '&author=' . $jinput->getCmd('author')
		); ?>
	</form>
<?php }








