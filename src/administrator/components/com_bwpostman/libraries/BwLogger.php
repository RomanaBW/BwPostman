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

namespace BoldtWebservice\Component\BwPostman\Administrator\Libraries;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Log\LogEntry;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Component\ComponentHelper;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use RuntimeException;
use Joomla\CMS\Log\Logger\W3cLogger;



/**
 * Basic logging class implemented by every logger of BwPostman
 *
 * @since 2.0.0
 */
class BwLogger extends W3cLogger implements LoggerAwareInterface
{
	use LoggerAwareTrait;

	/**
	 * Action must be taken immediately.
	 *
	 * @var    integer
	 * @since  3.0.0
	 */
	const BW_ERROR = 1;

	/**
	 * Warning conditions
	 *
	 * @var    integer
	 * @since  3.0.0
	 */
	const BW_WARNING = 2;

	/**
	 * Informational message.
	 *
	 * @var    integer
	 * @since  3.0.0
	 */
	const BW_INFO = 4;

	/**
	 * Debugging message.
	 *
	 * @var    integer
	 * @since  3.0.0
	 */
	const BW_DEBUG = 8;

	/**
	 * Trace message.
	 *
	 * @var    integer
	 * @since  3.0.0
	 */
	const BW_DEVELOPMENT = 16;

	/**
	 * Translation array for LogEntry priorities to text strings.
	 *
	 * @var    array
	 * @since  3.0.1
	 */
	protected $priorities = array(
		self::BW_ERROR       => 'BW_ERROR',
		self::BW_WARNING     => 'BW_WARNING',
		self::BW_INFO        => 'BW_INFO',
		self::BW_DEBUG       => 'BW_DEBUG',
		self::BW_DEVELOPMENT => 'BW_DEVELOPMENT',
	);


	/**
	 * Stores the singleton instances of BwLogger.
	 *
	 * @var    BwLogger
	 *
	 * @since  3.0.0
	 */
	protected static $instances = array();

	/**
	 * Constructor.
	 *
	 * @param   array  &$options Log object options.
	 * @param string    $name    The logger name
	 *
	 * @since   2.0.0
	 */
	public function __construct(array &$options, string $name = 'bwLogger')
	{
		// The name of the text file defaults to 'bwpostman/BwPostman.log' if not explicitly given, based on log folder of Joomla.
		if (empty($options['text_file']))
		{
			$options['text_file'] = 'bwpostman/BwPostman.log';
		}

		// Call the parent constructor.
		parent::__construct($options);
	}

	/**
	 * Returns the global BwLogger object, only creating it if it
	 * doesn't already exist.
	 *
	 * @param   array  &$options Log object options.
	 * @param string    $name    The name of the toolbar.
	 *
	 * @return  BwLogger  The BwLogger object.
	 *
	 * @since   3.0.0
	 */
	public static function getInstance(array &$options, string $name = 'bwLogger'): BwLogger
	{
		if (empty(self::$instances[$name]))
		{
			self::$instances[$name] = new BwLogger($options, $name);
		}

		return self::$instances[$name];
	}

	/**
	 * Method to add an entry to the log.
	 *
	 * @param   LogEntry  $entry  The log entry object to add to the log.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 *
	 * @throws  RuntimeException
	 */
	public function addEntry(LogEntry $entry)
	{
		// Get component option loglevel
		$param    = ComponentHelper::getParams('com_bwpostman');
		$loglevel = $param->get('loglevel', self::BW_ERROR);

		// Rewrite Joomla default loglevel (Info) to the one of BwPostman (Info)
		if ($entry->priority > 16)
		{
			$entry->priority = 4;
		}

		$write = false;

		// Check if priority meets selected loglevel
		switch ($loglevel)
		{
			case "BW_ERROR":
				if ($entry->priority <= 1)
				{
					$write = true;
				}
				break;
			case "BW_WARNING":
				if ($entry->priority <= 2)
				{
					$write = true;
				}
				break;
			case "BW_INFO":
				if ($entry->priority <= 4)
				{
					$write = true;
				}
				break;
			case "BW_DEBUG":
				if ($entry->priority <= 8)
				{
					$write = true;
				}
				break;
			case "BW_DEVELOPMENT":
				if ($entry->priority <= 16)
				{
					$write = true;
				}
				break;
			default:
				break;
		}

		// Write entry to log file, if priority meets set loglevel, else discard entry
		if ($write === true)
		{
			// Initialise the file if not already done.
			$this->initFile();

			// Set some default field values if not already set.
			if (!isset($entry->clientIP))
			{
				// Check for proxies as well.
				if (isset($_SERVER['REMOTE_ADDR']))
				{
					$entry->clientIP = $_SERVER['REMOTE_ADDR'];
				}
				elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
				{
					$entry->clientIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
				}
				elseif (isset($_SERVER['HTTP_CLIENT_IP']))
				{
					$entry->clientIP = $_SERVER['HTTP_CLIENT_IP'];
				}
			}

			// If the time field is missing or the date field isn't only the date we need to rework it.
			if ((strlen($entry->date) != 10) || !isset($entry->time))
			{
				// Get the date and time strings in GMT.
//				$entry->datetime = $entry->date->toISO8601();
				$entry->time     = $entry->date->format('H:i:s', false);
				$entry->date     = $entry->date->format('Y-m-d', false);
			}

			// Get a list of all the entry keys and make sure they are upper case.
			$tmp = array_change_key_case(get_object_vars($entry), CASE_UPPER);

			// Decode the entry priority into an English string.
			$tmp['PRIORITY'] = $this->priorities[$entry->priority];

			// Fill in field data for the line.
			$line = $this->format;

			foreach ($this->fields as $field)
			{
				$line = str_replace('{' . $field . '}', (isset($tmp[$field])) ? $tmp[$field] : '-', $line);
			}

			// Write the new entry to the file.
			$line .= "\n";

			if (!File::append($this->path, $line))
			{
				throw new RuntimeException('Cannot write to log file.');
			}
		}
	}
}
