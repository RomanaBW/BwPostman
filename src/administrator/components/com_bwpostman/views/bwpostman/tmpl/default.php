<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman main default template for backend.
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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;


// Load the modal behavior for the campaign preview
HtmlHelper::_('behavior.modal', 'a.popup');

//Load tabs behavior for the statistics
jimport('joomla.html.html.sliders');

$app = Factory::getApplication();

$jinput	= $app->input;

if ($this->queueEntries) {
	$app->enqueueMessage(Text::_('COM_BWPOSTMAN_ENTRIES_IN_QUEUE'), 'warning');
}

$app->enqueueMessage(Text::_('COM_BWPOSTMAN_REVIEW_MESSAGE'), 'message');
?>

<div id="view_bwpostman">
	<div class="top-spacer">
		<?php if (property_exists($this, 'sidebar')) : ?>
			<div id="j-sidebar-container" class="span2">
				<?php echo $this->sidebar; ?>
			</div>
			<div id="j-main-container" class="span10">
		<?php else :  ?>
			<div id="j-main-container">
		<?php endif; ?>

			<table width="100%">
				<tr>
					<td valign="top">
					<table class="adminlist">
						<tr>
							<td>
								<div id="cpanel" class="cpanel_j3">
								<?php
								$option = $jinput->getCmd('option', 'com_bwpostman');

								if ($this->permissions['view']['newsletter'])
								{
									$link = 'index.php?option=' . $option . '&view=newsletters';
									BwPostmanHTMLHelper::quickiconButton($link, 'icon-48-newsletters.png', Text::_("COM_BWPOSTMAN_NLS"), 0, 0);

									if ($this->permissions['newsletter']['create'])
									{
										$link = 'index.php?option=' . $option . '&view=newsletter&task=add&layout=edit_basic';
										BwPostmanHTMLHelper::quickiconButton(
											$link,
											'icon-48-newsletteradd.png',
											Text::_("COM_BWPOSTMAN_NL_ADD"),
											0,
											0
										);
									}
								}

								if ($this->permissions['view']['subscriber'])
								{
									$link = 'index.php?option=' . $option . '&view=subscribers';
									BwPostmanHTMLHelper::quickiconButton($link, 'icon-48-subscribers.png', Text::_("COM_BWPOSTMAN_SUB"), 0, 0);

									if ($this->permissions['subscriber']['create'])
									{
										$link = 'index.php?option=' . $option . '&view=subscriber&task=subscriber.add&layout=edit';
										BwPostmanHTMLHelper::quickiconButton(
											$link,
											'icon-48-subscriberadd.png',
											Text::_("COM_BWPOSTMAN_SUB_ADD"),
											0,
											0
										);

										$link = 'index.php?option=' . $option . '&view=subscriber&task=subscriber.add_test&layout=edit';
										BwPostmanHTMLHelper::quickiconButton(
											$link,
											'icon-48-testrecipientadd.png',
											Text::_("COM_BWPOSTMAN_TEST_ADD"),
											0,
											0
										);
									}
								}

								if ($this->permissions['view']['campaign'])
								{
									$link = 'index.php?option=' . $option . '&view=campaigns';
									BwPostmanHTMLHelper::quickiconButton($link, 'icon-48-campaigns.png', Text::_("COM_BWPOSTMAN_CAMS"), 0, 0);

									if ($this->permissions['campaign']['create'])
									{
										$link = 'index.php?option=' . $option . '&view=campaign&=add';
										BwPostmanHTMLHelper::quickiconButton(
											$link,
											'icon-48-campaignadd.png',
											Text::_("COM_BWPOSTMAN_CAM_ADD"),
											0,
											0
										);
									}
								}

								if ($this->permissions['view']['mailinglist'])
								{
									$link = 'index.php?option=' . $option . '&view=mailinglists';
									BwPostmanHTMLHelper::quickiconButton($link, 'icon-48-mailinglists.png', Text::_("COM_BWPOSTMAN_MLS"), 0, 0);

									if ($this->permissions['mailinglist']['create'])
									{
										$link = 'index.php?option=' . $option . '&view=mailinglist&task=add';
										BwPostmanHTMLHelper::quickiconButton(
											$link,
											'icon-48-mailinglistadd.png',
											Text::_("COM_BWPOSTMAN_ML_ADD"),
											0,
											0
										);
									}
								}

								if ($this->permissions['view']['template'])
								{
									$link = 'index.php?option=' . $option . '&view=templates';
									BwPostmanHTMLHelper::quickiconButton($link, 'icon-48-templates.png', Text::_("COM_BWPOSTMAN_TPLS"), 0, 0);

									if ($this->permissions['template']['create'])
									{
										$link = 'index.php?option=' . $option . '&view=template&task=addhtml';
										BwPostmanHTMLHelper::quickiconButton(
											$link,
											'icon-48-templateadd.png',
											Text::_("COM_BWPOSTMAN_TPL_ADDHTML"),
											0,
											0
										);

										$link = 'index.php?option=' . $option . '&view=template&task=addtext';
										BwPostmanHTMLHelper::quickiconButton(
											$link,
											'icon-48-text_templateadd.png',
											Text::_("COM_BWPOSTMAN_TPL_ADDTEXT"),
											0,
											0
										);
									}
								}

								if ($this->permissions['view']['archive'])
								{
									$link = 'index.php?option=' . $option . '&view=archive&layout=newsletters';
									BwPostmanHTMLHelper::quickiconButton($link, 'icon-48-archive.png', Text::_("COM_BWPOSTMAN_ARC"), 0, 0);
								}

								if ($this->permissions['com']['admin'])
								{
									$link	= 'index.php?option=com_config&amp;view=component&amp;component=' . $option . '&amp;path=';
									BwPostmanHTMLHelper::quickiconButton($link, 'icon-48-config.png', Text::_("COM_BWPOSTMAN_SETTINGS"), '', '');
								}

								if ($this->permissions['view']['maintenance'])
								{
									$link = 'index.php?option=' . $option . '&view=maintenance';
									BwPostmanHTMLHelper::quickiconButton(
										$link,
										'icon-48-maintenance.png',
										Text::_("COM_BWPOSTMAN_MAINTENANCE"),
										0,
										0
									);
								}

								$link = BwPostmanHTMLHelper::getForumLink();
								BwPostmanHTMLHelper::quickiconButton($link, 'icon-48-forum.png', Text::_("COM_BWPOSTMAN_FORUM"), 0, 0, 'new');

								?></div>
							</td>
						</tr>
					</table>
					</td>
					<td class="well well-small" valign="top" width="250">
						<?php	echo HtmlHelper::_('sliders.start', 'bwpostman_statistic-pane');
								echo HtmlHelper::_('sliders.panel', Text::_('COM_BWPOSTMAN_GENERAL_STATS'), 'general');
						?>
					<table class="adminlist">
						<?php
						if ($this->permissions['com']['admin']
							|| $this->permissions['view']['maintenance']
							|| $this->permissions['view']['newsletter']
						)
						{ ?>
							<tr>
								<td width="200"><?php echo Text::_('COM_BWPOSTMAN_NL_UNSENT_NUM') . ': '; ?></td>
								<td width="50">
									<b>
										<a href="<?php Route::_('index.php?option=com_bwpostman&view=newsletters&layout=sent'); ?>">
											<?php echo $this->general['nl_unsent']; ?>
										</a>
									</b>
								</td>
							</tr>
							<tr>
								<td><?php echo Text::_('COM_BWPOSTMAN_NL_SENT_NUM') . ': '; ?></td>
								<td>
									<b>
										<a href="<?php Route::_('index.php?option=com_bwpostman&view=newsletters&layout=sent'); ?>">
											<?php echo $this->general['nl_sent']; ?>
										</a>
									</b>
								</td>
							</tr>
						<?php
						}

						if ($this->permissions['com']['admin']
							|| $this->permissions['view']['maintenance']
							|| $this->permissions['view']['subscriber']
						)
						{ ?>
							<tr>
								<td><?php echo Text::_('COM_BWPOSTMAN_SUB_NUM') . ': '; ?></td>
								<td>
									<b>
										<a href="<?php Route::_('index.php?option=com_bwpostman&view=subscribers'); ?>">
											<?php echo $this->general['sub']; ?>
										</a>
									</b>
								</td>
							</tr>
							<tr>
								<td><?php echo Text::_('COM_BWPOSTMAN_TEST_NUM') . ': '; ?></td>
								<td>
									<b>
										<a href="<?php Route::_('index.php?option=com_bwpostman&view=subscribers'); ?>">
											<?php echo $this->general['test']; ?>
										</a>
									</b>
								</td>
							</tr>
						<?php
						}

						if ($this->permissions['com']['admin']
							|| $this->permissions['view']['maintenance']
							|| $this->permissions['view']['campaign']
						)
						{ ?>
							<tr>
								<td><?php echo Text::_('COM_BWPOSTMAN_CAM_NUM') . ': '; ?></td>
								<td>
									<b>
										<a href="<?php Route::_('index.php?option=com_bwpostman&view=campaigns'); ?>">
											<?php echo $this->general['cam']; ?>
										</a>
									</b>
								</td>
							</tr>
						<?php
						}

						if ($this->permissions['com']['admin']
							|| $this->permissions['view']['maintenance']
							|| $this->permissions['view']['mailinglist']
						)
						{ ?>
							<tr>
								<td><?php echo Text::_('COM_BWPOSTMAN_ML_PUBLIC_NUM') . ': '; ?></td>
								<td>
									<b>
										<a href="<?php Route::_('index.php?option=com_bwpostman&view=mailinglists'); ?>">
											<?php echo $this->general['ml_published']; ?>
										</a>
									</b>
								</td>
							</tr>
							<tr>
								<td><?php echo Text::_('COM_BWPOSTMAN_ML_INTERNAL_NUM') . ': '; ?></td>
								<td>
									<b>
										<a href="<?php Route::_('index.php?option=com_bwpostman&view=mailinglists'); ?>">
											<?php echo $this->general['ml_unpublished']; ?>
										</a>
									</b>
								</td>
							</tr>
						<?php }

						if ($this->permissions['com']['admin']
							|| $this->permissions['view']['maintenance']
							|| $this->permissions['view']['template']
						)
						{ ?>
							<tr>
								<td><?php echo Text::_('COM_BWPOSTMAN_TPL_HTML_NUM') . ': '; ?></td>
								<td>
									<b>
										<a href="<?php Route::_('index.php?option=com_bwpostman&view=templates'); ?>">
											<?php echo $this->general['html_templates']; ?>
										</a>
									</b>
								</td>
							</tr>
							<tr>
								<td><?php echo Text::_('COM_BWPOSTMAN_TPL_TEXT_NUM') . ': '; ?></td>
								<td>
									<b>
										<a href="<?php Route::_('index.php?option=com_bwpostman&view=templates'); ?>">
											<?php echo $this->general['text_templates']; ?>
										</a>
									</b>
								</td>
							</tr>
						<?php } ?>
					</table>
					<?php

					if ($this->permissions['com']['admin'] || $this->permissions['view']['maintenance'] || $this->permissions['view']['archive'])
					{
						echo HtmlHelper::_('sliders.panel', Text::_('COM_BWPOSTMAN_ARC_STATS'), 'general');
						?>
						<table class="adminlist">
							<?php
							if ($this->permissions['com']['admin']
								|| $this->permissions['view']['maintenance']
								|| ($this->permissions['view']['archive'] && $this->permissions['view']['newsletter'])
							)
							{ ?>
								<tr>
									<td width="200"><?php echo Text::_('COM_BWPOSTMAN_ARC_NL_NUM') . ': '; ?></td>
									<td width="50">
										<b>
											<a href="<?php Route::_('index.php?option=com_bwpostman&view=archive&layout=newsletters'); ?>">
												<?php echo $this->archive['arc_nl']; ?>
											</a>
										</b>
									</td>
								</tr>
								<?php
							}

							if ($this->permissions['com']['admin']
								|| $this->permissions['view']['maintenance']
								|| ($this->permissions['view']['archive'] && $this->permissions['view']['subscriber'])
							)
							{ ?>
								<tr>
									<td><?php echo Text::_('COM_BWPOSTMAN_ARC_SUB_NUM') . ': '; ?></td>
									<td>
										<b>
											<a href="<?php Route::_('index.php?option=com_bwpostman&view=archive&layout=subscribers'); ?>">
												<?php echo $this->archive['arc_sub']; ?>
											</a>
										</b>
									</td>
								</tr>
								<?php
							}

							if ($this->permissions['com']['admin']
								|| $this->permissions['view']['maintenance']
								|| ($this->permissions['view']['archive'] && $this->permissions['view']['campaign'])
							)
							{ ?>
								<tr>
									<td><?php echo Text::_('COM_BWPOSTMAN_ARC_CAM_NUM') . ': '; ?></td>
									<td>
										<b>
											<a href="<?php Route::_('index.php?option=com_bwpostman&view=archive&layout=campaigns'); ?>">
												<?php echo $this->archive['arc_cam']; ?>
											</a>
										</b>
									</td>
								</tr>
								<?php
							}

							if ($this->permissions['com']['admin']
								|| $this->permissions['view']['maintenance']
								|| ($this->permissions['view']['archive'] && $this->permissions['view']['mailinglist'])
							)
							{ ?>
								<tr>
									<td><?php echo Text::_('COM_BWPOSTMAN_ARC_ML_NUM') . ': '; ?></td>
									<td>
										<b>
											<a href="<?php Route::_('index.php?option=com_bwpostman&view=archive&layout=mailinglists'); ?>">
												<?php echo $this->archive['arc_ml']; ?>
											</a>
										</b>
									</td>
								</tr>
							<?php }

							if ($this->permissions['com']['admin']
								|| $this->permissions['view']['maintenance']
								|| ($this->permissions['view']['archive'] && $this->permissions['view']['template'])
							)
							{ ?>
								<tr>
									<td><?php echo Text::_('COM_BWPOSTMAN_ARC_TPL_HTML_NUM') . ': '; ?></td>
									<td>
										<b>
											<a href="<?php Route::_('index.php?option=com_bwpostman&view=archive&layout=templates'); ?>">
												<?php echo $this->archive['arc_html_templates']; ?>
											</a>
										</b>
									</td>
								</tr>
							<?php }

							if ($this->permissions['com']['admin']
								|| $this->permissions['view']['maintenance']
								|| ($this->permissions['view']['archive'] && $this->permissions['view']['template'])
							)
							{ ?>
								<tr>
									<td><?php echo Text::_('COM_BWPOSTMAN_ARC_TPL_TEXT_NUM') . ': '; ?></td>
									<td>

										<b>
											<a href="<?php Route::_('index.php?option=com_bwpostman&view=archive&layout=templates'); ?>">
												<?php echo $this->archive['arc_text_templates']; ?>
											</a>
										</b>
									</td>
								</tr>
							<?php } ?>
						</table>
						<?php
					}

					echo HtmlHelper::_('sliders.end');
					?></td>
				</tr>
			</table>
		</div>
	</div>
	<div class="clr clearfix"></div>
	<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>
</div>

