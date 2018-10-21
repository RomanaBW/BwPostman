<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman basic logging class.
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

// Import MODEL object class
jimport('libraries.joomla.log.logger.w3c');


/**
 * Basic logging class implemented by every logger of BwPostman
 *
 * @since 2.0.0
 */
class BwLogger extends JLogLoggerW3c
{
	/**
	 * Constructor.
	 *
	 * @param   array  &$options  Log object options.
	 *
	 * @since   1.3.0
	 */
	public function __construct(array &$options)
	{
		// The name of the text file defaults to 'bwpostman/BwPostman.log' if not explicitly given, based on log folder of Joomla.
		if (empty($options['text_file']))
		{
			$options['text_file'] = 'bwpostman/BwPostman.log';
		}

		// Call the parent constructor.
		parent::__construct($options);
	}
}
