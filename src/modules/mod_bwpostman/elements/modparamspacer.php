<?php
/**
 * BwPostman Module
 *
 * BwPostman special form field for module.
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

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

jimport('joomla.form.formfield');

/**
 * Class JFormFieldAsset
 *
 * @since
 */
class JFormFieldModparamspacer extends JFormField
{
	/**
	 * property to hold type
	 *
	 * @var string
	 *
	 * @since
	 */
	protected $type = 'Modparamspacer';

	/**
	 * Method to get the field input markup for a spacer with special class.
	 * The spacer does not have accept input.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   2.4.0
	 */
	protected function getInput()
	{
		return '';
	}

	/**
	 * Method to get the field label markup for a spacer with special class.
	 * The spacer does not accept label.
	 *
	 * @return  string  The field label markup.
	 *
	 * @since   2.4.0
	 */
	protected function getLabel()
	{
		return '';
	}

	/**
	 * Method to get the field label markup for a spacer with special class.
	 * The spacer does not accept title.
	 *
	 * @return  string  The field title.
	 *
	 * @since   2.4.0
	 */
	protected function getTitle()
	{
		return '';
	}

	/**
	 * Method to get a control group with label and input for a spacer with special class.
	 *
	 * @param   array  $options  Options to be passed into the rendering of the field
	 *
	 * @return  string  A string containing the html for the control group
	 *
	 * @since   2.4.0
	 */
	public function renderField($options = array())
	{
		$options['class'] = 'bwpmod field-spacer';

		return parent::renderField($options);
	}
}

