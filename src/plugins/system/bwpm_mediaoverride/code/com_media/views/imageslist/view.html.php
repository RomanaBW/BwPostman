<?php
/**
 * BwPostman MediaOverride Plugin
 *
 * Plugin to override Joomla media list to override view file from
 * package Joomla.Administrator
 * subpackage com_media
 *
 * BwPostman MediaOverride Plugin view file for BwPostman.
 *
 * @version %%version_number%%
 * @package BwPostman MediaOverride Plugin
 * @author Joomla, Romana Boldt, Karl Klostermann
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de> and 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @support https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
 * @license GNU/GPL v3, see LICENSE.txt
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

/**
 * HTML View class for the Media component
 *
 * @since  2.3.0
 */
class MediaViewImagesList extends JViewLegacy
{
	/**
	 * Property to hold base URL
	 *
	 * @var    string
	 *
	 * @since  2.3.0
	 */
	protected $baseURL    = '';

	/**
	 * Property to hold images list
	 *
	 * @var    array
	 *
	 * @since  2.3.0
	 */
	protected $images    = array();

	/**
	 * Property to hold current image
	 *
	 * @var    array
	 *
	 * @since  2.3.0
	 */
	protected $_tmp_img    = array();

	/**
	 * Property to hold folders list
	 *
	 * @var    array
	 *
	 * @since  2.3.0
	 */
	protected $folders    = array();

	/**
	 * Property to hold current folder
	 *
	 * @var    array
	 *
	 * @since  2.3.0
	 */
	protected $_tmp_folder    = array();

	/**
	 * Property to hold state
	 *
	 * @var    array
	 *
	 * @since  2.3.0
	 */
	protected $state    = array();

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @throws \Exception
	 *
	 * @since   2.3.0
	 */
	public function display($tpl = null)
	{
		// Do not allow cache
		JFactory::getApplication()->allowCache(false);

        $list = $this->get('list');

        $images = array();
        foreach($list['images'] as $i) {
            $i->thumb = COM_MEDIA_BASEURL . '/' . $i->path_relative;
            $images[] = $i;
        }
        foreach($list['docs'] as $d) {
            $d->thumb = JURI::root() . 'media/media/images' . substr($d->icon_32, strpos($d->icon_32, '/'));
            $d->width_60 = 32;
            $d->height_60 = 32;
            $images[] = $d;
        }
        foreach($list['videos'] as $v) {
            $v->thumb = JURI::root() . 'media/media/images' . substr($v->icon_32, strpos($v->icon_32, '/'));
            $v->width_60 = 32;
            $v->height_60 = 32;
            $images[] = $v;
        }
		$folders = $this->get('folders');
		$state   = $this->get('state');

		$this->baseURL = COM_MEDIA_BASEURL;
		$this->images  = &$images;
		$this->folders = &$folders;
		$this->state   = &$state;

		$isis = strpos(implode(',',$this->_path['template']), 'isis');

		if ($isis === false) {
	    	$tpl_path = '/tmpl/';
		}
		else
		{
	    	$tpl_path = '/isis/';
		}

		// set path to template on first place
        array_unshift($this->_path['template'], dirname(__FILE__) . $tpl_path);

		return parent::display($tpl);
	}

	/**
	 * Set the active folder
	 *
	 * @param   integer  $index  Folder position
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function setFolder($index = 0)
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
	 * Set the active image
	 *
	 * @param   integer  $index  Image position
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function setImage($index = 0)
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
}
