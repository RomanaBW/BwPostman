<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman archive controller for backend.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2016 Boldt Webservice <forum@boldt-webservice.de>
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

// Import CONTROLLER and Helper object class
jimport('joomla.application.component.controller');

use Joomla\Utilities\ArrayHelper as ArrayHelper;

/**
 * BwPostman Archive Controller
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Archive
 *
 * @since   0.9.1
 */
class BwPostmanControllerArchive extends JControllerLegacy
{

	/**
	 * Constructor
	 *
	 * @param array $config     configuration params
	 *
	 * @since   0.9.1
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Method to unarchive items
	 * --> operates on the models which are assigned to the tabs (e.g. tab = newsletters --> model = newsletter)
	 *
	 * @access	public
	 *
	 * @since   0.9.1
	 */
	public function unarchive()
	{
		$app	= JFactory::getApplication();
		$jinput	= JFactory::getApplication()->input;

		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		$tab = $jinput->get('layout','newsletters');

		$cid = $jinput->get('cid', array(0), 'post');
		ArrayHelper::toInteger($cid);

		$n = count ($cid);

		switch ($tab)
		{
			// We are in the newsletters_tab
			default:
			case "newsletters":
					$model = $this->getModel('newsletter');
					if(!$model->archive($cid, 0))
					{
						if ($n > 1)
						{
							echo "<script> alert ('".JText::_('COM_BWPOSTMAN_ARC_ERROR_UNARCHIVING_NLS', true)."'); window.history.go(-1); </script>\n";
						}
						else
						{
							echo "<script> alert ('".JText::_('COM_BWPOSTMAN_ARC_ERROR_UNARCHIVING_NL', true)."'); window.history.go(-1); </script>\n";
						}
					}
					else
					{
						if ($n > 1)
						{
							$msg = JText::_('COM_BWPOSTMAN_ARC_NLS_UNARCHIVED');
						}
						else
						{
							$msg = JText::_('COM_BWPOSTMAN_ARC_NL_UNARCHIVED');
						}

						$app->enqueueMessage($msg);
						$jinput->set('layout', 'newsletters');
					}
				break;

			// We are in the subscribers_tab
			case "subscribers":
					$model = $this->getModel('subscriber');
					if(!$model->archive($cid, 0))
					{
						if ($n > 1)
						{
							echo "<script> alert ('".JText::_('COM_BWPOSTMAN_ARC_ERROR_UNARCHIVING_SUBS', true)."'); window.history.go(-1); </script>\n";
						}
						else
						{
							echo "<script> alert ('".JText::_('COM_BWPOSTMAN_ARC_ERROR_UNARCHIVING_SUB', true)."'); window.history.go(-1); </script>\n";
						}
					}
					else
					{
						if ($n > 1)
						{
							$msg = JText::_('COM_BWPOSTMAN_ARC_SUBS_UNARCHIVED');
						}
						else
						{
							$msg = JText::_('COM_BWPOSTMAN_ARC_SUB_UNARCHIVED');
						}

						$app->enqueueMessage($msg);
						$jinput->set('layout', 'subscribers');
					}
				break;

			// We are in the campaigns_tab
			case "campaigns":
					// If archive_nl = 1 the assigned newsletters shall be archived, too
					$unarchive_nl = $jinput->get('unarchive_nl');

					$model = $this->getModel('campaign');
					if(!$model->archive($cid, 0, $unarchive_nl))
					{
						if ($n > 1)
						{
							if ($unarchive_nl)
							{
								echo "<script> alert ('".JText::_('COM_BWPOSTMAN_ARC_ERROR_UNARCHIVING_CAMS_NL', true)."'); window.history.go(-1); </script>\n";
							}
							else
							{
								echo "<script> alert ('".JText::_('COM_BWPOSTMAN_ARC_ERROR_UNARCHIVING_CAMS', true)."'); window.history.go(-1); </script>\n";
							}
						}
						else
						{
							if ($unarchive_nl)
							{
								echo "<script> alert ('".JText::_('COM_BWPOSTMAN_ARC_ERROR_UNARCHIVING_CAM_NL', true)."'); window.history.go(-1); </script>\n";
							}
							else
							{
								echo "<script> alert ('".JText::_('COM_BWPOSTMAN_ARC_ERROR_UNARCHIVING_CAM', true)."'); window.history.go(-1); </script>\n";
							}
						}
					}
					else {
						if ($n > 1)
						{
							if ($unarchive_nl)
							{
								$msg = JText::_('COM_BWPOSTMAN_ARC_CAMS_NL_UNARCHIVED');
							}
							else
							{
								$msg = JText::_('COM_BWPOSTMAN_ARC_CAMS_UNARCHIVED');
							}
						}
						else
						{
							if ($unarchive_nl)
							{
								$msg = JText::_('COM_BWPOSTMAN_ARC_CAM_NL_UNARCHIVED');
							}
							else
							{
								$msg = JText::_('COM_BWPOSTMAN_ARC_CAM_UNARCHIVED');
							}
						}
						$app->enqueueMessage($msg);
						$jinput->set('layout', 'campaigns');
					}
				break;

			// We are in the mailinglists_tab
			case "mailinglists":
					$model = $this->getModel('mailinglist');
					if(!$model->archive($cid, 0))
					{
						if ($n > 1)
						{
							echo "<script> alert ('".JText::_('COM_BWPOSTMAN_ARC_ERROR_UNARCHIVING_MLS', true)."'); window.history.go(-1); </script>\n";
						}
						else
						{
							echo "<script> alert ('".JText::_('COM_BWPOSTMAN_ARC_ERROR_UNARCHIVING_ML', true)."'); window.history.go(-1); </script>\n";
						}
					}
					else
					{
						if ($n > 1)
						{
							$msg = JText::_('COM_BWPOSTMAN_ARC_MLS_UNARCHIVED');
						}
						else
						{
							$msg = JText::_('COM_BWPOSTMAN_ARC_ML_UNARCHIVED');
						}
						$app->enqueueMessage($msg);
						$jinput->set('layout', 'mailinglists');
					}
				break;

			// We are in the templates_tab
			case "templates":
					$model = $this->getModel('template');
					if(!$model->archive($cid, 0))
					{
						if ($n > 1)
						{
							echo "<script> alert ('".JText::_('COM_BWPOSTMAN_ARC_ERROR_UNARCHIVING_TPLS', true)."'); window.history.go(-1); </script>\n";
						}
						else
						{
							echo "<script> alert ('".JText::_('COM_BWPOSTMAN_ARC_ERROR_UNARCHIVING_TPL', true)."'); window.history.go(-1); </script>\n";
						}
					}
					else
					{
						if ($n > 1)
						{
							$msg = JText::_('COM_BWPOSTMAN_ARC_TPLS_UNARCHIVED');
						}
						else
						{
							$msg = JText::_('COM_BWPOSTMAN_ARC_TPL_UNARCHIVED');
						}
						$app->enqueueMessage($msg);
						$jinput->set('layout', 'templates');
					}
				break;
		}
		$jinput->set('view', 'archive');
		parent::display();

	}

	/**
	 * Method to remove an item from the database
	 * --> operates on the models which are assigned to the tabs (e.g. tab = newsletters --> model = newsletter)
	 *
	 * @access	public
	 *
	 * @since   0.9.1
	 */
	public function delete()
	{
		$jinput	= JFactory::getApplication()->input;

		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		$app	= JFactory::getApplication();
		$tab	= $jinput->get('layout','newsletters');
		$cid	= $jinput->get('cid', array(0), 'post');
		$type	= 'message';

		ArrayHelper::toInteger($cid);

		$n = count ($cid);

		switch ($tab)
		{
			// We are in the newsletters_tab
			default:
			case "newsletters":
					$model = $this->getModel('newsletter');
					if(!$model->delete($cid))
					{
						$type	= 'error';
						if ($n > 1)
						{
							$msg = JText::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_NLS');
						}
						else
						{
							$msg = JText::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_NL');
						}
					}
					else
					{
						if ($n > 1)
						{
							$msg = JText::_('COM_BWPOSTMAN_ARC_NLS_REMOVED');
						}
						else
						{
							$msg = JText::_('COM_BWPOSTMAN_ARC_NL_REMOVED');
						}
					}
				break;

			// We are in the subscribers_tab
			case "subscribers":
					$model = $this->getModel('subscriber');
					if(!$model->delete($cid))
					{
						$type	= 'error';
						if ($n > 1)
						{
							$msg = JText::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_SUBS');
						}
						else
						{
							$msg = JText::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_SUB');
						}
					}
					else
					{
						if ($n > 1)
						{
							$msg = JText::_('COM_BWPOSTMAN_ARC_SUBS_REMOVED');
						}
						else
						{
							$msg = JText::_('COM_BWPOSTMAN_ARC_SUB_REMOVED');
						}
					}
				break;

			// We are in the campaigns_tab
			case "campaigns":
					// If archive_nl = 1 the assigned newsletters shall be archived, too
					$remove_nl = $jinput->get('remove_nl');
					$model = $this->getModel('campaign');
					if(!$model->delete($cid, $remove_nl))
					{
						$type	= 'error';
						if ($n > 1) {
							if ($remove_nl)
							{
								$msg = JText::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_CAMS_NL');
							}
							else
							{
								$msg = JText::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_CAMS');
							}
						}
						else {
							if ($remove_nl)
							{
								$msg = JText::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_CAM_NL');
							}
							else
							{
								$msg = JText::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_CAM');
							}
						}
					}
					else {
						if ($n > 1)
						{
							if ($remove_nl)
							{
								$msg = JText::_('COM_BWPOSTMAN_ARC_CAMS_NL_REMOVED');
							}
							else
							{
								$msg = JText::_('COM_BWPOSTMAN_ARC_CAMS_REMOVED');
							}
						}
						else {
							if ($remove_nl)
							{
								$msg = JText::_('COM_BWPOSTMAN_ARC_CAM_NL_REMOVED');
							}
							else
							{
								$msg = JText::_('COM_BWPOSTMAN_ARC_CAM_REMOVED');
							}
						}
					}
				break;

			// We are in the mailinglists_tab
			case "mailinglists":
					$model = $this->getModel('mailinglist');
					if(!$model->delete($cid))
					{
						$type	= 'error';
						if ($n > 1)
						{
							$msg = JText::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_MLS');
						}
						else
						{
							$msg = JText::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_ML');
						}

					}
					else
					{
						if ($n > 1)
						{
							$msg = JText::_('COM_BWPOSTMAN_ARC_MLS_REMOVED');
						}
						else
						{
							$msg = JText::_('COM_BWPOSTMAN_ARC_ML_REMOVED');
						}
					}
					break;

				// We are in the templates_tab
				case "templates":
					$model = $this->getModel('template');
					if(!$model->delete($cid))
					{
						$type	= 'error';
						if ($n > 1)
						{
							$msg = JText::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_TPLS');
						}
						else
						{
							$msg = JText::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_TPL');
						}

					}
					else {
						if ($n > 1)
						{
							$msg = JText::_('COM_BWPOSTMAN_ARC_TPLS_REMOVED');
						}
						else
						{
							$msg = JText::_('COM_BWPOSTMAN_ARC_TPL_REMOVED');
						}
					}
				break;
		}
		$app->enqueueMessage($msg, $type);
		parent::display();
	}
}
