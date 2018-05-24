<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single text (raw) subscribers view for backend.
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

// Import VIEW object class
jimport('joomla.application.component.view');

/**
 * BwPostman Maintenance RAW View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	Subscribers
 *
 * @since       1.0.1
 */
class BwPostmanViewMaintenance extends JViewLegacy
{
	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since       1.0.1
	 * @throws Exception
	 *
	 */
	public function display($tpl = null)
	{
		$app 	= JFactory::getApplication();
		$jinput	= JFactory::getApplication()->input;
		$date	= JFactory::getDate();

		if (!BwPostmanHelper::canView('maintenance'))
		{
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', JText::_('COM_BWPOSTMAN_MAINTENANCE')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		$layout	= $jinput->get('layout');

		if ($layout == 'saveTables')
		{
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);

			$query->select($db->quoteName('manifest_cache'));
			$query->from($db->quoteName('#__extensions'));
			$query->where($db->quoteName('element') . " = " . $db->quote('com_bwpostman'));
			$db->setQuery($query);

			$manifest	= json_decode($db->loadResult(), true);
			$version	= str_replace('.', '_', $manifest['version']);

			$filename	= "BwPostman_" . $version . "_Tables_" . $date->format("Y-m-d_H:i") . '.xml';
			$mime_type	= "application/xml";

			// Maybe we need other headers depending on browser type...
			jimport('joomla.environment.browser');
			$browser		= JBrowser::getInstance();
			$user_browser	= $browser->getBrowser();
			$appWeb         = new JApplicationWeb();

			$appWeb->clearHeaders();

			$appWeb->setHeader('Content-Type', $mime_type, true); // Joomla will overwrite this...
			$appWeb->setHeader('Content-Disposition', "attachment; filename=\"$filename\"", true);
			$appWeb->setHeader('Expires', gmdate('D, d M Y H:i:s') . ' GMT', true);
			$appWeb->setHeader('Pragma', 'no-cache', true);

			if ($user_browser == "msie")
			{
				$appWeb->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
				$appWeb->setHeader('Pragma', 'public', true);
			}

			// Joomla overwrites content-type, we can't use $appWeb->setHeader()
			$document = JFactory::getDocument();
			$document->setMimeEncoding("application/xml");

			@ob_end_clean();
			ob_start();

			$appWeb->sendHeaders();

			// Get the export data
			$model	= $this->getModel('maintenance');

			readfile($model->saveTables(false));
		}

		if ($layout == 'doRestore')
		{
			$model	= $this->getModel();
			$dest	= $app->getUserState('com_bwpostman.maintenance.dest', '');

			$model->restoreTables($dest);
		}

		if ($layout == 'checkTables')
		{
			$model	= $this->getModel();

			echo '<div class="modal" rel="{size: {x: 700, y: 500}}">';
			$model->checkTables();
			echo '</div>';

		}
		return $this;
	}
}
