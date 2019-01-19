<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman all subscribers main template for backend.
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

//jimport ('joomla.html.html.bootstrap');

JHtml::_('bootstrap.tooltip');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.multiselect');

//Load tabs behavior for the Tabs
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/bwtabs.php');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

$app	= JFactory::getApplication();
$tab	= $app->getUserState($this->context . '.tab', 'confirmed');

switch ($tab)
{
	default:
	case 'confirmed':
		$tab_offset	= 0;
		break;
	case 'unconfirmed':
		$tab_offset	= 1;
		break;
	case 'testrecipients':
		$tab_offset	= 2;
		break;
}

$tab_cookie	= false;

$tab_options = array(
	'onActive' => 'function(title, description)
	{
        description.setStyle("display", "block");
        title.addClass("open").removeClass("closed");
    }',
	'onBackground' => 'function(title, description)
	{
        description.setStyle("display", "none");
        title.addClass("closed").removeClass("open");
    }',
	'startOffset' => $tab_offset,  // 0 starts on the first tab, 1 starts the second, etc...
	'useCookie' => $tab_cookie, // note the quotes around true, since it must be a string. But if you put false there, you
								// must not use quotes otherwise JHtmlTabs will handle it as true
);
?>

<script type="text/javascript">
/* <![CDATA[ */
	function OnlyFiltered(onlyFiltered) // Get the selected value from modal box
	{
		if (onlyFiltered === '1') {
			document.getElementById('mlToExport').value = <?php echo $this->filterMl; ?>;
		}

		Joomla.submitbutton('subscribers.exportSubscribers');
	}

	Joomla.submitbutton = function (pressbutton)
	{
		if (pressbutton == 'subscriber.archive')
		{
			ConfirmArchive = confirm("<?php echo JText::_('COM_BWPOSTMAN_SUB_CONFIRM_ARCHIVE', true); ?>");
			if (ConfirmArchive == true)
			{
				submitform(pressbutton);
				return;
			}
		}
		else
		{
			submitform(pressbutton);
		}
	};
	/* ]]> */
</script>

<div id="bwp_view_lists">
	<form action="<?php echo JRoute::_('index.php?option=com_bwpostman&view=subscribers'); ?>"
			method="post" name="adminForm" id="adminForm" class="form-inline">
		<?php if (property_exists($this, 'sidebar')) : ?>
			<div id="j-sidebar-container" class="span2">
				<?php echo $this->sidebar; ?>
			</div>
			<div id="j-main-container" class="span10">
		<?php else :  ?>
			<div id="j-main-container">
		<?php endif; ?>
		<?php
			// Search tools bar
			echo JLayoutHelper::render(
				'default',
				array('view' => $this, 'tab' => $tab),
				$basePath = JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/searchtools'
			);
		?>

			<div class="row-fluid">
				<?php
				// @ToDo: give tables a unique identifier for tests
					echo JHtmlBwTabs::start('bwpostman_subscribers_tabs', $tab_options);
					echo JHtmlBwTabs::panel(
						JText::_('COM_BWPOSTMAN_SUB_CONFIRMED'),
						'confirmed',
						"
							document.adminForm.getElementById('tab').setAttribute('value', 'confirmed');
							Joomla.submitbutton('subscribers.changeTab');
						"
					);
					echo $this->loadTemplate('confirmed');
					echo JHtmlBwTabs::panel(
						JText::_('COM_BWPOSTMAN_SUB_UNCONFIRMED'),
						'unconfirmed',
						"
							document.adminForm.getElementById('tab').setAttribute('value', 'unconfirmed');
							Joomla.submitbutton('subscribers.changeTab');
						"
					);
					echo $this->loadTemplate('unconfirmed');
					echo JHtmlBwTabs::panel(
						JText::_('COM_BWPOSTMAN_TEST'),
						'testrecipients',
						"
							document.adminForm.getElementById('tab').setAttribute('value', 'testrecipients');
							Joomla.submitbutton('subscribers.changeTab');
						"
					);
					echo $this->loadTemplate('testrecipients');
					echo JHtmlBwTabs::end();
				?>
			</div>
			<div class="pagination"><?php echo $this->pagination->getListFooter(); ?></div>
			<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>

			<?php //Load the batch processing form. ?>
			<?php echo $this->loadTemplate('batch'); ?>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" id="tab" name="tab" value="" />
			<input type="hidden" id="mlToExport" name="mlToExport" value="" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
