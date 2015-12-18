<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman subscribers-lists table for backend.
 *
 * @version 1.3.0 bwpm
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
 * #__bwpostman_subscribers_mailinglists table handler
 * Table for storing the subscriber data
 *
 * @package		BwPostman-Admin
 * @subpackage	Subscribers
 */
class BwPostmanTableSubscribers_Mailinglists extends JTable
{
	/** @var int Primary Key subscriber-id*/
	var $subscriber_id = null;

	/** @var int Primary Key list-id */
	var $mailinglist_id = null;

	/**
	 * Constructor
	 *
	 * @param db Database object
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__bwpostman_subscribers_mailinglists', 'subscriber_id', $db);
	}

	/**
	 * Overloaded bind function
	 *
	 * @access public
	 * @param object Named array
	 * @param string Space separated list of fields not to bind
	 * @return boolean
	 */
	public function bind($data, $ignore='')
	{
		// Bind the rules.
		if (is_object($data)) {
			if (property_exists($data, 'rules') && is_array($data->rules))
			{
				$rules = new JAccessRules($data->rules);
				$this->setRules($rules);
			}
		}
		elseif (is_array($data)) {
			if (array_key_exists('rules', $data) && is_array($data['rules']))
			{
				$rules = new JAccessRules($data['rules']);
				$this->setRules($rules);
			}
		}
		else {
			$e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_BIND_FAILED_INVALID_SOURCE_ARGUMENT', get_class($this)));
			$this->setError($e);
			return false;
		}

		// Cast properties
		$this->id	= (int) $this->id;

		return parent::bind($data, $ignore);
	}
}
