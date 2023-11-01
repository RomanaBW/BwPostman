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

namespace BoldtWebservice\Component\BwPostman\Administrator\Table;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwException;
use BoldtWebservice\Component\BwPostman\Administrator\Model\TemplateModel;
use BoldtWebservice\Component\BwPostman\Administrator\Model\TemplatesTplModel;
use DateTime;
use Exception;
use JAccessRules;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Versioning\VersionableTableInterface;
use Joomla\Database\DatabaseDriver;
use Joomla\Filter\InputFilter;
use Joomla\Registry\Registry;
use RuntimeException;
use stdClass;

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
class TemplateTable extends Table implements VersionableTableInterface
{
	/**
	 * @var int Primary Key
	 *
	 * @since       1.1.0
	 */
	public $id = null;

	/**
	 * @var int asset_id
	 *
	 * @since       1.1.0
	 */
	public $asset_id = null;

	/**
	 * @var int standard template
	 *
	 * @since       1.1.0
	 */
	public $standard = 0;

	/**
	 * @var string title
	 *
	 * @since       1.1.0
	 */
	public $title = null;

	/**
	 * @var string description
	 *
	 * @since       1.1.0
	 */
	public $description = null;

	/**
	 * @var string thumbnail url
	 *
	 * @since       1.1.0
	 */
	public $thumbnail = null;

	/**
	 * @var string tpl_html
	 *
	 * @since       1.1.0
	 */
	public $tpl_html = '';

	/**
	 * @var string tpl_css
	 *
	 * @since       1.1.0
	 */
	public $tpl_css = '';

	/**
	 * @var string tpl_article
	 *
	 * @since       1.1.0
	 */
	public $tpl_article = '';

	/**
	 * @var string tpl_divider
	 *
	 * @since       1.1.0
	 */
	public $tpl_divider = '';

	/**
	 * @var int tpl_id
	 *
	 * @since       1.1.0
	 */
	public $tpl_id = null;

	/**
	 * @var string basics
	 *
	 * @since       1.1.0
	 */
	public $basics = '';

	/**
	 * @var string header
	 *
	 * @since       1.1.0
	 */
	public $header = null;

	/**
	 * @var string intro
	 *
	 * @since       1.1.0
	 */
	public $intro = '';

	/**
	 * @var array article
	 *
	 * @since       1.1.0
	 */
	public $article = '';

	/**
	 * @var array footer
	 *
	 * @since       1.1.0
	 */
	public $footer = null;

	/**
	 * @var string button1
	 *
	 * @since       1.1.0
	 */
	public $button1 = null;

	/**
	 * @var string button2
	 *
	 * @since       1.1.0
	 */
	public $button2 = null;

	/**
	 * @var string button3
	 *
	 * @since       1.1.0
	 */
	public $button3 = null;

	/**
	 * @var string button4
	 *
	 * @since       1.1.0
	 */
	public $button4 = null;

	/**
	 * @var string button5
	 *
	 * @since       1.1.0
	 */
	public $button5 = null;

	/**
	 * @var int access
	 *
	 * @since       1.1.0
	 */
	public $access = 1;

	/**
	 * @var int Published
	 *
	 * @since       1.1.0
	 */
	public $published = 0;

	/**
	 * @var datetime creation date of the newsletter
	 *
	 * @since       1.1.0
	 */
	public $created_date = '0000-00-00 00:00:00';

	/**
	 * @var int Author
	 *
	 * @since       1.1.0
	 */
	public $created_by = 0;

	/**
	 * @var datetime last modification date of the newsletter
	 *
	 * @since       1.1.0
	 */
	public $modified_time = null;

	/**
	 * @var int user ID
	 *
	 * @since       1.1.0
	 */
	public $modified_by = 0;

	/**
	 * @var int Checked-out owner
	 *
	 * @since       1.1.0
	 */
	public $checked_out = 0;

	/**
	 * @var datetime Checked-out time
	 *
	 * @since       1.1.0
	 */
	public $checked_out_time = null;

	/**
	 * @var int Archive-flag --> 0 = not archived, 1 = archived
	 *
	 * @since       1.1.0
	 */
	public $archive_flag = 0;

	/**
	 * @var datetime Archive-date
	 *
	 * @since       1.1.0
	 */
	public $archive_date = null;

	/**
	 * @var int ID --> 0 = newsletter is not archived, another ID = account is archived by an administrator
	 *
	 * @since       1.1.0
	 */
	public $archived_by = 0;

	/**
	 * Constructor
	 *
	 * @param 	DatabaseDriver  $db Database object
	 *
	 * @since 1.1.0
	 */
	public function __construct($db = null)
	{
		parent::__construct('#__bwpostman_templates', 'id', $db);
	}

	/**
	 * Alias function
	 *
	 * @return  string
	 *
	 * @since   1.1.0
	 */
	public function getAssetName(): string
	{
		return self::_getAssetName();
	}

	/**
	 * Alias function
	 *
	 * @return  string
	 *
	 * @since   1.1.0
	 */
	public function getAssetTitle(): ?string
	{
		return self::_getAssetTitle();
	}

	/**
	 * Alias function
	 *
	 * @return  int
	 *
	 * @throws Exception
	 *
	 * @since   1.1.0
	 */
	public function getAssetParentId(): int
	{
		return self::_getAssetParentId();
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 *
	 * @since   1.1.0
	 */
	protected function _getAssetName(): string
	{
		$k = $this->_tbl_key;
		return 'com_bwpostman.template.' . (int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return  string
	 *
	 * @since   1.1.0
	 */
	protected function _getAssetTitle(): ?string
	{
		return $this->title;
	}

	/**
	 * Method to get the parent asset id for the record
	 *
	 * @param Table|null $table A Table object (optional) for the asset parent
	 * @param null       $id    The id (optional) of the content.
	 *
	 * @return  integer
	 *
	 * @since   11.1
	 */
	protected function _getAssetParentId(Table $table = null, $id = null): int
	{
//		$MvcFactory = Factory::getApplication()->bootComponent('com_bwpostman')->getMVCFactory();
//		$asset      = $MvcFactory->createTable('Asset', 'Administrator');
		$asset = Table::getInstance('Asset');

		$asset->loadByName('com_bwpostman.template');
		return $asset->id;
	}

	/**
	 * Overloaded bind function
	 *
	 * @access public
	 *
	 * @param   array|object  $src     An associative array or object to bind to the Table instance.
	 * @param   array|string  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return boolean
	 *
	 * @throws BwException
	 *
	 * @since 1.1.0
	 */
	public function bind($src, $ignore=''): bool
	{

		// Remove all HTML tags from the title and description
		$filter				= new InputFilter(array(), array(), 0, 0);
		$this->title		= $filter->clean($this->title);
		$this->description	= $filter->clean($this->description);

		// Bind the rules.
		if (is_object($src))
		{
			if (property_exists($src, 'rules') && is_array($src->rules))
			{
				$rules = new JAccessRules($src->rules);
				$this->setRules($rules);
			}
		}
		elseif (is_array($src))
		{
			if (array_key_exists('rules', $src) && is_array($src['rules']))
			{
				$rules = new JAccessRules($src['rules']);
				$this->setRules($rules);
			}
		}
		else
		{
			throw new BwException(Text::sprintf('JLIB_DATABASE_ERROR_BIND_FAILED_INVALID_SOURCE_ARGUMENT', get_class($this)));
		}

		// Cast properties
		$this->id	= (int) $this->id;

		return parent::bind($src, $ignore);
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
	public function check(): bool
	{
		$app   = Factory::getApplication();
		$db    = $this->_db;
		$query = $db->getQuery(true);
		$fault = false;
		$xid   = 0;

		// Sanitize values
		$filter = new InputFilter(array(), array(), 0, 0);

		$this->id                  = $filter->clean($this->id, 'UINT');
		$this->asset_id            = $filter->clean($this->asset_id, 'UINT');
		$this->standard            = trim($filter->clean($this->standard, 'UINT'));
		$this->title               = trim($filter->clean($this->title));
		$this->description         = $filter->clean($this->description);
		$this->thumbnail           = trim($filter->clean($this->thumbnail));
		$this->tpl_html            = $filter->clean($this->tpl_html, 'RAW');
		$this->tpl_css             = $filter->clean($this->tpl_css, 'RAW');
		$this->tpl_article         = $filter->clean($this->tpl_article, 'RAW');
		$this->tpl_divider         = $filter->clean($this->tpl_divider, 'RAW');
		$this->tpl_id              = $filter->clean($this->tpl_id, 'UINT');
		$this->basics              = $filter->clean($this->basics, 'unknown');
		$this->header              = $filter->clean($this->header, 'unknown');
		$this->intro               = $filter->clean($this->intro, 'unknown');
		$this->article             = $filter->clean($this->article, 'unknown');
		$this->footer              = $filter->clean($this->footer, 'unknown');
		$this->button1             = $filter->clean($this->button1, 'unknown');
		$this->button2             = $filter->clean($this->button2, 'unknown');
		$this->button3             = $filter->clean($this->button3, 'unknown');
		$this->button4             = $filter->clean($this->button4, 'unknown');
		$this->button5             = $filter->clean($this->button5, 'unknown');
		$this->access              = $filter->clean($this->access, 'UINT');
		$this->published           = $filter->clean($this->access, 'UINT');
		$this->created_date        = $filter->clean($this->created_date);
		$this->created_by          = $filter->clean($this->created_by, 'INT');
		$this->modified_time       = $filter->clean($this->modified_time);
		$this->modified_by         = $filter->clean($this->modified_by, 'INT');
		$this->checked_out         = $filter->clean($this->checked_out, 'INT');
		$this->checked_out_time    = $filter->clean($this->checked_out_time);
		$this->archive_flag        = $filter->clean($this->archive_flag, 'UINT');
		$this->archive_date        = $filter->clean($this->archive_date);
		$this->archived_by         = $filter->clean($this->archived_by, 'INT');

		// unset standard template if task is save2copy
		$task   = $app->input->get('task', 0);

		if ($task == 'save2copy')
		{
			$this->standard = 0;
		}

		// *** prepare the template data ***
		// @Karl: Muss man das hier wirklich auf $item umschreiben?
		$item = $this;

		// user-made html template
		if ($item->tpl_id == 0)
		{
			if (isset($this->article) && is_array($this->article))
			{
				$registry = new Registry();
				$registry->loadArray($this->article);
				$this->article = (string) $registry;
			}
		}
		// user-made text template
		elseif ($item->tpl_id == 998)
		{
			if (isset($this->article) && is_array($this->article))
			{
				$registry = new Registry();
				$registry->loadArray($this->article);
				$this->article = (string) $registry;
			}
		}
		// pre-installed text template
		elseif ($item->tpl_id > 999)
		{
			// first get templates tpls
			$tpl_id		= $item->tpl_id;
			$tpl_model = new TemplatesTplModel();
			$tpl		= $tpl_model->getItem($tpl_id);

			// get template model
			$model = new TemplateModel();
			// make html template data
			$this->tpl_html	= $model->makeTexttemplate($item, $tpl);
			if ($this->footer['show_impressum'] == 1)
			{
				$this->tpl_html = $this->tpl_html . '[%impressum%]';
			}

			// make article template data
			$article			= $tpl->article_tpl;
			$readon				= $tpl->readon_tpl;
			$this->tpl_article	= $this->article['show_readon'] != 1 ?
				str_replace('[%readon_button%]', '', $article) :
				str_replace('[%readon_button%]', $readon, $article);

			//  set divider template
			$this->tpl_divider	= $tpl->divider_tpl;

			// convert object array to string
			self::converttostr($this);
		}
		// pre-installed html template
		else
		{
			// first get templates tpls
			$tpl_id    = $item->tpl_id;
			$tpl_model = new TemplatesTplModel();
			$tpl       = $tpl_model->getItem($tpl_id);

			// get template model
			$model = new TemplateModel();
			// make html template data
			$this->tpl_html = $model->makeTemplate($item, $tpl);
			if ($this->footer['show_impressum'] == 1)
			{
				$this->tpl_html = $this->tpl_html . '[%impressum%]';
			}

			// make css data
			$this->tpl_css = $model->replaceZooms($tpl->css, $item);

			// make article template data
			$article           = $model->replaceZooms($tpl->article_tpl, $item);
			$readon            = $model->makeButton($tpl->readon_tpl, $item);
			$this->tpl_article = $this->article['show_readon'] != 1 ?
				str_replace('[%readon_button%]', '', $article) :
				str_replace('[%readon_button%]', $readon, $article);

			//  set divider template and replace placeholder
			$tpl->divider_tpl  = $model->replaceZooms($tpl->divider_tpl, $item);
			$this->tpl_divider = str_replace('[%divider_color%]', $item->article['divider_color'], $tpl->divider_tpl);

			// convert object array to string
			self::converttostr($this);
		}

		// *** end prepare the template data ***

		// Check for valid title
		if (trim($this->title) == '')
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_TPL_ERROR_TITLE'), 'error');
			$fault = true;
		}

		// Check for valid title
		if (trim($this->description) == '')
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_TPL_ERROR_DESCRIPTION'), 'error');
			$fault = true;
		}

		// Check for existing title
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('title') . ' = ' . $db->quote($this->title));

		try
		{
			$db->setQuery($query);

			$xid = intval($this->_db->loadResult());
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		if ($xid && $xid != intval($this->id))
		{
			$app->enqueueMessage((Text::sprintf('COM_BWPOSTMAN_TPL_ERROR_TITLE_DOUBLE', $this->title, $xid)), 'error');
			return false;
		}

		if ($fault)
		{
			$app->setUserState('com_bwpostman.edit.template.data', $this);
			return false;
		}

		return true;
	}

	/**
	 * Overridden Table::store to set created/modified and user id.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws Exception
	 *
	 * @since   1.1.0
	 */
	public function store($updateNulls = false): bool
	{
		$date = Factory::getDate();
		$user = Factory::getApplication()->getIdentity();

		// trim leading and last <style>-tag
		$this->tpl_css = trim($this->tpl_css);
		// @ToDo: Deprecated HTML attribute
		$this->tpl_css = ltrim($this->tpl_css, '<style type="text/css">');
		$this->tpl_css = rtrim($this->tpl_css, '</style>');

		if ($this->id)
		{
			// Existing mailing list
			$this->modified_time = $date->toSql();
			$this->modified_by   = $user->get('id');
		}
		else
		{
			// New template
			$this->created_date = $date->toSql();
			$this->created_by   = $user->get('id');
		}

		// Ensure nulldate columns have correct nulldate
		$nulldateCols = array(
			'modified_time',
			'checked_out_time',
			'archive_date',
		);

		foreach ($nulldateCols as $nulldateCol)
		{
			if ($this->$nulldateCol === '' || $this->$nulldateCol === $this->_db->getNullDate())
			{
				$this->$nulldateCol = null;
			}
		}

		$res = parent::store($updateNulls);
		Factory::getApplication()->setUserState('com_bwpostman.edit.template.id', $this->id);

		return $res;
	}

	/**
	 * Convert object array to string
	 *
	 * @access private
	 *
	 * @param object $data
	 *
	 * @since 1.1.0
	 */
	private function converttostr(object $data)
	{
		// array to string
		if (isset($data->basics) && is_array($data->basics))
		{
			$registry = new Registry();
			$registry->loadArray($data->basics);
			$data->basics = (string) $registry;
		}

		if (isset($data->header) && is_array($data->header))
		{
			$registry = new Registry();
			$registry->loadArray($data->header);
			$data->header = (string) $registry;
		}

		if (isset($data->intro) && is_array($data->intro))
		{
			$registry = new Registry();
			$registry->loadArray($data->intro);
			$data->intro = (string) $registry;
		}

		if (isset($data->article) && is_array($data->article))
		{
			$registry = new Registry();
			$registry->loadArray($data->article);
			$data->article = (string) $registry;
		}

		if (isset($data->footer) && is_array($data->footer))
		{
			$registry = new Registry();
			$registry->loadArray($data->footer);
			$data->footer = (string) $registry;
		}

		if (isset($data->button1) && is_array($data->button1))
		{
			$registry = new Registry();
			$registry->loadArray($data->button1);
			$data->button1 = (string) $registry;
		}

		if (isset($data->button2) && is_array($data->button2))
		{
			$registry = new Registry();
			$registry->loadArray($data->button2);
			$data->button2 = (string) $registry;
		}

		if (isset($data->button3) && is_array($data->button3))
		{
			$registry = new Registry();
			$registry->loadArray($data->button3);
			$data->button3 = (string) $registry;
		}

		if (isset($data->button4) && is_array($data->button4))
		{
			$registry = new Registry();
			$registry->loadArray($data->button4);
			$data->button4 = (string) $registry;
		}

		if (isset($data->button5) && is_array($data->button5))
		{
			$registry = new Registry();
			$registry->loadArray($data->button5);
			$data->button5 = (string) $registry;
		}

//		return $data;
	}

	/**
	 * Method to get the number of standard templates
	 *
	 * @param array $cid
	 *
	 * @return int
	 *
	 * @throws Exception
	 *
	 * @since 3.0.0
	 */
	public function getNumberOfStdTemplates(array $cid): int
	{
		$count_std = 0;

		$db    = $this->_db;
		$query = $db->getQuery(true);

		// count selected standard templates
		$query->select($db->quoteName('standard'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('id') . " IN (" . implode(",", $cid) . ")");
		$query->where($db->quoteName('standard') . " = " . $db->quote(1));

		try
		{
			$db->setQuery($query);
			$db->execute();

			$count_std = $db->getNumRows();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}


		return $count_std;
	}

	/**
	 * Method to get the number of templates depending on provided mode. archive state and title
	 * If title is provided, then archive state is not used
	 *
	 * @param string  $mode
	 * @param boolean $archived
	 * @param string|null  $title
	 *
	 * @return 	integer|boolean number of templates or false
	 *
	 * @throws Exception
	 *
	 * @since 3.0.0 (here, before since 2.3.0 at template helper)
	 */
	public function getNbrOfTemplates(string $mode, bool $archived, ?string $title = '')
	{
		$archiveFlag = 0;

		if ($archived)
		{
			$archiveFlag = 1;
		}

		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->select('COUNT(*)');
		$query->from($db->quoteName($this->_tbl));

		if (strtolower($mode) === 'html')
		{
			$query->where($db->quoteName('tpl_id') . ' < ' . $db->quote('998'));
		}
		elseif (strtolower($mode) === 'text')
		{
			$query->where($db->quoteName('tpl_id') . ' > ' . $db->quote('997'));
		}

		if ($title !== '')
		{
			$query->where($db->quoteName('title') . ' LIKE ' . $db->quote('%' . $title . '%'));
		}
		else
		{
			$query->where($db->quoteName('archive_flag') . ' = ' . $archiveFlag);
		}

		try
		{
			$db->setQuery($query);

			return $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
		return false;
	}

	/**
	 * Method to get the title of a template
	 *
	 * @param integer $id
	 *
	 * @return 	string|boolean title of template or false
	 *
	 * @throws Exception
	 *
	 * @since 3.0.0
	 */
	public function getTemplateTitle(int $id)
	{
		$db    = $this->_db;
		$query = $db->getQuery(true);

		// get template title
		$query->select($db->quoteName('title'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('id') . ' = ' . $id);

		try
		{
			$db->setQuery($query);

			$TplTitle = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		return $TplTitle;
	}

	/**
	 * Method to set the title of a template
	 *
	 * @param integer $id
	 * @param string  $title
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
	 *
	 * @since 3.0.0
	 */
	public function setTemplateTitle(int $id, string $title): bool
	{
		$db    = $this->_db;
		$query = $db->getQuery(true);

		// get template title
		$query->update($db->quoteName($this->_tbl));
		$query->set($db->quoteName('title') . ' = ' . $db->quote($title));
		$query->where($db->quoteName('id') . ' = ' . $id);

		try
		{
			$db->setQuery($query);

			return $db->execute();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}
	}

	/**
	 * Method to get the template settings which are used to compose a newsletter
	 *
	 * @access	public
	 *
	 * @param int $template_id template id
	 *
	 * @return	object|null
	 *
	 * @throws Exception
	 *
	 * @since	3.0.0 (here, since 2.3.0 at ContentRenderer, since 1.1.0 at newsletter model)
	 */
	public function getTemplate(int $template_id): ?object
	{
		$tpl    = new stdClass();
		$db   = $this->_db;
		$query = $db->getQuery(true);

		$query->select($db->quoteName('id'));
		$query->select($db->quoteName('tpl_html'));
		$query->select($db->quoteName('tpl_css'));
		$query->select($db->quoteName('tpl_article'));
		$query->select($db->quoteName('tpl_divider'));
		$query->select($db->quoteName('tpl_id'));
		$query->select($db->quoteName('basics'));
		$query->select($db->quoteName('article'));
		$query->select($db->quoteName('intro'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('id') . ' = ' . $template_id);

		try
		{
			$db->setQuery($query);

			$tpl = $db->loadObject();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
		return $tpl;
	}

	/**
	 * Method to get the ID of the standard template for HTML or text mode
	 *
	 * @param string $mode HTML or text
	 *
	 * @return	string|null	            ID of standard template
	 *
	 * @throws Exception
	 *
	 * @since	3.0.0 (here, since 1.2.0 at model newsletter)
	 */
	public function getStandardTpl(string $mode = 'html'): ?string
	{
		$tpl   = null;
		$db    = $this->_db;
		$query = $db->getQuery(true);

		// Id of the standard template
		switch ($mode)
		{
			case 'html':
			default:
				$query->select($db->quoteName('id'));
				$query->from($db->quoteName($this->_tbl));
				$query->where($db->quoteName('standard') . ' = ' . $db->quote('1'));
				$query->where($db->quoteName('tpl_id') . ' < ' . $db->quote('998'));
				break;

			case 'text':
				$query->select($db->quoteName('id') . ' AS ' . $db->quoteName('value'));
				$query->from($db->quoteName($this->_tbl));
				$query->where($db->quoteName('standard') . ' = ' . $db->quote('1'));
				$query->where($db->quoteName('tpl_id') . ' > ' . $db->quote('997'));
				break;
		}

		try
		{
			$db->setQuery($query);

			$tpl = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $tpl;
	}

	/**
	 * Method to set a template as default.
	 *
	 * @param integer $id The primary key ID for the style.
	 *
	 * @return  boolean  True if successful.
	 *
	 * @throws	Exception
	 *
	 * @since 1.1.0
	 */
	public function setDefaultTpl(int $id = 0): bool
	{
		if (!$this->load($id))
		{
			throw new Exception(Text::_('COM_BWPOSTMAN_ERROR_TEMPLATE_NOT_FOUND'));
		}

		// Reset the standard fields for the templates.
		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->update($db->quoteName($this->_tbl));
		$query->set($db->quoteName('standard') . " = " . $db->Quote(0));
		$query->where($db->quoteName('standard') . ' = ' . $db->Quote(1));

		if ($this->tpl_id < 988)
		{
			$query->where($db->quoteName('tpl_id') . ' < ' . $db->Quote(988));
		}
		else
		{
			$query->where($db->quoteName('tpl_id') . ' > ' . $db->Quote(987));
		}

		try
		{
			$db->setQuery($query);
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// Set the new standard template.
		$query = $db->getQuery(true);

		$query->update($db->quoteName($this->_tbl));
		$query->set($db->quoteName('standard') . " = " . $db->Quote(1));
		$query->set($db->quoteName('published') . " = " . $db->Quote(1));
		$query->where($db->quoteName('id') . ' = ' . $db->Quote($id));

		try
		{
			$db->setQuery($query);
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return true;
	}

	/**
	 * Method to reset default template
	 *
	 * @param integer $id
	 *
	 * @return 	void
	 *
	 * @throws Exception
	 *
	 * @since 4.1.0
	 */
	public function resetDefaultTpl(int $id)
	{
		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->update($db->quoteName($this->_tbl));
		$query->set($db->quoteName('standard') . " = " . $db->Quote(0));
		$query->where($db->quoteName('id') . ' = ' . $db->Quote($id));

		if ($this->tpl_id < 988)
		{
			$query->where($db->quoteName('tpl_id') . ' < ' . $db->Quote(988));
		}
		else
		{
			$query->where($db->quoteName('tpl_id') . ' > ' . $db->Quote(987));
		}

		try
		{
			$db->setQuery($query);
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
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
		return 'com_bwpostman.template';
	}
}
