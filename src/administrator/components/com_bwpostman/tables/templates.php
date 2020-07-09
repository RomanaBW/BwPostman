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

use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Filter\InputFilter;
use Joomla\Registry\Registry;

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
class BwPostmanTableTemplates extends JTable
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
	public $tpl_html = null;

	/**
	 * @var string tpl_css
	 *
	 * @since       1.1.0
	 */
	public $tpl_css = null;

	/**
	 * @var string tpl_article
	 *
	 * @since       1.1.0
	 */
	public $tpl_article = null;

	/**
	 * @var string tpl_divider
	 *
	 * @since       1.1.0
	 */
	public $tpl_divider = null;

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
	public $basics = null;

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
	public $intro = null;

	/**
	 * @var array article
	 *
	 * @since       1.1.0
	 */
	public $article = null;

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
	public $modified_time = '0000-00-00 00:00:00';

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
	public $checked_out_time = 0;

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
	public $archive_date = '0000-00-00 00:00:00';

	/**
	 * @var int ID --> 0 = newsletter is not archived, another ID = account is archived by an administrator
	 *
	 * @since       1.1.0
	 */
	public $archived_by = 0;

	/**
	 * Constructor
	 *
	 * @param 	JDatabaseDriver  $db Database object
	 *
	 * @since 1.1.0
	 */
	public function __construct(& $db)
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
	public function getAssetName()
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
	public function getAssetTitle()
	{
		return self::_getAssetTitle();
	}

	/**
	 * Alias function
	 *
	 * @return  string
	 *
	 * @throws Exception
	 *
	 * @since   1.1.0
	 */
	public function getAssetParentId()
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
	protected function _getAssetName()
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
	protected function _getAssetTitle()
	{
		return $this->title;
	}

	/**
	 * Method to get the parent asset id for the record
	 *
	 * @param   Table   $table  A Table object (optional) for the asset parent
	 * @param   integer  $id     The id (optional) of the content.
	 *
	 * @return  integer
	 *
	 * @since   11.1
	 */
	protected function _getAssetParentId(Table $table = null, $id = null)
	{
		$asset = Table::getInstance('Asset');
		$asset->loadByName('com_bwpostman.template');
		return $asset->id;
	}

	/**
	 * Overloaded bind function
	 *
	 * @access public
	 *
	 * @param array|object  $data       Named array
	 * @param string        $ignore     Space separated list of fields not to bind
	 *
	 * @throws BwException
	 *
	 * @return boolean
	 *
	 * @since 1.1.0
	 */
	public function bind($data, $ignore='')
	{

		// Remove all HTML tags from the title and description
		$filter				= new InputFilter(array(), array(), 0, 0);
		$this->title		= $filter->clean($this->title);
		$this->description	= $filter->clean($this->description);

		// Bind the rules.
		if (is_object($data))
		{
			if (property_exists($data, 'rules') && is_array($data->rules))
			{
				$rules = new JAccessRules($data->rules);
				$this->setRules($rules);
			}
		}
		elseif (is_array($data))
		{
			if (array_key_exists('rules', $data) && is_array($data['rules']))
			{
				$rules = new JAccessRules($data['rules']);
				$this->setRules($rules);
			}
		}
		else
		{
			throw new BwException(Text::sprintf('JLIB_DATABASE_ERROR_BIND_FAILED_INVALID_SOURCE_ARGUMENT', get_class($this)));
		}

		// Cast properties
		$this->id	= (int) $this->id;

		return parent::bind($data, $ignore);
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
		$app	= Factory::getApplication();
		$db	= $this->_db;
		$query	= $db->getQuery(true);
		$fault	= false;
		$xid    = 0;

		// unset standard template if task is save2copy
		$jinput	= Factory::getApplication()->input;
		$task = $jinput->get('task', 0);
		if ($task == 'save2copy')
		{
			$this->standard = 0;
		}

		// *** prepare the template data ***
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
			require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/models/templates_tpl.php');
			$tpl_model = new BwPostmanModelTemplates_Tpl();
			$tpl		= $tpl_model->getItem($tpl_id);

			// get template model
			require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/models/template.php');
			$model = new BwPostmanModelTemplate();
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
			$tpl_id		= $item->tpl_id;
			require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/models/templates_tpl.php');
			$tpl_model = new BwPostmanModelTemplates_Tpl();
			$tpl		= $tpl_model->getItem($tpl_id);

			// get template model
			require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/models/template.php');
			$model = new BwPostmanModelTemplate();
			// make html template data
			$this->tpl_html = $model->makeTemplate($item, $tpl);
			if ($this->footer['show_impressum'] == 1)
			{
				$this->tpl_html = $this->tpl_html . '[%impressum%]';
			}

			// make css data
			$this->tpl_css = $model->replaceZooms($tpl->css, $item);

			// make article template data
			$article			= $model->replaceZooms($tpl->article_tpl, $item);
			$readon				= $model->makeButton($tpl->readon_tpl, $item);
			$this->tpl_article	= $this->article['show_readon'] != 1 ?
				str_replace('[%readon_button%]', '', $article) :
				str_replace('[%readon_button%]', $readon, $article);

			//  set divider template and replace placeholder
			$tpl->divider_tpl	= $model->replaceZooms($tpl->divider_tpl, $item);
			$this->tpl_divider	= str_replace('[%divider_color%]', $item->article['divider_color'], $tpl->divider_tpl);

			// convert object array to string
			self::converttostr($this);
		}

		// *** end prepare the template data ***

		// Check for valid title
		if (trim($this->title) == '')
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_TPL_ERROR_TITLE'), 'error');
			$fault	= true;
		}

		// Check for valid title
		if (trim($this->description) == '')
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_TPL_ERROR_DESCRIPTION'), 'error');
			$fault	= true;
		}

		// Check for existing title
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('title') . ' = ' . $db->quote($this->title));

		$db->setQuery($query);

		try
		{
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

		if ($fault) {
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
	public function store($updateNulls = false)
	{
		$date = Factory::getDate();
		$user = Factory::getUser();

		// trim leading and last <style>-tag
		$this->tpl_css = trim($this->tpl_css);
		$this->tpl_css = ltrim($this->tpl_css, '<style type="text/css">');
		$this->tpl_css = rtrim($this->tpl_css, '</style>');

		if ($this->id)
		{
			// Existing mailing list
			$this->modified_time = $date->toSql();
			$this->modified_by = $user->get('id');
		}
		else
		{
			// New template
			$this->created_date = $date->toSql();
			$this->created_by = $user->get('id');
		}

		$res	= parent::store($updateNulls);
		Factory::getApplication()->setUserState('com_bwpostman.edit.template.id', $this->id);

		return $res;
	}

	/**
	 * Convert object array to string
	 *
	 * @access private
	 *
	 * @param   object  $data
	 *
	 * @return object   $data
	 *
	 * @since 1.1.0
	 */
	private function converttostr($data)
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

		return $data;
	}

	/**
	 * Method to get the number of standard templates
	 *
	 * @param $cid
	 *
	 * @return int
	 *
	 * @since 2.4.0
	 */
	public function getNumberOfStdTemplates($cid)
	{
		$db    = $this->_db;
		$query = $db->getQuery(true);

		// count selected standard templates
		$query->select($db->quoteName('standard'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('id') . " IN (" . implode(",", $cid) . ")");
		$query->where($db->quoteName('standard') . " = " . $db->quote(1));

		$db->setQuery($query);
		$db->execute();
		$count_std = $db->getNumRows();

		return $count_std;
	}

	/**
	 * Method to get the number of templates depending on provided mode. archive state and title
	 * If title is provided, then archive state is not used
	 *
	 * @param string  $mode
	 * @param boolean $archived
	 * @param string  $title
	 *
	 * @return 	integer|boolean number of templates or false
	 *
	 * @throws Exception
	 *
	 * @since 2.4.0 (here, before since 2.3.0 at template helper)
	 */
	public function getNbrOfTemplates($mode, $archived, $title = '')
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

		$db->setQuery($query);

		try
		{
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
	 * @param integer  $id
	 *
	 * @return 	string|boolean title of template or false
	 *
	 * @throws Exception
	 *
	 * @since 2.4.0
	 */
	public function getTemplateTitle($id)
	{
		$db    = $this->_db;

		// get template title
		$q = $db->getQuery(true)
			->select($db->quoteName('title'))
			->from($db->quoteName($this->_tbl))
			->where($db->quoteName('id') . ' = ' .$id);
		$db->setQuery($q);

		try
		{
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
	 * @param integer  $id
	 * @param string   $title
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
	 *
	 * @since 2.4.0
	 */
	public function setTemplateTitle($id, $title)
	{
		$db    = $this->_db;

		// get template title
		$q = $db->getQuery(true)
			->update($db->quoteName($this->_tbl))
			->set($db->quoteName('title') . ' = ' . $db->quote($title))
			->where($db->quoteName('id') . ' = ' .$id);
		$db->setQuery($q);

		try
		{
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
	 * @param   int    $template_id     template id
	 *
	 * @return	object
	 *
	 * @throws Exception
	 *
	 * @since	2.4.0 (here, since 2.3.0 at ContentRenderer, since 1.1.0 at newsletter model)
	 */
	public function getTemplate($template_id)
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
		$query->where($db->quoteName('id') . ' = ' . (int) $template_id);

		$db->setQuery($query);
		try
		{
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
	 * @param   string  $mode       HTML or text
	 *
	 * @return	string	            ID of standard template
	 *
	 * @throws Exception
	 *
	 * @since	2.4.0 (here, since 1.2.0 at model newsletter)
	 */
	public function getStandardTpl($mode	= 'html')
	{
		$tpl   = new stdClass();
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

		$db->setQuery($query);

		try
		{
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
	 * @param   integer  $id  The primary key ID for the style.
	 *
	 * @return  boolean  True if successful.
	 *
	 * @throws	Exception
	 *
	 * @since 1.1.0
	 */
	public function setDefaultTpl($id = 0)
	{
		if (!$this->load((int) $id))
		{
			throw new Exception(Text::_('COM_BWPOSTMAN_ERROR_TEMPLATE_NOT_FOUND'));
		}

		// Reset the standard fields for the templates.
		$db   = $this->_db;
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

		$db->setQuery($query);

		try
		{
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
		$query->where($db->quoteName('id') . ' = ' . $db->Quote((int)$id));

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
