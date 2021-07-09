<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman subscriber data fields layout.
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

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;

$subscriber = $displayData['subscriber'];
$params     = ComponentHelper::getParams('com_bwpostman', true);
$lists      = $displayData['lists'];
?>

<div class="contentpane<?php echo $params->get('pageclass_sfx'); ?>">
	<?php // Show pretext only if set in basic parameters
	if ($params->get('pretext'))
	{
		$preText = Text::_($params->get('pretext'));
		?>
		<p class="pre_text"><?php echo $preText; ?></p>
		<?php
	} // End: Show pretext only if set in basic parameters ?>

	<?php // Show editlink only if the user is not logged in
	if (Factory::getApplication()->input->get('view') !== 'edit')
	{
		$link = Uri::base() . 'index.php?option=com_bwpostman&view=edit';
		?><p class="user_edit">
			<a href="<?php echo $link; ?>">
				<?php echo Text::_('COM_BWPOSTMAN_LINK_TO_EDITLINKFORM'); ?>
			</a>
		</p><?php
	}

	// End: Show editlink only if the user is not logged in ?>

	<?php // Show formfield gender only if enabled in basic parameters
	if ($params->get('show_gender') == 1)
	{
		?><p class="edit_gender">
			<label id="gendermsg"> <?php echo Text::_('COM_BWPOSTMAN_GENDER'); ?>:</label><?php
			echo $lists['gender']; ?>
		</p><?php
	} // End gender ?>

	<?php // Show first name-field only if set in basic parameters
	if ($params->get('show_firstname_field') || $params->get('firstname_field_obligation'))
	{
		?><p class="user_firstname input<?php echo ($params->get('firstname_field_obligation')) ? '-append' : '' ?>">
			<label id="firstnamemsg" for="firstname"><?php
				echo Text::_('COM_BWPOSTMAN_FIRSTNAME'); ?>: </label><?php
			// Is filling out the firstname field obligating
			if ($params->get('firstname_field_obligation'))
			{
				?><input type="text" name="firstname" id="firstname" size="40"
						value="<?php
						if (!empty($subscriber->firstname))
						{
							echo $subscriber->firstname;
						} ?>"
						class="<?php
						if ((!empty($subscriber->err_code)) && ($subscriber->err_code == 1))
						{
							echo "invalid";
						} ?>"
						maxlength="50" /><span class="append-area"><i class="icon-star"></i></span>
			<?php
			}
			else
			{
				?><input type="text" name="firstname" id="firstname" size="40"
						value="<?php echo $subscriber->firstname; ?>"
						class="<?php
						if ((!empty($subscriber->err_code)) && ($subscriber->err_code == 1))
						{
							echo "invalid";
						}
						else
						{
							echo "inputbox";
						} ?>"
						maxlength="50" />
			<?php
			}

			// End: Is filling out the firstname field obligating
			?>
		</p><?php
	}

	// End: Show first name-field only if set in basic parameters ?>


	<?php // Show name-field only if set in basic parameters
	if ($params->get('show_name_field') || $params->get('name_field_obligation'))
	{ ?><p class="user_name edit_name input<?php echo ($params->get('name_field_obligation')) ? '-append' : '' ?>">
			<label id="namemsg" for="name"
				<?php
				if ((!empty($subscriber->err_code)) && ($subscriber->err_code == 1))
				{
					echo "class=\"invalid\"";
				} ?>>
				<?php echo Text::_('COM_BWPOSTMAN_NAME'); ?>: </label><?php
			// Is filling out the name field obligating
			if ($params->get('name_field_obligation'))
			{
				?><input type="text" name="name" id="name" size="40" value="<?php echo $subscriber->name; ?>"
						class="<?php
						if ((!empty($subscriber->err_code)) && ($subscriber->err_code == 1))
						{
							echo "invalid";
						} ?>"
						maxlength="50" /><span class="append-area"><i class="icon-star"></i></span> <?php
			}
			else
			{ ?><input type="text" name="name" id="name" size="40" value="<?php echo $subscriber->name; ?>"
					class="<?php
					if ((!empty($subscriber->err_code)) && ($subscriber->err_code == 1)) {
						echo "invalid";
					}
					else
					{
						echo "inputbox";
					} ?>"
					maxlength="50" /> <?php
			}

			// End: Is filling out the name field obligating
			?>
		</p> <?php
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
		}

		?><p class="edit_special input<?php echo ($params->get('special_field_obligation')) ? '-append' : '' ?>">
			<label id="specialmsg" class="hasTooltip" title="<?php echo HtmlHelper::tooltipText($tip); ?>" for="special"
				<?php
				if ((!empty($subscriber->err_code)) && ($subscriber->err_code == 1))
				{
					echo " class=\"invalid\"";
				}?>
				>
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
			</label><?php
			// Is filling out the special field obligating
			if ($params->get('special_field_obligation'))
			{ ?><input type="text" name="special" id="special" size="40" value="<?php echo $subscriber->special; ?>"
						class="<?php
						if ((!empty($subscriber->err_code)) && ($subscriber->err_code == 1))
						{
							echo "invalid";
						}
						else
						{
							echo "inputbox";
						} ?>"
						maxlength="50" /><span class="append-area"><i class="icon-star"></i></span><?php
			}
			else
			{ ?><input type="text" name="special" id="special" size="40" value="<?php echo $subscriber->special; ?>"
						class="<?php
						if ((!empty($subscriber->err_code)) && ($subscriber->err_code == 1))
						{
							echo "invalid";
						}
						else
						{
							echo "inputbox";
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
			if ((!empty($subscriber->err_code)) && ($subscriber->err_code != 1))
			{
				echo "class=\"invalid\"";
			} ?>>
			<?php echo Text::_('COM_BWPOSTMAN_EMAIL'); ?>:
		</label><input type="text" id="email" name="email" size="40" value="<?php echo $subscriber->email; ?>"
			class="<?php
			if ((!empty($subscriber->err_code)) && ($subscriber->err_code != 1))
			{
				echo "invalid";
			}
			else
			{
				echo "inputbox validate-email";
			} ?>"
			maxlength="100" /><span class="append-area"><i class="icon-star"></i></span>
	</p>
	<?php
	// Show formfield email format only if enabled in basic parameters
	if ($params->get('show_emailformat') == 1)
	{
		?><div class="user_mailformat edit_emailformat">
			<label id="emailformatmsg"> <?php echo Text::_('COM_BWPOSTMAN_EMAILFORMAT'); ?>: </label><?php
			echo $lists['emailformat']; ?>
		</div>
	<?php
	}
	else
	{
		// hidden field with the default email format
		?><input type="hidden" name="emailformat" value="<?php echo $params->get('default_emailformat'); ?>" />
	<?php
	}

	// End email format
	?>

	<?php
	// Show available mailinglists
	if ($lists['available_mailinglists'])
	{
		?><div class="maindivider<?php echo $params->get('pageclass_sfx'); ?>"></div>

		<div class="contentpane<?php echo $params->get('pageclass_sfx'); ?>">
			<?php
			$n = count($lists['available_mailinglists']);

			$descLength = $params->get('desc_length');

			if ($lists['available_mailinglists'] && ($n > 0))
			{
				if ($n == 1)
				{
					?><input title="mailinglists_array" type="checkbox" style="display: none;" id="<?php echo "mailinglists0"; ?>"
							name="<?php echo "mailinglists[]"; ?>" value="<?php echo $lists['available_mailinglists'][0]->id; ?>" checked="checked" />
					<?php
					if ($params->get('show_desc') == 1)
					{
						?><p class="mail_available">
							<?php echo Text::_('COM_BWPOSTMAN_MAILINGLIST'); ?>
						</p>
						<p class="mailinglist-description-single">
							<span class="mail_available_list_title">
								<?php echo $lists['available_mailinglists'][0]->title . ": "; ?>
							</span>
							<?php
							echo substr(Text::_($lists['available_mailinglists'][0]->description), 0, $descLength);

							if (strlen(Text::_($lists['available_mailinglists'][0]->description)) > $descLength)
							{
								echo '... ';
								echo HtmlHelper::tooltip(Text::_($lists['available_mailinglists'][0]->description),
									$lists['available_mailinglists'][0]->title);
							} ?>
						</p>
						<?php
					}
				}
				else
				{
					?><p class="mail_available">
						<?php echo Text::_('COM_BWPOSTMAN_MAILINGLISTS') . ' <sup><i class="icon-star"></i></sup>'; ?>
					</p>
					<?php
					foreach ($lists['available_mailinglists'] as $i => $item)
					{
						?><p class="mail_available_list <?php echo "mailinglists$i"; ?>">
							<input title="mailinglists_array" type="checkbox" id="<?php echo "mailinglists$i"; ?>"
									name="<?php echo "mailinglists[]"; ?>" value="<?php echo $item->id; ?>"
							<?php
							if ((is_array($subscriber->mailinglists)) && (in_array((int) $item->id,
									$subscriber->mailinglists)))
							{
								echo "checked=\"checked\"";
							} ?> />
							<span class="mail_available_list_title">
								<?php echo $params->get('show_desc') == 1 ? $item->title . ": " : $item->title; ?>
							</span>
							<?php
							if ($params->get('show_desc') == 1)
							{ ?>
							<span>
								<?php
								echo substr(Text::_($item->description), 0, $descLength);
								if (strlen(Text::_($item->description)) > $descLength)
								{
									echo '... ';
									echo HtmlHelper::tooltip(Text::_($item->description), $item->title);
								} ?>
							</span>
							<?php
							} ?>
						</p>
						<?php
					} ?>
					<div class="maindivider<?php echo $params->get('pageclass_sfx'); ?>"></div>
					<?php
				}
			}?>
		</div>

		<?php
	}

	// End Mailinglists ?>

</div>
