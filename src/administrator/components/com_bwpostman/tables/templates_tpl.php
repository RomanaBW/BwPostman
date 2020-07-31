<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman templates table for backend.
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\Filter\InputFilter;


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
	public $id = null;

	/**
	 * @var string title
	 *
	 * @since       1.1.0
	 */
	public $title = null;

	/**
	 * @var string css
	 *
	 * @since       1.1.0
	 */
	public $css = null;

	/**
	 * @var string header_tpl
	 *
	 * @since       1.1.0
	 */
	public $header_tpl = null;

	/**
	 * @var string intro_tpl
	 *
	 * @since       1.1.0
	 */
	public $intro_tpl = null;

	/**
	 * @var string divider_tpl
	 *
	 * @since       1.1.0
	 */
	public $divider_tpl = null;

	/**
	 * @var string article_tpl
	 *
	 * @since       1.1.0
	 */
	public $article_tpl = null;

	/**
	 * @var string readon_tpl
	 *
	 * @since       1.1.0
	 */
	public $readon_tpl = null;

	/**
	 * @var string footer_tpl
	 *
	 * @since       1.1.0
	 */
	public $footer_tpl = null;

	/**
	 * @var string button_tpl
	 *
	 * @since       1.1.0
	 */
	public $button_tpl = null;

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

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 *
	 * @return boolean True
	 *
	 * @throws Exception
	 *
	 * @since 1.1.0
	 */
	public function check()
	{
		// Sanitize values
		$filter = new InputFilter(array(), array(), 0, 0);

		$this->id          = $filter->clean($this->id, 'UINT');
		$this->title       = trim($filter->clean($this->title));
		$this->css         = $filter->clean($this->css, 'RAW');
		$this->header_tpl  = $filter->clean($this->header_tpl, 'HTML');
		$this->intro_tpl   = $filter->clean($this->intro_tpl, 'HTML');
		$this->divider_tpl = $filter->clean($this->divider_tpl, 'HTML');
		$this->article_tpl = $filter->clean($this->article_tpl, 'HTML');
		$this->readon_tpl  = $filter->clean($this->readon_tpl, 'HTML');
		$this->footer_tpl  = $filter->clean($this->footer_tpl, 'HTML');
		$this->button_tpl  = $filter->clean($this->button_tpl, 'HTML');

		return true;
	}

	/**
	 * Returns the identity (primary key) value of this record
	 *
	 * @return  mixed
	 *
	 * @since  3.0.0
	 */
	public function getId()
	{
		$key = $this->getKeyName();

		return $this->$key;
	}

	/**
	 * Check if the record has a property (applying a column alias if it exists)
	 *
	 * @param string $key key to be checked
	 *
	 * @return  boolean
	 *
	 * @since   3.0.0
	 */
	public function hasField($key)
	{
		$key = $this->getColumnAlias($key);

		return property_exists($this, $key);
	}
}
