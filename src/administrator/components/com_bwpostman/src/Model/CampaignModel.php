<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single campaign model for backend.
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

namespace BoldtWebservice\Component\BwPostman\Administrator\Model;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Plugin\PluginHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanCampaignHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanMailinglistHelper;
use RuntimeException;
use stdClass;

/**
 * BwPostman campaign model
 * Provides methods to add and edit campaigns
 *
 * @package		BwPostman-Admin
 *
 * @subpackage	Campaigns
 *
 * @since       0.9.1
 */
class CampaignModel extends AdminModel
{
	/**
	 * Campaign ID
	 *
	 * @var integer
	 *
	 * @since       0.9.1
	 */
	private $id = null;

	/**
	 * Campaign data
	 *
	 * @var array
	 *
	 * @since       0.9.1
	 */
	private $data = null;

	/**
	 * All mailinglists
	 *
	 * @var array
	 *
	 * @since       3.0.0
	 */
	private $mailinglists = array();

	/**
	 * All mailinglists
	 *
	 * @var array
	 *
	 * @since       3.0.0
	 */
	private $ml_available = array();

	/**
	 * Normally unavailable mailinglists
	 *
	 * @var array
	 *
	 * @since       3.0.0
	 */
	private $ml_unavailable = array();

	/**
	 * Internal mailinglists
	 *
	 * @var array
	 *
	 * @since       3.0.0
	 */
	private $ml_intern = array();

	/**
	 * Associated usergroups
	 *
	 * @var array
	 *
	 * @since       3.0.0
	 */
	private $usergroups = array();

	/**
	 * Constructor
	 * Determines the campaign ID
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function __construct()
	{
		$jinput	= Factory::getApplication()->input;

		parent::__construct();

		$cids = $jinput->get('cid',  array(0), '');
		$this->setId((int) $cids[0]);
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	string $name    The table type to instantiate
	 * @param	string $prefix  A prefix for the table class name. Optional.
	 * @param	array  $options Configuration array for model. Optional.
	 *
	 * @return	Table|boolean   A Table object if found or boolean false on failure.
	 *
	 * @throws Exception
	 *
	 * @since  1.0.1
	 */
	public function getTable($name = 'Campaign', $prefix = 'Administrator', $options = array())
	{
		return parent::getTable($name, $prefix, $options);
	}

	/**
	 * Method to reset the campaign ID and campaign data
	 *
	 * @param int $id Campaign ID
	 *
	 * @since       0.9.1
	 */
	public function setId(int $id)
	{
		$this->id   = $id;
		$this->data = null;
	}

	/**
	 * Method to test whether the state of a record can be changed
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to change state of the record.
	 *
	 * @since	1.0.1
	 *
	 * @throws Exception
	 */
	protected function canEditState($record): bool
	{
		return BwPostmanHelper::canEditState('campaign', (int) $record->id);
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  bool|CMSObject|stdClass    Data object on success, false on failure.
	 *
	 * @throws Exception
	 *
	 * @since   1.0.1
	 */
	public function getItem($pk = null)
	{
		$app	= Factory::getApplication();
		$data	= $app->getUserState('com_bwpostman.edit.campaign.data');
		$id     = 0;

		$pk = (int)(!empty($pk)) ? $pk : $this->getState($this->getName() . '.id');

		if (is_object($data) && property_exists($data, 'id'))
		{
			$id = $data->id;
		}
		elseif (is_array($data) && key_exists('id', $data))
		{
			$id = $data['id'];
		}

		if (!$data || ($id != $pk))
		{
			// Initialise variables.
			$item	= parent::getItem($pk);

			if ($pk === 0)
			{
				$item->id = 0;
			}

			//get associated mailinglists
			$camMlTable = $this->getTable('CampaignsMailinglists');
			$item->mailinglists = $camMlTable->getAssociatedMailinglistsByCampaign($item->id);

			//extract associated usergroups
			$item->usergroups	= BwPostmanMailinglistHelper::extractAssociatedUsergroups($item->mailinglists);

			// get available mailinglists to predefine for state
			$mlTable = $this->getTable('Mailinglist');
			$item->ml_available = $mlTable->getMailinglistsByRestriction($item->mailinglists, 'available');

			// get unavailable mailinglists to predefine for state
			$item->ml_unavailable = $mlTable->getMailinglistsByRestriction($item->mailinglists, 'unavailable');

			// get internal mailinglists to predefine for state
			$item->ml_intern = $mlTable->getMailinglistsByRestriction($item->mailinglists, 'internal');

		}
		else
		{
			$item = new stdClass();

			foreach ($data as $key => $value)
			{
				$item->$key	= $value;
			}
		}

		$app->setUserState('com_bwpostman.edit.campaign.data', $item);

		return $item;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return    Form|false    A JForm object on success, false on failure
	 *
	 * @throws Exception
	 *
	 *@since	1.6
	 *
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form     = $this->loadForm('com_bwpostman.campaign', 'Campaign', array('control' => 'jform', 'load_data' => $loadData));
		$nullDate = $this->_db->getNullDate();

		if (empty($form))
		{
			return false;
		}

		// Check to show created data
		$c_date	= $form->getValue('created_date');

		if ($c_date === $nullDate || $c_date == null)
		{
			$form->setFieldAttribute('created_date', 'type', 'hidden');
			$form->setFieldAttribute('created_by', 'type', 'hidden');
		}

		// Check to show modified data
		$m_date	= $form->getValue('modified_time');

		if ($m_date === $nullDate || $m_date == null)
		{
			$form->setFieldAttribute('modified_time', 'type', 'hidden');
			$form->setFieldAttribute('modified_by', 'type', 'hidden');
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	object	The data for the form.
	 *
	 * @throws Exception
	 *
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		$recordId = Factory::getApplication()->getUserState('com_bwpostman.edit.campaign.id', 0);

		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_bwpostman.edit.campaign.data', []);

		if (empty($data) || (is_object($data) && $recordId != $data->id))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get all newsletters which are assigned to a specific campaign
	 *
	 * @return 	object Newsletters
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function getNewslettersOfCampaign(): object
	{
		$newsletters = new stdClass();
		$camId       = (int)$this->getState('campaign.id');

		$newsletters->sent   = BwPostmanCampaignHelper::getSelectedNewslettersOfCampaign($camId, true, false);
		$newsletters->unsent = BwPostmanCampaignHelper::getSelectedNewslettersOfCampaign($camId, false, false);
		$newsletters->all    = BwPostmanCampaignHelper::getSelectedNewslettersOfCampaign($camId, false, true);

		return $newsletters;
	}

	/**
	 * Method to (un)archive a campaign and if the user want also the assigned newsletters
	 * --> when unarchiving it is called by the archive-controller
	 *
	 * @param array $cid        Campaign IDs
	 * @param int   $archive    Task --> 1 = archive, 0 = unarchive
	 * @param int   $archive_nl Archive/Unarchive assigned newsletters (0 = No, 1 = Yes)
	 *
	 * @return	boolean
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function archive(array $cid = array(0), int $archive = 1, int $archive_nl = 1): bool
	{
		$date     = Factory::getDate();
		$uid      = Factory::getApplication()->getIdentity()->get('id');
		$db       = $this->_db;
		$query    = $db->getQuery(true);

		$cid = ArrayHelper::toInteger($cid);

		if ($archive == 1)
		{
			$time = $db->quote(Factory::getDate()->toSql(), false);

			// Access check.
			foreach ($cid as $id)
			{
				if (!BwPostmanHelper::canArchive('campaign', 0, $id))
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
				if (!BwPostmanHelper::canRestore('campaign', $id))
				{
					return false;
				}
			}

			$time = 'null';
			$uid  = 0;
		}

		if (count($cid))
		{
			$query->update($db->quoteName('#__bwpostman_campaigns'));
			$query->set($db->quoteName('archive_flag') . ' = ' . $archive);
			$query->set($db->quoteName('archive_date') . ' = ' . $time);
			$query->set($db->quoteName('archived_by') . " = " . (int) $uid);
			$query->where($db->quoteName('id') . ' IN (' . implode(',', $cid) . ')');

			try
			{
				$db->setQuery($query);
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_CAM_RESTORE_RIGHTS_MISSING'), 'error');
				return false;
			}

			// Archive_nl = 1 if the user want to (un)archive the assigned newsletters
			if ($archive_nl)
			{
				$query->clear();
				$query->update($db->quoteName('#__bwpostman_newsletters'));
				$query->set($db->quoteName('archive_flag') . ' = ' . $archive);
				$query->set($db->quoteName('archive_date') . ' = ' . $db->quote($time, false));
				$query->set($db->quoteName('archived_by') . " = " . (int) $uid);
				$query->where($db->quoteName('campaign_id') . ' IN (' . implode(',', $cid) . ')');

				try
				{
					$db->setQuery($query);
					$db->execute();
				}
				catch (RuntimeException $e)
				{
					Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Method to override save function
	 *
	 * @param	array	$data	A campaign object.
	 *
	 * @return	boolean	True if allowed to save the record. Defaults to the permission set in the component.
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	public function save($data): bool
	{
		$app = Factory::getApplication();

		// merge ml-arrays, single array may not exist, therefore array_merge would not give a result
		BwPostmanMailinglistHelper::mergeMailinglists($data);

		if (isset($data['mailinglists']))
		{
			$res = parent::save($data);

			if ($res)
			{
				// Delete all entries of the newsletter from newsletters_mailinglists table
				if ($data['id'])
				{
					$this->getTable('CampaignsMailinglists')->deleteCampaignsMailinglistsEntry((int)$data['id']);
				}
				else
				{
					$jinput = $app->input;
					//get id of new inserted data to write cross table newsletters-mailinglists and inject into form
					$data['id']	= $app->getUserState('com_bwpostman.edit.campaign.id', 0);
					$jinput->set('id', $data['id']);

					// update state
					$state_data	= $app->getUserState('com_bwpostman.edit.campaign.data', new stdClass);
					$state_data->id	= $data['id'];
					$app->setUserState('com_bwpostman.edit.campaign.data', $state_data);

				}

				// Store the selected BwPostman mailinglists into campaigns_mailinglists-table
				$this->getTable('CampaignsMailinglists')->addCampaignsMailinglistsEntry($data);

				PluginHelper::importPlugin('bwpostman');

				$app->triggerEvent('onBwPostmanCampaignSave', array ($data));
			}
		}
		else
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_CAM_ERROR_NO_RECIPIENTS_SELECTED'), 'error');
			$res = false;
		}

		return $res;
	}

	/**
	 * Method to remove one or more campaigns and if the user want also the assigned newsletters
	 * --> is called by the archive-controller
	 *
	 * @param	array &$pks     Campaign IDs
	 *
	 * @return	boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function delete(&$pks): bool
	{
		$jinput	   = Factory::getApplication()->input;
		$remove_nl = $jinput->get('remove_nl', false);
		$app       = Factory::getApplication();

		if (count($pks))
		{
			$pks = ArrayHelper::toInteger($pks);

			// Access check.
			foreach ($pks as $id)
			{
				if (!BwPostmanHelper::canDelete('campaign', $id))
				{
					return false;
				}
			}

			// Delete campaigns from campaigns table
			$camsTable = $this->getTable();

			foreach ($pks as $id)
			{
				if (!$camsTable->delete($id))
				{
					$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_ARC_ERROR_REMOVING_CAMS_NO_CAM_DELETED', $id), 'error');
					return false;
				}

				// Remove campaigns mailinglists entries
				if (!$this->getTable('CampaignsMailinglists')->deleteCampaignsMailinglistsEntry((int)$id))
				{
					$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_ARC_ERROR_REMOVING_CAMS_NO_ML_DELETED', $id), 'error');
					return false;
				}

				// Remove_nl = 1 if the user want to delete the assigned newsletters
				if ($remove_nl)
				{
					if (!$this->getTable('Newsletter')->deleteCampaignsNewsletters((int)$id))
					{
						$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_ARC_ERROR_REMOVING_MLS_NO_NLS_DELETED', $id), 'error');
						return false;
					}
				}
			}
		}
		return true;
	}
}
