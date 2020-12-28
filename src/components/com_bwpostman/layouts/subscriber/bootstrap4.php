<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman subscriber data fields layout.
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

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Layout\LayoutHelper;

$subscriber = $displayData['subscriber'];
$params     = ComponentHelper::getParams('com_bwpostman', true);
$lists      = $displayData['lists'];

$formclass	= $params->get('formclass');
?>

<div class="contentpane<?php echo $params->get('pageclass_sfx'); ?>">
	<?php // Show pretext only if set in basic parameters
	if ($params->get('pretext'))
	{
		$preText = Text::_($params->get('pretext'));
		?>
		<div class="pre_text mb-3"><?php echo $preText; ?></div>
		<?php
	} // End: Show pretext only if set in basic parameters ?>

	<?php // Show editlink only if the user is not logged in
	if (Factory::getApplication()->input->get('view') !== 'edit')
	{
		$link = Uri::base() . 'index.php?option=com_bwpostman&view=edit';
		?>
		<div class="user_edit mb-3">
			<a href="<?php echo $link; ?>">
				<?php echo Text::_('COM_BWPOSTMAN_LINK_TO_EDITLINKFORM'); ?>
			</a>
		</div><?php
	}

	// End: Show editlink only if the user is not logged in ?>

	<?php // Show formfield gender only if enabled in basic parameters
	if ($params->get('show_gender') == 1)
	{
        $gender_selected = isset($subscriber->gender) ? $subscriber->gender : 0;
		$class = $formclass === 'sm' ? 'form-control form-control-sm' : 'form-control';
    ?>
		<div class="form-group row">
			<label id="gendermsg" class="col-sm-3 col-form-label" for="gender"> <?php echo Text::_('COM_BWPOSTMAN_GENDER'); ?>:</label>
            <div class="col-sm-9">
			<?php
				echo LayoutHelper::render(
					'gender_bs4',
					array('gender_selected' => $gender_selected, 'class' => $class),
					$basePath = JPATH_COMPONENT . '/layouts/subscriber'
				);
			?>
            </div>
		</div>
	<?php
	} // End gender ?>

	<?php // Show first name-field only if set in basic parameters
	if ($params->get('show_firstname_field') || $params->get('firstname_field_obligation'))
	{ ?>
		<div class="form-group row user_firstname">
			<label id="firstnamemsg" class="col-sm-3 col-form-label" for="firstname">
				<?php echo Text::_('COM_BWPOSTMAN_FIRSTNAME'); ?>: </label>
			<?php // Is filling out the firstname field obligating
			if ($params->get('firstname_field_obligation'))
			{ ?>
	            <div class="col-sm-9">
	                <div class="input-group<?php echo $formclass === "sm" ? ' input-group-sm' : ''; ?>">
						<input type="text" name="firstname" id="firstname" size="40"
							value="<?php echo $subscriber->firstname; ?>"
							class="form-control" maxlength="50" />
						<div class="input-group-append">
							<span class="input-group-text"><i class="fa fa-star"></i></span>
						</div>
					</div>
	            </div>
			<?php
			}
			else
			{ ?>
	            <div class="col-sm-9">
					<input type="text" name="firstname" id="firstname" size="40"
							class="form-control<?php echo $formclass === "sm" ? ' form-control-sm' : ''; ?>"
							value="<?php echo $subscriber->firstname; ?>" maxlength="50" />
	            </div>
			<?php
			}

			// End: Is filling out the firstname field obligating
			?>
		</div> <?php
	}

	// End: Show first name-field only if set in basic parameters ?>


	<?php // Show name-field only if set in basic parameters
	if ($params->get('show_name_field') || $params->get('name_field_obligation'))
	{ ?>
		<div class="form-group row user_name edit_name">
			<label id="namemsg" class="col-sm-3 col-form-label" for="name">
				<?php echo Text::_('COM_BWPOSTMAN_NAME'); ?>: </label>
			<?php // Is filling out the name field obligating
			if ($params->get('name_field_obligation'))
			{ ?>
	            <div class="col-sm-9">
	                <div class="input-group<?php echo $formclass === "sm" ? ' input-group-sm' : ''; ?>">
						<input type="text" name="name" id="name" size="40"
							value="<?php echo $subscriber->name; ?>"
							class="form-control" maxlength="50" />
						<div class="input-group-append">
							<span class="input-group-text"><i class="fa fa-star"></i></span>
						</div>
					</div>
	            </div>
			<?php
			}
			else
			{ ?>
	            <div class="col-sm-9">
					<input type="text" name="name" id="name" size="40"
						class="form-control<?php echo $formclass === "sm" ? ' form-control-sm' : ''; ?>"
						value="<?php echo $subscriber->name; ?>" maxlength="50" />
	            </div>
			<?php
			}

			// End: Is filling out the name field obligating
			?>
		</div> <?php
	}

	// End: Show name-fields only if set in basic parameters ?>

	<?php // Show special only if set in basic parameters or required
	if ($params->get('show_special') || $params->get('special_field_obligation'))
	{
		if ($params->get('special_desc') != '')
		{
			$tip = Text::_($params->get('special_desc'));
		}
		else
		{
			$tip = Text::_('COM_BWPOSTMAN_SPECIAL');
		} ?>

		<div class="form-group row edit_special">
			<label id="specialmsg" class="col-sm-3 col-form-label hasTooltip" title="<?php echo HtmlHelper::tooltipText($tip); ?>" for="special">
				<?php
				if ($params->get('special_label') != '')
				{
					echo Text::_($params->get('special_label'));
				}
				else
				{
					echo Text::_('COM_BWPOSTMAN_SPECIAL');
				}
				?>:
			</label>
			<?php // Is filling out the special field obligating
			if ($params->get('special_field_obligation'))
			{ ?>
	            <div class="col-sm-9">
	                <div class="input-group<?php echo $formclass === "sm" ? ' input-group-sm' : ''; ?>">
						<input type="text" name="special" id="special" size="40" value="<?php echo $subscriber->special; ?>"
							class="form-control" maxlength="50" />
						<div class="input-group-append">
							<span class="input-group-text"><i class="fa fa-star"></i></span>
						</div>
					</div>
	            </div>
			<?php
			}
			else
			{ ?>
	            <div class="col-sm-9">
					<input type="text" name="special" id="special" size="40"
						class="form-control<?php echo $formclass === "sm" ? ' form-control-sm' : ''; ?>"
						value="<?php echo $subscriber->special; ?>" maxlength="50" />
	            </div>
			<?php
			}

			// End: Is filling out the special field obligating
			?>
		</div> <?php
	} // End: Show special field only if set in basic parameters ?>


	<div class="form-group row user_email edit_email">
		<label id="emailmsg" class="col-sm-3 col-form-label" for="email">
			<?php echo Text::_('COM_BWPOSTMAN_EMAIL'); ?>:
		</label>
	    <div class="col-sm-9">
			<div class="input-group<?php echo $formclass === "sm" ? ' input-group-sm' : ''; ?>">
				<input type="text" id="email" name="email" size="40" value="<?php echo $subscriber->email; ?>"
					class="form-control" maxlength="50" />
				<div class="input-group-append">
					<span class="input-group-text"><i class="fa fa-star"></i></span>
				</div>
			</div>
		</div>
	</div>
	<?php
	// Show formfield email format only if enabled in basic parameters
	if ($params->get('show_emailformat') == 1)
	{
        $mailformat_selected = isset($subscriber->emailformat) ? $subscriber->emailformat : $params->get('default_emailformat');
	?>
		<div class="form-group row user_mailformat edit_emailformat">
			<label id="emailformatmsg" class="col-sm-6 col-md-4 col-form-label"> <?php echo Text::_('COM_BWPOSTMAN_EMAILFORMAT'); ?>: </label>
		    <div class="col-sm-6">
			<?php
				echo LayoutHelper::render(
					'emailformat_bs4',
					array('mailformat_selected' => $mailformat_selected, 'formclass' => $formclass),
					$basePath = JPATH_COMPONENT . '/layouts/subscriber'
				);
			?>
			</div>
		</div>
	<?php
	}
	else
	{
		// hidden field with the default email format
		?>
		<input type="hidden" name="emailformat" value="<?php echo $params->get('default_emailformat'); ?>" />
	<?php
	}

	// End email format
	?>

	<?php
	// Show available mailinglists
	if ($lists['available_mailinglists'])
	{ ?>
		<div class="lists my-4<?php echo $params->get('pageclass_sfx'); ?>">
			<?php
			$n = count($lists['available_mailinglists']);

			$descLength = $params->get('desc_length');

			if ($lists['available_mailinglists'] && ($n > 0))
			{
				if ($n == 1)
				{ ?>
					<input title="mailinglists_array" type="checkbox" style="display: none;" id="<?php echo "mailinglists0"; ?>"
							name="<?php echo "mailinglists[]"; ?>" value="<?php echo $lists['available_mailinglists'][0]->id; ?>" checked="checked" />
					<?php
					if ($params->get('show_desc') == 1)
					{ ?>
						<div class="mail_available mb-2">
							<?php echo Text::_('COM_BWPOSTMAN_MAILINGLIST'); ?>
						</div>
						<div class="mailinglist-description-single">
							<span class="mail_available_list_title">
								<?php echo $lists['available_mailinglists'][0]->title . ": "; ?>
							</span><br />
							<?php
							echo substr(Text::_($lists['available_mailinglists'][0]->description), 0, $descLength);

							if (strlen(Text::_($lists['available_mailinglists'][0]->description)) > $descLength)
							{
								echo '... ';
								echo HtmlHelper::tooltip(Text::_($lists['available_mailinglists'][0]->description),
									$lists['available_mailinglists'][0]->title, '', '<i class="fa fa-info-circle fa-lg"></i>', '');
							} ?>
						</div>
						<?php
					}
				}
				else
				{ ?>
					<div class="mail_available mb-2">
						<?php echo Text::_('COM_BWPOSTMAN_MAILINGLISTS') . ' <sup><i class="fa fa-star"></i></sup>'; ?>
					</div>
					<?php
					foreach ($lists['available_mailinglists'] as $i => $item)
					{ ?>
						<div class="form-check mail_available_list <?php echo "mailinglists$i"; ?>">
							<input class="form-check-input" title="mailinglists_array" type="checkbox" id="<?php echo "mailinglists$i"; ?>"
									name="<?php echo "mailinglists[]"; ?>" value="<?php echo $item->id; ?>"
							<?php
							if ((is_array($subscriber->mailinglists)) && (in_array((int) $item->id,
									$subscriber->mailinglists)))
							{
								echo "checked=\"checked\"";
							} ?> />
                            <label class="form-check-label" for="<?php echo "mailinglists$i"; ?>">
								<span class="mail_available_list_title">
									<?php echo $params->get('show_desc') == 1 ? $item->title . ": " : $item->title; ?>
								</span><br />
								<?php
								if ($params->get('show_desc') == 1)
								{ ?>
								<span>
									<?php
									echo substr(Text::_($item->description), 0, $descLength);
									if (strlen(Text::_($item->description)) > $descLength)
									{
										echo '... ';
										echo HtmlHelper::tooltip(Text::_($item->description), $item->title, '', '<i class="fa fa-info-circle fa-lg"></i>', '');
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
