<?php
/**
 * BwPostman Newsletter Module
 *
 * BwPostman bootstrap 3 template for module.
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
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanSubscriberHelper;

$n	= count($mailinglists);

$remote_ip  = Factory::getApplication()->input->server->get('REMOTE_ADDR', '', '');

// we use bs4 css
$wa->useStyle('mod_bwpostman.bwpm_register_bs4');
$wa->useScript('mod_bwpostman.bwpm_register');

if (file_exists(JPATH_BASE . '/' . $css_filename))
{
	$wa->registerAndUseStyle('mod_bwpostman.bwpm_register_custom', Uri::root() . $css_filename);
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

// Size of form fields - 'sm' for small fields
$formclass = 'sm';

// Build the gender select list
$class = $formclass === 'sm' ? 'form-control input-sm' : 'form-control';
$lists['gender'] = BwPostmanSubscriberHelper::buildGenderList('2', 'a_gender', $class, 'm_');

$itemid = BwPostmanSubscriberHelper::getMenuItemid('register');

// We cannot use the same form name and name for the disclaimer checkbox
// because this will not work if the module and the component will be displayed
// on the same page

$required_begin = '<div class="input-group' . ($formclass === "sm" ? ' input-group-sm' : '') . '">';

$required_end = '	<div class="input-group-addon">';
$required_end .= '		<span class="input-group-text"><i class="fa fa-star"></i></span>';
$required_end .= '	</div>';
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
		$wa->useScript('mod_bwpostman.bwpm_register_modal');
		// Show registration form only if a mailinglist is selectable ?>

	<form action="<?php echo Route::_('index.php?option=com_bwpostman&view=register'); ?>" method="post" id="bwp_mod_form"
			name="bwp_mod_form" class="bs3" onsubmit="return checkModRegisterForm();">

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
			<div id="bwp_mod_form_pretext" class="mb"><?php echo $preText; ?></div>
			<?php
		} // End: Show pretext only if set in basic parameters

		// Show formfield gender only if enabled at parameters
		if ($paramsComponent->get('show_gender', '1') == 1)
		{
			?>
			<div class="form-group bwp_mod_form_genderformat">
				<label id="gendermsg_mod"<?php echo $formclass === "sm" ? ' class="small"' : ''; ?>>
					<?php echo Text::_('MOD_BWPOSTMANGENDER'); ?>:
				</label>
				<?php echo $lists['gender']; ?>
			</div>
			<?php
		} // End show gender

		if ($paramsComponent->get('show_firstname_field', '1') OR $paramsComponent->get('firstname_field_obligation', '1'))
		{ // Show firstname-field only if set in basic parameters
			?>
			<div id="bwp_mod_form_firstnamefield" class="form-group">
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
						class="form-control<?php echo $formclass === "sm" ? ' input-sm' : ''; ?>" maxlength="50" />
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
			<div id="bwp_mod_form_namefield" class="form-group">
				<?php // Is filling out the name field obligating
				isset($subscriber->name) ? $sub_name = $subscriber->name : $sub_name = '';
				if ($paramsComponent->get('name_field_obligation', '1') === '1')
				{
	                echo $required_begin;
				}
				?>
				<input type="text" name="a_name" id="a_name"
						placeholder="<?php echo addslashes(Text::_('MOD_BWPOSTMANNAME')); ?>" value="<?php echo $sub_name; ?>"
                        class="form-control<?php echo $formclass === "sm" ? ' input-sm' : ''; ?>" maxlength="50" />
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
		?>
			<div id="bwp_mod_form_specialfield" class="form-group">
				<?php // Is filling out the additional field obligating
				if ($specialObligatory === '1')
				{
	                echo $required_begin;
				}
				?>
				<input type="text" name="a_special" id="a_special"
						placeholder="<?php echo addslashes($specialLabel); ?>" value="<?php echo $sub_special; ?>"
						class="form-control<?php echo $formclass === "sm" ? ' input-sm' : ''; ?>" maxlength="50" />
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

		<div id="bwp_mod_form_emailfield" class="form-group">
			<?php isset($subscriber->email) ? $sub_email = $subscriber->email : $sub_email = '';
	            echo $required_begin;
			?>
			<input type="text" id="a_email" name="email"
					placeholder="<?php echo addslashes(Text::_('MOD_BWPOSTMANEMAIL')); ?>" value="<?php echo $sub_email; ?>"
                    class="form-control<?php echo $formclass === "sm" ? ' input-sm' : ''; ?>" maxlength="100" />
			<?php
                echo $required_end;
			?>
		</div>
		<?php
		if ($paramsComponent->get('show_emailformat', '1') == 1)
		{
			// Show formfield emailformat only if enabled in basic parameters
	        $mailformat_selected = isset($subscriber->emailformat) ? $subscriber->emailformat : $paramsComponent->get('default_emailformat', '1');
			?>
			<div id="bwp_mod_form_emailformat" class="form-group mb-3">
				<label id="emailformatmsg_mod"<?php echo $formclass === "sm" ? ' class="small"' : ''; ?>><?php echo Text::_('MOD_BWPOSTMANEMAILFORMAT'); ?>: </label><br />
			    <div id="edit_mailformat_m" class="btn-group<?php echo $formclass === "sm" ? ' btn-group-sm' : ''; ?>" data-toggle="buttons">
					<label class="btn btn-default<?php echo (!$mailformat_selected ? ' active' : ''); ?>" for="formatTextMod">
						<input type="radio" name="emailformat" id="formatTextMod" value="0"<?php echo (!$mailformat_selected ? ' checked="checked"' : ''); ?> />
						<span>&nbsp;&nbsp;&nbsp;<?php echo Text::_('COM_BWPOSTMAN_TEXT'); ?>&nbsp;&nbsp;&nbsp;</span>
					</label>
					<label class="btn btn-default<?php echo ($mailformat_selected ? ' active' : ''); ?>" for="formatHtmlMod">
						<input type="radio" name="emailformat" id="formatHtmlMod" value="1"<?php echo ($mailformat_selected ? ' checked="checked"' : ''); ?> />
						<span>&nbsp;&nbsp;<?php echo Text::_('COM_BWPOSTMAN_HTML'); ?>&nbsp;&nbsp;</span>
					</label>
				</div>
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
				<input type="checkbox" style="display: none;" id="a_<?php echo "mailinglists0"; ?>" name="<?php echo "mailinglists[]"; ?>"
					title="<?php echo "mailinglists[]"; ?>" value="<?php echo $mailinglists[0]->id; ?>" checked="checked" />
				<?php
				if ($params->get('show_desc', '1') == 1)
				{ ?>
					<div id="bwp_mod_form_lists"<?php echo $formclass === "sm" ? ' class="small"' : ''; ?>>
						<?php echo Text::_('MOD_BWPOSTMANLIST'); ?>
					</div>
					<div class="mailinglist-title mb">
						<?php echo $mailinglist->title; ?>:
							<br /><span class="mailinglist-description-single"><?php
								echo substr(Text::_($mailinglists[0]->description), 0, $descLength);

								if (strlen(Text::_($mailinglists[0]->description)) > $descLength)
								{
									echo '... ';
									echo '<span class="bwptip" title="' . Text::_($mailinglist->description) . '"><i class="fa fa-info-circle fa-lg"></i></span>';
								} ?>
							</span>
					</div>
					<?php
				}
			}
			else
			{ ?>
				<div id="bwp_mod_form_lists"<?php echo $formclass === "sm" ? ' class="small"' : ''; ?>>
					<?php echo Text::_('MOD_BWPOSTMANLISTS') . ' <sup><i class="fa fa-star"></i></sup>'; ?>
				</div>
				<div id="bwp_mod_form_listsfield" class="mb<?php echo $formclass === "sm" ? ' small' : ''; ?>">
				<?php
				foreach ($mailinglists AS $i => $mailinglist)
				{ ?>
					<div class="checkbox a_mailinglist_item_<?php echo $i; ?>">
						<label class="mailinglist-title form-check-label" for="a_<?php echo "mailinglists$i"; ?>">
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
										echo '<span class="bwptip" title="' . Text::_($mailinglist->description) . '"><i class="fa fa-info-circle fa-lg"></i></span>';
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
				<div class="security_question_entry small"><?php echo Text::_('MOD_BWPOSTMANCAPTCHA'); ?></div>
				<div class="security_question_lbl my"><strong><?php echo Text::_($paramsComponent->get('security_question', '')); ?></strong></div>
				<div class="question form-group">
					<div class="input-group<?php echo $formclass === "sm" ? ' input-group-sm' : ''; ?>">
						<input type="text" name="stringQuestion" id="a_stringQuestion" placeholder="<?php echo addslashes(Text::_('MOD_BWPOSTMANCAPTCHA_LABEL')); ?>"
								maxlength="50" class="form-control" /><span class="append-area"><i class="bwp_icon-star"></i></span>
						<div class="input-group-addon">
							<span class="input-group-text"><i class="fa fa-star"></i></span>
						</div>
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
				<div class="security_question_entry small"><?php echo Text::_('MOD_BWPOSTMANCAPTCHA'); ?></div>
				<div class="security_question_lbl my">
					<img src="<?php echo Uri::base(); ?>index.php?option=com_bwpostman&amp;view=register&amp;task=showCaptcha&amp;format=raw&amp;codeCaptcha=<?php echo $codeCaptcha; ?>" alt="captcha" />
				</div>
				<div class="captcha form-group">
					<div class="input-group<?php echo $formclass === "sm" ? ' input-group-sm' : ''; ?>">
						<input type="text" name="stringCaptcha" id="a_stringCaptcha"
							placeholder="<?php echo addslashes(Text::_('MOD_BWPOSTMANCAPTCHA_LABEL')); ?>"
							maxlength="50" class="form-control" />
						<div class="input-group-addon">
							<span class="input-group-text"><i class="fa fa-star"></i></span>
						</div>
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
			<div id="bwp_mod_form_disclaimer" class="form-check my<?php echo $formclass === "sm" ? ' small' : ''; ?>">
				<input type="checkbox" id="agreecheck_mod" class="form-check-input" name="agreecheck_mod" title="<?php echo Text::_('MOD_BWPOSTMAN_DISCLAIMER'); ?>" />
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
					if ($tpl_com !== '' && (Factory::getApplication()->get('sef') === '1' || Factory::getApplication()->get('sef') === true))
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
				<label class="form-check-label">
					<?php
					// Show inside modalbox
					if ($paramsComponent->get('showinmodal', '1') == 1)
					{
						echo '<a id="bwp_mod_open" href="javascript:void(0)"';

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
					echo '>' . Text::_('MOD_BWPOSTMAN_DISCLAIMER') . '</a> <sup><i class="fa fa-star"></i></sup>'; ?>
				</label>
			</div>
			<?php
		} // Show disclaimer ?>

		<div class="mod-button-register text-right">
			<button class="button validate btn btn-default<?php echo $formclass === "sm" ? ' btn-sm' : ''; ?>" type="submit"><?php echo Text::_('MOD_BWPOSTMANBUTTON_REGISTER'); ?>
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

		<p id="bwp_mod_form_required" class="small">(<i class="fa fa-star"></i>) <?php echo Text::_('MOD_BWPOSTMANREQUIRED'); ?></p>
		<div id="bwp_mod_form_editlink" class="text-right">
			<button class="button btn btn-default<?php echo $formclass === "sm" ? ' btn-sm' : ''; ?>" onclick="location.href='<?php
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
			<h4 id="bwp_mod_modal-title">Information</h4>
			<span class="bwp_mod_close">&times;</span>
			<div id="bwp_mod_wrapper"></div>
		</div>
	</div>
</div>
