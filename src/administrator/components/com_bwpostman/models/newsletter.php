<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single newsletter model for backend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
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
defined ('_JEXEC') or die ('Restricted access');

require_once (JPATH_SITE.'/components/com_content/helpers/route.php');
require_once (JPATH_COMPONENT_ADMINISTRATOR . '/libraries/logging/BwLogger.php');

// Import MODEL and Helper object class
jimport('joomla.application.component.modeladmin');

use Joomla\Utilities\ArrayHelper as ArrayHelper;
use Joomla\Registry\Registry as JRegistry;

// Require helper class
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');

/**
 * BwPostman newsletter model
 * Provides methods to add, edit and send newsletters
 *
 * @package		BwPostman-Admin
 *
 * @subpackage	Newsletters
 *
 * @since       0.9.1
 */
class BwPostmanModelNewsletter extends JModelAdmin
{
	/**
	 * Newsletter id
	 *
	 * @var int
	 *
	 * @since       0.9.1
	 */
	var $_id = null;

	/**
	 * Newsletter data
	 *
	 * @var array
	 *
	 * @since       0.9.1
	 */
	var $_data = null;

	var $_demo_mode         = 0;
	var $_dummy_sender      = '';
	var $_dummy_recipient   = '';
	var $_arise_queue       = 0;

	/**
	 * Constructor
	 * Determines the newsletter ID
	 *
	 * @since       0.9.1
	 */
	public function __construct()
	{
		parent::__construct();

		$jinput	= JFactory::getApplication()->input;
		$array	= $jinput->get('cid',  0, '');
		$this->setId((int)$array[0]);

		$this->_processTestMode();
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	string  $type	    The table type to instantiate
	 * @param	string	$prefix     A prefix for the table class name. Optional.
	 * @param	array	$config     Configuration array for model. Optional.
	 *
	 * @return	object  JTable	A database object
	 *
	 * @since  1.0.1
	*/
	public function getTable($type = 'Newsletters', $prefix = 'BwPostmanTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to reset the newsletter ID and data
	 *
	 * @access	public
	 *
	 * @param	int $id     Newsletter ID
	 *
	 * @since       0.9.1
	 */
	public function setId($id)
	{
		$this->_id		= $id;
		$this->_data	= null;
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to change the state of the record.
	 *
	 * @since	1.0.1
	 */
	protected function canEditState($record)
	{
		$permission = BwPostmanHelper::canEditState('newsletter', $record->id);

		return $permission;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   1.0.1
	 */
	public function getItem($pk = null)
	{
		$app	= JFactory::getApplication();
		$_db	= $this->_db;
		$item   = new stdClass();

		// Initialise variables.
		$pk		= (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
		$table	= $this->getTable();
		$app->setUserState('com_bwpostman.edit.newsletter.id', $pk);

		// Get input data
		$state_data	= $app->getUserState('com_bwpostman.edit.newsletter.data');

		// if state exists and matches required id, use state, otherwise get data from table
		if (is_object($state_data) && $state_data->id == $pk)
		{
			$item	= $state_data;
		}
		else
		{
			// Get the data from the model
			try
			{
				// Attempt to load the row.
				$return = $table->load($pk);

				// Check for a table object error.
				if ($return === false && $table->getError())
				{
					$app->enqueueMessage($table->getError());

					return false;
				}

				// Convert to the JObject before adding other data.
				$properties = $table->getProperties(1);
				$item       = ArrayHelper::toObject($properties, 'JObject');

				if (property_exists($item, 'params'))
				{
					$registry = new JRegistry;
					$registry->loadJSON($item->params);
					$item->params = $registry->toArray();
				}

				//get associated mailinglists
				$query = $_db->getQuery(true);
				$query->select($_db->quoteName('mailinglist_id'));
				$query->from($_db->quoteName('#__bwpostman_newsletters_mailinglists'));
				$query->where($_db->quoteName('newsletter_id') . ' = ' . (int) $item->id);
				$_db->setQuery($query);
				$item->mailinglists = $_db->loadColumn();

				//extract associated usergroups
				$usergroups = array();
				foreach ($item->mailinglists as $mailinglist)
				{
					if ((int) $mailinglist < 0)
					{
						$usergroups[] = -(int) $mailinglist;
					}
				}
				$item->usergroups = $usergroups;

				if ($pk == 0)
				{
					$item->id = 0;
				}

				// get available mailinglists to predefine for state
				$query = $_db->getQuery(true);
				$query->select('id');
				$query->from($_db->quoteName('#__bwpostman_mailinglists'));
				$query->where($_db->quoteName('published') . ' = ' . (int) 1);
				$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);
				$query->where($_db->quoteName('access') . ' = ' . (int) 1);

				$_db->setQuery($query);

				$mls_available = $_db->loadColumn();
				$res_available = array_intersect($item->mailinglists, $mls_available);

				if (count($res_available) > 0)
				{
					$item->ml_available = $res_available;
				}
				else
				{
					$item->ml_available = array();
				}

				// get unavailable mailinglists to predefine for state
				$query = $_db->getQuery(true);
				$query->select('id');
				$query->from($_db->quoteName('#__bwpostman_mailinglists'));
				$query->where($_db->quoteName('published') . ' = ' . (int) 1);
				$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);
				$query->where($_db->quoteName('access') . ' > ' . (int) 1);

				$_db->setQuery($query);

				$mls_unavailable = $_db->loadColumn();
				$res_unavailable = array_intersect($item->mailinglists, $mls_unavailable);

				if (count($res_unavailable) > 0)
				{
					$item->ml_unavailable = $res_unavailable;
				}
				else
				{
					$item->ml_unavailable = array();
				}

				// get internal mailinglists to predefine for state
				$query = $_db->getQuery(true);
				$query->select('id');
				$query->from($_db->quoteName('#__bwpostman_mailinglists'));
				$query->where($_db->quoteName('published') . ' = ' . (int) 0);
				$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);
				//			$query->where($_db->quoteName('access') . ' = ' . (int) 1);

				$_db->setQuery($query);

				$mls_intern = $_db->loadColumn();
				$res_intern = array_intersect($item->mailinglists, $mls_intern);

				if (count($res_intern) > 0)
				{
					$item->ml_intern = $res_intern;
				}
				else
				{
					$item->ml_intern = array();
				}

				// Preset template ids
				// Old template for existing newsletters not set during update to 1.1.x, so we have to manage this here also

				// preset HTML-Template for old newsletters
				if ($item->id == 0)
				{
					$item->template_id = $this->_getStandardTpl('html');
				}
				elseif ($item->template_id == 0)
				{
					$query = $_db->getQuery(true);
					$query->select('id');
					$query->from($_db->quoteName('#__bwpostman_templates'));
					$query->where($_db->quoteName('id') . ' = ' . (int) -1);

					$_db->setQuery($query);

					$html_tpl = $_db->loadResult();

					if (is_null($html_tpl))
					{
						$html_tpl = $this->_getStandardTpl('html');
					}
					$item->template_id = $html_tpl;
				}

				// preset Text-Template for old newsletters
				if ($item->id == 0)
				{
					$item->text_template_id = $this->_getStandardTpl('text');
				}
				elseif ($item->text_template_id == 0)
				{
					$query = $_db->getQuery(true);
					$query->select('id');
					$query->from($_db->quoteName('#__bwpostman_templates'));
					$query->where($_db->quoteName('id') . ' = ' . (int) -2);

					$_db->setQuery($query);

					$text_tpl = $_db->loadResult();

					if (is_null($text_tpl))
					{
						$text_tpl = $this->_getStandardTpl('text');
					}
					$item->text_template_id = $text_tpl;
				}
				// preset Old Template IDs
				if ($item->id == 0)
				{
					$item->template_id_old      = '';
					$item->text_template_id_old = '';
				}
				else
				{
					$item->template_id_old      = $item->template_id;
					$item->text_template_id_old = $item->text_template_id;
				}
			}
			catch (RuntimeException $e)
			{
				$app->enqueueMessage($e->getMessage(), 'error');
			}
		}
		JFactory::getApplication()->setUserState('com_bwpostman.edit.newsletter.data', $item);

		return $item;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 *
	 * @since	1.0.1
	 */
	public function getForm($data = array(), $loadData = true)
	{
		JForm::addFieldPath('JPATH_ADMINISTRATOR/components/com_bwpostman/models/fields');

		$params = JComponentHelper::getParams('com_bwpostman');
		$config = JFactory::getConfig();
		$user	= JFactory::getUser();

		$form = $this->loadForm('com_bwpostman.newsletter', 'newsletter', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		$jinput	= JFactory::getApplication()->input;
		$id		=  $jinput->get('id', 0);

		// predefine some values
		if (!$form->getValue('from_name')) {
			if ($params->get('default_from_name') && !$form->getValue('from_name'))
			{
				$form->setValue('from_name', '', $params->get('default_from_name'));
			}
			else
			{
				$form->setValue('from_name', '', $config->get('fromname'));
			}
		}

		if (!$form->getValue('from_email'))
		{
			if ($params->get('default_from_email'))
			{
				$form->setValue('from_email', '', $params->get('default_from_email'));
			}
			else
			{
				$form->setValue('from_email', '', $config->get('mailfrom'));
			}
		}

		if (!$form->getValue('reply_email'))
		{
			if ($params->get('default_reply_email')) {
				$form->setValue('reply_email', '', $params->get('default_reply_email'));
			}
			else
			{
				$form->setValue('reply_email', '', $config->get('mailfrom'));
			}
		}

		// Check for existing newsletter.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (!$user->authorise('bwpm.newsletter.edit.state', 'com_bwpostman.newsletter.'.(int) $id))
				|| ($id == 0 && !$user->authorise('bwpm.edit.state', 'com_bwpostman')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('published', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is an newsletter you can edit.
			$form->setFieldAttribute('state', 'filter', 'unset');
		}
		// Check to show created data
		$c_date	= $form->getValue('created_date');
		if ($c_date == '0000-00-00 00:00:00')
		{
			$form->setFieldAttribute('created_date', 'type', 'hidden');
			$form->setFieldAttribute('created_by', 'type', 'hidden');
		}

		// Check to show modified data
		$m_date	= $form->getValue('modified_time');
		if ($m_date == '0000-00-00 00:00:00')
		{
			$form->setFieldAttribute('modified_time', 'type', 'hidden');
			$form->setFieldAttribute('modified_by', 'type', 'hidden');
		}

		// Check to show mailing data
		$s_date	= $form->getValue('mailing_date');
		if ($s_date == '0000-00-00 00:00:00')
		{
			$form->setFieldAttribute('mailing_date', 'type', 'hidden');
		}

		// Hide published on tab edit_basic
		if ($jinput->get('layout') == 'edit_basic')
		{
			$form->setFieldAttribute('published', 'type', 'hidden');
		}
		return $form;
	}

	/**
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 *
	 * @since	1.0.1
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data	= JFactory::getApplication()->getUserState('com_bwpostman.edit.newsletter.data', null);

		if (empty($data))
		{
			$data = $this->getItem();
		}
		return $data;
	}

	/**
	 * Method to get the standard template.
	 *
	 * @param   string  $mode       HTML or text
	 *
	 * @return	string	            ID of standard template
	 *
	 * @since	1.2.0
	 */
	private function _getStandardTpl($mode	= 'html')
	{
		$tpl    = new stdClass();
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		// Id of the standard template
		switch ($mode)
		{
			case 'html':
			default:
					$query->select($_db->quoteName('id'));
					$query->from($_db->quoteName('#__bwpostman_templates'));
					$query->where($_db->quoteName('standard') . ' = ' . $_db->quote('1'));
					$query->where($_db->quoteName('tpl_id') . ' < ' . $_db->quote('998'));
				break;

			case 'text':
					$query->select($_db->quoteName('id')  . ' AS ' . $_db->quoteName('value'));
					$query->from($_db->quoteName('#__bwpostman_templates'));
					$query->where($_db->quoteName('standard') . ' = ' . $_db->quote('1'));
					$query->where($_db->quoteName('tpl_id') . ' > ' . $_db->quote('997'));
				break;
		}
		$_db->setQuery($query);
		try
		{
			$tpl    = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication($e->getMessage(), 'error');
		}
		return $tpl;
	}

	/**
	 * Method to get the data of a single newsletter for the preview/modal box
	 *
	 * @access	public
	 *
	 * @return 	object Newsletter with formatted pieces
	 *
	 * @since
	 */
	public function getSingleNewsletter ()
	{
		$app	= JFactory::getApplication();
		$item	= $app->getUserState('com_bwpostman.edit.newsletter.data');

		//convert to object if necessary
		if ($item && !is_object($item))
		{
			$data_tmp	= new stdClass();
			foreach ($item as $key => $value)
			{
				$data_tmp->$key	= $value;
			}
			$item = $data_tmp;
		}

		// if old newsletter, there are no template IDs, so lets set them to the old template
		if ($item->template_id == '0')
			$item->template_id		= -1;
		if ($item->text_template_id == '0')
			$item->text_template_id	= -2;

		if ($item->id == 0 && !empty($item->selected_content) && empty($item->html_version) && empty($item->text_version))
		{
			if (!is_array($item->selected_content))
				$item->selected_content = explode(',', $item->selected_content);

			$renderer	= new contentRenderer();
			$content	= $renderer->getContent((array) $item->selected_content, $item->template_id, $item->text_template_id);
			$item->html_version	= $content['html_version'];
			$item->text_version	= $content['text_version'];
		}

		// force two linebreak at the end of text
		$item->text_version = rtrim($item->text_version)."\n\n";

		// Replace the links to provide the correct preview
		$item->html_formatted	= $item->html_version;
		$item->text_formatted	= $item->text_version;

		// add template data
		$this->_addTplTags($item->html_formatted, $item->template_id);
		$this->_addTextTpl($item->text_formatted, $item->text_template_id);

		// Replace the intro to provide the correct preview
		if (!empty($item->intro_headline))
			$item->html_formatted	= str_replace('[%intro_headline%]', $item->intro_headline, $item->html_formatted);
		if (!empty($item->intro_text))
			$item->html_formatted	= str_replace('[%intro_text%]', nl2br($item->intro_text, true), $item->html_formatted);
		if (!empty($item->intro_text_headline))
			$item->text_formatted	= str_replace('[%intro_headline%]', $item->intro_text_headline, $item->text_formatted);
		if (!empty($item->intro_text_text))
			$item->text_formatted	= str_replace('[%intro_text%]', $item->intro_text_text, $item->text_formatted);

		// only for old html templates
		if ($item->template_id < 1)
		{
			$item->html_formatted = $item->html_formatted . '[dummy]';
		}
		$this->_replaceTplLinks($item->html_formatted);
		$this->_addHtmlTags($item->html_formatted, $item->template_id);
		$this->_addHTMLFooter($item->html_formatted, $item->template_id);

		// only for old text templates
		if ($item->text_template_id < 1)
		{
			$item->text_formatted = $item->text_formatted . '[dummy]';
		}
		$this->_replaceTextTplLinks($item->text_formatted);
		$this->_addTextFooter($item->text_formatted, $item->text_template_id);

		// Replace the links to provide the correct preview
		$this->_replaceLinks($item->html_formatted);
		$this->_replaceLinks($item->text_formatted);

		return $item;
	}

	/**
	 * Method to get the selected content items which are used to compose a newsletter
	 *
	 * @access	public
	 *
	 * @return	array
	 *
	 * @since
	 */
	public function getSelectedContent()
	{
		$_db	= $this->_db;

		$selected_content = $this->_selectedContent();
		$selected_content_void = array ();

		if ($selected_content)
		{
			if (!is_array($selected_content))
				$selected_content = explode(',',$selected_content);

			$selected_content_items = array();

			// We do a foreach to protect our ordering
			foreach($selected_content as $content_id)
			{
				$items  = array();

				$subquery	= $_db->getQuery(true);
				$subquery->select($_db->quoteName('cc') . '.' . $_db->quoteName('title'));
				$subquery->from($_db->quoteName('#__categories') . ' AS ' . $_db->quoteName('cc'));
				$subquery->where($_db->quoteName('cc') . '.' . $_db->quoteName('id') . ' = ' . $_db->quoteName('c') . '.' . $_db->quoteName('catid'));

				$query	= $_db->getQuery(true);
				$query->select($_db->quoteName('c') . '.' . $_db->quoteName('id'));
				$query->select($_db->quoteName('c') . '.' . $_db->quoteName('title') . ', (' . $subquery) . ') AS ' . $_db->quoteName('category_name');
				$query->from($_db->quoteName('#__content') . ' AS ' . $_db->quoteName('c'));
				$query->where($_db->quoteName('c') . '.' . $_db->quoteName('id') . ' = ' . $_db->quote($content_id));

				$_db->setQuery($query);

				try
				{
					$items= $_db->loadObjectList();
				}
				catch (RuntimeException $e)
				{
					JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				}

				if(sizeof($items) > 0)
				{
					if ($items[0]->category_name == '')
					{
						$selected_content_items[] = JHtml::_('select.option', $items[0]->id, "Uncategorized - " . $items[0]->title);
					}
					else
					{
						$selected_content_items[] = JHtml::_('select.option', $items[0]->id, $items[0]->category_name . " - " . $items[0]->title);
					}
				}
			}
			return $selected_content_items;

		}
		else
		{
			return $selected_content_void;
		}
	}

	/**
	 * Method to get the menu item ID which will be needed for the unsubscribe link in the footer
	 *
	 * @access	public
	 *
	 * @param   string  $view
	 *
	 * @return 	int     $itemid     menu item ID
	 *
	 * @since       0.9.1
	 */
	static public function getItemid($view)
	{
		$itemid = '';
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__menu'));
		$query->where($_db->quoteName('link') . ' = ' . $_db->quote('index.php?option=com_bwpostman&view='.$view));
		$query->where($_db->quoteName('published') . ' = ' . (int) 1);

		try
		{
			$_db->setQuery($query);
			$itemid = $_db->loadResult();

			if (empty($itemid))
			{
				$query = $_db->getQuery(true);

				$query->select($_db->quoteName('id'));
				$query->from($_db->quoteName('#__menu'));
				$query->where($_db->quoteName('link') . ' = ' . $_db->quote('index.php?option=com_bwpostman&view=register'));
				$query->where($_db->quoteName('published') . ' = ' . (int) 1);

				$_db->setQuery($query);
				$itemid = $_db->loadResult();
			}
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
		return $itemid;
	}

	/**
	 * Method to get the language of an article
	 *
	 * @access	public
	 *
	 * @param	int		$id     article ID
	 *
	 * @return 	mixed	language string or 0
	 *
	 * @since	1.0.7
	 */
	static public function getArticleLanguage($id)
	{
		if (JLanguageMultilang::isEnabled())
		{
			$result = '';
			$_db	= JFactory::getDbo();
			$query	= $_db->getQuery(true);

			$query->select($_db->quoteName('language'));
			$query->from($_db->quoteName('#__content'));
			$query->where($_db->quoteName('id') . ' = ' . (int) $id);

			$_db->setQuery($query);
			try
			{
				$result = $_db->loadResult();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
			return $result;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Method to store the newsletter data from the newsletters_tmp-table into the newsletters-table
	 *
	 * @access	public
	 *
	 * @param   array   $data       data to save
	 *
	 * @return 	boolean
	 *
	 * @since       0.9.1
	 */
	public function save($data)
	{
		$jinput		= JFactory::getApplication()->input;
		$_db		= $this->_db;
		$query		= $_db->getQuery(true);

		// merge ml-arrays, single array may not exist, therefore array_merge would not give a result
		if (isset($data['ml_available']))
			foreach ($data['ml_available'] as $key => $value)
				$data['mailinglists'][] 	= $value;
		if (isset($data['ml_unavailable']))
			foreach ($data['ml_unavailable'] as $key => $value)
				$data['mailinglists'][] 	= $value;
		if (isset($data['ml_intern']))
			foreach ($data['ml_intern'] as $key => $value)
				$data['mailinglists'][] 	= $value;

		// merge usergroups into mailinglists, single array may not exist, therefore array_merge would not give a result
		if (isset($data['usergroups']) && !empty($data['usergroups']))
			foreach ($data['usergroups'] as $key => $value)
				$data['mailinglists'][] = '-' . $value;

		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('bwpostman');

		// if saving a new newsletter before changing tab, we have to look, if there is a content selected and set html- and text-version
		if (empty($data['html_version']) && empty($data['text_version']))
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_ERROR_CONTENT_MISSING'));
			return false;
		}

		if (!parent::save($data)) {
			return false;
		}

	// Delete all entries of the newsletter from newsletters_mailinglists table
		if ($data['id'])
		{
			$query->delete($_db->quoteName('#__bwpostman_newsletters_mailinglists'));
			$query->where($_db->quoteName('newsletter_id') . ' =  ' . (int) $data['id']);

			$_db->setQuery($query);
			try
			{
				$_db->execute();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}
		else
		{
			//get id of new inserted data to write cross table newsletters-mailinglists and inject into form
			$data['id']	= $this->getState('newsletter.id');
			$jinput->set('id', $data['id']);

			// update state
			$state_data	= JFactory::getApplication()->getUserState('com_bwpostman.edit.newsletter.data');
			if (is_object($state_data))
			{	// check needed because copying newsletters has no state and does not need it
				$state_data->id	= $data['id'];
				JFactory::getApplication()->setUserState('com_bwpostman.edit.newsletter.data', $state_data);
			}
		}

		if ($data['campaign_id'] == '-1') {
			// Store the selected BwPostman mailinglists into newsletters_mailinglists-table
			if (isset($data['mailinglists']) && $data['campaign_id'] == '-1')
			{
				foreach ($data['mailinglists'] AS $mailinglists_value)
				{
					$query	= $_db->getQuery(true);

					$query->insert($_db->quoteName('#__bwpostman_newsletters_mailinglists'));
					$query->columns(array(
						$_db->quoteName('newsletter_id'),
						$_db->quoteName('mailinglist_id')
						));
						$query->values(
						(int) $data['id'] . ',' .
						(int) $mailinglists_value
						);
					$_db->setQuery($query);
					try
					{
						$_db->execute();
					}
					catch (RuntimeException $e)
					{
						JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
					}
				}
			}
		}
		$dispatcher->trigger('onBwPostmanAfterNewsletterModelSave', array(&$data));

		return true;
	}

	/**
	 * Method to (un)archive a newsletter from the newsletters-table
	 * --> when unarchiving it is called by the archive-controller
	 *
	 * @access	public
	 *
	 * @param	array $cid          Newsletter IDs
	 * @param	int     $archive    Task --> 1 = archive, 0 = unarchive
	 *
	 * @return	boolean
	 *
	 * @since       0.9.1
	 */
	public function archive($cid = array(), $archive = 1)
	{
		$app		= JFactory::getApplication();
		$date		= JFactory::getDate();
		$uid		= JFactory::getUser()->get('id');
		$state_data	= $app->getUserState('com_bwpostman.edit.newsletter.data');
		$_db		= $this->_db;
		$query		= $_db->getQuery(true);

		if ($archive == 1)
		{
			$time = $date->toSql();

			// Access check.
			if (!BwPostmanHelper::canArchive('newsletter', $cid))
			{
				return false;
			}
		}
		else
		{
			// Access check.
			if (!BwPostmanHelper::canRestore('newsletter', $cid))
			{
				return false;
			}

			$time	= '0000-00-00 00:00:00';
			$uid	= 0;
		}

		if (count($cid))
		{
			ArrayHelper::toInteger($cid);

			$query->update($_db->quoteName('#__bwpostman_newsletters'));
			$query->set($_db->quoteName('archive_flag') . " = " . (int) $archive);
			$query->set($_db->quoteName('archive_date') . " = " . $_db->quote($time, false));
			$query->set($_db->quoteName('archived_by') . " = " . (int) $uid);
			$query->where($_db->quoteName('id') . ' IN (' .implode(',', $cid) . ')');

			$_db->setQuery($query);
			try
			{
				$_db->execute();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}
		$app->setUserState('com_bwpostman.edit.newsletter.data', $state_data);
		return true;
	}

	/**
	 * Method to copy one or more newsletters
	 * --> the assigned mailing lists will be copied, too
	 *
	 * @param 	array   $cid        Newsletter-IDs
	 *
	 * @return 	boolean
	 *
	 * @since
	 */
	public function copy($cid = array())
	{
		if (!BwPostmanHelper::canAdd('newsletter'))
		{
			return false;
		}

		$app	= JFactory::getApplication();
		$_db	= $this->_db;

		if (count($cid))
		{
			foreach ($cid as $id)
			{
				$newsletters_data_copy  = new stdClass();
				$query	                = $_db->getQuery(true);

				$query->select('*');
				$query->from($_db->quoteName('#__bwpostman_newsletters'));
				$query->where($_db->quoteName('id') . ' = ' . (int) $id);

				$_db->setQuery($query);

				try
				{
					$newsletters_data_copy = $_db->loadObject();
				}
				catch (RuntimeException $e)
				{
					JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				}
				if (is_string($newsletters_data_copy->usergroups))
				{
					if ($newsletters_data_copy->usergroups == '')
					{
						$newsletters_data_copy->usergroups = array();
					}
					else
					{
						$newsletters_data_copy->usergroups	= explode(',', $newsletters_data_copy->usergroups);
					}
				}

				if (!is_object($newsletters_data_copy))
				{
					$app->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_COPY_FAILED'), 'error');
				}

				$date	= JFactory::getDate();
				$time	= $date->toSql();
				$user	= JFactory::getUser();
				$uid	= $user->get('id');

				$newsletters_data_copy->id 					= null;
				$newsletters_data_copy->asset_id			= null;
				$newsletters_data_copy->subject 			= JText::sprintf('COM_BWPOSTMAN_NL_COPY_OF', $newsletters_data_copy->subject);
				$newsletters_data_copy->attachment	 		= null;
				$newsletters_data_copy->created_date 		= $time;
				$newsletters_data_copy->created_by			= $uid;
				$newsletters_data_copy->modified_time	 	= null;
				$newsletters_data_copy->modified_by	 		= null;
				$newsletters_data_copy->mailing_date 		= 0;
				$newsletters_data_copy->published 			= null;
				$newsletters_data_copy->checked_out 		= null;
				$newsletters_data_copy->checked_out_time 	= null;
				$newsletters_data_copy->archive_flag 		= 0;
				$newsletters_data_copy->archive_date 		= 0;
				$newsletters_data_copy->hits 				= null;

				$subQuery	= $_db->getQuery(true);

				$subQuery->select($_db->quoteName('mailinglist_id'));
				$subQuery->from($_db->quoteName('#__bwpostman_newsletters_mailinglists'));
				$subQuery->where($_db->quoteName('newsletter_id') . ' = ' . (int) $id);

				$_db->setQuery($subQuery);

				try
				{
					$newsletters_data_copy->mailinglists	= $_db->loadColumn();
				}
				catch (RuntimeException $e)
				{
					JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				}

				if (!$this->save(ArrayHelper::fromObject($newsletters_data_copy, false)))
				{
					$app->enqueueMessage($_db->getErrorMsg(), 'error');
					return false;
				}
			}
			$app->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_COPIED'), 'message');
			return true;
		}
		else
		{
			$app->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_ERROR_COPYING'), 'error');
			return false;
		}
	}

	/**
	 * Method to remove one or more newsletters from the newsletters-table
	 * --> is called by the archive-controller
	 *
	 * @access	public
	 *
	 * @param	array   $pks        Newsletter IDs
	 *
	 * @return	boolean
	 *
	 * @since       0.9.1
	 */
	public function delete(&$pks)
	{
		if (count($pks))
		{
			ArrayHelper::toInteger($pks);

			// Access check.
			if (!BwPostmanHelper::canDelete('newsletter', $pks))
			{
				return false;
			}

			// Delete newsletter from newsletters-table
			$nl_table = JTable::getInstance('newsletters', 'BwPostmanTable');

			foreach ($pks as $id)
			{
				if (!$nl_table->delete($id))
				{
					return false;
				}
			}

			// Delete assigned mailinglists from newsletters_mailinglists-table
			$lists_table = JTable::getInstance('newsletters_mailinglists', 'BwPostmanTable');

			foreach ($pks as $id)
			{
				if (!$lists_table->delete($id))
				{
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Method to clear the queue
	 *
	 * @access	public
	 *
	 * @return 	boolean
	 *
	 * @since
	 */
	public function delete_queue()
	{
		// Access check
		if (!BwPostmanHelper::canClearQueue())
		{
			return false;
		}

		$_db	= $this->_db;

		$query = "TRUNCATE TABLE {$_db->quoteName('#__bwpostman_sendmailqueue')} ";
		$_db->setQuery($query);
		try
		{
			$_db->execute();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
		return true;
	}

	/**
	 * Method to check and clean the input fields
	 *
	 * @access	public
	 *
	 * @param 	int		$recordId       Newsletter ID
	 * @param	array	$err            errors
	 *
	 * @return	mixed
	 *
	 * @since
	 */
	public function checkForm($recordId = 0, &$err)
	{
		jimport('joomla.mail.helper');

		// heal form data and get them
		$this->changeTab();
		$data	= ArrayHelper::fromObject(JFactory::getApplication()->getUserState('com_bwpostman.edit.newsletter.data'));

		$data['id']	= $recordId;
		$i = 0;

		//Remove all HTML tags from name, emails, subject and the text version
		$filter                 = new JFilterInput(array(), array(), 0, 0);
		$data['from_name'] 		= $filter->clean($data['from_name']);
		$data['from_email'] 	= $filter->clean($data['from_email']);
		$data['reply_email'] 	= $filter->clean($data['reply_email']);
		$data['subject']		= $filter->clean($data['subject']);
		$data['text_version']	= $filter->clean($data['text_version']);

		$err = array();

		// Check for valid from_name
		if (trim($data['from_name']) == '')
		{
			$err[$i]['err_code'] = 301;
			$err[$i]['err_msg'] = JText::_('COM_BWPOSTMAN_NL_ERROR_FROM_NAME');
			$i++;
		}

		// Check for valid from_email address
		if (trim($data['from_email']) == '')
		{
			$err[$i]['err_code'] = 302;
			$err[$i]['err_msg'] = JText::_('COM_BWPOSTMAN_NL_ERROR_FROM_EMAIL');
			$i++;
		}
		else
		{
			// If there is a from_email address check if the address is valid
			if (!JMailHelper::isEmailAddress(trim($data['from_email'])))
			{
				$err[$i]['err_code'] = 306;
				$err[$i]['err_msg'] = JText::_('COM_BWPOSTMAN_NL_ERROR_FROM_EMAIL_INVALID');
				$i++;
			}
		}

		// Check for valid reply_email address
		if (trim($data['reply_email']) == '')
		{
			$err[$i]['err_code'] = 303;
			$err[$i]['err_msg'] = JText::_('COM_BWPOSTMAN_NL_ERROR_REPLY_EMAIL');
			$i++;
		}
		else
		{
			// If there is a from_email address check if the address is valid
			if (!JMailHelper::isEmailAddress(trim($data['reply_email'])))
			{
				$err[$i]['err_code'] = 307;
				$err[$i]['err_msg'] = JText::_('COM_BWPOSTMAN_NL_ERROR_REPLY_EMAIL_INVALID');
				$i++;
			}
		}

		// Check for valid subject
		if (trim($data['subject']) == '')
		{
			$err[$i]['err_code'] = 304;
			$err[$i]['err_msg'] = JText::_('COM_BWPOSTMAN_NL_ERROR_SUBJECT');
			$i++;
		}

		// Check for valid html or text version
		if ((trim($data['html_version']) == '') && (trim($data['text_version']) == ''))
		{
			$err[$i]['err_code'] = 305;
			$err[$i]['err_msg'] = JText::_('COM_BWPOSTMAN_NL_ERROR_HTML_AND_TEXT');
		}
		return $data;
	}

	/**
	 * Method to check if there are selected mailinglists and if they contain recipients
	 *
	 * @access	public
	 *
	 * @param	string	$ret_msg                Error message
	 * @param	int		$nl_id                  newsletter id
	 * @param	boolean	$send_to_unconfirmed    Status --> 0 = do not send to unconfirmed, 1 = sent also to unconfirmed
	 * @param	int		$cam_id                 campaign id
	 *
	 * @return 	boolean
	 *
	 * @since
	 */
	public function checkRecipients(&$ret_msg, $nl_id, $send_to_unconfirmed, $cam_id)
	{
		$_db	= $this->_db;

		try
		{
			if ($cam_id != '-1')
			{
				// Check if there are assigned mailinglists or usergroups
				$query = $_db->getQuery(true);
				$query->select($_db->quoteName('mailinglist_id'));
				$query->from($_db->quoteName('#__bwpostman_campaigns_mailinglists'));
				$query->where($_db->quoteName('campaign_id') . ' = ' . (int) $cam_id);
			}
			else
			{
				// Check if there are assigned mailinglists or usergroups of the campaign
				$query = $_db->getQuery(true);
				$query->select($_db->quoteName('mailinglist_id'));
				$query->from($_db->quoteName('#__bwpostman_newsletters_mailinglists'));
				$query->where($_db->quoteName('newsletter_id') . ' = ' . (int) $nl_id);
			}
			$_db->setQuery($query);

			$mailinglists = $_db->loadObjectList();

			if (!$mailinglists)
			{
				$ret_msg = JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_NL_NO_LISTS');

				return false;
			}

			$check_subscribers    = 0;
			$check_allsubscribers = 0;
			$count_users          = 0;
			$usergroup            = array();

			foreach ($mailinglists as $mailinglist)
			{
				$mailinglist_id = $mailinglist->mailinglist_id;
				// Mailinglists
				if ($mailinglist_id > 0)
				{
					$check_subscribers = 1;
				}
				// All subscribers
				if ($mailinglist_id == -1)
				{
					$check_allsubscribers = 1;
				}
				else
				{
					// Usergroups
					if ((int) $mailinglist_id < 0)
					{
						$usergroup[] = -(int) $mailinglist_id;
					}
				}
			}

			// Check if the subscribers are confirmed and not archived
			$count_subscribers  = 0;
			if ($check_subscribers)
			{ // Check subscribers from selected mailinglists
				if ($send_to_unconfirmed)
				{
					$status = '0,1';
				}
				else
				{
					$status = '1';
				}

				$subQuery1 = $_db->getQuery(true);
				$subQuery2 = $_db->getQuery(true);
				$query     = $_db->getQuery(true);

				if ($cam_id != '-1')
				{
					// Check if there are assigned mailinglists or usergroups
					$subQuery2->select($_db->quoteName('mailinglist_id'));
					$subQuery2->from($_db->quoteName('#__bwpostman_campaigns_mailinglists'));
					$subQuery2->where($_db->quoteName('campaign_id') . ' = ' . (int) $cam_id);
				}
				else
				{
					$subQuery2->select($_db->quoteName('mailinglist_id'));
					$subQuery2->from($_db->quoteName('#__bwpostman_newsletters_mailinglists'));
					$subQuery2->where($_db->quoteName('newsletter_id') . ' IN (' . (int) $nl_id . ')');
				}

				$subQuery1->select('DISTINCT' . $_db->quoteName('subscriber_id'));
				$subQuery1->from($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
				$subQuery1->where($_db->quoteName('mailinglist_id') . ' IN (' . $subQuery2 . ')');

				$query->select('COUNT(' . $_db->quoteName('id') . ')');
				$query->from($_db->quoteName('#__bwpostman_subscribers'));
				$query->where($_db->quoteName('id') . ' IN (' . $subQuery1 . ')');
				$query->where($_db->quoteName('status') . ' IN (' . $status . ')');
				$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);

				$_db->setQuery($query);

				$count_subscribers = $_db->loadResult();
			}
			elseif ($check_allsubscribers)
			{ // Check all subscribers (select option "All subscribers")
				if ($send_to_unconfirmed)
				{
					$status = '0,1,9';
				}
				else
				{
					$status = '1,9';
				}

				$query = $_db->getQuery(true);

				$query->select('COUNT(' . $_db->quoteName('id') . ')');
				$query->from($_db->quoteName('#__bwpostman_subscribers'));
				$query->where($_db->quoteName('status') . ' IN (' . $status . ')');
				$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);

				$_db->setQuery($query);

				$count_subscribers = $_db->loadResult();
			}

			// Checks if the selected usergroups contain users
			if (is_array($usergroup) && count($usergroup))
			{
				$query     = $_db->getQuery(true);
				$sub_query = $_db->getQuery(true);

				$sub_query->select($_db->quoteName('g') . '.' . $_db->quoteName('user_id'));
				$sub_query->from($_db->quoteName('#__user_usergroup_map') . ' AS ' . $_db->quoteName('g'));
				$sub_query->where($_db->quoteName('g') . '.' . $_db->quoteName('group_id') . ' IN (' . implode(',', $usergroup) . ')');

				$query->select('COUNT(' . $_db->quoteName('u') . '.' . $_db->quoteName('id') . ')');
				$query->from($_db->quoteName('#__users') . ' AS ' . $_db->quoteName('u'));
				$query->where($_db->quoteName('u') . '.' . $_db->quoteName('block') . ' = ' . (int) 0);
				$query->where($_db->quoteName('u') . '.' . $_db->quoteName('activation') . ' = ' . $_db->quote(''));
				$query->where($_db->quoteName('u') . '.' . $_db->quoteName('id') . ' IN (' . $sub_query . ')');

				$_db->setQuery($query);
				$count_users = $_db->loadResult();
			}

			// We return only false, if no subscribers AND no joomla users are selected.
			if (!$count_users && !$count_subscribers)
			{
				if (!$count_users)
				{
					$ret_msg = JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_NL_NO_USERS');

					return false;
				}
				if (!$count_subscribers)
				{
					$ret_msg = JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_NL_NO_SUBSCRIBERS');

					return false;
				}
			}
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
		return true;
	}

	/**
	 * Method to check if there are test-recipients to whom the newsletter shall be send
	 *
	 * @access	public
	 *
	 * @return 	boolean
	 *
	 * @since
	 */
	public function checkTestrecipients()
	{
		$result         = false;
		$testrecipients = 0;
		$_db	        = $this->_db;
		$query	= $_db->getQuery(true);

		$query->select('COUNT(' . $_db->quoteName('id') . ')');
		$query->from($_db->quoteName('#__bwpostman_subscribers'));
		$query->where($_db->quoteName('status') . ' = ' . (int) 9);
		$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);

		$_db->setQuery($query);

		try
		{
			$testrecipients = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		if ($testrecipients) {
			$result = true;
		}
		return $result;
	}

	/**
	 * Method to compose a newsletter out of the selected content items
	 *
	 * @access	public
	 *
	 * @return 	string $content  associative array of content data
	 *
	 * @since
	 */
	public function composeNl()
	{
		$jinput	= JFactory::getApplication()->input;

		$nl_content			= $jinput->get('selected_content');
//		$nl_subject			= $jinput->get('subject');
		$template_id		= $jinput->get('template_id');
		$text_template_id	= $jinput->get('text_template_id');
		$renderer			= new contentRenderer();
		$content			= $renderer->getContent($nl_content, $template_id, $text_template_id);

		return $content;
	}

	/**
	 * Method to fetch the content out of the selected content items
	 *
	 * @access	public
	 *
	 * @since
	 */
	public function changeTab()
	{
		$app				= JFactory::getApplication();
		$jinput				= JFactory::getApplication()->input;
		$form_data			= $jinput->get('jform', '', 'array');
		$layout				= $jinput->get('layout', '', 'string');
		$add_content		= $jinput->get('add_content', 0);
		$sel_content		= $jinput->get('selected_content_old', '', 'string');
		$old_template		= $jinput->get('template_id_old', '', 'string');
		$old_text_template	= $jinput->get('text_template_id_old', '', 'string');
		$state_data			= $app->getUserState('com_bwpostman.edit.newsletter.data');

		// heal form fields
		switch ($layout)
		{
			case 'edit_basic':
					if(is_object($state_data) && property_exists($state_data, 'html_version'))
					{
						$form_data['html_version']	= $state_data->html_version;
					}
					if(is_object($state_data) && property_exists($state_data, 'text_version'))
					{
						$form_data['text_version']	= $state_data->text_version;
					}
				break;
			case 'edit_html':
					$form_data['attachment']		= $state_data->attachment;
					$form_data['text_version']		= $state_data->text_version;
					$form_data['campaign_id']		= $state_data->campaign_id;
					$form_data['usergroups']		= $state_data->usergroups;
					$form_data['template_id']		= $state_data->template_id;
					$form_data['text_template_id']	= $state_data->text_template_id;
					if (is_object($state_data) && property_exists($state_data, 'ml_available'))
					{
						$form_data['ml_available']	    = $state_data->ml_available;
					}
					if (is_object($state_data) && property_exists($state_data, 'ml_unavailable'))
					{
						$form_data['ml_unavailable']	= $state_data->ml_unavailable;
					}
					if (is_object($state_data) && property_exists($state_data, 'ml_intern'))
					{
						$form_data['ml_intern']			= $state_data->ml_intern;
					}
				break;
			case 'edit_text':
					$form_data['attachment']		= $state_data->attachment;
					$form_data['html_version']		= $state_data->html_version;
					$form_data['campaign_id']		= $state_data->campaign_id;
					$form_data['usergroups']		= $state_data->usergroups;
					$form_data['template_id']		= $state_data->template_id;
					$form_data['text_template_id']	= $state_data->text_template_id;
					if (is_object($state_data) && property_exists($state_data, 'ml_available'))
					{
						$form_data['ml_available']		= $state_data->ml_available;
					}
					if (is_object($state_data) && property_exists($state_data, 'ml_unavailable'))
					{
						$form_data['ml_unavailable']	= $state_data->ml_unavailable;
					}
					if (is_object($state_data) && property_exists($state_data, 'ml_intern'))
					{
						$form_data['ml_intern']			= $state_data->ml_intern;
					}
				break;
			case 'edit_preview':
					$form_data['attachment']		= $state_data->attachment;
					$form_data['html_version']		= $state_data->html_version;
					$form_data['text_version']		= $state_data->text_version;
					$form_data['campaign_id']		= $state_data->campaign_id;
					$form_data['usergroups']		= $state_data->usergroups;
					$form_data['template_id']		= $state_data->template_id;
					$form_data['text_template_id']	= $state_data->text_template_id;
					if (is_object($state_data) && property_exists($state_data, 'ml_available'))
					{
						$form_data['ml_available']		= $state_data->ml_available;
					}
					if (is_object($state_data) && property_exists($state_data, 'ml_unavailable'))
					{
						$form_data['ml_unavailable']	= $state_data->ml_unavailable;
					}
					if (is_object($state_data) && property_exists($state_data, 'ml_intern'))
					{
						$form_data['ml_intern']			= $state_data->ml_intern;
					}
				break;
			case 'edit_send':
					$form_data['attachment']		= $state_data->attachment;
					$form_data['html_version']		= $state_data->html_version;
					$form_data['text_version']		= $state_data->text_version;
					$form_data['campaign_id']		= $state_data->campaign_id;
					$form_data['usergroups']		= $state_data->usergroups;
					$form_data['template_id']		= $state_data->template_id;
					$form_data['text_template_id']	= $state_data->text_template_id;
					if (is_object($state_data) && property_exists($state_data, 'ml_available'))
					{
						$form_data['ml_available']		= $state_data->ml_available;
					}
					if (is_object($state_data) && property_exists($state_data, 'ml_unavailable'))
					{
						$form_data['ml_unavailable']	= $state_data->ml_unavailable;
					}
					if (is_object($state_data) && property_exists($state_data, 'ml_intern'))
					{
						$form_data['ml_intern']			= $state_data->ml_intern;
					}
				break;
			default:
					$form_data['html_version']		= $state_data->html_version;
					$form_data['text_version']		= $state_data->text_version;
					$form_data['campaign_id']		= $state_data->campaign_id;
					$form_data['usergroups']		= $state_data->usergroups;
					$form_data['template_id']		= $state_data->template_id;
					$form_data['text_template_id']	= $state_data->text_template_id;
					if (is_object($state_data) && property_exists($state_data, 'ml_available'))
					{
						$form_data['ml_available']		= $state_data->ml_available;
					}
					if (is_object($state_data) && property_exists($state_data, 'ml_unavailable'))
					{
						$form_data['ml_unavailable']	= $state_data->ml_unavailable;
					}
					if (is_object($state_data) && property_exists($state_data, 'ml_intern'))
					{
						$form_data['ml_intern']			= $state_data->ml_intern;
					}
				break;
		}

		if (array_key_exists('selected_content', $form_data) !== true)
			$form_data['selected_content'] = array();
		if (array_key_exists('usergroups', $form_data) !== true)
			$form_data['usergroups'] = array();

		// serialize selected_content
		$nl_content	= (array) $form_data['selected_content'];
		if (is_array($form_data['selected_content']))
			$form_data['selected_content']	= implode(',', $form_data['selected_content']);

		// some content or template has changed?
		if ($add_content)
		{
			if (($sel_content != $form_data['selected_content'])
				|| ($old_template != $form_data['template_id'])
				|| ($old_text_template != $form_data['text_template_id']))
			{
				if ($add_content == '-1'  && (count($nl_content) == 0))
					$nl_content =  (array) "-1";

				// only render new content, if selection from article list or template has changed
				$renderer	= new contentRenderer();
				$content	= $renderer->getContent($nl_content, $form_data['template_id'], $form_data['text_template_id']);

				$form_data['html_version']	= $content['html_version'];
				$form_data['text_version']	= $content['text_version'];

				// add intro to form data
				if ($sel_content != $form_data['selected_content'] || $old_template != $form_data['template_id'])
				{
					$tpl = self::getTemplate($form_data['template_id']);
					if (is_object($tpl) && key_exists('intro_headline', $tpl->intro))
					{
						$form_data['intro_headline']	= $tpl->intro['intro_headline'];
					}

					if (is_object($tpl) && key_exists('intro_text', $tpl->intro))
					{
						$form_data['intro_text']		= $tpl->intro['intro_text'];
					}
				}
				if ($sel_content != $form_data['selected_content'] || $old_text_template != $form_data['text_template_id'])
				{
					$tpl = self::getTemplate($form_data['text_template_id']);
					if (is_object($tpl) && key_exists('intro_headline', $tpl->intro))
					{
						$form_data['intro_text_headline'] = $tpl->intro['intro_headline'];
					}

					if (is_object($tpl) && key_exists('intro_text', $tpl->intro))
					{
						$form_data['intro_text_text'] = $tpl->intro['intro_text'];
					}
				}
				$form_data['template_id_old']		= $form_data['template_id'];
				$form_data['text_template_id_old']	= $form_data['text_template_id'];
			}
		}
		else
		{
			$form_data['selected_content']	= $state_data->selected_content;
			// if change of content not confirmed don't change template_id
			$form_data['template_id']		= $state_data->template_id;
			$form_data['text_template_id']	= $state_data->text_template_id;
		}

		// convert form data to object to update state
		$data = new stdClass();
		foreach ($form_data as $k => $v)
		{
			$data->$k = $v;
		}

		$app->setUserState('com_bwpostman.edit.newsletter.data', $data);
		$app->setUserState('com_bwpostman.edit.newsletter.changeTab', true);
	}

	/**
	 * Method to prepare the sending of a newsletter
	 *
	 * @access	public
	 *
	 * @param	string	$ret_msg        Error message
	 * @param 	string	$recipients     Recipient --> either recipients or test-recipients
	 * @param 	int		$nl_id          Newsletter ID
	 * @param 	boolean	$unconfirmed    Send to unconfirmed or not
	 * @param	int		$cam_id         campaign id
	 *
	 * @return	boolean	                False if there occurred an error
	 *
	 * @since
	 */
	public function sendNewsletter(&$ret_msg, $recipients, $nl_id, $unconfirmed, $cam_id)
	{
		// Access check
		if (!BwPostmanHelper::canSend($nl_id))
		{
			return false;
		}

		// Prepare the newsletter content
		$id	= $this->_addSendMailContent($nl_id);
		if ($id	=== false)
		{
			$ret_msg	= JText::_('COM_BWPOSTMAN_NL_ERROR_CONTENT_PREPARING');
			return false;
		}

		// Prepare the recipient queue
		if (!$this->_addSendMailQueue($ret_msg, $id, $recipients, $nl_id, $unconfirmed, $cam_id))
			return false;

		// Update the newsletters table, to prevent repeated sending of the newsletter
		if ($recipients == 'recipients')
		{
			$tblNewsletters = $this->getTable('newsletters', 'BwPostmanTable');
			$tblNewsletters->markAsSent($nl_id);
		}
		return true;

		// The actual sending of the newsletter is executed only in
		// Sendmail Queue layout.
	}

	/**
	 * Method to reset the count of sending attempts in sendmailqueue.
	 *
	 * @return bool
	 *
	 * @since
	 */
	public function resetSendAttempts()
	{
		// Access check
		if (!BwPostmanHelper::canResetQueue())
		{
			return false;
		}

		$tblSendmailQueue = $this->getTable('sendmailqueue', 'BwPostmanTable');
		$tblSendmailQueue->resetTrials();
		return true;
	}

	/**
	 * Method to get the selected content
	 *
	 * @access	public
	 *
	 * @return	string
	 *
	 * @since
	 */
	public function _selectedContent()
	{
		$content_ids    = '';
		$_db	        = $this->_db;

		// Get selected content from the newsletters-Table
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('selected_content'));
		$query->from($_db->quoteName('#__bwpostman_newsletters'));
		$query->where($_db->quoteName('id') . ' = ' . (int) $this->getState($this->getName() . '.id'));

		$_db->setQuery($query);
		try
		{
			$content_ids = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $content_ids;
	}

	/**
	 * Method to replace the links in a newsletter to provide the correct preview
	 *
	 * @access	private
	 *
	 * @param 	string $text        HTML-/Text-version
	 *
	 * @return 	boolean
	 *
	 * @since
	 *
	 */
	static private function _replaceLinks(&$text)
	{
		$search_str = '/\s+(href|src)\s*=\s*["\']?\s*(?!http|mailto|#)([\w\s&%=?#\/\.;:_-]+)\s*["\']?/i';
		$text = preg_replace($search_str,' ${1}="'.JUri::root().'${2}"',$text);
		return true;
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
	 * @since	1.1.0
	 */
	public function getTemplate($template_id)
	{
		$tpl    = new stdClass();
		$params = JComponentHelper::getParams('com_bwpostman');

		if (is_null($template_id))
			$template_id = '1';

		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);
		$query->select($_db->quoteName('id'));
		$query->select($_db->quoteName('tpl_html'));
		$query->select($_db->quoteName('tpl_css'));
		$query->select($_db->quoteName('tpl_article'));
		$query->select($_db->quoteName('tpl_divider'));
		$query->select($_db->quoteName('tpl_id'));
		$query->select($_db->quoteName('basics'));
		$query->select($_db->quoteName('article'));
		$query->select($_db->quoteName('intro'));
		$query->from($_db->quoteName('#__bwpostman_templates'));
		$query->where($_db->quoteName('id') . ' = ' . $template_id);

		$_db->setQuery($query);
		try
		{
			$tpl = $_db->loadObject();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		if (is_string($tpl->basics))
		{
			$registry = new JRegistry;
			$registry->loadString($tpl->basics);
			$tpl->basics = $registry->toArray();
		}

		if (is_string($tpl->article))
		{
			$registry = new JRegistry;
			$registry->loadString($tpl->article);
			$tpl->article = $registry->toArray();
		}

		if (is_string($tpl->intro))
		{
			$registry = new JRegistry;
			$registry->loadString($tpl->intro);
			$tpl->intro = $registry->toArray();
		}

		// only for old templates
		if (empty($tpl->article))
		{
			$tpl->article['show_createdate'] = $params->get('newsletter_show_createdate');
			$tpl->article['show_author'] = $params->get('newsletter_show_author');
			$tpl->article['show_readon'] = 1;
		}
		return $tpl;
	}

	/**
	 * Method to add the Template-Tags to the content
	 *
	 * @access	private
	 *
	 * @param   string  $text
	 * @param   int     $id
	 *
	 * @return 	boolean
	 *
	 * @since	1.1.0
	 */
	private function _addTplTags (&$text, &$id)
	{
		$tpl = self::getTemplate($id);

		$newtext	= $tpl->tpl_html."\n";

		$text		= str_replace('[%content%]', $text, $newtext);

		return true;

	}

	/**
	 * Method to replace edit and unsubscribe link
	 *
	 * @access	private
	 *
	 * @param   string  $text
	 *
	 * @return 	boolean
	 *
	 * @since	1.1.0
	 */
	private function _replaceTplLinks (&$text)
	{

		// replace edit and unsubscribe link
		$replace1	= '<a href="[EDIT_HREF]">' . JText::_('COM_BWPOSTMAN_TPL_UNSUBSCRIBE_LINK_TEXT') . '</a>';
		$text		= str_replace('[%unsubscribe_link%]', $replace1, $text);
		$replace2	= '<a href="[EDIT_HREF]">' . JText::_('COM_BWPOSTMAN_TPL_EDIT_LINK_TEXT') . '</a>';
		$text		= str_replace('[%edit_link%]', $replace2, $text);

		return true;
	}

	/**
	 * Method to add the HTML-Tags and the css to the HTML-Newsletter
	 *
	 * @access	private
	 *
	 * @param 	string  $text      HTML newsletter
	 * @param   int     $id
	 *
	 * @return 	boolean
	 *
	 * @since
	 */
	private function _addHtmlTags (&$text, &$id)
	{
		$params = JComponentHelper::getParams('com_bwpostman');
		$tpl    = self::getTemplate($id);

		$newtext = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
		$newtext .= '<html>'."\n";
		$newtext .= ' <head>'."\n";
		$newtext .= '   <title>Newsletter</title>'."\n";
		$newtext .= '   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'."\n";
		$newtext .= '   <meta name="robots" content="noindex,nofollow" />'."\n";
		$newtext .= '   <meta property="og:title" content="HTML Newsletter" />'."\n";
		$newtext .= '   <style type="text/css">'."\n";
		$newtext .= '   '.$tpl->tpl_css."\n";
			// only for old newsletters with template_id < 1
		if ($id < 1 && $params->get('use_css_for_html_newsletter') == 1)
		{
			$params	= JComponentHelper::getParams('com_bwpostman');
			$css	= $params->get('css_for_html_newsletter');
			$newtext .= '   '.$css."\n";
		}
		if (isset($tpl->basics['custom_css']))
		{
			$newtext .= $tpl->basics['custom_css']."\n";
		}
		$newtext .= '   </style>'."\n";
		$newtext .= ' </head>'."\n";

		if (isset($tpl->basics['paper_bg']))
		{
			$newtext .= ' <body bgcolor="'. $tpl->basics['paper_bg'] .'" emb-default-bgcolor="'. $tpl->basics['paper_bg'] .'" style="background-color:'. $tpl->basics['paper_bg'] .';color:'. $tpl->basics['legal_color'] .';">'."\n";
		}
		else
		{
			$newtext .= ' <body bgcolor="#ffffff" emb-default-bgcolor="#ffffff">'."\n";
		}
		$newtext .= $text."\n";
		$newtext .= ' </body>'."\n";
		$newtext .= '</html>'."\n";

 		$text =  $newtext;

		return true;
	}

	/**
	 * Method to add the HTML-footer to the HTML-Newsletter
	 *
	 * @access	private
	 *
	 * @param 	string  $text   HTML newsletter
	 * @param   int     $id
	 *
	 * @return 	boolean
	 *
	 * @since
	 */
	private function _addHTMLFooter(&$text, &$id)
	{
		$uri  				= JUri::getInstance();
		$params 			= JComponentHelper::getParams('com_bwpostman');
		$impressum			= "<br /><br />" . $params->get('legal_information_text');
		$impressum			= nl2br($impressum, true);

		if (strpos($text, '[%impressum%]') !== false)
		{
			// replace [%impressum%]
			$replace = "<br /><br />" . JText::sprintf('COM_BWPOSTMAN_NL_FOOTER_HTML', $uri->root()) . $impressum;
			$replace3  = '   <table id="legal" cellspacing="0" cellpadding="0" border="0" style="table-layout: fixed; width: 100%;"><tbody>';
			$replace3 .= '     <tr>'."\n";
			$replace3 .= '       <td  id="legal_td">'."\n";
			$replace3 .= '         <table class="one-col legal" style="border-collapse: collapse;border-spacing: 0;"><tbody>'."\n";
			$replace3 .= '          <tr>'."\n";
			$replace3 .= '           <td class="legal_td">'."\n";
			$replace3 .= $replace."<br /><br />\n";
			$replace3 .= '           </td>'."\n";
			$replace3 .= '          </tr>'."\n";
			$replace3 .= '         </tbody></table>'."\n";
			$replace3 .= '       </td>'."\n";
			$replace3 .= '     </tr>'."\n";
			$replace3 .= '   </tbody></table>'."\n";
			$text = str_replace('[%impressum%]', $replace3, $text);
		}
		// only for old newsletters with template_id < 1
		if ($id < 1)
		{
			$replace = JText::_('COM_BWPOSTMAN_NL_FOOTER_HTML_LINE') . JText::sprintf('COM_BWPOSTMAN_NL_FOOTER_HTML', $uri->root()) . $impressum;
			$text = str_replace("[dummy]", "<div class=\"footer-outer\"><p class=\"footer-inner\">{$replace}</p></div>", $text);
		}

		return true;
	}

	/**
	 * Method to add the TEXT to the TEXT-Newsletter
	 *
	 * @access	private
	 *
	 * @param 	string  $text   Text newsletter
	 * @param   int     $id     template id
	 *
	 * @return 	boolean
	 *
	 * @since	1.1.0
	 */
	private function _addTextTpl (&$text, &$id)
	{
		$tpl	= self::getTemplate($id);

		$text	= str_replace('[%content%]', "\n" . $text, $tpl->tpl_html);

	return true;
	}

	/**
	 * Method to replace edit and unsubscribe link
	 *
	 * @access	private
	 *
	 * @param   string  $text
	 *
	 * @return 	boolean
	 *
	 * @since	1.1.0
	 */
	private function _replaceTextTplLinks (&$text)
	{
		$uri  				= JUri::getInstance();
		$itemid_edit		= $this->getItemid('edit');

		// replace edit and unsubscribe link
		$replace1	= '+ ' . JText::_('COM_BWPOSTMAN_TPL_UNSUBSCRIBE_LINK_TEXT') . " +\n  " . $uri->root() . 'index.php?option=com_bwpostman&amp;Itemid='. $itemid_edit . '&amp;view=edit&amp;task=unsub&amp;editlink=[EDITLINK]';
		$text		= str_replace('[%unsubscribe_link%]', $replace1, $text);
		$replace2	= '+ ' . JText::_('COM_BWPOSTMAN_TPL_EDIT_LINK_TEXT') . " +\n  " . $uri->root() . 'index.php?option=com_bwpostman&amp;Itemid='. $itemid_edit . '&amp;view=edit&amp;editlink=[EDITLINK]';
		$text		= str_replace('[%edit_link%]', $replace2, $text);

	return true;
	}

	/**
	 * Method to add the footer Text-Newsletter
	 *
	 * @access	private
	 *
	 * @param 	string  $text   Text newsletter
	 * @param   int     $id
	 *
	 * @return 	boolean
	 *
	 * @since
	 */
	private function _addTextFooter (&$text, &$id)
	{
		$uri  				= JUri::getInstance();
		$itemid_unsubscribe	= $this->getItemid('register');
		$itemid_edit		= $this->getItemid('edit');
		$params 			= JComponentHelper::getParams('com_bwpostman');
		$impressum			= "\n\n" . $params->get('legal_information_text') . "\n\n";

		if (strpos($text, '[%impressum%]') !== false)
		{
			// replace [%impressum%]
			$replace	= "\n\n" . JText::sprintf('COM_BWPOSTMAN_NL_FOOTER_TEXT', $uri->root(), $uri->root(), $itemid_unsubscribe, $uri->root(), $itemid_edit) . $impressum;
			$text		= str_replace('[%impressum%]', $replace, $text);
		}
		// only for old newsletters with template_id < 1
		if ($id < 1)
		{
			$replace	= JText::_('COM_BWPOSTMAN_NL_FOOTER_TEXT_LINE') . JText::sprintf('COM_BWPOSTMAN_NL_FOOTER_TEXT', $uri->root(), $uri->root(), $itemid_unsubscribe, $uri->root(), $itemid_edit) . $impressum;
			$text		= str_replace("[dummy]", $replace, $text);
		}

		return true;
	}

	/**
	 * Method to get ID of actual content ID of a newsletter from content table
	 *
	 * @access	private
	 *
	 * @param 	int 	$nl_id      newsletter ID
	 *
	 * @return 	int		content ID
	 *
	 * @since
	 */
	private function _getSingleContentId($nl_id)
	{
		$app	= JFactory::getApplication();
		$result = '';
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__bwpostman_sendmailcontent'));
		$query->where($_db->quoteName('nl_id') . ' = ' . (int) $nl_id);
		$_db->setQuery($query);

		try
		{
			$result = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		return $result;
	}


	/**
	 * If a newsletter shall be sent, then it will inserted at table sendMailContent
	 * as a manner of archive and process method completely with content,
	 * subject & Co. in
	 *
	 * @access	private
	 *
	 * @param 	int		        $nl_id          Newsletter ID
	 *
	 * @return 	int|boolean 	                int content ID, if everything went fine, else boolean false
	 *
	 * @since
	 */
	private function _addSendMailContent($nl_id)
	{
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$newsletters_data   = new stdClass();

		// Get the SendmailContent ID
//		$content_id = $this->_getSingleContentId($nl_id);

		if ($nl_id)
		{
			$query->select('*');
			$query->from($_db->quoteName('#__bwpostman_newsletters'));
			$query->where($_db->quoteName('id') . ' = ' . (int) $nl_id);

			$_db->setQuery($query);

			try
			{
				$newsletters_data = $_db->loadObject();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}
		else
		{
			return false;
		}

		// Initialize the sendmailContent
		$tblSendmailContent = $this->getTable('sendmailcontent', 'BwPostmanTable');

		// Copy the data from newsletters to sendmailContent
		$tblSendmailContent->nl_id 			= $newsletters_data->id;
		$tblSendmailContent->from_name 		= $newsletters_data->from_name;
		$tblSendmailContent->from_email 	= $newsletters_data->from_email;
		$tblSendmailContent->subject 		= $newsletters_data->subject;
		$tblSendmailContent->attachment		= $newsletters_data->attachment;
		$tblSendmailContent->cc_email 		= null;
		$tblSendmailContent->bcc_email 		= null;
		$tblSendmailContent->reply_email 	= $newsletters_data->reply_email;
		$tblSendmailContent->reply_name	 	= $newsletters_data->from_name;

		// Preprocess html version of the newsletter

		// only for old text templates
		if ($newsletters_data->template_id < 1)
		{
			$newsletters_data->html_version = $newsletters_data->html_version . '[dummy]';
		}
		// add template data
		if (!$this->_addTplTags($newsletters_data->html_version, $newsletters_data->template_id))
			return false;

		// Replace the intro at HTML part of the newsletter
		$replace_html_intro_head  = '';
		if (!empty($newsletters_data->intro_headline))
		{
			$replace_html_intro_head  = $newsletters_data->intro_headline;
		}
		$newsletters_data->html_version	= str_replace('[%intro_headline%]', $replace_html_intro_head, $newsletters_data->html_version);

		$replace_html_intro_text  = '';
		if (!empty($newsletters_data->intro_text))
		{
			$replace_html_intro_text   = nl2br($newsletters_data->intro_text, true);
		}
		$newsletters_data->html_version		= str_replace('[%intro_text%]', $replace_html_intro_text, $newsletters_data->html_version);

		if (!$this->_replaceTplLinks($newsletters_data->html_version))
			return false;
		if (!$this->_addHtmlTags($newsletters_data->html_version, $newsletters_data->template_id))
			return false;
		if (!$this->_addHTMLFooter($newsletters_data->html_version, $newsletters_data->template_id))
			return false;
		if (!$this->_replaceLinks($newsletters_data->html_version))
			return false;

		// Preprocess text version of the newsletter
		// only for old text templates
		if ($newsletters_data->text_template_id < 1)
		{
			$newsletters_data->text_version = $newsletters_data->text_version . '[dummy]';
		}
		// add template data
		if (!$this->_addTextTpl($newsletters_data->text_version, $newsletters_data->text_template_id)) return false;

		// Replace the intro at text part of the newsletter
		$replace_text_intro_head  = '';
		if (!empty($newsletters_data->intro_text_headline))
		{
			$replace_text_intro_head    = $newsletters_data->intro_text_headline;
		}
		$newsletters_data->text_version	= str_replace('[%intro_headline%]', $replace_text_intro_head, $newsletters_data->text_version);

		$replace_text_intro_text  = '';
		if (!empty($newsletters_data->intro_text_text))
		{
			$replace_text_intro_text    = $newsletters_data->intro_text_text;
		}
		$newsletters_data->text_version	= str_replace('[%intro_text%]', $replace_text_intro_text, $newsletters_data->text_version);

		if (!$this->_replaceTextTplLinks($newsletters_data->text_version))
			return false;
		if (!$this->_addTextFooter($newsletters_data->text_version, $newsletters_data->text_template_id))
			return false;
		if (!$this->_replaceLinks($newsletters_data->text_version))
			return false;

		// We have to create two entries in the sendmailContent table. One entry for the text mail body and one for the html mail.
		for ($mode = 0;$mode <= 1; $mode++)
		{
			// Set the body and the id, if exists
			if ($mode == 0)
			{
				$tblSendmailContent->body = $newsletters_data->text_version;
			}
			else
			{
				$tblSendmailContent->body = $newsletters_data->html_version;
			}

			// Set the mode (0=text,1=html)
			$tblSendmailContent->mode = $mode;

			// Store the data into the sendmailcontent-table
			// First run generates a new id, which will be used also for the second run.
			if (!$tblSendmailContent->store())
			{
				return false;
			}
		}

		$id = $tblSendmailContent->id;

		return $id;
	}

	/**
	 * Method to push the recipients into a queue
	 *
	 * @access	private
	 *
	 * @param	string	$ret_msg                Error message
	 * @param 	int		$content_id             Content ID -->  --> from the sendmailcontent-Table
	 * @param 	string	$recipients             Recipient --> either subscribers or test-recipients
	 * @param 	int		$nl_id                  Newsletter ID
	 * @param	boolean	$send_to_unconfirmed    Status --> 0 = do not send to unconfirmed, 1 = sent also to unconfirmed
	 * @param	int		$cam_id                 campaign id
	 *
	 * @return 	boolean False if there occurred an error
	 *
	 * @since
	 */
	private function _addSendMailQueue(&$ret_msg, $content_id, $recipients, $nl_id, $send_to_unconfirmed, $cam_id)
	{
		// @ToDo: Send also to unconfirmed does not work!!!!!!
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		if (!$content_id)
			return false;

		if (!$nl_id)
		{
			$ret_msg	= JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_TECHNICAL_REASON');
			return false;
		}

		$mailinglists   = array();

		switch ($recipients)
		{
			case "recipients": // Contain subscribers and joomla users
				$tblSendmailQueue = $this->getTable('sendmailqueue', 'BwPostmanTable');

				if ($cam_id != '-1')
				{
					$query->select($_db->quoteName('mailinglist_id'));
					$query->from($_db->quoteName('#__bwpostman_campaigns_mailinglists'));
					$query->where($_db->quoteName('campaign_id') . ' = ' . (int) $cam_id);
				}
				else
				{
					$query->select($_db->quoteName('mailinglist_id'));
					$query->from($_db->quoteName('#__bwpostman_newsletters_mailinglists'));
					$query->where($_db->quoteName('newsletter_id') . ' = ' . (int) $nl_id);
				}

				$_db->setQuery($query);

				try
				{
					$mailinglists = $_db->loadObjectList();
				}
				catch (RuntimeException $e)
				{
					JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				}

				if (!$mailinglists)
				{
					$ret_msg	= JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_NO_MAILINGLISTS');
					return false;
				}

				$send_subscribers = 0;
				$send_to_all = 0;
				$users = array();

				foreach ($mailinglists as $mailinglist)
				{
					$mailinglist_id = $mailinglist->mailinglist_id;
					// Mailinglists
					if ($mailinglist_id > 0)
						$send_subscribers = 1;
					// All subscribers
					if ($mailinglist_id == -1)
					{
						$send_to_all = 1;
					}
					else
					{
						// Usergroups
						if ((int) $mailinglist_id < 0) $users[] = -(int) $mailinglist_id;
					}
				}

				if ($send_to_all)
				{
					if ($send_to_unconfirmed)
					{
						$status = '0,1,9';
					}
					else
					{
						$status = '1,9';
					}
					if (!$tblSendmailQueue->pushAllSubscribers($content_id, $status))
					{
						$ret_msg	= JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_TECHNICAL_REASON');
						return false;
					}
				}

				if (count($users))
				{
					$params = JComponentHelper::getParams('com_bwpostman');
					if (!$tblSendmailQueue->pushJoomlaUser($content_id, $users, $params->get('default_emailformat')))
					{
						$ret_msg	= JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_TECHNICAL_REASON');
						return false;
					}
				}

				if ($send_subscribers)
				{
					if ($send_to_unconfirmed)
					{
						$status = '0,1';
					}
					else
					{
						$status = '1';
					}
					if (!$tblSendmailQueue->pushAllFromNlId($nl_id, $content_id, $status, $cam_id)){
						$ret_msg	= JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_TECHNICAL_REASON');
						return false;
					}
				}
			break;

			case "testrecipients":
				$tblSubscribers		= $this->getTable('subscribers', 'BwPostmanTable');
				$testrecipients		= $tblSubscribers->loadTestrecipients();
				$tblSendmailQueue	= $this->getTable('sendmailqueue', 'BwPostmanTable');

				if(sizeof($testrecipients) > 0)
				{
					foreach($testrecipients AS $testrecipient)
						$tblSendmailQueue->push($content_id, $testrecipient->emailformat, $testrecipient->email, $testrecipient->name, $testrecipient->firstname, $testrecipient->id);
				}
				break;

			default:
				$ret_msg	= JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_TECHNICAL_REASON');
		}
		return true;
	}

	/**
	 * Check number of trials
	 *
	 * @param	int		$trial
	 * @param   int     $count
	 *
	 * @return	bool|int	true if no entries or there are entries with number trials less than 2, otherwise false
	 *
	 * @since 1.0.3
	 */
	public function checkTrials($trial = 2, $count = 0)
	{
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);
		$result = 0;

		$query->select('COUNT(' . $_db->quoteName('id') . ')');
		$query->from($_db->quoteName('#__bwpostman_sendmailqueue'));

		$_db->setQuery($query);
		try
		{
			$result = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// returns only number of entries
		if ($count != 0)
			return $result;

		// queue not empty
		if ($result != 0)
		{
			$query->where($_db->quoteName('trial') . ' < ' . (int) $trial);
			$_db->setQuery($query);
			// all queue entries have trial number 2
			try
			{
				$result = $_db->loadResult();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
			if ($result == 0) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Make partial send. Send only, say like 50 newsletters and the next 50 in a next call.
	 *
	 * @param int   $mailsPerStep     number mails to send
	 *
	 * @return int	0 -> queue is empty, 1 -> maximum reached
	 *
	 * @since
	 */
	public function sendMailsFromQueue($mailsPerStep = 100)
	{
		$sendMailCounter = 0;
		echo JText::_('COM_BWPOSTMAN_NL_SENDING_PROCESS');
		ob_flush();
		flush();

		while(1)
		{
			$ret = $this->sendMail(true);
			if ($ret == 0){                              // Queue is empty!
				return 0;
				break;
			}
			$sendMailCounter++;
			if ($sendMailCounter >= $mailsPerStep)
			{     // Maximum is reached.
				return 1;
				break;
			}
		}
		return 0;
	}

	/**
	 * Method to send a *single* newsletter to a recipient from sendMailQueue.
	 * CAUTION! This always begins with the first entry! If there are entries left from previous attempts,
	 * then it begins with them!
	 *
	 * @param	bool 	true if we came from component
	 *
	 * @return	int		(-1, if there was an error; 0, if no mail addresses left in the queue; 1, if one Mail was send).
	 *
	 * @since
	 */
	public function sendMail($fromComponent = false)
	{
		// initialize
		$app				= JFactory::getApplication();
		$uri  				= JUri::getInstance();
		$itemid_unsubscribe	= $this->getItemid('register');
		$itemid_edit		= $this->getItemid('edit');

		$log_options        = array('test' => 'testtext');
		$logger             = new BwLogger($log_options);

		JPluginHelper::importPlugin('bwpostman');
		$dispatcher = JEventDispatcher::getInstance();

		$res				= false;
		$_db				= $this->_db;
		$query				= $_db->getQuery(true);
		$table_name			= '#__bwpostman_sendmailqueue';
		$recipients_data	= new stdClass();

		// getting object for queue and content
		$tblSendMailQueue	= $this->getTable('sendmailqueue', 'BwPostmanTable');
		$tblSendMailContent	= $this->getTable('sendmailcontent', 'BwPostmanTable');

		// trigger BwTimeControl event, if we come not from component
		// needed for changing table objects for queue and content, show/hide messages, ...
		if (!$fromComponent)
		{
			$dispatcher->trigger('onBwPostmanBeforeNewsletterSend', array(&$table_name, &$tblSendMailQueue, &$tblSendMailContent));
		}

		// Get first entry from sendmailqueue
		// Nothing has been returned, so the queue should be empty
		if (!$tblSendMailQueue->pop())
		{
			return 0;
		}

		// rewrite some property names
		if (property_exists($tblSendMailQueue, 'tc_content_id'))
		{
			$tblSendMailQueue->content_id	= $tblSendMailQueue->tc_content_id;
		}
		if (property_exists($tblSendMailQueue, 'email'))
		{
			$tblSendMailQueue->recipient	= $tblSendMailQueue->email;

			if ($this->_demo_mode)
			{
				$tblSendMailQueue->recipient   = $this->_dummy_recipient;
			}
		}

		$app->setUserState('com_bwpostman.newsletter.send.mode', $tblSendMailQueue->mode);

 		// Get Data from sendmailcontent, set attachment path
		// @ToDo, store data in this class to prevent from loading every time a mail will be sent
		$app->setUserState('bwtimecontrol.mode', $tblSendMailQueue->mode);
		$tblSendMailContent->load($tblSendMailQueue->content_id);

		if ($tblSendMailContent->attachment) $tblSendMailContent->attachment = JPATH_SITE . '/' . $tblSendMailContent->attachment;
		if (property_exists($tblSendMailContent, 'email')) $tblSendMailContent->content_id	= $tblSendMailContent->id;

		// check if subscriber is archived
		if ($tblSendMailQueue->subscriber_id)
		{
			$query->from('#__bwpostman_subscribers');
			$query->select('id');
			$query->select('editlink');
			$query->select('archive_flag');
			$query->select('status');
			$query->where('id = ' . (int) $tblSendMailQueue->subscriber_id);
			$_db->setQuery($query);
			try
			{
				$_db->execute();
			}
			catch (RuntimeException $e)
			{
				$app->enqueueMessage($e->getMessage(), 'error');
			}
			$recipients_data = $_db->loadObject();

			// if subscriber is archived, delete entry from queue
			if ($recipients_data->archive_flag)
			{
				$query->clear();
				$query->from($_db->quoteName($table_name));
				$query->delete();
				$query->where($_db->quoteName('subscriber_id') .' = ' . (int) $recipients_data->id);
				$_db->setQuery((string) $query);

				try
				{
					$_db->execute();
				}
				catch (RuntimeException $e)
				{
					$app->enqueueMessage($e->getMessage(), 'error');
				}
				return 1;
			}
		} // end archived-check

		$body = $tblSendMailContent->body;
		// Replace the links to provide the correct preview
		$footerid = 0;
		if ($tblSendMailQueue->mode == 1)
		{ // HTML newsletter
			if ($tblSendMailQueue->subscriber_id)
			{ // Add footer only if it is a subscriber
				$this->_replaceTplLinks($body);
				$this->_addHTMLFooter($body, $footerid);
			}
			else
			{
				$body = str_replace("[%edit_link%]", "", $body);
				$body = str_replace("[%unsubscribe_link%]", "", $body);
				$body = str_replace("[%impressum%]", "", $body);
				$body = str_replace("[dummy]", "", $body);
			}
		}
		else
		{ // Text newsletter
			if ($tblSendMailQueue->subscriber_id)
			{	// Add footer only if it is a subscriber
				$this->_replaceTextTplLinks($body);
				$this->_addTextFooter($body, $footerid);
			}
			else
			{
				$body = str_replace("[%edit_link%]", "", $body);
				$body = str_replace("[%unsubscribe_link%]", "", $body);
				$body = str_replace("[%impressum%]", "", $body);
				$body = str_replace("[dummy]", "", $body);
			}
		}
		$this->_replaceLinks($body);

		$fullname = '';
		if ($tblSendMailQueue->firstname != '')
		{
			$fullname = $tblSendMailQueue->firstname . ' ';
		}
		if ($tblSendMailQueue->name != '')
		{
			$fullname .= $tblSendMailQueue->name;
		}
		$fullname = trim($fullname);

		// Replace the dummies
		$body = str_replace("[NAME]", $tblSendMailQueue->name, $body);
		$body = str_replace("[LASTNAME]", $tblSendMailQueue->name, $body);
		$body = str_replace("[FIRSTNAME]", $tblSendMailQueue->firstname, $body);
		$body = str_replace("[FULLNAME]", $fullname, $body);
		// do not replace CODE by testrecipients
		if (isset($recipients_data->status) && $recipients_data->status != 9)
		{
			if (property_exists($recipients_data, 'editlink'))
			{
				$body = str_replace("[UNSUBSCRIBE_HREF]", JText::sprintf('COM_BWPOSTMAN_NL_UNSUBSCRIBE_HREF', $uri->root(), $itemid_unsubscribe), $body);
				$body = str_replace("[EDIT_HREF]", JText::sprintf('COM_BWPOSTMAN_NL_EDIT_HREF', $uri->root(), $itemid_edit), $body);
				$body = str_replace("[UNSUBSCRIBE_EMAIL]", $tblSendMailQueue->recipient, $body);
				$body = str_replace("[UNSUBSCRIBE_CODE]", $recipients_data->editlink, $body);
				$body = str_replace("[EDITLINK]", $recipients_data->editlink, $body);
			}
		}

		// Fire the onBwPostmanPersonalize event.
		if(JPluginHelper::isEnabled('bwpostman', 'personalize')
			&& !$dispatcher->trigger('onBwPostmanPersonalize', array('com_bwpostman.send', &$body, &$tblSendMailQueue->subscriber_id)))
		{
			$error_msg_plugin   = JText::_('COM_BWPOSTMAN_PERSONALIZE_ERROR');
			$app->enqueueMessage($error_msg_plugin, 'error');
			$logger->addEntry(new JLogEntry($error_msg_plugin));

			$tblSendMailQueue->push($tblSendMailQueue->content_id, $tblSendMailQueue->mode, $tblSendMailQueue->recipient, $tblSendMailQueue->name, $tblSendMailQueue->firstname, $tblSendMailQueue->subscriber_id, $tblSendMailQueue->trial + 1);
			return -1;
		}

		// Send Mail
		// show queue working only wanted if sending newsletters from component backend directly, not in time controlled sending
		if ($fromComponent)
		{
			echo "\n<br>{$tblSendMailQueue->recipient} (".JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_TRIAL').($tblSendMailQueue->trial + 1).") ... ";
			ob_flush();
			flush();
		}

		// Get a JMail instance
		$mailer		= JFactory::getMailer();
		$mailer->SMTPDebug = true;
		$sender		= array();

		$sender[0]	= $tblSendMailContent->from_email;
		$sender[1]	= $tblSendMailContent->from_name;

		if ($this->_demo_mode)
		{
			$sender[0]	                        = $this->_dummy_sender;
			$tblSendMailContent->reply_email	= $this->_dummy_sender;
		}

		$mailer->setSender($sender);
		$mailer->addReplyTo($tblSendMailContent->reply_email,$tblSendMailContent->reply_name);
		$mailer->addRecipient($tblSendMailQueue->recipient);
		$mailer->setSubject($tblSendMailContent->subject);
		$mailer->setBody($body);

		if ($tblSendMailContent->attachment)
		{
			$mailer->addAttachment($tblSendMailContent->attachment);
		}

		if ($tblSendMailQueue->mode == 1)
		{
			$mailer->isHtml(true);
		}

		// Newsletter sending 1=on, 0=off
		if (!defined ('BWPOSTMAN_NL_SENDING')) define ('BWPOSTMAN_NL_SENDING', 1);

		if (!$this->_arise_queue)
		{
			$res = $mailer->Send();
			// @ToDo: $res may be boolean of JException object!
			$logger->addEntry(new JLogEntry(sprintf('Sending result: %s', $res)));
		}

		if ($res === true)
		{
			if ($fromComponent)
			{
				echo JText::_('COM_BWPOSTMAN_NL_SENT_SUCCESSFULLY');
			}
			else
			{
				$app->enqueueMessage(sprintf('Error while sending: %s'), $res);
				// Sendmail was successful, flag "sent" in table TcContent has to be set
				$tblSendMailContent->setSent($tblSendMailContent->id);
				// and test-entries may be deleted
				if ($recipients_data->status == 9) {
					// @todo delete entry in content-table?
				}
			}
		}
		else
		{
			$app->enqueueMessage(sprintf('Error while sending: $s'), $res);
			// Sendmail was not successful, we need to add the recipient to the queue again.
			if ($fromComponent)
			{
				// show message only wanted if sending newsletters from component backend directly, not in time controlled sending
				echo JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING');
			}
			$tblSendMailQueue->push($tblSendMailQueue->content_id, $tblSendMailQueue->mode, $tblSendMailQueue->recipient, $tblSendMailQueue->name, $tblSendMailQueue->firstname, $tblSendMailQueue->subscriber_id, $tblSendMailQueue->trial + 1);
			return -1;
		}
		return 1;
	}

	/**
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function _processTestMode()
	{
		$test_plugin = JPluginHelper::getPlugin('system', 'bwtestmode');

		if ($test_plugin)
		{
			$params             = json_decode($test_plugin->params);

			$this->_demo_mode        = $params->demo_mode_option;
			$this->_dummy_sender     = $params->sender_address_option;
			$this->_dummy_recipient  = $params->recipient_address_option;
			$this->_arise_queue      = $params->arise_queue_option;
		}
	}
}

/**
 * Content Renderer Class
 * Provides methods render the selected contents from which the newsletters shall be generated
 * --> Referring to BwPostman 1.6 beta and Communicator 2.0.0rc1 (??)
 *
 * @package		BwPostman-Admin
 * @subpackage	Newsletters
 *
 * @since       0.9.1
 */
class contentRenderer
{
	/**
	 * Method to get the menu item ID for the content item
	 *
	 * @access	public
	 *
	 * @param   string  $row
	 *
	 * @return 	int     $itemid     menu item ID
	 *
	 * @since       0.9.1
	 */
	public function getItemid($row)
	{
		$itemid = 0;
		try
		{
			$_db   = JFactory::getDbo();
			$query = $_db->getQuery(true);

			$query->select($_db->quoteName('id'));
			$query->from($_db->quoteName('#__menu'));
			$query->where($_db->quoteName('link') . ' = ' . $_db->quote('index.php?option=com_bwpostman&view=' . $row));
			$query->where($_db->quoteName('published') . ' = ' . (int) 1);

			$_db->setQuery($query);

			$itemid = $_db->loadResult();

			if (empty($itemid))
			{
				$query = $_db->getQuery(true);

				$query->select($_db->quoteName('id'));
				$query->from($_db->quoteName('#__menu'));
				$query->where($_db->quoteName('link') . ' = ' . $_db->quote('index.php?option=com_bwpostman&view=register'));
				$query->where($_db->quoteName('published') . ' = ' . (int) 1);

				$_db->setQuery($query);

				$itemid = $_db->loadResult();
			}
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $itemid;
	}

	/**
	 * This is the main function to render the content from an ID to HTML
	 *
	 * @param array		$nl_content
	 * @param int		$template_id
	 * @param string	$text_template_id
	 *
	 * @return string	content
	 *
	 * @since       0.9.1
	 */
	public function getContent($nl_content, $template_id, $text_template_id)
	{
		$param = JComponentHelper::getParams('com_bwpostman');

		$model		= new BwPostmanModelNewsletter();
		$tpl		= $model->getTemplate($template_id);
		$text_tpl	= $model->getTemplate($text_template_id);

		// only for old templates
		if ($template_id < 1)
		{
			$content['html_version'] = '<div class="outer"><div class="header"><img class="logo" src="'.JRoute::_(JUri::root().$param->get('logo')).'" alt="" /></div><div class="content-outer"><div class="content"><div class="content-inner"><p class="nl-intro">&nbsp;</p>';
		}
		else
		{
			$content['html_version'] = '';
		}

		$content['text_version'] = '';

		if ($nl_content == null)
		{
			$content['html_version'] .= '';
			$content['text_version'] .= '';

		}
		else
		{
			foreach($nl_content as $content_id)
			{
				if ($tpl->tpl_id && $template_id > 0)
				{
					$content['html_version'] .= $this->replaceContentHtmlNew($content_id, $tpl);
					if (($tpl->article['divider'] == 1) && ($content_id != end($nl_content)))
						$content['html_version'] = $content['html_version'] . $tpl->tpl_divider;
				}
				else
				{
					$content['html_version'] .= $this->replaceContentHtml($content_id, $tpl);
				}

				if ($text_tpl->tpl_id && $text_template_id > 0)
				{
					$content['text_version'] .= $this->replaceContentTextNew($content_id, $text_tpl);
					if (($text_tpl->article['divider'] == 1) && ($content_id != end($nl_content)))
						$content['text_version'] = $content['text_version'] . $text_tpl->tpl_divider . "\n\n";
				}
				else
				{
					$content['text_version'] .= $this->replaceContentText($content_id, $text_tpl);
				}
			}
		}

			// only for old templates
		if ($template_id < 1)
		{
			$content['html_version'] .= '</div></div></div></div>';
		}

		return $content;

	}

	/**
	 * Method to retrieve content
	 *
	 * @param int   $id
	 *
	 * @return mixed
	 *
	 * @since       0.9.1
	 */
	public function retrieveContent($id)
	{
		$row    = new stdClass();
		$app	= JFactory::getApplication();
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('a') . '.*');
		$query->select('ROUND(v.rating_sum/v.rating_count) AS ' . $_db->quoteName('rating'));
		$query->select($_db->quoteName('v') . '.' . $_db->quoteName('rating_count'));
		$query->select($_db->quoteName('u') . '.' . $_db->quoteName('name') . ' AS ' . $_db->quoteName('author'));
		$query->select($_db->quoteName('cc') . '.' . $_db->quoteName('title') . ' AS ' . $_db->quoteName('category'));
		$query->select($_db->quoteName('s') . '.' . $_db->quoteName('title') . ' AS ' . $_db->quoteName('section'));
		$query->select($_db->quoteName('g') . '.' . $_db->quoteName('title') . ' AS ' . $_db->quoteName('groups'));
		$query->select($_db->quoteName('s') . '.' . $_db->quoteName('published') . ' AS ' . $_db->quoteName('sec_pub'));
		$query->select($_db->quoteName('cc') . '.' . $_db->quoteName('published') . ' AS ' . $_db->quoteName('cat_pub'));
		$query->from($_db->quoteName('#__content') . ' AS ' . $_db->quoteName('a'));
		$query->join('LEFT', $_db->quoteName('#__categories') . ' AS ' . $_db->quoteName('cc') . ' ON ' . $_db->quoteName('cc') . '.' . $_db->quoteName('id') . ' = ' . $_db->quoteName('a') . '.' . $_db->quoteName('catid'));
		$query->join('LEFT', $_db->quoteName('#__categories') . ' AS ' . $_db->quoteName('s') . ' ON ' . $_db->quoteName('s') . '.' . $_db->quoteName('id') . ' = ' . $_db->quoteName('cc') . '.' . $_db->quoteName('parent_id') . 'AND' . $_db->quoteName('s') . '.' . $_db->quoteName('extension') . ' = ' . $_db->quote('com_content'));
		$query->join('LEFT', $_db->quoteName('#__users') . ' AS ' . $_db->quoteName('u') . ' ON ' . $_db->quoteName('u') . '.' . $_db->quoteName('id') . ' = ' . $_db->quoteName('a') . '.' . $_db->quoteName('created_by'));
		$query->join('LEFT', $_db->quoteName('#__content_rating') . ' AS ' . $_db->quoteName('v') . ' ON ' . $_db->quoteName('a') . '.' . $_db->quoteName('id') . ' = ' . $_db->quoteName('v') . '.' . $_db->quoteName('content_id'));
		$query->join('LEFT', $_db->quoteName('#__usergroups') . ' AS ' . $_db->quoteName('g') . ' ON ' . $_db->quoteName('a') . '.' . $_db->quoteName('access') . ' = ' . $_db->quoteName('g') . '.' . $_db->quoteName('id'));
		$query->where($_db->quoteName('a') . '.' . $_db->quoteName('id') . ' = ' . (int) $id);

		$_db->setQuery($query);
		try
		{
			$row = $_db->loadObject();
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		if($row) {
			$params = new JRegistry();
			$params->loadString($row->attribs, 'JSON');

			$params->def('link_titles',	$app->get('link_titles'));
			$params->def('author', 		$params->get('newsletter_show_author'));
			$params->def('createdate', 	$params->get('newsletter_show_createdate'));
			$params->def('modifydate', 	!$app->get('hideModifyDate'));
			$params->def('print', 		!$app->get('hidePrint'));
			$params->def('pdf', 		!$app->get('hidePdf'));
			$params->def('email', 		!$app->get('hideEmail'));
			$params->def('rating', 		$app->get('vote'));
			$params->def('icons', 		$app->get('icons'));
			$params->def('readmore', 	$app->get('readmore'));
			$params->def('item_title', 	1);

			$params->set('intro_only', 	1);
			$params->set('item_navigation', 0);

			$params->def('back_button', 	0);
			$params->def('image', 			1);

			$row->params = $params;
			$row->text = $row->introtext;
		}
		return $row;
	}

	/**
	 * Method to replace HTML content
	 *
	 * @param int       $id
	 * @param object    $tpl
	 *
	 * @return string
	 *
	 * @since       0.9.1
	 */
	public function replaceContentHtml($id, $tpl)
	{
		$content	= '';

		if($id != 0)
		{
			// Editor user type check
			$access          = new stdClass();
			$access->canEdit = $access->canEditOwn = $access->canPublish = 0;

			$row = $this->retrieveContent($id);

			if ($row)
			{
				$params		= $row->params;
				$model		= new BwPostmanModelNewsletter;
				$lang		= $model->getArticleLanguage($row->id);
				$_Itemid	= ContentHelperRoute::getArticleRoute($row->id, 0, $lang);
				$link		= JRoute::_(JUri::base());
				if ($_Itemid)
					$link .= $_Itemid;

//				$app->triggerEvent('onPrepareContent', array(&$row, &$params, 0), true);

				$intro_text = $row->text;

				$html_content = new HTML_content();

				ob_start();
				// Displays Item Title
				$html_content->Title($row, $params, $access);

				$content .= ob_get_contents();
				ob_end_clean();
				// Displays Category
				ob_start();

				// Displays Created Date
				if ($tpl->article['show_createdate'] != 0)
					$html_content->CreateDate($row, $params);

				// Displays Author Name
				if ($tpl->article['show_author'] != 0)
					$html_content->Author($row, $params);

				// Displays Urls
				$content .= ob_get_contents();
				ob_end_clean();

				$content .= '<div class="intro_text">'
				. $intro_text //(function_exists('ampReplace') ? ampReplace($intro_text) : $intro_text). '</td>'
				. '</div>';

				if ($tpl->article['show_readon'] != 0)
				{
					$content	.= '<div class="read_on">'
								. '		<p>'
								. '		<a href="'. str_replace('administrator/', '', $link) . '" class="readon">'
								. JText::_('READ_MORE')
								. '		</a><br/><br/>'
								. '		</p>'
								. '	</div>';
				}

				return stripslashes($content);
			}
		}
		return JText::sprintf('COM_BWPOSTMAN_NL_ERROR_RETRIEVING_CONTENT', $id);
	}

	/**
	 * Method to replace HTML content (new)
	 *
	 * @param $id
	 * @param $tpl
	 *
	 * @return string
	 *
	 * @since       1.1.0
	 */
	public function replaceContentHtmlNew($id, $tpl)
	{
		$content	    = '';
		$create_date    = '';

		if($id != 0){
			// Editor user type check
			$access          = new stdClass();
			$access->canEdit = $access->canEditOwn = $access->canPublish = 0;

			// $id = "-1" if no content is selected
			if ($id == '-1')
			{
				$content	.= $tpl->tpl_article;
				$content	= preg_replace( "/<table id=\"readon\".*?<\/table>/is", "", $content);
				$content	= str_replace('[%content_title%]', JText::_('COM_BWPOSTMAN_TPL_PLACEHOLDER_TITLE'), $content);
				$content	= str_replace('[%content_text%]', JText::_('COM_BWPOSTMAN_TPL_PLACEHOLDER_CONTENT'), $content);
				return stripslashes($content);
			}

			$row = $this->retrieveContent($id);

			if ($row)
			{
				$model		= new BwPostmanModelNewsletter;
				$lang		= $model->getArticleLanguage($row->id);
				$_Itemid	= ContentHelperRoute::getArticleRoute($row->id, 0, $lang);
				$link		= JRoute::_(JUri::base());
				if ($_Itemid)
					$link .= $_Itemid;

//				$app->triggerEvent('onPrepareContent', array(&$row, &$params, 0), true);

				$intro_text = $row->text;

				if (intval($row->created) != 0)
				{
					$create_date = JHtml::_('date', $row->created);
				}

				$link = str_replace('administrator/', '', $link);

				$content		.= $tpl->tpl_article;
				$content		= str_replace('[%content_title%]', $row->title, $content);
				$content_text	= '';
				if (($tpl->article['show_createdate'] == 1) || ($tpl->article['show_author'] == 1))
				{
					$content_text .= '<p>';
					if ($tpl->article['show_createdate'] == 1)
					{
						$content_text .= '<span><small>';
						$content_text .= JText::sprintf('COM_CONTENT_CREATED_DATE_ON', $create_date);
						$content_text .= '&nbsp;&nbsp;&nbsp;&nbsp;</small></span>';
					}
					if ($tpl->article['show_author'] == 1)
					{
						$content_text .= '<span><small>';
						$content_text .= JText::sprintf('COM_CONTENT_WRITTEN_BY', ($row->created_by_alias ? $row->created_by_alias : $row->created_by));
						$content_text .= '</small></span>';
					}
					$content_text .= '</p>';
				}
				$content_text	.= $intro_text;
				$content  		= str_replace('[%content_text%]', $content_text, $content);
				$content  		= str_replace('[%readon_href%]', $link, $content);
				$content  		= str_replace('[%readon_text%]', JText::_('READ_MORE'), $content);

				return stripslashes($content);
			}
		}
		return JText::sprintf('COM_BWPOSTMAN_NL_ERROR_RETRIEVING_CONTENT', $id);
	}

	/**
	 * Method to replace text content
	 *
	 * @param int       $id
	 * @param object    $text_tpl
	 *
	 * @return string
	 *
	 * @since       1.1.0
	 */
	public function replaceContentTextNew($id, $text_tpl)
	{
		$create_date    = '';

		if($id != 0)
		{
			$row = $this->retrieveContent($id);

			if ($row)
			{
				$model		= new BwPostmanModelNewsletter;
				$lang		= $model->getArticleLanguage($row->id);
				$_Itemid	= ContentHelperRoute::getArticleRoute($row->id, 0, $lang);
				$link		= JRoute::_(JUri::base());
				if ($_Itemid)
					$link .= $_Itemid;

//				$app->triggerEvent('onPrepareContent', array(&$row, &$params, 0), true);

				$intro_text = $row->text;
				$intro_text = strip_tags($intro_text);

				$intro_text = $this->unHTMLSpecialCharsAll($intro_text);

				if (intval($row->created) != 0)
				{
					$create_date = JHtml::_('date', $row->created);
				}

				$link = str_replace('administrator/', '', $link);

				$content		= $text_tpl->tpl_article;
				$content		= str_replace('[%content_title%]', $row->title , $content);
				$content_text	= "\n";
				if (($text_tpl->article['show_createdate'] == 1) || ($text_tpl->article['show_author'] == 1))
				{
					if ($text_tpl->article['show_createdate'] == 1)
					{
						$content_text .= JText::sprintf('COM_CONTENT_CREATED_DATE_ON', $create_date);
						$content_text .= '    ';
					}
					if ($text_tpl->article['show_author'] == 1)
					{
						$content_text .= JText::sprintf('COM_CONTENT_WRITTEN_BY', ($row->created_by_alias ? $row->created_by_alias : $row->created_by));
					}
					$content_text .= "\n\n";
				}
				$content_text	.= $intro_text;
				$content		= str_replace('[%content_text%]', $content_text."\n", $content);
				$content		= str_replace('[%readon_href%]', $link."\n", $content);
				$content		= str_replace('[%readon_text%]', JText::_('READ_MORE'), $content);

				return stripslashes($content);
			}
		}
		return '';
	}

	/**
	 * Method to replace text content
	 *
	 * @param int       $id
	 * @param object    $text_tpl
	 *
	 * @return string
	 *
	 * @since       0.9.1
	 */
	public function replaceContentText($id, $text_tpl)
	{
		$create_date    = '';

		if($id != 0)
		{
			$row = $this->retrieveContent($id);

			if ($row)
			{
				$model		= new BwPostmanModelNewsletter;
				$lang		= $model->getArticleLanguage($row->id);
				$_Itemid	= ContentHelperRoute::getArticleRoute($row->id, 0, $lang);
				$link		= JRoute::_(JUri::base());
				if ($_Itemid)
					$link .= $_Itemid;

//				$app->triggerEvent('onPrepareContent', array(&$row, &$params, 0), true);

				$intro_text = $row->text;
				$intro_text = strip_tags($intro_text);

				$intro_text = $this->unHTMLSpecialCharsAll($intro_text);

				if (intval($row->created) != 0)
				{
					$create_date = JHtml::_('date', $row->created);
				}

				$content = "\n" . $row->title;

				$content_text = "";
				if (($text_tpl->article['show_createdate'] == 1) || ($text_tpl->article['show_author'] == 1))
				{
					if ($text_tpl->article['show_createdate'] == 1)
					{
						$content_text .= JText::sprintf('COM_CONTENT_CREATED_DATE_ON', $create_date);
						$content_text .= '    ';
					}
					if ($text_tpl->article['show_author'] == 1)
					{
						$content_text .= JText::sprintf('COM_CONTENT_WRITTEN_BY', ($row->created_by_alias ? $row->created_by_alias : $row->created_by));
					}
					$content_text .= "\n\n";
				}
				$intro_text = $content_text . $intro_text;

				$content .= "\n\n" . $intro_text . "\n\n";
				if ($text_tpl->article['show_readon'] == 1) $content .= JText::_('READ_MORE') . ": \n". str_replace('administrator/', '', $link) . "\n\n";

				return stripslashes($content);
			}
		}
		return '';
	}

	/**
	 * Method to process special characters
	 *
	 * @param $text
	 *
	 * @return mixed
	 *
	 * @since       0.9.1
	 */
	private function unHTMLSpecialCharsAll($text) {

		$text = $this->deHTMLEntities($text);

		return $text;
	}

	/**
	 * convert html special entities to literal characters
	 *
	 * @param string    $text
	 *
	 * @return  string  $text
	 *
	 * @since       0.9.1
	 */
	private function deHTMLEntities($text) {
		$search = array(
		"'&(quot|#34);'i",
		"'&(amp|#38);'i",
		"'&(lt|#60);'i",
		"'&(gt|#62);'i",
		"'&(nbsp|#160);'i",   "'&(iexcl|#161);'i",  "'&(cent|#162);'i",   "'&(pound|#163);'i",  "'&(curren|#164);'i",
		"'&(yen|#165);'i",    "'&(brvbar|#166);'i", "'&(sect|#167);'i",   "'&(uml|#168);'i",    "'&(copy|#169);'i",
		"'&(ordf|#170);'i",   "'&(laquo|#171);'i",  "'&(not|#172);'i",    "'&(shy|#173);'i",    "'&(reg|#174);'i",
		"'&(macr|#175);'i",   "'&(neg|#176);'i",    "'&(plusmn|#177);'i", "'&(sup2|#178);'i",   "'&(sup3|#179);'i",
		"'&(acute|#180);'i",  "'&(micro|#181);'i",  "'&(para|#182);'i",   "'&(middot|#183);'i", "'&(cedil|#184);'i",
		"'&(supl|#185);'i",   "'&(ordm|#186);'i",   "'&(raquo|#187);'i",  "'&(frac14|#188);'i", "'&(frac12|#189);'i",
		"'&(frac34|#190);'i", "'&(iquest|#191);'i", "'&(Agrave|#192);'",  "'&(Aacute|#193);'",  "'&(Acirc|#194);'",
		"'&(Atilde|#195);'",  "'&(Auml|#196);'",    "'&(Aring|#197);'",   "'&(AElig|#198);'",   "'&(Ccedil|#199);'",
		"'&(Egrave|#200);'",  "'&(Eacute|#201);'",  "'&(Ecirc|#202);'",   "'&(Euml|#203);'",    "'&(Igrave|#204);'",
		"'&(Iacute|#205);'",  "'&(Icirc|#206);'",   "'&(Iuml|#207);'",    "'&(ETH|#208);'",     "'&(Ntilde|#209);'",
		"'&(Ograve|#210);'",  "'&(Oacute|#211);'",  "'&(Ocirc|#212);'",   "'&(Otilde|#213);'",  "'&(Ouml|#214);'",
		"'&(times|#215);'i",  "'&(Oslash|#216);'",  "'&(Ugrave|#217);'",  "'&(Uacute|#218);'",  "'&(Ucirc|#219);'",
		"'&(Uuml|#220);'",    "'&(Yacute|#221);'",  "'&(THORN|#222);'",   "'&(szlig|#223);'",   "'&(agrave|#224);'",
		"'&(aacute|#225);'",  "'&(acirc|#226);'",   "'&(atilde|#227);'",  "'&(auml|#228);'",    "'&(aring|#229);'",
		"'&(aelig|#230);'",   "'&(ccedil|#231);'",  "'&(egrave|#232);'",  "'&(eacute|#233);'",  "'&(ecirc|#234);'",
		"'&(euml|#235);'",    "'&(igrave|#236);'",  "'&(iacute|#237);'",  "'&(icirc|#238);'",   "'&(iuml|#239);'",
		"'&(eth|#240);'",     "'&(ntilde|#241);'",  "'&(ograve|#242);'",  "'&(oacute|#243);'",  "'&(ocirc|#244);'",
		"'&(otilde|#245);'",  "'&(ouml|#246);'",    "'&(divide|#247);'i", "'&(oslash|#248);'",  "'&(ugrave|#249);'",
		"'&(uacute|#250);'",  "'&(ucirc|#251);'",   "'&(uuml|#252);'",    "'&(yacute|#253);'",  "'&(thorn|#254);'",
		"'&(yuml|#255);'");
		$replace = array(
		"\"",
		"&",
		"<",
		">",
		" ",      chr(161), chr(162), chr(163), chr(164), chr(165), chr(166), chr(167), chr(168), chr(169),
		chr(170), chr(171), chr(172), chr(173), chr(174), chr(175), chr(176), chr(177), chr(178), chr(179),
		chr(180), chr(181), chr(182), chr(183), chr(184), chr(185), chr(186), chr(187), chr(188), chr(189),
		chr(190), chr(191), chr(192), chr(193), chr(194), chr(195), chr(196), chr(197), chr(198), chr(199),
		chr(200), chr(201), chr(202), chr(203), chr(204), chr(205), chr(206), chr(207), chr(208), chr(209),
		chr(210), chr(211), chr(212), chr(213), chr(214), chr(215), chr(216), chr(217), chr(218), chr(219),
		chr(220), chr(221), chr(222), chr(223), chr(224), chr(225), chr(226), chr(227), chr(228), chr(229),
		chr(230), chr(231), chr(232), chr(233), chr(234), chr(235), chr(236), chr(237), chr(238), chr(239),
		chr(240), chr(241), chr(242), chr(243), chr(244), chr(245), chr(246), chr(247), chr(248), chr(249),
		chr(250), chr(251), chr(252), chr(253), chr(254), chr(255));
		return $text = preg_replace($search, $replace, $text);
	}

}

/**
 * Utility class for writing the HTML for content
 * --> Referring to Communicator 2.0.0rc1
 *
 * @package 		BwPostman-Admin
 * @subpackage 	Newsletters
 *
 * @since       0.9.1
 */
class HTML_content
{
	/**
	 * Writes Title
	 *
	 * @param   object  $row
	 * @param   object  $params
	 *
	 * @return  void
	 *
	 * @since       0.9.1
	 */
	public function Title(&$row, &$params)
	{
		if ($params->get('item_title'))
		{
			if ($params->get('link_titles') && $row->link_on != '')
			{
				?>
				<h2><a href="<?php echo $row->link_on;?>"
					class="contentpagetitle<?php echo $params->get('pageclass_sfx'); ?>">
								<?php echo $row->title;?></a></h2>
								<?php
							} else {
								?>
				<h2><?php echo $row->title;?></h2>
				<?php
			}
		}
	}

	/**
	 * Writes Category
	 *
	 * @param   object  $row
	 *
	 * @return  void
	 *
	 * @since       0.9.1
	 */
	public function Category(&$row)
	{
		?>
		<span class="sc_category"><small> <?php
		echo $row->category;
		?></small></span>
		<?php
	}

	/**
	 * Writes Author name
	 *
	 * @param   object  $row
	 *
	 * @return  void
	 *
	 * @since       0.9.1
	 */
	public function Author(&$row)
	{
		?>
		<span class="created_by"><small><?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY',($row->created_by_alias ? $row->created_by_alias : $row->created_by)); ?></small></span>
		<?php
	}


	/**
	 * Writes Create Date
	 *
	 * @param   object  $row
	 *
	 * @return  void
	 *
	 * @since       0.9.1
	 */
	public function CreateDate(&$row)
	{
		$create_date = null;

		if (intval($row->created) != 0)
		{
			$create_date = JHtml::_('date', $row->created);
		}

		?>
		<span class="createdate"><small><?php echo JText::sprintf('COM_CONTENT_CREATED_DATE_ON', $create_date); ?>&nbsp;&nbsp;&nbsp;&nbsp;</small></span>
		<?php
	}

	/**
	 * Writes URL's
	 *
	 * @param   object  $row
	 * @param   object  $params
	 *
	 * @return  void
	 *
	 * @since       0.9.1
	 */
	public function URL(&$row, &$params)
	{
		if ($params->get('url') && $row->urls)
		{
			?>
			<p class="row_url"><a
				href="http://<?php echo $row->urls ; ?>" target="_blank"> <?php echo $row->urls; ?></a>
			</p>
			<?php
		}
	}

	/**
	 * Writes Modified Date
	 *
	 * @param   object  $row
	 * @param   object  $params
	 *
	 * @return  void
	 *
	 * @since       0.9.1
	 */
	public function ModifiedDate(&$row, &$params)
	{
		$mod_date = null;

		if (intval($row->modified) != 0)
		{
			$mod_date = JHtml::_('date', $row->modified);
		}

		if (($mod_date != '') && $params->get('modifydate'))
		{
			?>
			<p class="modifydate"><?php echo JText::_('LAST_UPDATED'); ?>
			(<?php echo $mod_date; ?>)</p>
			<?php
		}
	}

	/**
	 * Writes read more button
	 *
	 * @param   object  $row
	 * @param   object  $params
	 *
	 * @return  void
	 *
	 * @since       0.9.1
	 */
	public function ReadMore (&$row, &$params)
	{
		if ($params->get('readmore'))
		{
			if ($params->get('intro_only') && $row->link_text)
			{
				?>
				<p class="link_on"><a href="<?php echo $row->link_on;?>"
					class="readon<?php echo $params->get('pageclass_sfx'); ?>"> <?php echo $row->link_text;?></a>
				</p>
				<?php
			}
		}
	}
}
