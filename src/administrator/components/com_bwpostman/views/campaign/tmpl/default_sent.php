<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single campaigns form template for backend.
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
use Joomla\CMS\HTML\HTMLHelper;

?>

<fieldset class="adminform">
	<legend><?php echo Text::_('COM_BWPOSTMAN_NL_SENT'); ?></legend>
	<div class="well well-small">
		<?php
		if (!empty($this->item->id)) {
			if (empty($this->newsletters->sent)) {
				echo Text::_('COM_BWPOSTMAN_CAM_NO_SENT_NL');
			}
			else {
				$firstset	= $this->newsletters->sent[0];
				?>
				<table class="adminlist" width="100%">
					<thead>
						<tr>
							<th>
							<?php echo Text::_('NUM'); ?>
							</th>
							<th align="left"><?php echo Text::_('SUBJECT'); ?></th>
							<th width="150"><?php echo Text::_('COM_BWPOSTMAN_NL_MAILING_DATE'); ?></th>
							<th width="150"><?php echo Text::_('AUTHOR'); ?></th>
							<th width="150"><?php echo Text::_('PUBLISHED'); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php
					$k = 0;

					$newsletters_sent = $this->newsletters->sent;
					for ($i = 0, $n = count($newsletters_sent); $i < $n; $i++)
					{
						$item		= &$newsletters_sent[$i];
						$link_html	= 'index.php?option=com_bwpostman&amp;view=newsletter&amp;format=raw&amp;layout=newsletter_html_modal&amp;task=insideModal&amp;nl_id='. $item->id;
						$link_text	= 'index.php?option=com_bwpostman&amp;view=newsletter&amp;format=raw&amp;layout=newsletter_text_modal&amp;task=insideModal&amp;nl_id='. $item->id;
						?>
						<tr class="<?php echo "item$k"; ?>">
							<td align="center">
							<?php
							if (isset($automation) && $automation)
							{
								echo $item->mail_number;
							}
							else
							{
								echo $item->id;
							} ?>
							</td>
							<td><?php echo $item->subject; ?>&nbsp;&nbsp;
								<span class="cam_preview">
									<span class="editlinktip hasTip"
										title="<?php echo Text::_('COM_BWPOSTMAN_NL_SHOW_HTML');?>::<?php echo $this->escape($item->subject); ?>">
										<?php
										echo '<a class="popup" href="' . $link_html . '" 
										rel="{handler: \'iframe\', size: {x: 600, y: 450}, iframeOptions: {id: \'htmlFrame\'}}">'
											. Text::_('COM_BWPOSTMAN_HTML_NL') . '</a>'; ?>&nbsp;
										</span>
									<span class="editlinktip hasTip"
										title="<?php echo Text::_('COM_BWPOSTMAN_NL_SHOW_TEXT');?>::<?php echo $this->escape($item->subject); ?>">
										<?php
										echo '<a class="popup" href="' . $link_text . '"
										 rel="{handler: \'iframe\', size: {x: 600, y: 450}, iframeOptions: {id: \'textFrame\'}}">'
											. Text::_('COM_BWPOSTMAN_TEXT_NL') . '</a>'; ?>
									</span>
								</span>
							</td>
							<td align="center"><?php echo \Joomla\CMS\HTML\HTMLHelper::date($item->mailing_date, Text::_('BW_DATE_FORMAT_LC5')); ?></td>
							<td align="center"><?php echo $item->author; ?></td>
							<td align="center">
								<?php
								if ($item->published)
								{
									echo Text::_('COM_BWPOSTMAN_YES');
								}
								else
								{
									echo Text::_('COM_BWPOSTMAN_NO');
								}?>
							</td>
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
		?>
	</div>
</fieldset>
