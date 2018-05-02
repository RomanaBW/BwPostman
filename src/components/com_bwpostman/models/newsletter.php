<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletter single model for frontend.
 *
 * @version 2.0.1 bwpm
 * @package BwPostman-Site
 * @author Romana Boldt
 * @copyright (C) 2012-2018 Boldt Webservice <forum@boldt-webservice.de>
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
		$id		    = (int) JFactory::getApplication()->input->get('id', 0);
		$newsletter = null;
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);
		$user	= JFactory::getUser();

		// build query
		$query->select($_db->quoteName('body'));
		$query->from($_db->quoteName('#__bwpostman_sendmailcontent') . ' AS ' . $_db->quoteName('a'));
		$query->where($_db->quoteName('a') . '.' . $_db->quoteName('nl_id') . ' = ' . $id);
		$query->where($_db->quoteName('a') . '.' . $_db->quoteName('mode') . ' = ' . (int) 1);

		try
		{
			$_db->setQuery($query);
			$newsletter = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// Get the dispatcher and include bwpostman plugins
		JPluginHelper::importPlugin('bwpostman');
		$dispatcher = JEventDispatcher::getInstance();

		// Fire the onBwPostmanPersonalize event.
		$dispatcher->trigger('onBwPostmanPersonalize', array('com_bwpostman.view', &$newsletter, $user->id));

		return $newsletter;
	}
}
