<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single mailinglist model for backend.
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
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use RuntimeException;
use stdClass;

/**
 * BwPostman mailinglist model
 * Provides methods to add and edit mailinglists
 *
 * @package		BwPostman-Admin
 *
 * @subpackage	Mailinglists
 *
 * @since       0.9.1
 */
class MailinglistModel extends AdminModel
{
	/**
	 * Mailinglist ID
	 *
	 * @var integer
	 *
	 * @since       0.9.1
	 */
	private $id = null;

	/**
	 * Mailinglist data
	 *
	 * @var array
	 *
	 * @since       0.9.1
	 */
	private $data = null;

	/**
	 * Constructor
	 * Determines the mailinglist ID
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function __construct()
	{
		parent::__construct();

		$jinput = Factory::getApplication()->input;
		$cids   = $jinput->get('cid',  array(0), '');
		$this->setId((int) $cids[0]);
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	string $name    The table type to instantiate
	 * @param	string $prefix  A prefix for the table class name. Optional.
	 * @param	array  $options Configuration array for model. Optional.
	 *
	 * @return	boolean|Table	A database object
	 *
	 * @throws Exception
	 *
	 * @since  1.0.1
	 */
	public function getTable($name = 'Mailinglist', $prefix = 'Administrator', $options = array())
	{
		return parent::getTable($name, $prefix, $options);
	}

	/**
	 * Method to reset the mailinglist ID and mailinglist data
	 *
	 * @access	public
	 *
	 * @param int $id Mailinglist ID
	 *
	 * @since       0.9.1
	 */
	public function setId(int $id)
	{
		$this->id   = $id;
		$this->data = null;
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param object $record A record object.
	 *
	 * @return    boolean    True if allowed to change the state of the record.
	 *
	 * @throws Exception
	 *
	 * @since    1.0.1
	 */
	protected function canEditState($record): bool
	{
		return BwPostmanHelper::canEditState('mailinglist', (int) $record->id);
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  bool|CMSObject|stdClass    Object on success, false on failure.
	 *
	 * @throws Exception
	 *
	 * @since   1.0.1
	 */
	public function getItem($pk = null)
	{
		$app  = Factory::getApplication();
		$data = $app->getUserState('com_bwpostman.edit.mailinglist.data');

		$pk = (int)(!empty($pk)) ? $pk : $this->getState($this->getName() . '.id');

		if (!$data)
		{
			$item = parent::getItem($pk);
		}
		else
		{
			$item = new stdClass();

			foreach ($data as $key => $value)
			{
				$item->$key	= $value;
			}
		}

		return $item;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return    false|Form    A JForm object on success, false on failure
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_bwpostman.mailinglist', 'mailinglist', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		$jinput = Factory::getApplication()->input;

		// The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
		if ($jinput->get('a_id'))
		{
			$id = (int)$jinput->get('a_id', 0);
		}
		// The back end uses id so we use that the rest of the time and set it to 0 by default.
		else
		{
			$id = (int)$jinput->get('id', 0);
		}

		// Determine correct permissions to check.
		if ($this->getState('mailinglist.id'))
		{
			$id = (int)$this->getState('mailinglist.id');
			// Existing record. Can only edit in selected parent.
			$form->setFieldAttribute('parent_id', 'action', 'bwpm.edit');
			// Existing record. Can only edit own mailinglists in selected parent.
			$form->setFieldAttribute('parent_id', 'action', 'bwpm.edit.own');
		}
		else
		{
			// New record. Can only create in selected parent.
			$form->setFieldAttribute('parent_id', 'action', 'bwpm.create');
		}

		$user = Factory::getApplication()->getIdentity();

		// Check for existing mailinglist.
		// Modify the form based on Edit State access controls.
		if ($id !== 0 && (!$user->authorise('bwpm.edit.state', 'com_bwpostman.mailinglist.' . $id))
			|| ($id === 0 && !$user->authorise('bwpm.mailinglist.edit.state', 'com_bwpostman')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('state', 'disabled', 'true');
			// Disable fields while saving.
			// The controller has already verified this is a mailinglist you can edit.
			$form->setFieldAttribute('state', 'filter', 'unset');
		}

		// Check to show campaign_id
		$campaign_id = (int)$jinput->get('campaign_id');

		if (empty($campaign_id))
		{
			$form->setFieldAttribute('campaign_id', 'type', 'hidden');
		}

		// Check to show created data
		$c_date   = $form->getValue('created_date');
		$nullDate = $this->_db->getNullDate();

		if ($c_date === $nullDate || $c_date === null)
		{
			$form->setFieldAttribute('created_date', 'type', 'hidden');
			$form->setFieldAttribute('created_by', 'type', 'hidden');
		}

		// Check to show modified data
		$m_date	= $form->getValue('modified_time');

		if ($m_date === $nullDate || $m_date === null)
		{
			$form->setFieldAttribute('modified_time', 'type', 'hidden');
			$form->setFieldAttribute('modified_by', 'type', 'hidden');
		}

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
		$recordId = (int)Factory::getApplication()->getUserState('com_bwpostman.edit.mailinglist.id', 0);

		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_bwpostman.edit.mailinglist.data', array());

		if (empty($data) || (is_object($data) && $recordId !== (int)$data->id))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to (un)archive a mailinglist
	 * --> when unarchiving it is called by the archive-controller
	 *
	 * @param array $cid     Mailinglist IDs
	 * @param int   $archive Task --> 1 = archive, 0 = unarchive
	 *
	 * @return	boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function archive(array $cid = array(0), int $archive = 1): bool
	{
		$db   = $this->_db;
		$uid  = Factory::getApplication()->getIdentity()->get('id');
		$cid  = ArrayHelper::toInteger($cid);

		if ($archive == 1)
		{
			$time = $db->quote(Factory::getDate()->toSql(), false);

			// Access check.
			foreach ($cid as $id)
			{
				if (!BwPostmanHelper::canArchive('mailinglist', 0, $id))
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
				if (!BwPostmanHelper::canRestore('mailinglist', $id))
				{
					return false;
				}
			}

			$time = 'null';
			$uid  = 0;
		}

		if (count($cid))
		{
			ArrayHelper::toInteger($cid);
			$query	= $db->getQuery(true);

			$query->update($db->quoteName('#__bwpostman_mailinglists'));
			$query->set($db->quoteName('archive_flag') . " = " . $db->quote($archive));
			$query->set($db->quoteName('archive_date') . " = " . $time);
			$query->set($db->quoteName('archived_by') . " = " . (int) $uid);
			$query->where($db->quoteName('id') . ' IN (' . implode(',', $cid) . ')');

			try
			{
				$db->setQuery($query);
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}

		return true;
	}

	/**
	 * Method to remove one or more mailinglists
	 * --> is called by the archive-controller
	 *
	 * @param	array &$pks     Mailinglist IDs
	 *
	 * @return	boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function delete(&$pks): bool
	{
		$app = Factory::getApplication();
		$pks = ArrayHelper::toInteger($pks);

		if (count($pks))
		{
			// Access check.
			foreach ($pks as $id)
			{
				if (!BwPostmanHelper::canDelete('mailinglist', $id))
				{
					return false;
				}
			}

			$mlTable = $this->getTable();

			// Delete all entries from the mailinglists-table
			foreach ($pks as $id)
			{
				if (!$mlTable->delete($id))
				{
					$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_ARC_ERROR_REMOVING_MLS_NO_ML_DELETED', $id), 'error');
					return false;
				}

				if (!$this->getTable('CampaignsMailinglists')->deleteMailinglistsCampaignsEntry((int)$id))
				{
					$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_ARC_ERROR_REMOVING_MLS_NO_ML_CAM_DELETED', $id), 'error');
					return false;
				}

				if (!$this->getTable('SubscribersMailinglists')->deleteMailinglistSubscribers((int)$id))
				{
					$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_ARC_ERROR_REMOVING_MLS_NO_SUBS_DELETED', $id), 'error');
					return false;
				}

				// Delete all entries from the newsletters_mailinglists-table
				if (!$this->getTable('NewslettersMailinglists')->deleteMailinglistNewsletters((int)$id))
				{
					$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_ARC_ERROR_REMOVING_MLS_NO_NLS_DELETED', $id), 'error');
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Method to (un)publish a mailinglist
	 *
	 * @param	array   &$pks   Mailinglist IDs
	 * @param	int     $value  Task --> 1 = publish, 0 = unpublish
	 *
	 * @return	boolean
	 *
	 * @since       0.9.1
	 */
	public function publish(&$pks, $value = 1): bool
	{
		if (parent::publish($pks, $value))
		{
			return true;
		}

		return false;
	}
}
