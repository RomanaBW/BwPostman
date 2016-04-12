<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single campaigns form template for backend.
 *
 * @version 1.3.2 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2015 Boldt Webservice <forum@boldt-webservice.de>
 * @support http://www.boldt-webservice.de/forum/bwpostman.html
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
defined ('_JEXEC') or die ('Restricted access');

$text	= JText::_('COM_BWPOSTMAN_CAM_UNSENT_NLS');
if (property_exists($this->item, 'automailing_values')) {
	if ($this->item->automailing_values !== null) {
		$text	= JText::_('COM_BWPOSTMAN_CAM_ASSIGNED_NL');
	}
}

?>

<fieldset class="adminform">
	<legend><?php echo $text; ?></legend>
	<div class="well well-small">
		<?php
		//Show tabs with sent and unsent newsletters if we edit a campaign
		if (!empty($this->item->id)) {
			//Show no tabs if there is no newsletter assigned
			if (empty($this->newsletters->unsent)) {
				echo JText::_('COM_BWPOSTMAN_CAM_NO_ASSIGNED_NL');
				//Show tabs
			}
			else { ?>
				<table class="adminlist" width="100%">
					<thead>
						<tr>
							<th width="30"><?php echo JText::_('NUM'); ?></th>
							<th align="left"><?php echo JText::_('SUBJECT'); ?></th>
							<th width="150"><?php echo JText::_('COM_BWPOSTMAN_NL_LAST_MODIFICATION_DATE'); ?></th>
							<th width="150"><?php echo JText::_('AUTHOR'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php
						$k = 0;

						$newsletters_unsent = $this->newsletters->unsent;
						for ($i=0, $n=count($newsletters_unsent); $i < $n; $i++) {
							$item = &$newsletters_unsent[$i];

							$link_html = 'index.php?option=com_bwpostman&amp;view=newsletter&amp;format=raw&amp;layout=newsletter_html_modal&amp;task=insideModal&amp;nl_id='. $item->id;
							$link_text = 'index.php?option=com_bwpostman&amp;view=newsletter&amp;format=raw&amp;layout=newsletter_text_modal&amp;task=insideModal&amp;nl_id='. $item->id;
							?>
							<tr class="<?php echo "item$k"; ?>">
								<td align="center"><?php echo $i+1; ?></td>
								<td><?php echo $item->subject; ?>&nbsp;&nbsp; <span
									class="cam_preview"> <span class="editlinktip hasTip"
									title="<?php echo JText::_('COM_BWPOSTMAN_NL_SHOW_HTML');?>::<?php echo $this->escape($item->subject); ?>">
									<?php echo '<a class="popup" href="'.$link_html.'" rel="{handler: \'iframe\', size: {x: 600, y: 450}}">'.JText::_('COM_BWPOSTMAN_HTML_NL').'</a>'; ?>&nbsp;
								</span> <span class="editlinktip hasTip"
									title="<?php echo JText::_('COM_BWPOSTMAN_NL_SHOW_TEXT');?>::<?php echo $this->escape($item->subject); ?>">
									<?php echo '<a class="popup" href="'.$link_text.'" rel="{handler: \'iframe\', size: {x: 600, y: 450}}">'.JText::_('COM_BWPOSTMAN_TEXT_NL').'</a>'; ?>
								</span> </span></td>
								<td align="center"><?php echo JHtml::date($item->modified_time, JText::_('BW_DATE_FORMAT_LC5')); ?></td>
								<td align="center"><?php echo $item->author; ?></td>
							</tr>
							<?php
							$k = 1 - $k;
						}
					?>
					</tbody>
				</table>
			<?php
			}
		}
		//End: Show tabs with sent and unsent newsletters if we edit this campaign
		?>
	</div>
</fieldset>
