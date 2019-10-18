<?php
/**
 * Joomla! Content Management System
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

//namespace Joomla\CMS\Toolbar\Button;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Toolbar\ToolbarButton;
use Joomla\CMS\Language\Text;

// require_once JPATH_COMPONENT_ADMINISTRATOR . '/libraries/toolbar';

/**
 * Renders an external link button
 *
 * @since  2.2.0
 */
class JButtonExtlink extends ToolbarButton
{
	/**
	 * Button type
	 * @var    string
	 *
	 * @since 2.2.0
	 */
	protected $_name = 'Extlink';

	/**
	 * Fetch the HTML for the button
	 *
	 * @param   string  $type  Unused string.
	 * @param   string  $name  Name to be used as apart of the id
	 * @param   string  $text  Button text
	 * @param   string  $url   The link url
	 *
	 * @return  string  HTML string for the button
	 *
	 * @since   2.2.0
	 */
	public function fetchButton($type = 'Extlink', $name = 'back', $text = '', $url = null)
	{
		// Store all data to the options array for use with JLayout
		$options = array();
		$options['text'] = \JText::_($text);
		$options['class'] = $this->fetchIconClass($name);
		$options['doTask'] = $this->_getCommand($url);
		$this->options = $options;

		// Instantiate a new JLayoutFile instance and render the layout
		$layout = new FileLayout('layouts.toolbar.extlink', JPATH_COMPONENT_ADMINISTRATOR );

		return $layout->render($options);
	}

	/**
	 * Get the button CSS Id
	 *
	 * @param   string  $type  The button type.
	 * @param   string  $name  The name of the button.
	 *
	 * @return  string  Button CSS Id
	 *
	 * @since   2.2.0
	 */
	public function fetchId($type = 'Extlink', $name = '')
	{
		if(version_compare(JVERSION, '3.999.999', 'le'))
		{
			return $this->_parent->getName() . '-' . $name;
		}
		else
		{
			return $this->parent->getName() . '-' . $name;
		}
	}

	/**
	 * Get the JavaScript command for the button
	 *
	 * @param   string  $url  Button definition
	 *
	 * @return  string  JavaScript command string
	 *
	 * @since   2.2.0
	 */
	protected function _getCommand($url)
	{
		return $url;
	}

	/**
	 * Prepare options for this button.
	 *
	 * @param   array  &$options  The options about this button.
	 *
	 * @return  void
	 *
	 * @since  2.4.0
	 */
	protected function prepareOptions(array &$options)
	{
		$options['name']  = $this->getName();
		$options['text']  = Text::_($this->getText());
		$options['class'] = $this->getIcon() ?: $this->fetchIconClass($this->getName());
		$options['id']    = $this->ensureUniqueId($this->fetchId('Extlink', $options['idName']));

		if (!empty($options['is_child']))
		{
			$options['tagName'] = 'button';
			$options['btnClass'] = ($options['button_class'] ?? '') . ' dropdown-item';
			$options['attributes']['type'] = 'button';
		}
		else
		{
			$options['tagName'] = 'button';
			$options['btnClass'] = ($options['button_class'] ?? 'btn btn-primary');
			$options['attributes']['type'] = 'button';
		}
	}

	/**
	 * Get the HTML to render the button
	 *
	 * @param   array  &$definition  Parameters to be passed
	 *
	 * @return  string
	 *
	 * @since   2.4.0
	 *
	 * @throws \Exception
	 */
	public function render(&$definition = null)
	{
		if ($definition === null)
		{
			$action = $this->renderButton($this->options);
		}
		// For B/C
		elseif (is_array($definition))
		{
			$action = $this->fetchButton(...$definition);
		}
		else
		{
			throw new \InvalidArgumentException('Wrong argument: $definition, should be NULL or array.');
		}

		// Build the HTML Button
		$layout = new FileLayout('toolbar.extlink', JPATH_ADMINISTRATOR . '/components/com_bwpostman/layouts');

		return $layout->render(
			[
				'action'  => $action,
				'options' => $this->options
			]
		);
	}

	/**
	 * Method to get the CSS class name for an icon identifier
	 *
	 * Can be redefined in the final class
	 *
	 * @param   string  $identifier  Icon identification string
	 *
	 * @return  string  CSS class name
	 *
	 * @since   2.4.0
	 */
	public function fetchIconClass($identifier)
	{
		// It's an ugly hack, but this allows templates to define the icon classes for the toolbar
		$layout = new FileLayout('joomla.toolbar.iconclass');

		return $layout->render(array('icon' => $identifier));
	}

}
