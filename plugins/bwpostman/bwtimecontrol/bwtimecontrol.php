<?php
/**
 * BwPostman Newsletter QuickTimeControl Plugin
 *
 * BwPostman TimeControl Plugin main file for BwPostman.
 *
 * @version 1.2.0 bwplgtc
 * @package BwPostman TimeControl Plugin
 * @author Romana Boldt
 * @copyright (C) 2014 Boldt Webservice <forum@boldt-webservice.de>
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
defined ( '_JEXEC' ) or die ( 'Restricted access' );

// Require helper class
require_once (JPATH_PLUGINS.'/bwpostman/bwtimecontrol/helpers/campaignhelper.php');

use Joomla\Utilities\ArrayHelper as ArrayHelper;

/**
 * @package     ${NAMESPACE}
 *
 * @since       2.0.0
 */
class plgBwPostmanBwTimeControl extends JPlugin
{
	/**
	 * Database object
	 *
	 * @var    bool Plugin enabled?
	 *
	 * @since       2.0.0
	 */
	protected $_enabled;

	/**
	 * plgBwPostmanBwTimeControl constructor.
	 *
	 * @param object $subject
	 * @param array  $config
	 *
	 * @since       2.0.0
	 */
	function __construct($subject, $config) {
		$this->_enabled = false;

		// Do not load if BwPostman version is not supported or BwPostman isn't detected
		// TODO check licence!
        if (JComponentHelper::getComponent('com_bwpostman', true)->enabled === false) {
            return;
        }
        else {
        	$this->_enabled = true;
        }

		parent::__construct ( $subject, $config );

		$this->loadLanguage('plg_bwpostman_bwtimecontrol.sys');
	}

	/**
	 * Method to send due mails
	 *
	 * @access	public
	 *
	 * @return 	bool	true on success
	 *
	 * @since	1.2.0
	 */
	public function onBwPostmanCampaignsTaskDueSend ()
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}


		$controller	= JControllerLegacy::getInstance('BwPostman');

		$ret	= BwPostmanCampaignHelper::sendDueNewsletters();

		if ($ret == 0) {
			$msg	= JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_DUESEND_EMPTY_QUEUE');
			$type	= 'message';
		}
		elseif ($ret === false) {
			$msg	= JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_ERROR_DUESEND');
			$type	= 'error';
		}
		else {
			$msg	= JText::sprintf('PLG_BWPOSTMAN_BWTIMECONTROL_SUCCESS_DUESEND', $ret);
			$type	= 'message';
		}

		$link = 'index.php?option=com_bwpostman&view=campaigns';

		$controller->setRedirect($link, $msg, $type);
		return true;
	}

	/**
	 * Method to  test automation
	 *
	 * @access	public
	 *
	 * @return 	boolean 	true on success
	 *
	 * @since	1.2.0
	 */
	public function onBwPostmanCampaignsTaskAutoTest ()
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		$app		= JFactory::getApplication();
		$jinput		= JFactory::getApplication()->input;
		$controller	= JControllerLegacy::getInstance('BwPostman');
		$msg		= '';
		$type       = '';
		$link		= 'index.php?option=com_bwpostman&view=campaigns';

		// Get the selected campaign(s)
		$cids	= $jinput->get('cid', array(0), 'post');
		ArrayHelper::toInteger($cids);

		if (count($cids) > 1) {
			$msg	= JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_AUTOTEST_ONLY_ONE_CAMPAIGN');
			$type	= 'warning';
			$app->enqueueMessage($msg, $type);
		}
		$id		= BwPostmanCampaignHelper::getTcIdFromCampaign($cids[0]);
		$item	= BwPostmanCampaignHelper::getItem($id);

		// Automailing check
		if ($item->automailing != '1') {
			$msg	= JText::sprintf('PLG_BWPOSTMAN_BWTIMECONTROL_AUTOTEST_NO_AUTO_CAMPAIGN', $cids[0]);
			$type	= 'warning';
			$controller->setRedirect($link, $msg, $type);
			return false;
		}

		$ret	= BwPostmanCampaignHelper::HandleQueue($item->campaign_id, 0, 'test', 0);

		if ($ret[1] == 0) {
			$msg	= JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_AUTOTEST_EMPTY_QUEUE');
			$type	= 'message';
		}
		elseif ($ret[1] === false) {
			$msg	= JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_AUTOTEST_ERROR');
			$type	= 'error';
		}
		elseif ($ret[1] > 0) {
			$msg	= JText::sprintf('PLG_BWPOSTMAN_BWTIMECONTROL_AUTOTEST_SUCCESS_SEND', $ret[1]);
			$type	= 'message';
		}

		if ($ret[0] === false) {
			$msg	= JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_AUTOTEST_ERROR_FILL_QUEUE');
			$type	= 'error';
		}
		$controller->setRedirect($link, $msg, $type);
		return true;

	}

	/**
	 * Method to activate automated campaign
	 *
	 * @access	public
	 *
	 * @return 	boolean 	true on success
	 *
	 * @since	1.2.0
	 */
	public function onBwPostmanCampaignsTaskActivate ()
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		$app		= JFactory::getApplication();
		$jinput		= $app->input;
		$controller	= JControllerLegacy::getInstance('BwPostman');
		$msg		= '';
		$type       = '';
		$task		= 'activate';
		$link		= 'index.php?option=com_bwpostman&view=campaigns';

		// Get the selected campaign
		$cids	= $jinput->get('cid', array(0), 'post');

		if (count($cids) > 1) {
			$msg	= JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_ACTIVATE_ONLY_ONE_CAMPAIGN');
			$type	= 'warning';
			$app->enqueueMessage($msg, $type);
		}

		$id		= BwPostmanCampaignHelper::getTcIdFromCampaign($cids[0]);

		// Selected check
		if ($id == '0') {
			$msg	= JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_ACTIVATE_NO_AUTO_CAMPAIGN_SELECTED');
			$type	= 'error';
			$controller->setRedirect($link, $msg, $type);
			return false;
		}

		$item	= BwPostmanCampaignHelper::getItem($id);

		// Automailing check
		if ($item->automailing != '1') {
			$msg	= JText::sprintf('PLG_BWPOSTMAN_BWTIMECONTROL_ACTIVATE_NO_AUTO_CAMPAIGN', $cids[0]);
			$type	= 'error';
			$controller->setRedirect($link, $msg, $type);
			return false;
		}

		// task for deactivation?
		if ($item->active) $task = 'deactivate';

		$ret	= BwPostmanCampaignHelper::HandleQueue($item->campaign_id, 0, $task, 0);

		if ($ret[1] == 0) {
			$msg	= JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_ACTIVATE_EMPTY_QUEUE');
			$type	= 'message';
		}
		elseif ($ret[1] === false) {
			$msg	= JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_ACTIVATE_ERROR');
			$type	= 'error';
		}
		elseif ($ret[1] > 0) {
			$msg	= JText::sprintf('PLG_BWPOSTMAN_BWTIMECONTROL_ACTIVATE_SUCCESS_SEND', $ret[1]);
			$type	= 'message';
		}

		if ($ret[0] === false) {
			$msg	= JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_ACTIVATE_ERROR_FILL_QUEUE');
			$type	= 'error';
		}

		$controller->setRedirect($link, $msg, $type);
		return true;
	}

	/**
	 * Method to prepare toolbar buttons for BwTimeControl
	 *
	 * @access	public
	 *
	 * @param   object      $canDo
	 *
	 * @return 	boolean 	true on success
	 *
	 * @since	1.2.0
	 */
	public function onBwPostmanCampaignsPrepareToolbar ()
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		if (BwPostmanHelper::canEditState('campaign', 0))	JToolbarHelper::custom ('campaign.autotest', 'question-circle', 'question-circle', JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_AUTOTEST'), true);
		if (BwPostmanHelper::canEditState('campaign', 0))	JToolbarHelper::custom ('campaign.activate', 'publish', 'publish', JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_ACTIVATE'), true);
		if (BwPostmanHelper::canEditState('campaign', 0))	JToolbarHelper::custom ('campaign.dueSend', 'broadcast', 'broadcast', JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_DUESEND'), false);

		return true;
	}

	/**
	 * Method to switch content table and queue table to BwTimeControl-tables
	 *
	 * @access	public
	 *
	 * @param   object      $table_name
	 * @param   object      $tblSendMailQueue
	 * @param   object      $tblSendMailContent
	 *
	 * @return 	boolean	    true on success
	 *
	 * @since	1.2.0
	 */
	public function onBwPostmanBeforeNewsletterSend (&$table_name, &$tblSendMailQueue, &$tblSendMailContent)
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		$tblSendMailQueue	= BwPostmanCampaignHelper::getTable('tc_sendmailqueue', 'BwPostmanTable');
		$tblSendMailContent	= BwPostmanCampaignHelper::getTable('tc_sendmailcontent', 'BwPostmanTable');
		$table_name			= '#__bwpostman_tc_sendmailqueue';

		return true;
	}

	/**
	 * Method to prepare content for listing BwTimeControl values
	 *
	 * @access	public
	 *
	 * @param 	object	$items  campaign list
	 *
	 * @return  boolean         true on success
	 *
	 * @since	1.2.0
	 */
	public function onBwPostmanCampaignsPrepare (&$items)
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		$k	= 0;
		foreach ($items as $item) {
			// Get TC-ID
			$tc_id		= BwPostmanCampaignHelper::getTcIdFromCampaign($item->id);
			if ($tc_id) {
				// Set some values if campaign has automation
				$tc_item		= BwPostmanCampaignHelper::getItem($tc_id);
				$item->tc_id	= $tc_item->tc_id;
				$item->auto		= $tc_item->automailing;
				$item->active	= $tc_item->active;
				$k++;
			}
			else {
				$item->auto		= 0;
				$item->active	= 0;
			}
		}
		return $k;
	}

	/**
	 * Method to prepare content for editing BwTimeControl values
	 *
	 * @access	public
	 *
	 * @param 	object	$cam_data           campaign data
	 * @param 	object	$newsletters        newsletters lists
	 * @param 	object	$document           document
	 *
	 * @return 	boolean 	true on success
	 *
	 * @since	1.2.0
	 */
	public function onBwPostmanCampaignPrepare (&$cam_data, &$newsletters, &$document)
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}
		JFactory::getApplication()->setUserState('bwtimecontrol.campaign_id', $cam_data->id);
		$cam_data_nl_edit = JFactory::getApplication()->getUserState('bwtimecontrol.cam_data.nl_edit', null);

		// Get ID, item and initialize
		$id			= BwPostmanCampaignHelper::getTcIdFromCampaign($cam_data->id);
		$item		= BwPostmanCampaignHelper::getItem($id);

		if (is_object($cam_data_nl_edit)) {
			$item->automailing_values		= json_decode($cam_data_nl_edit->automailing_values);
			$cam_data->automailing_values	= $cam_data_nl_edit->automailing_values;
			$cam_data->chaining				= $cam_data_nl_edit->chaining;
		}
		else {
			$item->automailing_values		= json_decode($item->automailing_values);
			$cam_data->automailing_values	= $item->automailing_values;
			$cam_data->chaining				= $item->chaining;
		}

		// get HTML code for autovalues tab
		$cam_data->tc_mailing_data	= BwPostmanCampaignHelper::buildAutovaluesTab($item, $document, $this->params);

		// get HTML code for autoqueue tab
		$cam_data->queued_letters	= BwPostmanCampaignHelper::buildAutoqueueTab($cam_data->id);
		$newsletters->sent			= BwPostmanCampaignHelper::getAutoletters($cam_data->id)->sent_queue;

		// set state of campaign data before edit
		JFactory::getApplication()->setUserState('bwtimecontrol.campaign.old_data', $cam_data);

		// reset state
		JFactory::getApplication()->setUserState('bwtimecontrol.cam_data.nl_edit', null);
		return true;
	}

	/**
	 * Method to suspend a queued newsletter from sending
	 *
	 * @access	public
	 *
	 * @param 	int		    $get_data
	 *
	 * @return  boolean     true on success
	 *
	 * @since	1.2.0
	 */
	public function onBwPostmanCampaignTaskSuspendNewsletterFromSending (&$get_data)
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		$id			= $get_data['id'];
		$suspended	= $get_data['suspended'];

		if (!$id) {
			if ($suspended) {
				$msg	= JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_SUSPEND_REACTIVATE_NO_ID');
			}
			else {
				$msg	= JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_SUSPEND_SUSPEND_NO_ID');
			}
			$type	= 'error';
			JFactory::getApplication()->enqueueMessage($msg, $type);
			return false;
		}

		$res	= BwPostmanCampaignHelper::suspendNewsletterFromSending($id, $suspended);
		if ($res) {
			if ($suspended) {
				$msg	= JText::sprintf('PLG_BWPOSTMAN_BWTIMECONTROL_NEWSLETTER_SUSPEND_REACTIVATE_DONE', $id);
			}
			else {
				$msg	= JText::sprintf('PLG_BWPOSTMAN_BWTIMECONTROL_NEWSLETTER_SUSPEND_SUSPEND_DONE', $id);
			}
			$type	= 'message';
			JFactory::getApplication()->enqueueMessage($msg, $type);
			return false;
		}
		else {
			if ($suspended) {
				$msg	= JText::sprintf('PLG_BWPOSTMAN_BWTIMECONTROL_NEWSLETTER_SUSPEND_REACTIVATE_FAILED', $id);
			}
			else {
				$msg	= JText::sprintf('PLG_BWPOSTMAN_BWTIMECONTROL_NEWSLETTER_SUSPEND_SUSPEND_FAILED', $id);
			}
			$type	= 'error';
			JFactory::getApplication()->enqueueMessage($msg, $type);
			return false;
		}
	}

	/**
	 * Method to save BwTimeControl data of a campaign
	 *
	 * @access	public
	 *
	 * @param 	int		$campaign_id
	 *
	 * @return 	boolean 	true on success
	 *
	 * @since	1.2.0
	 */
	public function onBwPostmanCampaignSave ($campaign_id)
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		// get "old" campaign data before edit
		$old_cam_data	= JFactory::getApplication()->getUserState('bwtimecontrol.campaign.old_data', null);

		// get "new" data to save
		$jinput	= JFactory::getApplication()->input;
		$table	= BwPostmanCampaignHelper::getTable();

		$data		= $jinput->get('jform', array(), 'array');
		$am_values	= $jinput->get('automailing_values', array((array) 'day', (array) 'hour', (array) 'minute', (array) 'nl_id'), 'array');

		if ($data['automailing'] == 1) {
			// if automailing values exists
			if ($am_values) {
				// remove rows with empty newsletters from automailing values
				foreach ($am_values['nl_id'] as $key => $value) {
					if ($value == '0') {
						unset ($am_values['day'][$key]);
						unset ($am_values['hour'][$key]);
						unset ($am_values['minute'][$key]);
						unset ($am_values['nl_id'][$key]);
					}
				}
				$am_values['day']		= array_merge($am_values['day']);
				$am_values['hour']		= array_merge($am_values['hour']);
				$am_values['minute']	= array_merge($am_values['minute']);
				$am_values['nl_id']		= array_merge($am_values['nl_id']);

				// Convert the automailing values to a JSON string.
				$am_values = json_encode ($am_values);
			}

			// merge data to save
			$plg_data	= array();
			foreach ($data as $key => $value) {
				switch ($key) {
					case 'rules':
					case 'description':
					case 'title':
					case 'asset_id':
						break;
					case 'id':				$plg_data['campaign_id'] = $value;
						break;
					case 'created_date':	$plg_data['created'] = $value;
						break;
					case 'modified_time':	$plg_data['modified'] = $value;
						break;
					case 'archive_time':	$plg_data['archive_date'] = $value;
						break;
					default:				$plg_data[$key]	= $value;
				}
			}
			$plg_data['automailing_values']	= $am_values;

			// Bind the data.
			if (!$table->bind($plg_data))
			{
				$this->setError($table->getError());
				return false;
			}

			// Check the data.
			if (!$table->check())
			{
				$this->setError($table->getError());
				return false;
			}

			// Store the data.
			if (!$table->store())
			{
				$this->setError($table->getError());
				return false;
			}
			BwPostmanCampaignHelper::getItem(BwPostmanCampaignHelper::getTcIdFromCampaign($campaign_id));

			// Fill tc_sendmailcontent
			if (!BwPostmanCampaignHelper::storeCampaign($plg_data)) {
				return false;
			}

			// if we come from edit (not new) campaign, check for changes and process them if necessary
			if (is_object($old_cam_data)) {
				if ($old_cam_data->id != 0) {
					BwPostmanCampaignHelper::processChanges($plg_data);
				}
			}


		}
		return true;
	}

	/**
	 * Method to redirect back to newsletters list after editing campaign, if newsletter changed his campaign
	 *
	 * @access	public
	 *
	 * @return 	boolean	    true on success
	 *
	 * @since	1.2.0
	 */
	public function onBwPostmanAfterCampaignControllerSave ()
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}
		BwPostmanCampaignHelper::processChangesOfNewsletterEdit();
		$app		= JFactory::getApplication();
		$controller	= JControllerLegacy::getInstance('BwPostman');

		// get redirect states
		$returnlink	= $app->getUserState('bwtimecontrol.newsletter.save.returnlink', null);

		if ($returnlink) {
			$controller->setRedirect($returnlink);
		}
		// unset redirect states
		$app->setUserState('bwtimecontrol.newsletter.save.returnlink', null);
		return true;
	}

	/**
	 * Method to set state with newsletter data before it is edited (old data)
	 *
	 * @access	public
	 *
	 * @param   object      $item
	 * @param   string      $referrer
	 *
	 * @return 	bool	    true on success
	 *
	 * @since	1.2.0
	 */
	public function onBwPostmanBeforeNewsletterEdit (&$item, $referrer)
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		// Set state for old NL-data only if calling from newsletters list view
		if ($referrer == 'newsletters') {
			JFactory::getApplication()->setUserState('bwtimecontrol.newsletter.old_data', $item);
		}
		return true;
	}

	/**
	 * Method to redirect to edit campaign, if newsletter changes his campaign at task save
	 *
	 * @access	public
	 *
	 * @return 	bool	true on success
	 *
	 * @since	1.2.0
	 */
	public function onBwPostmanAfterNewsletterSave ()
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}
		BwPostmanCampaignHelper::processChangesOfNewsletterEdit();
		return  true;
	}

	/**
	 * Method to redirect to edit campaign, if newsletter changes his campaign at task cancel
	 *
	 * @access	public
	 *
	 * @return 	bool	true on success
	 *
	 * @since	1.2.0
	 */
	public function onBwPostmanAfterNewsletterCancel ()
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}
		BwPostmanCampaignHelper::processChangesOfNewsletterEdit();
		return true;
	}

	/**
	 * Method to copy a newsletter that belongs to a timecontrolled campaign
	 *
	 * @access	public
	 *
	 * @return 	bool	true on success
	 *
	 * @since	1.2.0
	 */
	public function onBwPostmanAfterNewsletterCopy ()
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}
		BwPostmanCampaignHelper::processChangesOfNewsletterEdit();
		return true;
	}

	/**
	 * Method to check, if archiving newsletters is possible. Newsletters of a timecontrolled campaign may not be archived
	 *
	 * @access	public
	 *
	 * @param 	array	$cid        Newsletter-IDs
	 * @param	string	$msg        return message
	 * @param 	bool	$res        result
	 *
	 * @return boolean              true on success
	 *
	 * @since	1.2.0
	 */
	public function onBwPostmanBeforeNewsletterArchive (&$cid, &$msg, &$res)
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		foreach ($cid as $id)
		{
			// get newsletter item
			$nl_data	= BwPostmanCampaignHelper::getItem($id);

			// get TC-ID of campaign
			$tc_id	= BwPostmanCampaignHelper::getTcIdFromCampaign($nl_data->campaign_id);

			if ($tc_id !== null) {
				$res	= false;
				$msg	= JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_ERROR_ARCHIVING');
			}
		}
		return true;
	}

	/**
	 * Method to update table tc_sendmail_content after a newsletter is edited
	 *
	 * @access	public
	 *
	 * @param   object  $item
	 *
	 * @return 	bool	true on success
	 *
	 * @since	1.2.0
	 */
	public function onBwPostmanAfterNewsletterModelSave (&$item)
	{
		// Sanity check :)
		if (!$this->_enabled)
		{
			return false;
		}

		// Set current newsletter data to 'new' state every time we come to edit newsletter
		JFactory::getApplication()->setUserState('bwtimecontrol.newsletter.new_data', $item);

		$nl_id			= $item['id'];
		$campaign_id	= $item['campaign_id'];

		$app	= JFactory::getApplication();
		$jinput	= $app->input;
		$task	= $jinput->getString('task', null);

		// reaction to changes in newsletter only if editing is finished
		if ($task == 'save') {
			$nl_data_old	= JFactory::getApplication()->getUserState('bwtimecontrol.newsletter.old_data');
			$was_campaign	= $nl_data_old->campaign_id;
			$controller		= JControllerLegacy::getInstance('BwPostman');
			$tc_id_new		= BwPostmanCampaignHelper::getTcIdFromCampaign($campaign_id);
			$tc_id_old		= BwPostmanCampaignHelper::getTcIdFromCampaign($was_campaign);

			// if newsletter comes new to this campaign, redirect to edit campaign to insert new newsletter.
			if (($campaign_id != $was_campaign) && $campaign_id > 0 && ($tc_id_new > 0)) {
				$app->setUserState('bwtimecontrol.newsletter.save.link', 'index.php?option=com_bwpostman&task=campaign.edit&id=' . $campaign_id);
				$app->setUserState('bwtimecontrol.newsletter.save.msg', JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_REDIRECT_NEW_NEWSLETTER'));
				$app->setUserState('bwtimecontrol.newsletter.save.type', 'warning');
				$app->setUserState('bwtimecontrol.newsletter.save.returnlink', 'index.php?option=com_bwpostman&view=newsletters');
			}
			// if newsletter goes out of old campaign, redirect to edit old campaign to delete going newsletter.
			if (($campaign_id != $was_campaign) && $was_campaign > 0 && ($tc_id_old > 0)) {
				$app->setUserState('bwtimecontrol.newsletter.save.link', 'index.php?option=com_bwpostman&task=campaign.edit&id=' . $was_campaign);
				$app->setUserState('bwtimecontrol.newsletter.save.msg', JText::_('PLG_BWPOSTMAN_BWTIMECONTROL_REDIRECT_OLD_NEWSLETTER'));
				$app->setUserState('bwtimecontrol.newsletter.save.type', 'warning');
				$app->setUserState('bwtimecontrol.newsletter.save.returnlink', 'index.php?option=com_bwpostman&view=newsletters');
			}
			// if newsletter belongs to same campaign like before the editing and campaign has automation, check for changed properties of NL
			if (($campaign_id == $was_campaign) && ($campaign_id > 0) && ($tc_id_new > 0))  {
				$_db	= JFactory::getDbo();
				$query	= $_db->getQuery(true);
dump ($nl_data_old, 'old NL-Data');
dump ($task, 'Task');

				// check if campaign is already sent
				// if so, update tc_sendmail_content. Handling depends on newsletter already sent or not

				// get newsletter data from newsletters table
				$query->select('*');
				$query->from($_db->quoteName('#__bwpostman_newsletters'));
				$query->where($_db->quoteName('id') . ' = ' . (int) $nl_id);
				$_db->setQuery($query);

				$nl_data	= $_db->loadObject();
dump ($nl_data, 'Event NL-Data');

				// get tc_content_data list from tc_sendmail_content table
				$query->clear();
				$query->select($_db->quoteName('id'));
				$query->select($_db->quoteName('sent'));
				$query->select($_db->quoteName('attachment'));
				$query->select($_db->quoteName('mode'));
				$query->select($_db->quoteName('mail_number'));
				$query->from($_db->quoteName('#__bwpostman_tc_sendmailcontent'));
				$query->where($_db->quoteName('nl_id') . ' = ' . (int) $nl_data->id);
				$query->where($_db->quoteName('campaign_id') . ' = ' . (int) $nl_data->campaign_id);
				$query->where($_db->quoteName('old') . ' = ' . (int) 0);
				$query->order($_db->quoteName('mode') . ' ASC');
				$_db->setQuery($query);

				if (!$_db->query()) {
					JError::raiseError(500, $_db->getErrorMsg());
				}

				$sent_list	= $_db->loadAssocList();
dump ($sent_list, 'Sent List 1');
				// check if sent is set
				$sent		= 0;
				foreach ($sent_list as $key) {
					$sent += $key['sent'];
				}

				// if already sent...
				if ($sent > 0) {
					//...set flag "old" and for safety's sake flag "sent" (maybe there is only sent one of HTML or text)...
					foreach ($sent_list as $key) {
						$query	= $_db->getQuery(true);
						$query->update($_db->quoteName('#__bwpostman_tc_sendmailcontent'));
						$query->set($_db->quoteName('old').' = '. (int) 1);
						$query->set($_db->quoteName('sent').' = '. (int) 1);
						$query->where($_db->quoteName('id').' = '.(int) $key['id']);
						$_db->setQuery($query);
						if (!$_db->query()) {
							JError::raiseError(500, $_db->getErrorMsg());
						}
						// set mail-id
						$nl_data->mail_id = $key['mail_number'];
					}
					// ... and generate a new tc-entry
					$ret = BwPostmanCampaignHelper::newTcContent($nl_data);
				}
				else {
					//if not sent, only update this entries
					foreach ($sent_list as $key) {
						// Get Newsletter-Model
						$nl_model	= $controller->getModel('newsletter');

						// Preprocess html and text version of the newsletter
						if (!BwPostmanHelper::replaceLinks($nl_data->text_version)) return false;
						if (!BwPostmanHelper::replaceLinks($nl_data->html_version)) return false;
						if (!$nl_model->_addHtmlTags($nl_data->text_version)) return false;

//dump ($key, 'Key');
						$query	= $_db->getQuery(true);
						$query->update($_db->quoteName('#__bwpostman_tc_sendmailcontent'));
						if ($key['id'] == 0) {
							$query->set($_db->quoteName('body').' = '.$_db->quote($nl_data->text_version));
						}
						else {
							$query->set($_db->quoteName('body').' = '.$_db->quote($nl_data->html_version));
						}

						$query->set($_db->quoteName('attachment').' = '.$_db->quote($nl_data->attachment));
						$query->where($_db->quoteName('id').' = '. (int) $key['id']);
						$_db->setQuery($query);
						if (!$_db->query()) {
							JError::raiseError(500, $_db->getErrorMsg());
						}
					}

				}
			}
		}
		// ...else nothing to do
		return true;
	}
}
