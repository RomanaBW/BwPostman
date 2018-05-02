<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman templates model for backend.
 *
 * @version 2.0.1 bwpm
 * @package BwPostman-Admin
 * @author Karl Klostermann
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Import MODEL object class
jimport('joomla.application.component.modellist');

/**
 * BwPostman templates model
 * Provides a general view of all templates
 *
 * @package		BwPostman-Admin
 *
 * @subpackage	templates
 *
 * @since 1.1.0
 */
class BwPostmanModelTemplates extends JModelList
{
	/**
	 * The query object
	 *
	 * @var	object
	 *
	 * @since       2.0.0
	 */
	protected $query;

	/**
	 * Constructor
	 * --> handles the pagination and set the Templates key
	 *
	 * @since 1.1.0
	 */
	public function __construct()
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'description', 'a.description',
				'tpl_id', 'a.tpl_id',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'published', 'a.published',
				'access', 'a.access', 'access_level',
				'created_date', 'a.created_date',
				'created_by', 'a.created_by'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   1.1.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		$layout = $app->input->get('layout');
		if ($layout)
		{
			$this->context .= '.' . $layout;
		}

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$filtersearch = $this->getUserStateFromRequest($this->context . '.filter.search_filter', 'filter_search_filter');
		$this->setState('filter.search_filter', $filtersearch);

		$access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access');
		$this->setState('filter.access', $access);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$tpl_id = $this->getUserStateFromRequest($this->context . '.filter.tpl_id', 'filter_tpl_id', '');
		$this->setState('filter.tpl_id', $tpl_id);

		// List state information.
		parent::populateState('a.title', 'asc');

		$limitstart = $app->input->get->post->get('limitstart');
		$this->setState('list.start', $limitstart);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 *
	 * @since	1.1.0
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':' . $this->getState('filter.search');
		$id	.= ':' . $this->getState('filter.search_filter');
		$id	.= ':' . $this->getState('filter.access');
		$id	.= ':' . $this->getState('filter.published');

		return parent::getStoreId($id);
	}

	/**
	 * Method to build the MySQL query
	 *
	 * @access 	protected
	 *
	 * @return 	string Query
	 *
	 * @throws Exception
	 *
	 * @since 1.1.0
	 */
	protected function getListQuery()
	{
		$this->query = $this->_db->getQuery(true);

		// Select the required fields from the table.
		$this->query->select(
			$this->getState(
				'list.select',
				'a.id, a.title, a.thumbnail, a.standard, a.description, a.tpl_id, 
				a.checked_out, a.checked_out_time, a.published, a.access, a.created_date, a.created_by'
			)
		);
		$this->query->from($this->_db->quoteName('#__bwpostman_templates', 'a'));

		$this->getQueryJoins();
		$this->getQueryWhere();
		$this->getQueryOrder();

		$this->_db->setQuery($this->query);

		return $this->query;
	}

	/**
	 * Method to get the joins this query needs
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getQueryJoins()
	{
		// Join over the users for the checked out user.
		$this->query->select($this->_db->quoteName('uc.name') . ' AS editor');
		$this->query->join(
			'LEFT',
			$this->_db->quoteName('#__users', 'uc') . ' ON ' . $this->_db->quoteName('uc.id') . ' = ' . $this->_db->quoteName('a.checked_out')
		);

		// Join over the asset groups.
		$this->query->select($this->_db->quoteName('ag.title') . ' AS access_level');
		$this->query->join(
			'LEFT',
			$this->_db->quoteName('#__viewlevels', 'ag') . ' ON ' . $this->_db->quoteName('ag.id') . ' = ' . $this->_db->quoteName('a.access')
		);

		// Join over the users for the author.
		$this->query->select($this->_db->quoteName('ua.name'), ' AS author_name');
		$this->query->join(
			'LEFT',
			$this->_db->quoteName('#__users', 'ua') . ' ON ' . $this->_db->quoteName('ua.id') . ' = ' . $this->_db->quoteName('a.created_by')
		);
	}

	/**
	 * Method to build the MySQL query 'where' part
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function getQueryWhere()
	{
		$this->getFilterByAccessLevelFilter();
		$this->getFilterByViewLevel();
//		$this->getFilterByComponentPermissions();
		$this->getFilterByNewTemplates();
		$this->getFilterByTemplateFormat();
		$this->getFilterByPublishedState();
		$this->getFilterByArchiveState();
		$this->getFilterBySearchword();
	}

	/**
	 * Method to build the MySQL query 'order' part
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getQueryOrder()
	{
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction', 'asc');

		//sqlsrv change
		if ($orderCol == 'access_level')
		{
			$orderCol = 'ag.title';
		}

		$this->query->order($this->_db->quoteName($this->_db->escape($orderCol)) . ' ' . $this->_db->escape($orderDirn));
	}

	/**
	 * Method to get the filter by access level
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function getFilterByAccessLevelFilter()
	{
		if (JFactory::getApplication()->isSite())
		{
			$access = $this->getState('filter.access');
			if ($access)
			{
				$this->query->where($this->_db->quoteName('a.access') . ' = ' . (int) $access);
			}
		}
	}

	/**
	 * Method to get the filter by Joomla view level
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function getFilterByViewLevel()
	{
		if (JFactory::getApplication()->isSite())
		{
			$user = JFactory::getUser();

			if (!$user->authorise('core.admin'))
			{
				$groups = implode(',', $user->getAuthorisedViewLevels());
				$this->query->where($this->_db->quoteName('a.access') . ' IN (' . $groups . ')');
			}
		}
	}

	/**
	 * Method to get the filter by BwPostman permissions
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getFilterByComponentPermissions()
	{
		$allowed_items  = BwPostmanHelper::getAllowedRecords('template');

		if ($allowed_items != 'all')
		{
			$allowed_ids    = implode(',', $allowed_items);
			$this->query->where($this->_db->quoteName('a.id') . ' IN (' . $allowed_ids . ')');
		}
	}

	/**
	 * Method to get only new templates
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getFilterByNewTemplates()
	{
		// Filter show only the new templates id > 0
		$this->query->where($this->_db->quoteName('a.id') . ' > ' . (int) 0);
	}


	/**
	 * Method to get the filter by selected template format
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function getFilterByTemplateFormat()
	{
		// Filter show only the new templates id > 0
		$this->query->where($this->_db->quoteName('a.id') . ' > ' . (int) 0);

		// Filter by format.
		$format = $this->getState('filter.tpl_id');
		if ($format)
		{
			if ($format == '1')
			{
				$this->query->where($this->_db->quoteName('a.tpl_id') . ' < 998');
			}

			if ($format == '2')
			{
				$this->query->where($this->_db->quoteName('a.tpl_id') . ' > 997');
			}
		}
	}

	/**
	 * Method to get the filter by published state
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getFilterByPublishedState()
	{
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$this->query->where($this->_db->quoteName('a.published') . ' = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$this->query->where('(' . $this->_db->quoteName('a.published') . ' = 0 OR ' . $this->_db->quoteName('a.published') . ' = 1)');
		}
	}

	/**
	 * Method to get the filter by archived state
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getFilterByArchiveState()
	{
		$this->query->where($this->_db->quoteName('a.archive_flag') . ' = ' . (int) 0);
	}

	/**
	 * Method to get the filter by search word
	 *
	 * @access 	private
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getFilterBySearchword()
	{
		$filtersearch = $this->getState('filter.search_filter');
		$search			= $this->_db->escape($this->getState('filter.search'), true);

		if (!empty($search))
		{
			$search			= '%' . $search . '%';

			switch ($filtersearch)
			{
				case 'description':
					$this->query->where($this->_db->quoteName('a.description') . ' LIKE ' . $this->_db->quote($search, false));
					break;
				case 'title_description':
					$this->query->where(
						'(' . $this->_db->quoteName('a.description') . ' LIKE ' . $this->_db->quote($search, false) .
						' OR ' . $this->_db->quoteName('a.title') . ' LIKE ' . $this->_db->quote($search, false) . ')'
					);
					break;
				case 'title':
					$this->query->where($this->_db->quoteName('a.title') . ' LIKE ' . $this->_db->quote($search, false));
					break;
				default:
			}
		}
	}

	/**
	 * Method to call the layout for the template upload and install process
	 *
	 * @access	public
	 *
	 * @param   string
	 *
	 * @return  string
	 *
	 * @throws Exception
	 *
	 * @since 1.1.0
	 */
	public function uploadTplFiles($file)
	{
		// Access check.
		$permissions = JFactory::getApplication()->getUserState('com_bwpm.permissions');
		if (!$permissions['template']['create'])
		{
			return false;
		}

		$msg = '';

		// Import filesystem libraries. Perhaps not necessary, but does not hurt
		jimport('joomla.filesystem.file');

		// Clean up filename to get rid of strange characters like spaces etc
		$filename = JFile::makeSafe($file['name']);

		// Set up the source and destination of the file
		$src = $file['tmp_name'];
		$ext = JFile::getExt($filename);
		$tempPath = JFactory::getConfig()->get('tmp_path');
		$archivename = $tempPath . '/tmp_bwpostman_installtpl.' . $ext;

		// If the file isn't okay, redirect to templates
		if ($file['error'] > 0)
		{
			//http://de.php.net/features.file-upload.errors
			$msg = JText::_('COM_BWPOSTMAN_TPL_UPLOAD_ERROR_UPLOAD');

			switch ($file['error'])
			{
				case '1':
				case '2':
					$msg .= JText::_('COM_BWPOSTMAN_TPL_UPLOAD_ERROR_UPLOAD_SIZE');
					break;
				case '3':
					$msg .= JText::_('COM_BWPOSTMAN_TPL_UPLOAD_ERROR_UPLOAD_PART');
					break;
				case '4':
					$msg .= JText::_('COM_BWPOSTMAN_TPL_UPLOAD_ERROR_NO_FILE');
					break;
			}
		}
		else
		{ // The file is okay
			// Check if the file has the right extension, we need zip
			if (strtolower(JFile::getExt($filename)) !== 'zip')
			{
				$msg .= JText::_('COM_BWPOSTMAN_TPL_UPLOAD_ERROR_NO_FILE');
			}
			else
			{ // The file is okay
				if (false === JFile::upload($src, $archivename, false, true))
				{
					$msg .= JText::_('COM_BWPOSTMAN_TPL_UPLOAD_ERROR_UPLOAD_PART');
				}
			}
		}

		return $msg;
	}

	/**
	 * Method to extract template zip
	 *
	 * @access	public
	 *
	 * @param   string
	 *
	 * @return  boolean
	 *
	 * @throws Exception
	 *
	 * @since 1.1.0
	 */
	public function extractTplFiles($file)
	{
		echo '<h4>' . JText::_('COM_BWPOSTMAN_TPL_INSTALL_EXTRACT') . '</h4>';
		// Import filesystem libraries. Perhaps not necessary, but does not hurt
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$filename = JFile::makeSafe($file['name']);
		$ext = JFile::getExt($filename);
		$tempPath = JFactory::getConfig()->get('tmp_path');
		$archivename = $tempPath . '/tmp_bwpostman_installtpl.' . $ext;
		$extractdir = $tempPath . '/tmp_bwpostman_installtpl/';

		$adapter = JArchive::getAdapter('zip');
		$result = $adapter->extract($archivename, $extractdir);

		if (!$result || $result instanceof Exception) // extract failed
		{
			$this->delMessage();
			echo '<p class="bw_tablecheck_error">' . JText::_('COM_BWPOSTMAN_TPL_INSTALL_ERROR_EXTRACT') . '</p>';
			return false;
		}

		echo '<p class="bw_tablecheck_ok">' . JText::_('COM_BWPOSTMAN_TPL_INSTALL_EXTRACT_OK') . '</p>';
		return true;
	}

	/**
	 * Method to install template
	 *
	 * @param string    $sql
	 * @param string    $step
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since 1.1.0
	 */

	public function installTplFiles(&$sql, $step)
	{
		echo '<h4>' . JText::_('COM_BWPOSTMAN_TPL_INSTALL_TABLE_' . $step) . '</h4>';
		$db		= JFactory::getDbo();

		$tempPath = JFactory::getConfig()->get('tmp_path');
		$extractdir = $tempPath . '/tmp_bwpostman_installtpl/';

		//we call sql file for the templates data
		$buffer = file_get_contents($extractdir . $sql);

		// Graceful exit and rollback if read not successful
		if ($buffer)
		{
			// Create an array of queries from the sql file
			jimport('joomla.installer.helper');
			$queries = JDatabaseDriver::splitSql($buffer);

			// No queries to process
			if (count($queries) != 0)
			{
				// Process each query in the $queries array (split out of sql file).
				foreach ($queries as $this->query)
				{
					$this->query = trim($this->query);
					if ($this->query != '' && $this->query{0} != '#')
					{
						$db->setQuery($this->query);

						try
						{
							$db->execute();
						}
						catch (RuntimeException $e)
						{
							JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
						}
					}
				}//end foreach
			}
		}
		else
		{
			echo '<p class="bw_tablecheck_error">' . JText::_('COM_BWPOSTMAN_TPL_INSTALL_TABLE_ERROR') . '</p>';
			return false;
		}

		echo '<p class="bw_tablecheck_ok">' . JText::_('COM_BWPOSTMAN_TPL_INSTALL_TABLE_' . $step . '_OK') . '</p>';
		return true;
	}

	/**
	 * Method to copy template thumbnail
	 *
	 * @access	public
	 *
	 * @throws Exception
	 *
	 * @since 1.1.0
	 */
	public function copyThumbsFiles()
	{
		echo '<h4>' . JText::_('COM_BWPOSTMAN_TPL_INSTALL_THUMBS') . '</h4>';
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$tempPath = JFactory::getConfig()->get('tmp_path');
		$imagedir = $tempPath . '/tmp_bwpostman_installtpl/images/';

		// make new folder and copy template thumbnails
		$m_params   = JComponentHelper::getParams('com_media');
		$dest       = JPATH_ROOT . '/' . $m_params->get('file_path', 'images') . '/bw_postman';
		$dest2		= JPATH_ROOT . '/images/bw_postman';
		$media_path = JPATH_ROOT . '/media/bw_postman/images/';

		if (!JFolder::exists($dest))
		{
			JFolder::create($dest);
		}

		if (!JFile::exists($dest . '/index.html'))
		{
			JFile::copy(JPATH_ROOT . '/images/index.html', $dest . '/index.html');
		}

		if (!JFolder::exists($dest2))
		{
			JFolder::create(JPATH_ROOT . '/images/bw_postman');
		}

		if (!JFile::exists(JPATH_ROOT . '/images/bw_postman/index.html'))
		{
			JFile::copy(JPATH_ROOT . '/images/index.html', JPATH_ROOT . '/images/bw_postman/index.html');
		}

		$warn = false;
		$files = JFolder::files($imagedir);
		foreach ($files as $file)
		{
			if (!JFile::exists($dest . '/' . $file))
			{
				JFile::copy($imagedir . $file, $dest . '/' . $file);
			}

			if (!JFile::exists($dest2 . '/' . $file))
			{
				JFile::copy($imagedir . $file, $dest2 . '/' . $file);
			}

			if (!JFile::exists($media_path . '/' . $file))
			{
				JFile::copy($imagedir . $file, $media_path . '/' . $file);
			}

			$this->delMessage();
			$path_now = $dest . '/';
			if (!JFile::exists($dest . '/' . $file))
			{
				echo '<p class="bw_tablecheck_warn">' . JText::sprintf('COM_BWPOSTMAN_TPL_INSTALL_COPY_THUMB_WARNING', $file, $path_now) . '</p>';
				echo '<p class="bw_tablecheck_warn">' . JText::sprintf('COM_BWPOSTMAN_TPL_INSTALL_NO_THUMB_WARNING', $file, $path_now) . '</p>';
				$warn = true;
			}
			else
			{
				echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_TPL_INSTALL_COPY_THUMB_OK', $file, $path_now) . '</p>';
			}

			$path_now = $dest2 . '/';
			if (!JFile::exists($dest2 . '/' . $file))
			{
				echo '<p class="bw_tablecheck_warn">' . JText::sprintf('COM_BWPOSTMAN_TPL_INSTALL_COPY_THUMB_WARNING', $file, $path_now) . '</p>';
				echo '<p class="bw_tablecheck_warn">' . JText::sprintf('COM_BWPOSTMAN_TPL_INSTALL_NO_THUMB_WARNING', $file, $path_now) . '</p>';
				$warn = true;
			}
			else
			{
				echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_TPL_INSTALL_COPY_THUMB_OK', $file, $path_now) . '</p>';
			}

			$path_now = $media_path;
			if (!JFile::exists($media_path . $file))
			{
				echo '<p class="bw_tablecheck_warn">' . JText::sprintf('COM_BWPOSTMAN_TPL_INSTALL_COPY_THUMB_WARNING', $file, $path_now) . '</p>';
				$warn = true;
			}
			else
			{
				echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_TPL_INSTALL_COPY_THUMB_OK', $file, $path_now) . '</p>';
			}
		}

		if ($warn)
		{
			return false;
		}
		else {
			return true;
		}
	}

	/**
	 * Method to delete temp folder
	 *
	 * @access	public
	 *
	 * @param   array  $file
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since 1.1.0
	 */
	public function deleteTempFolder($file)
	{
		echo '<h4>' . JText::_('COM_BWPOSTMAN_TPL_INSTALL_DEL_FOLDER') . '</h4>';
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$filename = JFile::makeSafe($file['name']);
		$ext = JFile::getExt($filename);
		$tempPath = JFactory::getConfig()->get('tmp_path');
		$extractdir = $tempPath . '/tmp_bwpostman_installtpl/';
		$archivename = $tempPath . '/tmp_bwpostman_installtpl.' . $ext;

		$warn = false;
		if (JFile::exists($archivename))
		{
			JFile::delete($archivename);
		}

		if (JFolder::exists($extractdir))
		{
			JFolder::delete($extractdir);
		}

		$this->delMessage();
		if (JFile::exists($archivename))
		{
			echo '<p class="bw_tablecheck_warn">' . JText::sprintf('COM_BWPOSTMAN_TPL_INSTALL_DEL_FILE_WARNING', $archivename, $tempPath) . '</p>';
			$warn = true;
		}
		else
		{
			echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_TPL_INSTALL_DEL_FILE_OK', $archivename, $tempPath) . '</p>';
		}

		if (JFolder::exists($extractdir))
		{
			echo '<p class="bw_tablecheck_warn">' .
				JText::sprintf('COM_BWPOSTMAN_TPL_INSTALL_DEL_FOLDER_WARNING', '/tmp_bwpostman_installtpl/', $tempPath) .
				'</p>';
			$warn = true;
		}
		else
		{
			echo '<p class="bw_tablecheck_ok">' .
				JText::sprintf('COM_BWPOSTMAN_TPL_INSTALL_DEL_FOLDER_OK', '/tmp_bwpostman_installtpl/', $tempPath) .
				'</p>';
		}

		if ($warn)
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Method to delete enqueue messages
	 *
	 * @access	private
	 *
	 * @throws Exception
	 *
	 * @since 1.1.0
	 */
	private function delMessage()
	{
		$app = JFactory::getApplication();
		$appReflection = new ReflectionClass(get_class($app));
		$_messageQueue = $appReflection->getProperty('_messageQueue');
		$_messageQueue->setAccessible(true);
		$messages = $_messageQueue->getValue($app);
		foreach ($messages as $key => $message)
		{
			unset($messages[$key]);
		}

		$_messageQueue->setValue($app, $messages);
	}
}
