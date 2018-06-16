<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletters lists table for backend.
 *
 * @version %%version_number%% build %%build_number%%
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/bwpostman.html
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
defined('_JEXEC') or die('Restricted access');

/**
 * #__bwpostman_newsletters_mailinglists table handler
 * Table for storing the mailinglists to which a newsletter shall be send
 *
 * @package		BwPostman-Admin
 *
 * @subpackage	Newsletters
 *
 * @since       0.9.1
 */
class BwPostmanTableNewsletters_Mailinglists extends JTable
{
	/**
	 * @var int Primary Key Newsletter-ID
	 *
	 * @since       0.9.1
	 */
	public $newsletter_id = null;

	/**
	 * @var int Primary Key Mailinglist-ID
	 *
	 * @since       0.9.1
	 */
	public $mailinglist_id = null;

	/**
	 * Constructor
	 *
	 * @param 	JDatabaseDriver  $db Database object
	 *
	 * @since       0.9.1
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__bwpostman_newsletters_mailinglists', 'newsletter_id', $db);
	}

	/**
	 * Method to copy the entries of this table for one or more newsletters
	 *
	 * @access	public
	 *
	 * @param 	int $oldid      ID of the existing newsletter
	 * @param 	int $newid      ID of the copied newsletter
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function copyLists($oldid, $newid)
	{
		$lists      = array();
		$_db		= $this->_db;
		$query		= $_db->getQuery(true);
		$subQuery	= $_db->getQuery(true);

		$subQuery->select($_db->quote($newid) . ' AS ' . $_db->quoteName('newsletter_id'));
		$subQuery->select($_db->quoteName('mailinglist_id'));
		$subQuery->from($_db->quoteName($this->_tbl));
		$subQuery->where($_db->quoteName('newsletter_id') . ' = ' . (int) $oldid);
		$_db->setQuery($subQuery);

		try
		{
			$lists		= $_db->loadAssocList();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		foreach ($lists as $list)
		{
			$query->clear();
			$query->insert($_db->quoteName($this->_tbl));
			$query->columns(
				array(
				$_db->quoteName('newsletter_id'),
				$_db->quoteName('mailinglist_id')
				)
			);
			$query->values(
				(int) $list['newsletter_id'] . ',' .
					(int) $list['mailinglist_id']
			);
			$_db->setQuery($query);

			try
			{
				$_db->execute();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_NL_COPY_MAILINGLISTS_FAILED'), 'error');
			}
		}

		return true;
	}
}
