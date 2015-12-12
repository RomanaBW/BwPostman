<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman templates table for backend.
 *
 * @version 1.2.4 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2015 Boldt Webservice <forum@boldt-webservice.de>
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

/**
 * #__bwpostman_templates table handler
 * Table for storing the templates data
 *
 * @package		BwPostman-Admin
 * @subpackage	Templates
 */
class BwPostmanTableTemplates_Tpl extends JTable
{
	/** @var int Primary Key */
	var $id = null;

	/** @var string title */
	var $title = null;

	/** @var string css */
	var $css = null;

	/** @var string header_tpl */
	var $header_tpl = null;

	/** @var string intro_tpl */
	var $intro_tpl = null;

	/** @var string divider_tpl */
	var $divider_tpl = null;

	/** @var string article_tpl */
	var $article_tpl = null;

	/** @var string readon_tpl */
	var $readon_tpl = null;

	/** @var string footer_tpl */
	var $footer_tpl = null;

	/** @var string button_tpl */
	var $button_tpl = null;

	/**
	 * Constructor
	 *
	 * @param 	db Database object
	 * 
	 * @since 1.1.0
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__bwpostman_templates_tpl', 'id', $db);
	}
}