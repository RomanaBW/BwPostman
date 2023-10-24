<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman subscribers controller for backend.
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

namespace BoldtWebservice\Component\BwPostman\Administrator\Controller;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanSubscriberHelper;
use Exception;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Filesystem\File;
use SimpleXMLElement;
use stdClass;

/**
 * BwPostman Subscribers Controller
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Subscribers
 *
 * @since       0.9.1
 */
class SubscribersController extends AdminController
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 *
	 * @since	1.0.4
	 */
	protected $text_prefix = 'COM_BWPOSTMAN_SUBS';

	/**
	 * property to hold permissions as array
	 *
	 * @var array $permissions
	 *
	 * @since       2.0.0
	 */
	public $permissions;

	/**
	 * Constructor
	 *
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 *
	 * @see		JController
	 */
	public function __construct($config = array())
	{
		$this->permissions = Factory::getApplication()->getUserState('com_bwpm.permissions', []);

		$this->factory = Factory::getApplication()->bootComponent('com_bwpostman')->getMVCFactory();

		parent::__construct($config, $this->factory);

		// Register Extra tasks
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->registerTask('export', 'export');
	}

	/**
	 * Display
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link FilterInput::clean()}.
	 *
	 * @return  SubscribersController		This object to support chaining.
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function display($cachable = false, $urlparams = array()): SubscribersController
	{
		if (!$this->permissions['view']['subscriber'])
		{
			$this->setRedirect(Route::_('index.php?option=com_bwpostman', false));
			$this->redirect();
			return $this;
		}

		parent::display();

		return $this;
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name   	The name of the model.
	 * @param	string	$prefix 	The prefix for the PHP class name.
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @return	BaseDatabaseModel

	 * @since	1.0.1
	 */
	public function getModel($name = 'Subscriber', $prefix = 'Administrator', $config = array('ignore_request' => true)): BaseDatabaseModel
	{
		return $this->factory->createModel($name, $prefix, $config);
	}

	/**
	 * Method to call the start layout for the import process
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function importSubscribers(): bool
	{
		$jinput = Factory::getApplication()->input;
		$user   = Factory::getApplication()->getIdentity();

		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		// Which tab are we in?
		$layout = $jinput->get('tab', 'confirmed');

		// Access check.
		if (!$user->authorise('bwpm.create', 'com_bwpostman') && !$user->authorise('bwpm.subscriber.create', 'com_bwpostman.subscriber'))
		{
			$link = Route::_('index.php?option=com_bwpostman&view=subscribers&layout=' . $layout, false);
			$this->setRedirect($link);
			return false;
		}

		$jinput->set('hidemainmenu', 1);
		$jinput->set('view', 'subscriber');
		$jinput->set('layout', 'import');
		$link = Route::_('index.php?option=com_bwpostman&view=subscriber&layout=import', false);
		$this->setRedirect($link);
		return true;
	}

	/**
	 * Method for uploading the import file and to prepare the import process
	 *
	 * @return	boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function prepareImport(): bool
	{
		$app    = Factory::getApplication();
		$jinput = $app->input;

		$delimiter  = '';
		$enclosure  = '"';
		$caption    = false;

		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		// Retrieve file details from uploaded file, sent from upload form
		$file = $jinput->files->get('importfile');

		// Clean up filename to get rid of strange characters like spaces etc
		$filename = File::makeSafe($file['name']);

		// Retrieve the delimiter and the caption field from the upload form
		$post = $jinput->getArray(
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
			)
		);

		$fileformat	= $post['fileformat'];

		if ($fileformat == 'csv')
		{
			$delimiter = $post['delimiter'];
			$enclosure = $post['enclosure'];

			if (isset($post['caption']))
			{
				$caption = true;
			}
			else
			{
				$caption = false;
			}
		}

		// Set up the source and destination of the file
		$src = $file['tmp_name'];
		$ext = File::getExt($filename);

		$m_params = ComponentHelper::getParams('com_media');
		$dest     = JPATH_ROOT . '/' . $m_params->get('image_path', 'images') . '/tmp_bwpostman_subscriber_import.' . $ext;

		// Store the post data into the session
		// If there occurred an error we will receive the data from the session
		// We also need the data for the next import-step
		if ($fileformat == 'csv')
		{
			$import_general_data = array(
				'fileformat' => $fileformat,
				'delimiter' => $delimiter,
				'enclosure' => $enclosure,
				'caption' => $caption,
				'filename' => $filename,
				'dest' => $dest,
				'ext' => $ext
			);
		}
		else
		{
			$import_general_data = array('fileformat' => $fileformat, 'filename' => $filename, 'dest' => $dest, 'ext' => $ext);
		}

		//Get session object
		$session = $app->getSession();
		$session->set('import_general_data', $import_general_data);

		// If the file isn't okay, redirect to import.php
		if ($file['error'] > 0)
		{
			//http://de.php.net/features.file-upload.errors
			$msg = Text::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_UPLOAD');

			switch ($file['error'])
			{
				case '1':
				case '2':
					$msg .= Text::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_UPLOAD_SIZE');
					break;
				case '3':
					$msg .= Text::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_UPLOAD_PART');
					break;
				case '4':
					$msg .= Text::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_UPLOAD_NO_FILE');
					break;
			}

			$link = Route::_('index.php?option=com_bwpostman&view=subscriber&layout=import&task=importSubscribers', false);
			$this->setRedirect($link, $msg, 'error');
		}
		else
		{ // The file is okay
			// Check if the file has the right extension, we need csv or xml
			// --> if the extension is wrong, redirect to import.php
			if ((strtolower(File::getExt($filename)) !== 'csv') && (strtolower(File::getExt($filename)) !== 'xml'))
			{
				$msg = Text::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_UPLOAD_TYPE');
				$link = Route::_('index.php?option=com_bwpostman&view=subscriber&layout=import&task=importSubscribers', false);
				$this->setRedirect($link, $msg, 'error');
			}
			// Check if the extension is identical to the selected fileformat
			// --> if not, redirect to import.php
			elseif (((strtolower(File::getExt($filename)) == 'csv') && ($fileformat != 'csv'))
				|| ((strtolower(File::getExt($filename)) == 'xml') && ($fileformat != 'xml')))
			{
				$msg = Text::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_FILE_FORMAT');
				$link = Route::_('index.php?option=com_bwpostman&view=subscriber&layout=import&task=importSubscribers', false);
				$this->setRedirect($link, $msg, 'error');
			}
			else
			{ // Everything is fine
				if (false === File::upload($src, $dest))
				{
					$msg = Text::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_UPLOAD');
					$link = Route::_('index.php?option=com_bwpostman&view=subscriber&layout=import&task=importSubscribers', false);
					$this->setRedirect($link, $msg, 'error');
				}
				else
				{
					$fh = fopen($dest, 'r');

					if ($fh === false)
					{ // File cannot be opened
						$app->enqueueMessage(Text::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_UNABLE_TO_OPEN_FILE'), 'warning');
						return false;
					}
					else
					{
						if ($ext == 'csv')
						{ // CSV file
							$delimiter = stripcslashes($delimiter);
							$data      = fgetcsv($fh, 1000, $delimiter);

							if ($data !== false)
							{
								$import_fields	= array();

								if ($caption)
								{
									for ($i = 0; $i < count($data); $i++)
									{
										$import_fields[] = HtmlHelper::_(
											'select.option',
											"column_$i",
											Text::_('COM_BWPOSTMAN_SUB_IMPORT_COLUMN') . "$i ($data[$i])"
										);
									}
								}
								else
								{
									for ($i = 0; $i < count($data); $i++)
									{
										$import_fields[] 	= HtmlHelper::_(
											'select.option',
											'column_' . $i,
											Text::_('COM_BWPOSTMAN_SUB_IMPORT_COLUMN') . $i
										);
									}
								}

								//Save the import_fields from the csv-file into the session
								$session->set('import_fields', $import_fields);
							}
							else
							{ // File cannot be read
								$app->enqueueMessage(Text::_('COM_BWPOSTMAN_SUB_IMPORT_ERROR_UNABLE_TO_READ_FILE') . ": '$dest'", 'warning');
							}
						}
						else
						{ // XML file
							// Parse the XML
							$parser = new SimpleXMLElement($dest, null, true);

							// Get the name of the paling element
							echo "NAME: '{$parser->getName()}' <br>\n";

							if ($parser->getName() != "subscribers")
							{
								// TODO: es ist kein bwpostman xml file! können trotzdem fortfahren, falls geeignete felder drin sind
								return false;
							}

							// Get all fields from the xml file for listing and selecting by the user
							$addresses    = $parser->xpath("subscriber");
							$elements     = $addresses[0]->children();
							$elementNames = array();

							foreach ($elements as $element)
							{
								$elementNames[] = $element->getName();
							}

							$import_fields = array();

							for ($i = 0; $i < count($elementNames); $i++)
							{
								$import_fields[] = HtmlHelper::_(
									'select.option',
									"$elementNames[$i]",
									Text::_('COM_BWPOSTMAN_SUB_IMPORT_FIELD') . "$i ($elementNames[$i])"
								);
							}

							$session->set('import_fields', $import_fields);
						}

						fclose($fh);
					}

					$link = Route::_('index.php?option=com_bwpostman&view=subscriber&layout=import1', false);
					$this->setRedirect($link);
				}
			}
		}

		return true;
	}

	/**
	 * Method to import subscriber data
	 *
	 * @return	void
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function import()
	{
		$jinput	= Factory::getApplication()->input;

		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		$post = $jinput->getArray(
			array(
				'db_fields' => 'array',
				'emailformat' => 'string',
				'import_fields' => 'array',
				'jform' => 'array',
				'task' => 'string',
				'controller' => 'string',
				'confirm' => 'string',
				'validate' => 'string',
				'option' => 'string'
			)
		);

		$model      = $this->getModel('subscriber');
		$subscriber = new stdClass();
		$maildata   = array();
		$session    = Factory::getApplication()->getSession();

		$model->import($post, $maildata);
		$import_result = $session->set('com_bwpostman.subscriber.import.messages', array());

		// Send emails to subscribers if they weren't confirmed
		if (count($maildata))
		{
			$itemid = BwPostmanSubscriberHelper::getMenuItemid('register');

			for ($i = 0;$i < count($maildata);$i++)
			{
				// Send registration confirmation mail
				$res = BwPostmanSubscriberHelper::sendMail($maildata[$i], 4, $itemid);

				if ($res === false)
				{ // Store the mailing errors into the result array
					$mail_err['row'] 	= $maildata[$i]->row;
					$mail_err['email'] 	= $subscriber->email;
//					$mail_err['msg'] 	= $res->message;

					$import_result['mail_err'][] = $mail_err;
				}
			}
		}

		//Get session object and store the result-array into the session
		$session->set('com_bwpostman.subscriber.import.messages', $import_result);

		$link = Route::_('index.php?option=com_bwpostman&view=subscriber&layout=import2', false);
		$this->setRedirect($link);
	}

	/**
	 * Method to call the layout for the export process
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function exportSubscribers(): bool
	{
		$jinput = Factory::getApplication()->input;
		$user   = Factory::getApplication()->getIdentity();

		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		// Which tab are we in?
		$layout = $jinput->get('tab', 'confirmed');

		// Access check.
		if (!$user->authorise('bwpm.edit', 'com_bwpostman') && !$user->authorise('bwpm.subscriber.edit', 'com_bwpostman.subscriber'))
		{
			$link = Route::_('index.php?option=com_bwpostman&controller=subscribers&layout=' . $layout, false);
			$this->setRedirect($link);
			return false;
		}

		// Set state for filtered mailinglist
		$mlToExport = $jinput->get('mlToExport', '');
		Factory::getApplication()->setUserState('com_bwpostman.subscribers.mlToExport', $mlToExport);

		$jinput->set('hidemainmenu', 1);
		$jinput->set('view', 'subscriber');
		$jinput->set('layout', 'export');
		$link = Route::_('index.php?option=com_bwpostman&view=subscriber&layout=export', false);
		$this->setRedirect($link);

		return true;
	}

	/**
	 * Method to call the view for the export process
	 * --> we will take the raw-view which calls the export-function in the model
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function export()
	{

		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		$app      = Factory::getApplication();
		$document = $app->getDocument();
		$jinput   = $app->input;
		$post     = $jinput->getArray(
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
				'option' => 'string',
				'mlToExport' => 'string'
			)
		);

		$app->setUserState('com_bwpostman.subscribers.export.data', $post);
		$jinput->set('view', 'subscriber');

		// Workaround to get error messages on the screen
		$model = $this->getModel('subscriber');

		if (!$model->export($post))
		{
			$jinput->set('layout', 'export');
			$link = Route::_('index.php?option=com_bwpostman&view=subscribers', false);
			$this->setRedirect($link);
		}
		else
		{
			$document->setType('raw');
			$link = Route::_('index.php?option=com_bwpostman&view=subscriber&layout=export&format=raw', false);

			$this->setRedirect($link);
		}

		parent::display();
	}
}
