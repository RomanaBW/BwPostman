<?php
/**
 * BwPostman Newsletter Module
 *
 * BwPostman modal default template for module.
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
use Joomla\Component\Content\Site\Helper\RouteHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanSubscriberHelper;

HtmlHelper::_('behavior.keepalive');

$n	= count($mailinglists);

// Build the gender select list
$lists['gender'] = BwPostmanSubscriberHelper::buildGenderList('2', 'a_gender', '', 'm_');

$itemid = BwPostmanSubscriberHelper::getMenuItemid('register');

$remote_ip  = Factory::getApplication()->input->server->get('REMOTE_ADDR', '', '');

$wa->useStyle('mod_bwpostman.bwpm_register_modal');
$wa->useScript('mod_bwpostman.bwpm_register');

if (file_exists(JPATH_BASE . $css_filename))
{
	$wa->registerAndUseStyle('mod_bwpostman.bwpm_register_custom', Uri::root(true) . $css_filename);
//	$document->addStyleSheet(Uri::root(true) . $css_filename);
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

// We cannot use the same form name and name for the disclaimer checkbox
// because this will not work if the module and the component will be displayed
// on the same page
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
		$wa->useScript('mod_bwpostman.bwpm_register_btn_group');
		$wa->useScript('mod_bwpostman.bwpm_register_modal');
		$wa->useScript('mod_bwpostman.bwpm_register_modal_modal');
		// Show registration form only if a mailinglist is selectable ?>
	<button id="bwp_reg_open" type="button" class="btn">
		<?php echo Text::_(
				$paramsComponent->get('modal_btn_label', '') != ''
				? $paramsComponent->get('modal_btn_label', '')
				: $module->title);
		?>
	</button>

	<div id="bwp_reg_modal">
		<div id="bwp_reg_modal-content">
			<div class="bwp_reg_header">
				<h4 id="bwp_reg_title"><?php echo $module->title; ?></h4>
				<span class="bwp_reg_close">&times;</span>
			</div>
			<div id="bwp_reg_wrapper">
				<form action="<?php echo Route::_('index.php?option=com_bwpostman&view=register'); ?>" method="post" id="bwp_mod_form"
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
						<p id="bwp_mod_form_pretext"><?php echo $preText; ?></p>
						<?php
					} // End: Show pretext only if set in basic parameters

					// Show formfield gender only if enabled at parameters
					if ($paramsComponent->get('show_gender', '1') == 1)
					{
						?>
						<p id="bwp_mod_form_genderfield">
							<label id="gendermsg_mod" class="bwp-label">
								<?php echo Text::_('MOD_BWPOSTMANGENDER'); ?>:
							</label>
							<?php echo $lists['gender']; ?>
						</p>
						<?php
					} // End show gender

					if ($paramsComponent->get('show_firstname_field', '1') OR $paramsComponent->get('firstname_field_obligation', '1'))
					{ // Show firstname-field only if set in basic parameters
						?>
						<p id="bwp_mod_form_firstnamefield"
								class="bwp input<?php echo ($paramsComponent->get('firstname_field_obligation', '1')) ? '-append' : '-xx' ?>">
							<?php
							// Is filling out the firstname field obligating
							isset($subscriber->firstname) ? $sub_firstname = $subscriber->firstname : $sub_firstname = '';
							$required = '';
							$append   = '';

							if ($paramsComponent->get('firstname_field_obligation', '1'))
							{
								$required = '<span class="add-on"><i class="icon-star"></i></span>';
								$append   = '';
							}
							?>
							<label class="bwp-label" for="a_firstname"><?php echo Text::_('MOD_BWPOSTMANFIRSTNAME'); ?>:</label>
							<input type="text" name="a_firstname" id="a_firstname" <?php echo $append; ?>
									value="<?php echo $sub_firstname; ?>" maxlength="50" /><?php echo $required; ?>
						</p>
						<?php
					}

					if ($paramsComponent->get('show_name_field', '1') OR $paramsComponent->get('name_field_obligation', '1'))
					{
						// Show name-field only if set in basic parameters
						?>
						<p id="bwp_mod_form_namefield"
								class="bwp input<?php echo ($paramsComponent->get('name_field_obligation', '1')) ? '-append' : '-xx' ?>">
							<?php // Is filling out the name field obligating
							isset($subscriber->name) ? $sub_name = $subscriber->name : $sub_name = '';
							$required = '';
							$append   = '';

							if ($paramsComponent->get('name_field_obligation', '1'))
							{
								$required = '<span class="add-on"><i class="icon-star"></i></span>';
								$append   = '';
							}
							?>
							<label class="bwp-label" for="a_name"><?php echo Text::_('MOD_BWPOSTMANNAME'); ?>:</label>
							<input type="text" name="a_name" id="a_name" <?php echo $append; ?>
									value="<?php echo $sub_name; ?>" maxlength="50" /><?php echo $required; ?>
						</p>
						<?php
					} // End: Show name field only if set in basic parameters
					?>

					<?php
					// Show additional field only if set in basic parameters
					$showSpecial       = $paramsComponent->get('show_special', '1');
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
						$required = '';
						$append   = '';

						if ($specialObligatory)
						{
							$required = '<span class="add-on"><i class="icon-star"></i></span>';
							$append   = '';
						}
						?>
						<p id="bwp_mod_form_specialfield" class="bwp input<?php echo ($specialObligatory) ? '-append' : '-xx' ?>">
							<?php // Is filling out the additional field obligating
							?>
							<label class="bwp-label" for="a_special"><?php echo $specialLabel; ?>:</label>
							<input type="text" name="a_special" id="a_special" <?php echo $append; ?>
									value="<?php echo $sub_special; ?>" maxlength="50" /><?php echo $required; ?>
						</p>
						<?php
					} // End: Show additional field only if set in basic parameters
					?>

					<?php isset($subscriber->email) ? $sub_email = $subscriber->email : $sub_email = ''; ?>
					<p id="bwp_mod_form_emailfield" class="bwp input-append">
						<label class="bwp-label" for="a_email"><?php echo Text::_('MOD_BWPOSTMANEMAIL'); ?>:</label>
						<input type="text" id="a_email" name="email" value="<?php echo $sub_email; ?>"
								maxlength="100" /><span class="add-on"><i class="icon-star"></i></span>
					</p>
					<?php
					if ($paramsComponent->get('show_emailformat', '1') == 1)
					{
						// Show formfield emailformat only if enabled in basic parameters
						?>
						<div id="bwp_mod_form_emailformatfield" class="mb">
							<label id="emailformatmsg_mod" class="bwp-label">
								<?php echo Text::_('MOD_BWPOSTMANEMAILFORMAT'); ?>:
							</label>
							<?php echo $lists['emailformat']; ?>
						</div>
						<?php
					}
					else
					{
						// hidden field with the default emailformat
						?>
						<input type="hidden" name="emailformat" value="<?php echo $paramsComponent->get('default_emailformat', '1'); ?>" />
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
							<p id="bwp_mod_form_lists">
								<?php echo Text::_('MOD_BWPOSTMANLIST'); ?>
							</p>
							<div class="mailinglist-title mb"><?php echo $mailinglist->title; ?>
								<input type="checkbox" style="display: none;" id="a_<?php echo "mailinglists0"; ?>" name="<?php echo "mailinglists[]"; ?>"
									title="<?php echo "mailinglists[]"; ?>" value="<?php echo $mailinglists[0]->id; ?>" checked="checked" />
							<?php
							if ($params->get('show_desc', '1') == 1)
							{ ?>
								<span class="mailinglist-description-single"><?php
									echo substr(Text::_($mailinglists[0]->description), 0, $descLength);

									if (strlen(Text::_($mailinglists[0]->description)) > $descLength)
									{
										echo '... ';
										echo '<span class="bwptip" title="' . Text::_($mailinglists[0]->description) . '"><i class="icon-info-sign"></i></span>';
									} ?>
								</span>
								<?php
							}
							echo '</div>';
						}
						else
						{ ?>
							<div id="bwp_mod_form_lists" class="mb">
								<?php echo Text::_('MOD_BWPOSTMANLISTS') . ' <sup><i class="icon-star"></i></sup>'; ?>
							</div>
							<div id="bwp_mod_form_listsfield" class="mb">
							<?php
							foreach ($mailinglists AS $i => $mailinglist)
							{ ?>
								<div class="a_mailinglist_item_<?php echo $i; ?>">
									<label class="mailinglist-title">
									<input type="checkbox" id="a_<?php echo "mailinglists$i"; ?>" name="<?php echo "mailinglists[]"; ?>"
											title="<?php echo "mailinglists[]"; ?>" value="<?php echo $mailinglist->id; ?>" />
										<?php
										echo $mailinglist->title;
										if ($params->get('show_desc', '1') == 1)
										{
										?>:
											<span class="mailinglist-description">
												<?php
												echo substr(Text::_($mailinglist->description), 0, $descLength);
												if (strlen(Text::_($mailinglist->description)) > $descLength)
												{
													echo '... ';
													echo '<span class="bwptip" title="' . Text::_($mailinglist->description) . '"><i class="icon-info-sign"></i></span>';
												} ?>
											</span>
									</label>
									<?php
										}
										else
										{
											echo '</label>';
										} ?>
								</div>
								<?php
							}
							?>
							</div><?php
						}
					} // End Mailinglists

					// Question
					if ($paramsComponent->get('use_captcha', '0') == 1)
					{ ?>
						<div class="question">
							<p class="security_question_entry"><?php echo Text::_('MOD_BWPOSTMANCAPTCHA'); ?></p>
							<p class="security_question_lbl"><?php echo Text::_($paramsComponent->get('security_question', '')); ?></p>
							<p class="question-result input-append">
								<label for="a_stringQuestion"></label>
								<input type="text" name="stringQuestion" id="a_stringQuestion"
										placeholder="<?php echo addslashes(Text::_('MOD_BWPOSTMANCAPTCHA_LABEL')); ?>" maxlength="50" /><span
								class="add-on"><i class="icon-star"></i></span>
							</p>
						</div>
						<?php
					} // End question
					?>

					<?php // Captcha
					if ($paramsComponent->get('use_captcha', '0') == 2)
					{
						$codeCaptcha = md5(microtime()); ?>
						<div class="captcha">
							<p class="security_question_entry"><?php echo Text::_('MOD_BWPOSTMANCAPTCHA'); ?></p>
							<p class="security_question_lbl">
								<img src="<?php echo Uri::base(); ?>index.php?option=com_bwpostman&amp;view=register&amp;task=showCaptcha&amp;format=raw&amp;codeCaptcha=<?php echo $codeCaptcha; ?>" alt="captcha" />
							</p>
							<p class="captcha-result input-append">
								<label for="a_stringCaptcha"></label>
								<input type="text" name="stringCaptcha" id="a_stringCaptcha"
										placeholder="<?php echo addslashes(Text::_('MOD_BWPOSTMANCAPTCHA_LABEL')); ?>"
										maxlength="50" /><span class="add-on"><i class="icon-star"></i></span>
							</p>
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
						<p id="bwp_mod_form_disclaimer">
							<input type="checkbox" id="agreecheck_mod" name="agreecheck_mod" title="<?php echo Text::_('MOD_BWPOSTMAN_DISCLAIMER'); ?>" />
							<?php
							// Extends the disclaimer link with '&tmpl=component' to see only the content
							$tpl_com = $paramsComponent->get('showinmodal', '1') == 1 ? '&amp;tmpl=component' : '';
							if ($paramsComponent->get('disclaimer_selection', '1') == 1 && $paramsComponent->get('article_id', '0') > 0)
							{
								// Disclaimer article and target_blank or not
								$disclaimer_link = Route::_(Uri::base() . RouteHelper::getArticleRoute($paramsComponent->get('article_id', '0')) . $tpl_com);
							}
							elseif ($paramsComponent->get('disclaimer_selection', '1') == 2 && $paramsComponent->get('disclaimer_menuitem', '0') > 0)
							{
								// Disclaimer menu item and target_blank or not
								$disclaimer_link = Route::_('index.php?Itemid=' . $paramsComponent->get('disclaimer_menuitem', '0') . $tpl_com);
							}
							else
							{
								// Disclaimer url and target_blank or not
								$disclaimer_link = $paramsComponent->get('disclaimer_link', '');
							} ?>
							<span>
								<?php
								// Show inside modalbox
								if ($paramsComponent->get('showinmodal', '1') == 1)
								{
									echo '<a id="bwp_mod_open" href="javascript:void(0);"';
								}
								// Show not in modalbox
								else
								{
									echo '<a href="' . $disclaimer_link . '"';
									if ($paramsComponent->get('disclaimer_target', '0') == 0)
									{
										echo ' target="_blank"';
									}
								}
								echo '>' . Text::_('MOD_BWPOSTMAN_DISCLAIMER') . '</a> <sup><i class="icon-star"></i></sup>'; ?>
							</span>
						</p>
						<?php
					} // Show disclaimer ?>

					<div class="mod-button-register mb">
						<button class="button validate btn" type="submit"><?php echo Text::_('MOD_BWPOSTMANBUTTON_REGISTER'); ?>
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

				<p id="bwp_mod_form_required">(<i class="icon-star"></i>) <?php echo Text::_('MOD_BWPOSTMANREQUIRED'); ?></p>
				<div id="bwp_mod_form_editlink">
					<button class="button btn" onclick="location.href='<?php
						echo Route::_('index.php?option=com_bwpostman&amp;view=edit&amp;Itemid=' . $itemid);
						?>'">
						<?php echo Text::_('MOD_BWPOSTMANLINK_TO_EDITLINKFORM'); ?>
					</button>
				</div>
			</div>
		</div>
	</div>
	<?php
	} // End: Show registration form
	// The Modal
	?>
	<input type="hidden" id="bwp_mod_Modalhref" value="<?php echo $disclaimer_link; ?>" />
	<div id="bwp_mod_Modal" class="bwp_mod_modal">
		<div id="bwp_mod_modal-content">
			<h4 id="bwp_mod_modal-title">Information</h4>
			<span class="bwp_mod_close">&times;</span>
			<div id="bwp_mod_wrapper"></div>
		</div>
	</div>
</div>
