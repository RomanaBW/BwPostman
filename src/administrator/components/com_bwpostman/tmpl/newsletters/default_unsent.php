<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all newsletters unsent template for backend.
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


JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');

// Load the modal behavior for the newsletter preview
//JHtml::_('behavior.modal', 'a.popup');

//Load tabs behavior for the Tabs
jimport('joomla.html.html.tabs');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

?>

<script type="text/javascript">
/* <![CDATA[ */
	function changeTab(tab)
	{
		if (tab != 'default_unsent')
		{
			document.adminForm.tab.setAttribute('value',tab);
		}
	}

	Joomla.submitbutton = function (pressbutton)
	{
		if (pressbutton == 'newsletters.archive')
		{
			ConfirmArchive = confirm("<?php echo JText::_('COM_BWPOSTMAN_NL_CONFIRM_ARCHIVE', true); ?>");
			if (ConfirmArchive == true)
			{
				Joomla.submitform(pressbutton, document.adminForm);
			}
		}
		else
		{
			Joomla.submitform(pressbutton, document.adminForm);
		}
	};

/* ]]> */
</script>

<?php
//	echo '<script type="text/javascript">' . "\n";
//	echo "window.addEvent('load', function() {\n";
//	// We cannot replace the "&" with an "&amp;" because it's JavaScript and not HTML
//	echo "SqueezeBox.open('index.php?option=com_bwpostman&view=newsletter&layout=queue_modal&format=raw&task=continue_sending', {handler: 'iframe', size: { x: 600, y: 450 }, closable: false, closeBtn: false, iframeOptions: {id: 'sendFrame', name: 'sendFrame'}}); \n";
//	echo "});\n";
//	echo "</script>\n";
?>

<div id="bwp_view_lists">
	<?php
	// Open modalbox if task == startsending --> we will show the sending process in the modalbox
	$jinput	= JFactory::getApplication()->input;
	$task	= $jinput->get->get('task');

	if ($task != 'startsending')
	{
		if ($this->queueEntries)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_ENTRIES_IN_QUEUE'), 'warning');
		}
	}

	// Open modalbox if task == startsending --> we will show the sending process in the modalbox
//	if ($task == "startsending")
//	{
//	}

	?>
	<form action="<?php echo JRoute::_('index.php?option=com_bwpostman&view=newsletters'); ?>"
			method="post" name="adminForm" id="adminForm">
		<div class="row">
			<div class="col-md-12">
				<div id="j-main-container" class="j-main-container">
					<?php
					// Search tools bar
					echo JLayoutHelper::render(
							'tabbed',
							array('view' => $this, 'tab' => 'unsent'),
							$basePath = JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/searchtools'
					);
					?>

					<div class="form-horizontal">
						<ul class="bwp_tabs">
							<li class="open">
								<button onclick="return changeTab('unsent');" class="buttonAsLink_open">
									<?php echo JText::_('COM_BWPOSTMAN_NL_UNSENT'); ?>
								</button>
							</li>
							<li class="closed">
								<button onclick="return changeTab('sent');" class="buttonAsLink">
									<?php echo JText::_('COM_BWPOSTMAN_NL_SENT'); ?>
								</button>
							</li>
							<?php if ($this->count_queue && $this->permissions['newsletter']['send']) { ?>
								<li class="closed">
									<button onclick="return changeTab('queue');" class="buttonAsLink">
										<?php echo JText::_('COM_BWPOSTMAN_NL_QUEUE'); ?>
									</button>
								</li>
							<?php } ?>
						</ul>
					</div>
					<div class="clr clearfix"></div>

					<div class="current">
						<table id="main-table" class="table">
							<caption id="captionTable" class="sr-only">
								<?php echo Text::_('COM_CSP_TABLE_CAPTION'); ?>, <?php echo Text::_('JGLOBAL_SORTED_BY'); ?>
							</caption>
						<thead>
							<tr>
								<th style="width: 1%;" class="text-center">
									<input type="checkbox" name="checkall-toggle" value=""
											title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
								</th>
								<th class="d-none d-md-table-cell" style="width: 7%;" scope="col">
									<?php echo JHtml::_('searchtools.sort',  'COM_BWPOSTMAN_NL_ATTACHMENT', 'a.attachment', $listDirn, $listOrder); ?>
								</th>
								<th class="d-none d-md-table-cell" style="min-width: 100px;" scope="col">
									<?php echo JHtml::_('searchtools.sort',  'COM_BWPOSTMAN_NL_SUBJECT', 'a.subject', $listDirn, $listOrder); ?>
								</th>
								<th class="d-none d-md-table-cell" style="min-width: 100px;" scope="col">
									<?php echo JHtml::_('searchtools.sort',  'COM_BWPOSTMAN_NL_DESCRIPTION', 'a.description', $listDirn, $listOrder); ?>
								</th>
								<th class="d-none d-md-table-cell" style="width: 10%;" scope="col">
									<?php echo JHtml::_(
										'searchtools.sort',
										'COM_BWPOSTMAN_NL_LAST_MODIFICATION_DATE',
										'a.modified_time',
										$listDirn,
										$listOrder
									); ?>
								</th>
								<th class="d-none d-md-table-cell" style="width: 7%;" scope="col">
									<?php echo JHtml::_('searchtools.sort', 'COM_BWPOSTMAN_NL_AUTHOR', 'authors', $listDirn, $listOrder); ?>
								</th>
								<th class="d-none d-md-table-cell" style="width: 10%;" scope="col">
									<?php echo JHtml::_('searchtools.sort',  'COM_BWPOSTMAN_CAM_NAME', 'campaign_id', $listDirn, $listOrder); ?>
								</th>
								<th class="d-none d-md-table-cell" style="width: 10%;" scope="col">
									<?php echo JHtml::_('searchtools.sort',  'COM_BWPOSTMAN_NL_IS_TEMPLATE', 'is_template', $listDirn, $listOrder); ?>
								</th>
								<th class="d-none d-md-table-cell" style="width: 3%;" scope="col">
									<?php echo JHtml::_('searchtools.sort',  'NUM', 'a.id', $listDirn, $listOrder); ?>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php
							if (count($this->items))
							{
								foreach ($this->items as $i => $item) :
									?>
									<tr class="row<?php echo $i % 2; ?>">
										<td align="center"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
										<td>
											<?php if (!empty($item->attachment)) { ?>
												<span class="icon_attachment" title="<?php echo JText::_('COM_BWPOSTMAN_ATTACHMENT'); ?>"></span>
											<?php } ?>
										</td>
										<td>
											<?php
											if ($item->checked_out)
											{
												echo JHtml::_(
													'jgrid.checkedout',
													$i,
													$item->editor,
													$item->checked_out_time,
													'newsletters.',
													BwPostmanHelper::canCheckin('newsletter', $item->checked_out)
												);
											} ?>
											<?php if (BwPostmanHelper::canEdit('newsletter', $item)) : ?>
												<a href="
												<?php
												echo JRoute::_(
													'index.php?option=com_bwpostman&view=newsletter&layout=edit_basic&task=newsletter.edit&id='
													. $item->id . '&referrer=newsletters'
												);?>">
													<?php echo $this->escape($item->subject); ?>
												</a>
											<?php else : ?>
												<?php echo $this->escape($item->subject); ?>
											<?php endif; ?>
										</td>
										<td><?php echo $this->escape($item->description); ?></td>
										<td>
											<?php
											if ($item->modified_time != '0000-00-00 00:00:00')
											{
												echo JHtml::date($item->modified_time, JText::_('BW_DATE_FORMAT_LC5'));
											} ?>
										</td>
										<td><?php echo $item->authors; ?></td>
										<td align="center"><?php echo $item->campaign_id; ?></td>
										<td class="center" align="center">
											<?php
											echo BwPostmanHTMLHelper::switchGridValue(
												$i,
												$item->is_template,
												'changeIsTemplate',
												'newsletter.',
												'COM_BWPOSTMAN_NL_FILTER_IS_TEMPLATE_UNSET_TITLE',
												'COM_BWPOSTMAN_NL_FILTER_IS_TEMPLATE_SET_TITLE',
												true,
												'featured',
												'unfeatured',
												true, // translate
												'cb', //checkbox
												BwPostmanHelper::canEdit('newsletter', (int) $item->id)
											);
											?>
										</td>
										<td align="center"><?php echo $item->id; ?></td>
									</tr><?php
								endforeach;
							}
							else
							{
								// if no data ?>
								<tr class="row1">
									<td colspan="9"><strong><?php echo JText::_('COM_BWPOSTMAN_NO_DATA'); ?></strong></td>
								</tr><?php
							}
							?>
						</tbody>
					</table>
					</div>
				</div>
			</div>
			<div class="pagination"><?php echo $this->pagination->getListFooter(); ?></div>
			<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>

			<input type="hidden" name="task" value="" />
			<input type="hidden" id="tab" name="tab" value="unsent" />
			<input type="hidden" name="layout" value="default" />
			<input type="hidden" name="tpl" value="unsent" />
			<input type="hidden" name="boxchecked" value="0" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
		<?php
		//		$link                  = Route::_('index.php?option=com_bwpostman&view=newsletter&layout=queue_modal&format=raw&task=continue_sending');
		//		$link                  = Route::_('index.php?option=com_bwpostman&view=newsletter&layout=queue_modal&format=raw&task=continue_sending&tmpl=component');
		$selector              = 'sendFrame';
		$params                = array();
		$params['title']       = 'Send newsletters';
		$params['backdrop']    = 'static';
		$params['keyboard']    = false;
		$params['closeButton'] = false;
		$params['animation']   = true;
		$params['footer']      = 'Send newsletter footer';
		//		$params['url']         = $link;
		$params['height']      = '450';
		$params['width']       = '600';

		HTMLHelper::_('bootstrap.renderModal', 'collapseModal', $params,
			$this->loadTemplate('modal'));

		?>
	</form>
</div>

