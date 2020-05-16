<?php
/**
 * BwTimeControl Plugin for BwPostman Newsletter Component
 *
 * BwTimeControl automailing values for campaigns table for backend.
 *
 * @version 2.0.0 bwplgtc
 * @package BwPostman BwTimeControl Plugin
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\Database\DatabaseDriver;

/**
 * #__bwpostman_campaigns table handler
 * Table for storing the campaign data
 *
 * @package		BwPostman BwTimeControl Plugin
 *
 * @since       2.3.0
 */
class BwPostmanTableTc_Schedule extends JTable
{
	/**
	 * Primary key
	 *
	 * @var int $newsletter_id
	 *
	 * @since       2.3.0
	 */
	var $newsletter_id = null;

	/**
	 * @var datetime Checked-out time
	 *
	 * @since       2.3.0
	 */
	var $scheduled_date = '0000-00-00 00:00:00';

	/**
	 * @var int ready to send
	 *
	 * @since       2.3.0
	 */
	public $ready_to_send = 0;

	/**
	 * @var int sent
	 *
	 * @since       2.3.0
	 */
	public $sent = 0;

	/**
	 * Constructor
	 *
	 * @param 	DatabaseDriver  $db Database object
	 *
	 * @since       2.3.0
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__bwpostman_tc_schedule', 'newsletter_id', $db);
	}

	/**
	 * Returns the identity (primary key) value of this record
	 *
	 * @return  mixed
	 *
	 * @since  2.4.0
	 */
	public function getId()
	{
		$key = $this->getKeyName();

		return $this->$key;
	}

	/**
	 * Check if the record has a property (applying a column alias if it exists)
	 *
	 * @param string $key key to be checked
	 *
	 * @return  boolean
	 *
	 * @since   2.4.0
	 */
	public function hasField($key)
	{
		$key = $this->getColumnAlias($key);

		return property_exists($this, $key);
	}
}
