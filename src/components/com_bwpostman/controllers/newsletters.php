<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletters controller for frontend.
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

use Joomla\CMS\Factory;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwException;

/**
 * Class BwPostmanControllerNewsletters
 *
 * @since       2.0.0
 */
class BwPostmanControllerNewsletters extends JControllerLegacy
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
		$Itemid = Factory::getApplication()->input->get('Itemid', null, 'STRING');
		Factory::getApplication()->setUserState('com_bwpostman.newsletters.itemid', $Itemid);

		Factory::getApplication()->input->set('view', 'newsletters');
		parent::display();
	}
}
