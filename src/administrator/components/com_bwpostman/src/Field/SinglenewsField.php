<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman backend element to select a single newsletter for a view in frontend.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
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

namespace BoldtWebservice\Component\BwPostman\Administrator\Field;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use function defined;

/**
 * Renders a newsletter element
 *
 * @version 4.0.0
 * @package BwPostman-Admin
 *
 * @since       1.0.8
 */

class SinglenewsField extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  4.0
	 */
	protected $type = 'SingleNews';

	/**
	 * Method to get form input field
	 *
	 * @return string
	 *
	 * @throws Exception
	 *
	 * @since       1.0.8
	 */
	protected function getinput(): string
	{
		$MvcFactory = Factory::getApplication()->bootComponent('com_bwpostman')->getMVCFactory();

		$newsletter = $MvcFactory->createTable('Newsletter', 'Administrator');

		if ($this->value)
		{
			$newsletter->load($this->value);
		}
		else {
			$newsletter->subject = Text::_('COM_BWPOSTMAN_SELECT_NEWSLETTER');
		}

		// The active newsletter id field.
		if ((int)$this->value > 0)
		{
			$value = (int)$this->value;
		}
		else
		{
			$value = '';
		}

		// Create the modal id.
		$modalId = 'Newsletter_' . $this->id;
		$modalTitle = Text::_('COM_BWPOSTMAN_SELECT_NEWSLETTER');

		$wa = Factory::getApplication()->getDocument()->getWebAssetManager();

		// Add the modal field script to the document head.
		$wa->useScript('field.modal-fields');

		$link = 'index.php?option=com_bwpostman&amp;view=newsletterelement&amp;tmpl=component&amp' . Session::getFormToken() . '=1';
		$urlSelect = $link . '&amp;function=jSelectNewsletter_' . $this->id;

		$wa->addInlineScript("
			window.SelectNewsletter = function (id, subject) {
				window.processModalSelect('Newsletter', '" . $this->id . "', id, subject, '', '', '', '')
			}",
			[],
			['type' => 'module']
		);

		$title = empty($newsletter->subject) ? Text::_('COM_CONTENT_SELECT_AN_ARTICLE') : htmlspecialchars($newsletter->subject, ENT_QUOTES);

		$html = '<span class="input-group">';
		$html .= '<input class="form-control" id="' . $this->id . '_name" type="text" value="' . $title . '" readonly size="35">';

		// Select newsletter button
		$html .= '<button'
			. ' class="btn btn-primary"'
			. ' id="' . $this->id . '_select"'
			. ' data-bs-toggle="modal"'
			. ' type="button"'
			. ' data-bs-target="#ModalSelect' . $modalId . '">'
			. '<span class="icon-file" aria-hidden="true"></span> ' . Text::_('JSELECT')
			. '</button>';

		// Clear article button
		$html .= '<button'
			. ' class="btn btn-secondary' . ($value ? '' : ' hidden') . '"'
			. ' id="' . $this->id . '_clear"'
			. ' type="button"'
			. ' onclick="window.processModalParent(\'' . $this->id . '\'); return false;">'
			. '<span class="icon-remove" aria-hidden="true"></span> ' . Text::_('JCLEAR')
			. '</button>';

		$html .= '</span>';

		// Select newsletter modal
		$html .= HTMLHelper::_(
			'bootstrap.renderModal',
			'ModalSelect' . $modalId,
			array(
				'title'       => $modalTitle,
				'url'         => $urlSelect,
				'height'      => '400px',
				'width'       => '800px',
				'bodyHeight'  => 70,
				'modalWidth'  => 80,
				'footer'      => '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">'
									. Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>',
			)
		);

		// Note: class='required' for client side validation.
		$class = $this->required ? ' class="required modal-value"' : '';

		$html .= '<input type="hidden" id="' . $this->id . '_id" ' . $class . ' data-required="' . (int) $this->required . '" name="' . $this->name
			. '" data-text="' . htmlspecialchars(Text::_('COM_BWPOSTMAN_SELECT_NEWSLETTER', true), ENT_COMPAT) . '" value="' . $value . '">';

		return $html;
	}
}

