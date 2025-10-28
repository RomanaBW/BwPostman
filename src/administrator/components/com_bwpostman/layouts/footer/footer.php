<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman admin class for backend.
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
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;

$version = BwPostmanHelper::getInstalledBwPostmanVersion();
?>

<div class="bwpm-footer col-md-12">
	<div class="card card-footer text-center mt-3">
		<p class="bwpm_copyright">
			BwPostman version <?php echo $version; ?> by <a href="https://www.boldt-webservice.de" target="_blank">Boldt Webservice</a>
		</p>
		<p class="bwpm-review">
			<?php echo Text::_('COM_BWPOSTMAN_REVIEW_MESSAGE'); ?>
		</p>
	</div>
</div>
