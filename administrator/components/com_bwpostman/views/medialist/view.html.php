<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman media list view for backend, based on joomla com_media.
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

require_once JPATH_ADMINISTRATOR . '/components/com_media/models/list.php';

// Load the helper class
require_once JPATH_ADMINISTRATOR . '/components/com_media/helpers/media.php';

/**
 * HTML View class for the Media component, based on com_media view images
 *
 * @since       1.0.4
 */
class BwPostmanViewMediaList extends JViewLegacy
{
	/**
	 * property to hold base url
	 *
	 * @var object  $baseURL
	 *
	 * @since       1.0.4
	 */
	public $baseURL;

	/**
	 * property to hold images
	 *
	 * @var array  $images
	 *
	 * @since       1.0.4
	 */
	public $images;

	/**
	 * property to hold documents
	 *
	 * @var array  $documents
	 *
	 * @since       1.0.4
	 */
	public $documents;

	/**
	 * property to hold folders
	 *
	 * @var array  $folders
	 *
	 * @since       1.0.4
	 */
	public $folders;

	/**
	 * property to hold state
	 *
	 * @var object  $state
	 *
	 * @since       1.0.4
	 */
	public $state;

	/**
	 * property to hold temporary folder
	 *
	 * @var object  $_tmp_folder
	 *
	 * @since       1.0.4
	 */
	protected $_tmp_folder;

	/**
	 * property to hold temporary image
	 *
	 * @var object  $_tmp_img
	 *
	 * @since       1.0.4
	 */
	protected $_tmp_img;

	/**
	 * property to hold temporary doc
	 *
	 * @var object  $_tmp_doc
	 *
	 * @since       1.0.4
	 */
	protected $_tmp_doc;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since       1.0.4
	 */
	public function display($tpl = null)
	{
		// Do not allow cache
		JFactory::getApplication()->allowCache(false);

		$jinput			= JFactory::getApplication()->input;
		$mediaParams	= JComponentHelper::getParams('com_media');
		$mediaModel		= JModelLegacy::getInstance('List', 'MediaModel');
		$view			= $jinput->get('view');

		// Set the path definitions
		$popup_upload = $jinput->get('pop_up', null);
		$path = 'file_path';


		if (substr(strtolower($view), 0, 6) == 'images' || $popup_upload == 1)
		{
			$path = 'image_path';
		}

		$media_path = $mediaParams->get($path, 'images');

		define('COM_MEDIA_BASE',    JPATH_ROOT . '/' . $media_path);
		define('COM_MEDIA_BASEURL', JUri::root() . $media_path);

		$lang	= JFactory::getLanguage();

		JHtml::_('stylesheet', 'media/popup-imagelist.css', array(), true);
		if ($lang->isRtl())
		{
			JHtml::_('stylesheet', 'media/popup-imagelist_rtl.css', array(), true);
		}

		$document = JFactory::getDocument();
		$document->addScriptDeclaration("var ImageManager = window.parent.ImageManager;");

		$images		= $mediaModel->getImages();
		$documents	= $mediaModel->getDocuments();
		$folders	= $mediaModel->getFolders();
		$state		= $mediaModel->getState();

		$this->baseURL		= COM_MEDIA_BASEURL;
		$this->images		= &$images;
		$this->documents	= &$documents;
		$this->folders		= &$folders;
		$this->state		= &$state;

		parent::display($tpl);
	}

	/**
	 * Method to set media folder
	 *
	 * @param int $index
	 *
	 * @since       1.0.4
	 */
	function setFolder($index = 0)
	{
		if (isset($this->folders[$index]))
		{
			$this->_tmp_folder = &$this->folders[$index];
		}
		else
		{
			$this->_tmp_folder = new JObject;
		}
	}

	/**
	 * Method to set image
	 *
	 * @param int $index
	 *
	 * @since       1.0.4
	 */
	function setImage($index = 0)
	{
		if (isset($this->images[$index]))
		{
			$this->_tmp_img = &$this->images[$index];
		}
		else
		{
			$this->_tmp_img = new JObject;
		}
	}

	/**
	 * Method to set document
	 *
	 * @param int $index
	 *
	 * @since       1.0.4
	 */
	function setDocument($index = 0)
	{
		if (isset($this->documents[$index]))
		{
			$this->_tmp_doc = &$this->documents[$index];
		}
		else
		{
			$this->_tmp_doc = new JObject;
		}
	}
}
