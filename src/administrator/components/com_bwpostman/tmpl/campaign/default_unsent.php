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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$text	= Text::_('COM_BWPOSTMAN_CAM_UNSENT_NLS');
if (property_exists($this->item, 'automailing_values'))
{
	if ($this->item->automailing_values !== null) {
		$text	= Text::_('COM_BWPOSTMAN_CAM_ASSIGNED_NL');
	}
}

$title_html = Text::_('COM_BWPOSTMAN_NL_SHOW_HTML');
$title_text = Text::_('COM_BWPOSTMAN_NL_SHOW_TEXT');
?>

	<div class="h3"><?php echo $text; ?></div>
	<div class="row">
		<div class="col-12">
			<?php
			//Show tabs with sent and unsent newsletters if we edit a campaign
			if (!empty($this->item->id))
			{
				//Show no tabs if there is no newsletter assigned
				if (empty($this->newsletters->unsent))
				{
					echo Text::_('COM_BWPOSTMAN_CAM_NO_ASSIGNED_NL');
					//Show tabs
				}
				else
				{ ?>
					<table class="table">
						<thead>
							<tr>
								<th class="w-1">
									<?php echo Text::_('NUM'); ?>
								</th>
								<th>
									<?php echo Text::_('SUBJECT'); ?>
								</th>
								<th class="w-20 d-none d-lg-table-cell text-center">
									<?php echo Text::_('COM_BWPOSTMAN_NL_LAST_MODIFICATION_DATE'); ?>
								</th>
								<th class="w-10 text-center">
									<?php echo Text::_('AUTHOR'); ?>
								</th>
							</tr>
						</thead>
						<tbody>
						<?php
						$k = 0;

						$newsletters_unsent = $this->newsletters->unsent;
						for ($i = 0, $n = count($newsletters_unsent); $i < $n; $i++)
						{
							$item = &$newsletters_unsent[$i];

							$link_html = Route::_('index.php?option=com_bwpostman&view=newsletter&format=raw&layout=newsletter_html_modal&task=insideModal&nl_id=' . $item->id);
							$link_text = Route::_('index.php?option=com_bwpostman&view=newsletter&format=raw&layout=newsletter_text_modal&task=insideModal&nl_id=' . $item->id);

							$frameHtml = "htmlFrameUnsent" . $item->id;
							$frameText = "textFrameUnsent" . $item->id;
							?>
							<tr class="<?php echo "item$k"; ?>">
								<td><?php echo $i + 1; ?></td>
								<td><?php echo $item->subject; ?>&nbsp;&nbsp;
									<div class="bw-btn">
										<div class="d-inline-block" aria-describedby="tip-html-<?php echo $i; ?>">
											<a class="iframe btn btn-info btn-sm text-decoration-none mt-1" href="javascript:void(0);"
													data-title="<?php echo $title_html;?>" data-bs-title="<?php echo $title_html;?>" data-bs-frame="myIframeHtml" data-bs-src="<?php echo $link_html;?>" data-bs-toggle="modal" data-bs-target="#bwp-modal">
												<?php echo Text::_('COM_BWPOSTMAN_HTML_NL');?>
											</a>
										</div>
										<div role="tooltip" id="tip-html-<?php echo $i; ?>">
											<?php echo Text::_('COM_BWPOSTMAN_NL_SHOW_HTML');?><br /><?php echo $this->escape($item->subject); ?>
										</div>
										<div class="d-inline-block" aria-describedby="tip-text-<?php echo $i; ?>">
											<a class="iframe btn btn-info btn-sm text-decoration-none mt-1" href="javascript:void(0);"
													data-title="<?php echo $title_text;?>" data-bs-title="<?php echo $title_text;?>" data-bs-frame="myIframeText" data-bs-src="<?php echo $link_text;?>" data-bs-toggle="modal" data-bs-target="#bwp-modal">
												<?php echo Text::_('COM_BWPOSTMAN_TEXT_NL');?>
											</a>
										</div>
										<div role="tooltip" id="tip-text-<?php echo $i; ?>">
											<?php echo Text::_('COM_BWPOSTMAN_NL_SHOW_TEXT');?><br /><?php echo $this->escape($item->subject); ?>
										</div>
									</div>
								</td>
								<td class="d-none d-lg-table-cell text-center"><?php echo HTMLHelper::date($item->modified_time, Text::_('BW_DATE_FORMAT_LC5')); ?></td>
								<td class="text-center"><?php echo $item->author; ?></td>
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
	</div>
