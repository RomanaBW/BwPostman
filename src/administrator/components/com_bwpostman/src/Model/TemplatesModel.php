<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman templates model for backend.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
 * @author Karl Klostermann
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

namespace BoldtWebservice\Component\BwPostman\Administrator\Model;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use JLogEntry;
use Joomla\Archive\Archive;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwLogger;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\QueryInterface;
use RuntimeException;

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
class TemplatesModel extends ListModel
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
	 * @var	string
	 *
	 * @since       2.1.0
	 */
	protected $dummy;

	/**
	 * @var	string
	 *
	 * @since       2.1.0
	 */
	protected $content;

	/**
	 * @var	string
	 *
	 * @since       2.1.0
	 */
	protected $tmp_path;

	/**
	 * @var	string
	 *
	 * @since       2.1.0
	 */
	protected $imgPath;

	/**
	 * @var	string
	 *
	 * @since       2.1.0
	 */
	protected $basename;

	/**
	 * @var	string
	 *
	 * @since       2.1.0
	 */
	protected $exportId;

	/**
	 * @var	object
	 *
	 * @since       2.4.0
	 */
	protected $logger;

	/**
	 * Constructor
	 * --> handles the pagination and set the Templates key
	 *
	 * @throws Exception
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

		$log_options  = array();
		$this->logger = BwLogger::getInstance($log_options);

		parent::__construct($config);
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	string $name    The table type to instantiate
	 * @param	string $prefix  A prefix for the table class name. Optional.
	 * @param	array  $options Configuration array for model. Optional.
	 *
	 * @return	bool|Table	A database object
	 *
	 * @throws Exception
	 *
	 * @since  1.1.0
	 */
	public function getTable($name = 'Template', $prefix = 'Administrator', $options = array())
	{
		return parent::getTable($name, $prefix, $options);
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
		$app = Factory::getApplication();

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
	protected function getStoreId($id = ''): string
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
	 * @return    false|QueryInterface|object Query
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

		try
		{
			$this->_db->setQuery($this->query);
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_ERROR_GET_LIST_QUERY_ERROR'), 'error');
			return false;
		}

		return $this->query;
	}

	/**
	 * Method to get the joins this query needs
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getQueryJoins()
	{
		$db = $this->_db;
		// Join over the users for the checked out user.
		$this->query->select($db->quoteName('uc.name') . ' AS editor');
		$this->query->join(
			'LEFT',
			$db->quoteName('#__users', 'uc') . ' ON ' . $db->quoteName('uc.id') . ' = ' . $db->quoteName('a.checked_out')
		);

		// Join over the asset groups.
		$this->query->select($db->quoteName('ag.title') . ' AS access_level');
		$this->query->join(
			'LEFT',
			$db->quoteName('#__viewlevels', 'ag') . ' ON ' . $db->quoteName('ag.id') . ' = ' . $db->quoteName('a.access')
		);

		// Join over the users for the author.
		$this->query->select($db->quoteName('ua.name'), ' AS author_name');
		$this->query->join(
			'LEFT',
			$db->quoteName('#__users', 'ua') . ' ON ' . $db->quoteName('ua.id') . ' = ' . $db->quoteName('a.created_by')
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
		if (Factory::getApplication()->isClient('site'))
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
	 * @return 	void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function getFilterByViewLevel()
	{
		if (Factory::getApplication()->isClient('site'))
		{
			$user = Factory::getApplication()->getIdentity();

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
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
//	private function getFilterByComponentPermissions()
//	{
//		$allowed_items  = BwPostmanHelper::getAllowedRecords('template');
//
//		if ($allowed_items != 'all')
//		{
//			$allowed_ids    = implode(',', $allowed_items);
//			$this->query->where($this->_db->quoteName('a.id') . ' IN (' . $allowed_ids . ')');
//		}
//	}

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
		$this->query->where($this->_db->quoteName('a.id') . ' > ' . 0);
	}


	/**
	 * Method to get the filter by selected template format
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
		$this->query->where($this->_db->quoteName('a.id') . ' > ' . 0);

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
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getFilterByArchiveState()
	{
		$this->query->where($this->_db->quoteName('a.archive_flag') . ' = ' . 0);
	}

	/**
	 * Method to get the filter by search word
	 *
	 * @return 	void
	 *
	 * @since   2.0.0
	 */
	private function getFilterBySearchword()
	{
		$filtersearch = $this->getState('filter.search_filter');
		$search       = $this->_db->escape($this->getState('filter.search'), true);

		if (!empty($search))
		{
			$search = '%' . $search . '%';

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
	 * @param array $file
	 *
	 * @return  string|boolean
	 *
	 * @throws Exception
	 * @since 1.1.0
	 */
	public function uploadTplFiles(array $file)
	{
		// Access check.
		$permissions = Factory::getApplication()->getUserState('com_bwpm.permissions', []);

		if (!$permissions['template']['create'])
		{
			return false;
		}

		$msg = '';

		// Clean up filename to get rid of strange characters like spaces etc
		$filename = File::makeSafe($file['name']);

		// Set up the source and destination of the file
		$src         = $file['tmp_name'];
		$ext         = File::getExt($filename);
		$tempPath    = Factory::getApplication()->getConfig()->get('tmp_path');
		$archivename = $tempPath . '/tmp_bwpostman_installtpl.' . $ext;

		// If the file isn't okay, redirect to templates
		if ($file['error'] > 0)
		{
			$this->logger->addEntry(new JLogEntry('tmp filename if template to import: ' . $src, BwLogger::BW_DEBUG, 'templates'));
			$this->logger->addEntry(new JLogEntry('archive name if template to import: ' . $archivename, BwLogger::BW_DEBUG, 'templates'));
			$this->logger->addEntry(new JLogEntry('file array: ' . print_r($file, true), BwLogger::BW_DEBUG, 'templates'));

			//http://de.php.net/features.file-upload.errors
			$msg = Text::_('COM_BWPOSTMAN_TPL_UPLOAD_ERROR_UPLOAD');

			switch ($file['error'])
			{
				case '1':
				case '2':
					$msg .= Text::_('COM_BWPOSTMAN_TPL_UPLOAD_ERROR_UPLOAD_SIZE');
					break;
				case '3':
					$msg .= Text::_('COM_BWPOSTMAN_TPL_UPLOAD_ERROR_UPLOAD_PART');
					break;
				case '4':
					$msg .= Text::_('COM_BWPOSTMAN_TPL_UPLOAD_ERROR_NO_FILE');
					break;
			}
		}
		else
		{ // The file is okay
			// Check if the file has the right extension, we need zip
			if (strtolower(File::getExt($filename)) !== 'zip')
			{
				$msg .= Text::_('COM_BWPOSTMAN_TPL_UPLOAD_ERROR_NO_FILE');
			}
			else
			{ // The file is okay
				if (false === File::upload($src, $archivename, false, true))
				{
					$msg .= Text::_('COM_BWPOSTMAN_TPL_UPLOAD_ERROR_UPLOAD_PART');
				}
			}
		}

		return $msg;
	}

	/**
	 * Method to extract template zip
	 *
	 * @param array $file
	 *
	 * @return  boolean
	 *
	 * @throws Exception
	 *
	 * @since 1.1.0
	 */
	public function extractTplFiles(array $file): bool
	{
		echo '<h4>' . Text::_('COM_BWPOSTMAN_TPL_INSTALL_EXTRACT') . '</h4>';

		$filename    = File::makeSafe($file['name']);
		$ext         = File::getExt($filename);
		$tempPath    = Factory::getApplication()->getConfig()->get('tmp_path');
		$archivename = $tempPath . '/tmp_bwpostman_installtpl.' . $ext;
		$extractdir  = $tempPath . '/tmp_bwpostman_installtpl/';

		$archiveclass = new Archive;

		$adapter = $archiveclass->getAdapter('zip');
		$result  = $adapter->extract($archivename, $extractdir);

		if (!$result) // extract failed
		{
//			$this->delMessage();
			echo '<p class="text-danger">' . Text::_('COM_BWPOSTMAN_TPL_INSTALL_ERROR_EXTRACT') . '</p>';
			return false;
		}

		echo '<p class="text-success">' . Text::_('COM_BWPOSTMAN_TPL_INSTALL_EXTRACT_OK') . '</p>';
		return true;
	}

	/**
	 * Method to install template
	 *
	 * @param string $sql
	 * @param string $step
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since 1.1.0
	 */

	public function installTplFiles(string $sql, string $step): bool
	{
		echo '<h4>' . Text::_('COM_BWPOSTMAN_TPL_INSTALL_TABLE_' . $step) . '</h4>';
		$db = $this->_db;

		$tempPath   = Factory::getApplication()->getConfig()->get('tmp_path');
		$extractdir = $tempPath . '/tmp_bwpostman_installtpl/';

		//we call sql file for the templates data
		$buffer = file_get_contents($extractdir . $sql);

		// Graceful exit and rollback if read not successful
		if ($buffer)
		{
			// Create an array of queries from the sql file
			$queries = DatabaseDriver::splitSql($buffer);

			// Are there queries to process?
			if (count($queries) !== 0)
			{
				// Process each query in the $queries array (split out of sql file).
				foreach ($queries as $this->query)
				{
					$this->query = trim($this->query);

					if ($this->query != '' && $this->query[0] != '#')
					{
						$error      = '';
						$TplTitle   = '';
						$CountTitle = '';

						$this->query = str_replace("`DUMMY`", "'DUMMY'", $this->query);

						try
						{
							$db->setQuery($this->query);
							$db->execute();

							// get last id
							$lastID   = $db->insertid();
							$tplTable = $this->getTable();

							// get template title
							$TplTitle = $tplTable->getTemplateTitle($lastID);

							// reset default value of imported template preventively
							$tplTable->resetDefaultTpl($lastID);

							// count template titles
							$CountTitle = $tplTable->getNbrOfTemplates('', '', $TplTitle);

							// set new template title
							if ($CountTitle > 1) {
								$newTitle = $TplTitle . ' (' . $CountTitle++ . ')';
								$tplTable->setTemplateTitle($lastID, $newTitle);
							}
						}
						catch (RuntimeException $e)
						{
							$error = $e->getMessage();
							Factory::getApplication()->enqueueMessage($error, 'error');
						}

						if ($TplTitle === false || $CountTitle === false || $error !== '')
						{
							echo '<p class="text-danger">' . Text::_('COM_BWPOSTMAN_TPL_INSTALL_TABLE_ERROR') . '</p>';
							echo '<p class="text-danger">' . $error . '</p>';
							return false;
						}
					}
				}//end foreach
			}
		}
		else
		{
			echo '<p class="text-danger">' . Text::_('COM_BWPOSTMAN_TPL_INSTALL_TABLE_ERROR') . '</p>';
			return false;
		}

		echo '<p class="text-success">' . Text::_('COM_BWPOSTMAN_TPL_INSTALL_TABLE_' . $step . '_OK') . '</p>';
		return true;
	}

	/**
	 * Method to copy template thumbnail
	 *
	 * @throws Exception
	 *
	 * @since 1.1.0
	 */
	public function copyThumbsFiles(): bool
	{
		echo '<h4>' . Text::_('COM_BWPOSTMAN_TPL_INSTALL_THUMBS') . '</h4>';

		$tempPath = Factory::getApplication()->getConfig()->get('tmp_path');
		$imagedir = $tempPath . '/tmp_bwpostman_installtpl/images/';

		// make new folder and copy template thumbnails
		$m_params   = ComponentHelper::getParams('com_media');
		$dest       = JPATH_ROOT . '/' . $m_params->get('file_path', 'images') . '/com_bwpostman';
		$dest2      = JPATH_ROOT . '/images/com_bwpostman';
		$media_path = JPATH_ROOT . '/media/com_bwpostman/images/';

		if (!Folder::exists($dest))
		{
			Folder::create($dest);
		}

		if (!File::exists($dest . '/index.html'))
		{
			File::copy(JPATH_ROOT . '/images/index.html', $dest . '/index.html');
		}

		if (!Folder::exists($dest2))
		{
			Folder::create(JPATH_ROOT . '/images/com_bwpostman');
		}

		if (!File::exists(JPATH_ROOT . '/images/com_bwpostman/index.html'))
		{
			File::copy(JPATH_ROOT . '/images/index.html', JPATH_ROOT . '/images/com_bwpostman/index.html');
		}

		$warn  = false;
		$files = Folder::files($imagedir);

		foreach ($files as $file)
		{
			if (!File::exists($dest . '/' . $file))
			{
				File::copy($imagedir . $file, $dest . '/' . $file);
			}

			if (!File::exists($dest2 . '/' . $file))
			{
				File::copy($imagedir . $file, $dest2 . '/' . $file);
			}

			if (!File::exists($media_path . '/' . $file))
			{
				File::copy($imagedir . $file, $media_path . '/' . $file);
			}

//			$this->delMessage();
			$path_now = $dest . '/';

			if (!File::exists($dest . '/' . $file))
			{
				echo '<p class="text-warning">' . Text::sprintf('COM_BWPOSTMAN_TPL_INSTALL_COPY_THUMB_WARNING', $file, $path_now) . '</p>';
				echo '<p class="text-warning">' . Text::sprintf('COM_BWPOSTMAN_TPL_INSTALL_NO_THUMB_WARNING', $file, $path_now) . '</p>';
				$warn = true;
			}
			else
			{
				echo '<p class="text-success">' . Text::sprintf('COM_BWPOSTMAN_TPL_INSTALL_COPY_THUMB_OK', $file, $path_now) . '</p>';
			}

			$path_now = $dest2 . '/';

			if (!File::exists($dest2 . '/' . $file))
			{
				echo '<p class="text-warning">' . Text::sprintf('COM_BWPOSTMAN_TPL_INSTALL_COPY_THUMB_WARNING', $file, $path_now) . '</p>';
				echo '<p class="text-warning">' . Text::sprintf('COM_BWPOSTMAN_TPL_INSTALL_NO_THUMB_WARNING', $file, $path_now) . '</p>';
				$warn = true;
			}
			else
			{
				echo '<p class="text-success">' . Text::sprintf('COM_BWPOSTMAN_TPL_INSTALL_COPY_THUMB_OK', $file, $path_now) . '</p>';
			}

			$path_now = $media_path;

			if (!File::exists($media_path . $file))
			{
				echo '<p class="text-warning">' . Text::sprintf('COM_BWPOSTMAN_TPL_INSTALL_COPY_THUMB_WARNING', $file, $path_now) . '</p>';
				$warn = true;
			}
			else
			{
				echo '<p class="text-success">' . Text::sprintf('COM_BWPOSTMAN_TPL_INSTALL_COPY_THUMB_OK', $file, $path_now) . '</p>';
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
	 * @param array $file
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since 1.1.0
	 */
	public function deleteTempFolder(array $file): bool
	{
		echo '<h4>' . Text::_('COM_BWPOSTMAN_TPL_INSTALL_DEL_FOLDER') . '</h4>';

		$filename    = File::makeSafe($file['name']);
		$ext         = File::getExt($filename);
		$tempPath    = Factory::getApplication()->getConfig()->get('tmp_path');
		$extractdir  = $tempPath . '/tmp_bwpostman_installtpl/';
		$archivename = $tempPath . '/tmp_bwpostman_installtpl.' . $ext;

		$warn = false;

		if (File::exists($archivename))
		{
			File::delete($archivename);
		}

		if (Folder::exists($extractdir))
		{
			Folder::delete($extractdir);
		}

//		$this->delMessage();

		if (File::exists($archivename))
		{
			echo '<p class="text-warning">' . Text::sprintf('COM_BWPOSTMAN_TPL_INSTALL_DEL_FILE_WARNING', $archivename, $tempPath) . '</p>';
			$warn = true;
		}
		else
		{
			echo '<p class="text-success">' . Text::sprintf('COM_BWPOSTMAN_TPL_INSTALL_DEL_FILE_OK', $archivename, $tempPath) . '</p>';
		}

		if (Folder::exists($extractdir))
		{
			echo '<p class="text-warning">' .
				Text::sprintf('COM_BWPOSTMAN_TPL_INSTALL_DEL_FOLDER_WARNING', '/tmp_bwpostman_installtpl/', $tempPath) .
				'</p>';
			$warn = true;
		}
		else
		{
			echo '<p class="text-success">' .
				Text::sprintf('COM_BWPOSTMAN_TPL_INSTALL_DEL_FOLDER_OK', '/tmp_bwpostman_installtpl/', $tempPath) .
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
	 * @throws Exception
	 *
	 * @since 1.1.0
	 */
//	private function delMessage()
//	{
//		$app           = Factory::getApplication();
//		$appReflection = new ReflectionClass(get_class($app));
//		$_messageQueue = $appReflection->getProperty('_messageQueue');
//		$_messageQueue->setAccessible(true);
//
//		$messages = $_messageQueue->getValue($app);
//
//		foreach ($messages as $key => $message)
//		{
//			unset($messages[$key]);
//		}
//
//		$_messageQueue->setValue($app, $messages);
//	}

	/**
	 * Get file name for ZIP archive
	 *
	 * @return  string  The file name
	 *
	 * @throws Exception
	 *
	 * @since   2.1.0
	 */
	public function getBaseName(): string
	{
		// @ToDo: Karl: This method is never called? Perhaps to do at constructor?
		if (!isset($this->basename))
		{
			$app            = Factory::getApplication();
			$jinput         = $app->input;
			$this->exportId = $jinput->get('id');

			$this->tmp_path = $app->get('tmp_path') . '/';

			$basename = 'bwpostman_template_export_id_' . $this->exportId . '.zip';

			$this->basename = $basename;
		}

		return $this->basename;
	}

	/**
	 * Method to call the template export process
	 *
	 * @param integer|null $id     ID to export
	 * @param integer|null $tpl_id template ID
	 *
	 * @return  string
	 *
	 * @throws Exception
	 *
	 * @since	2.1.0
	 */
	public function getExportTpl(int $id = NULL, int $tpl_id = NULL)
	{
		$id          = $this->exportId;
		$zip_created = '';

		if (!isset($this->content))
		{
			$settings = array
			(
				array
				(
					'table' =>  'bwpostman_templates',
					'where1' =>  'id',
					'where2'    =>  'id',
					'insert'    =>  'INSERT IGNORE',
					'nums'      =>  array(
						'id',
						'asset_id',
						'standard',
						'tpl_id',
						'access',
						'published',
						'created_by',
						'modified_by',
						'checked_out',
						'archive_flag',
						'archived_by',
					),
					'j'      =>  1
				),
				array
				(
					'table'  =>  'bwpostman_templates_tpl',
					'where1' =>  'id',
					'where2' =>  'tpl_id',
					'insert' =>  'REPLACE',
					'nums'   =>  array('id'),
					'j'      =>  0
				),
				array
				(
					'table'  =>  'bwpostman_templates_tags',
					'where1' =>  'templates_table_id',
					'where2' =>  'id',
					'insert' =>  'INSERT IGNORE',
					'nums'   =>  array(
						'templates_table_id',
						'tpl_tags_head',
						'tpl_tags_body',
						'tpl_tags_article',
						'tpl_tags_readon',
						'tpl_tags_legal',
					),
					'j'      =>  0
				)
			);

			$this->content = '';

			// prepare sql string
			foreach($settings as $setting)
			{
				$_db   = $this->_db;
				$query = $_db->getQuery(true);

				$query->select('*');
				$query->from($_db->quoteName('#__' . $setting['table'] . ''));
				$query->where($_db->quoteName($setting['where1']) . ' = ' . (($setting['where2'] == 'id') ? (int) $id : (int) $tpl_id));

				try
				{
					$_db->setQuery($query);

					$res = $_db->loadAssoc();
				}
				catch (RuntimeException $e)
				{
					Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
					$this->errRedirect($e->getMessage());
				}

				if (!empty($res))
				{
					// Count fields in row
//					$num_fields = count($res);

					// Field names

					$quote         = "`";
					$fields_quoted = array();
					$field_set     = array_keys($res);

					foreach ($field_set as $field)
					{
						$fields_quoted[] = $quote . $field . $quote;
					}

					$fields = implode(', ', $fields_quoted);
					$this->content .= $setting['insert'] . ' INTO `#__' . $setting['table'] . '`  (' . $fields . ') VALUES' . "\n";

					// Set tpl_id, path to thumbnail
					if ($setting['table'] == 'bwpostman_templates')
					{
						// we cannot reinstall a second standard template
						$res['standard'] = 0;
						$tpl_id        = $res['tpl_id'];
						$this->imgPath = $res['thumbnail'];
					}

					// Values
					$values = array();

					foreach ($res as $key => $value) {

						if (!isset($value))	// NULL
						{
							$values[] = 'NULL';
						}
						elseif (in_array($key, $setting['nums']))     // INT
						{
							$values[] = $value;
						}
						else                                         // STRING
						{
							$values[] = '\''
								. $_db->escape($value)
								. '\'';
						}
					}
					// Set standard template to 0
					if ($setting['table'] == 'bwpostman_templates')
					{
						// If we don't reset id of exported template, a template with an id, which exists on target can't be imported
						// So we let the database manage the id
						$values[0] = 0;
						// asset_id always depends on current system state, so we can't use the exported one
						$values[1] = 0;
					}
					// We need the last insert id from 'bwpostman_templates'
					if ($setting['table'] == 'bwpostman_templates_tags')
					{
						$values[0] = 'LAST_INSERT_ID()';
					}
					$this->content .= '(' . implode(', ', $values) . ');' . "\n";
				}
			}

			$this->dummy = '/* Dummy SQL-Query */' . "\n" . "SELECT id FROM `#__bwpostman_templates_tpl` WHERE `title` = 'DUMMY'";

			$zip_created = $this->createZip();
		}

		return $zip_created;

	}

	/**
	 * Method to create zip archive
	 *
	 * @return  boolean
	 *
	 * @throws Exception
	 *
	 * @since	2.1.0
	 */
	protected function createZip(): bool
	{
		$files =	array(
			array(
				'name' => 'bwp_templates.sql',
				'data' => $this->dummy,
				'time' => time()
			),
			array(
				'name' => 'bwp_templatestpl.sql',
				'data' => $this->content,
				'time' => time()
			)
		);

		// Joomla 4 support
		$imgPath = explode("#", $this->imgPath);
		if (is_array($imgPath))
		{
			$this->imgPath = $imgPath[0];
		}

		// We need thumbnail in tmp_path
		$thumbnail = JPATH_ROOT . '/' . $this->imgPath;

		if (File::exists($thumbnail))
		{
			$lastSlash = strrpos($thumbnail, '/');
			$img       = substr($thumbnail, $lastSlash + 1);

			if (!Folder::exists($this->tmp_path . 'images'))
			{
				Folder::create($this->tmp_path . 'images');
			}

			if (File::exists($this->tmp_path . 'images/' . $img))
			{
				File::delete($this->tmp_path . 'images/' . $img);
			}

			File::copy($thumbnail, $this->tmp_path . 'images/' . $img);

			$files[] =	array(
				'name' => 'images/' . $img,
				'data' => '',
				'time' => time()
			);
		}

		// Create ZIP
		$zipRoot = $this->tmp_path . $this->basename;

		if (File::exists($zipRoot))
		{
			if (!File::delete($zipRoot))
			{
				$errormsg = Text::sprintf('COM_BWPOSTMAN_TPL_ERROR_ZIP_DELETE', $zipRoot);
				$this->errRedirect($errormsg);

				return false;
			}
		}

		$archive  = new Archive;
		$packager = $archive->getAdapter('zip');

		if (!$packager)
		{
			$errormsg = Text::_('COM_BWPOSTMAN_TPL_ERROR_ZIP_ADAPTER');
			$this->errRedirect($errormsg);

			return false;
		}
		elseif (!$packager->create($zipRoot, $files))
		{
			$errormsg = Text::_('COM_BWPOSTMAN_TPL_ERROR_ZIP_CREATE');
			$this->errRedirect($errormsg);

			return false;
		}

		$path = JPATH_ROOT . "/images/com_bwpostman/templates";

		if (!Folder::exists($path))
		{
			Folder::create($path);
		}
		File::copy($zipRoot, JPATH_ROOT . '/images/com_bwpostman/templates/' . $this->basename);

		// Delete thumbnail in tmp folder
		Folder::delete($this->tmp_path . 'images');
		File::delete($zipRoot);

		if (!File::exists(JPATH_ROOT . '/images/com_bwpostman/templates/' . $this->basename))
		{
			$errormsg = Text::_('COM_BWPOSTMAN_TPL_ERROR_COPY_ZIP');
			$this->errRedirect($errormsg);

			return false;
		}

		return true;
	}

	/**
	 * Method to redirect the raw view on errors
	 *
	 * @param string $errormsg
	 * @param string $type
	 *
	 * @throws Exception
	 *
	 * @since	2.1.0
	 */
	protected function errRedirect(string $errormsg, string $type = 'error')
	{
		// Delete thumbnail in tmp folder
		Folder::delete($this->tmp_path . 'images');

		$app = Factory::getApplication();
		$app->enqueueMessage($errormsg, $type);
		$app->redirect(Route::_('index.php?option=com_bwpostman&view=templates', false));
	}
}
