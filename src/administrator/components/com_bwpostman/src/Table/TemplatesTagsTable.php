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

namespace BoldtWebservice\Component\BwPostman\Administrator\Table;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Versioning\VersionableTableInterface;
use Joomla\Database\DatabaseDriver;
use Joomla\Filter\InputFilter;
use RuntimeException;

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
class TemplatesTagsTable extends Table implements VersionableTableInterface
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
	public $tpl_tags_head_advanced = '';

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
	public $tpl_tags_body_advanced = '';

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
	public $tpl_tags_article_advanced_b = '';

	/**
	 * @var string template tag article advanced end
	 *
	 * @since       2.0.0
	 */
	public $tpl_tags_article_advanced_e = '';

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
	public $tpl_tags_readon_advanced = '';

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
	public $tpl_tags_legal_advanced_b = '';

	/**
	 * @var string template tag legal advanced end
	 *
	 * @since       2.0.0
	 */
	public $tpl_tags_legal_advanced_e = '';

	/**
	 * @var integer
	 *
	 * @since       2.0.0
	 */
	public $standard = 0;

	/**
	 * Constructor
	 *
	 * @param 	DatabaseDriver  $db Database object
	 *
	 * @since       2.0.0
	 */
	public function __construct($db = null)
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
	public function check(): bool
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
	 * @param int $template_id template id
	 *
	 * @return	array
	 *
	 * @throws Exception
	 *
	 * @since	2.3.0 here (moved from newsletter model there since 2.0.0)
	 */
	public function getTemplateAssets(int $template_id): array
	{
		$tpl_assets = array();

		$db	= $this->_db;
		$query	= $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('templates_table_id') . ' = ' . $template_id);

		try
		{
			$db->setQuery($query);

			$tpl_assets = $db->loadAssoc();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'TemplatesTagsTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		if ($tpl_assets === null)
		{
			return array();
		}

		return $tpl_assets;
	}

	/**
	 * Method to save template tags for user-made html templates
	 *
	 *
	 * @param array   $data  The form data.
	 * @param integer $tplId The id of the template the tags belongs to
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws Exception
	 *
	 * @since   3.0.0
	 */
	public function saveTags(array $data, int $tplId): bool
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
				$tplId . ',' .
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

		try
		{
			$db->setQuery($query);
			$db->execute();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'TemplatesTagsTable BE');

            Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

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
	public function hasField($key): bool
	{
		$key = $this->getColumnAlias($key);

		return property_exists($this, $key);
	}

	/**
	 * Get the type alias for the history table
	 *
	 * The type alias generally is the internal component name with the
	 * content type. Ex.: com_content.article
	 *
	 * @return  string  The alias as described above
	 *
	 * @since   4.0.0
	 */
	public function getTypeAlias(): string
	{
		return 'com_bwpostman.templates_tags';
	}
}
