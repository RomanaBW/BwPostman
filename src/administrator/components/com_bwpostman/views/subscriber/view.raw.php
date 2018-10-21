<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single text (raw) subscribers view for backend.
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Import VIEW object class
jimport('joomla.application.component.view');

/**
 * BwPostman Subscriber RAW View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	Subscribers
 *
 * @since       0.9.1
 */
class BwPostmanViewSubscriber extends JViewLegacy
{
	/**
	 * property to hold permissions as array
	 *
	 * @var array $permissions
	 *
	 * @since       2.0.0
	 */
	public $permissions;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function display($tpl = null)
	{
		$app 	= JFactory::getApplication();

		$this->permissions		= JFactory::getApplication()->getUserState('com_bwpm.permissions');

		if (!$this->permissions['view']['subscriber'])
		{
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', JText::_('COM_BWPOSTMAN_SUBS')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		// Get the post data
		$post	= $app->getUserState('com_bwpostman.subscribers.export.data');

		if ($post['fileformat'] == 'csv')
		{
			$mime_type = "application/csv";
		}
		else
		{
			$mime_type = "application/xml";
		}

		$date		= JFactory::getDate();
		$filename	= "BackupList_BwPostman_from_" . $date->format("Y-m-d");

		// Maybe we need other headers depending on browser type...
		jimport('joomla.environment.browser');
		$browser		= JBrowser::getInstance();
		$user_browser	= $browser->getBrowser();
		$appWeb         = new JApplicationWeb();

		$appWeb->clearHeaders();

		$appWeb->setHeader('Content-Type', $mime_type, true); // Joomla will overwrite this...
		if ($post['fileformat'] == 'csv')
		{
			$appWeb->setHeader('Content-Disposition', "attachment; filename=\"$filename.csv\"", true);
		}
		else
		{
			$appWeb->setHeader('Content-Disposition', "attachment; filename=\"$filename.xml\"", true);
		}

		$appWeb->setHeader('Expires', gmdate('D, d M Y H:i:s') . ' GMT', true);
		$appWeb->setHeader('Pragma', 'no-cache', true);

		if ($user_browser == "msie")
		{
			$appWeb->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
			$appWeb->setHeader('Pragma', 'public', true);
		}

		// Joomla overwrites content-type, we can't use $appWeb->setHeader()
		$document = JFactory::getDocument();
		$document->setMimeEncoding($mime_type);

		@ob_end_clean();
		ob_start();

		$appWeb->sendHeaders();

		// Get the export data
		$model = $this->getModel('subscriber');
		echo $model->export($post);

		return $this;
	}
}
