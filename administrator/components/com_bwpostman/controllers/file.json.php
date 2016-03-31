<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman json file controller for backend, based on joomla com_media.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2016 Boldt Webservice <forum@boldt-webservice.de>
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

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

$m_params = JComponentHelper::getParams('com_media');
define('COM_MEDIA_BASE', JPATH_ROOT . '/' . $m_params->get($path, 'images'));

// Load the helper class
require_once JPATH_ADMINISTRATOR . '/components/com_media/helpers/media.php';

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
	 */
	function upload()
	{
		$params = JComponentHelper::getParams('com_media');

		// Check for request forgeries
		if (!JSession::checkToken('request'))
		{
			$response = array(
				'status' => '0',
				'error' => JText::_('JINVALID_TOKEN')
			);
			echo json_encode($response);
			return;
		}

		// Get the user
		$user = JFactory::getUser();
		JLog::addLogger(array('text_file' => 'upload.error.php'), JLog::ALL, array('upload'));

		// Get some data from the request
		$file	= $this->input->files->get('Filedata', '', 'array');
		$folder	= $this->input->get('folder', '', 'path');

		// Instantiate the media helper
		$mediaHelper = new JHelperMedia;

		if (
			$_SERVER['CONTENT_LENGTH'] > ($params->get('upload_maxsize', 0) * 1024 * 1024) ||
			$_SERVER['CONTENT_LENGTH'] > $mediaHelper->toBytes(ini_get('upload_max_filesize')) ||
			$_SERVER['CONTENT_LENGTH'] > $mediaHelper->toBytes(ini_get('post_max_size')) ||
			$_SERVER['CONTENT_LENGTH'] > $mediaHelper->toBytes(ini_get('memory_limit'))
		)
		{
			$response = array(
				'status' => '0',
				'error' => JText::_('COM_BWPOSTMAN_MEDIA_ERROR_WARNFILETOOLARGE')
			);
			echo json_encode($response);
			return;
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');

		// Make the filename safe
		$file['name'] = JFile::makeSafe($file['name']);

		if (isset($file['name']))
		{
			// The request is valid
			$err = null;

			$filepath = JPath::clean(COM_MEDIA_BASE . '/' . $folder . '/' . strtolower($file['name']));

			if (!MediaHelper::canUpload($file, $err))
			{
				JLog::add('Invalid: ' . $filepath . ': ' . $err, JLog::INFO, 'upload');

				$response = array(
					'status' => '0',
					'error' => JText::_($err)
				);

				echo json_encode($response);
				return;
			}

			// Trigger the onContentBeforeSave event.
			JPluginHelper::importPlugin('content');

			$object_file	= new JObject($file);
			$object_file->filepath = $filepath;

			if (JFile::exists($object_file->filepath))
			{
				// File exists
				JLog::add('File exists: ' . $object_file->filepath . ' by user_id ' . $user->id, JLog::INFO, 'upload');

				$response = array(
					'status' => '0',
					'error' => JText::_('COM_BWPOSTMAN_MEDIA_ERROR_FILE_EXISTS')
				);

				echo json_encode($response);
				return;
			}
			elseif (!$user->authorise('core.create', 'com_media'))
			{
				// File does not exist and user is not authorised to create
				JLog::add('Create not permitted: ' . $object_file->filepath . ' by user_id ' . $user->id, JLog::INFO, 'upload');

				$response = array(
					'status' => '0',
					'error' => JText::_('COM_BWPOSTMAN_MEDIA_ERROR_CREATE_NOT_PERMITTED')
				);

				echo json_encode($response);
				return;
			}

			if (!JFile::upload($object_file->tmp_name, $object_file->filepath))
			{
				// Error in upload
				JLog::add('Error on upload: ' . $object_file->filepath, JLog::INFO, 'upload');

				$response = array(
					'status' => '0',
					'error' => JText::_('COM_BWPOSTMAN_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE')
				);

				echo json_encode($response);
				return;
			}
			else
			{
				JLog::add($folder, JLog::INFO, 'upload');

				$response = array(
					'status' => '1',
					'error' => JText::sprintf('COM_BWPOSTMAN_MEDIA_UPLOAD_COMPLETE', substr($object_file->filepath, strlen(COM_MEDIA_BASE)))
				);

				echo json_encode($response);
				return;
			}
		}
		else
		{
			$response = array(
				'status' => '0',
				'error' => JText::_('COM_BWPOSTMAN_MEDIA_ERROR_BAD_REQUEST')
			);

			echo json_encode($response);
			return;
		}
	}
}
