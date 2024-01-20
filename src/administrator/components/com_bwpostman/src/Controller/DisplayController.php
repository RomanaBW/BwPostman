<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace BoldtWebservice\Component\BwPostman\Administrator\Controller;

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
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link \JFilterInput::clean()}.
	 *
	 * @return  BaseController|bool  This object to support chaining.
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	public function display($cachable = false, $urlparams = array())
	{
		// Get the user object
		$app  = Factory::getApplication();
		$user = $app->getIdentity();

		// Access check.
		if ((!$user->authorise('core.manage', 'com_bwpostman')))
		{
			$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'warning');

			return false;
		}

		// Preload user permissions
		BwPostmanHelper::setPermissionsState();

		return parent::display();
	}


	/**
	 * Method to call the start layout for the add text template
	 *
	 * @throws Exception
	 *
	 * @since	1.1.0
	 */
	public function addtext(): void
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
	 * @throws Exception
	 *
	 * @since	1.1.0
	 */
	public function addhtml(): void
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
	public function storePermission(): void
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
}
