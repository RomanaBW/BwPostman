<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman edit bootstrap 2 template for frontend.
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

Text::script('COM_BWPOSTMAN_ERROR_NAME');
Text::script('COM_BWPOSTMAN_ERROR_FIRSTNAME');
Text::script("COM_BWPOSTMAN_SUB_ERROR_SPECIAL");
Text::script('COM_BWPOSTMAN_ERROR_EMAIL');
Text::script('COM_BWPOSTMAN_ERROR_NL_CHECK');

// Get provided style file
$app = Factory::getApplication();
$wa  = $app->getDocument()->getWebAssetManager();

$wa->useStyle('com_bwpostman.bwpostman_bs2');
$wa->useScript('com_bwpostman.bwpm_register_validate');

// Get user defined style file
$templateName = $app->getTemplate();
$css_filename = 'templates/' . $templateName . '/css/com_bwpostman.css';

if (file_exists(JPATH_BASE . '/' . $css_filename))
{
	$wa->registerAndUseStyle('customCss', Uri::root() . $css_filename);
}

HtmlHelper::_('behavior.formvalidator');

$formclass	= ''; // '' = default inputs or 'sm' = smaller Inputs
?>

<noscript>
	<div id="system-message">
		<div class="alert alert-warning">
			<h4 class="alert-heading"><?php echo Text::_('WARNING'); ?></h4>
			<div>
				<p><?php echo Text::_('COM_BWPOSTMAN_JAVASCRIPTWARNING'); ?></p>
			</div>
		</div>
	</div>
</noscript>

<div id="bwpostman" class="mt-3">
	<div id="bwp_com_edit_subscription">
		<?php if (($this->params->get('show_page_heading', '0') != 0) && ($this->params->get('page_heading', '') != '')) : ?>
			<h1 class="componentheading<?php echo $this->params->get('pageclass_sfx', ''); ?>">
				<?php echo $this->escape($this->params->get('page_heading', '')); ?>
			</h1>
		<?php endif; ?>

		<div class="content_inner">
			<form action="<?php echo Route::_('index.php?option=com_bwpostman'); ?>" method="post" id="bwp_com_form"
					name="bwp_com_form" class="form-validate form-horizontal">

				<div class="contentpane mb-3<?php echo $this->params->get('pageclass_sfx', ''); ?>">
					<?php // Show pretext only if set in basic parameters
					if ($this->params->get('pretext', ''))
					{
						$preText = Text::_($this->params->get('pretext', ''));
						?>
						<div class="pre_text mb-3"><?php echo $preText; ?></div>
						<?php
					} // End: Show pretext only if set in basic parameters ?>

					<?php // Show formfield gender only if enabled in basic parameters
					if ($this->params->get('show_gender', '1') == 1)
					{
				        $gender_selected = isset($this->subscriber->gender) ? $this->subscriber->gender : '2';
						$class = $formclass === 'sm' ? ' class="input-small"' : ' class="input-medium"';
				    ?>
						<div class="control-group">
							<label id="gendermsg" class="control-label" for="gender"> <?php echo Text::_('COM_BWPOSTMAN_GENDER'); ?>:</label>
				            <div class="controls">
								<select id="gender"<?php echo $class; ?> name="gender">
									<option value="2"<?php echo $gender_selected == '2' ? ' selected="selected"' : ''; ?>>
							            <?php echo Text::_('COM_BWPOSTMAN_NO_GENDER'); ?>
									</option>
									<option value="0"<?php echo $gender_selected == '0' ? ' selected="selected"' : ''; ?>>
							            <?php echo Text::_('COM_BWPOSTMAN_MALE'); ?>
									</option>
									<option value="1"<?php echo $gender_selected == '1' ? ' selected="selected"' : ''; ?>>
							            <?php echo Text::_('COM_BWPOSTMAN_FEMALE'); ?>
									</option>
								</select>
				            </div>
						</div>
					<?php
					} // End gender ?>

					<?php // Show first name-field only if set in basic parameters
					if ($this->params->get('show_firstname_field', '1') || $this->params->get('firstname_field_obligation', '1'))
					{ ?>
						<div class="control-group user_firstname">
							<label id="firstnamemsg" class="control-label" for="firstname">
								<?php echo Text::_('COM_BWPOSTMAN_FIRSTNAME'); ?>: </label>
							<?php // Is filling out the firstname field obligating
							if ($this->params->get('firstname_field_obligation', '1'))
							{ ?>
					            <div class="controls">
					                <div class="input-append">
										<input type="text" name="firstname" id="firstname" size="40"
											value="<?php echo $this->subscriber->firstname; ?>"
											class="<?php echo $formclass === "sm" ? 'input-small' : 'input-medium'; ?>" maxlength="50" />
										<span class="add-on"><i class="icon-star"></i></span>
									</div>
					            </div>
							<?php
							}
							else
							{ ?>
					            <div class="controls">
									<input type="text" name="firstname" id="firstname" size="40"
											class="<?php echo $formclass === "sm" ? 'input-small' : 'input-medium'; ?>"
											value="<?php echo $this->subscriber->firstname; ?>" maxlength="50" />
					            </div>
							<?php
							}

							// End: Is filling out the firstname field obligating
							?>
						</div> <?php
					}

					// End: Show first name-field only if set in basic parameters ?>


					<?php // Show name-field only if set in basic parameters
					if ($this->params->get('show_name_field', '1') || $this->params->get('name_field_obligation', '1'))
					{ ?>
						<div class="control-group user_name edit_name">
							<label id="namemsg" class="control-label" for="name">
								<?php echo Text::_('COM_BWPOSTMAN_NAME'); ?>: </label>
							<?php // Is filling out the name field obligating
							if ($this->params->get('name_field_obligation', '1'))
							{ ?>
					            <div class="controls">
					                <div class="input-append">
										<input type="text" name="name" id="name" size="40"
											value="<?php echo $this->subscriber->name; ?>"
											class="<?php echo $formclass === "sm" ? 'input-small' : 'input-medium'; ?>" maxlength="50" />
										<span class="add-on"><i class="icon-star"></i></span>
									</div>
					            </div>
							<?php
							}
							else
							{ ?>
					            <div class="controls">
									<input type="text" name="name" id="name" size="40"
										class="<?php echo $formclass === "sm" ? 'input-small' : 'input-medium'; ?>"
										value="<?php echo $this->subscriber->name; ?>" maxlength="50" />
					            </div>
							<?php
							}

							// End: Is filling out the name field obligating
							?>
						</div> <?php
					}

					// End: Show name-fields only if set in basic parameters ?>

					<?php // Show special only if set in basic parameters or required
					if ($this->params->get('show_special', '1') || $this->params->get('special_field_obligation', '0'))
					{
						if ($this->params->get('special_desc', '') != '')
						{
							$tip = Text::_($this->params->get('special_desc', ''));
						}
						else
						{
							$tip = Text::_('COM_BWPOSTMAN_SPECIAL');
						} ?>

						<div class="control-group edit_special">
							<label id="specialmsg" class="control-label hasTooltip" title="<?php echo HtmlHelper::tooltipText($tip); ?>" for="special">
								<?php
								if ($this->params->get('special_label', '') != '')
								{
									echo Text::_($this->params->get('special_label', ''));
								}
								else
								{
									echo Text::_('COM_BWPOSTMAN_SPECIAL');
								}
								?>:
							</label>
							<?php // Is filling out the special field obligating
							if ($this->params->get('special_field_obligation', '0'))
							{ ?>
					            <div class="controls">
					                <div class="input-append">
										<input type="text" name="special" id="special" size="40" value="<?php echo $this->subscriber->special; ?>"
											class="<?php echo $formclass === "sm" ? 'input-small' : 'input-medium'; ?>" maxlength="50" />
										<span class="add-on"><i class="icon-star"></i></span>
									</div>
					            </div>
							<?php
							}
							else
							{ ?>
					            <div class="controls">
									<input type="text" name="special" id="special" size="40"
										class="<?php echo $formclass === "sm" ? 'input-small' : 'input-medium'; ?>"
										value="<?php echo $this->subscriber->special; ?>" maxlength="50" />
					            </div>
							<?php
							}

							// End: Is filling out the special field obligating
							?>
						</div> <?php
					} // End: Show special field only if set in basic parameters ?>


					<div class="control-group user_email edit_email">
						<label id="emailmsg" class="control-label" for="email">
							<?php echo Text::_('COM_BWPOSTMAN_EMAIL'); ?>:
						</label>
					    <div class="controls">
							<div class="input-append">
								<input type="text" id="email" name="email" size="40" value="<?php echo $this->subscriber->email; ?>"
									class="<?php echo $formclass === "sm" ? 'input-small' : 'input-medium'; ?>" maxlength="50" />
								<span class="add-on"><i class="icon-star"></i></span>
							</div>
						</div>
					</div>
					<?php
					// Show formfield email format only if enabled in basic parameters
					if ($this->params->get('show_emailformat', '1') == 1)
					{
				        $mailformat_selected = isset($this->subscriber->emailformat) ? $this->subscriber->emailformat : $this->params->get('default_emailformat', '1');
					?>
						<div class="control-group user_mailformat edit_emailformat">
							<label id="emailformatmsg" class="control-label"> <?php echo Text::_('COM_BWPOSTMAN_EMAILFORMAT'); ?>: </label>
						    <div class="controls">
								<div id="edit_mailformat" class="btn-group" data-toggle="buttons-radio">
									<label class="btn<?php echo $formclass === "sm" ? ' btn-small' : ''; ?><?php echo (!$mailformat_selected ? ' active' : ''); ?>" for="formatText">
										<input type="radio" name="emailformat" id="formatText" value="0"<?php echo (!$mailformat_selected ? ' checked="checked"' : ''); ?> />
										<span>&nbsp;&nbsp;&nbsp;<?php echo Text::_('COM_BWPOSTMAN_TEXT'); ?>&nbsp;&nbsp;&nbsp;</span>
									</label>
									<label class="btn<?php echo $formclass === "sm" ? ' btn-small' : ''; ?><?php echo ($mailformat_selected ? ' active' : ''); ?>" for="formatHtml">
										<input type="radio" name="emailformat" id="formatHtml" value="1"<?php echo ($mailformat_selected ? ' checked="checked"' : ''); ?> />
										<span>&nbsp;&nbsp;<?php echo Text::_('COM_BWPOSTMAN_HTML'); ?>&nbsp;&nbsp;</span>
									</label>
								</div>
							</div>
						</div>
					<?php
					}
					else
					{
						// hidden field with the default email format
						?>
						<input type="hidden" name="emailformat" value="<?php echo $this->params->get('default_emailformat', '1'); ?>" />
					<?php
					}

					// End email format
					?>

					<?php
					// Show available mailinglists
					if ($this->lists['available_mailinglists'])
					{ ?>
						<div class="lists <?php echo $this->params->get('pageclass_sfx', ''); ?>">
							<?php
							$n = count($this->lists['available_mailinglists']);

							$descLength = $this->params->get('desc_length', '150');

							if ($this->lists['available_mailinglists'] && ($n > 0))
							{
								if ($n == 1)
								{ ?>
									<input title="mailinglists_array" type="checkbox" style="display: none;" id="<?php echo "mailinglists0"; ?>"
											name="<?php echo "mailinglists[]"; ?>" value="<?php echo $this->lists['available_mailinglists'][0]->id; ?>" checked="checked" />
									<?php
									if ($this->params->get('show_desc', '1') == 1)
									{ ?>
										<p class="mail_available strong">
											<?php echo Text::_('COM_BWPOSTMAN_MAILINGLIST'); ?>
										</p>
										<div class="mailinglist-description-single">
											<span class="mail_available_list_title">
												<?php echo $this->lists['available_mailinglists'][0]->title . ": "; ?>
											</span><br />
											<?php
											echo substr(Text::_($this->lists['available_mailinglists'][0]->description), 0, $descLength);

											if (strlen(Text::_($this->lists['available_mailinglists'][0]->description)) > $descLength)
											{
												echo '... ';
												echo '<span class="bwptip" title="' . Text::_($this->lists['available_mailinglists'][0]->description) . '"><i class="icon-info"></i></span>';
											} ?>
										</div>
										<?php
									}
								}
								else
								{ ?>
									<p class="mail_available strong">
										<?php echo Text::_('COM_BWPOSTMAN_MAILINGLISTS') . ' <sup><i class="icon-star"></i></sup>'; ?>
									</p>
									<?php
									foreach ($this->lists['available_mailinglists'] as $i => $item)
									{ ?>
										<div class="mail_available_list <?php echo "mailinglists$i"; ?>">
				                            <label class="checkbox" for="<?php echo "mailinglists$i"; ?>">
												<input class="" title="mailinglists_array" type="checkbox" id="<?php echo "mailinglists$i"; ?>"
														name="<?php echo "mailinglists[]"; ?>" value="<?php echo $item->id; ?>"
												<?php
												if ((is_array($this->subscriber->mailinglists)) && (in_array((int) $item->id,
														$this->subscriber->mailinglists)))
												{
													echo "checked=\"checked\"";
												} ?> />
												<span class="mail_available_list_title">
													<?php echo $this->params->get('show_desc', '1') == 1 ? $item->title . ": " : $item->title; ?>
												</span><br />
												<?php
												if ($this->params->get('show_desc', '1') == 1)
												{ ?>
												<span>
													<?php
													echo substr(Text::_($item->description), 0, $descLength);
													if (strlen(Text::_($item->description)) > $descLength)
													{
														echo '... ';
														echo '<span class="bwptip" title="' . Text::_($item->description) . '"><i class="icon-info"></i></span>';
													} ?>
												</span>
												<?php
												} ?>
				                            </label>
										</div>
										<?php
									}
								}
							}?>
						</div>

						<?php
					}

					// End Mailinglists ?>

				</div>

				<div class="well well-small<?php echo $this->escape($this->params->get('pageclass_sfx', '')); ?>">
					<div class="edit_unsubscribe">
						<label class="checkbox edit_unsubscribe_text text-error">
							<input title="unsubscribe" type="checkbox" id="unsubscribe" class="form-check-input" name="unsubscribe" value="1" />
							<?php echo Text::_('COM_BWPOSTMAN_UNSUBSCRIBE') ?>
						</label>
					</div>
				</div>

				<div class="buttons my-3">
					<button class="button validate save btn mb-2" type="button" onclick="return submitbutton('submit');">
						<?php echo Text::_('COM_BWPOSTMAN_BUTTON_EDIT'); ?>
					</button>
					<button class="button validate leave btn mb-2" type="button" onclick="return submitbutton('submitleave');">
						<?php echo Text::_('COM_BWPOSTMAN_BUTTON_LEAVEEDIT'); ?>
					</button>
				</div>

				<div class="edit-required small">
					<?php echo Text::_('COM_BWPOSTMAN_REQUIRED'); ?>
				</div>

				<input type="hidden" name="option" value="com_bwpostman" />
				<input type="hidden" name="task" value="save" />
				<input type="hidden" name="view" value="edit" />
				<input type="hidden" name="edit" value="" />
				<input type="hidden" name="id" value="<?php echo $this->subscriber->id; ?>" />
				<input type="hidden" name="name_field_obligation" value="<?php echo $this->params->get('name_field_obligation', '1'); ?>" />
				<input type="hidden" name="firstname_field_obligation" value="<?php echo $this->params->get('firstname_field_obligation', '1'); ?>" />
				<input type="hidden" name="special_field_obligation" value="<?php echo $this->params->get('special_field_obligation', '0'); ?>" />
				<input type="hidden" name="show_name_field" value="<?php echo $this->params->get('show_name_field', '1'); ?>" />
				<input type="hidden" name="show_firstname_field" value="<?php echo $this->params->get('show_firstname_field', '1'); ?>" />
				<input type="hidden" name="show_special" value="<?php echo $this->params->get('show_special', '1'); ?>" />
				<?php // Is filling out the special field obligating
				if ($this->params->get('show_special', '1') || $this->params->get('special_field_obligation', '0'))
				{ ?>
					<input type="hidden" name="special_label" value="<?php echo $this->params->get('special_label', ''); ?>" />
				<?php
				} ?>
				<?php echo HtmlHelper::_('form.token'); ?>
			</form>

			<?php
			if ($this->params->get('show_boldt_link', '1') === '1')
			{ ?>
				<p class="bwpm_copyright text-center my-3"><?php echo BwPostmanSite::footer(); ?></p>
			<?php
			} ?>
		</div>
	</div>
</div>
