<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman  form field all media class.
 *
 * @version 1.3.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2016 Boldt Webservice <forum@boldt-webservice.de>
 * @support http://www.boldt-webservice.de/forum/bwpostman.html
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

/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Class JFormFieldAllMedia
 */
class JFormFieldAllMedia extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0.4
	 */
	protected $type = 'Media';

	/**
	 * The initialised state of the document object.
	 *
	 * @var    boolean
	 * @since  1.0.4
	 */
	protected static $initialised = false;

	/**
	 * The authorField.
	 *
	 * @var    string
	 * @since  1.0.4
	 */
	protected $authorField;

	/**
	 * The asset.
	 *
	 * @var    string
	 * @since  1.0.4
	 */
	protected $asset;

	/**
	 * The link.
	 *
	 * @var    string
	 * @since  1.0.4
	 */
	protected $link;

	/**
	 * The authorField.
	 *
	 * @var    string
	 * @since  1.0.4
	 */
	protected $preview;

	/**
	 * The preview.
	 *
	 * @var    string
	 * @since  1.0.4
	 */
	protected $directory;

	/**
	 * The previewWidth.
	 *
	 * @var    int
	 * @since  1.0.4
	 */
	protected $previewWidth;

	/**
	 * The previewHeight.
	 *
	 * @var    int
	 * @since  1.0.4
	 */
	protected $previewHeight;

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to the the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 * @since   1.0.4
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'authorField':
			case 'asset':
			case 'link':
			case 'preview':
			case 'directory':
			case 'previewWidth':
			case 'previewHeight':
				return $this->$name;
		}

		return parent::__get($name);
	}

	/**
	 * Method to set certain otherwise inaccessible properties of the form field object.
	 *
	 * @param   string  $name   The property name for which to the the value.
	 * @param   mixed   $value  The value of the property.
	 *
	 * @return  void
	 *
	 * @since   1.0.4
	 */
	public function __set($name, $value)
	{
		switch ($name)
		{
			case 'authorField':
			case 'asset':
			case 'link':
			case 'preview':
			case 'directory':
				$this->$name = (string) $value;
				break;

			case 'previewWidth':
			case 'previewHeight':
				$this->$name = (int) $value;
				break;

			default:
				parent::__set($name, $value);
		}
	}

	/**
	 * Method to attach a JForm object to the field.
	 *
	 * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
	 * @param   mixed             $value    The form field value to validate.
	 * @param   string            $group    The field name group control value. This acts as as an array container for the field.
	 *                                      For example if the field has name="foo" and the group value is set to "bar" then the
	 *                                      full field name would end up being "bar[foo]".
	 *
	 * @return  boolean  True on success.
	 *
	 * @see 	JFormField::setup()
	 * @since   1.0.4
	 */
	public function setup(SimpleXMLElement $element, $value, $group = null)
	{
		$result = parent::setup($element, $value, $group);

		if ($result == true)
		{
			$assetField = $this->element['asset_field'] ? (string) $this->element['asset_field'] : 'asset_id';

			$this->authorField   = $this->element['created_by_field'] ? (string) $this->element['created_by_field'] : 'created_by';
			$this->asset         = $this->form->getValue($assetField) ? $this->form->getValue($assetField) : (string) $this->element['asset_id'];
			$this->link          = (string) $this->element['link'];
			$this->preview       = (string) $this->element['preview'];
			$this->directory     = (string) $this->element['directory'];
			$this->previewWidth  = isset($this->element['preview_width']) ? (int) $this->element['preview_width'] : 200;
			$this->previewHeight = isset($this->element['preview_height']) ? (int) $this->element['preview_height'] : 200;
		}

		return $result;
	}

	/**
	 * Method to get the field input markup for a media selector.
	 * Use attributes to identify specific created_by and asset_id fields
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.0.4
	 */
	protected function getInput()
	{
		$asset = $this->asset;

		if ($asset == '')
		{
			$asset = JFactory::getApplication()->input->get('option');
		}

		if ($asset == 'com_bwpostman') {
			$asset_txt 	= '';
		}
		else {
			$asset_txt 	= '&amp;asset=' . $asset;
		}

		if (!self::$initialised)
		{
			// Load the modal behavior script.
			JHtml::_('behavior.modal');

			// Include jQuery
			JHtml::_('jquery.framework');

			// Build the script.
			$script = array();
			$script[] = '	function jInsertFieldValue(value, id) {';
			$script[] = '		var $ = jQuery.noConflict();';
			$script[] = '		var old_value = $("#" + id).val();';
			$script[] = '		if (old_value != value) {';
			$script[] = '			var $elem = $("#" + id);';
			$script[] = '			$elem.val(value);';
			$script[] = '			$elem.trigger("change");';
			$script[] = '			if (typeof($elem.get(0).onchange) === "function") {';
			$script[] = '				$elem.get(0).onchange();';
			$script[] = '			}';
			$script[] = '			jMediaRefreshPreview(id);';
			$script[] = '		}';
			$script[] = '	}';

			$script[] = '	function jMediaRefreshPreview(id) {';
			$script[] = '		var $ = jQuery.noConflict();';
			$script[] = '		var value = $("#" + id).val();';
			$script[] = '		var $img = $("#" + id + "_preview");';
			$script[] = '		if ($img.length) {';
			$script[] = '			if (value) {';
			$script[] = '				$img.attr("src", "' . JUri::root() . '" + value);';
			$script[] = '				$("#" + id + "_preview_empty").hide();';
			$script[] = '				$("#" + id + "_preview_img").show()';
			$script[] = '			} else { ';
			$script[] = '				$img.attr("src", "")';
			$script[] = '				$("#" + id + "_preview_empty").show();';
			$script[] = '				$("#" + id + "_preview_img").hide();';
			$script[] = '			} ';
			$script[] = '		} ';
			$script[] = '	}';

			$script[] = '	function jMediaRefreshPreviewTip(tip)';
			$script[] = '	{';
			$script[] = '		var $ = jQuery.noConflict();';
			$script[] = '		var $tip = $(tip);';
			$script[] = '		var $img = $tip.find("img.media-preview");';
			$script[] = '		$tip.find("div.tip").css("max-width", "none");';
			$script[] = '		var id = $img.attr("id");';
			$script[] = '		id = id.substring(0, id.length - "_preview".length);';
			$script[] = '		jMediaRefreshPreview(id);';
			$script[] = '		$tip.show();';
			$script[] = '	}';

			// Add the script to the document head.
			JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

			self::$initialised = true;
		}

		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= !empty($this->class) ? ' class="input-small ' . $this->class . '"' : ' class="input-small"';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';

		// Initialize JavaScript field attributes.
		$attr .= !empty($this->onchange) ? ' onchange="' . $this->onchange . '"' : '';

		// The text field.
		$html[] = '<div class="input-prepend input-append">';

		// The Preview.
		$showPreview = true;
		$showAsTooltip = false;

		switch ($this->preview)
		{
			case 'no': // Deprecated parameter value
			case 'false':
			case 'none':
				$showPreview = false;
				break;

			case 'yes': // Deprecated parameter value
			case 'true':
			case 'show':
				break;

			case 'tooltip':
			default:
				$showAsTooltip = true;
				$options = array(
					'onShow' => 'jMediaRefreshPreviewTip',
				);
				JHtml::_('behavior.tooltip', '.hasTipPreview', $options);
				break;
		}

		if ($showPreview)
		{
			if ($this->value && file_exists(JPATH_ROOT . '/' . $this->value))
			{
				$src = JUri::root() . $this->value;
			}
			else
			{
				$src = '';
			}

			$width = $this->previewWidth;
			$height = $this->previewHeight;
			$style = '';
			$style .= ($width > 0) ? 'max-width:' . $width . 'px;' : '';
			$style .= ($height > 0) ? 'max-height:' . $height . 'px;' : '';

			$imgattr = array(
				'id' => $this->id . '_preview',
				'class' => 'media-preview',
				'style' => $style,
			);

			$img = JHtml::image($src, JText::_('JLIB_FORM_MEDIA_PREVIEW_ALT'), $imgattr);
			$previewImg = '<div id="' . $this->id . '_preview_img"' . ($src ? '' : ' style="display:none"') . '>' . $img . '</div>';
			$previewImgEmpty = '<div id="' . $this->id . '_preview_empty"' . ($src ? ' style="display:none"' : '') . '>'
				. JText::_('JLIB_FORM_MEDIA_PREVIEW_EMPTY') . '</div>';

			if ($showAsTooltip)
			{
				$html[] = '<div class="media-preview add-on">';
				$tooltip = $previewImgEmpty . $previewImg;
				$options = array(
					'title' => JText::_('JLIB_FORM_MEDIA_PREVIEW_SELECTED_IMAGE'),
					'text' => '<i class="icon-eye"></i>',
					'class' => 'hasTipPreview'
				);

				$html[] = JHtml::tooltip($tooltip, $options);
				$html[] = '</div>';
			}
			else
			{
				$html[] = '<div class="media-preview add-on" style="height:auto;">';
				$html[] = ' ' . $previewImgEmpty;
				$html[] = ' ' . $previewImg;
				$html[] = '</div>';
			}
		}

		$html[] = '	<input type="text" name="' . $this->name . '" id="' . $this->id . '" value="'
			. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '" readonly="readonly"' . $attr . ' />';

		if ($this->value && file_exists(JPATH_ROOT . '/' . $this->value))
		{
			$folder = explode('/', $this->value);
			$folder = array_diff_assoc($folder, explode('/', JComponentHelper::getParams('com_media')->get('image_path', 'images')));
			array_pop($folder);
			$folder = implode('/', $folder);
		}
		elseif (file_exists(JPATH_ROOT . '/' . JComponentHelper::getParams('com_media')->get('image_path', 'images') . '/' . $this->directory))
		{
			$folder = $this->directory;
		}
		else
		{
			$folder = '';
		}

		// The button.
		if ($this->disabled != true)
		{
			JHtml::_('bootstrap.tooltip');

			$html[] = '<a class="modal btn" title="' . JText::_('JLIB_FORM_BUTTON_SELECT') . '" href="'
				. ($this->readonly ? ''
				: ($this->link ? $this->link
				: 'index.php?option=com_bwpostman&amp;view=media&amp;tmpl=component' . $asset_txt . '&amp;author='
				. $this->form->getValue($this->authorField)) . '&amp;fieldid=' . $this->id . '&amp;folder=' . $folder) . '"'
				. ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
			$html[] = JText::_('JLIB_FORM_BUTTON_SELECT') . '</a><a class="btn hasTooltip" title="' . JText::_('JLIB_FORM_BUTTON_CLEAR') . '" href="#" onclick="';
			$html[] = 'jInsertFieldValue(\'\', \'' . $this->id . '\');';
			$html[] = 'return false;';
			$html[] = '">';
			$html[] = '<i class="icon-remove"></i></a>';
		}
		$html[] = '</div>';

		return implode("\n", $html);
	}
}
