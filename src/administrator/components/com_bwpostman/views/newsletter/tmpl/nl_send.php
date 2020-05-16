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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

$model		= $this->getModel();
?>

	<div id="sendResult" class="j3 mb-2">
		<div class="row-fluid">
			<div class="span12 text-center mb-2">
				<h1><?php echo Text::_('COM_BWPOSTMAN_NL_SENDMAIL'); ?></h1>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6 inner">
				<div class="well">
					<h2><?php echo Text::_('JSTATUS'); ?></h2>
					<div class="progress progress-success progress-striped active" style="height: 2rem;">
						<div id="nl_bar" class="bar" style="width: 0%; line-height: 2rem; font-size: 1rem;">0%
						</div>
					</div>
					<div id="nl_to_send_message" class="mb-4 lead">&nbsp;</div>
					<div id="sending" class="alert alert-success">
						<strong><?php echo Text::_('COM_BWPOSTMAN_NL_SENDING_PROCESS'); ?></strong>
					</div>
					<div id="delay_msg" class="alert alert-secondary">
						<strong><?php echo $this->delay_message; ?></strong>
					</div>
					<div id="complete" class="alert alert-secondary">
						<i class="icon-ok icon-lg"></i>&nbsp;&nbsp;
						<strong><?php echo Text::_('COM_BWPOSTMAN_NL_QUEUE_COMPLETED'); ?></strong>
					</div>
					<div id="published" class="alert alert-secondary">
						<i class="icon-ok icon-lg"></i>&nbsp;&nbsp;
						<strong><?php echo Text::_('COM_BWPOSTMAN_NLS_N_ITEMS_PUBLISHED_1'); ?></strong>
					</div>
					<div id="nopublished" class="alert alert-secondary">
						<i class="icon-remove icon-lg"></i>&nbsp;&nbsp;
						<strong><?php echo Text::_('COM_BWPOSTMAN_NLS_N_ITEMS_PUBLISHED_0'); ?></strong>
					</div>
					<div id="error" class="alert alert-secondary">
						<i class="icon-remove icon-lg"></i>&nbsp;&nbsp;
						<strong><?php echo Text::_('JERROR_AN_ERROR_HAS_OCCURRED'); ?></strong>
					</div>
				</div>
			</div>
			<div class="span6">
				<div class="well">
					<h2><?php echo Text::_('COM_BWPOSTMAN_NL_SENDING_DETAILS'); ?></h2>
					<div id="loading2" class="text-center"></div>
					<div id="result" class="mt-2"></div>
				</div>
			</div>
		</div>
	</div>

	<p class="bwpm_copyright"><?php echo BwPostmanAdmin::footer(); ?></p>

	<input type="hidden" id="startUrl" value="index.php?option=com_bwpostman&task=newsletter.startsending&format=json&<?php echo Session::getFormToken(); ?>=1" />
	<input type="hidden" id="delay" value="<?php echo $this->delay; ?>" />

<?php
Factory::getDocument()->addScript(Uri::root(true) . '/administrator/components/com_bwpostman/assets/js/bwpm_j3_nl_send.js');


