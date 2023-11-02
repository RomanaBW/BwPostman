<?php

/**
 * BwPostman Newsletter Component
 *
 * BwPostman  form field campaigns class.
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

namespace BoldtWebservice\Component\BwPostman\Administrator\Field;

defined('JPATH_BASE') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use RuntimeException;
use stdClass;

/**
 * Class CampaignsField
 *
 * @since       1.0.8
 */
class CampaignsField extends ListField
{
	/**
	 * property to hold campaigns
	 *
	 * @var string  $type
	 *
	 * @since       1.0.8
	 */
	protected $type = 'Campaigns';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @throws Exception
	 *
	 * @since   1.0.8
	 */
	protected function getOptions(): array
	{
		// Get a db connection.
		$db    = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true);

		// Get all published campaigns
		$query->select($db->quoteName('id') . ' AS value');
		$query->select($db->quoteName('title') . 'AS text');
		$query->select($db->quoteName('description') . ' AS description');
		$query->from($db->quoteName('#__bwpostman_campaigns'));
		$query->where($db->quoteName('archive_flag') . ' = ' . 0);

		try
		{
			$db->setQuery($query);

			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		$parent = new stdClass;
		$parent->value	= '';
		$parent->text	= '- ' . Text::_('COM_BWPOSTMAN_NL_FILTER_CAMPAIGN') . ' -';
		array_unshift($options, $parent);

		// Merge any additional options in the XML definition.
		return array_merge(parent::getOptions(), $options);
	}
}
