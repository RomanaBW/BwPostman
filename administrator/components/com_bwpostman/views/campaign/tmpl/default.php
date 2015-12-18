<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single campaigns form template for backend.
 *
 * @version 1.3.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2016 Boldt Webservice <forum@boldt-webservice.de>
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

// Load the tooltip behavior for the notes
JHTML::_('behavior.tooltip');
JHTML::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');

//Load tabs behavior for the Tabs
//jimport('joomla.html.html.tabs');
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/bwtabs.php');

// Load the modal behavior for the newsletter preview
JHTML::_('behavior.modal', 'a.popup');

$tab_options = array(
    'onActive' => 'function(title, description){
        description.setStyle("display", "block");
        title.addClass("open").removeClass("closed");
    }',
    'onBackground' => 'function(title, description){
        description.setStyle("display", "none");
        title.addClass("closed").removeClass("open");
    }',
    'useCookie' => 'true', // note the quotes around true, since it must be a string. But if you put false there, you must not use qoutes otherwise JHtmlBwTabs will handle it as true
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
	Joomla.submitbutton = function (pressbutton) {

		var form = document.adminForm;

		<?php if (property_exists($this, 'autocam_values')) { ?>
			if (pressbutton == 'delete') {
				submitform('campaign.apply');
				return;
			}
		<?php } ?>

		<?php if (property_exists($this->item, 'tc_mailing_data')) { ?>
			return checkReasonableTimes(pressbutton);
		<?php } ?>

		if (pressbutton == 'campaign.cancel') {
			submitform(pressbutton);
			return;
		}

		// Valdiate input fields
		if (form.jform_title.value == ""){
			alert("<?php echo JText::_('COM_BWPOSTMAN_CAM_ERROR_TITLE', true); ?>");
		} else {
			submitform(pressbutton);
		}
	}
/* ]]> */
</script>

<div id="bwp_editform">
	<?php
		if ($this->queueEntries) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_ENTRIES_IN_QUEUE'), 'warning');
 		}
	?>
	<form action="<?php echo JRoute::_('index.php?option=com_bwpostman&layout=default&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm">
		<?php
			echo JHtmlBwTabs::start('bwp-cam-nl-pane', $tab_options);

			// Start Tab basic
			echo JHtmlBwTabs::panel(JText::_(empty($this->item->id) ? JText::_('COM_BWPOSTMAN_NEW_CAM') : JText::sprintf('COM_BWPOSTMAN_EDIT_CAM', $this->item->id)), 'basic', '');
			echo $this->loadTemplate('basic');

			// Start Tab assigned/unsent newsletters
			$text	= JText::_('COM_BWPOSTMAN_CAM_UNSENT_NLS');
			if (property_exists($this->item, 'automailing_values')) {
				if ($this->item->automailing_values !== null) {
					$text	= JText::_('COM_BWPOSTMAN_CAM_ASSIGNED_NL');
				}
			}
			echo JHtmlBwTabs::panel($text, 'unsent', '');
			echo $this->loadTemplate('unsent');

			if (property_exists($this->item, 'tc_mailing_data')) {
				// Start Tab autovalues
				echo JHtmlBwTabs::panel(JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_AUTOVALUES_TITLE'), 'autovalues', '');
				echo $this->item->tc_mailing_data;
			}

			if (property_exists($this->item, 'queued_letters')) {
				// Start Tab automailing queue
				echo JHtmlBwTabs::panel(JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_AUTOQUEUE_TITLE'), 'autoqueue', '');
				echo $this->item->queued_letters;
			}

			// Start Tab sent newsletters
			echo JHtmlBwTabs::panel(JText::_('COM_BWPOSTMAN_NL_SENT'), 'sent', '');
			echo $this->loadTemplate('sent');

			// Start Tab permissions
			echo JHtmlBwTabs::panel(JText::_('COM_BWPOSTMAN_CAM_FIELDSET_RULES'), 'rules', '');
			echo $this->loadTemplate('rules');

			echo JHtmlBwTabs::end();
		?>

		<input type="hidden" name="task" value="" />

		<?php echo $this->form->getInput('id'); ?>
		<?php echo $this->form->getInput('asset_id'); ?>
		<?php echo $this->form->getInput('archive_flag'); ?>
		<?php echo $this->form->getInput('archive_time'); ?>
		<?php echo JHTML::_('form.token'); ?>
	</form>

	<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>
</div>
