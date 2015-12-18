<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman subscribers controller for backend.
 *
 * @version 1.3.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2015 Boldt Webservice <forum@boldt-webservice.de>
 * @support http://www.boldt-webservice.de/forum/bwpostman.html
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

// Import CONTROLLER object class
jimport('joomla.application.component.controlleradmin');

// Require helper class
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');

/**
 * BwPostman Subscribers Controller
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Subscribers
 */
class BwPostmanControllerSubscribers extends JControllerAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.0.4
	 */
	protected $text_prefix = 'COM_BWPOSTMAN_SUBS';

	/**
	 * Constructor
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Register Extra tasks
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('export', 'export');
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name	The name of the model.
	 * @param	string	$prefix	The prefix for the PHP class name.
	 *
	 * @return	JModel
	 * @since	1.0.1
	 */
	public function getModel($name = 'Subscriber', $prefix = 'BwPostmanModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Method to validate one or more email addresses
	 * --> If the validation failed, add INVALID_ to the name
	 * --> If the validation was succesful, set status = 1 and set confirmation_date and confirmed_by
	 *
	 * @access	public
	 * @return	load Validation Result layout
	 */
	public function validateEmailAdresses()
	{
		$jinput	= JFactory::getApplication()->input;

		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		$cid = $jinput->get('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);

		$model = $this->getModel('subscriber');

		// Leider bietet Joomla keine Möglichkeit die anzuzeigende Seite Stück für Stück
		// auszugeben. Stattdessen wird ein Buffer verwendet, welcher erst ganz zum Schluss
		// ausgegeben wird. -> Quick and Dirty Selbst sofort den Inhalt ausgeben, durch
		// umgehen des PHP Buffers und sofortiges Flushen nach echos.
		// Anschließend wird per javascript ein redirect ausgelöst.
		echo JText::_('COM_BWPOSTMAN_SUB_VALIDATION_PROCESS');
		ob_flush();
		flush();
		$validation_res = $model->validate_mail($cid, true);

		//Get session object
		$session = JFactory::getSession();
		$session->set('validation_res', $validation_res);

		$url = JURI::base().'index.php?option=com_bwpostman&view=subscriber&layout=validation';
		echo '<script type="text/javascript">'."\n"
		.'<!--'."\n"
		.'window.location = "'.$url.'"'."\n"
		.'//-->'."\n"
		.'</script>'."\n";
		exit();
		exit();
	}

	/**
	 * Method to finish the validation
	 * --> all data which we store into the session will be cleared
	 *
	 * @param	public
	 * @return 	Redirect
	 */
	public function finishValidation()
	{
		$jinput	= JFactory::getApplication()->input;

		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		$app			= JFactory::getApplication();
		$session		= JFactory::getSession();
		$validation_res	= $session->get('validation_res');

		if(isset($validation_res) && is_array($validation_res)){
			$session->clear('validation_res');
		}
		$msg = JText::_('COM_BWPOSTMAN_SUB_VALIDATION_FINISHED');
		$link = JRoute::_('index.php?option=com_bwpostman&controller=subscribers&layout=unconfirmed', false);

		$this->setRedirect($link, $msg);
	}

	/**
	 * Method to call the start layout for the import process
	 *
	 * @access	public
	 */
	public function importSubscribers()
	{
		$jinput	= JFactory::getApplication()->input;
		$user	= JFactory::getUser();
		$app	= JFactory::getApplication();

		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		// Which tab are we in?
		$layout = $jinput->get('tab', 'confirmed');

		// Access check.
		if (!$user->authorise('core.create', 'com_bwpostman')) {
			$msg = $app->enqueueMessage(JText::_('COM_BWPOSTMAN_SUB_ERROR_IMPORT_NO_PERMISSION'), 'warning');
			$link = JRoute::_('index.php?option=com_bwpostman&view=subscribers&layout='.$layout, false);
			$this->setRedirect($link);
			return false;
		}
		$jinput->set('hidemainmenu', 1);
		$jinput->set('view', 'subscriber');
		$jinput->set('layout', 'import');
		$link = JRoute::_('index.php?option=com_bwpostman&view=subscriber&layout=import', false);
		$this->setRedirect($link);
	}

	/**
	 * Method for uploading the import file and to prepare the import process
	 *
	 * @access	public
	 * @return	Redirect
	 */
	public function prepareImport()
	{
		$jinput	= JFactory::getApplication()->input;
		$app	= JFactory::getApplication();

		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		// Retrieve file details from uploaded file, sent from upload form
		$file = $jinput->files->get('importfile');

		// Import filesystem libraries.
		jimport('joomla.filesystem.file');

		// Clean up filename to get rid of strange characters like spaces etc
		$filename = JFile::makeSafe($file['name']);

		// Retrieve the delimiter and the caption field from the upload form
		$post	= $jinput->getArray(
					array(
						'fileformat' => 'string',
						'delimiter' => 'string',
						'enclosure' => 'string',
						'caption' => 'string',
						'status1' => 'string',
						'status0' => 'string',
						'status9' => 'string',
						'archive0' => 'string',
						'archive1' => 'string',
						'task' => 'string',
						'controller' => 'string',
						'option' => 'string'
				));

		$fileformat	= $post['fileformat'];

		if ($fileformat == 'csv') {
			$delimiter = $post['delimiter'];
			$enclosure = $post['enclosure'];
			if (isset($post['caption'])) {
				$caption = true;
			}
			else {
				$caption = false;
			}
		}

		// Set up the source and destination of the file
		$src	= $file['tmp_name'];

		$ext	= JFile::getExt($filename);
		$dest	= JPATH_SITE.'/images/tmp_bwpostman_subscriber_import.'.$ext;

		// Store the post data into the session
		// If there occured an error we will receive the data from the session
		// We also need the data for the next import-step
		if ($fileformat == 'csv') {
			$import_general_data = array('fileformat' => $fileformat, 'delimiter' => $delimiter, 'enclosure' => $enclosure, 'caption' => $caption, 'filename' => $filename, 'dest' => $dest, 'ext' => $ext);
		}
		else {
			$import_general_data = array('fileformat' => $fileformat, 'filename' => $filename, 'dest' => $dest, 'ext' => $ext);
		}

		//Get session object
		$session = JFactory::getSession();
		$session->set('import_general_data', $import_general_data);

		// If the file isn't okay, redirect to import.php
		if ($file['error'] > 0) {

			//http://de.php.net/features.file-upload.errors
			$msg = JText::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_UPLOAD');

			switch ($file['error']) {
				case '1':
				case '2': $msg .= JText::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_UPLOAD_SIZE');
				break;
				case '3': $msg .= JText::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_UPLOAD_PART');
				break;
				case '4': $msg .= JText::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_UPLOAD_NO_FILE');
				break;
			}

			$link = JRoute::_('index.php?option=com_bwpostman&view=subscriber&layout=import&task=importSubscribers', false);
			$this->setRedirect($link, $msg, 'error');
		}
		else { // The file is okay
			// Check if the file has the right extension, we need csv or xml
			// --> if the extension is wrong, redirect to import.php
			if ((strtolower(JFile::getExt($filename)) !== 'csv') && (strtolower(JFile::getExt($filename)) !== 'xml')) {
				$msg = JText::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_UPLOAD_TYPE');
				$link = JRoute::_('index.php?option=com_bwpostman&view=subscriber&layout=import&task=importSubscribers', false);
				$this->setRedirect($link, $msg, 'error');
			}
			// Check if the extension is identical to the selected fileformat
			// --> if not, redirect to import.php
			elseif (((strtolower(JFile::getExt($filename)) == 'csv') && ($fileformat != 'csv')) || ((strtolower(JFile::getExt($filename)) == 'xml') && ($fileformat != 'xml'))) {
				$msg = JText::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_FILE_FORMAT');
				$link = JRoute::_('index.php?option=com_bwpostman&view=subscriber&layout=import&task=importSubscribers', false);
				$this->setRedirect($link, $msg, 'error');
			}
			else { // Everything is fine
				if (false === JFile::upload($src, $dest)) {
					$msg = JText::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_UPLOAD');
					$link = JRoute::_('index.php?option=com_bwpostman&view=subscriber&layout=import&task=importSubscribers', false);
					$this->setRedirect($link, $msg, 'error');
				}
				else {
					if (false === $fh = fopen($dest, 'r')) { // File cannot be opened
						$app->enqueueMessage(JText::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_UNABLE_TO_OPEN_FILE'), 'warning');
						return false;
					}
					else {
						if ($ext == 'csv') { // CSV file
							$delimiter = stripcslashes($delimiter);
							if (($data = fgetcsv ($fh, 1000, $delimiter)) !== FALSE) {
								$num			= count($data);
								$import_fields	= array();

								if ($caption) {
									for ($i=0; $i < count($data); $i++){
										$import_fields[] 	= JHTML::_('select.option', "column_$i", JText::_('COM_BWPOSTMAN_SUB_IMPORT_COLUMN')."$i ({$data[$i]})");
									}
								}
								else {
									for ($i=0; $i < count($data); $i++){
										$import_fields[] 	= JHTML::_('select.option', 'column_'.$i, JText::_('COM_BWPOSTMAN_SUB_IMPORT_COLUMN').$i);
									}
								}

								//Save the import_fields from the csv-file into the session
								$session->set('import_fields', $import_fields);

								fclose($fh);
							}
							else { // File cannot be read
								$app->enqueueMessage(JText::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_UNABLE_TO_READ_FILE') . ": '$dest'", 'warning');
							}
						}
						else { // XML file
							// Parse the XML
							$parser	= JFactory::getXML($dest);

							// Get the name of the paling element
							echo "NAME: '{$parser->name()}' <br>\n";

							if ($parser->name() != "subscribers"){
								// TODO: es ist kein bwpostman xml file! koennen trotzdem fortfahren, falls geeignete felder drin sind
							}

							// Get all fields from the xml file for listing and selecting by the user
							$fields				= array_keys(get_object_vars($parser->subscriber));
							$xml_fields_keys	= array_keys(get_object_vars($parser->subscriber));
							$import_fields		= array();

							for ($i=0; $i < count($xml_fields_keys); $i++){
								$import_fields[] 	= JHTML::_('select.option', "$xml_fields_keys[$i]", JText::_('COM_BWPOSTMAN_SUB_IMPORT_FIELD')."$i ({$xml_fields_keys[$i]})");
							}
							$session->set('import_fields', $import_fields);
						}
					}
					$link = JRoute::_('index.php?option=com_bwpostman&view=subscriber&layout=import1', false);
					$this->setRedirect($link);
				}
			}
		}
	}

	/**
	 * Method to import subscriber data
	 *
	 * @access	public
	 * @return	Redirect
	 */
	public function import()
	{
		$jinput	= JFactory::getApplication()->input;

		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		$app	= JFactory::getApplication();
		$post	= $jinput->getArray(
					array(
						'db_fields' => 'array',
						'emailformat' => 'string',
						'import_fields' => 'array',
						'jform' => 'array',
						'task' => 'string',
						'controller' => 'string',
						'option' => 'string'
					));

		$model			= $this->getModel('subscriber');
		$import_result	= array();
		$subscriber		= new stdClass();
		$import_err     = '';
		$import_warn    = '';
		$maildata       = '';

		$model->import($post, $import_err, $import_warn, $maildata);

		// Send emails to subscribers if they weren't confirmed
		if ($maildata) {
			$itemid = $model->getItemid();

			for ($i = 0;$i < count($maildata);$i++){
				$subscriber->name		= $maildata[$i]->name;
				$subscriber->firstname	= $maildata[$i]->firstname;
				$subscriber->email		= $maildata[$i]->email;
				$subscriber->activation	= $maildata[$i]->activation;

				// Send registration confirmation mail
				$res = $this->_sendMail($subscriber, $itemid);

				if ($res === false) { // Store the mailing errors into the result array
					$mail_err['row'] 	= $maildata[$i]->row;
					$mail_err['email'] 	= $subscriber->email;
					$mail_err['msg'] 	= $res->message;

					$import_result['mail_err'][] = $mail_err;
				}
			}
		}

		// Store the import errors into the result array
		if ($import_err) {
			$import_result['import_err'] = $import_err;
		}

		// Store the import warnings into the result array
		if ($import_warn) {
			$import_result['import_warn'] = $import_warn;
		}
		//Get session object and store the result-array into the session
		$session = JFactory::getSession();
		$session->set('import_result', $import_result);

		$link = JRoute::_('index.php?option=com_bwpostman&view=subscriber&layout=import2', false);
		$this->setRedirect($link);
	}

	/**
	 * Method to finish or cancel the import process
	 * --> all session data which we needed for the import will be cleared
	 *
	 * @param	public
	 * @return 	Redirect
	 */
	public function finishImport()
	{
		$jinput	= JFactory::getApplication()->input;

		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		$app		= JFactory::getApplication();
		$session	= JFactory::getSession();
		$finished	= false;

		$import_general_data = $session->get('import_general_data');

		if(isset($import_general_data) && is_array($import_general_data)){
			$dest = $import_general_data['dest'];
			jimport('joomla.filesystem.file');
			if (JFile::exists($dest)) {
				JFile::delete($dest);
			}
			$session->clear('import_general_data');
		}

		$import_fields = $session->get('import_fields');
		if(isset($import_fields) && is_array($import_fields)){
			$session->clear('import_fields');
		}

		$import_result = $session->get('import_result');
		if(isset($import_result) && is_array($import_result)){
			$session->clear('import_result');
			$finished = true;
		}

		if (!$finished) {
			$msg = JText::_('COM_BWPOSTMAN_OPERATION_CANCELLED');
		}
		else {
			$msg = JText::_('COM_BWPOSTMAN_SUB_IMPORT_FINISHED');
		}
		$link = JRoute::_('index.php?option=com_bwpostman&controller=subscribers', false);

		$this->setRedirect($link, $msg);
	}

	/**
	 * Method to call the layout for the export process
	 *
	 * @access	public
	 */
	public function exportSubscribers()
	{
		$jinput	= JFactory::getApplication()->input;
		$user	= JFactory::getUser();
		$app	= JFactory::getApplication();

		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		// Which tab are we in?
		$layout = $jinput->get('tab', 'confirmed');

		// Access check.
		if (!$user->authorise('core.edit', 'com_bwpostman')) {
			$msg = $app->enqueueMessage(JText::_('COM_BWPOSTMAN_SUB_ERROR_EXPORT_NO_PERMISSION'), 'warning');
			$link = JRoute::_('index.php?option=com_bwpostman&controller=subscribers&layout='.$layout, false);
			$this->setRedirect($link);
			return false;
		}

		$jinput->set('hidemainmenu', 1);
		$jinput->set('view', 'subscriber');
		$jinput->set('layout', 'export');
		$link = JRoute::_('index.php?option=com_bwpostman&view=subscriber&layout=export', false);
		$this->setRedirect($link);
	}

	/**
	 * Method to call the view for the export process
	 * --> we will take the raw-view which calls the export-function in the model
	 *
	 * @access	public
	 */
	public function export()
	{
		$jinput	= JFactory::getApplication()->input;

		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		$document	= JFactory::getDocument();
		$app		= JFactory::getApplication();
		$post		= $jinput->getArray(
						array(
							'fileformat' => 'string',
							'delimiter' => 'string',
							'enclosure' => 'string',
							'status1' => 'string',
							'status0' => 'string',
							'status9' => 'string',
							'archive0' => 'string',
							'archive1' => 'string',
							'export_fields' => 'array',
							'task' => 'string',
							'controller' => 'string',
							'option' => 'string'
						));

		$app->setUserState('com_bwpostman.subscribers.export.data', $post);
		$jinput->set('view', 'subscriber');

		$document->setType('raw');
		$link = JRoute::_('index.php?option=com_bwpostman&view=subscriber&layout=export&format=raw', false);
		$this->setRedirect($link);

		parent::display();
	}

	/**
	 * Method to send an email
	 *
	 * @access	private
	 * @param 	object Subscriber
	 * @param	int Menu item ID
	 * @return 	boolean True on success | error object
	 */
	protected function _sendMail(&$subscriber, $itemid = null)
	{
		$app	= JFactory::getApplication();
		$db		= JFactory::getDBO();
		$params = JComponentHelper::getParams('com_bwpostman');

		$name 		= $subscriber->name;
		$firstname 	= $subscriber->firstname;
		if ($firstname != '') $name = $firstname . ' ' . $name;
		$sitename	= $app->getCfg('sitename');
		$siteURL	= JURI::root();

		$activation = $subscriber->activation;
		$subject 	= JText::sprintf('COM_BWPOSTMAN_SUB_SEND_REGISTRATION_SUBJECT', $sitename);
		if (is_null($itemid)) {
			$message = JText::sprintf('COM_BWPOSTMAN_SUB_SEND_REGISTRATION_MSG', $name, $siteURL, $siteURL."index.php?option=com_bwpostman&view=register&task=activate&subscriber={$subscriber->activation}");
		}
		else {
			$message = JText::sprintf('COM_BWPOSTMAN_SUB_SEND_REGISTRATION_MSG', $name, $siteURL, $siteURL."index.php?option=com_bwpostman&Itemid={$itemid}&view=register&task=activate&subscriber={$subscriber->activation}");
		}
		$subject = html_entity_decode($subject, ENT_QUOTES);
		$message = html_entity_decode($message, ENT_QUOTES);

		// Get a JMail instance
		$mailer		= JFactory::getMailer();
		$sender		= array();
		$reply		= array();

		$sender[0]	= $params->get('default_from_email');
		$sender[1]	= $params->get('default_from_name');

		$reply[0]	= $params->get('default_from_email');
		$reply[1]	= $params->get('default_from_name');

		$mailer->setSender($sender);
		$mailer->addReplyTo($reply);
		$mailer->addRecipient($subscriber->email);
		$mailer->setSubject($subject);
		$mailer->setBody($message);

		$res = $mailer->Send();

		return $res;
	}

	/**
	 * Method to set the tab state while changing tabs, used for building the appropriate toolbar
	 *
	 * @access	public
	 */
	public function changeTab()
	{
		$app		= JFactory::getApplication();
		$jinput		= JFactory::getApplication()->input;
		$tab		= $jinput->get('tab', 'confirmed');

		$app->setUserState('com_bwpostman.subscribers.tab', $tab);

		$link = JRoute::_('index.php?option=com_bwpostman&view=subscribers', false);

		$this->setRedirect($link);
	}
}
