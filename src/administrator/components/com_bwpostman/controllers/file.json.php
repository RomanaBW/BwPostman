<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman json file controller for backend, based on joomla com_media.
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

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Client\ClientHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\MediaHelper;
use Joomla\CMS\Log\LogEntry;

$m_params = ComponentHelper::getParams('com_media');
define('COM_MEDIA_BASE', JPATH_ROOT . '/' . $m_params->get('file_path', 'images'));

// Load the logger class
require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/libraries/logging/BwLogger.php');

/**
 * BwPostman File Media Controller
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Media
 *
 * @since		1.0.4
 */
class BwPostmanControllerFile extends JControllerLegacy
{
	/**
	 * Upload a file
	 *
	 * @return	void
	 *
	 * @since	1.0.4
	 *
	 * @throws Exception
	 */
	function upload()
	{
		$params = ComponentHelper::getParams('com_media');
		$logger = BwLogger::getInstance(array());

		// Check for request forgeries
		if (!Session::checkToken('request'))
		{
			$response = array(
				'status' => '0',
				'error' => Text::_('JINVALID_TOKEN')
			);
			echo json_encode($response);
			return;
		}

		// Get the user
		$user = Factory::getUser();


		// Get some data from the request
		$file	= $this->input->files->get('Filedata', '', 'array');
		$folder	= $this->input->get('folder', '', 'path');

		// Instantiate the media helper
		$mediaHelper = new MediaHelper();
		$contentLength = Factory::getApplication()->input->server->get('CONTENT_LENGTH', '', '');

		if (
			$contentLength > ($params->get('upload_maxsize', 0) * 1024 * 1024) ||
			$contentLength > $mediaHelper->toBytes(ini_get('upload_max_filesize')) ||
			$contentLength > $mediaHelper->toBytes(ini_get('post_max_size')) ||
			$contentLength > $mediaHelper->toBytes(ini_get('memory_limit'))
		)
		{
			$response = array(
				'status' => '0',
				'error' => Text::_('COM_BWPOSTMAN_MEDIA_ERROR_WARNFILETOOLARGE')
			);
			echo json_encode($response);
			return;
		}

		// Set FTP credentials, if given
		ClientHelper::setCredentialsFromRequest('ftp');

		// Make the filename safe
		$file['name'] = File::makeSafe($file['name']);

		if (isset($file['name']))
		{
			// The request is valid
			$err = null;

			$filepath = Path::clean(COM_MEDIA_BASE . '/' . $folder . '/' . strtolower($file['name']));

			if (!$mediaHelper->canUpload($file, 'com_media'))
			{
				$message = 'Invalid: ' . $filepath . ': ' . $err;
				$logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'upload'));

				$response = array(
					'status' => '0',
					'error' => Text::_($err)
				);

				echo json_encode($response);
				return;
			}

			// Trigger the onContentBeforeSave event.
			PluginHelper::importPlugin('content');

			$object_file	        = new JObject($file);
			$object_file->filepath  = $filepath;

			if (File::exists($object_file->filepath))
			{
				// File exists
				$message = 'File exists: ' . $object_file->filepath . ' by user_id ' . $user->id;
				$logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'upload'));

				$response = array(
					'status' => '0',
					'error' => Text::_('COM_BWPOSTMAN_MEDIA_ERROR_FILE_EXISTS')
				);

				echo json_encode($response);
				return;
			}
			elseif (!$user->authorise('core.create', 'com_media'))
			{
				// File does not exist and user is not authorised to create
				$message = 'Create not permitted: ' . $object_file->filepath . ' by user_id ' . $user->id;
				$logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'upload'));

				$response = array(
					'status' => '0',
					'error' => Text::_('COM_BWPOSTMAN_MEDIA_ERROR_CREATE_NOT_PERMITTED')
				);

				echo json_encode($response);
				return;
			}

			if (!File::upload($object_file->tmp_name, $object_file->filepath))
			{
				// Error in upload
				$message = 'Error on upload: ' . $object_file->filepath;
				$logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, 'upload'));

				$response = array(
					'status' => '0',
					'error' => Text::_('COM_BWPOSTMAN_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE')
				);

				echo json_encode($response);
				return;
			}
			else
			{
				$logger->addEntry(new LogEntry($folder, BwLogger::BW_INFO, 'upload'));

				$response = array(
					'status' => '1',
					'error' => Text::sprintf('COM_BWPOSTMAN_MEDIA_UPLOAD_COMPLETE', substr($object_file->filepath, strlen(COM_MEDIA_BASE)))
				);

				echo json_encode($response);
				return;
			}
		}
		else
		{
			$response = array(
				'status' => '0',
				'error' => Text::_('COM_BWPOSTMAN_MEDIA_ERROR_BAD_REQUEST')
			);

			echo json_encode($response);
			return;
		}
	}
}
