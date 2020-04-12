<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletter sending process view for backend.
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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

$model		= $this->getModel();
$token      = Session::getFormToken();
?>

<div id="sendResult" class="card mb-3">
	<div class="row">
		<div class="col-12 text-center mb-2">
			<div class="card-header">
				<div class="h1"><?php echo Text::_('COM_BWPOSTMAN_NL_SENDMAIL'); ?></div>
			</div>
		</div>
		<div class="col-md-6 inner">
			<div class="card-body">
				<div class="h2"><?php echo Text::_('JSTATUS'); ?></div>
				<div class="progress mb-3" style="height: 2rem; font-size: 1rem;">
					<div id="nl_bar" class="progress-bar progress-bar-striped progress-bar-animated bg-success"
						role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">0%
					</div>
				</div>
				<div id="nl_to_send_message" class="mb-4">&nbsp;</div>
				<div id="sending" class="alert alert-success">
					<i class="fas fa-spinner fa-pulse fa-lg"></i>&nbsp;&nbsp;
					<?php echo Text::_('COM_BWPOSTMAN_NL_SENDING_PROCESS'); ?>
				</div>
				<div id="delay_msg" class="alert alert-secondary">
					<i class="fas fa-spinner fa-pulse fa-lg"></i>&nbsp;&nbsp;
					<?php echo $this->delay_message; ?>
				</div>
				<div id="complete" class="alert alert-secondary">
					<i class="fas fa-check-circle fa-lg"></i>&nbsp;&nbsp;
					<?php echo Text::_('COM_BWPOSTMAN_NL_QUEUE_COMPLETED'); ?>
				</div>
				<div id="published" class="alert alert-secondary">
					<i class="fas fa-check-circle fa-lg"></i>&nbsp;&nbsp;
					<?php echo Text::_('COM_BWPOSTMAN_NLS_N_ITEMS_PUBLISHED_1'); ?>
				</div>
				<div id="nopublished" class="alert alert-secondary">
					<i class="fas fa-exclamation-circle fa-lg"></i>&nbsp;&nbsp;
					<?php echo Text::_('COM_BWPOSTMAN_NLS_N_ITEMS_PUBLISHED_0'); ?>
				</div>
				<div id="error" class="alert alert-secondary">
					<i class="fas fa-exclamation-circle fa-lg"></i>&nbsp;&nbsp;
					<?php echo Text::_('JERROR_AN_ERROR_HAS_OCCURRED'); ?>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="card-body">
				<div class="h2"><?php echo Text::_('COM_BWPOSTMAN_NL_DETAILS'); ?></div>
				<div id="load" class="text-center"><i class="fas fa-spinner fa-pulse fa-3x"></i></div>
				<div id="result" class="mt-2"></div>
			</div>
		</div>
	</div>
</div>

<?php echo LayoutHelper::render('footer', null, JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts/footer'); ?>

	<input type="hidden" id="startUrl" value="index.php?option=com_bwpostman&task=newsletter.startsending&format=json&<?php echo Session::getFormToken(); ?>=1" />
	<input type="hidden" id="delay" value="<?php echo $this->delay; ?>" />

<?php
Factory::getDocument()->addScript(Uri::root(true) . '/administrator/components/com_bwpostman/assets/js/bwpm_nl_send.js');

