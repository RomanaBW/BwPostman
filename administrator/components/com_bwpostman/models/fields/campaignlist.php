<?php

/**
 * BwPostman Newsletter Component
 *
 * BwPostman  form field campaigns class.
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

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Class JFormFieldCampaignlist
 */
class JFormFieldCampaignlist extends JFormFieldList {

	/**
	 * property to hold campaigns
	 *
	 * @var string  $type
	 */
	protected $type = 'Campaigns';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.0.8
	 */
	protected function getOptions()
	{
	    // Get a db connection.
	    $_db	= JFactory::getDbo();
	    $query	= $_db->getQuery(true);

		// Get all published campaigns
		$query->select($_db->quoteName('id') . ' AS value');
		$query->select($_db->quoteName('title') . 'AS text');
		$query->select($_db->quoteName('description') . ' AS description');
		$query->from($_db->quoteName('#__bwpostman_campaigns'));
		$query->where($_db->quoteName('archive_flag') . ' = ' . (int) 0);

		$_db->setQuery ($query);

		try
		{
			$options = $_db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage());
		}

		$parent = new stdClass;
		$parent->value	= '-1';
		$parent->text	= '- '. JText::_('COM_BWPOSTMAN_NL_FILTER_CAMPAIGN') .' -';
		array_unshift($options, $parent);

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
