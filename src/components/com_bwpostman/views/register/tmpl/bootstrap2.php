<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman register bootstrap 2 template for frontend.
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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

JLoader::register('ContentHelperRoute', JPATH_SITE . '/components/com_content/helpers/route.php');

HTMLHelper::_('bootstrap.tooltip');

JHtml::_('stylesheet', 'com_bwpostman/bwpostman_bs2.css', array('version' => 'auto', 'relative' => true));

$remote_ip  = Factory::getApplication()->input->server->get('REMOTE_ADDR', '', '');

$this->subscriber = $displayData['subscriber'];
$this->params     = $displayData['params'];
$this->lists      = $displayData['lists'];
$this->captcha      = $displayData['captcha'];

$formclass	= $this->params->get('formclass');
?>

<div id="bwpostman">
	<div id="bwp_com_register">
		<?php // displays a message if no availible mailinglist
		if ($this->lists['available_mailinglists'])
		{
			if (($this->params->get('show_page_heading') != 0) && ($this->params->get('page_heading') != ''))
			{ ?>
				<h1 class="componentheading<?php echo $this->params->get('pageclass_sfx'); ?>">
					<?php echo $this->escape($this->params->get('page_heading')); ?>
				</h1>
			<?php
			} ?>

		<div class="content_inner">
			<form action="<?php echo Route::_('index.php?option=com_bwpostman'); ?>" method="post"
					id="bwp_com_form" name="bwp_com_form" class="form-validate form-horizontal">
				<?php // Spamcheck 1 - Input-field: class="user_highlight" style="position: absolute; top: -5000px;" ?>
				<p class="user_hightlight">
					<label for="falle"><strong><?php echo addslashes(Text::_('COM_BWPOSTMAN_SPAMCHECK')); ?></strong></label>
					<input type="text" name="falle" id="falle" size="20"
							title="<?php echo addslashes(Text::_('COM_BWPOSTMAN_SPAMCHECK')); ?>" maxlength="50" />
				</p>
				<?php // End Spamcheck

				echo LayoutHelper::render(
					'bootstrap2',
					array('subscriber' => $this->subscriber, 'params' => $this->params, 'lists' => $this->lists),
					$basePath = JPATH_COMPONENT . '/layouts/subscriber'
				);
				?>
				<?php // Question
				if ($this->params->get('use_captcha') == 1) : ?>
					<div class="question img-polaroid">
						<div class="question-text"><?php echo Text::_('COM_BWPOSTMAN_CAPTCHA'); ?></div>
						<div class="security_question_lbl controls strong my-3"><?php echo Text::_($this->params->get('security_question')); ?></div>
						<div class="control-group question-result">
							<label id="question" class="control-label" for="stringQuestion"><?php echo Text::_('COM_BWPOSTMAN_CAPTCHA_LABEL'); ?>:</label>
				            <div class="controls">
				                <div class="input-append">
									<input type="text" name="stringQuestion" id="stringQuestion" size="40"
										class="<?php echo $formclass === "sm" ? 'input-small' : 'input-medium'; ?>" maxlength="50" />
									<span class="add-on"><i class="icon-star"></i></span>
								</div>
							</div>
						</div>
					</div>
				<?php endif; // End question ?>

				<?php // Captcha
				if ($this->params->get('use_captcha') == 2) :
					$codeCaptcha = md5(microtime());
					?>

					<div class="captcha img-polaroid">
						<div class="captcha-text"><?php echo Text::_('COM_BWPOSTMAN_CAPTCHA'); ?></div>
						<div class="security_question_lbl controls strong my-3">
							<img src="<?php echo Uri::base();?>index.php?option=com_bwpostman&amp;view=register&amp;task=showCaptcha&amp;format=raw&amp;codeCaptcha=<?php echo $codeCaptcha; ?>" alt="captcha" />
						</div>
						<div class="control-group captcha-result">
							<label id="captcha" class="control-label" for="stringCaptcha"><?php echo Text::_('COM_BWPOSTMAN_CAPTCHA_LABEL'); ?>:</label>
				            <div class="controls">
				                <div class="input-append">
									<input type="text" name="stringCaptcha" id="stringCaptcha" size="40"
										class="<?php echo $formclass === "sm" ? 'input-small' : 'input-medium'; ?>" maxlength="50" />
									<span class="add-on"><i class="icon-star"></i></span>
								</div>
				            </div>
						</div>
					</div>
					<input type="hidden" name="codeCaptcha" value="<?php echo $codeCaptcha; ?>" />
				<?php endif; // End captcha ?>

				<div class="disclaimer<?php echo $this->params->get('pageclass_sfx'); ?>">
					<?php // Show Disclaimer only if enabled in basic parameters
					if ($this->params->get('disclaimer')) :
						?>
						<div class="agree_check my-3">
							<input title="agreecheck" type="checkbox" id="agreecheck" class="pull-left" name="agreecheck" />
							<?php
							// Extends the disclaimer link with '&tmpl=component' to see only the content
							$tpl_com = $this->params->get('showinmodal') == 1 ? '&amp;tmpl=component' : '';
							// Disclaimer article and target_blank or not
							if ($this->params->get('disclaimer_selection') == 1 && $this->params->get('article_id') > 0)
							{
								$disclaimer_link = Route::_(Uri::base() . ContentHelperRoute::getArticleRoute($this->params->get('article_id') . $tpl_com, 0));
							}
							// Disclaimer menu item and target_blank or not
							elseif ($this->params->get('disclaimer_selection') == 2 && $this->params->get('disclaimer_menuitem') > 0)
							{
								if ($tpl_com !== '' && Factory::getConfig()->get('sef') === '1')
								{
									$tpl_com = '?tmpl=component';
								}
								$disclaimer_link = Route::_("index.php?Itemid={$this->params->get('disclaimer_menuitem')}") . $tpl_com;
							}
							// Disclaimer url and target_blank or not
							else
							{
								$disclaimer_link = $this->params->get('disclaimer_link');
							}
							?>
							<label class="checkbox">
								<?php
								// Show inside modalbox
								if ($this->params->get('showinmodal') == 1)
								{
									echo '<a id="bwp_com_open"';
								}
								// Show not in modalbox
								else
								{
									echo '<a href="' . $disclaimer_link . '"';
									if ($this->params->get('disclaimer_target') == 0)
									{
										echo ' target="_blank"';
									};
								}
								echo '>' . Text::_('COM_BWPOSTMAN_DISCLAIMER') . '</a> <sup><i class="icon-star"></i></sup>'; ?>
							</label>
						</div>
					<?php endif; // Show disclaimer ?>

					<div class="button-register mb-3">
						<button class="button validate btn" type="submit"><?php echo Text::_('COM_BWPOSTMAN_BUTTON_REGISTER'); ?></button>
					</div>

					<div class="register-required small">
						<?php echo Text::_('COM_BWPOSTMAN_REQUIRED'); ?>
					</div>
				</div>

			<input type="hidden" name="option" value="com_bwpostman" />
			<input type="hidden" name="task" value="save" />
			<input type="hidden" name="view" value="register" />
			<input type="hidden" name="id" value="<?php echo $this->subscriber->id; ?>" />
			<input type="hidden" name="bwp-<?php echo $this->captcha; ?>" value="1" />
			<input type="hidden" name="name_field_obligation" id="name_field_obligation"
					value="<?php echo $this->params->get('name_field_obligation'); ?>" />
			<input type="hidden" name="firstname_field_obligation" id="firstname_field_obligation"
					value="<?php echo $this->params->get('firstname_field_obligation'); ?>" />
			<input type="hidden" name="special_field_obligation" id="special_field_obligation"
					value="<?php echo $this->params->get('special_field_obligation'); ?>" />
			<input type="hidden" name="show_name_field" value="<?php echo $this->params->get('show_name_field'); ?>" />
			<input type="hidden" name="show_firstname_field" value="<?php echo $this->params->get('show_firstname_field'); ?>" />
			<input type="hidden" name="show_special" value="<?php echo $this->params->get('show_special'); ?>" />
			<input type="hidden" name="registration_ip" value="<?php echo $remote_ip; ?>" />
			<?php echo HtmlHelper::_('form.token'); ?>
			</form>

			<?php

			// The Modal
			if ($this->params->get('showinmodal') == 1)
			{ ?>
				<input type="hidden" id="bwp_com_Modalhref" value="<?php echo $disclaimer_link; ?>" />
				<div id="bwp_com_Modal" class="bwp_com_modal">
					<div id="bwp_com_modal-content">
						<h4 id="bwp_modal-title">Information</h4>
						<span class="bwp_com_close">&times;</span>
						<div id="bwp_com_wrapper"></div>
					</div>
				</div>
			<?php
			}
		}
		else
		{
			echo Text::_('COM_BWPOSTMAN_MESSAGE_NO_AVAILIBLE_MAILINGLIST');
		}

		if ($this->params->get('show_boldt_link') === '1')
		{ ?>
			<p class="bwpm_copyright"><?php echo BwPostman::footer(); ?></p>
		<?php
		} ?>
		</div>
	</div>
</div>
<?php
if ($this->params->get('showinmodal') == 1)
{ ?>
<script type="text/javascript">
jQuery(document).ready(function()
{
	function setComModal() {
		// Set the modal height and width 90%
		if (typeof window.innerWidth != 'undefined')
		{
			viewportwidth = window.innerWidth,
				viewportheight = window.innerHeight
		}
		else if (typeof document.documentElement != 'undefined'
			&& typeof document.documentElement.clientWidth !=
			'undefined' && document.documentElement.clientWidth != 0)
		{
			viewportwidth = document.documentElement.clientWidth,
				viewportheight = document.documentElement.clientHeight
		}
		else
		{
			viewportwidth = document.getElementsByTagName('body')[0].clientWidth,
				viewportheight = document.getElementsByTagName('body')[0].clientHeight
		}
		var modalcontent = document.getElementById('bwp_com_modal-content');
		modalcontent.style.height = viewportheight-(viewportheight*0.10)+'px';
		modalcontent.style.width = viewportwidth-(viewportwidth*0.10)+'px';

		// Get the modal
		var commodal = document.getElementById('bwp_com_Modal');
		var commodalhref = document.getElementById('bwp_com_Modalhref').value;

		// Get the Iframe-Wrapper and set Iframe
		var wrapper = document.getElementById('bwp_com_wrapper');
		var html = '<iframe id="BwpFrame" name="BwpFrame" src="'+commodalhref+'" frameborder="0" style="width:100%; height:100%;"></iframe>';

		// Get the button that opens the modal
		var btnopen = document.getElementById("bwp_com_open");

		// Get the <span> element that closes the modal
		var btnclose = document.getElementsByClassName("bwp_com_close")[0];

		// When the user clicks the button, open the modal
		btnopen.onclick = function() {
			wrapper.innerHTML = html;
			// Hack for Beez3 template
			var iframe = document.getElementById('BwpFrame');
			iframe.onload = function() {
				this.contentWindow.document.head.insertAdjacentHTML("beforeend", `<style>.contentpane #all{max-width:unset;}</style>`)
			}
			commodal.style.display = "block";
		}

		// When the user clicks on <span> (x), close the modal
		btnclose.onclick = function() {
			commodal.style.display = "none";
		}

		// When the user clicks anywhere outside of the modal, close it
		window.addEventListener('click', function(event) {
			if (event.target == commodal) {
				commodal.style.display = "none";
			}
		});
	}
	setComModal();
})
</script>
<?php
} ?>