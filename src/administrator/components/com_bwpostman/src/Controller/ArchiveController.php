<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman archive controller for backend.
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

use Exception;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;

/**
 * BwPostman Archive Controller
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Archive
 *
 * @since   0.9.1
 */
class ArchiveController extends AdminController
{

	/**
	 * Constructor
	 *
	 * @param array $config configuration params
	 *
	 * @throws Exception
	 *
	 * @since   0.9.1
	 */
	public function __construct($config = array())
	{
		$this->factory = Factory::getApplication()->bootComponent('com_bwpostman')->getMVCFactory();

		parent::__construct($config, $this->factory);
	}

	/**
	 * Display
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  ArchiveController		This object to support chaining.
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function display($cachable = false, $urlparams = array()): ArchiveController
	{
		if (!BwPostmanHelper::canView('archive'))
		{
			$this->setRedirect(Route::_('index.php?option=com_bwpostman', false));
			$this->redirect();
			return $this;
		}

		parent::display();
		return $this;
	}

	/**
	 * Method to check if you can restore records
	 *
	 * @param string $view      The view to check if restore is allowed
	 * @param array  $recordIds The item to check if restore is allowed
	 *
	 * @return	boolean
	 *
	 * @throws Exception
	 *
	 * @since	2.0.0
	 */
	protected function allowRestore(string $view = 'newsletter', array $recordIds = array()): bool
	{
		foreach ($recordIds as $recordId)
		{
			$allowed = BwPostmanHelper::canRestore($view, (int) $recordId);

			if (!$allowed)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to check if you can delete records
	 *
	 * @param string $view      The view to check if delete is allowed
	 * @param array  $recordIds The item to check if delete is allowed
	 *
	 * @return	boolean
	 *
	 * @throws Exception
	 *
	 * @since	2.0.0
	 */
	protected function allowDelete(string $view = 'newsletter', array $recordIds = array()): bool
	{
		foreach ($recordIds as $recordId)
		{
			$allowed = BwPostmanHelper::canDelete($view, (int) $recordId);

			if (!$allowed)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name   	The name of the model.
	 * @param	string	$prefix 	The prefix for the PHP class name.
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @return	BaseDatabaseModel

	 * @since	4.0.0
	 */
	public function getModel($name = 'Newsletter', $prefix = 'Administrator', $config = array('ignore_request' => true)): BaseDatabaseModel
	{
		return $this->factory->createModel($name, $prefix, $config);
	}

	/**
	 * Method to unarchive items
	 * --> operates on the models which are assigned to the tabs (e.g. tab = newsletters --> model = newsletter)
	 *
	 * @return boolean|BaseController
	 *
	 * @throws Exception
	 *
	 * @since   0.9.1
	 */
	public function unarchive()
	{
		$app    = Factory::getApplication();
		$jinput = $app->input;

		// Check for request forgeries
		if (!Session::checkToken()) {
			jexit(Text::_('JINVALID_TOKEN'));
		}

		$tab = $jinput->get('layout', 'newsletters');

		$cid = $jinput->get('cid', array(0), 'post');
		ArrayHelper::toInteger($cid);

		// Access check.
		$view   = substr($tab, 0, -1);

		if (!$this->allowRestore($view, $cid))
		{
			$this->setRedirect(
				Route::_(
					'index.php?option=com_bwpostman&view=Archive&layout=' . $tab,
					false
				)
			);
			return false;
		}

		$n = count($cid);

		switch ($tab)
		{
			// We are in the newsletters_tab
			default:
			case "newsletters":
				$model = $this->getModel();

				if(!$model->archive($cid, 0))
				{
					if ($n > 1)
					{
						$alert = Text::_('COM_BWPOSTMAN_ARC_ERROR_UNARCHIVING_NLS', true);
					}
					else
					{
						$alert = Text::_('COM_BWPOSTMAN_ARC_ERROR_UNARCHIVING_NL', true);
					}

					echo "<script>alert ('" . $alert . "'); window.history.go(-1); </script>\n";
				}
				else
				{
					if ($n > 1)
					{
						$msg = Text::_('COM_BWPOSTMAN_ARC_NLS_UNARCHIVED');
					}
					else
					{
						$msg = Text::_('COM_BWPOSTMAN_ARC_NL_UNARCHIVED');
					}

					$app->enqueueMessage($msg);
					$jinput->set('layout', 'newsletters');
				}
				break;

			// We are in the subscribers_tab
			case "subscribers":
				$model = $this->getModel('Subscriber');

				if(!$model->archive($cid, 0))
				{
					if ($n > 1)
					{
						$alert = Text::_('COM_BWPOSTMAN_ARC_ERROR_UNARCHIVING_SUBS', true);
					}
					else
					{
						$alert = Text::_('COM_BWPOSTMAN_ARC_ERROR_UNARCHIVING_SUB', true);
					}

					echo "<script> alert ('" . $alert . "'); window.history.go(-1); </script>\n";
				}
				else
				{
					if ($n > 1)
					{
						$msg = Text::_('COM_BWPOSTMAN_ARC_SUBS_UNARCHIVED');
					}
					else
					{
						$msg = Text::_('COM_BWPOSTMAN_ARC_SUB_UNARCHIVED');
					}

					$app->enqueueMessage($msg);
					$jinput->set('layout', 'subscribers');
				}
				break;

			// We are in the campaigns_tab
			case "campaigns":
				// If archive_nl = 1 the assigned newsletters shall be archived, too
				$unarchive_nl = $jinput->get('unarchive_nl');

				$model = $this->getModel('Campaign');

				if(!$model->archive($cid, 0, $unarchive_nl))
				{
					if ($n > 1)
					{
						if ($unarchive_nl)
						{
							$alert = Text::_('COM_BWPOSTMAN_ARC_ERROR_UNARCHIVING_CAMS_NL', true);
						}
						else
						{
							$alert = Text::_('COM_BWPOSTMAN_ARC_ERROR_UNARCHIVING_CAMS', true);
						}

					}
					else
					{
						if ($unarchive_nl)
						{
							$alert = Text::_('COM_BWPOSTMAN_ARC_ERROR_UNARCHIVING_CAM_NL', true);
						}
						else
						{
							$alert = Text::_('COM_BWPOSTMAN_ARC_ERROR_UNARCHIVING_CAM', true);
						}

					}
					echo "<script> alert ('" . $alert . "'); window.history.go(-1); </script>\n";
				}
				else {
					if ($n > 1)
					{
						if ($unarchive_nl)
						{
							$msg = Text::_('COM_BWPOSTMAN_ARC_CAMS_NL_UNARCHIVED');
						}
						else
						{
							$msg = Text::_('COM_BWPOSTMAN_ARC_CAMS_UNARCHIVED');
						}
					}
					else
					{
						if ($unarchive_nl)
						{
							$msg = Text::_('COM_BWPOSTMAN_ARC_CAM_NL_UNARCHIVED');
						}
						else
						{
							$msg = Text::_('COM_BWPOSTMAN_ARC_CAM_UNARCHIVED');
						}
					}

					$app->enqueueMessage($msg);
					$jinput->set('layout', 'campaigns');
				}
				break;

			// We are in the mailinglists_tab
			case "mailinglists":
				$model = $this->getModel('Mailinglist');

				if(!$model->archive($cid, 0))
				{
					if ($n > 1)
					{
						$alert = Text::_('COM_BWPOSTMAN_ARC_ERROR_UNARCHIVING_MLS', true);
					}
					else
					{
						$alert = Text::_('COM_BWPOSTMAN_ARC_ERROR_UNARCHIVING_ML', true);
					}

					echo "<script> alert ('" . $alert . "'); window.history.go(-1); </script>\n";
				}
				else
				{
					if ($n > 1)
					{
						$msg = Text::_('COM_BWPOSTMAN_ARC_MLS_UNARCHIVED');
					}
					else
					{
						$msg = Text::_('COM_BWPOSTMAN_ARC_ML_UNARCHIVED');
					}

					$app->enqueueMessage($msg);
					$jinput->set('layout', 'mailinglists');
				}
				break;

			// We are in the templates_tab
			case "templates":
				$model = $this->getModel('Template');

				if(!$model->archive($cid, 0))
				{
					if ($n > 1)
					{
						$alert = Text::_('COM_BWPOSTMAN_ARC_ERROR_UNARCHIVING_TPLS', true);
					}
					else
					{
						$alert = Text::_('COM_BWPOSTMAN_ARC_ERROR_UNARCHIVING_TPL', true);
					}

					echo "<script> alert ('" . $alert . "'); window.history.go(-1); </script>\n";
				}
				else
				{
					if ($n > 1)
					{
						$msg = Text::_('COM_BWPOSTMAN_ARC_TPLS_UNARCHIVED');
					}
					else
					{
						$msg = Text::_('COM_BWPOSTMAN_ARC_TPL_UNARCHIVED');
					}

					$app->enqueueMessage($msg);
					$jinput->set('layout', 'templates');
				}
				break;
		}

		$jinput->set('view', 'Archive');

		return parent::display();
	}

	/**
	 * Method to remove an item from the database
	 * --> operates on the models which are assigned to the tabs (e.g. tab = newsletters --> model = newsletter)
	 *
	 * @return boolean|BaseController
	 *
	 * @throws Exception
	 *
	 * @since   0.9.1
	 */
	public function delete()
	{
		$app    = Factory::getApplication();
		$jinput	= $app->input;

		// Check for request forgeries
		if (!Session::checkToken())
		{
			jexit(Text::_('JINVALID_TOKEN'));
		}

		$tab  = $jinput->get('layout', 'newsletters');
		$cid  = $jinput->get('cid', array(0), 'post');
		$type = 'message';

		ArrayHelper::toInteger($cid);

		// Access check.
		$view   = substr($tab, 0, -1);

		if (!$this->allowDelete($view, $cid))
		{
			$this->setRedirect(
				Route::_('index.php?option=com_bwpostman&view=Archive&layout=' . $tab, false)
			);
			return false;
		}

		$n = count($cid);

		switch ($tab)
		{
			// We are in the newsletters_tab
			default:
			case "newsletters":
				$model = $this->getModel();

				if(!$model->delete($cid))
				{
					$type	= 'error';
					if ($n > 1)
					{
						$msg = Text::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_NLS');
					}
					else
					{
						$msg = Text::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_NL');
					}
				}
				else
				{
					if ($n > 1)
					{
						$msg = Text::_('COM_BWPOSTMAN_ARC_NLS_REMOVED');
					}
					else
					{
						$msg = Text::_('COM_BWPOSTMAN_ARC_NL_REMOVED');
					}
				}
				break;

			// We are in the subscribers_tab
			case "subscribers":
				$model = $this->getModel('Subscriber');

				if(!$model->delete($cid))
				{
					$type	= 'error';
					if ($n > 1)
					{
						$msg = Text::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_SUBS');
					}
					else
					{
						$msg = Text::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_SUB');
					}
				}
				else
				{
					if ($n > 1)
					{
						$msg = Text::_('COM_BWPOSTMAN_ARC_SUBS_REMOVED');
					}
					else
					{
						$msg = Text::_('COM_BWPOSTMAN_ARC_SUB_REMOVED');
					}
				}
				break;

			// We are in the campaigns_tab
			case "campaigns":
				// If archive_nl = 1 the assigned newsletters shall be archived, too
				$remove_nl = $jinput->get('remove_nl');
				$model     = $this->getModel('Campaign');

				if(!$model->delete($cid))
				{
					$type	= 'error';
					if ($n > 1) {
						if ($remove_nl)
						{
							$msg = Text::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_CAMS_NL');
						}
						else
						{
							$msg = Text::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_CAMS');
						}
					}
					else {
						if ($remove_nl)
						{
							$msg = Text::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_CAM_NL');
						}
						else
						{
							$msg = Text::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_CAM');
						}
					}
				}
				else {
					if ($n > 1)
					{
						if ($remove_nl)
						{
							$msg = Text::_('COM_BWPOSTMAN_ARC_CAMS_NL_REMOVED');
						}
						else
						{
							$msg = Text::_('COM_BWPOSTMAN_ARC_CAMS_REMOVED');
						}
					}
					else {
						if ($remove_nl)
						{
							$msg = Text::_('COM_BWPOSTMAN_ARC_CAM_NL_REMOVED');
						}
						else
						{
							$msg = Text::_('COM_BWPOSTMAN_ARC_CAM_REMOVED');
						}
					}
				}
				break;

			// We are in the mailinglists_tab
			case "mailinglists":
				$model = $this->getModel('Mailinglist');

				if(!$model->delete($cid))
				{
					$type	= 'error';
					if ($n > 1)
					{
						$msg = Text::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_MLS');
					}
					else
					{
						$msg = Text::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_ML');
					}
				}
				else
				{
					if ($n > 1)
					{
						$msg = Text::_('COM_BWPOSTMAN_ARC_MLS_REMOVED');
					}
					else
					{
						$msg = Text::_('COM_BWPOSTMAN_ARC_ML_REMOVED');
					}
				}
				break;

			// We are in the templates_tab
			case "templates":
				$model = $this->getModel('Template');

				if(!$model->delete($cid))
				{
					$type	= 'error';
					if ($n > 1)
					{
						$msg = Text::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_TPLS');
					}
					else
					{
						$msg = Text::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_TPL');
					}
				}
				else {
					if ($n > 1)
					{
						$msg = Text::_('COM_BWPOSTMAN_ARC_TPLS_REMOVED');
					}
					else
					{
						$msg = Text::_('COM_BWPOSTMAN_ARC_TPL_REMOVED');
					}
				}
				break;
		}

		$app->enqueueMessage($msg, $type);

		return parent::display();
	}
}
