<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman register error get edit link template for frontend.
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

use BoldtWebservice\Component\BwPostman\Site\Classes\BwPostmanSite;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

// needed to validate email
HtmlHelper::_('behavior.formvalidator');

// Get provided style file
$app = Factory::getApplication();
$wa  = $app->getDocument()->getWebAssetManager();

$wa->useStyle('com_bwpostman.bwpostman');

// Get user defined style file
$templateName = $app->getTemplate();
$css_filename = 'templates/' . $templateName . '/css/com_bwpostman.css';

if (file_exists(JPATH_BASE . '/' . $css_filename))
{
	$wa->registerAndUseStyle('customCss', Uri::root() . $css_filename);
}


// Displays a button to send the editlink
// Will be shown if
// 1. the editlink in the uri is empty
// 2. the editlink in the uri doesn't exist in the subscribers-table
?>

<div id="bwpostman">
	<div id="bwp_com_error_geteditlink">
		<?php
		if (($this->params->get('show_page_heading', '0') != 0) && ($this->params->get('page_heading', '') != ''))
		{ ?>
			<h1 class="componentheading<?php echo $this->params->get('pageclass_sfx', ''); ?>">
				<?php echo $this->escape($this->params->get('page_heading', '')); ?>
			</h1>
		<?php
		}

		echo '<p class="bwp-error">' . Text::_('COM_BWPOSTMAN_ERROR') . '</p>';
		echo '<p class="error-message">' . Text::_($this->error->err_msg) . '</p>';
		?>

		<form action="<?php echo Route::_('index.php?option=com_bwpostman'); ?>" method="post" id="bwp_com_form"
				name="bwp_com_form" class="form-validate">
			<div class="contentpane<?php echo $this->params->get('pageclass_sfx', ''); ?>">
				<p class="getlink">
					<label id="emailmsg" for="email"> <?php echo Text::_('COM_BWPOSTMAN_EMAIL'); ?>:</label>
					<input type="text" id="email" name="email" size="40" value="" class="required validate-email" maxlength="100" />
				</p>
			</div>

			<button class="button validate btn" type="submit"><?php echo Text::_('COM_BWPOSTMAN_BUTTON_SENDEDITLINK'); ?></button>
			<input type="hidden" name="option" value="com_bwpostman" />
			<input type="hidden" name="view" value="edit" />
			<input type="hidden" name="task" value="sendEditlink" />
			<input type="hidden" name="id" value="<?php echo (property_exists($this->error, 'err_code')) ? $this->error->err_code : ''; ?>" />
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
