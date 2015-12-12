<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single text (raw) subscribers view for backend.
 *
 * @version 1.2.4 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2015 Boldt Webservice <forum@boldt-webservice.de>
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

// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die ('Restricted access');

// Import VIEW object class
jimport('joomla.application.component.view');

/**
 * BwPostman Maintenance RAW View
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Subscribers
 */
class BwPostmanViewMaintenance extends JViewLegacy
{
	public function display ($tpl = Null)
	{
		$app 	= JFactory::getApplication();
		$jinput	= JFactory::getApplication()->input;
		$date	= JFactory::getDate();
		
		$layout	= $jinput->get('layout');
		
		if ($layout == 'saveTables') {
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);
			
			$query->select($db->quoteName('manifest_cache'));
			$query->from($db->quoteName('#__extensions'));
			$query->where($db->quoteName('element') . " = " . $db->quote('com_bwpostman'));
			$db->SetQuery($query);
			
			$manifest	= json_decode($db->loadResult(), true);
			$version	= str_replace('.', '_', $manifest['version']);
			
			$filename	= "BwPostman_" . $version . "_Tables_" . $date->format("Y-m-d_H:i") . '.xml';
			$mime_type	= "application/xml";
	
			// Maybe we need other headers depending on browser type...
			jimport('joomla.environment.browser');
			$browser		= JBrowser::getInstance();
			$user_browser	= $browser->getBrowser();
	
			JResponse::clearHeaders();
	
			JResponse::setHeader('Content-Type', $mime_type, true); // Joomla will overwrite this...
			JResponse::setHeader('Content-Disposition', "attachment; filename=\"$filename\"", true);
			JResponse::setHeader('Expires', gmdate('D, d M Y H:i:s') . ' GMT', true);
			JResponse::setHeader('Pragma', 'no-cache', true);
	
			if ($user_browser == "msie"){
				JResponse::setHeader('Cache-Control','must-revalidate, post-check=0, pre-check=0', true);
				JResponse::setHeader('Pragma', 'public', true);
			}
	
			// Joomla overwrites content-type, we can't use JResponse::setHeader()
			$document = JFactory::getDocument();
			$document->setMimeEncoding("application/xml");
	
			@ob_end_clean();
			ob_start();
	
			JResponse::sendHeaders();
	
			// Get the export data
			$model	= $this->getModel('maintenance');

			readfile($model->saveTables(false));
		}
		
		if ($layout == 'doRestore') {
			$model	= $this->getModel();
			$dest	= $app->getUserState('com_bwpostman.maintenance.dest', '');
			
			$model->restoreTables($dest);
		}

		if ($layout == 'checkTables') {
			$model	= $this->getModel();
			
			echo '<div class="modal" rel="{size: {x: 700, y: 500}}">';
			$model->checkTables();
			echo '</div>';
			
		}
	}
}
