<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman campaigns mailing lists cross table for backend.
 *
 * @version 1.2.4 bwpm
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

/**
 * #__bwpostman_campaigns_mailinglists table handler
 * Table for storing the mailinglists to which a campaign shall be send
 *
 * @package		BwPostman-Admin
 * @subpackage	Campaigns
 */
class BwPostmanTableCampaigns_Mailinglists extends JTable
{
	/** @var int Primary Key Campaign-ID */
	var $campaign_id = null;

	/** @var int Primary Key Mailinglist-ID */
	var $mailinglist_id = null;

	/**
	 * Constructor
	 *
	 * @param 	db Database object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__bwpostman_campaigns_mailinglists', 'campaign_id', $db);
	}

	/**
	 * Method to copy the entries of this table for one or more campaigns
	 *
	 * @access	public
	 * @param 	int ID of the existing campaign
	 * @param 	int ID of the copied campaign
	 * @return 	boolean
	 */
	public function copyLists($oldid, $newid)
	{
		$_db		= $this->_db;
		$query		= $_db->getQuery(true);
		$subQuery	= $_db->getQuery(true);
		
		$subQuery->select($_db->Quote($newid)  . ' AS ' . $_db->quoteName('campaign_id'));
		$subQuery->select($_db->quoteName('mailinglist_id'));
		$subQuery->from($_db->quoteName($this->_tbl));
		$subQuery->where($_db->quoteName('campaign_id') . ' = ' . (int) $oldid);
		$_db->setQuery($subQuery);
		
		$lists		= $_db->loadAssocList();

		foreach ($lists as $list) {
			$query->clear();
			$query->insert($_db->quoteName($this->_tbl));
			$query->columns(array(
				$_db->quoteName('campaign_id'),
				$_db->quoteName('mailinglist_id')
				));
			$query->values(
					(int) $list['campaign_id'] . ',' . 
					(int) $list['mailinglist_id']
				);
			$_db->setQuery($query);

			if (!$_db->query()){
			JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_CAM_COPY_MAILINGLISTS_FAILED'), 'error');
			return false;
			}
		}
		return true;
	}
}