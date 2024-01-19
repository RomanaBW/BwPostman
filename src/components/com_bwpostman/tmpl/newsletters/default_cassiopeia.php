<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletter all cassiopeia template for frontend.
 *
 * @version %%version_number%%
 * @package BwPostman-Site
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

use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanNewsletterHelper;
use BoldtWebservice\Component\BwPostman\Site\Classes\BwPostmanSite;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');

// Get provided style file
$app = Factory::getApplication();
$wa  = $app->getDocument()->getWebAssetManager();

$wa->useStyle('com_bwpostman.cassiopeia');
$wa->useScript('com_bwpostman.bwpm_newsletters_filters');

// Get user defined style file
$templateName = $app->getTemplate();
$css_filename = 'templates/' . $templateName . '/css/com_bwpostman.css';

if (file_exists(JPATH_BASE . '/' . $css_filename))
{
	$wa->registerAndUseStyle('customCss', Uri::root() . $css_filename);
}

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$limitstart	= $this->escape($this->state->get('list.start'));
$moduleId	= $this->escape($this->state->get('module.id', null));

$actionSuffix = '&Itemid=' . $this->Itemid;

if ($moduleId !== null && $moduleId !== '')
{
	$actionSuffix = '&mid=' . $moduleId;
}

$formclass	= ''; // '' = default inputs or 'sm' = smaller Inputs
?>

<noscript>
	<div id="system-message">
		<div class="alert alert-warning">
			<h4 class="alert-heading"><?php echo Text::_('WARNING'); ?></h4>
			<div>
				<p><?php echo Text::_('COM_BWPOSTMAN_JAVASCRIPTWARNING'); ?></p>
			</div>
		</div>
	</div>
</noscript>

<div id="bwpostman" class="mt-3">
	<div id="bwp_com_nl_all">
		<?php if (($this->params->get('show_page_heading', '0') != 0) && ($this->params->get('page_heading', '') != '')) : ?>
			<h1 class="componentheading<?php echo $this->params->get('pageclass_sfx', ''); ?>">
				<?php echo $this->escape($this->params->get('page_heading', '')); ?>
			</h1>
		<?php endif; ?>

		<form action="<?php echo Route::_('index.php?option=com_bwpostman&view=newsletters' . $actionSuffix); ?>" method="post"
				name="adminForm" id="adminForm">
			<div id="bwp_search<?php echo $this->params->get('pageclass_sfx', ''); ?>" class="clearfix">
				<div class="row clearfix">
					<div class="col-lg-4 search_left mb-2">
						<?php if ($this->params->get('filter_field', '1') != "hide") : ?>
							<label for="filter_search" class="element-invisible">
								<?php echo Text::_('JSEARCH_FILTER'); ?>
							</label>
							<div class="btn-wrapper input-group<?php echo $formclass === "sm" ? '  input-group-sm' : ''; ?>">
								<input type="text" name="filter_search" id="filter_search" class="form-control go"
										value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
										title="<?php echo Text::_('COM_BWPOSTMAN_FILTER_SEARCH_DESC'); ?>"
										placeholder="<?php echo Text::_('COM_BWPOSTMAN_SEARCH'); ?> " />
									<button type="submit" class="btn btn-outline-primary hasTooltip"
											title="<?php echo HtmlHelper::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>">
										<i class="fa fa-search"></i>
									</button>
									<button type="button" class="btn btn-outline-primary hasTooltip reset"
											title="<?php echo HtmlHelper::tooltipText('COM_BWPOSTMAN_RESET'); ?>"
											onclick="document.getElementById('filter_search').setAttribute('value','');this.form.submit();">
										<i class="fa fa-times"></i>
									</button>
							</div>
						<?php endif; ?>
					</div>
					<div class="col-lg-8 search_right clearfix">
						<div class="search_right1 d-flex justify-content-end clearfix">
							<div class="mb-2">
								<?php if ($this->params->get('date_filter_enable', '1') != 'hide') : ?>
									<?php echo $formclass === "sm" ? str_replace('form-select', 'form-select form-select-sm', $this->form->monthField) : $this->form->monthField; ?>
									<?php echo $formclass === "sm" ? str_replace('form-select', 'form-select form-select-sm', $this->form->yearField) : $this->form->yearField; ?>
								<?php endif; ?>
								<?php echo $formclass === "sm" ? str_replace('form-select', 'form-select form-select-sm', $this->form->limitField) : $this->form->limitField; ?>
							</div>
						</div>
						<div class="search_right2 d-flex justify-content-end clearfix">
							<?php if ($this->params->get('ml_filter_enable', '1') != 'hide' && is_array($this->mailinglists) && count($this->mailinglists) > 2)
							{ ?>
									<?php echo HtmlHelper::_(
										'select.genericlist',
										$this->mailinglists,
										'filter.mailinglist',
										$formclass === "sm" ? 'class="form-select form-select-sm mb-2 filter-mailinglist"' : 'class="form-select mb-2 filter-mailinglist"',
										'id',
										'title',
										$this->state->get('filter.mailinglist'),
										'filter.mailinglist'
									); ?>
							<?php
							}
							else
							{
	//							$this->state->set('filter.mailinglist', '');
							} ?>
							<?php if ($this->params->get('groups_filter_enable', '') != 'hide' && is_array($this->usergroups) && count($this->usergroups) > 2) : ?>
									<?php echo HtmlHelper::_(
										'select.genericlist',
										$this->usergroups,
										'filter.usergroup',
										$formclass === "sm" ? 'class="form-select form-select-sm mb-2 filter-usergroup"' : 'class="form-select mb-2 filter-usergroup"',
										'id',
										'title',
										$this->state->get('filter.usergroup'),
										'filter.usergroup'
									); ?>
							<?php endif; ?>
							<?php if ($this->params->get('cam_filter_enable', '1') != 'hide' && is_array($this->campaigns) && count($this->campaigns) > 2) : ?>
									<?php echo HtmlHelper::_(
										'select.genericlist',
										$this->campaigns,
										'filter.campaign',
										$formclass === "sm" ? 'class="form-select form-select-sm mb-2 filter-campaign"' : 'class="form-select mb-2 filter-campaign"',
										'id',
										'title',
										$this->state->get('filter.campaign'),
										'filter.campaign'
									); ?>
							<?php endif; ?>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
			<table id="bwp_newsletters_table" class="table table-striped<?php echo $this->params->get('pageclass_sfx', ''); ?>">
				<thead>
					<tr class="table-secondary">
						<th class="date_head">
							<?php echo HtmlHelper::_('grid.sort',  'COM_BWPOSTMAN_DATE', 'a.mailing_date', $listDirn, $listOrder); ?>
						</th>
						<th class="subject_head">
							<?php echo HtmlHelper::_('grid.sort',  'COM_BWPOSTMAN_SUBJECT', 'a.subject', $listDirn, $listOrder); ?>
						</th>
						<th class="clicks_head">
							<?php echo HtmlHelper::_('grid.sort',  'COM_BWPOSTMAN_HITS', 'a.hits', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tbody>
				<?php
				if (count($this->items) > 0)
				{
					foreach ($this->items as $i => $item)
					{
						// Convert attachment string or JSON to array, if present
						if (is_string($item->attachment))
						{
							$attachments = BwPostmanNewsletterHelper::decodeAttachments($item->attachment);
						}

						// Insert first tier to attachments array if only one tier exists
						if (is_array($item->attachment) && !is_array($item->attachment[array_key_first($item->attachment)]))
						{
							$attachments = BwPostmanNewsletterHelper::makeTwoTierAttachment($item->attachment);
						}
						?>
						<tr class="row<?php echo $i % 2; ?>">
							<td class="date">
								<?php $date = HtmlHelper::date($item->mailing_date, Text::_('DATE_FORMAT_LC3'));
								echo $date; ?>
							</td>
							<td class="subject">
								<a href="<?php echo Route::_("index.php?option=com_bwpostman&amp;view=newsletter&amp;id=" . $item->id . $actionSuffix);
								?>">
									<?php echo $item->subject; ?>
								</a>
								<?php
								if (!empty($item->attachment) && $this->params->get('attachment_enable', '1') != 'hide')
								{
									foreach ($attachments as $attachment)
									{ ?>
										&nbsp;&nbsp;<a class="link-attachment" href="<?php echo Uri::base() . $attachment['single_attachment']; ?>" target="_blank">
											<span class="icon_attachment" title="<?php echo Text::_('COM_BWPOSTMAN_ATTACHMENT'); ?>"></span>
										</a>
									<?php
									}
								} ?>
							</td>
							<td class="clicks"><?php echo $item->hits; ?></td>
						</tr>
						<?php
					}
				}
				else
				{ ?>
					<tr class="row0">
						<td colspan="3"><?php echo Text::_('COM_BWPOSTMAN_NO_NEWSLETTERS_FOUND'); ?></td> <?php
				} ?>
				</tbody>
			</table>

			<?php
			if ($this->pagination->pagesTotal > 1)
			{ ?>
				<div class="d-flex justify-content-center">
					<?php echo $this->pagination->getPagesLinks(); ?>
				</div>
				<p class="counter text-center"><?php echo $this->pagination->getPagesCounter(); ?> </p>
			<?php
			} ?>

			<input type="hidden" name="option" value="com_bwpostman" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<input type="hidden" name="limitstart" value="<?php echo $limitstart; ?>" />
			<input type="hidden" name="id" value="<?php //echo $this->items->id; ?>" />
			<?php echo HtmlHelper::_('form.token'); ?>
		</form>

		<?php
		if ($this->params->get('show_boldt_link', '1') === '1')
		{ ?>
			<p class="bwpm_copyright"><?php echo BwPostmanSite::footer(); ?></p>
			<?php
		} ?>
	</div>
</div>
