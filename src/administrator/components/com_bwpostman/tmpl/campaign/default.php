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

// Load the tooltip behavior for the notes
JHtml::_('behavior.tooltip');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

//Load tabs behavior for the Tabs
//jimport('joomla.html.html.tabs');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/bwtabs.php');

// Load the modal behavior for the newsletter preview
JHtml::_('behavior.modal', 'a.popup');

$tab_options = array(
	'onActive' => 'function(title, description){
		description.setStyle("display", "block");
		title.addClass("open").removeClass("closed");
	}',
	'onBackground' => 'function(title, description){
		description.setStyle("display", "none");
		title.addClass("closed").removeClass("open");
	}',
	'useCookie' => 'true',  // note the quotes around true, since it must be a string.
							// But if you put false there, you must not use quotes otherwise JHtmlBwTabs will handle it as true
);

/**
 * BwPostman Single Campaign Layout
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Campaigns
 */

?>

<script type="text/javascript">
/* <![CDATA[ */
	Joomla.submitbutton = function (pressbutton)
	{
		if (pressbutton == 'campaign.cancel')
		{
			Joomla.submitform(pressbutton, document.adminForm);
			return;
		}

		// Validate input fields
		if (document.adminForm.jform_title.value == "")
		{
			alert("<?php echo JText::_('COM_BWPOSTMAN_CAM_ERROR_TITLE', true); ?>");
		}
		else
		{
			Joomla.submitform(pressbutton, document.adminForm);
		}

		<?php if (property_exists($this, 'autocam_values'))
		{ ?>
		if (pressbutton == 'delete')
		{
			Joomla.submitform('campaign.apply');
			return;
		}
		<?php } ?>

		<?php
		if (property_exists($this->item, 'tc_mailing_data'))
		{ ?>
			return checkReasonableTimes(pressbutton);
			<?php
		}
		?>
	};
/* ]]> */
</script>

<div id="bwp_view_single">
	<?php
	if ($this->queueEntries)
	{
		JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_ENTRIES_IN_QUEUE'), 'warning');
	}
	?>
	<form action="<?php echo JRoute::_('index.php?option=com_bwpostman&layout=default&id=' . (int) $this->item->id); ?>"
			method="post" name="adminForm" id="adminForm">
		<div class="row">
			<div class="col-md-12">
				<?php echo JHtml::_('uitab.startTabSet', 'campaign_tabs', array('active' => 'basic'));
				// Start Tab basic
				$title = JText::_('COM_BWPOSTMAN_NEW_CAM');

				if ($this->item->id)
				{
					$title = JText::sprintf('COM_BWPOSTMAN_EDIT_CAM', $this->item->id);
				}

				echo JHtml::_(
					'uitab.addTab',
					'campaign_tabs',
					'basic',
					$title
				);

				echo $this->loadTemplate('basic');
				echo JHtml::_('uitab.endTab');

				// Start Tab assigned/unsent newsletters
				$text	= JText::_('COM_BWPOSTMAN_CAM_UNSENT_NLS');
				echo JHtml::_(
					'uitab.addTab',
					'campaign_tabs',
					'unsent',
					$text
				);

				echo $this->loadTemplate('unsent');
				echo JHtml::_('uitab.endTab');

				// Start Tab sent newsletters
				$text	= JText::_('COM_BWPOSTMAN_NL_SENT');
				echo JHtml::_(
					'uitab.addTab',
					'campaign_tabs',
					'sent',
					$text
				);

				echo $this->loadTemplate('sent');
				echo JHtml::_('uitab.endTab');

				// Start Tab permissions
				if ($this->permissions['com']['admin'] || $this->permissions['admin']['campaign'])
				{
					echo JHtml::_('uitab.addTab', 'campaign_tabs', 'permissions',
						JText::_('COM_BWPOSTMAN_CAM_FIELDSET_RULES'));
					echo $this->loadTemplate('rules');
				}

				echo JHtml::_('uitab.endTab');
				echo JHtml::_('uitab.endTabSet'); ?>

				<input type="hidden" name="task" value="" />

				<?php echo $this->form->getInput('id'); ?>
				<?php echo $this->form->getInput('asset_id'); ?>
				<?php echo $this->form->getInput('checked_out'); ?>
				<?php echo $this->form->getInput('archive_flag'); ?>
				<?php echo $this->form->getInput('archive_time'); ?>
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</div>
	</form>

	<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>
</div>
