<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletter all default template for frontend.
 *
 * @version 1.2.4 bwpm
 * @package BwPostman-Site
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

JHtml::_('jquery.ui', array('searchtools'));
JHtml::_('formbehavior.chosen', 'select');

/**
 * BwPostman Newsletter Overview Layout
 *
 * @package 	BwPostman-Site
 * @subpackage 	Newsletters
 */

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

?>

<div id="bwpostman">
	<div id="bwp_com_nl_all">
		<?php if (($this->params->get('show_page_heading') != 0) && ($this->params->get('page_heading') != '')) : ?>
			<h1 class="componentheading<?php echo $this->params->get('pageclass_sfx'); ?>"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
		<?php endif; ?>
		
		<form action="<?php echo $this->uri; ?>" method="post" name="adminForm" id="adminForm" class="form-inline form-horizontal">
			<div id="bwp_search<?php echo $this->params->get('pageclass_sfx'); ?>" class="js-tools clearfix">
				<div class="clearfix">
					<div class="search_left">
						<?php if ($this->params->get('filter_field') != "hide") : ?>
							<label for="filter_search" class="element-invisible">
								<?php echo JText::_('JSEARCH_FILTER'); ?>
							</label>
							<div class="btn-wrapper input-append">
								<input type="text" name="filter_search" id="filter_search" class="inputbox go" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_BWPOSTMAN_FILTER_SEARCH_DESC'); ?>" placeholder="<?php echo JText::_('COM_BWPOSTMAN_SEARCH'); ?> " />
								<button type="submit" class="append-area hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>">
									<i class="icon-search"></i>
								</button>
								<button type="button" class="append-area hasTooltip js-stools-btn-clear reset" title="<?php echo JHtml::tooltipText('COM_BWPOSTMAN_RESET'); ?>" onclick="document.getElementById('filter_search').setAttribute('value','');this.form.submit();">
									<i class="icon-remove"></i>
								</button>
							</div>
						<?php endif; ?>
					</div>
					<div class="js-stools-container-list search_right">
						<?php if ($this->params->get('date_filter_enable') != 'hide') : ?>
							<div class="js-stools-field-filter filter_month"><?php echo $this->form->monthField; ?></div>
							<div class="js-stools-field-filter filter_year"><?php echo $this->form->yearField; ?></div>
						<?php endif; ?>
						<?php if ($this->params->get('ml_filter_enable') != 'hide' && count($this->mailinglists) > 2) : ?>
							<div class="js-stools-field-filter filter_mls"><?php echo JHTML::_('select.genericlist', $this->mailinglists, 'filter.mailinglist', 'class="inputbox input-medium filter-mailinglist"', 'id', 'title', $this->state->get('filter.mailinglist'), 'filter.mailinglist'); ?></div>
						<?php endif; ?>
						<?php if ($this->params->get('groups_filter_enable') != 'hide' && count($this->usergroups) > 2) : ?>
							<div class="js-stools-field-filter filter_groups"><?php echo JHTML::_('select.genericlist', $this->usergroups, 'filter.usergroup', 'class="inputbox input-medium filter-usergroup"', 'id', 'title', $this->state->get('filter.usergroup'), 'filter.usergroup'); ?></div>
						<?php endif; ?>
						<?php if ($this->params->get('cam_filter_enable') != 'hide' && count($this->campaigns) > 2) : ?>
							<div class="js-stools-field-filter filter_cams"><?php echo JHTML::_('select.genericlist', $this->campaigns, 'filter.campaign', 'class="inputbox input-medium filter-campaign"', 'id', 'title', $this->state->get('filter.campaign'), 'filter.campaign'); ?></div>
						<?php endif; ?>
						<div class="js-stools-field-filter filter_list"><?php echo $this->form->limitField; ?></div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>	
			<table id="bwp_newsletters_table<?php echo $this->params->get('pageclass_sfx'); ?>">
				<thead>
					<tr>
						<th class="date_head"><?php echo JHTML::_('grid.sort',  'COM_BWPOSTMAN_DATE', 'a.mailing_date', $listDirn, $listOrder); ?></th>
						<th class="subject_head"><?php echo JHTML::_('grid.sort',  'COM_BWPOSTMAN_SUBJECT', 'a.subject', $listDirn, $listOrder); ?></th>
						<th class="clicks_head"><?php echo JHTML::_('grid.sort',  'COM_BWPOSTMAN_HITS', 'a.hits', $listDirn, $listOrder); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
					if (count($this->items) > 0) { 
						foreach ($this->items as $i => $item) : ?>
							<tr class="row<?php echo $i % 2; ?>">
								<td class="date"><?php $date = JHTML::Date($item->mailing_date, JText::_('DATE_FORMAT_LC3')); echo $date;?></td>
								<td class="subject">
									<a href="<?php echo JRoute::_("index.php?option=com_bwpostman&amp;view=newsletter&amp;id={$item->id}"); ?>"><?php echo $item->subject; ?></a>
									<?php if (!empty($item->attachment) && $this->params->get('attachment_enable') != 'hide') { ?>
										<a  class="link-attachment" href="<?php echo JUri::base() . '/' . $item->attachment; ?>" target="_blank">
											<span class="icon_attachment" title="<?php echo JText::_('COM_BWPOSTMAN_ATTACHMENT'); ?>"></span>
										</a>
									<?php } ?>
								</td>
								<td class="clicks"><?php echo $item->hits; ?></td>
							</tr>
						<?php
						endforeach; 
					}
					else { ?>
						<tr class="row0">
							<td colspan="3"><?php echo JText::_('COM_BWPOSTMAN_NO_NEWSLETTERS_FOUND'); ?></td>
					<?php } ?>
				</tbody>
			</table>
	
			<?php if ($this->pagination->pagesTotal > 1) { ?>
				<div class="pagination">
					<?php echo $this->pagination->getPagesLinks(); ?>
					<p class="counter"><?php echo $this->pagination->getPagesCounter(); ?> </p>
				</div>
			<?php } ?>
			
			<input type="hidden" name="option" value="com_bwpostman" /> 
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" /> 
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" /> 
			<input type="hidden" name="id" value="<?php //echo $this->items->id; ?>" />
			<?php echo JHTML::_('form.token'); ?>
	
		</form>
		
		<p class="bwpm_copyright"<?php if ($this->params->get('show_boldt_link') != 1) echo ' style="display:none;"'; ?>><?php echo BwPostman::footer(); ?></p>
	</div>
</div>

<script type="text/javascript">
/* <![CDATA[ */
var $j	= jQuery.noConflict();

$j(".filter-mailinglist").on("change", function() {
	$j(".filter-campaign").prop('selectedIndex', 0);
	$j(".filter-usergroup").prop('selectedIndex', 0);
	$j('#adminForm').submit();
	});

$j(".filter-usergroup").on("change", function() {
	$j(".filter-mailinglist").prop('selectedIndex', 0);
	$j(".filter-campaign").prop('selectedIndex', 0);
	$j('#adminForm').submit();
});

$j(".filter-campaign").on("change", function() {
	$j(".filter-mailinglist").prop('selectedIndex', 0);
	$j(".filter-usergroup").prop('selectedIndex', 0);
	$j('#adminForm').submit();
});


/* ]]> */
</script>

<noscript>
	<div id="system-message">
		<div class="alert alert-warning">
			<h4 class="alert-heading"><?php echo JText::_('WARNING'); ?></h4>
			<div>
				<p><?php echo JText::_('COM_BWPOSTMAN_JAVAWARNING'); ?></p>
			</div>
		</div>
	</div>
</noscript>