<?php
/**
 * BwPostman Newsletter Module
 *
 * BwPostman bootstrap 4 template for module.
 *
 * @version %%version_number%%
 * @package BwPostman-Module
 * @author Romana Boldt, Karl Klostermann
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

$n	= count($mailinglists);

$remote_ip  = Factory::getApplication()->input->server->get('REMOTE_ADDR', '', '');

JHtml::_('stylesheet', 'mod_bwpostman/bwpm_register_bs2.css', array('version' => 'auto', 'relative' => true));
JHtml::_('script', 'mod_bwpostman/bwpm_register.js', array('version' => 'auto', 'relative' => true));

if (file_exists(JPATH_BASE . $css_filename))
{
	$document->addStyleSheet(Uri::root(true) . $css_filename);
}

Text::script('MOD_BWPOSTMANERROR_FIRSTNAME');
Text::script('MOD_BWPOSTMANERROR_NAME');
Text::script('MOD_BWPOSTMAN_SUB_ERROR_SPECIAL');
Text::script('MOD_BWPOSTMAN_SPECIAL');
Text::script('MOD_BWPOSTMANERROR_EMAIL');
Text::script('MOD_BWPOSTMANERROR_EMAIL_INVALID');
Text::script('MOD_BWPOSTMANERROR_NL_CHECK');
Text::script('MOD_BWPOSTMANERROR_DISCLAIMER_CHECK');
Text::script('MOD_BWPOSTMANERROR_CAPTCHA_CHECK');

// Size of form fields - 'sm' for small fields
$formclass = '';

// We cannot use the same form name and name for the disclaimer checkbox
// because this will not work if the module and the component will be displayed
// on the same page

$required_begin = '<div class="input-append input-block-level">';

$required_end = '		<span class="add-on"><i class="icon-star"></i></span>';
$required_end .= '</div>';
?>

<noscript>
	<div id="system-message">
		<div class="alert alert-warning">
			<h4 class="alert-heading"><?php echo Text::_('WARNING'); ?></h4>
			<div>
				<p><?php echo Text::_('MOD_BWPOSTMAN_JAVASCRIPTWARNING'); ?></p>
			</div>
		</div>
	</div>
</noscript>

<div id="mod_bwpostman">
	<?php
	$disclaimer_link = '';
	if ($n == 0)
	{
		// Don't show registration form if no mailinglist is selectable ?>
		<p class="bwp_mod_error_no_mailinglists"><?php echo addslashes(Text::_('MOD_BWPOSTMANERROR_NO_MAILINGLIST_AVAILABLE')); ?></p> <?php
	}
	else
	{
		// Show registration form only if a mailinglist is selectable ?>

	<form action="<?php echo Route::_('index.php?option=com_bwpostman&task=register'); ?>" method="post" id="bwp_mod_form"
			name="bwp_mod_form" class="form-validate" onsubmit="return checkModRegisterForm();">

		<?php // Spamcheck 1 - Input-field: class="user_hightlight" style="position: absolute; top: -5000px;"
		?>
		<p class="user_hightlight">
			<label for="a_falle"><strong><?php echo addslashes(Text::_('MOD_BWPOSTMANSPAMCHECK')); ?></strong></label>
			<input type="text" name="falle" id="a_falle" size="20"
					title="<?php echo addslashes(Text::_('MOD_BWPOSTMANSPAMCHECK')); ?>" maxlength="50" />
		</p>
		<?php // End Spamcheck
		if ($paramsComponent->get('pretext', ''))
		{ // Show pretext only if set in basic parameters
			$preText = Text::_($paramsComponent->get('pretext', ''));
			?>
			<div id="bwp_mod_form_pretext" class="mb-3"><?php echo $preText; ?></div>
			<?php
		} // End: Show pretext only if set in basic parameters

		// Show formfield gender only if enabled at parameters
		if ($paramsComponent->get('show_gender', '1') == 1)
		{
			$class = $formclass === 'sm' ? 'span12' : 'span12';
			?>
			<div class="form-group bwp_mod_form_genderformat">
				<label id="gendermsg_mod" for="m_gender"> <?php echo Text::_('MOD_BWPOSTMANGENDER'); ?>:</label>
				<?php
					echo LayoutHelper::render(
						'gender_bs4',
						array('gender_selected' => '2', 'name' => 'a_gender', 'class' => $class, 'idPrefix' => 'm_'),
						$basePath = JPATH_SITE . '/components/com_bwpostman/layouts/subscriber'
					);
				?>
			</div>
			<?php
		} // End show gender

		if ($paramsComponent->get('show_firstname_field', '1') OR $paramsComponent->get('firstname_field_obligation', '1'))
		{ // Show firstname-field only if set in basic parameters
			?>
			<div id="bwp_mod_form_firstnamefield">
				<?php
				// Is filling out the firstname field obligating
				isset($subscriber->firstname) ? $sub_firstname = $subscriber->firstname : $sub_firstname = '';
				if ($paramsComponent->get('firstname_field_obligation', '1') === '1')
				{
	                echo $required_begin;
				}
				?>
				<input type="text" name="a_firstname" id="a_firstname"
						placeholder="<?php echo addslashes(Text::_('MOD_BWPOSTMANFIRSTNAME')); ?>" value="<?php echo $sub_firstname; ?>"
						class="<?php echo $formclass === "sm" ? 'span12' : 'span12'; ?>" maxlength="50" />
				<?php
				if ($paramsComponent->get('firstname_field_obligation', '1') === '1')
				{
					echo $required_end;
				}
				?>
			</div>
			<?php
		}

		if ($paramsComponent->get('show_name_field', '1') OR $paramsComponent->get('name_field_obligation', '1'))
		{
			// Show name-field only if set in basic parameters
			?>
			<div id="bwp_mod_form_namefield">
				<?php // Is filling out the name field obligating
				isset($subscriber->name) ? $sub_name = $subscriber->name : $sub_name = '';
				if ($paramsComponent->get('name_field_obligation', '1') === '1')
				{
	                echo $required_begin;
				}
				?>
				<input type="text" name="a_name" id="a_name"
						placeholder="<?php echo addslashes(Text::_('MOD_BWPOSTMANNAME')); ?>" value="<?php echo $sub_name; ?>"
                        class="<?php echo $formclass === "sm" ? 'span12' : 'span12'; ?>" maxlength="50" />
				<?php
				if ($paramsComponent->get('name_field_obligation', '1') === '1')
				{
	                echo $required_end;
				}
				?>
			</div>
			<?php
		} // End: Show name field only if set in basic parameters
		?>

		<?php
		// Show additional field only if set in basic parameters
		$showSpecial       = $paramsComponent->get('show_special', '0');
		$specialObligatory = $paramsComponent->get('special_field_obligation', '0');
		$specialLabel      = Text::_($paramsComponent->get('special_label', ''));
		$sub_special       = '';

		if (isset($subscriber->special))
		{
			$sub_special = $subscriber->special;
		}

		if($specialLabel === '')
		{
			$specialLabel = Text::_('MOD_BWPOSTMAN_SPECIAL');
		}

		if ($showSpecial || $specialObligatory)
		{
		?>
			<div id="bwp_mod_form_specialfield">
				<?php // Is filling out the additional field obligating
				if ($specialObligatory === '1')
				{
	                echo $required_begin;
				}
				?>
				<input type="text" name="a_special" id="a_special"
						placeholder="<?php echo addslashes($specialLabel); ?>" value="<?php echo $sub_special; ?>"
						class="<?php echo $formclass === "sm" ? 'span12' : 'span12'; ?>" maxlength="50" />
				<?php
				if ($specialObligatory === '1')
				{
	                echo $required_end;
				}
				?>
			</div>
		<?php
		} // End: Show additional field only if set in basic parameters
		?>

		<div id="bwp_mod_form_emailfield">
			<?php isset($subscriber->email) ? $sub_email = $subscriber->email : $sub_email = '';
	            echo $required_begin;
			?>
			<input type="text" id="a_email" name="email"
					placeholder="<?php echo addslashes(Text::_('MOD_BWPOSTMANEMAIL')); ?>" value="<?php echo $sub_email; ?>"
                    class="<?php echo $formclass === "sm" ? 'span12' : 'span12'; ?>" maxlength="100" />
			<?php
                echo $required_end;
			?>
		</div>
		<?php
		if ($paramsComponent->get('show_emailformat', '0') == 1)
		{
			// Show formfield emailformat only if enabled in basic parameters
			?>
			<div id="bwp_mod_form_emailformat" class="mb-3">
				<p id="emailformatmsg_mod">
					<?php echo Text::_('MOD_BWPOSTMANEMAILFORMAT'); ?>:
				</p>
				<?php
					echo LayoutHelper::render(
						'emailformat_bs2',
						array('mailformat_selected' => $paramsComponent->get('default_emailformat', '0'), 'formclass' => $formclass),
						$basePath = JPATH_SITE . '/components/com_bwpostman/layouts/subscriber'
					);
				?>
			</div>
			<?php
		}
		else
		{
			// hidden field with the default emailformat
			?>
			<input type="hidden" name="emailformat" value="<?php echo $paramsComponent->get('default_emailformat', '0'); ?>" />
			<?php
		} // End emailformat
		?>

		<?php // Show available mailinglists
		$n = count($mailinglists);

		$descLength = $params->get('desc_length', '150');

		if (($mailinglists) && ($n > 0))
		{
			if ($n == 1)
			{ ?>
				<input type="checkbox" style="display: none;" id="a_<?php echo "mailinglists0"; ?>" name="<?php echo "mailinglists[]"; ?>"
					title="<?php echo "mailinglists[]"; ?>" value="<?php echo $mailinglists[0]->id; ?>" checked="checked" />
				<?php
				if ($params->get('show_desc', '1') == 1)
				{ ?>
					<p id="bwp_mod_form_lists">
						<?php echo Text::_('MOD_BWPOSTMANLIST'); ?>
					</p>
					<div class="mailinglist-title hasTooltip mb-3" title="<?php echo HTMLHelper::tooltipText($mailinglists[0]->title, Text::_($mailinglists[0]->description)); ?>">
						<?php echo $mailinglist->title; ?>:
							<br /><span class="mailinglist-description-single"><?php
								echo substr(Text::_($mailinglists[0]->description), 0, $descLength);

								if (strlen(Text::_($mailinglists[0]->description)) > $descLength)
								{
									echo '... ';
									echo '<i class="icon-info"></i>';
								} ?>
							</span>
					</div>
					<?php
				}
			}
			else
			{ ?>
				<p id="bwp_mod_form_lists" class="required">
					<?php echo Text::_('MOD_BWPOSTMANLISTS') . ' <sup><i class="icon-star"></i></sup>'; ?>
				</p>
				<div id="bwp_mod_form_listsfield" class="mb-3">
				<?php
				foreach ($mailinglists AS $i => $mailinglist)
				{ ?>
					<div class="a_mailinglist_item_<?php echo $i; ?>">
						<label class="mailinglist-title hasTooltip checkbox" for="a_<?php echo "mailinglists$i"; ?>"
								title="<?php echo HTMLHelper::tooltipText($mailinglists[$i]->title, Text::_($mailinglists[$i]->description)); ?>">
							<input type="checkbox" id="a_<?php echo "mailinglists$i"; ?>" class="form-check-input" name="<?php echo "mailinglists[]"; ?>"
									title="<?php echo "mailinglists[]"; ?>" value="<?php echo $mailinglist->id; ?>" />
								<?php
								echo $mailinglist->title;
								if ($params->get('show_desc', '1') == 1)
								{
								?>:
									<br />
									<span class="mailinglist-description">
										<?php
										echo substr(Text::_($mailinglist->description), 0, $descLength);
										if (strlen(Text::_($mailinglist->description)) > $descLength)
										{
											echo '... ';
											echo '<i class="icon-info"></i>';
										} ?>
									</span>
								<?php
								} ?>
						</label>
					</div>
					<?php
				}
				?>
				</div><?php
			}
		} // End Mailinglists

		?>

		<?php // Question
		if ($paramsComponent->get('use_captcha', '0') == 1)
		{ ?>
			<div class="question">
				<p class="security_question_entry small"><?php echo Text::_('MOD_BWPOSTMANCAPTCHA'); ?></p>
				<p class="security_question_lbl"><strong><?php echo Text::_($paramsComponent->get('security_question', '')); ?></strong></p>
				<div class="questionp">
					<div class="input-append input-block-level">
						<input type="text" name="stringQuestion" id="a_stringQuestion" placeholder="<?php echo addslashes(Text::_('MOD_BWPOSTMANCAPTCHA_LABEL')); ?>"
								maxlength="50" /><span class="append-area"><i class="bwp_icon-star"></i></span>
						<span class="add-on"><i class="icon-star"></i></span>
					</div>
				</div>
			</div>
			<?php
		} // End question
		?>

		<?php // Captcha
		if ($paramsComponent->get('use_captcha', '0') == 2)
		{
			$codeCaptcha = md5(microtime()); ?>
			<div class="captcha">
				<p class="security_question_entry small"><?php echo Text::_('MOD_BWPOSTMANCAPTCHA'); ?></p>
				<p class="security_question_lbl">
					<img src="<?php echo Uri::base(); ?>index.php?option=com_bwpostman&amp;view=register&amp;task=showCaptcha&amp;format=raw&amp;codeCaptcha=<?php echo $codeCaptcha; ?>" alt="captcha" />
				</p>
				<div class="captchap">
					<div class="input-append input-block-level">
						<input type="text" name="stringCaptcha" id="a_stringCaptcha"
							placeholder="<?php echo addslashes(Text::_('MOD_BWPOSTMANCAPTCHA_LABEL')); ?>"
							maxlength="50" />
						<span class="add-on"><i class="icon-star"></i></span>
					</div>
				</div>
			</div>
			<input type="hidden" name="codeCaptcha" value="<?php echo $codeCaptcha; ?>" />
			<?php
		} // End captcha
		?>
		<?php // End Spamcheck 2

		if ($paramsComponent->get('disclaimer', '0'))
		{
			// Show Disclaimer only if enabled in basic parameters
			?>
			<div id="bwp_mod_form_disclaimer" class="my-3">
				<input type="checkbox" id="agreecheck_mod" class="pull-left" name="agreecheck_mod" title="agreecheck_mod" />
				<?php
				// Extends the disclaimer link with '&tmpl=component' to see only the content
				$tpl_com = $paramsComponent->get('showinmodal', '1') == 1 ? '&amp;tmpl=component' : '';
				if ($paramsComponent->get('disclaimer_selection', '1') == 1 && $paramsComponent->get('article_id', '0') > 0)
				{
					// Disclaimer article and target_blank or not
					$disclaimer_link = Route::_(Uri::base() . ContentHelperRoute::getArticleRoute($paramsComponent->get('article_id', '0')) . $tpl_com);
				}
				elseif ($paramsComponent->get('disclaimer_selection', '1') == 2 && $paramsComponent->get('disclaimer_menuitem', '0') > 0)
				{
					// Disclaimer menu item and target_blank or not
					if ($tpl_com !== '' && Factory::getConfig()->get('sef') === '1')
					{
						$tpl_com = '?tmpl=component';
					}
					$disclaimer_link = Route::_("index.php?Itemid={$paramsComponent->get('disclaimer_menuitem', '0')}") . $tpl_com;
				}
				else
				{
					// Disclaimer url and target_blank or not
					$disclaimer_link = $paramsComponent->get('disclaimer_link', '');
				} ?>
				<label class="checkbox">
					<?php
					// Show inside modalbox
					if ($paramsComponent->get('showinmodal', '1') == 1)
					{
						echo '<a id="bwp_mod_open"';
					}
					// Show not in modalbox
					else
					{
						echo '<a href="' . $disclaimer_link . '"';
						if ($paramsComponent->get('disclaimer_target', '0') == 0)
						{
							echo ' target="_blank"';
						};
					}
					echo '>' . Text::_('MOD_BWPOSTMAN_DISCLAIMER') . '</a> <sup><i class="icon-star"></i></sup>'; ?>
				</label>
			</div>
			<?php
		} // Show disclaimer ?>

		<div class="mod-button-register text-right">
			<button class="button validate btn btn-secondary" type="submit"><?php echo Text::_('MOD_BWPOSTMANBUTTON_REGISTER'); ?>
			</button>
		</div>

		<input type="hidden" name="option" value="com_bwpostman" />
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="view" value="register" />
		<input type="hidden" name="bwp-<?php echo $captcha; ?>" value="1" />

		<?php // TODO: Has subscriber->id to be here or may this remain empty? ?>
		<!-- <input type="hidden" name="id" value="<?php echo isset($subscriber->id); ?>" /> -->
		<input type="hidden" name="registration_ip" value="<?php echo $remote_ip; ?>" />
		<input type="hidden" name="name_field_obligation_mod" id="name_field_obligation_mod"
				value="<?php echo $paramsComponent->get('name_field_obligation', '1'); ?>" />
		<input type="hidden" name="firstname_field_obligation_mod" id="firstname_field_obligation_mod"
				value="<?php echo $paramsComponent->get('firstname_field_obligation', '1'); ?>" />
		<input type="hidden" name="special_field_obligation_mod" id="special_field_obligation_mod"
				value="<?php echo $paramsComponent->get('special_field_obligation', '0'); ?>" />
		<input type="hidden" name="show_name_field_mod" id="show_name_field_mod"
				value="<?php echo $paramsComponent->get('show_name_field', '1'); ?>" />
		<input type="hidden" name="show_firstname_field_mod" id="show_firstname_field_mod"
				value="<?php echo $paramsComponent->get('show_firstname_field', '1'); ?>" />
		<input type="hidden" name="show_special_mod" id="show_special_mod" value="<?php echo $paramsComponent->get('show_special', '1'); ?>" />
		<input type="hidden" name="special_label" id="special_label" value="<?php echo $paramsComponent->get('special_label', ''); ?>" />
		<input type="hidden" name="mod_id" id="mod_id" value="<?php echo $module_id; ?>" />
		<?php echo HtmlHelper::_('form.token'); ?>
	</form>

		<p id="bwp_mod_form_required" class="small">(<i class="icon-star"></i>) <?php echo Text::_('MOD_BWPOSTMANREQUIRED'); ?></p>
		<div id="bwp_mod_form_editlink" class="text-right">
			<button class="button btn btn-outline-secondary" onclick="location.href='<?php
				echo Route::_('index.php?option=com_bwpostman&amp;view=edit&amp;Itemid=' . $itemid);
				?>'">
				<?php echo Text::_('MOD_BWPOSTMANLINK_TO_EDITLINKFORM'); ?>
			</button>
		</div>
	<?php
	}; // End: Show registration form
	// The Modal
	?>
	<input type="hidden" id="bwp_mod_Modalhref" value="<?php echo $disclaimer_link; ?>" />
	<div id="bwp_mod_Modal" class="bwp_mod_modal">
		<div id="bwp_mod_modal-content">
			<h4 id="bwp_modal-title">Information</h4>
			<span class="bwp_mod_close">&times;</span>
			<div id="bwp_mod_wrapper"></div>
		</div>
	</div>
</div>
