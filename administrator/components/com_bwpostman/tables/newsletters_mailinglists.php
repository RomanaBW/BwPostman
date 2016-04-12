<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletters lists table for backend.
 *
 * @version 1.3.2 bwpm
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

/**
 * #__bwpostman_newsletters_mailinglists table handler
 * Table for storing the mailinglists to which a newsletter shall be send
 *
 * @package		BwPostman-Admin
 * @subpackage	Newsletters
 */
class BwPostmanTableNewsletters_Mailinglists extends JTable
{
	/** @var int Primary Key Newsletter-ID */
	var $newsletter_id = null;

	/** @var int Primary Key Mailinglist-ID */
	var $mailinglist_id = null;

	/**
	 * Constructor
	 *
	 * @param 	JDatabaseDriver  $db Database object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__bwpostman_newsletters_mailinglists', 'newsletter_id', $db);
	}

	/**
	 * Method to copy the entries of this table for one or more newsletters
	 *
	 * @access	public
	 * @param 	int $oldid      ID of the existing newsletter
	 * @param 	int $newid      ID of the copied newsletter
	 * @return 	boolean
	 */
	public function copyLists($oldid, $newid)
	{
		$_db		= $this->_db;
		$query		= $_db->getQuery(true);
		$subQuery	= $_db->getQuery(true);

		$subQuery->select($_db->Quote($newid)  . ' AS ' . $_db->quoteName('newsletter_id'));
		$subQuery->select($_db->quoteName('mailinglist_id'));
		$subQuery->from($_db->quoteName($this->_tbl));
		$subQuery->where($_db->quoteName('newsletter_id') . ' = ' . (int) $oldid);
		$_db->setQuery($subQuery);

		$lists		= $_db->loadAssocList();

		foreach ($lists as $list) {
			$query->clear();
			$query->insert($_db->quoteName($this->_tbl));
			$query->columns(array(
				$_db->quoteName('newsletter_id'),
				$_db->quoteName('mailinglist_id')
				));
			$query->values(
					(int) $list['newsletter_id'] . ',' .
					(int) $list['mailinglist_id']
				);
			$_db->setQuery($query);

			if (!$_db->query()){
			JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_COPY_MAILINGLISTS_FAILED'), 'error');
			return false;
			}
		}
		return true;
	}
}
