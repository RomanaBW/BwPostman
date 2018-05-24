<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman media view for backend, based on joomla com_media.
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

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_media/models/manager.php';

/**
 * HTML View class for the Media component, based on com_media view images
 *
 * @since       1.0.4
 */
class BwPostmanViewMedia extends JViewLegacy
{
	/**
	 * property to hold session
	 *
	 * @var object  $session
	 *
	 * @since       1.0.4
	 */
	public $session;

	/**
	 * property to hold config
	 *
	 * @var object  $config
	 *
	 * @since       1.0.4
	 */
	public $config;

	/**
	 * property to hold state
	 *
	 * @var object  $state
	 *
	 * @since       1.0.4
	 */
	public $state;

	/**
	 * property to hold folder list
	 *
	 * @var array  $folderList
	 *
	 * @since       1.0.4
	 */
	public $folderList;

	/**
	 * property to hold require ftp
	 *
	 * @var boolean  $require_ftp
	 *
	 * @since       1.0.4
	 */
	public $require_ftp;

	/**
	 * property to hold folders id
	 *
	 * @var int $folders_id
	 *
	 * @since       1.0.4
	 */
	public $folders_id;

	/**
	 * property to hold folders
	 *
	 * @var array  $folders
	 *
	 * @since       1.0.4
	 */
	public $folders;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @throws Exception
	 *
	 * @since       1.0.4
	 */
	public function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$config 	= JComponentHelper::getParams('com_media');
		$lang		= JFactory::getLanguage();
		$document	= JFactory::getDocument();
		$mediaModel = JModelLegacy::getInstance('Manager', 'MediaModel');
		$m_params   = JComponentHelper::getParams('com_media');
		define('COM_MEDIA_BASE',    JPATH_ROOT . '/' . $m_params->get('file_path', 'images'));


		if (!$app->isAdmin())
		{
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'warning');
			return;
		}

		$style = $app->getUserStateFromRequest('media.list.layout', 'layout', 'thumbs', 'word');

		// Include jQuery
		JHtml::_('jquery.framework');
		JHtml::_('script', 'bw_postman/popup-imagemanager.js', true, true);

		JHtml::_('stylesheet', 'media/popup-imagemanager.css', array(), true);
		JHtml::_('stylesheet', 'system/mootree.css', array(), true);

		if ($lang->isRtl())
		{
			JHtml::_('stylesheet', 'media/popup-imagemanager_rtl.css', array(), true);
			JHtml::_('stylesheet', 'media/mootree_rtl.css', array(), true);
		}

		if (DIRECTORY_SEPARATOR == '\\')
		{
			$base = str_replace(DIRECTORY_SEPARATOR, "\\\\", COM_MEDIA_BASE);
		}
		else
		{
			$base = COM_MEDIA_BASE;
		}

		$js = "
			var basepath = '" . $base . "';
			var viewstyle = '" . $style . "';
		";
		$document->addScriptDeclaration($js);

		/*
		 * Display form for FTP credentials?
		 * Don't set them here, as there are other functions called before this one if there is any file write operation
		 */
		$ftp = !JClientHelper::hasCredentials('ftp');

		$this->session		= JFactory::getSession();
		$this->config		= $config;
		$state				= $mediaModel->getState();
		$this->state		= &$state;
		$this->folderList	= $mediaModel->getFolderList();
		$this->require_ftp	= $ftp;
		$this->folders_id	= ' id="media-tree"';
		$this->folders		= $mediaModel->getFolderTree();

		parent::display($tpl);

		return $this;
	}
}
