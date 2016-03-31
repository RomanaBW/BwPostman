<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman templates model for backend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Karl Klostermann
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

// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die ('Restricted access');

// Import MODEL object class
jimport('joomla.application.component.modellist');

/**
 * BwPostman templates model
 * Provides a general view of all templates
 *
 * @package		BwPostman-Admin
 * @subpackage	templates
 */
class BwPostmanModelTemplates extends JModelList
{

	/**
	 * Templates data
	 *
	 * @var array
	 *
	 * @since 1.1.0
	 */
	var $_data = null;

	/**
	 * Number of all Templates
	 *
	 * @var integer
	 *
	 * @since 1.1.0
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 *
	 * @since 1.1.0
	 */
	var $_pagination = null;

	/**
	 * Templates search
	 *
	 * @var string
	 *
	 * @since 1.1.0
	 */
	var $_search = null;

	/**
	 * Templates key
	 * --> we need this as identifier for the different Templates filters (e.g. filter_order, search ...)
	 * --> value will be "templates"
	 *
	 * @var	string
	 *
	 * @since 1.1.0
	 */
	var $_key = null;

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
	 * @since   1.1.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
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
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.search_filter');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.published');

		return parent::getStoreId($id);
	}

	/**
	 * Method to build the MySQL query
	 *
	 * @access 	protected
	 * @return 	string Query
	 */
	protected function getListQuery()
	{
		$_db		= $this->_db;
		$query		= $_db->getQuery(true);

		$user		= JFactory::getUser();

		// Select the required fields from the table.
		$query->select(
				$this->getState(
						'list.select',
						'a.id, a.title, a.thumbnail, a.standard, a.description, a.tpl_id, a.checked_out, a.checked_out_time, a.published, a.access, a.created_date, a.created_by'
				)
		);
		$query->from('#__bwpostman_templates AS a');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Join over the users for the author.
		$query->select('ua.name AS author_name');
		$query->join('LEFT', '#__users AS ua ON ua.id = a.created_by');

		// Filter show only the new templates id > 0
		$query->where('a.id > ' . (int) 0);

		// Filter by format.
		if ($format = $this->getState('filter.tpl_id')) {
			if ($format == '1') {
				$query->where('a.tpl_id < 998');
			}
			if ($format == '2') {
				$query->where('a.tpl_id > 997');
			}
		}

		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('a.access = ' . (int) $access);
		}

		// Implement View Level Access
		if (!$user->authorise('core.admin'))
		{
			$groups	= implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN ('.$groups.')');
		}

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '') {
			$query->where('(a.published = 0 OR a.published = 1)');
		}

		// Filter by archive state
		$query->where('a.archive_flag = ' . (int) 0);

		// Filter by search word.
		$filtersearch	= $this->getState('filter.search_filter');
		$search			= $_db->escape($this->getState('filter.search'), true);

		if (!empty($search)) {
			$search			= '%' . $search . '%';
			switch ($filtersearch) {
				case 'description':
					$query->where('a.description LIKE ' . $_db->Quote($search));
					break;
				case 'title_description':
					$query->where('(a.description LIKE ' . $_db->Quote($search) . 'OR a.title LIKE ' . $_db->Quote($search) . ')');
					break;
				case 'title':
					$query->where('a.title LIKE ' . $_db->Quote($search));
					break;
				default:
			}
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction', 'asc');

		//sqlsrv change
		if($orderCol == 'access_level')
			$orderCol = 'ag.title';
		$query->order($_db->escape($orderCol.' '.$orderDirn));

		$_db->setQuery($query);
		return $query;
	}

	/**
	 * Method to call the layout for the template upload and install process
	 *
	 * @access	public
	 */
	public function uploadTplFiles($file)
	{
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
		if ($file['error'] > 0) {

			//http://de.php.net/features.file-upload.errors
			$msg = JText::_('COM_BWPOSTMAN_TPL_UPLOAD_ERROR_UPLOAD');

			switch ($file['error']) {
				case '1':
				case '2': $msg .= JText::_('COM_BWPOSTMAN_TPL_UPLOAD_ERROR_UPLOAD_SIZE');
					break;
				case '3': $msg .= JText::_('COM_BWPOSTMAN_TPL_UPLOAD_ERROR_UPLOAD_PART');
					break;
				case '4': $msg .= JText::_('COM_BWPOSTMAN_TPL_UPLOAD_ERROR_NO_FILE');
					break;
			}

		}
		else { // The file is okay
			// Check if the file has the right extension, we need zip
			if (strtolower(JFile::getExt($filename)) !== 'zip') {
				$msg .= JText::_('COM_BWPOSTMAN_TPL_UPLOAD_ERROR_NO_FILE');
			}
			else { // The file is okay
				if (false === JFile::upload($src, $archivename, false, true)) {
					$msg .= JText::_('COM_BWPOSTMAN_TPL_UPLOAD_ERROR_UPLOAD_PART');
				}
				else { // file is uploaded
				}
			}
		}
		return $msg;
	}

	/**
	 * Method to extract template zip
	 *
	 * @access	public
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
		$new_filename = '/tmp_bwpostman_installtpl.' . $ext;

		$adapter = JArchive::getAdapter('zip');
		$result = $adapter->extract($archivename, $extractdir);

		if (!$result || $result instanceof Exception) // extract failed
		{
			$this->_delMessage();
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
	 */

	public function installTplFiles(&$sql, $step)
	{
		echo '<h4>' . JText::_('COM_BWPOSTMAN_TPL_INSTALL_TABLE_' . $step) . '</h4>';
		$db		= JFactory::getDBO();

		$tempPath = JFactory::getConfig()->get('tmp_path');
		$extractdir = $tempPath . '/tmp_bwpostman_installtpl/';

		//we call sql file for the templates data
		$buffer = file_get_contents($extractdir . $sql);

		// Graceful exit and rollback if read not successful
		if ( $buffer ) {
			// Create an array of queries from the sql file
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);

			// No queries to process
			if (count($queries) != 0) {
				// Process each query in the $queries array (split out of sql file).
				foreach ($queries as $query){
					$query = trim($query);
					if ($query != '' && $query{0} != '#') {
						$db->setQuery($query);
						if ( !$db->query() ) {
							echo '<p class="bw_tablecheck_error">' . JText::_('COM_BWPOSTMAN_TPL_INSTALL_TABLE_ERROR') . '</p>';
							return false;
						}
					}
				}//endfoearch
			}
		}
		else {
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
	 */
	public function copyThumbsFiles($file)
	{
		echo '<h4>' . JText::_('COM_BWPOSTMAN_TPL_INSTALL_THUMBS') . '</h4>';
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$tempPath = JFactory::getConfig()->get('tmp_path');
		$imagedir = $tempPath . '/tmp_bwpostman_installtpl/images/';

		// make new folder and copy template thumbnails
		$dest = JPATH_ROOT.'/images/bw_postman';
		if (!JFolder::exists($dest)) JFolder::create(JPATH_ROOT.'/images/bw_postman');
		if (!JFile::exists(JPATH_ROOT.'/images/bw_postman/index.html')) JFile::copy(JPATH_ROOT.'/images/index.html', JPATH_ROOT.'/images/bw_postman/index.html');

		$warn = false;
		$files = JFolder::files($imagedir);
		foreach ($files as $file)
		{
			if (!JFile::exists(JPATH_ROOT . '/images/bw_postman/' . $file)) JFile::copy($imagedir . $file, JPATH_ROOT.'/images/bw_postman/' . $file);
			if (!JFile::exists(JPATH_ROOT . '/media/bw_postman/images/' . $file)) JFile::copy($imagedir . $file, JPATH_ROOT.'/media/bw_postman/images/' . $file);
			$this->_delMessage();
			$path_now = JPATH_ROOT.'/images/bw_postman/';
			if (!JFile::exists(JPATH_ROOT . '/images/bw_postman/' . $file)) {
				echo '<p class="bw_tablecheck_warn">' . JText::sprintf('COM_BWPOSTMAN_TPL_INSTALL_COPY_THUMB_WARNING', $file, $path_now) . '</p>';
				echo '<p class="bw_tablecheck_warn">' . JText::sprintf('COM_BWPOSTMAN_TPL_INSTALL_NO_THUMB_WARNING', $file, $path_now) . '</p>';
				$warn = true;
			}
			else {
				echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_TPL_INSTALL_COPY_THUMB_OK', $file, $path_now) . '</p>';
			}
			$path_now = JPATH_ROOT.'/media/bw_postman/images/';
			if (!JFile::exists(JPATH_ROOT . '/media/bw_postman/images/' . $file)) {
				echo '<p class="bw_tablecheck_warn">' . JText::sprintf('COM_BWPOSTMAN_TPL_INSTALL_COPY_THUMB_WARNING', $file, $path_now) . '</p>';
				$warn = true;
			}
			else {
				echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_TPL_INSTALL_COPY_THUMB_OK', $file, $path_now) . '</p>';
			}
		}
		if ($warn) {
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
		if (JFile::exists($archivename)) JFile::delete($archivename);
		if (JFolder::exists($extractdir)) JFolder::delete($extractdir);
		$this->_delMessage();
		if (JFile::exists($archivename)) {
			echo '<p class="bw_tablecheck_warn">' . JText::sprintf('COM_BWPOSTMAN_TPL_INSTALL_DEL_FILE_WARNING', $archivename, $tempPath) . '</p>';
			$warn = true;
		}
		else {
			echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_TPL_INSTALL_DEL_FILE_OK', $archivename, $tempPath) . '</p>';
		}
		if (JFolder::exists($extractdir)) {
			echo '<p class="bw_tablecheck_warn">' . JText::sprintf('COM_BWPOSTMAN_TPL_INSTALL_DEL_FOLDER_WARNING', '/tmp_bwpostman_installtpl/', $tempPath) . '</p>';
			$warn = true;
		}
		else {
			echo '<p class="bw_tablecheck_ok">' . JText::sprintf('COM_BWPOSTMAN_TPL_INSTALL_DEL_FOLDER_OK', '/tmp_bwpostman_installtpl/', $tempPath) . '</p>';
		}
		if ($warn) {
			return false;
		}
		else {
			return true;
		}
	}

	/**
	 * Method to delete enqueue messages
	 *
	 * @access	private
	 */
	private function _delMessage()
	{
		$app = JFactory::getApplication();
		$appReflection = new ReflectionClass(get_class($app));
		$_messageQueue = $appReflection->getProperty('_messageQueue');
		$_messageQueue->setAccessible(true);
		$messages = $_messageQueue->getValue($app);
		foreach($messages as $key=>$message) {
			unset($messages[$key]);
		}
		$_messageQueue->setValue($app,$messages);
	}
}
