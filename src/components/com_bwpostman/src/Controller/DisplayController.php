<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace BoldtWebservice\Component\BwPostman\Site\Controller;

defined('_JEXEC') or die;

use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Model\BwpostmanModel;
use BoldtWebservice\Plugin\Bwpostman\Bwtimecontrol\Helper\BwPostmanPhpCron;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Registry\Registry;
use JResponseJson;
use function defined;

/**
 * Banners master display controller.
 *
 * @since  1.6
 */
class DisplayController extends BaseController
{
	/**
	 * The default view.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $default_view = 'bwpostman';

	/**
	 * Constructor.
	 *
	 * @param 	array	$config		An optional associative array of configuration settings.
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1

	 * @see		JController
	 */
	public function __construct($config = array())
	{
		$this->factory = Factory::getApplication()->bootComponent('com_bwpostman')->getMVCFactory();
		$config['view_path'] = JPATH_COMPONENT . '/src/View';

		parent::__construct($config, $this->factory);
	}

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link \JFilterInput::clean()}.
	 *
	 * @return  DisplayController  This object to support chaining.
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function display($cachable = false, $urlparams = array()): DisplayController
	{
		// Get the user object
		$app   = Factory::getApplication();
		$input = $app->input;
		$view  = $input->get('view', 'Register');

		$input->set('view', $view);

		// Preload user permissions
		BwPostmanHelper::setPermissionsState();

		return parent::display();
	}


	/**
	 * Method to call the start layout for the add text template
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since	1.1.0
	 */
	public function addtext()
	{
		$jinput	= Factory::getApplication()->input;

		$jinput->set('hidemainmenu', 1);
		$jinput->set('view', 'template');
		$jinput->set('layout', 'default_text');
		$link = Route::_('index.php?option=com_bwpostman&view=template&layout=default_text', false);
		$this->setRedirect($link);
	}

	/**
	 * Method to call the start layout for the add html template
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since	1.1.0
	 */
	public function addhtml()
	{
		$jinput	= Factory::getApplication()->input;

		$jinput->set('hidemainmenu', 1);
		$jinput->set('view', 'template');
		$jinput->set('layout', 'default_add');
		$link = Route::_('index.php?option=com_bwpostman&view=template&layout=default_html', false);
		$this->setRedirect($link);
	}

	/**
	 * Method to GET permission value and give it to the model for storing in the database.
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since   3.5
	 */
	public function storePermission()
	{
		$app	= Factory::getApplication();

		// Send json mime type.
		$app->mimeType = 'application/json';
//		@ToDo: $app has no property charSet
//		@ToDo: $app has no method setHeader
//		@ToDo: $app has no method sendHeaders
		$app->setHeader('Content-Type', $app->mimeType . '; charset=' . $app->charSet);
		$app->sendHeaders();

		// Check if user token is valid.
		if (!Session::checkToken('get'))
		{
			$app->enqueueMessage(Text::_('JINVALID_TOKEN'), 'error');
			echo new JResponseJson;
			$app->close();
		}

		$model = new BwpostmanModel();
		echo new JResponseJson($model->storePermissions());
		$app->close();
	}

	/**
	 * Method to do the cron loop
	 *
	 * @return boolean
	 *
	 * @throws  Exception
	 *
	 * @since       2.3.0
	 */
	public function doCron(): bool
	{
        \JLoader::registerNamespace('BoldtWebservice\\Plugin\\Bwpostman\\Bwtimecontrol\\Helper', JPATH_PLUGINS . '/bwpostman/bwtimecontrol/helpers');

        $plugin = PluginHelper::getPlugin('bwpostman', 'bwtimecontrol');
		$pluginParams = new Registry();
		$pluginParams->loadString($plugin->params);
		$pluginPw   = (string) $pluginParams->get('bwtimecontrol_passwd', '');
		$pluginUser = (string) $pluginParams->get('bwtimecontrol_username', '');

		if ($pluginUser === "" || $pluginPw === "")
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_ERROR_TC_NO_CREDENTIALS'), 'error');
		}

		$bwpostmancron = new BwPostmanPhpCron();

		$bwpostmancron->doCronJob();

		return true;
	}
}
