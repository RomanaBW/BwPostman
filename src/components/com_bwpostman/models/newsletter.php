<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletter single model for frontend.
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
use Joomla\CMS\Plugin\PluginHelper;

// Import MODEL object class
jimport('joomla.application.component.modelitem');

/**
 * Class BwPostmanModelNewsletter
 *
 * @since       0.9.1
 */
class BwPostmanModelNewsletter extends JModelItem
{
	/**
	 * Method to get  newsletter content
	 *
	 * @return	mixed	string on success, null on failure.
	 *
	 * @throws Exception
	 *
	 * @since	1.2.0
	 */
	public function getContent()
	{
		$id		    = (int) Factory::getApplication()->input->getInt('id', 0);
		$newsletter = null;
		$user	= Factory::getUser();

		$newsletter = $this->getTable('Sendmailcontent', 'BwPostmanTable')->getContent($id);

		// Get the dispatcher and include bwpostman plugins
		PluginHelper::importPlugin('bwpostman');

		// Fire the onBwPostmanPersonalize event.
		Factory::getApplication()->triggerEvent('onBwPostmanPersonalize', array('com_bwpostman.view', &$newsletter, $user->id));

		return $newsletter;
	}

	/**
	 * Method to get an item.
	 *
	 * @param   integer  $pk  The id of the item
	 *
	 * @return  object
	 *
	 * @throws Exception
	 *
	 * @since 4.0.0
	 */
	public function getItem($pk = null)
	{
		require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/models/newsletter.php');
		$model = new BwPostmanModelNewsletter();

		$item = $model->getItem((int)$pk);

		return $item;
	}
}
