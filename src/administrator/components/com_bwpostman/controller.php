<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman main controller for backend.
 *
 * @version 2.0.2 bwpm
 * @package BwPostman-Admin
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

// Import CONTROLLER object class
jimport('joomla.application.component.controller');

/**
 * BwPostman Component Controller
 *
 * @package 	BwPostman-Admin
 *
 * @since       0.9.1
 */
class BwPostmanController extends JControllerLegacy
{

	/**
	 * Display
	 *
	 * @param bool $cachable
	 * @param bool $urlparams
	 *
	 * @return  JControllerLegacy  A JControllerLegacy object to support chaining.
	 *
	 * @since       0.9.1
	 */
	public function display($cachable = false, $urlparams = false)
	{
		parent::display();

		return $this;
	}

	/**
	 * Method to call the start layout for the add text template
	 *
	 * @throws Exception
	 *
	 * @since	1.1.0
	 */
	public function addtext()
	{
		$jinput	= JFactory::getApplication()->input;

		$jinput->set('hidemainmenu', 1);
		$jinput->set('view', 'template');
		$jinput->set('layout', 'default_text');
		$link = JRoute::_('index.php?option=com_bwpostman&view=template&layout=default_text', false);
		$this->setRedirect($link);
	}

	/**
	 * Method to call the start layout for the add html template
	 *
	 * @throws Exception
	 *
	 * @since	1.1.0
	 */
	public function addhtml()
	{
		$jinput	= JFactory::getApplication()->input;

		$jinput->set('hidemainmenu', 1);
		$jinput->set('view', 'template');
		$jinput->set('layout', 'default_add');
		$link = JRoute::_('index.php?option=com_bwpostman&view=template&layout=default_html', false);
		$this->setRedirect($link);
	}

	/**
	 * Method to GET permission value and give it to the model for storing in the database.
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   3.5
	 */
	public function storePermission()
	{
		$app	= JFactory::getApplication();

		// Send json mime type.
		$app->mimeType = 'application/json';
		$app->setHeader('Content-Type', $app->mimeType . '; charset=' . $app->charSet);
		$app->sendHeaders();

		// Check if user token is valid.
		if (!JSession::checkToken('get'))
		{
			$app->enqueueMessage(JText::_('JINVALID_TOKEN'), 'error');
			echo new JResponseJson;
			$app->close();
		}

		$model = $this->getModel('BwPostman');
		echo new JResponseJson($model->storePermissions());
		$app->close();
	}
}
