<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletters lists table for backend.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
 * @author Romana Boldt
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

/**
 * #__bwpostman_templates_tags table handler
 * Table for storing the templates tags
 *
 * @package		BwPostman-Admin
 *
 * @subpackage	Newsletters
 *
 * @since       2.0.0
 */
class BwPostmanTableTemplates_Tags extends JTable
{
	/**
	 * @var int Primary Key Template-ID
	 *
	 * @since       2.0.0
	 */
	public $templates_table_id = null;

	/**
	 * @var integer template tag head
	 *
	 * @since       2.0.0
	 */
	public $tpl_tags_head = null;

	/**
	 * @var string template tag head advanced
	 *
	 * @since       2.0.0
	 */
	public $tpl_tags_head_advanced = null;

	/**
	 * @var integer template tag body
	 *
	 * @since       2.0.0
	 */
	public $tpl_tags_body = null;

	/**
	 * @var string template tag body advanced
	 *
	 * @since       2.0.0
	 */
	public $tpl_tags_body_advanced = null;

	/**
	 * @var integer template tag article
	 *
	 * @since       2.0.0
	 */
	public $tpl_tags_article = null;

	/**
	 * @var string template tag article advanced begin
	 *
	 * @since       2.0.0
	 */
	public $tpl_tags_body_advanced_b = null;

	/**
	 * @var string template tag article advanced end
	 *
	 * @since       2.0.0
	 */
	public $tpl_tags_body_advanced_e = null;

	/**
	 * @var integer template tag readon
	 *
	 * @since       2.0.0
	 */
	public $tpl_tags_readon = null;

	/**
	 * @var string template tag readon advanced
	 *
	 * @since       2.0.0
	 */
	public $tpl_tags_readon_advanced = null;

	/**
	 * @var string template tag legal advanced begin
	 *
	 * @since       2.0.0
	 */
	public $tpl_tags_legal_advanced_b = null;

	/**
	 * @var string template tag legal advanced end
	 *
	 * @since       2.0.0
	 */
	public $tpl_tags_legal_advanced_e = null;

	/**
	 * @var integer
	 *
	 * @since       2.0.0
	 */
	public $standard = 0;

	/**
	 * Constructor
	 *
	 * @param 	JDatabaseDriver  $db Database object
	 *
	 * @since       2.0.0
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__bwpostman_templates_tags', 'templates_table_id', $db);
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
	 * @since       2.0.0
	 */
	public function check()
	{

		// unset standard template if task is save2copy
		$jinput	= JFactory::getApplication()->input;
		$task = $jinput->get('task', 0);
		if ($task == 'save2copy')
		{
			$this->standard = 0;
		}

		// *** prepare the template data ***
		$item = $this;

		return true;
	}
}
