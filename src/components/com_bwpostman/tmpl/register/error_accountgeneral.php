<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman register error account general template for frontend.
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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

// Get provided style file
$app = Factory::getApplication();
$wa  = $app->getDocument()->getWebAssetManager();

$wa->useStyle('com_bwpostman.bwpostman');

// Get user defined style file
$templateName = $app->getTemplate();
$css_filename = '/templates/' . $templateName . '/css/com_bwpostman.css';

if (file_exists(JPATH_BASE . $css_filename))
{
	$wa->registerAndUseStyle('customCss', Uri::root(true) . $css_filename);
}


// Displays a link to the registration form or the editlink form
// Will be shown if
// 1. the unsubscribe process failed --> the editlink or email address is wrong
// 2. someone ordered an editlink but the email address doesn't exist in the subscribers-table
// 3. the registration process failed because the account already exists
// 4. the unsubscribe process failed --> the account couldn't be removed --> find a better solution for that
?>

<div id="bwpostman">
	<div id="bwp_com_error_account_general">
		<?php
		if (($this->params->get('show_page_heading', '0') != 0) && ($this->params->get('page_heading', '') != ''))
		{
			?>
			<h1 class="componentheading<?php echo $this->params->get('pageclass_sfx', ''); ?>">
				<?php echo $this->escape($this->params->get('page_heading', '')); ?>
			</h1>
		<?php
		}

		echo '<p class="bwp-error">' . Text::_('COM_BWPOSTMAN_ERROR') . '</p>';
		$msg = '';

		if (is_null($this->error->err_code))
		{
			if ($this->error->err_msg == 'COM_BWPOSTMAN_ERROR_UNSUBSCRIBE')
			{ // Case 4
				echo '<p class="error-message">' . Text::_($this->error->err_msg) . '</p>';

				$admin_email = $this->params->def('default_from_email', Factory::getApplication()->getConfig()->get('mailfrom'));

				$msg1 = '<p class="contact-admin">' . Text::sprintf('COM_BWPOSTMAN_ERROR_CONTACTADMIN', $admin_email) . '</p>';
				echo HtmlHelper::_('content.prepare', $msg1);
			}
			else
			{
				// Case 1
				if (!property_exists($this->error, 'err_itemid'))
				{
					$link = Route::_(Uri::root() . "index.php?option=com_bwpostman&amp;view=edit");
				}
				else
				{
					$link = Route::_(Uri::root() . "index.php?option=com_bwpostman&amp;view=edit&amp;Itemid={$this->error->err_itemid}");
				}

				$msg = '<p class="error-message">' . Text::sprintf($this->error->err_msg, $link) . '</p>';
			}
		}
		else
		{
			if ($this->error->err_code == 0) {
				// Case 2
				if (!property_exists($this->error, 'err_itemid'))
				{
					$link = Route::_(Uri::root() . "index.php?option=com_bwpostman&amp;view=register");
				}
				else
				{
					$link = Route::_(Uri::root() . "index.php?option=com_bwpostman&amp;view=register&amp;Itemid={$this->error->err_itemid}");
				}
			}
			else
			{
				// Case 3
				if (!property_exists($this->error, 'err_itemid'))
				{
					$link = Route::_(Uri::root() . "index.php?option=com_bwpostman&amp;view=edit");
				}
				else
				{
					$link = Route::_(Uri::root() . "index.php?option=com_bwpostman&amp;view=edit&amp;Itemid={$this->error->err_itemid}");
				}
			}

			$msg = '<p class="error-message">' . Text::sprintf($this->error->err_msg, $this->error->err_email, $link) . '</p>';
		}

		echo $msg;

		if ($this->params->get('show_boldt_link', '1') === '1')
		{ ?>
			<p class="bwpm_copyright"><?php echo BwPostmanSite::footer(); ?></p>
		<?php
		} ?>
	</div>
</div>
