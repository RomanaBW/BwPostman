<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman edit default template for frontend.
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

$wa->useStyle('com_bwpostman.bwpostman');
$wa->useScript('com_bwpostman.bwpm_register_btn_group');
$wa->useScript('com_bwpostman.bwpm_register_validate');

// Get user defined style file
$templateName = $app->getTemplate();
$css_filename = 'templates/' . $templateName . '/css/com_bwpostman.css';

if (file_exists(JPATH_BASE . '/' . $css_filename))
{
	$wa->registerAndUseStyle('customCss', Uri::root() . $css_filename);
}

HtmlHelper::_('behavior.formvalidator');

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

<div id="bwpostman" class="mt">
	<div id="bwp_com_edit_subscription">
		<?php if (($this->params->get('show_page_heading', '0') != 0) && ($this->params->get('page_heading', '') != '')) : ?>
			<h1 class="componentheading<?php echo $this->params->get('pageclass_sfx', ''); ?>">
				<?php echo $this->escape($this->params->get('page_heading', '')); ?>
			</h1>
		<?php endif; ?>

		<div class="content_inner">
			<form action="<?php echo Route::_('index.php?option=com_bwpostman'); ?>" method="post" id="bwp_com_form"
					name="bwp_com_form" class="form-validate form-inline">

				<div class="contentpane<?php echo $this->params->get('pageclass_sfx', ''); ?>">

					<?php // Show pretext only if set in basic parameters
					if ($this->params->get('pretext', ''))
					{
						$preText = Text::_($this->params->get('pretext', ''));
						?>
						<p class="pre_text"><?php echo $preText; ?></p>
						<?php
					} // End: Show pretext only if set in basic parameters ?>

					<?php // Show formfield gender only if enabled in basic parameters
					if ($this->params->get('show_gender', '1') == 1)
					{ ?>
						<p class="edit_gender">
							<label id="gendermsg"> <?php echo Text::_('COM_BWPOSTMAN_GENDER'); ?>:</label>
							<?php echo $this->lists['gender']; ?>
						</p> <?php
					} // End gender ?>

					<?php // Show first name-field only if set in basic parameters
					if ($this->params->get('show_firstname_field', '1') || $this->params->get('firstname_field_obligation', '1'))
					{ ?>
						<p class="user_firstname input<?php echo ($this->params->get('firstname_field_obligation', '1')) ? '-append' : '' ?>">
							<label id="firstnamemsg" for="firstname">
								<?php echo Text::_('COM_BWPOSTMAN_FIRSTNAME'); ?>: </label>
							<?php // Is filling out the firstname field obligating
							if ($this->params->get('firstname_field_obligation', '1'))
							{ ?>
								<input type="text" name="firstname" id="firstname" size="40"
										value="<?php
										if (!empty($this->subscriber->firstname))
										{
											echo $this->subscriber->firstname;
										} ?>"
										class="<?php
										if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1))
										{
											echo "invalid";
										} ?>"
										maxlength="50" /><span class="append-area"><i class="icon-star"></i></span>
							<?php
							}
							else
							{ ?>
								<input type="text" name="firstname" id="firstname" size="40"
										value="<?php echo $this->subscriber->firstname; ?>"
										class="<?php
										if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1))
										{
											echo "invalid";
										} ?>"
										maxlength="50" />
							<?php
							}

							// End: Is filling out the firstname field obligating
							?>
						</p> <?php
					}

					// End: Show first name-field only if set in basic parameters ?>

					<?php // Show name-field only if set in basic parameters
					if ($this->params->get('show_name_field', '1') || $this->params->get('name_field_obligation', '1'))
					{ ?>
						<p class="user_name edit_name input<?php echo ($this->params->get('name_field_obligation', '1')) ? '-append' : '' ?>">
							<label id="namemsg" for="name"
								<?php
								if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1))
								{
									echo "class=\"invalid\"";
								} ?>>
								<?php echo Text::_('COM_BWPOSTMAN_NAME'); ?>: </label>
							<?php // Is filling out the name field obligating
							if ($this->params->get('name_field_obligation', '1'))
							{
								?>
								<input type="text" name="name" id="name" size="40" value="<?php echo $this->subscriber->name; ?>"
										class="<?php
										if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1))
										{
											echo "invalid";
										} ?>"
										maxlength="50" /><span class="append-area"><i class="icon-star"></i></span> <?php
							}
							else
							{ ?>
								<input type="text" name="name" id="name" size="40" value="<?php echo $this->subscriber->name; ?>"
									class="<?php
									if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1)) {
										echo "invalid";
									} ?>"
									maxlength="50" /> <?php
							}

							// End: Is filling out the name field obligating
							?>
						</p> <?php
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

						<p class="edit_special input<?php echo ($this->params->get('special_field_obligation', '0')) ? '-append' : '' ?>">
							<label id="specialmsg" class="hasTooltip" title="<?php echo $tip; ?>" for="special"
								<?php
								if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1))
								{
									echo " class=\"invalid\"";
								}
								echo ">";
								if ($this->params->get('special_label', '') != '')
								{
									echo Text::_($this->params->get('special_label', ''));
								}
								else
								{
									echo Text::_('COM_BWPOSTMAN_SPECIAL');
								}
								?>>:
							</label>
							<?php // Is filling out the special field obligating
							if ($this->params->get('special_field_obligation', '0'))
							{ ?>
								<input type="text" name="special" id="special" size="40" value="<?php echo $this->subscriber->special; ?>"
										class="<?php
										if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1))
										{
											echo "invalid";
										} ?>"
										maxlength="50" /><span class="append-area"><i class="icon-star"></i></span> <?php
							}
							else
							{ ?>
								<input type="text" name="special" id="special" size="40" value="<?php echo $this->subscriber->special; ?>"
										class="<?php
										if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code == 1))
										{
											echo "invalid";
										} ?>"
										maxlength="50" /> <?php
							}

							// End: Is filling out the special field obligating
							?>
						</p> <?php
					} // End: Show special field only if set in basic parameters ?>

					<p class="user_email edit_email input-append">
						<label id="emailmsg" for="email"
							<?php
							if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code != 1))
							{
								echo "class=\"invalid\"";
							} ?>>
							<?php echo Text::_('COM_BWPOSTMAN_EMAIL'); ?>:
						</label>
						<input type="text" id="email" name="email" size="40" value="<?php echo $this->subscriber->email; ?>"
							class="<?php
							if ((!empty($this->subscriber->err_code)) && ($this->subscriber->err_code != 1))
							{
								echo "invalid";
							}
							else
							{
								echo "validate-email";
							} ?>"
							maxlength="100" /><span class="append-area"><i class="icon-star"></i></span>
					</p>

					<?php
					// Show formfield email format only if enabled in basic parameters
					if ($this->params->get('show_emailformat', '1') == 1)
					{ ?>
						<div class="user_mailformat edit_emailformat">
							<label id="emailformatmsg"><?php echo Text::_('COM_BWPOSTMAN_EMAILFORMAT'); ?>: </label>
							<?php echo $this->lists['emailformat']; ?>
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
						<div class="maindivider<?php echo $this->params->get('pageclass_sfx', ''); ?>"></div>

						<div class="contentpane<?php echo $this->params->get('pageclass_sfx', ''); ?>">
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
										<p class="mail_available">
											<?php echo Text::_('COM_BWPOSTMAN_MAILINGLIST'); ?>
										</p>
										<p class="mailinglist-description-single">
											<span class="mail_available_list_title">
												<?php echo $this->lists['available_mailinglists'][0]->title . ": "; ?>
											</span>
											<?php
											echo substr(Text::_($this->lists['available_mailinglists'][0]->description), 0, $descLength);

											if (strlen(Text::_($this->lists['available_mailinglists'][0]->description)) > $descLength)
											{
												echo '... ';
												echo '<span class="bwptip" title="' . Text::_($this->lists['available_mailinglists'][0]->description) . '"><i class="icon-info-sign"></i></span>';
											} ?>
										</p>
										<?php
									}
								}
								else
								{ ?>
									<p class="mail_available">
										<?php echo Text::_('COM_BWPOSTMAN_MAILINGLISTS') . ' <sup><i class="icon-star"></i></sup>'; ?>
									</p>
									<?php
									foreach ($this->lists['available_mailinglists'] as $i => $item)
									{ ?>
										<p class="mail_available_list <?php echo "mailinglists$i"; ?>">
											<input title="mailinglists_array" type="checkbox" id="<?php echo "mailinglists$i"; ?>"
													name="<?php echo "mailinglists[]"; ?>" value="<?php echo $item->id; ?>"
											<?php
											if ((is_array($this->subscriber->mailinglists)) && (in_array((int) $item->id,
													$this->subscriber->mailinglists)))
											{
												echo "checked=\"checked\"";
											} ?> />
											<span class="mail_available_list_title">
												<?php echo $this->params->get('show_desc', '1') == 1 ? $item->title . ": " : $item->title; ?>
											</span>
											<?php
											if ($this->params->get('show_desc', '1') == 1)
											{ ?>
											<span>
												<?php
												echo substr(Text::_($item->description), 0, $descLength);
												if (strlen(Text::_($item->description)) > $descLength)
												{
													echo '... ';
													echo '<span class="bwptip" title="' . Text::_($item->description) . '"><i class="icon-info-sign"></i></span>';
												} ?>
											</span>
											<?php
											} ?>
										</p>
										<?php
									} ?>
									<div class="maindivider<?php echo $this->params->get('pageclass_sfx', ''); ?>"></div>
									<?php
								}
							}?>
						</div>

						<?php
					}

					// End Mailinglists ?>

				</div>

				<div class="contentpane<?php echo $this->escape($this->params->get('pageclass_sfx', '')); ?>">
					<p class="edit_unsubscribe">
						<input title="unsubscribe" type="checkbox" id="unsubscribe" name="unsubscribe" value="1" />
						<span class="edit_unsubscribe_text"><?php echo Text::_('COM_BWPOSTMAN_UNSUBSCRIBE') ?></span>
					</p>
				</div>

				<div class="maindivider<?php echo $this->escape($this->params->get('pageclass_sfx', '')); ?>"></div>

				<div class="w-100 contentpane<?php echo $this->escape($this->params->get('pageclass_sfx', '')); ?>">
					<p class="edit_required">
						<?php echo Text::_('COM_BWPOSTMAN_REQUIRED'); ?>
					</p>
				</div>

				<button class="button validate save btn" type="button" onclick="return submitbutton('submit');">
					<?php echo Text::_('COM_BWPOSTMAN_BUTTON_EDIT'); ?>
				</button>
				<button class="button validate leave btn ml-2" type="button" onclick="return submitbutton('submitleave');">
					<?php echo Text::_('COM_BWPOSTMAN_BUTTON_LEAVEEDIT'); ?>
				</button>

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
				<p class="bwpm_copyright"><?php echo BwPostmanSite::footer(); ?></p>
			<?php
			} ?>
		</div>
	</div>
</div>
