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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Load the tooltip behavior for the notes
HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');


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

	<?php
	if ($this->queueEntries)
	{
		Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_ENTRIES_IN_QUEUE'), 'warning');
	}
	?>
	<div id="bwp_view_single">
		<form action="<?php echo Route::_('index.php?option=com_bwpostman&layout=default&id=' . (int) $this->item->id); ?>"
				method="post" name="adminForm" id="item-form">
			<div class="row title-alias form-vertical form-no-margin mb-3">
			</div>
			<div>
				<?php echo HTMLHelper::_('uitab.startTabSet', 'campaign_tabs', array('active' => 'basic'));
				// Start Tab basic
				$title = Text::_('COM_BWPOSTMAN_NEW_CAM');

				if ($this->item->id)
				{
					$title = Text::sprintf('COM_BWPOSTMAN_EDIT_CAM', $this->item->id);
				}

				echo HTMLHelper::_(
					'uitab.addTab',
					'campaign_tabs',
					'basic',
					$title
				);

				echo $this->loadTemplate('basic');
				echo HTMLHelper::_('uitab.endTab');

				// Start Tab assigned/unsent newsletters
				$text	= Text::_('COM_BWPOSTMAN_CAM_UNSENT_NLS');
				echo HTMLHelper::_(
					'uitab.addTab',
					'campaign_tabs',
					'unsent',
					$text
				);

				echo $this->loadTemplate('unsent');
				echo HTMLHelper::_('uitab.endTab');

				// Start Tab sent newsletters
				$text	= Text::_('COM_BWPOSTMAN_NL_SENT');
				echo HTMLHelper::_(
					'uitab.addTab',
					'campaign_tabs',
					'sent',
					$text
				);

				echo $this->loadTemplate('sent');
				echo HTMLHelper::_('uitab.endTab');

				// Start Tab permissions
				if ($this->permissions['com']['admin'] || $this->permissions['admin']['campaign'])
				{
					echo HTMLHelper::_('uitab.addTab', 'campaign_tabs', 'permissions',
						Text::_('COM_BWPOSTMAN_CAM_FIELDSET_RULES'));
					echo $this->loadTemplate('rules');
					echo HTMLHelper::_('uitab.endTab');
				}

				echo HTMLHelper::_('uitab.endTabSet'); ?>

				<input type="hidden" name="task" value="" />

				<?php echo $this->form->getInput('id'); ?>
				<?php echo $this->form->getInput('asset_id'); ?>
				<?php echo $this->form->getInput('checked_out'); ?>
				<?php echo $this->form->getInput('archive_flag'); ?>
				<?php echo $this->form->getInput('archive_time'); ?>
				<?php echo HTMLHelper::_('form.token'); ?>

				<input type="hidden" id="alertTitle" value="<?php echo Text::_('COM_BWPOSTMAN_CAM_ERROR_TITLE', true); ?>" />
				<input type="hidden" id="alertRecipients" value="<?php echo Text::_('COM_BWPOSTMAN_CAM_ERROR_NO_RECIPIENTS_SELECTED'); ?>" />
			</div>
		</form>
	</div>

	<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>
