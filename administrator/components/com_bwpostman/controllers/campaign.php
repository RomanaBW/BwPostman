<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman campaign controller for backend.
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

// Import CONTROLLER object class
jimport('joomla.application.component.controllerform');

// Require helper class
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');

/**
 * BwPostman Campaign Controller
 *
 * @since		1.0.1
 * @package 	BwPostman-Admin
 * @subpackage 	Campaigns
 */
class BwPostmanControllerCampaign extends JControllerForm
{
	/**
	 * @var		string		The prefix to use with controller messages.
	 * @since	1.0.4
	 */
	protected $text_prefix = 'COM_BWPOSTMAN_CAM';

	/**
	 * Constructor.
	 *
	 * @param	array	$config		An optional associative array of configuration settings.
	 *
	 * @since	1.0.1
	 *
	 * @see		JController
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

	}

	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param	array	$data		An array of input data.
	 *
	 * @return	boolean
	 *
	 * @since	1.0.1
	 */
	protected function allowAdd($data = array())
	{
		$user	= JFactory::getUser();

		return ($user->authorise('bwpm.create', 'com_bwpostman'));
	}

	/**
	 * Method override to check if you can edit a record.
	 *
	 * @param	array	$data		An array of input data.
	 * @param	string	$key		The name of the key for the primary key.
	 *
	 * @return	boolean
	 *
	 * @since	1.0.1
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Initialise variables.
		$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
		$user		= JFactory::getUser();
		$userId		= $user->get('id');

		// Check general edit permission first.
		if ($user->authorise('bwpm.edit', 'com_bwpostman.campaign'))
		{
			return true;
		}

		// Check specific edit permission.
		if ($user->authorise('bwpm.campaign.edit', 'com_bwpostman.campaigns.' . $recordId))
		{
			return true;
		}

		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('bwpm.campaign.edit.own', 'com_bwpostman.campaign.' . $recordId) || $user->authorise('bwpm.edit.own', 'com_bwpostman'))
		{
			// Now test the owner is the user.
			$ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;
			if (empty($ownerId) && $recordId)
			{
				// Need to do a lookup from the model.
				$record = $this->getModel()->getItem($recordId);

				if (empty($record))
				{
					return false;
				}
				$ownerId = $record->created_by;
			}

			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId)
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Override method to edit an existing record, based on Joomla method.
	 * We need an override, because we want to handle state a bit different than Joomla at this point
	 *
	 * @param	string	$key		The name of the primary key of the URL variable.
	 * @param	string	$urlVar		The name of the URL variable if different from the primary key
	 * (sometimes required to avoid router collisions).
	 *
	 * @return	boolean		True if access level check and checkout passes, false otherwise.
	 *
	 * @since	1.0.1
	 */
	public function edit($key = null, $urlVar = null)
	{
		// Initialise variables.
		$jinput		= JFactory::getApplication()->input;
		$model		= $this->getModel();
		$table		= $model->getTable();
		$cid		= $jinput->post->get('cid', array(), 'array');
		$context	= "$this->option.edit.$this->context";

		// Determine the name of the primary key for the data.
		if (empty($key))
		{
			$key = $table->getKeyName();
		}

		// To avoid data collisions the urlVar may be different from the primary key.
		if (empty($urlVar))
		{
			$urlVar = $key;
		}

		// Get the previous record id (if any) and the current record id.
		$recordId = (int) (count($cid) ? $cid[0] : $jinput->getInt($urlVar));
		$checkin = property_exists($table, 'checked_out');

		// Access check.
		if (!$this->allowEdit(array($key => $recordId), $key))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(), false
				)
			);

			return false;
		}

		// Attempt to check-out the new record for editing and redirect.
		if ($checkin && !$model->checkout($recordId))
		{
			// Check-out failed, display a notice…
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');

			// …and do not allow the user to see the record.
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
				)
			);

			return false;
		}
		else
		{
			// Check-out succeeded, push the new record id into the session.
			$this->holdEditId($context, $recordId);
//			$app->setUserState($context . '.data', null);

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
				)
			);

			return true;
		}
	}

	/**
	 * Override method to save a campaign
	 *
	 * @access	public
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 */
	public function save($key = null, $urlVar = null)
	{
		parent::save();

		$dispatcher = JEventDispatcher::getInstance();

		JPluginHelper::importPlugin('bwpostman');
		$dispatcher->trigger('onBwPostmanAfterCampaignControllerSave', array());
	}

	/**
	 * Method to archive one or more campaigns and if the user want also the assigned newsletters
	 * --> campaigns-table: archive_flag = 1, set archive_date
	 *
	 * @access	public
	 *
	 * @return 	void
	 */
	public function archive()
	{
		// Check for request forgeries
		if (!JSession::checkToken()) jexit(JText::_('JINVALID_TOKEN'));

		$jinput	= JFactory::getApplication()->input;

		// If archive_nl = 1 the assigned newsletters shall be archived, too
		$archive_nl = $jinput->get('archive_nl');

		// Get the selected campaign(s)
		$cid = $jinput->get('cid', array(0), 'post', 'array');
		JArrayHelper::toInteger($cid);

		$n = count ($cid);

		$model = $this->getModel('campaign');
		if(!$model->archive($cid, 1, $archive_nl)) {
			if ($n > 1) {
				if ($archive_nl) {
					echo "<script> alert ('".JText::_('COM_BWPOSTMAN_CAMS_NL_ERROR_ARCHIVING', true)."'); window.history.go(-1); </script>\n";
				}
				else {
					echo "<script> alert ('".JText::_('COM_BWPOSTMAN_CAMS_ERROR_ARCHIVING', true)."'); window.history.go(-1); </script>\n";
				}
			}
			else {
				if ($archive_nl) {
					echo "<script> alert ('".JText::_('COM_BWPOSTMAN_CAM_NL_ERROR_ARCHIVING', true)."'); window.history.go(-1); </script>\n";
				}
				else {
					echo "<script> alert ('".JText::_('COM_BWPOSTMAN_CAM_ERROR_ARCHIVING', true)."'); window.history.go(-1); </script>\n";
				}
			}
		}
		else {
			if ($n > 1) {
				if ($archive_nl) {
					$msg = JText::_('COM_BWPOSTMAN_CAMS_NL_ARCHIVED');
				}
				else {
					$msg = JText::_('COM_BWPOSTMAN_CAMS_ARCHIVED');
				}
			}
			else {
				if ($archive_nl) {
					$msg = JText::_('COM_BWPOSTMAN_CAM_NL_ARCHIVED');
				}
				else {
					$msg = JText::_('COM_BWPOSTMAN_CAM_ARCHIVED');
				}
			}
			$link = JRoute::_('index.php?option=com_bwpostman&view=campaigns', false);

			$this->setRedirect($link, $msg);
		}
	}

	/**
	 * Dummy Method for Plugin BwTimeControl
	 *
	 * @access	public
	 *
	 * @return 	void
	 */
	public function dueSend()
	{
		$dispatcher = JEventDispatcher::getInstance();

		JPluginHelper::importPlugin('bwpostman');
		$dispatcher->trigger('onBwPostmanCampaignsTaskDueSend');
	}

	/**
	 * Dummy Method for Plugin BwTimeControl
	 *
	 * @access	public
	 *
	 * @return 	void
	 */
	public function autotest()
	{
		$dispatcher = JEventDispatcher::getInstance();

		JPluginHelper::importPlugin('bwpostman');
		$dispatcher->trigger('onBwPostmanCampaignsTaskAutoTest');
	}

	/**
	 * Dummy Method for Plugin BwTimeControl
	 *
	 * @access	public
	 *
	 * @return 	void
	 */
	public function activate()
	{
		$dispatcher = JEventDispatcher::getInstance();

		JPluginHelper::importPlugin('bwpostman');
		$dispatcher->trigger('onBwPostmanCampaignsTaskActivate');
	}


	/**
	 * Dummy Method for Plugin BwTimeControl
	 *
	 * @access	public
	 *
	 * @return 	void
	 */
	public function suspendNewletterFromSending()
	{
		$dispatcher = JEventDispatcher::getInstance();

		JPluginHelper::importPlugin('bwpostman');

		$jinput		= JFactory::getApplication()->input;
		$get_data	= $jinput->getArray(array('suspended' => '', 'id' => ''));
		$link		= JRoute::_(JFactory::getApplication()->getUserState('bwtimecontrol.campaign_edit.request', null), false);
		$msg        = '';

		$dispatcher->trigger('onBwPostmanCampaignTaskSuspendNewsletterFromSending', array(&$get_data));

		$this->setRedirect($link, $msg);
	}
}
