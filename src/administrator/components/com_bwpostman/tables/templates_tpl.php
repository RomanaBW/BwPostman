<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman templates table for backend.
 *
 * @version 2.0.0 bwpm
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

/**
 * #__bwpostman_templates table handler
 * Table for storing the templates data
 *
 * @package		BwPostman-Admin
 *
 * @subpackage	Templates
 *
 * @since       1.1.0
 */
class BwPostmanTableTemplates_Tpl extends JTable
{
	/**
	 * @var int Primary Key
	 *
	 * @since       1.1.0
	 */
	var $id = null;

	/**
	 * @var string title
	 *
	 * @since       1.1.0
	 */
	var $title = null;

	/**
	 * @var string css
	 *
	 * @since       1.1.0
	 */
	var $css = null;

	/**
	 * @var string header_tpl
	 *
	 * @since       1.1.0
	 */
	var $header_tpl = null;

	/**
	 * @var string intro_tpl
	 *
	 * @since       1.1.0
	 */
	var $intro_tpl = null;

	/**
	 * @var string divider_tpl
	 *
	 * @since       1.1.0
	 */
	var $divider_tpl = null;

	/**
	 * @var string article_tpl
	 *
	 * @since       1.1.0
	 */
	var $article_tpl = null;

	/**
	 * @var string readon_tpl
	 *
	 * @since       1.1.0
	 */
	var $readon_tpl = null;

	/**
	 * @var string footer_tpl
	 *
	 * @since       1.1.0
	 */
	var $footer_tpl = null;

	/**
	 * @var string button_tpl
	 *
	 * @since       1.1.0
	 */
	var $button_tpl = null;

	/**
	 * Constructor
	 *
	 * @param 	JDatabaseDriver  $db Database object
	 *
	 * @since 1.1.0
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__bwpostman_templates_tpl', 'id', $db);
	}
}
