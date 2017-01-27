<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman file controller for backend, based on joomla com_media.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
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

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

$m_params = JComponentHelper::getParams('com_media');
define('COM_MEDIA_BASE', JPATH_ROOT . '/' . $m_params->get('file_path', 'images'));

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
	 * The folder we are uploading into
	 *
	 * @var		string
	 *
	 * @since       1.0.4
	 */
	protected $folder = '';

	/**
	 * Upload one or more files
	 *
	 * @return	boolean
	 *
	 * @since	1.0.4
	 */
	public function upload()
	{
		// Check for request forgeries
		JSession::checkToken('request') or jexit(JText::_('JINVALID_TOKEN'));
		$params = JComponentHelper::getParams('com_media');

		// Get some data from the request
		$files			= $this->input->files->get('Filedata', '', 'array');
		$this->folder	= $this->input->get('folder', '', 'path');
		$return			= JFactory::getSession()->get('com_bwpostman.media.return_url');

		// Set the redirect
		if ($return)
		{
			$this->setRedirect($return . '&folder=' . $this->folder);
		}
		else
		{
			$this->setRedirect('index.php?option=com_bwpostman&view=media&folder=' . $this->folder);
		}

		// Authorize the user
		if (!$this->authoriseUser('create'))
		{
			return false;
		}

		// Total length of post back data in bytes.
		$contentLength = (int) $_SERVER['CONTENT_LENGTH'];

		// Instantiate the media helper
		$mediaHelper = new JHelperMedia;

		// Maximum allowed size of post back data in MB.
		$postMaxSize = $mediaHelper->toBytes(ini_get('post_max_size'));

		// Maximum allowed size of script execution in MB.
		$memoryLimit = $mediaHelper->toBytes(ini_get('memory_limit'));

		// Check for the total size of post back data.
		if (($postMaxSize > 0 && $contentLength > $postMaxSize)
			|| ($memoryLimit != -1 && $contentLength > $memoryLimit))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_MEDIA_ERROR_WARNUPLOADTOOLARGE'), 'warning');

			return false;
		}

		$uploadMaxSize = $params->get('upload_maxsize', 0) * 1024 * 1024;
		$uploadMaxFileSize = $mediaHelper->toBytes(ini_get('upload_max_filesize'));

		// Perform basic checks on file info before attempting anything
		foreach ($files as &$file)
		{
			$file['name']		= JFile::makeSafe($file['name']);
			$file['filepath']	= JPath::clean(implode(DIRECTORY_SEPARATOR, array(COM_MEDIA_BASE, $this->folder, $file['name'])));

			if (($file['error'] == 1)
				|| ($uploadMaxSize > 0 && $file['size'] > $uploadMaxSize)
				|| ($uploadMaxFileSize > 0 && $file['size'] > $uploadMaxFileSize))
			{
				// File size exceed either 'upload_max_filesize' or 'upload_maxsize'.
				JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_MEDIA_ERROR_WARNFILETOOLARGE'), 'warning');

				return false;
			}

			if (JFile::exists($file['filepath']))
			{
				// A file with this name already exists
				JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_MEDIA_ERROR_FILE_EXISTS'), 'warning');

				return false;
			}

			if (!isset($file['name']))
			{
				// No filename (after the name was cleaned by JFile::makeSafe)
				$this->setRedirect('index.php', JText::_('COM_BWPOSTMAN_MEDIA_INVALID_REQUEST'), 'error');

				return false;
			}
		}

		// Set FTP credentials, if given
		JClientHelper::setCredentialsFromRequest('ftp');
		JPluginHelper::importPlugin('content');

		// Instantiate the media helper
		$mediaHelper = new JHelperMedia;

		foreach ($files as &$file)
		{
			// The request is valid
			$err = null;

			if (!$mediaHelper->canUpload($file, $err))
			{
				// The file can't be uploaded

				return false;
			}

			// Trigger the onContentBeforeSave event.
			$object_file = new JObject($file);

			if (!JFile::upload($object_file->tmp_name, $object_file->filepath))
			{
				// Error in upload
				JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE'), 'warning');

				return false;
			}
			else
			{
				$this->setMessage(JText::sprintf('COM_BWPOSTMAN_MEDIA_UPLOAD_COMPLETE', substr($object_file->filepath, strlen(COM_MEDIA_BASE))));
			}
		}

		return true;
	}

	/**
	 * Check that the user is authorized to perform this action
	 *
	 * @param	string		$action		the action to be performed (create or delete)
	 *
	 * @return	boolean
	 *
	 * @since	1.0.4
	 */
	protected function authoriseUser($action)
	{
		if (!JFactory::getUser()->authorise('core.' . strtolower($action), 'com_media'))
		{
			// User is not authorised
			JFactory::getApplication()->enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_' . strtoupper($action) . '_NOT_PERMITTED'), 'warning');

			return false;
		}

		return true;
	}


	/**
	 * Used as a callback for array_map, turns the multi-file input array into a sensible array of files
	 * Also, removes illegal characters from the 'name' and sets a 'filepath' as the final destination of the file
	 *
	 * @param	string	- file name			($files['name'])
	 * @param	string	- file type			($files['type'])
	 * @param	string	- temporary name	($files['tmp_name'])
	 * @param	string	- error info		($files['error'])
	 * @param	string	- file size			($files['size'])
	 *
	 * @return	array
	 * @access	protected
	 */
	protected function reformatFilesArray($name, $type, $tmp_name, $error, $size)
	{
		$name = JFile::makeSafe($name);
		return array(
			'name'		=> $name,
			'type'		=> $type,
			'tmp_name'	=> $tmp_name,
			'error'		=> $error,
			'size'		=> $size,
			'filepath'	=> JPath::clean(implode(DIRECTORY_SEPARATOR, array(COM_MEDIA_BASE, $this->folder, $name)))
		);
	}
}
