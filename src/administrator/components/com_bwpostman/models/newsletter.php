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

// Import MODEL and Helper object class
jimport('joomla.application.component.modeladmin');

use Joomla\CMS\Form\Form;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Mail\MailHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Log\LogEntry;
use Joomla\CMS\Filter\InputFilter;

// Require helper class
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/libraries/logging/BwLogger.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/contentRenderer.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/helper.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/newsletterhelper.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/subscriberhelper.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/mailinglisthelper.php');
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
	 * property to messages while sending
	 *
	 * @var string
	 *
	 * @since       3.0.0
	 */
	public $sendmessage;
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
		$this->permissions		= Factory::getApplication()->getUserState('com_bwpm.permissions');

		parent::__construct();

		$jinput = Factory::getApplication()->input;
		$cids   = $jinput->get('cid',  array(0), '');
		$this->setId((int) $cids[0]);

		$this->processTestMode();

		$log_options    = array();
		$this->logger   = BwLogger::getInstance($log_options);
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
		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to reset the newsletter ID and data
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
	 * @throws Exception
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
		$app  = Factory::getApplication();
		$item = new stdClass();
		PluginHelper::importPlugin('bwpostman');

		// Initialise variables.
		$pk    = (int)(!empty($pk)) ? $pk : $this->getState($this->getName() . '.id');
		$table = $this->getTable();
		$app->setUserState('com_bwpostman.edit.newsletter.id', $pk);

		// Get input data
		$state_data	= $app->getUserState('com_bwpostman.edit.newsletter.data');

		// if state exists and matches required id, use state, otherwise get data from table
		if (is_object($state_data) && $state_data->id == $pk)
		{
			$item = $state_data;
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

				$app->triggerEvent('onBwPostmanAfterNewsletterModelGetProperties', array(&$properties));
				$item = ArrayHelper::toObject($properties, 'JObject');

				if (property_exists($item, 'params'))
				{
					$registry     = new Registry($item->params);
					$item->params = $registry->toArray();
				}

				// Get associated mailinglists
				$crossTable = $this->getTable('Newsletters_Mailinglists');
				$item->mailinglists = $crossTable->getAssociatedMailinglistsByNewsletter($item->id);

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

				if ($pk === 0)
				{
					$item->id = 0;
				}

				// get available mailinglists to predefine for state
				$mlTable            = $this->getTable('Mailinglists');
				$item->ml_available = $mlTable->getMailinglistsByRestriction($item->mailinglists, 'available');

				// get unavailable mailinglists to predefine for state
				$item->ml_unavailable = $mlTable->getMailinglistsByRestriction($item->mailinglists, 'unavailable');

				// get internal mailinglists to predefine for state
				$item->ml_intern = $mlTable->getMailinglistsByRestriction($item->mailinglists, 'internal');

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

		// if plugin "substitutelinks" is active and substitute_links == '1' -> setUserState
		if (isset($item->substitute_links) && $item->substitute_links == '1')
		{
			$app->setUserState('com_bwpostman.edit.newsletter.data.substitutelinks', '1');
		}

		$app->setUserState('com_bwpostman.edit.newsletter.data', $item);

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
		Form::addFieldPath('JPATH_ADMINISTRATOR/components/com_bwpostman/models/fields');

		$params = ComponentHelper::getParams('com_bwpostman');
		$config = Factory::getConfig();
		$user   = Factory::getUser();

		$form = $this->loadForm('com_bwpostman.newsletter', 'newsletter', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		$jinput = Factory::getApplication()->input;
		$id     = $jinput->get('id', 0);

		// predefine some values
		if (!$form->getValue('from_name'))
		{
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
		$nulldate = Factory::getDbo()->getNullDate();
		$c_date   = $form->getValue('created_date');

		if ($c_date === $nulldate)
		{
			$form->setFieldAttribute('created_date', 'type', 'hidden');
			$form->setFieldAttribute('created_by', 'type', 'hidden');
		}

		// Check to show modified data
		$m_date	= $form->getValue('modified_time');

		if ($m_date === $nulldate)
		{
			$form->setFieldAttribute('modified_time', 'type', 'hidden');
			$form->setFieldAttribute('modified_by', 'type', 'hidden');
		}

		// Check to show mailing data
		$s_date	= $form->getValue('mailing_date');

		if ($s_date === $nulldate)
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
		$recordId = Factory::getApplication()->getUserState('com_bwpostman.newsletter.id');

		// Check the session for previously entered form data for this record id.
		$data = Factory::getApplication()->getUserState('com_bwpostman.edit.newsletter.data', null);

		if (empty($data) || (is_object($data) && $recordId != $data->id))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method check if newsletter is content template
	 *
	 * @param   integer  $id        ID of newsletter
	 *
	 * @return	boolean           state of is_template
	 *
	 * @throws Exception
	 *
	 * @since	2.2.0
	 */
	public function isTemplate($id)
	{
		$table = $this->getTable();

		$isTemplate = $table->isTemplate($id);

		return $isTemplate;
	}

	/**
	 * Method to get the data of a single newsletter for the preview/modal box
	 *
	 * @return 	object Newsletter with formatted pieces
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function getSingleNewsletter()
	{
		$app  = Factory::getApplication();
		$item = $app->getUserState('com_bwpostman.edit.newsletter.data');

		//convert to object if necessary
		if ($item && !is_object($item))
		{
			$item = ArrayHelper::toObject($item);
		}

		// if old newsletter, there are no template IDs, so lets set them to the old template
		if (property_exists($item, 'template_id') && $item->template_id == '0')
		{
			$item->template_id = -1;
		}

		if (property_exists($item, 'text_template_id') && $item->text_template_id == '0')
		{
			$item->text_template_id = -2;
		}

		$item->template_id      = (int)$item->template_id;
		$item->text_template_id = (int)$item->text_template_id;

		$renderer = new contentRenderer();

		if ($item->id == 0 && !empty($item->selected_content) && empty($item->html_version) && empty($item->text_version))
		{
			if (!is_array($item->selected_content))
			{
				$item->selected_content = explode(',', $item->selected_content);
			}

			$content = $renderer->getContent((array) $item->selected_content, $item->template_id, $item->text_template_id);

			$item->html_version	= $content['html_version'];
			$item->text_version	= $content['text_version'];
		}

		// force two linebreaks at the end of text
		$item->text_version = rtrim($item->text_version) . "\n\n";

		// Replace the links to provide the correct preview
		$item->html_formatted = $item->html_version;
		$item->text_formatted = $item->text_version;

		// add template data
		$renderer->addTplTags($item->html_formatted, $item->template_id);
		$renderer->addTextTpl($item->text_formatted, $item->text_template_id);

		// Replace the intro to provide the correct preview
		if (!empty($item->intro_headline))
		{
			$item->html_formatted = str_replace('[%intro_headline%]', $item->intro_headline, $item->html_formatted);
		}

		if (!empty($item->intro_text))
		{
			$item->html_formatted = str_replace('[%intro_text%]', nl2br($item->intro_text, true), $item->html_formatted);
		}

		if (!empty($item->intro_text_headline))
		{
			$item->text_formatted = str_replace('[%intro_headline%]', $item->intro_text_headline, $item->text_formatted);
		}

		if (!empty($item->intro_text_text))
		{
			$item->text_formatted = str_replace('[%intro_text%]', $item->intro_text_text, $item->text_formatted);
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
	 * @return	array
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function getSelectedContentItems()
	{
		$nlId = (int) $this->getState($this->getName() . '.id');
		$db   = $this->_db;

		$selected_content = $this->getTable()->getSelectedContentOfNewsletter($nlId);

		if (!$selected_content)
		{
			return array();
		}

		if (!is_array($selected_content))
		{
			$selected_content = explode(',', $selected_content);
		}

		$selected_content = ArrayHelper::toInteger($selected_content);

		$selected_content_items = array();

		// We do a foreach to protect our ordering
		foreach($selected_content as $content_id)
		{
			$items  = array();

			$subquery = $db->getQuery(true);
			$subquery->select($db->quoteName('cc') . '.' . $db->quoteName('title'));
			$subquery->from($db->quoteName('#__categories') . ' AS ' . $db->quoteName('cc'));
			$subquery->where($db->quoteName('cc') . '.' . $db->quoteName('id') . ' = ' . $db->quoteName('c') . '.' . $db->quoteName('catid'));

			$query = $db->getQuery(true);
			$query->select($db->quoteName('c') . '.' . $db->quoteName('id'));
			$query->select($db->quoteName('c') . '.' . $db->quoteName('title') . ', (' . $subquery . ') AS ' . $db->quoteName('category_name'));
			$query->from($db->quoteName('#__content') . ' AS ' . $db->quoteName('c'));
			$query->where($db->quoteName('c') . '.' . $db->quoteName('id') . ' = ' . $db->quote($content_id));

			try
			{
				$db->setQuery($query);

				$items = $db->loadObjectList();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			if(count($items) > 0)
			{
				$categoryName = "Uncategorized - ";

				if ($items[0]->category_name !== '')
				{
					$categoryName = $items[0]->category_name . " - ";
				}

				$selected_content_items[] = HtmlHelper::_('select.option', $items[0]->id, $categoryName . $items[0]->title);
			}
		}

		return $selected_content_items;
	}

	/**
	 * Method to validate the form data.
	 *
	 * @param   Form    $form   The form to validate against.
	 * @param   array   $data   The data to validate.
	 * @param   string  $group  The name of the field group to validate.
	 *
	 * @return  array|boolean  Array of filtered data if valid, false otherwise.
	 *
	 * @see     FormRule
	 * @see     InputFilter
	 *
	 * @since   3.0.0
	 */
	public function validate($form, $data, $group = null)
	{
		if (!isset($data['attachment']) || $data['attachment'] === "")
		{
			$data['attachment'] = array();
		}

		$validData = parent::validate($form, $data, $group);

		return $validData;
	}

	/**
	 * Method to store the newsletter data into the newsletters-table
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
		$app      = Factory::getApplication();
		$jinput   = $app->input;
		$nullDate = Factory::getDbo()->getNullDate();

		// Correct empty publishing dates
		if (isset($data['publish_up']) &&  $data['publish_up'] === "")
		{
			$data['publish_up'] = $nullDate;
		}

		if (isset($data['publish_down']) &&  $data['publish_down'] === "")
		{
			$data['publish_down'] = $nullDate;
		}

		// merge ml-arrays, single array may not exist, therefore array_merge would not give a result
		BwPostmanMailinglistHelper::mergeMailinglists($data);

		// convert attachment array to JSON, to be able to save
		if (isset($data['attachment']) && is_array($data['attachment']) && $data['attachment'] !== '')
		{
			if (count($data['attachment']))
			{
				$data['attachment'] = json_encode($data['attachment']);
			}
			else
			{
				$data['attachment'] = "";
			}
		}

		PluginHelper::importPlugin('bwpostman');

		// if saving a new newsletter before changing tab, we have to look, if there is a content selected and set html- and text-version
		if (empty($data['html_version']) && empty($data['text_version']))
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_NL_ERROR_CONTENT_MISSING'));
			return false;
		}

		if (!parent::save($data))
		{
			return false;
		}

		$crossTableNlMl = $this->getTable('Newsletters_Mailinglists');

		// On existing newsletter delete all entries of the newsletter from newsletters_mailinglists table
		if ($data['id'])
		{
			$crossTableNlMl->deleteNewsletter((int) $data['id']);
		}
		else
		{
			//get id of new inserted data to write cross table newsletters-mailinglists and inject into form
			$data['id']	= (int)$app->getUserState('com_bwpostman.newsletter.id');
			$jinput->set('id', $data['id']);

			// update state
			$state_data	= $app->getUserState('com_bwpostman.edit.newsletter.data');

			if (is_object($state_data))
			{	// check needed because copying newsletters has no state and does not need it
				$state_data->id	= $data['id'];
				$app->setUserState('com_bwpostman.edit.newsletter.data', $state_data);
			}
		}

		// Rewrite newsletters_mailinglists table (only if newsletter is not part of campaign, else this is managed at campaigns)
		if ((int)$data['campaign_id'] === -1)
		{
			// Store the selected BwPostman mailinglists into newsletters_mailinglists-table
			if (isset($data['mailinglists']))
			{
				foreach ($data['mailinglists'] AS $mailinglists_value)
				{
					$crossTableNlMl->insertNewsletter((int) $data['id'], (int) $mailinglists_value);
				}
			}
		}

		$app->triggerEvent('onBwPostmanAfterNewsletterModelSave', array(&$data));

		return true;
	}

	/**
	 * Method to (un)archive a newsletter from the newsletters-table
	 * --> when unarchiving it is called by the archive-controller
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
		$app        = Factory::getApplication();
		$state_data = $app->getUserState('com_bwpostman.edit.newsletter.data');
		$cid        = ArrayHelper::toInteger($cid);

		// Access check.
		foreach ($cid as $id)
		{
			if ($archive === 1)
			{
				if (!BwPostmanHelper::canArchive('newsletter', 0, $id))
				{
					return false;
				}
			}
			else
			{
				if (!BwPostmanHelper::canRestore('newsletter', $id))
				{
					return false;
				}
			}
		}

		if (count($cid))
		{
			$this->getTable()->archive($cid, $archive);
		}

		$app->setUserState('com_bwpostman.edit.newsletter.data', $state_data);
		return true;
	}

	/**
	 * Method to copy one or more newsletters
	 * --> the assigned mailing lists will be copied, too
	 *
	 * @param 	integer   $cid        Newsletter-ID
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function copy($id)
	{
		if (!$this->permissions['newsletter']['create'])
		{
			return false;
		}

		$app = Factory::getApplication();

		if (!$id)
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_NL_ERROR_COPYING'), 'error');

			return false;
		}

		$db	= $this->_db;

		// Get newsletter data to copy
		$newsletters_data_copy = $this->getTable()->getNewsletterData($id);

		if (!$newsletters_data_copy || !is_object($newsletters_data_copy))
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_NL_COPY_FAILED'), 'error');

			return false;
		}

		// Adjust usergroups data
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

		$date = Factory::getDate();
		$time = $date->toSql();
		$user = Factory::getUser();
		$uid  = $user->get('id');

		// Reset some item specific values
		$newsletters_data_copy->id               = null;
		$newsletters_data_copy->asset_id         = null;
		$newsletters_data_copy->subject          = Text::sprintf('COM_BWPOSTMAN_NL_COPY_OF', $newsletters_data_copy->subject);
		$newsletters_data_copy->attachment       = null;
		$newsletters_data_copy->created_date     = $time;
		$newsletters_data_copy->created_by       = $uid;
		$newsletters_data_copy->modified_time    = null;
		$newsletters_data_copy->modified_by      = null;
		$newsletters_data_copy->mailing_date     = 0;
		$newsletters_data_copy->published        = null;
		$newsletters_data_copy->checked_out      = null;
		$newsletters_data_copy->checked_out_time = null;
		$newsletters_data_copy->archive_flag     = 0;
		$newsletters_data_copy->archive_date     = 0;
		$newsletters_data_copy->hits             = null;
		$newsletters_data_copy->substitute_links = null;
		$newsletters_data_copy->is_template      = 0;

		$crossTable = $this->getTable('Newsletters_Mailinglists');

		$newsletters_data_copy->mailinglists = $crossTable->getAssociatedMailinglistsByNewsletter((int) $id);

		if (!$this->save(ArrayHelper::fromObject($newsletters_data_copy, false)))
		{
			$app->enqueueMessage($db->getErrorMsg(), 'error');

			return false;
		}

		$app->setUserState('com_bwpostman.edit.newsletter.data', $newsletters_data_copy);

		$app->enqueueMessage(Text::_('COM_BWPOSTMAN_NL_COPIED'), 'message');
		return true;
	}

	/**
	 * Method to remove one or more newsletters from the newsletters-table
	 * --> is called by the archive-controller
	 *
	 * @param	array   $pks        Newsletter IDs
	 *
	 * @return	boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function delete(&$pks)
	{
		if (count($pks))
		{
			$pks = ArrayHelper::toInteger($pks);

			// Access check.
			foreach ($pks as $id)
			{
				if (!BwPostmanHelper::canDelete('newsletter', $id))
				{
					return false;
				}

				// Delete newsletter from newsletters-table
				if (!$this->getTable()->delete($id))
				{
					return false;
				}

				// Delete assigned mailinglists from newsletters_mailinglists-table
				if (!$this->getTable('newsletters_mailinglists')->delete($id))
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

		$queueTable = $this->getTable('Sendmailqueue');

		try
		{
			$queueTable->clearQueue();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
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
	 * @throws Exception
	 *
	 * @since   1.6
	 */
	public function changeIsTemplate($id)
	{
		$user  = Factory::getUser();
		$table = $this->getTable();
		$id    = (int) $id;

		// Access checks.
		if ($table->load($id))
		{
			if (!BwPostmanHelper::canEdit('newsletter', array('id' => $id)))
			{
				$this->logger->addEntry(new LogEntry(Text::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'), BwLogger::BW_WARNING, 'newsletter'));

				return false;
			}

			// If the table is checked out by another user, drop it and report to the user trying to change its state.
			if (property_exists($table, 'checked_out') && $table->checked_out && ($table->checked_out != $user->id))
			{
				$this->logger->addEntry(new LogEntry(Text::_('JLIB_APPLICATION_ERROR_CHECKIN_USER_MISMATCH'), BwLogger::BW_WARNING, 'newsletter'));

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
	 * Method to do checks before sending
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
		$recordId = (int)$recordId;

		// Access check.
		if (!BwPostmanHelper::canSend($recordId))
		{
			$error[] = Text::_('COM_BWPOSTMAN_NL_ERROR_SEND_NOT_PERMITTED');

			return false;
		}

		// Check the newsletter form
		$data = $this->checkForm($error, $recordId, $automation);

		// if checkForm fails redirect to edit
		if ($error)
		{
			return false;
		}

		//check for content template
		if ($data['is_template'] === "1")
		{
			$error[] = Text::_('COM_BWPOSTMAN_NL_IS_TEMPLATE_ERROR');

			return false;
		}

		Factory::getApplication()->setUserState('com_bwpostman.newsletter.idToSend', $recordId);

		return $data;
	}

	/**
	 * Method to check and clean the input fields
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
		$recordId = (int)$recordId;

		if (!$automation)
		{
			// heal form data and get them
			$this->changeTab();
			$data = ArrayHelper::fromObject(Factory::getApplication()->getUserState('com_bwpostman.edit.newsletter.data'));

			$data['id'] = $recordId;
		}
		else
		{
			$data = ArrayHelper::fromObject($this->getItem($recordId));
		}

		//Remove all HTML tags from name, emails, subject and the text version
		$filter               = new InputFilter(array(), array(), 0, 0);
		$data['from_name']    = $filter->clean($data['from_name']);
		$data['from_email']   = $filter->clean($data['from_email']);
		$data['reply_email']  = $filter->clean($data['reply_email']);
		$data['subject']      = $filter->clean($data['subject']);
		$data['text_version'] = $filter->clean($data['text_version']);

		$err = array();

		// Check for valid from_name
		if (trim($data['from_name']) === '')
		{
			$err[] = Text::_('COM_BWPOSTMAN_NL_ERROR_FROM_NAME');
		}

		// Check for valid from_email address
		if (trim($data['from_email']) === '')
		{
			$err[] = Text::_('COM_BWPOSTMAN_NL_ERROR_FROM_EMAIL');
		}
		else
		{
			// If there is a from_email address check if the address is valid
			if (!MailHelper::isEmailAddress(trim($data['from_email'])))
			{
				$err[] = Text::_('COM_BWPOSTMAN_NL_ERROR_FROM_EMAIL_INVALID');
			}
		}

		// Check for valid reply_email address
		if (trim($data['reply_email']) === '')
		{
			$err[] = Text::_('COM_BWPOSTMAN_NL_ERROR_REPLY_EMAIL');
		}
		else
		{
			// If there is a from_email address check if the address is valid
			if (!MailHelper::isEmailAddress(trim($data['reply_email'])))
			{
				$err[] = Text::_('COM_BWPOSTMAN_NL_ERROR_REPLY_EMAIL_INVALID');
			}
		}

		// Check for valid subject
		if (trim($data['subject']) === '')
		{
			$err[] = Text::_('COM_BWPOSTMAN_NL_ERROR_SUBJECT');
		}

		// Check for valid html or text version
		if ((trim($data['html_version']) === '') && (trim($data['text_version']) === ''))
		{
			$err[] = Text::_('COM_BWPOSTMAN_NL_ERROR_HTML_AND_TEXT');
		}

		$nullDate = Factory::getDbo()->getNullDate();

		// Check for valid publish_up values
		if (!$data['publish_up'])
		{
			$data['publish_up'] = $nullDate;
		}

		// Check for valid publish_down values
		if (!$data['publish_down'])
		{
			$data['publish_down'] = $nullDate;
		}

		return $data;
	}

	/**
	 * Method to check if there are selected mailinglists and/or usergroups and if they contain recipients
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
	public function checkForRecipients(&$ret_msg, $nl_id, $send_to_unconfirmed, $cam_id)
	{
		try
		{
			$check_subscribers    = 0;
			$usergroups           = array();

			$associatedMailinglists = $this->getAssociatedMailinglists($nl_id, (int)$cam_id);

			if (!$associatedMailinglists)
			{
				$ret_msg = Text::_('COM_BWPOSTMAN_NL_ERROR_SENDING_NL_NO_LISTS');
				return false;
			}

			$this->getSubscriberChecks($associatedMailinglists, $check_subscribers, $usergroups);

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

				$count_subscribers = BwPostmanNewsletterHelper::countSubscribersOfNewsletter($associatedMailinglists, $status, false);
			}

			// Checks if the selected usergroups contain users
			$count_users = 0;

			if (is_array($usergroups) && count($usergroups))
			{
				$count_users = BwPostmanNewsletterHelper::countUsersOfNewsletter($usergroups);
			}

			// We return only false, if no subscribers AND no joomla users are selected.
			if (!$count_users && !$count_subscribers)
			{
				if (!$count_users)
				{
					$ret_msg = Text::_('COM_BWPOSTMAN_NL_ERROR_SENDING_NL_NO_USERS');

					return false;
				}

				if (!$count_subscribers)
				{
					$ret_msg = Text::_('COM_BWPOSTMAN_NL_ERROR_SENDING_NL_NO_SUBSCRIBERS');

					return false;
				}
			}
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return true;
	}

	/**
	 * Method to check if there are test-recipients to whom the newsletter shall be send
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function checkForTestrecipients()
	{
		$subsTable      = $this->getTable('subscribers');
		$testrecipients = $subsTable->checkForTestrecipients();

		return $testrecipients;
	}

	/**
	 * Method to compose a newsletter out of the selected content items
	 *
	 * @return 	array $content  associative array of content data
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function composeNl()
	{
		$jinput	= Factory::getApplication()->input;

		$nl_content       = $jinput->get('selected_content');
		$template_id      = $jinput->get('template_id');
		$text_template_id = $jinput->get('text_template_id');
		$renderer         = new contentRenderer();
		$content          = $renderer->getContent($nl_content, $template_id, $text_template_id);

		return $content;
	}

	/**
	 * Method to fetch the content out of the selected content items
	 * $layout is the source tab
	 * $tab ist the target tab
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function changeTab()
	{
		$app         = Factory::getApplication();
		$jinput      = $app->input;
		$form_data   = $jinput->get('jform', '', 'array');
		$layout      = $jinput->get('layout', '', 'string');
		$add_content = $jinput->get('add_content', 0);

		// support for plugin substitute links
		if(isset($form_data['substitute_links']) && $form_data['substitute_links'] == '1')
		{
			$app->setUserState('com_bwpostman.edit.newsletter.data.substitutelinks', '1');
		}

		$state_data = $app->getUserState('com_bwpostman.edit.newsletter.data');

		// Check for differences between form and state, only for development purpose
//		$diffDataKeys = $this->getDiffDataKeys($state_data, $form_data);
//		$diffDataValues = $this->getDiffDataValues($state_data, $form_data);

		if (!is_object($state_data))
		{
			$state_data = ArrayHelper::toObject($state_data, 'stdClass', false);
		}

		// inject newly changed values to form and heal form fields
		switch ($layout)
		{
			case 'edit_send':
				if (property_exists($state_data, 'template_old_id'))
				{
					$form_data['template_old_id'] = $state_data->template_old_id;
				}

				if (property_exists($state_data, 'text_template_old_id'))
				{
					$form_data['text_template_old_id'] = $state_data->text_template_old_id;
				}

				if (property_exists($state_data, 'access'))
				{
					$form_data['access'] = $state_data->access;
				}
				break;
			case 'edit_publish':
				$state_data->publish_up   = $form_data['publish_up'];
				$state_data->publish_down = $form_data['publish_down'];
				$form_data['is_template'] = $state_data->is_template;
				break;
			case 'edit_basic':
				// convert attachment array to JSON, to be able to save and show as hidden field
				if (isset($form_data['attachment']) && is_array($form_data['attachment']) && $form_data['attachment'] !== '')
				{
					$form_data['attachment'] = json_encode($form_data['attachment']);
				}

				$state_data->attachment = $form_data['attachment'];
				break;
			case 'edit_html':
			case 'edit_text':
			case 'edit_preview':
			default:
				break;
		}

		if ($layout !== 'edit_basic')
		{
			$elementsToCheck1 = array(
				'usergroups',
				'ml_available',
				'ml_unavailable',
				'ml_intern',
				'substitute_links',
				'scheduled_date',
				'ready_to_send',
			);

			foreach ($elementsToCheck1 as $element)
			{
				if (property_exists($state_data, $element))
				{
					$form_data[$element] = $state_data->$element;
				}
			}
		}

		// created_…, modified_…, publish_…, checkout_… needed on every tab
		$elementsToCheckAlways = array(
			'created_by',
			'modified_by',
			'publish_up',
			'publish_down',
			'checked_out_time',
			'checked_out',
		);

		foreach ($elementsToCheckAlways as $element)
		{
			if (property_exists($state_data, $element))
			{
				$form_data[$element] = $state_data->$element;
			}
		}

		if (array_key_exists('usergroups', $form_data) !== true)
		{
			$form_data['usergroups'] = array();
		}

		if (array_key_exists('selected_content', $form_data) !== true)
		{
			$form_data['selected_content'] = array();
		}

		// serialize selected_content
		$nl_content	= (array) $form_data['selected_content'];

		if (is_array($form_data['selected_content']))
		{
			$form_data['selected_content']	= implode(',', $form_data['selected_content']);
		}

		// only render new content, if selection from article list or template has changed
		if ($add_content)
		{
			$form_data = $this->processChangedContent($form_data, $add_content, $nl_content);
		}
		else
		{
			$form_data['selected_content'] = $state_data->selected_content;
			// if change of content is not confirmed don't change template_id
			$form_data['template_id']      = $state_data->template_id;
			$form_data['text_template_id'] = $state_data->text_template_id;
		}

		// convert form data to object to update state
		$data = ArrayHelper::toObject($form_data, 'stdClass', false);

		$app->setUserState('com_bwpostman.edit.newsletter.data', $data);
		$app->setUserState('com_bwpostman.edit.newsletter.changeTab', true);
	}

	/**
	 * Method to prepare the sending of a newsletter
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
		$nl_id = (int)$nl_id;

		// Access check
		if (!BwPostmanHelper::canSend($nl_id))
		{
			return false;
		}

		// Prepare the newsletter content
		$id = $this->addSendMailContent($nl_id);

		if ($id	=== false)
		{
			$ret_msg = Text::_('COM_BWPOSTMAN_NL_ERROR_CONTENT_PREPARING');
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
	 * @throws Exception
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
	 * If a newsletter shall be sent, then it will be inserted at table sendMailContent
	 * as a manner of archive and process method completely with content,
	 * subject & Co. in
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
		if (!$nl_id)
		{
			return false;
		}

		$newsletters_data = $this->getTable()->getNewsletterData((int)$nl_id);

		if (!$newsletters_data)
		{
			return false;
		}

		// Initialize the sendmailContent
		$sendMailContent = $this->getTable('sendmailcontent', 'BwPostmanTable');

		// Copy needed data from newsletters to sendmailContent
		$sendMailContent->nl_id       = $newsletters_data->id;
		$sendMailContent->from_name   = $newsletters_data->from_name;
		$sendMailContent->from_email  = $newsletters_data->from_email;
		$sendMailContent->subject     = $newsletters_data->subject;
		$sendMailContent->attachment  = $newsletters_data->attachment;
		$sendMailContent->cc_email    = null;
		$sendMailContent->bcc_email   = null;
		$sendMailContent->reply_email = $newsletters_data->reply_email;
		$sendMailContent->reply_name  = $newsletters_data->from_name;

		if (property_exists($newsletters_data, 'substitute_links'))
		{
			$sendMailContent->substitute_links = $newsletters_data->substitute_links;

			// support for plugin substitute links
			if ($sendMailContent->substitute_links == '1')
			{
				Factory::getApplication()->setUserState('com_bwpostman.edit.newsletter.data.substitutelinks', '1');
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
			if ($mode === 0)
			{
				$sendMailContent->body = $newsletters_data->text_version;
			}
			else
			{
				$sendMailContent->body = $newsletters_data->html_version;
			}

			// Set the mode (0=text,1=html)
			$sendMailContent->mode = $mode;

			// Store the data into the sendmailcontent-table
			// First run generates a new id, which will be used also for the second run.
			if (!$sendMailContent->store())
			{
				return false;
			}
		}

		$id = (int)$sendMailContent->id;

		return $id;
	}

	/**
	 * Method to push the recipients into a queue
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
			$ret_msg = Text::sprintf('COM_BWPOSTMAN_NL_ERROR_SENDING_TECHNICAL_REASON', '1');
			return false;
		}

		$tblSendmailQueue = $this->getTable('sendmailqueue', 'BwPostmanTable');

		switch ($recipients)
		{
			case "recipients": // Contain subscribers and joomla users

				$check_subscribers    = 0;
				$usergroups           = array();

				$associatedMailinglists = $this->getAssociatedMailinglists($nl_id, (int)$cam_id);

				if (!$associatedMailinglists)
				{
					$ret_msg = Text::_('COM_BWPOSTMAN_NL_ERROR_SENDING_NL_NO_LISTS');
					return false;
				}

				$this->getSubscriberChecks($associatedMailinglists, $check_subscribers, $check_allsubscribers, $usergroups);

				// Push all users of selected usergroups  to sendmailqueue if desired
				if (count($usergroups))
				{
					$params = ComponentHelper::getParams('com_bwpostman');
					if (!$tblSendmailQueue->pushJoomlaUser($content_id, $usergroups, $params->get('default_emailformat')))
					{
						$ret_msg = Text::sprintf('COM_BWPOSTMAN_NL_ERROR_SENDING_TECHNICAL_REASON', '3');
						return false;
					}
				}

				// Push all subscribers excluding archived to sendmailqueue if desired
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

					if (!$tblSendmailQueue->pushSubscribers($content_id, $status, $nl_id, $cam_id))
					{
						$ret_msg = Text::sprintf('COM_BWPOSTMAN_NL_ERROR_SENDING_TECHNICAL_REASON', '4');
						return false;
					}
				}
				break;

			case "testrecipients":
				$tblSubscribers = $this->getTable('subscribers', 'BwPostmanTable');
				$testrecipients = $tblSubscribers->loadTestrecipients();

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
				$ret_msg = Text::sprintf('COM_BWPOSTMAN_NL_ERROR_SENDING_TECHNICAL_REASON', '5');
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
		PluginHelper::importPlugin('bwpostman');
		Factory::getApplication()->triggerEvent('onBwPostmanGetAdditionalQueueWhere', array(&$query, true));

		$tblSendmailQueue = $this->getTable('sendmailqueue', 'BwPostmanTable');

		$result = $tblSendmailQueue->checkTrials($trial, $count);

		return $result;
	}

	/**
	 * Make partial send. Send only, say like 50 newsletters and the next 50 in a next call.
	 *
	 * @param integer   $mailsPerStep     number mails to send
	 * @param boolean   $fromComponent    do we come from component or from plugin?
	 * @param int       $mailsPerStepDone number of mails of current step sent
	 *
	 * @return int	0 -> queue is empty, 1 -> maximum reached, 2 -> fatal error
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function sendMailsFromQueue($mailsPerStep = 100, $fromComponent = true, $mailsPerStepDone = 0)
	{
		$this->logger->addEntry(new LogEntry('Model sendMailsFromQueue mails per Step: ' . $mailsPerStep, BwLogger::BW_INFO, 'send'));
		$this->sendmessage = '';

		try
		{
			$sendMailCounter = (int)$mailsPerStepDone;
			$counter = 0;

			while(1)
			{
				$ret = $this->sendMail((bool)$fromComponent);
				echo $this->sendmessage;
				$this->sendmessage = '';

				if ($ret === 0)
				{                              // Queue is empty!
					return 0;
				}

				$sendMailCounter++;
				if ($sendMailCounter >= (int)$mailsPerStep)
				{     // Maximum is reached.
					return 1;
				}

				$counter++;
				if ($counter >= 10)
				{     // package for ajax call
					return $sendMailCounter;
				}
			}

		}
		catch (Throwable $e) // PHP-Version >= 7
		{
			return 2;
		}
		catch (Exception $e) // PHP-Version < 7
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
		$renderer           = new contentRenderer();
		$app                = Factory::getApplication();
		$itemid_unsubscribe = BwPostmanSubscriberHelper::getMenuItemid('register');
		$itemid_edit        = BwPostmanSubscriberHelper::getMenuItemid('edit');
		$res                = false;
		$queueTableName     = '#__bwpostman_sendmailqueue';

		// getting object for queue and content
		$tblSendMailQueue   = $this->getTable('sendmailqueue', 'BwPostmanTable');
		$tblSendMailContent = $this->getTable('sendmailcontent', 'BwPostmanTable');

		PluginHelper::importPlugin('bwpostman');

		// trigger BwTimeControl event, if we come not from component
		// needed for changing table objects for queue and content, show/hide messages, ...
		if (!$fromComponent)
		{
			$app->triggerEvent('onBwPostmanBeforeNewsletterSend', array(&$queueTableName, &$tblSendMailQueue, &$tblSendMailContent));
		}

		// Get first entry from sendmailqueue
		// Nothing has been returned, so the queue should be empty
		if (!$tblSendMailQueue->pop(2, $fromComponent))
		{
			return 0;
		}

		// rewrite some property names od sendMailQueue if needed
		if (property_exists($tblSendMailQueue, 'tc_content_id'))
		{
			$tblSendMailQueue->content_id = (int)$tblSendMailQueue->tc_content_id;
		}

		if (property_exists($tblSendMailQueue, 'email'))
		{
			$tblSendMailQueue->recipient = $tblSendMailQueue->email;

			if ($this->demo_mode)
			{
				$tblSendMailQueue->recipient = $this->dummy_recipient;
			}
		}

		$app->setUserState('com_bwpostman.newsletter.send.mode', $tblSendMailQueue->mode);
		$app->setUserState('bwtimecontrol.mode', $tblSendMailQueue->mode);

		// Get Data from sendmailcontent, set attachment path
		// @ToDo, store data in this class to prevent from loading every time a mail will be sent
		$tblSendMailContent->load($tblSendMailQueue->content_id);

		// Convert attachment string or JSON to array, if present
		$attachments = BwPostmanNewsletterHelper::decodeAttachments($tblSendMailContent->attachment);

		// Add base path to attachments
		$fullAttachments = array();

		foreach ($attachments as $attachment)
		{
			$fullAttachments[] = JPATH_SITE . '/' .$attachment['single_attachment'];
		}

		$tblSendMailContent->attachment = $fullAttachments;

		if (property_exists($tblSendMailContent, 'email'))
		{
			$tblSendMailContent->content_id	= $tblSendMailContent->id;
		}

		// check if subscriber is archived, check if testrecipient
		$editlink = '';

		if ($tblSendMailQueue->subscriber_id)
		{
			$subsTable       = $this->getTable('Subscribers', 'BwPostmanTable');
			$recipients_data = $subsTable->getSubscriberNewsletterData($tblSendMailQueue->subscriber_id);

			// if subscriber is archived, do nothing
			if ($recipients_data->archive_flag)
			{
				return 1;
			}

			$isTestrecipient = false;

			if ((int)$recipients_data->status === 9)
			{
				$isTestrecipient = true;
			}

			if (property_exists($recipients_data, 'editlink') && !$isTestrecipient)
			{ // testrecipient has no edit link
				$editlink = $recipients_data->editlink;
			}
		}

		$this->logger->addEntry(new LogEntry('Model sendMail ItemId edit: ' . $itemid_edit, BwLogger::BW_DEBUG, 'send'));
		$this->logger->addEntry(new LogEntry('Model sendMail ItemId unsubscribe: ' . $itemid_unsubscribe, BwLogger::BW_DEBUG, 'send'));
		$this->logger->addEntry(new LogEntry('Model sendMail ItemId editlink: ' . $editlink, BwLogger::BW_DEBUG, 'send'));

		// Replace the links to provide the correct preview
		$body = $tblSendMailContent->body;
		$renderer->replaceAllFooterLinks($body, (int)$tblSendMailQueue->subscriber_id, (int)$tblSendMailQueue->mode);
		BwPostmanHelper::replaceLinks($body);
		$renderer->replaceContentPlaceholders($body, $tblSendMailQueue, $itemid_edit, $itemid_unsubscribe, $editlink, (int)$tblSendMailContent->substitute_links);

		// Fire the onBwPostmanPersonalize event.
		if(PluginHelper::isEnabled('bwpostman', 'personalize')
			&& !$app->triggerEvent('onBwPostmanPersonalize', array('com_bwpostman.send', &$body, &$tblSendMailQueue->subscriber_id)))
		{
			$error_msg_plugin = Text::_('COM_BWPOSTMAN_PERSONALIZE_ERROR');
			$app->enqueueMessage($error_msg_plugin, 'error');
			$this->logger->addEntry(new LogEntry($error_msg_plugin, BwLogger::BW_ERROR, 'personalize'));

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
			$this->sendmessage .= "\n<br>{$tblSendMailQueue->recipient} (" .
				Text::_('COM_BWPOSTMAN_NL_ERROR_SENDING_TRIAL') . ($tblSendMailQueue->trial + 1) . ") ... ";
		}

		$unsubscribe_url = $renderer->generateUnsubscribeUrl($itemid_unsubscribe, $tblSendMailQueue->recipient, $editlink);

		// Get a JMail instance
		$mailer = Factory::getMailer();
		$mailer->SMTPDebug = true;

		$sender    = array();
		$sender[0] = MailHelper::cleanAddress($tblSendMailContent->from_email);
		$sender[1] = $tblSendMailContent->from_name;

		if ($this->demo_mode)
		{
			$sender[0]                       = MailHelper::cleanAddress($this->dummy_sender);
			$tblSendMailContent->reply_email = MailHelper::cleanAddress($this->dummy_sender);
		}

		try
		{
			$mailer->setSender($sender);

			if ($unsubscribe_url !== '')
			{
				$mailer->addCustomHeader("List-Unsubscribe", $unsubscribe_url);
			}

			$mailer->addReplyTo(MailHelper::cleanAddress($tblSendMailContent->reply_email), $tblSendMailContent->reply_name);
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

			if (!$this->arise_queue)
			{
				$this->logger->addEntry(new LogEntry('Before sending', BwLogger::BW_INFO, 'send'));
//				Use the following with care! Complete mails with body are written to log…
//				$this->logger->addEntry(new LogEntry('Mailer data: ' . print_r($mailer, true), BwLogger::BW_DEVELOPMENT, 'send'));

				$res = $mailer->Send();
				$this->logger->addEntry(new LogEntry(sprintf('Sending result: %s', $res), BwLogger::BW_INFO, 'send'));
			}
		}
		catch (UnexpectedValueException $exception)
		{
			$message    = $exception->getMessage();
			$res        = false;

			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'send newsletter'));
		}
		catch (InvalidArgumentException $exception)
		{
			$message    = $exception->getMessage();
			$res        = false;

			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'send newsletter'));
		}
		catch (RuntimeException $exception)
		{
			$message    = $exception->getMessage();
			$res        = false;

			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'send newsletter'));
		}

		if ($res === true)
		{
			if ($fromComponent)
			{
				$this->sendmessage .= Text::_('COM_BWPOSTMAN_NL_SENT_SUCCESSFULLY');
			}
		}
		else
		{
			$app->enqueueMessage(sprintf('Error while sending: $s'), $res);
			// Sendmail was not successful, we need to add the recipient to the queue again.
			if ($fromComponent)
			{
				// show message only wanted if sending newsletters from component backend directly, not in time controlled sending
				$this->sendmessage .= Text::_('COM_BWPOSTMAN_NL_ERROR_SENDING');
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
	 * Method to process test mode with one or more of the following settings
	 * - dummy sender
	 * - dummy recipients
	 * - arise queue (to check behavior of queue tab)
	 * - demo mode
	 *
	 * @return void
	 *
	 * @since 2.0.0
	 */
	private function processTestMode()
	{
		$test_plugin = PluginHelper::getPlugin('system', 'bwtestmode');

		if ($test_plugin)
		{
			$params = json_decode($test_plugin->params);

			$this->demo_mode       = $params->demo_mode_option;
			$this->dummy_sender    = $params->sender_address_option;
			$this->dummy_recipient = $params->recipient_address_option;
			$this->arise_queue     = $params->arise_queue_option;
		}
	}

	/**
	 * Method to preset HTML-Template for old newsletters

	 * @param                 $item
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since 2.3.0
	 */
	private function presetOldHTMLTemplate(&$item)
	{
		$html_tpl = null;
		$db       = $this->_db;

		if ($item->id == 0)
		{
			$item->template_id = $this->getTable('Templates')->getStandardTpl('html');
		}
		elseif ($item->template_id == 0)
		{
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from($db->quoteName('#__bwpostman_templates'));
			$query->where($db->quoteName('id') . ' = ' . -1);

			try
			{
				$db->setQuery($query);

				$html_tpl = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			if (is_null($html_tpl))
			{
				$html_tpl = $this->getTable('Templates')->getStandardTpl('html');
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
	 * @throws Exception
	 *
	 * @since 2.3.0
	 */
	private function presetOldTextTemplate(&$item)
	{
		$text_tpl = null;
		$db       = $this->_db;

		if ($item->id == 0)
		{
			$item->text_template_id = $this->getTable('Templates')->getStandardTpl('text');
		}
		elseif ($item->text_template_id == 0)
		{
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from($db->quoteName('#__bwpostman_templates'));
			$query->where($db->quoteName('id') . ' = ' . -2);

			try
			{
				$db->setQuery($query);

				$text_tpl = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			if (is_null($text_tpl))
			{
				$text_tpl = $this->getTable('Templates')->getStandardTpl('text');
			}

			$item->text_template_id = $text_tpl;
		}
	}

	/**
	 * Method to preprocess content of HTML version of the newsletter
	 *
	 * @param object $newsletters_data
	 *
	 * @return mixed
	 *
	 * @throws Exception
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
	 * @throws Exception
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
	 * @throws Exception
	 *
	 * @since 2.3.0
	 */
	private function getAssociatedMailinglists($nl_id, $cam_id)
	{
		if ($cam_id !== -1)
		{
			// Check if there are assigned mailinglists or usergroups
			$crossTable = $this->getTable('Campaigns_Mailinglists');
			$mailinglists = $crossTable->getAssociatedMailinglistsByCampaign((int) $cam_id);
		}
		else
		{
			// Check if there are assigned mailinglists or usergroups of the campaign
			$crossTable = $this->getTable('Newsletters_Mailinglists');
			$mailinglists = $crossTable->getAssociatedMailinglistsByNewsletter((int) $nl_id);
		}

		return ArrayHelper::toInteger($mailinglists);
	}


	/**
	 * Method to get the needed subscriber checks
	 *
	 * @param array    $mailinglists
	 * @param boolean  $check_subscribers
	 * @param array    $usergroups
	 *
	 * @return string|boolean
	 *
	 * @since 2.3.0
	 */
	private function getSubscriberChecks($mailinglists, &$check_subscribers, &$usergroups)
	{
		foreach ($mailinglists as $mailinglist)
		{
			// Mailinglists
			if ((int) $mailinglist > 0)
			{
				$check_subscribers = 1;
			}

			// Usergroups
			if ((int) $mailinglist < 0)
			{
				$usergroups[] = -(int) $mailinglist;
			}
		}

		return true;
	}

	/**
	 * Method to adjust newsletter content if content or template has changed
	 *
	 * @param       $form_data
	 * @param       $add_content
	 * @param array $nl_content
	 *
	 * @return array
	 *
	 * @throws Exception
	 *
	 * @since 3.0.0
	 */
	private function processChangedContent($form_data, $add_content, $nl_content)
	{
		$jinput            = Factory::getApplication()->input;
		$sel_content       = $jinput->get('selected_content_old', '', 'string');
		$old_template      = $jinput->get('template_id_old', '', 'string');
		$old_text_template = $jinput->get('text_template_id_old', '', 'string');
		$nl_content        = ArrayHelper::toInteger($nl_content);

		if (($sel_content != $form_data['selected_content'])
			|| ($old_template != $form_data['template_id'])
			|| ($old_text_template != $form_data['text_template_id']))
		{
			if ($add_content == '-1' && (count($nl_content) === 0))
			{
				$nl_content = array(-1);
			}

			// only render new content, if selection from article list or template has changed
			$renderer = new contentRenderer();
			$content  = $renderer->getContent($nl_content, (int)$form_data['template_id'], (int)$form_data['text_template_id']);

			$form_data['html_version'] = $content['html_version'];
			$form_data['text_version'] = $content['text_version'];

			// add intro to form data
			if ($sel_content != $form_data['selected_content'] || $old_template != $form_data['template_id'])
			{
				$tpl = $renderer->getTemplate($form_data['template_id']);

				if (is_object($tpl) && key_exists('intro_headline', $tpl->intro))
				{
					$form_data['intro_headline'] = $tpl->intro['intro_headline'];
				}

				if (is_object($tpl) && key_exists('intro_text', $tpl->intro))
				{
					$form_data['intro_text'] = $tpl->intro['intro_text'];
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

			$form_data['template_id_old'] = $form_data['template_id'];
			$form_data['text_template_id_old'] = $form_data['text_template_id'];
		}

		return $form_data;
	}

	/**
	 * Method to get the differences between form keys and state properties
	 * Primary for development purpose
	 *
	 * @param $state_data
	 * @param $form_data
	 *
	 * @return array
	 *
	 * @since 3.0.0
	 */
	private function getDiffDataKeys($state_data, $form_data)
	{
		$diffKeys = array_diff_key((array) $state_data, $form_data);

		unset($diffKeys["\u0000*\u0000_errors"]);

		return $diffKeys;
	}

	/**
	 * Method to get the differences between form data and state data
	 * Primary for development purpose
	 *
	 * @param $state_data
	 * @param $form_data
	 *
	 * @return array
	 *
	 * @since 3.0.0
	 */
	private function getDiffDataValues($state_data, $form_data)
	{
		$diffData = array_diff((array) $state_data, $form_data);

		return $diffData;
	}
}
