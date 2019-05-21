<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single newsletter model for backend.
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

require_once(JPATH_SITE . '/components/com_content/helpers/route.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/logging/BwLogger.php');

// Import MODEL and Helper object class
jimport('joomla.application.component.modeladmin');

use Joomla\Utilities\ArrayHelper as ArrayHelper;
use Joomla\Registry\Registry as JRegistry;

// Require helper class
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/contentRenderer.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/tplhelper.php');

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
	 * @var integer
	 *
	 * @since       0.9.1
	 */
	private $id = null;

	/**
	 * Newsletter data
	 *
	 * @var array
	 *
	 * @since       0.9.1
	 */
	private $data = null;

	/**
	 * Demo mode
	 *
	 * @var integer
	 *
	 * @since
	 */
	private $demo_mode         = 0;

	/**
	 * Dummy sender
	 *
	 * @var string
	 *
	 * @since
	 */
	private $dummy_sender      = '';

	/**
	 * Dummy recipient
	 *
	 * @var string
	 *
	 * @since
	 */
	private $dummy_recipient   = '';

	/**
	 * Arise queue
	 *
	 * @var integer
	 *
	 * @since
	 */
	private $arise_queue       = 0;

	/**
	 * property to hold permissions as array
	 *
	 * @var array $permissions
	 *
	 * @since       2.0.0
	 */
	public $permissions;

	/**
	 * property to hold logger
	 *
	 * @var object $logger
	 *
	 * @since       2.3.0
	 */
	public $logger;

	/**
	 * Constructor
	 * Determines the newsletter ID
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function __construct()
	{
		$this->permissions		= JFactory::getApplication()->getUserState('com_bwpm.permissions');

		parent::__construct();

		$jinput	= JFactory::getApplication()->input;
		$array	= $jinput->get('cid',  0, '');
		$this->setId((int) $array[0]);

		$this->processTestMode();

		$log_options    = array();
		$this->logger   = new BwLogger($log_options);
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
		$this->id   = $id;
		$this->data = null;
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to change the state of the record.
	 *
	 * @throws \Exception
	 *
	 * @since	1.0.1
	 */
	protected function canEditState($record)
	{
		$permission = BwPostmanHelper::canEditState('newsletter', (int) $record->id);

		return $permission;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @throws Exception
	 *
	 * @since   1.0.1
	 */
	public function getItem($pk = null)
	{
		$app	= JFactory::getApplication();
		$item   = new stdClass();
		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('bwpostman');

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
				$dispatcher->trigger('onBwPostmanAfterNewsletterModelGetProperties', array(&$properties));
				$item       = ArrayHelper::toObject($properties, 'JObject');

				if (property_exists($item, 'params'))
				{
					$registry = new JRegistry;
					$registry->loadJSON($item->params);
					$item->params = $registry->toArray();
				}

				// Get associated mailinglists
				$item->mailinglists = $this->getAssociatedMailinglistsByNewsletter($item->id);

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
				$item->ml_available = $this->getMailinglistsByRestriction($item->mailinglists, 'available');

				// get unavailable mailinglists to predefine for state
				$item->ml_unavailable = $this->getMailinglistsByRestriction($item->mailinglists, 'unavailable');

				// get internal mailinglists to predefine for state
				$item->ml_intern = $this->getMailinglistsByRestriction($item->mailinglists, 'internal');

				// Preset template ids
				// Old template for existing newsletters not set during update to 1.1.x, so we have to manage this here also

				// preset HTML-Template for old newsletters
				$this->presetOldHTMLTemplate($item);

				// preset Text-Template for old newsletters
				$this->presetOldTextTemplate($item);

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

		$app->setUserState('com_bwpostman.edit.newsletter.data', $item);

		// if plugin "substitutelinks" is active and substitute_links == '1' -> setUserState
		if (isset($item->substitute_links) && $item->substitute_links == '1')
		{
			$app->setUserState('com_bwpostman.edit.newsletter.data.substitutelinks', '1');
		}

		//  convert attachment string to subform array
		if ($item->attachment != '' && is_string($item->attachment))
		{
			$baseArray = explode(';', $item->attachment);
			$attachmentArray = array();

			for ($i = 0; $i < count($baseArray); $i++)
			{
				$key = 'attachment' . $i;
				$attachmentArray[$key]['single_attachment'] = $baseArray[$i];
			}

			$item->attachment = $attachmentArray;
		}

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
	 * @throws Exception
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
		$id		= $jinput->get('id', 0);

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
		if ($id != 0 && (!$user->authorise('bwpm.newsletter.edit.state', 'com_bwpostman.newsletter.' . (int) $id))
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

		$form->setValue('title', '', $form->getValue('subject'));

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	protected function loadFormData()
	{
		$recordId = JFactory::getApplication()->getUserState('com_bwpostman.newsletter.id');

		// Check the session for previously entered form data for this record id.
		$data	= JFactory::getApplication()->getUserState('com_bwpostman.edit.newsletter.data', null);

		if (empty($data) || (is_object($data) && $recordId != $data->id))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method check if newsletter is content template
	 *
	 * @param   array  $id        ID of newsletter
	 *
	 * @return	boolean           state of is_template
	 *
	 * @throws \Exception
	 *
	 * @since	2.2.0
	 */
	public function isTemplate($id)
	{
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('is_template'));
		$query->from($_db->quoteName('#__bwpostman_newsletters'));
		$query->where($_db->quoteName('id') . ' = ' . $_db->quote($id));

		$_db->setQuery($query);
		try
		{
			$isTemplate = $_db->loadResult();
			if ($isTemplate === '1')
			{
				return true;
			}
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return false;
	}

	/**
	 * Method to get the standard template.
	 *
	 * @param   string  $mode       HTML or text
	 *
	 * @return	string	            ID of standard template
	 *
	 * @throws Exception
	 *
	 * @since	1.2.0
	 */
	private function getStandardTpl($mode	= 'html')
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
					$query->select($_db->quoteName('id') . ' AS ' . $_db->quoteName('value'));
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
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
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
	 * @throws Exception
	 *
	 * @since
	 */
	public function getSingleNewsletter()
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
		if (property_exists($item, 'template_id') && $item->template_id == '0')
		{
			$item->template_id		= -1;
		}

		if (property_exists($item, 'text_template_id') && $item->text_template_id == '0')
		{
			$item->text_template_id	= -2;
		}

		$renderer	= new contentRenderer();

		if ($item->id == 0 && !empty($item->selected_content) && empty($item->html_version) && empty($item->text_version))
		{
			if (!is_array($item->selected_content))
			{
				$item->selected_content = explode(',', $item->selected_content);
			}

			$content	= $renderer->getContent((array) $item->selected_content, $item->template_id, $item->text_template_id);
			$item->html_version	= $content['html_version'];
			$item->text_version	= $content['text_version'];
		}

		// force two linebreak at the end of text
		$item->text_version = rtrim($item->text_version) . "\n\n";

		// Replace the links to provide the correct preview
		$item->html_formatted	= $item->html_version;
		$item->text_formatted	= $item->text_version;

		// add template data
		$renderer->addTplTags($item->html_formatted, $item->template_id);
		$renderer->addTextTpl($item->text_formatted, $item->text_template_id);

		// Replace the intro to provide the correct preview
		if (!empty($item->intro_headline))
		{
			$item->html_formatted	= str_replace('[%intro_headline%]', $item->intro_headline, $item->html_formatted);
		}

		if (!empty($item->intro_text))
		{
			$item->html_formatted	= str_replace('[%intro_text%]', nl2br($item->intro_text, true), $item->html_formatted);
		}

		if (!empty($item->intro_text_headline))
		{
			$item->text_formatted	= str_replace('[%intro_headline%]', $item->intro_text_headline, $item->text_formatted);
		}

		if (!empty($item->intro_text_text))
		{
			$item->text_formatted	= str_replace('[%intro_text%]', $item->intro_text_text, $item->text_formatted);
		}

		// only for old html templates
		if ($item->template_id < 1)
		{
			$item->html_formatted = $item->html_formatted . '[dummy]';
		}

		$renderer->replaceTplLinks($item->html_formatted);
		$renderer->addHtmlTags($item->html_formatted, $item->template_id);
		$renderer->addHTMLFooter($item->html_formatted, $item->template_id);

		// only for old text templates
		if ($item->text_template_id < 1)
		{
			$item->text_formatted = $item->text_formatted . '[dummy]';
		}

		$renderer->replaceTextTplLinks($item->text_formatted);
		$renderer->addTextFooter($item->text_formatted, $item->text_template_id);

		// Replace the links to provide the correct preview
		BwPostmanHelper::replaceLinks($item->html_formatted);
		BwPostmanHelper::replaceLinks($item->text_formatted);

		return $item;
	}

	/**
	 * Method to get the selected content items which are used to compose a newsletter
	 *
	 * @access	public
	 *
	 * @return	array
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function getSelectedContentItems()
	{
		$_db	= $this->_db;

		$selected_content = $this->getSelectedContentFromNewsletterTable();
		$selected_content_void = array ();

		if ($selected_content)
		{
			if (!is_array($selected_content))
			{
				$selected_content = explode(',', $selected_content);
			}

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
					$items = $_db->loadObjectList();
				}
				catch (RuntimeException $e)
				{
					JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				}

				if(count($items) > 0)
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
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public static function getItemid($view)
	{
		$itemid = '';
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__menu'));
		$query->where($_db->quoteName('link') . ' = ' . $_db->quote('index.php?option=com_bwpostman&view=' . $view));
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
	 * Method to store the newsletter data from the newsletters_tmp-table into the newsletters-table
	 *
	 * @access	public
	 *
	 * @param   array   $data       data to save
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
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
		{
			foreach ($data['ml_available'] as $key => $value)
			{
				$data['mailinglists'][] 	= $value;
			}
		}

		if (isset($data['ml_unavailable']))
		{
			foreach ($data['ml_unavailable'] as $key => $value)
			{
				$data['mailinglists'][] 	= $value;
			}
		}

		if (isset($data['ml_intern']))
		{
			foreach ($data['ml_intern'] as $key => $value)
			{
				$data['mailinglists'][] 	= $value;
			}
		}

		// merge usergroups into mailinglists, single array may not exist, therefore array_merge would not give a result
		if (isset($data['usergroups']) && !empty($data['usergroups']))
		{
			foreach ($data['usergroups'] as $key => $value)
			{
				$data['mailinglists'][] = '-' . $value;
			}
		}

		// convert attachment array to string, to be able to save
		if (isset($data['attachment']) && $data['attachment'] != '' && is_array($data['attachment']))
		{
			$fullAttachments = array();

			foreach ($data['attachment'] as $k => $v)
			{
				if ($data['attachment'][$k]['single_attachment'] != '')
				{
					$fullAttachments[] = $data['attachment'][$k]['single_attachment'];
				}
			}
			$data['attachment'] = implode(';', $fullAttachments);
		}

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
					$query->columns(
						array(
							$_db->quoteName('newsletter_id'),
							$_db->quoteName('mailinglist_id')
						)
					);
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
	 * @throws Exception
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
			foreach ($cid as $id)
			{
				if (!BwPostmanHelper::canArchive('newsletter', 0, (int) $id))
				{
					return false;
				}
			}
		}
		else
		{
			// Access check.
			foreach ($cid as $id)
			{
				if (!BwPostmanHelper::canRestore('newsletter', (int) $id))
				{
					return false;
				}
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
			$query->where($_db->quoteName('id') . ' IN (' . implode(',', $cid) . ')');

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
	 * @throws Exception
	 *
	 * @since
	 */
	public function copy($cid = array())
	{
		if (!$this->permissions['newsletter']['create'])
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
				$newsletters_data_copy->substitute_links	= null;
				$newsletters_data_copy->is_template			= null;

				$newsletters_data_copy->mailinglists = $this->getAssociatedMailinglistsByNewsletter((int) $id);

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
	 * @throws \Exception
	 *
	 * @since       0.9.1
	 */
	public function delete(&$pks)
	{
		if (count($pks))
		{
			ArrayHelper::toInteger($pks);

			// Access check.
			foreach ($pks as $id)
			{
				if (!BwPostmanHelper::canDelete('newsletter', (int) $id))
				{
					return false;
				}
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
	 * @throws Exception
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
	 * Changes the state of isTemplate
	 *
	 * @param   array    $id      A list of the primary keys to change.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws \Exception
	 *
	 * @since   1.6
	 */
	public function changeIsTemplate($id)
	{
		$user = \JFactory::getUser();
		$table = $this->getTable();

		// Access checks.
		if ($table->load($id))
		{
			if (!BwPostmanHelper::canEdit('newsletter', array('id' => $id)))
			{
				\JLog::add(\JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'), \JLog::WARNING, 'jerror');

				return false;
			}

			// If the table is checked out by another user, drop it and report to the user trying to change its state.
			if (property_exists($table, 'checked_out') && $table->checked_out && ($table->checked_out != $user->id))
			{
				\JLog::add(\JText::_('JLIB_APPLICATION_ERROR_CHECKIN_USER_MISMATCH'), \JLog::WARNING, 'jerror');

				return false;
			}
		}

		// Attempt to change the state of the record.
		$changeResult = $table->changeIsTemplate($id);
		if ($changeResult === false)
		{
			$this->setError($table->getError());

			return false;
		}

		// Clear the component's cache
		$this->cleanCache();

		return $changeResult;
	}

	/**
	 * Method to check and clean the input fields
	 *
	 * @access	public
	 *
	 * @param	array	$error          errors
	 * @param 	int		$recordId       Newsletter ID
	 * @param   boolean $automation     do we come from plugin?
	 *
	 * @return	mixed
	 *
	 * @throws Exception
	 *
	 * @since 2.3.0
	 */
	public function preSendChecks( &$error, $recordId = 0, $automation = false)
	{
		// Access check.
		if (!BwPostmanHelper::canSend($recordId))
		{
			$error[] = JText::_('COM_BWPOSTMAN_NL_ERROR_SEND_NOT_PERMITTED');

			return false;
		}

		// Check the newsletter form
		$data	= $this->checkForm($error, $recordId, $automation);

		// if checkForm fails redirect to edit
		if ($error)
		{
			return false;
		}

		//check for content template
		if ($data['is_template'] === "1")
		{
			$error[] = JText::_('COM_BWPOSTMAN_NL_IS_TEMPLATE_ERROR');

			return false;
		}

		JFactory::getApplication()->setUserState('com_bwpostman.newsletter.idToSend', $recordId);

		return $data;
	}

	/**
	 * Method to check and clean the input fields
	 *
	 * @access	public
	 *
	 * @param	array	$err            errors
	 * @param 	int		$recordId       Newsletter ID
	 * @param   boolean $automation     do we come from plugin?
	 *
	 * @return	mixed
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function checkForm( &$err, $recordId = 0, $automation = false)
	{
		jimport('joomla.mail.helper');

		if (!$automation)
		{
			// heal form data and get them
			$this->changeTab();
			$data	= ArrayHelper::fromObject(JFactory::getApplication()->getUserState('com_bwpostman.edit.newsletter.data'));

			$data['id']	= $recordId;
		}
		else
		{
			$data = ArrayHelper::fromObject($this->getItem($recordId));
		}

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
			$err[] = JText::_('COM_BWPOSTMAN_NL_ERROR_FROM_NAME');
		}

		// Check for valid from_email address
		if (trim($data['from_email']) == '')
		{
			$err[] = JText::_('COM_BWPOSTMAN_NL_ERROR_FROM_EMAIL');
		}
		else
		{
			// If there is a from_email address check if the address is valid
			if (!JMailHelper::isEmailAddress(trim($data['from_email'])))
			{
				$err[] = JText::_('COM_BWPOSTMAN_NL_ERROR_FROM_EMAIL_INVALID');
			}
		}

		// Check for valid reply_email address
		if (trim($data['reply_email']) == '')
		{
			$err[] = JText::_('COM_BWPOSTMAN_NL_ERROR_REPLY_EMAIL');
		}
		else
		{
			// If there is a from_email address check if the address is valid
			if (!JMailHelper::isEmailAddress(trim($data['reply_email'])))
			{
				$err[] = JText::_('COM_BWPOSTMAN_NL_ERROR_REPLY_EMAIL_INVALID');
			}
		}

		// Check for valid subject
		if (trim($data['subject']) == '')
		{
			$err[] = JText::_('COM_BWPOSTMAN_NL_ERROR_SUBJECT');
		}

		// Check for valid html or text version
		if ((trim($data['html_version']) == '') && (trim($data['text_version']) == ''))
		{
			$err[] = JText::_('COM_BWPOSTMAN_NL_ERROR_HTML_AND_TEXT');
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
	 * @throws Exception
	 *
	 * @since
	 */
	public function checkRecipients(&$ret_msg, $nl_id, $send_to_unconfirmed, $cam_id)
	{
		try
		{
			$check_subscribers    = 0;
			$check_allsubscribers = 0;
			$usergroups           = array();

			$associatedMailinglists = $this->getAssociatedMailinglists($nl_id, $cam_id);

			if (!$associatedMailinglists)
			{
				$ret_msg = JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_NL_NO_LISTS');
				return false;
			}

			$this->getSubscriberChecks($associatedMailinglists, $check_subscribers, $check_allsubscribers, $usergroups);

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

				$count_subscribers = $this->countSubscribersOfNewsletter($associatedMailinglists, $status, false);
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

				$count_subscribers = $this->countSubscribersOfNewsletter(array(), $status, true);
			}

			// Checks if the selected usergroups contain users
			$count_users          = 0;

			if (is_array($usergroups) && count($usergroups))
			{
				$count_users = $this->countUsersOfNewsletter($usergroups);
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
	 * @throws Exception
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
	 * @return 	array $content  associative array of content data
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function composeNl()
	{
		$jinput	= JFactory::getApplication()->input;

		$nl_content			= $jinput->get('selected_content');
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
	 * @throws Exception
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

		// support for plugin substitute links
		if(isset($form_data['substitute_links']) && $form_data['substitute_links'] == '1')
		{
			$app->setUserState('com_bwpostman.edit.newsletter.data.substitutelinks', '1');
		}

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
				$form_data['is_template']		= $state_data->is_template;
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

				if (is_object($state_data) && property_exists($state_data, 'substitute_links'))
				{
					$form_data['substitute_links']	= $state_data->substitute_links;
				}

				if (is_object($state_data) && property_exists($state_data, 'scheduled_date'))
				{
					$form_data['scheduled_date']	= $state_data->scheduled_date;
				}

				if (is_object($state_data) && property_exists($state_data, 'ready_to_send'))
				{
					$form_data['ready_to_send']	= $state_data->ready_to_send;
				}
				break;
			case 'edit_text':
				$form_data['attachment']		= $state_data->attachment;
				$form_data['html_version']		= $state_data->html_version;
				$form_data['campaign_id']		= $state_data->campaign_id;
				$form_data['usergroups']		= $state_data->usergroups;
				$form_data['is_template']		= $state_data->is_template;
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

				if (is_object($state_data) && property_exists($state_data, 'substitute_links'))
				{
					$form_data['substitute_links']	= $state_data->substitute_links;
				}

				if (is_object($state_data) && property_exists($state_data, 'scheduled_date'))
				{
					$form_data['scheduled_date']	= $state_data->scheduled_date;
				}

				if (is_object($state_data) && property_exists($state_data, 'ready_to_send'))
				{
					$form_data['ready_to_send']	= $state_data->ready_to_send;
				}
				break;
			case 'edit_preview':
				$form_data['attachment']		= $state_data->attachment;
				$form_data['html_version']		= $state_data->html_version;
				$form_data['text_version']		= $state_data->text_version;
				$form_data['campaign_id']		= $state_data->campaign_id;
				$form_data['usergroups']		= $state_data->usergroups;
				$form_data['is_template']		= $state_data->is_template;
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

				if (is_object($state_data) && property_exists($state_data, 'substitute_links'))
				{
					$form_data['substitute_links']	= $state_data->substitute_links;
				}

				if (is_object($state_data) && property_exists($state_data, 'scheduled_date'))
				{
					$form_data['scheduled_date']	= $state_data->scheduled_date;
				}

				if (is_object($state_data) && property_exists($state_data, 'ready_to_send'))
				{
					$form_data['ready_to_send']	= $state_data->ready_to_send;
				}
				break;
			case 'edit_send':
				$form_data['id']                    = $state_data->id;
				$form_data['subject']               = $state_data->subject;
				$form_data['description']           = $state_data->description;
				$form_data['asset_id']              = $state_data->asset_id;
				$form_data['from_name']             = $state_data->from_name;
				$form_data['from_email']            = $state_data->from_email;
				$form_data['reply_email']           = $state_data->reply_email;
				$form_data['intro_headline']        = $state_data->intro_headline;
				$form_data['intro_text_headline']   = $state_data->intro_text_headline;
				$form_data['intro_text']            = $state_data->intro_text;
				$form_data['intro_text_text']       = $state_data->intro_text_text;
				$form_data['hits']                  = $state_data->hits;
				$form_data['access']                = property_exists($state_data, 'access') ? $state_data->access : 1;
				$form_data['publish_up']            = $state_data->publish_up;
				$form_data['publish_down']          = $state_data->publish_down;
				$form_data['archived_by']           = $state_data->archived_by;
				$form_data['created_date']          = $state_data->created_date;
				$form_data['modified_time']         = $state_data->modified_time;
				$form_data['archive_date']          = $state_data->archive_date;
				$form_data['archive_flag']          = $state_data->archive_flag;
				$form_data['attachment']            = $state_data->attachment;
				$form_data['html_version']          = $state_data->html_version;
				$form_data['text_version']          = $state_data->text_version;
				$form_data['campaign_id']           = $state_data->campaign_id;
				$form_data['usergroups']            = $state_data->usergroups;
				$form_data['is_template']		    = $state_data->is_template;
				$form_data['template_id']           = $state_data->template_id;
				$form_data['text_template_id']      = $state_data->text_template_id;

				if (is_object($state_data) && property_exists($state_data, 'template_old_id'))
				{
					$form_data['template_old_id'] = $state_data->template_old_id;
				}

				if (is_object($state_data) && property_exists($state_data, 'text_template_old_id'))
				{
					$form_data['text_template_old_id'] = $state_data->text_template_old_id;
				}

				if (is_object($state_data) && property_exists($state_data, 'access'))
				{
					$form_data['access'] = $state_data->access;
				}

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

				if (is_object($state_data) && property_exists($state_data, 'substitute_links'))
				{
					$form_data['substitute_links']	= $state_data->substitute_links;
				}

				if (is_object($state_data) && property_exists($state_data, 'scheduled_date'))
				{
					$form_data['scheduled_date']	= $state_data->scheduled_date;
				}

				if (is_object($state_data) && property_exists($state_data, 'ready_to_send'))
				{
					$form_data['ready_to_send']	= $state_data->ready_to_send;
				}
				break;
			default:
				$form_data['html_version']		= $state_data->html_version;
				$form_data['text_version']		= $state_data->text_version;
				$form_data['campaign_id']		= $state_data->campaign_id;
				$form_data['usergroups']		= $state_data->usergroups;
				$form_data['is_template']		= $state_data->is_template;
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

				if (is_object($state_data) && property_exists($state_data, 'scheduled_date'))
				{
					$form_data['scheduled_date']	= $state_data->scheduled_date;
				}

				if (is_object($state_data) && property_exists($state_data, 'ready_to_send'))
				{
					$form_data['ready_to_send']	= $state_data->ready_to_send;
				}
				break;
		}

		// created_by an modified_by needed on every tab
		if (is_object($state_data) && property_exists($state_data, 'created_by'))
		{
			$form_data['created_by'] = $state_data->created_by;
		}

		if (is_object($state_data) && property_exists($state_data, 'modified_by'))
		{
			$form_data['modified_by'] = $state_data->modified_by;
		}

		if (array_key_exists('selected_content', $form_data) !== true)
		{
			$form_data['selected_content'] = array();
		}

		if (array_key_exists('usergroups', $form_data) !== true)
		{
			$form_data['usergroups'] = array();
		}

		// serialize selected_content
		$nl_content	= (array) $form_data['selected_content'];
		if (is_array($form_data['selected_content']))
		{
			$form_data['selected_content']	= implode(',', $form_data['selected_content']);
		}

		// some content or template has changed?
		if ($add_content)
		{
			if (($sel_content != $form_data['selected_content'])
				|| ($old_template != $form_data['template_id'])
				|| ($old_text_template != $form_data['text_template_id']))
			{
				if ($add_content == '-1'  && (count($nl_content) == 0))
				{
					$nl_content = (array) "-1";
				}

				// only render new content, if selection from article list or template has changed
				$renderer	= new contentRenderer();
				$content	= $renderer->getContent($nl_content, $form_data['template_id'], $form_data['text_template_id']);

				$form_data['html_version']	= $content['html_version'];
				$form_data['text_version']	= $content['text_version'];

				// add intro to form data
				if ($sel_content != $form_data['selected_content'] || $old_template != $form_data['template_id'])
				{
					$tpl = $renderer->getTemplate($form_data['template_id']);
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
					$tpl = $renderer->getTemplate($form_data['text_template_id']);
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
	 * @throws Exception
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
		$id	= $this->addSendMailContent($nl_id);
		if ($id	=== false)
		{
			$ret_msg	= JText::_('COM_BWPOSTMAN_NL_ERROR_CONTENT_PREPARING');
			return false;
		}

		// Prepare the recipient queue
		if (!$this->addSendMailQueue($ret_msg, $id, $recipients, $nl_id, $unconfirmed, $cam_id))
		{
			return false;
		}

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
	 * @throws \Exception
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
	 * @throws Exception
	 *
	 * @since
	 */
	public function getSelectedContentFromNewsletterTable()
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
	 * If a newsletter shall be sent, then it will be inserted at table sendMailContent
	 * as a manner of archive and process method completely with content,
	 * subject & Co. in
	 *
	 * @access	private
	 *
	 * @param 	int		        $nl_id          Newsletter ID
	 *
	 * @return 	int|boolean 	                int content ID, if everything went fine, else boolean false
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	private function addSendMailContent($nl_id)
	{
		$renderer	= new contentRenderer();
		$_db	= $this->_db;
		$query	= $_db->getQuery(true);

		$newsletters_data   = new stdClass();

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

		if (property_exists($newsletters_data, 'substitute_links'))
		{
			$tblSendmailContent->substitute_links 	= $newsletters_data->substitute_links;

			// support for plugin substitute links
			if ($tblSendmailContent->substitute_links == '1')
			{
				JFactory::getApplication()->setUserState('com_bwpostman.edit.newsletter.data.substitutelinks', '1');
			}
		}

		// Preprocess html version of the newsletter
		if (!$this->preprocessHtmlVersion($newsletters_data))
		{
			return false;
		}

		// Preprocess text version of the newsletter
		if (!$this->preprocessTextVersion($newsletters_data))
		{
			return false;
		}

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
	 * @throws Exception
	 *
	 * @since
	 */
	private function addSendMailQueue(&$ret_msg, $content_id, $recipients, $nl_id, $send_to_unconfirmed, $cam_id)
	{
		if (!$content_id)
		{
			return false;
		}

		if (!$nl_id)
		{
			$ret_msg	= JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_TECHNICAL_REASON');
			return false;
		}

		switch ($recipients)
		{
			case "recipients": // Contain subscribers and joomla users
				$tblSendmailQueue = $this->getTable('sendmailqueue', 'BwPostmanTable');

				$check_subscribers = 0;
				$check_allsubscribers = 0;
				$usergroups = array();

				$associatedMailinglists = $this->getAssociatedMailinglists($nl_id, $cam_id);

				if (!$associatedMailinglists)
				{
					$ret_msg = JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_NL_NO_LISTS');
					return false;
				}

				$this->getSubscriberChecks($associatedMailinglists, $check_subscribers, $check_allsubscribers, $usergroups);

				if ($check_allsubscribers)
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

				if (count($usergroups))
				{
					$params = JComponentHelper::getParams('com_bwpostman');
					if (!$tblSendmailQueue->pushJoomlaUser($content_id, $usergroups, $params->get('default_emailformat')))
					{
						$ret_msg	= JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_TECHNICAL_REASON');
						return false;
					}
				}

				if ($check_subscribers)
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

				if(count($testrecipients) > 0)
				{
					foreach($testrecipients AS $testrecipient)
					{
						$tblSendmailQueue->push(
							$content_id,
							$testrecipient->emailformat,
							$testrecipient->email,
							$testrecipient->name,
							$testrecipient->firstname,
							$testrecipient->id
						);
					}
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
	 * @throws Exception
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

		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('bwpostman');

		$dispatcher->trigger('onBwPostmanGetAdditionalQueueWhere', array(&$query, true));

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
		{
			return $result;
		}

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
	 * @param integer   $mailsPerStep     number mails to send
	 * @param boolean   $fromComponent    do we come from component or from plugin?
	 *
	 * @return int	0 -> queue is empty, 1 -> maximum reached, 2 -> fatal error
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function sendMailsFromQueue($mailsPerStep = 100, $fromComponent = true)
	{
		try
		{
			$sendMailCounter = 0;
			echo JText::_('COM_BWPOSTMAN_NL_SENDING_PROCESS');
			ob_flush();
			flush();

			while(1)
			{
				$ret = $this->sendMail($fromComponent);
				if ($ret == 0)
				{                              // Queue is empty!
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
		catch (\Throwable $e) // PHP-Version >= 7
		{
			return 2;
		}
		catch (\Exception $e) // PHP-Version < 7
		{
			return 2;
		}
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
	 * @throws Exception
	 *
	 * @since
	 */
	public function sendMail($fromComponent = true)
	{
		// initialize
		$renderer	        = new contentRenderer();
		$log_options        = array('test' => 'testtext');
		$logger             = new BwLogger($log_options);

		$app				= JFactory::getApplication();
		$uri  				= JUri::getInstance();
		$itemid_unsubscribe	= $this->getItemid('register');
		$itemid_edit		= $this->getItemid('edit');

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
		if (!$tblSendMailQueue->pop(2, $fromComponent))
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

			if ($this->demo_mode)
			{
				$tblSendMailQueue->recipient   = $this->dummy_recipient;
			}
		}

		$app->setUserState('com_bwpostman.newsletter.send.mode', $tblSendMailQueue->mode);

		// Get Data from sendmailcontent, set attachment path
		// @ToDo, store data in this class to prevent from loading every time a mail will be sent
		$app->setUserState('bwtimecontrol.mode', $tblSendMailQueue->mode);
		$tblSendMailContent->load($tblSendMailQueue->content_id);

		if ($tblSendMailContent->attachment)
		{
			$attachments = explode(';', $tblSendMailContent->attachment);
			$fullAttachments = array();

			foreach ($attachments as $attachment)
			{
				$fullAttachments[] = JPATH_SITE . '/' .$attachment;
			}

			$tblSendMailContent->attachment = $fullAttachments;
		}

		if (property_exists($tblSendMailContent, 'email'))
		{
			$tblSendMailContent->content_id	= $tblSendMailContent->id;
		}

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
				$query->where($_db->quoteName('subscriber_id') . ' = ' . (int) $recipients_data->id);
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
				$renderer->replaceTplLinks($body);
				$renderer->addHTMLFooter($body, $footerid);
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
				$renderer->replaceTextTplLinks($body);
				$renderer->addTextFooter($body, $footerid);
			}
			else
			{
				$body = str_replace("[%edit_link%]", "", $body);
				$body = str_replace("[%unsubscribe_link%]", "", $body);
				$body = str_replace("[%impressum%]", "", $body);
				$body = str_replace("[dummy]", "", $body);
			}
		}

		BwPostmanHelper::replaceLinks($body);

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
				// Trigger Plugin "substitutelinks"
				if ($app->getUserState('com_bwpostman.edit.newsletter.data.substitutelinks') == '1' || $tblSendMailContent->substitute_links == '1')
				{
					$dispatcher->trigger('onBwPostmanSubstituteBody', array(&$body, &$itemid_edit, &$itemid_unsubscribe));
				}
				else
				{
					$body = str_replace(
						"[UNSUBSCRIBE_HREF]",
						JText::sprintf('COM_BWPOSTMAN_NL_UNSUBSCRIBE_HREF', $uri->root(), $itemid_unsubscribe),
						$body
					);
					$body = str_replace(
						"[EDIT_HREF]",
						JText::sprintf('COM_BWPOSTMAN_NL_EDIT_HREF', $uri->root(), $itemid_edit),
						$body
					);
				}

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

			$tblSendMailQueue->push(
				$tblSendMailQueue->content_id,
				$tblSendMailQueue->mode,
				$tblSendMailQueue->recipient,
				$tblSendMailQueue->name,
				$tblSendMailQueue->firstname,
				$tblSendMailQueue->subscriber_id,
				$tblSendMailQueue->trial + 1
			);
			return -1;
		}

		// Send Mail
		// show queue working only wanted if sending newsletters from component backend directly, not in time controlled sending
		if ($fromComponent)
		{
			echo "\n<br>{$tblSendMailQueue->recipient} (" .
				JText::_('COM_BWPOSTMAN_NL_ERROR_SENDING_TRIAL') . ($tblSendMailQueue->trial + 1) . ") ... ";
			ob_flush();
			flush();
		}

		// Get a JMail instance
		$mailer		= JFactory::getMailer();
		$mailer->SMTPDebug = true;
		$sender		= array();

		$sender[0]	= $tblSendMailContent->from_email;
		$sender[1]	= $tblSendMailContent->from_name;

		if ($this->demo_mode)
		{
			$sender[0]	                        = $this->dummy_sender;
			$tblSendMailContent->reply_email	= $this->dummy_sender;
		}

		$mailer->setSender($sender);
		$mailer->addReplyTo($tblSendMailContent->reply_email, $tblSendMailContent->reply_name);
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
		if (!defined('BWPOSTMAN_NL_SENDING'))
		{
			define('BWPOSTMAN_NL_SENDING', 1);
		}

		if (!$this->arise_queue)
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

			$tblSendMailQueue->push(
				$tblSendMailQueue->content_id,
				$tblSendMailQueue->mode,
				$tblSendMailQueue->recipient,
				$tblSendMailQueue->name,
				$tblSendMailQueue->firstname,
				$tblSendMailQueue->subscriber_id,
				$tblSendMailQueue->trial + 1
			);
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
	private function processTestMode()
	{
		$test_plugin = JPluginHelper::getPlugin('system', 'bwtestmode');

		if ($test_plugin)
		{
			$params             = json_decode($test_plugin->params);

			$this->demo_mode       = $params->demo_mode_option;
			$this->dummy_sender    = $params->sender_address_option;
			$this->dummy_recipient = $params->recipient_address_option;
			$this->arise_queue     = $params->arise_queue_option;
		}
	}

	/**
	 * Method to get associated mailing lists by campaign
	 *
	 * @param  integer   $id   newsletter id
	 *
	 * @return array
	 *
	 * @throws \Exception
	 *
	 * @since 2.3.0
	 */
	private function getAssociatedMailinglistsByCampaign($id)
	{
		$_db	= $this->_db;
		$mailinglists = array();

		$query = $_db->getQuery(true);
		$query->select($_db->quoteName('mailinglist_id'));
		$query->from($_db->quoteName('#__bwpostman_campaigns_mailinglists'));
		$query->where($_db->quoteName('campaign_id') . ' = ' . (int) $id);

		$_db->setQuery($query);

		try
		{
			$mailinglists = $_db->loadColumn();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $mailinglists;
	}

	/**
	 * Method to get associated mailing lists by newsletter
	 *
	 * @param  integer   $id   newsletter id
	 *
	 * @return array
	 *
	 * @throws \Exception
	 *
	 * @since 2.3.0
	 */
	private function getAssociatedMailinglistsByNewsletter($id)
	{
		$_db	= $this->_db;
		$mailinglists = array();

		$query = $_db->getQuery(true);
		$query->select($_db->quoteName('mailinglist_id'));
		$query->from($_db->quoteName('#__bwpostman_newsletters_mailinglists'));
		$query->where($_db->quoteName('newsletter_id') . ' = ' . (int) $id);

		$_db->setQuery($query);

		try
		{
			$mailinglists = $_db->loadColumn();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $mailinglists;
	}

	/**
	 * Method to get mailing lists by restriction
	 *
	 * @param array     $mailinglists
	 * @param string    $condition
	 *
	 * @return array
	 *
	 * @throws \Exception
	 *
	 * @since 2.3.0
	 */
	private function getMailinglistsByRestriction($mailinglists, $condition)
	{
		$mls   = null;
		$_db   = $this->_db;

		$query = $_db->getQuery(true);
		$query->select('id');
		$query->from($_db->quoteName('#__bwpostman_mailinglists'));
		$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);

		switch ($condition)
		{
			case 'available':
				$query->where($_db->quoteName('published') . ' = ' . (int) 1);
				$query->where($_db->quoteName('access') . ' = ' . (int) 1);
				break;
			case 'unavailable':
				$query->where($_db->quoteName('published') . ' = ' . (int) 1);
				$query->where($_db->quoteName('access') . ' > ' . (int) 1);
				break;
			case 'internal':
				$query->where($_db->quoteName('published') . ' = ' . (int) 0);
				break;
		}

		$_db->setQuery($query);

		try
		{
			$mls = $_db->loadColumn();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		$resultingMls = array_intersect($mailinglists, $mls);

		if (count($resultingMls) > 0)
		{
			$restrictedMls = $resultingMls;
		}
		else
		{
			$restrictedMls = array();
		}

		return $restrictedMls;
	}

	/**
	 * Method to preset HTML-Template for old newsletters

	 * @param                 $item
	 *
	 * @return void
	 *
	 * @since 2.3.0
	 *
	 * @throws Exception
	 */
	private function presetOldHTMLTemplate(&$item)
	{
		$html_tpl = null;
		$_db   = $this->_db;

		if ($item->id == 0)
		{
			$item->template_id = $this->getStandardTpl('html');
		}
		elseif ($item->template_id == 0)
		{
			$query = $_db->getQuery(true);
			$query->select('id');
			$query->from($_db->quoteName('#__bwpostman_templates'));
			$query->where($_db->quoteName('id') . ' = ' . (int) -1);

			$_db->setQuery($query);

			try
			{
				$html_tpl = $_db->loadResult();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			if (is_null($html_tpl))
			{
				$html_tpl = $this->getStandardTpl('html');
			}

			$item->template_id = $html_tpl;
		}
	}

	/**
	 * Method to preset Text-Template for old newsletters

	 * @param                 $item
	 *
	 * @return void
	 *
	 * @since 2.3.0
	 *
	 * @throws Exception
	 */
	private function presetOldTextTemplate(&$item)
	{
		$text_tpl = null;
		$_db   = $this->_db;

		if ($item->id == 0)
		{
			$item->text_template_id = $this->getStandardTpl('text');
		}
		elseif ($item->text_template_id == 0)
		{
			$query = $_db->getQuery(true);
			$query->select('id');
			$query->from($_db->quoteName('#__bwpostman_templates'));
			$query->where($_db->quoteName('id') . ' = ' . (int) -2);

			$_db->setQuery($query);

			try
			{
				$text_tpl = $_db->loadResult();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			if (is_null($text_tpl))
			{
				$text_tpl = $this->getStandardTpl('text');
			}

			$item->text_template_id = $text_tpl;
		}
	}

	/**
	 * @param array    $associatedMailinglists
	 * @param string   $status
	 * @param boolean  $allSubscribers
	 *
	 * @return integer
	 *
	 * @throws \Exception
	 *
	 * @since 2.3.0
	 */
	private function countSubscribersOfNewsletter(array $associatedMailinglists, $status, $allSubscribers)
	{
		$count_subscribers = 0;
		$_db       = $this->_db;
		$query     = $_db->getQuery(true);

		$query->select('COUNT(' . $_db->quoteName('id') . ')');
		$query->from($_db->quoteName('#__bwpostman_subscribers'));

		if (!$allSubscribers)
		{
			$subQuery1 = $_db->getQuery(true);
			$subQuery1->select('DISTINCT' . $_db->quoteName('subscriber_id'));
			$subQuery1->from($_db->quoteName('#__bwpostman_subscribers_mailinglists'));
			$subQuery1->where($_db->quoteName('mailinglist_id') . ' IN (' . implode(',', $associatedMailinglists) . ')');
			$query->where($_db->quoteName('id') . ' IN (' . $subQuery1 . ')');
		}

		$query->where($_db->quoteName('status') . ' IN (' . $status . ')');
		$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);

		$_db->setQuery($query);

		try
		{
			$count_subscribers = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $count_subscribers;
	}

	/**
	 * @param array $usergroup
	 *
	 * @return mixed
	 *
	 * @throws \Exception
	 *
	 * @since 2.3.0
	 */
	private function countUsersOfNewsletter(array $usergroup)
	{
		$count_users = 0;
		$_db       = $this->_db;
		$query     = $_db->getQuery(true);
		$sub_query = $_db->getQuery(true);

		$sub_query->select($_db->quoteName('g') . '.' . $_db->quoteName('user_id'));
		$sub_query->from($_db->quoteName('#__user_usergroup_map') . ' AS ' . $_db->quoteName('g'));
		$sub_query->where($_db->quoteName('g') . '.' . $_db->quoteName('group_id') . ' IN (' . implode(',',
				$usergroup) . ')');

		$query->select('COUNT(' . $_db->quoteName('u') . '.' . $_db->quoteName('id') . ')');
		$query->from($_db->quoteName('#__users') . ' AS ' . $_db->quoteName('u'));
		$query->where($_db->quoteName('u') . '.' . $_db->quoteName('block') . ' = ' . (int) 0);
		$query->where($_db->quoteName('u') . '.' . $_db->quoteName('activation') . ' = ' . $_db->quote(''));
		$query->where($_db->quoteName('u') . '.' . $_db->quoteName('id') . ' IN (' . $sub_query . ')');

		$_db->setQuery($query);

		try
		{
			$count_users = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $count_users;
	}

	/**
	 * Method to preprocess content of HTML version of the newsletter
	 *
	 * @param object $newsletters_data
	 *
	 * @return mixed
	 *
	 * @throws \Exception
	 *
	 * @since 2.3.0
	 */
	private function preprocessHtmlVersion($newsletters_data)
	{
		$renderer = new contentRenderer();

		// only for old text templates
		if ($newsletters_data->template_id < 1)
		{
			$newsletters_data->html_version = $newsletters_data->html_version . '[dummy]';
		}

		// add template data
		if (!$renderer->addTplTags($newsletters_data->html_version, $newsletters_data->template_id))
		{
			return false;
		}

		// Replace the intro at HTML part of the newsletter
		$replace_html_intro_head = '';
		if (!empty($newsletters_data->intro_headline))
		{
			$replace_html_intro_head = $newsletters_data->intro_headline;
		}

		$newsletters_data->html_version = str_replace('[%intro_headline%]', $replace_html_intro_head,
			$newsletters_data->html_version);

		$replace_html_intro_text = '';
		if (!empty($newsletters_data->intro_text))
		{
			$replace_html_intro_text = nl2br($newsletters_data->intro_text, true);
		}

		$newsletters_data->html_version = str_replace('[%intro_text%]', $replace_html_intro_text,
			$newsletters_data->html_version);

		if (!$renderer->replaceTplLinks($newsletters_data->html_version))
		{
			return false;
		}

		if (!$renderer->addHtmlTags($newsletters_data->html_version, $newsletters_data->template_id))
		{
			return false;
		}

		if (!$renderer->addHTMLFooter($newsletters_data->html_version, $newsletters_data->template_id))
		{
			return false;
		}

		if (!BwPostmanHelper::replaceLinks($newsletters_data->html_version))
		{
			return false;
		}

		return true;
	}

	/**
	 * Method to preprocess content of text version of the newsletter
	 *
	 * @param object $newsletters_data
	 *
	 * @return mixed
	 *
	 * @throws \Exception
	 *
	 * @since 2.3.0
	 */
	private function preprocessTextVersion($newsletters_data)
	{
		$renderer = new contentRenderer();

		// only for old text templates
		if ($newsletters_data->text_template_id < 1)
		{
			$newsletters_data->text_version = $newsletters_data->text_version . '[dummy]';
		}

	// add template data
		if (!$renderer->addTextTpl($newsletters_data->text_version, $newsletters_data->text_template_id))
		{
			return false;
		}

	// Replace the intro at text part of the newsletter
		$replace_text_intro_head = '';
		if (!empty($newsletters_data->intro_text_headline))
		{
			$replace_text_intro_head = $newsletters_data->intro_text_headline;
		}

		$newsletters_data->text_version = str_replace('[%intro_headline%]', $replace_text_intro_head,
			$newsletters_data->text_version);

		$replace_text_intro_text = '';
		if (!empty($newsletters_data->intro_text_text))
		{
			$replace_text_intro_text = $newsletters_data->intro_text_text;
		}

		$newsletters_data->text_version = str_replace('[%intro_text%]', $replace_text_intro_text,
			$newsletters_data->text_version);

		if (!$renderer->replaceTextTplLinks($newsletters_data->text_version))
		{
			return false;
		}

		if (!$renderer->addTextFooter($newsletters_data->text_version, $newsletters_data->text_template_id))
		{
			return false;
		}

		if (!BwPostmanHelper::replaceLinks($newsletters_data->text_version))
		{
			return false;
		}

		return true;
	}

	/**
	 * Method to get the associated mailinglists of a newsletter
	 * @param integer  $nl_id
	 * @param integer  $cam_id
	 *
	 * @return array
	 *
	 * @since 2.3.0
	 *
	 * @throws Exception
	 */
	private function getAssociatedMailinglists($nl_id, $cam_id)
	{
		if ($cam_id != '-1')
		{
			// Check if there are assigned mailinglists or usergroups
			$mailinglists = $this->getAssociatedMailinglistsByCampaign((int) $cam_id);
		}
		else
		{
			// Check if there are assigned mailinglists or usergroups of the campaign
			$mailinglists = $this->getAssociatedMailinglistsByNewsletter((int) $nl_id);
		}

		return $mailinglists;
	}


	/**
	 * Method to get the associated mailinglists of a newsletter
	 *
	 * @param array    $mailinglists
	 * @param boolean  $check_subscribers
	 * @param boolean  $check_allsubscribers
	 * @param array    $usergroups
	 *
	 * @return string|boolean
	 *
	 * @since 2.3.0
	 */
	private function getSubscriberChecks($mailinglists, &$check_subscribers, &$check_allsubscribers, &$usergroups)
	{
		foreach ($mailinglists as $mailinglist)
		{
			// Mailinglists
			if ($mailinglist > 0)
			{
				$check_subscribers = 1;
			}

			// All subscribers
			if ($mailinglist == -1)
			{
				$check_allsubscribers = 1;
			}
			else
			{
				// Usergroups
				if ((int) $mailinglist < 0)
				{
					$usergroups[] = -(int) $mailinglist;
				}
			}
		}

		return true;
	}
}
