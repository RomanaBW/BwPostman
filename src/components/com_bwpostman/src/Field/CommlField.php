<?php
/**
 * BwPostman Module
 *
 * BwPostman special form field for module.
 *
 * @version %%version_number%%
 * @package BwPostman-Module
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

namespace BoldtWebservice\Component\BwPostman\Site\Field;

defined('JPATH_PLATFORM') or die;

use Exception;
use JLoader;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\CheckboxesField;
use Joomla\CMS\Language\Text;
use \BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanMailinglistHelper;

JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Helper', JPATH_ADMINISTRATOR.'/components/com_bwpostman/Helper', false, false);

/**
 * Form Field class.
 * Displays options as a list of check boxes.
 * Multiselect may be forced to be true.
 *
 * @package		BwPostman
 * @subpackage	Site
 * @see			JFormFieldCheckbox
 * @since		1.2.0
 */
class CommlField extends CheckboxesField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.2.0
	 */
	protected $type = 'Comml';

	/**
	 * Flag to tell the field to always be in multiple values mode.
	 *
	 * @var		boolean
	 * @since	1.2.0
	 */
	protected $forceMultiple = true;

	/**
	 * Method to get the field input markup for check boxes.
	 *
	 * @return	string  The field input markup.
	 *
	 * @throws Exception
	 *
	 * @since	1.2.0
	 */
	protected function getInput(): string
	{

		$doc = Factory::getApplication()->getDocument();
		$wa  = $doc->getWebAssetManager();
		$wa->registerAndUseScript('com_bwpostman.bwpm_menuhelper', 'com_bwpostman/bwpm_menuhelper.js');

		// Initialize variables.
		$html	= array();
		$stub	= "'mb'";

		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="checkboxes ' . $this->element['class'] . '"' : ' class="checkboxes"';

		// Start the checkbox field output.
		$html[] = '<fieldset id="' . $this->id . '"' . $class . '>';

		// Get the field options.
		$options = $this->getOptions();

		// Build the checkbox field output.
		$html[] = '	    <div class="bwp-field well well-small table-responsive">';
		$html[] = '			<table class="adminlist table table-striped">';
		$html[] = '				<thead class="thead-light">';
		$html[] = '					<tr>';
		$html[] = '						<th scope="col" style="width: 3%;">' . Text::_('JGRID_HEADING_ID') . '</th>';
		$html[] = '						<th scope="col" style="width: 1%;" class="text-center"><input type="checkbox" name="checkall-toggle" value="" title="'
			. Text::_('JGLOBAL_CHECK_ALL') . '" onclick="Joomla.checkAll(this, ' . $stub . ')" /></th>';
		$html[] = '						<th style="width: 10%;" scope="col">' . Text::_('COM_BWPOSTMAN_ARCHIVED') . '</th>';
		$html[] = '						<th style="min-width: 100px;" scope="col">' . Text::_('JGLOBAL_TITLE') . '</th>';
		$html[] = '						<th class="d-none d-lg-table-cell" style="min-width: 250px;" scope="col">' . Text::_('JGLOBAL_DESCRIPTION') . '</th>';
		$html[] = '						<th style="width: 10%;" scope="col">' . Text::_('COM_BWPOSTMAN_PUBLISHED') . '</th>';
		$html[] = '						<th style="width: 10%;" scope="col">' . Text::_('JFIELD_ACCESS_LABEL') . '</th>';
		$html[] = '					</tr>';
		$html[] = '				</thead>';
		$html[] = '				<tbody>';

		if (count($options) > 0)
		{
			foreach ($options as $i => $option)
			{
				// Initialize some option attributes.
				$checked	= (in_array((string) $option->value, (array) $this->value) ? ' checked="checked"' : '');
				$class		= !empty($option->class) ? ' class="' . $option->class . '"' : '';
				$disabled	= !empty($option->disable) ? ' disabled="disabled"' : '';
				$archived	= ($option->archived) ? '<i class="icon-archive"></i>' : '';
				$published	= ($option->published) ? '<i class="icon-publish"></i>' : '<i class="icon-unpublish"></i>';

				// Initialize some JavaScript option attributes.
				$onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';

				$html[] = '							<tr class="row' . $i % 2 . '" onclick="bwpSelectTr(\'mb' . $i . '\')">';
				$html[] = '								<td class="text-center">' . Text::_($option->value) . '</td>';
				$html[] = '								<td class="text-center"><input type="checkbox" id="mb' . $i . '" name="' . $this->name . '" value="'
					. htmlspecialchars($option->value, ENT_COMPAT) . '" ' . $checked . $class . $onclick . $disabled . ' /></td>';
				$html[] = '								<td class="text-center"><span class="tbody-icon ">' . $archived . '</span></td>';
				$html[] = '								<td>' . Text::_($option->text) . '</td>';
				$html[] = '								<td class="d-none d-lg-table-cell">' . Text::_($option->description) . '</td>';
				$html[] = '								<td class="text-center"><span class="tbody-icon">' . $published . '</span></td>';
				$html[] = '								<td>' . Text::_($option->access_level) . '</td>';
				$html[] = '						  </tr>';
			}
		}
		else
		{
			$html[] = '							<tr class="row1">';
			$html[] = '								<td colspan="7"><strong>' . Text::_('COM_BWPOSTMAN_NO_ML') . '</strong></td>';
			$html[] = '							</tr>';
		}

		$html[] = '				</tbody>';
		$html[] = '			</table>';
		$html[] = '		</div>';

		// End the checkbox field output.
		$html[] = '</fieldset>';

		return implode($html);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return	array  The field option objects.
	 *
	 * @throws Exception
	 *
	 * @since	1.2.0
	 */
	protected function getOptions(): array
	{
		$options = BwPostmanMailinglistHelper::getMailinglistsFieldlistOptions(false);

		// Merge any additional options in the XML definition.
		return array_merge(parent::getOptions(), $options);
	}
}
