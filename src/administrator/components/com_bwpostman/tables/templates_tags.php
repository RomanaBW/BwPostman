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

use Joomla\CMS\Factory;
use Joomla\Filter\InputFilter;

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
	public $tpl_tags_article_advanced_b = null;

	/**
	 * @var string template tag article advanced end
	 *
	 * @since       2.0.0
	 */
	public $tpl_tags_article_advanced_e = null;

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
	 * @var integer template tag legal
	 *
	 * @since       2.0.0
	 */
	public $tpl_tags_legal = null;

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
		$jinput = Factory::getApplication()->input;
		$task   = $jinput->get('task', 0);

		// Sanitize values
		$filter = new InputFilter(array(), array(), 0, 0);

		$this->templates_table_id          = $filter->clean($this->templates_table_id, 'UINT');
		$this->tpl_tags_head               = $filter->clean($this->tpl_tags_head, 'UINT');
		$this->tpl_tags_head_advanced      = $filter->clean($this->tpl_tags_head_advanced, 'HTML');
		$this->tpl_tags_body               = $filter->clean($this->tpl_tags_body, 'UINT');
		$this->tpl_tags_body_advanced      = $filter->clean($this->tpl_tags_body_advanced, 'HTML');
		$this->tpl_tags_article            = $filter->clean($this->tpl_tags_article, 'UINT');
		$this->tpl_tags_article_advanced_b = $filter->clean($this->tpl_tags_article_advanced_b, 'HTML');
		$this->tpl_tags_article_advanced_e = $filter->clean($this->tpl_tags_article_advanced_e, 'HTML');
		$this->tpl_tags_readon             = $filter->clean($this->tpl_tags_readon, 'UINT');
		$this->tpl_tags_readon_advanced    = $filter->clean($this->tpl_tags_readon_advanced, 'HTML');
		$this->tpl_tags_legal              = $filter->clean($this->tpl_tags_legal, 'UINT');
		$this->tpl_tags_legal_advanced_b   = $filter->clean($this->tpl_tags_legal_advanced_b, 'HTML');
		$this->tpl_tags_legal_advanced_e   = $filter->clean($this->tpl_tags_legal_advanced_e, 'HTML');

		if ($task == 'save2copy')
		{
			$this->standard = 0;
		}

		return true;
	}

	/**
	 * Method to get the template assets which are used to compose a newsletter
	 *
	 * @access	public
	 *
	 * @param   int    $template_id     template id
	 *
	 * @return	array
	 *
	 * @throws Exception
	 *
	 * @since	2.3.0 here (moved from newsletter model there since 2.0.0)
	 */
	public function getTemplateAssets($template_id)
	{
		$tpl_assets = array();

		$db	= $this->_db;
		$query	= $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('templates_table_id') . ' = ' . (int) $template_id);
		$db->setQuery($query);

		try
		{
			$tpl_assets = $db->loadAssoc();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $tpl_assets;
	}

	/**
	 * Method to save template tags for user-made html templates
	 *
	 *
	 * @param   array   $data  The form data.
	 * @param   integer $tplId The id of the template the tags belongs to
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws Exception
	 *
	 * @since   2.4.0
	 */
	public function saveTags($data, $tplId)
	{
		$db	= $this->_db;
		$query	= $db->getQuery(true);

		if (empty($data['templates_table_id']))
		{
			$query->insert($db->quoteName($this->_tbl));
			$query->columns(
				array(
					$db->quoteName('templates_table_id'),
					$db->quoteName('tpl_tags_head'),
					$db->quoteName('tpl_tags_head_advanced'),
					$db->quoteName('tpl_tags_body'),
					$db->quoteName('tpl_tags_body_advanced'),
					$db->quoteName('tpl_tags_article'),
					$db->quoteName('tpl_tags_article_advanced_b'),
					$db->quoteName('tpl_tags_article_advanced_e'),
					$db->quoteName('tpl_tags_readon'),
					$db->quoteName('tpl_tags_readon_advanced'),
					$db->quoteName('tpl_tags_legal'),
					$db->quoteName('tpl_tags_legal_advanced_b'),
					$db->quoteName('tpl_tags_legal_advanced_e'),
				)
			);
			$query->values(
				(int) $tplId . ',' .
				(int) $data['tpl_tags_head'] . ',' .
				$db->quote($data['tpl_tags_head_advanced']) . ',' .
				(int) $data['tpl_tags_body'] . ',' .
				$db->quote($data['tpl_tags_body_advanced']) . ',' .
				(int) $data['tpl_tags_article'] . ',' .
				$db->quote($data['tpl_tags_article_advanced_b']) . ',' .
				$db->quote($data['tpl_tags_article_advanced_e']) . ',' .
				(int) $data['tpl_tags_readon'] . ',' .
				$db->quote($data['tpl_tags_readon_advanced']) . ',' .
				(int) $data['tpl_tags_legal'] . ',' .
				$db->quote($data['tpl_tags_legal_advanced_b']) . ',' .
				$db->quote($data['tpl_tags_legal_advanced_e'])
			);
		}
		else
		{
			$query->update($db->quoteName($this->_tbl));

			$query->set($db->quoteName('tpl_tags_head') . ' = ' . (int) $data['tpl_tags_head']);
			$query->set($db->quoteName('tpl_tags_head_advanced') . ' = ' . $db->quote($data['tpl_tags_head_advanced']));
			$query->set($db->quoteName('tpl_tags_body') . ' = ' . (int) $data['tpl_tags_body']);
			$query->set($db->quoteName('tpl_tags_body_advanced') . ' = ' . $db->quote($data['tpl_tags_body_advanced']));
			$query->set($db->quoteName('tpl_tags_article') . ' = ' . (int) $data['tpl_tags_article']);
			$query->set($db->quoteName('tpl_tags_article_advanced_b') . ' = ' . $db->quote($data['tpl_tags_article_advanced_b']));
			$query->set($db->quoteName('tpl_tags_article_advanced_e') . ' = ' . $db->quote($data['tpl_tags_article_advanced_e']));
			$query->set($db->quoteName('tpl_tags_readon') . ' = ' . (int) $data['tpl_tags_readon']);
			$query->set($db->quoteName('tpl_tags_readon_advanced') . ' = ' . $db->quote($data['tpl_tags_readon_advanced']));
			$query->set($db->quoteName('tpl_tags_legal') . ' = ' . (int) $data['tpl_tags_legal']);
			$query->set($db->quoteName('tpl_tags_legal_advanced_b') . ' = ' . $db->quote($data['tpl_tags_legal_advanced_b']));
			$query->set($db->quoteName('tpl_tags_legal_advanced_e') . ' = ' . $db->quote($data['tpl_tags_legal_advanced_e']));

			$query->where($db->quoteName('templates_table_id') . ' = ' . $data['id']);
		}

		$db->setQuery($query);

		try
		{
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return true;
	}

	/**
	 * Returns the identity (primary key) value of this record
	 *
	 * @return  mixed
	 *
	 * @since  2.4.0
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
	 * @since   2.4.0
	 */
	public function hasField($key)
	{
		$key = $this->getColumnAlias($key);

		return property_exists($this, $key);
	}
}
