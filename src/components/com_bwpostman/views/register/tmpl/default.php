<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman register default template for frontend.
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

HtmlHelper::_('behavior.keepalive');
HtmlHelper::_('behavior.formvalidator');
HtmlHelper::_('formbehavior.chosen', 'select');

HTMLHelper::_('bootstrap.tooltip');

$remote_ip  = Factory::getApplication()->input->server->get('REMOTE_ADDR', '', '');
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
					id="bwp_com_form" name="bwp_com_form" class="form-validate form-inline">
				<?php // Spamcheck 1 - Input-field: class="user_highlight" style="position: absolute; top: -5000px;" ?>
				<p class="user_hightlight">
					<label for="falle"><strong><?php echo addslashes(Text::_('COM_BWPOSTMAN_SPAMCHECK')); ?></strong></label>
					<input type="text" name="falle" id="falle" size="20"
							title="<?php echo addslashes(Text::_('COM_BWPOSTMAN_SPAMCHECK')); ?>" maxlength="50" />
				</p>
				<?php // End Spamcheck

				echo LayoutHelper::render(
					'default',
					array('subscriber' => $this->subscriber, 'params' => $this->params, 'lists' => $this->lists),
					$basePath = JPATH_COMPONENT . '/layouts/subscriber'
				);
				?>
				<?php // Question
				if ($this->params->get('use_captcha') == 1) : ?>
					<div class="question">
						<p class="question-text"><?php echo Text::_('COM_BWPOSTMAN_CAPTCHA'); ?></p>
						<p class="security_question_lbl"><?php echo Text::_($this->params->get('security_question')); ?></p>
						<p class="question-result input-append">
							<label id="question" for="stringQuestion"><?php echo Text::_('COM_BWPOSTMAN_CAPTCHA_LABEL'); ?>:</label>
							<input type="text" name="stringQuestion" id="stringQuestion" size="40" maxlength="50" />
							<span class="append-area"><i class="icon-star"></i></span>
						</p>
					</div>
				<?php endif; // End question ?>

				<?php // Captcha
				if ($this->params->get('use_captcha') == 2) :
					$codeCaptcha = md5(microtime());
					?>

					<div class="captcha">
						<p class="captcha-text"><?php echo Text::_('COM_BWPOSTMAN_CAPTCHA'); ?></p>
						<p class="security_question_lbl">
							<img src="<?php echo Uri::base();?>index.php?option=com_bwpostman&amp;view=register&amp;task=showCaptcha&amp;format=raw&amp;codeCaptcha=<?php echo $codeCaptcha; ?>" alt="captcha" />
						</p>
						<p class="captcha-result input-append">
							<label id="captcha" for="stringCaptcha"><?php echo Text::_('COM_BWPOSTMAN_CAPTCHA_LABEL'); ?>:</label>
							<input type="text" name="stringCaptcha" id="stringCaptcha" size="40" maxlength="50" />
							<span class="append-area"><i class="icon-star"></i></span>
						</p>
					</div>
					<input type="hidden" name="codeCaptcha" value="<?php echo $codeCaptcha; ?>" />
				<?php endif; // End captcha ?>

				<div class="contentpane<?php echo $this->params->get('pageclass_sfx'); ?>">
					<?php // Show Disclaimer only if enabled in basic parameters
					if ($this->params->get('disclaimer')) :
						?>
						<p class="agree_check">
							<input title="agreecheck" type="checkbox" id="agreecheck" name="agreecheck" />
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
								$disclaimer_link = Route::_('index.php?Itemid=' . $this->params->get('disclaimer_menuitem') . $tpl_com);
							}
							// Disclaimer url and target_blank or not
							else
							{
								$disclaimer_link = $this->params->get('disclaimer_link');
							}
							?>
							<span>
								<?php
								// Show inside modalbox
								if ($this->params->get('showinmodal') == 1)
								{
									echo '<a id="bwp_open" data-target="#DisclaimerModal" data-toggle="modal"';
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
								echo '>' . Text::_('COM_BWPOSTMAN_DISCLAIMER') . '</a> <i class="icon-star"></i>'; ?>
							</span>
						</p>
					<?php endif; // Show disclaimer ?>
					<p class="show_disclaimer">
						<?php echo Text::_('COM_BWPOSTMAN_REQUIRED'); ?>
					</p>
				</div>

				<p class="button-register text-right">
					<button class="button validate btn text-right" type="submit"><?php echo Text::_('COM_BWPOSTMAN_BUTTON_REGISTER'); ?></button>
				</p>

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
			{
				$modalParams               = array();
				$modalParams['modalWidth'] = 80;
				$modalParams['bodyHeight'] = 70;
				$modalParams['url']        = $disclaimer_link;
				$modalParams['title']      = Text::_('COM_BWPOSTMAN_DISCLAIMER_TITLE');
				echo HTMLHelper::_('bootstrap.renderModal', 'DisclaimerModal', $modalParams);
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

<script type="text/javascript">
jQuery(document).ready(function()
{
	// Turn radios into btn-group
	jQuery('.radio.btn-group label').addClass('btn');
	jQuery(".btn-group label:not(.active)").click(function()
	{
		var label = jQuery(this);
		var input = jQuery('#' + label.attr('for'));

		if (!input.prop('checked'))
		{
			label.closest('.btn-group').find("label").removeClass('active btn-success btn-danger btn-primary');
			if (input.val() === '')
			{
				label.addClass('active btn-primary');
			}
			else if (input.val() === 0)
			{
				label.addClass('active btn-danger');
			}
			else
			{
				label.addClass('active btn-success');
			}
			input.prop('checked', true);
		}
	});
	jQuery(".btn-group input[checked=checked]").each(function()
	{
		if (jQuery(this).val() === '')
		{
			jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-primary');
		}
		else if (jQuery(this).val() === 0)
		{
			jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-danger');
		}
		else
		{
			jQuery("label[for=" + jQuery(this).attr('id') + "]").addClass('active btn-success');
		}
	});
})
</script>
