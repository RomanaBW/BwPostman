<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletter controller for frontend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Site
 * @author Romana Boldt
 * @copyright (C) 2012-2017 Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/bwpostman.html
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

// Import CONTROLLER object class
jimport('joomla.application.component.controller');

// Require component admin helper class and exception class
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/exceptions/BwException.php');


/**
 * Class BwPostmanControllerNewsletter
 *
 * @since       2.0.0
 */
class BwPostmanControllerNewsletter extends JControllerLegacy
{

	/**
	 * Display
	 *
	 * @param	boolean		$cachable	If true, the view output will be cached
	 * @param	boolean		$urlparams	An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0
	 */
	public function display($cachable = false, $urlparams = false)
	{
		JFactory::getApplication()->input->set('view', 'newsletter');

		parent::display();
	}
}
