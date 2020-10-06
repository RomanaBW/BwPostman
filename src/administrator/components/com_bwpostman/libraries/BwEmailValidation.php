<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman email verification class.
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

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\LogEntry;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwLogger;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwException;

/**
 * Email verification class
 *
 * - Check if the email address has valid format
 * - Get MX records of the domain of the email address
 * - Connect to the SMTP server by the MX records
 * - Based on this response code following checks are processed:
 *   - Is the given recipient email address valid?
 *   - Does the user of email’s domain exist?
 *   - Is delivery of a message possible?
 *
 * @link http://www.faqs.org/rfcs/rfc2821.html RFC 2821 - Simple Mail Transfer Protocol
 *
 * @since 3.0.0
 */
class BwEmailValidation
{
	/**
	 * Stream resource
	 *
	 * @var resource
	 *
	 * @since 3.0.0
	 */
	protected $stream = false;

	/**
	 * SMTP port number
	 *
	 * @var int
	 *
	 * @since 3.0.0
	 */
	protected $port = 25;

	/**
	 * Email address for outgoing request
	 *
	 * @var string
	 *
	 * @since 3.0.0
	 */
	protected $from = 'root@localhost';

	/**
	 * The connection timeout in seconds
	 *
	 * @var int
	 *
	 * @since 3.0.0
	 */
	protected $maxConnectionTimeout = 30;

	/**
	 * Timeout value on stream in seconds
	 *
	 * @var int
	 *
	 * @since 3.0.0
	 */
	protected $streamTimeout = 5;

	/**
	 * Wait timeout on stream in seconds
	 * * 0 - not wait
	 *
	 * @var int
	 *
	 * @since 3.0.0
	 */
	protected $streamTimeoutWait = 0;

	/**
	 * The number of errors encountered
	 *
	 * @type integer
	 *
	 * @since 3.0.0
	 */
	protected $errorCounter = 0;

	/**
	 * Instance of BwLogger
	 *
	 * @var BwLogger
	 *
	 * @since 3.0.0
	 */
	protected $logger;

	/**
	 * class debug output mode
	 *
	 * @type boolean
	 *
	 * @since 3.0.0
	 */
	public $doDebug = false;

	/**
	 * SMTP RFC standard line ending
	 *
	 * @since 3.0.0
	 */
	const CRLF = "\r\n";

	/**
	 * Holds the most recent error message
	 *
	 * @type string
	 *
	 * @since 3.0.0
	 */
	public $recentError = '';

	/**
	 * Constructor
	 *
	 * @param $logOptions
	 *
	 * @since 3.0.0
	 */
	public function __construct($logOptions)
	{
		$this->logger = BwLogger::getInstance($logOptions);
	}

	/**
	 * Set email address for SMTP request
	 *
	 * @param string $email Email address
	 *
	 * @since 3.0.0
	 */
	public function setEmailFrom($email)
	{
		if (!self::validate($email))
		{
			$message = Text::sprintf('COM_BWPOSTMAN_SUB_ERROR_VALIDATING_SENDER_EMAIL', $email);
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'mailcheck'));
		}
		$this->from = $email;
	}

	/**
	 * Set connection timeout (seconds)
	 *
	 * @param int $seconds
	 *
	 * @since 3.0.0
	 */
	public function setConnectionTimeout($seconds)
	{
		if ($seconds > 0)
		{
			$this->maxConnectionTimeout = (int) $seconds;
		}
	}

	/**
	 * Sets the timeout value on stream (seconds)
	 *
	 * @param int $seconds
	 *
	 * @since 3.0.0
	 */
	public function setStreamTimeout($seconds)
	{
		if ($seconds > 0)
		{
			$this->streamTimeout = (int) $seconds;
		}
	}

	/**
	 * Sets the timeout value on stream (seconds)
	 *
	 * @param int $seconds
	 *
	 * @since 3.0.0
	 */
	public function setStreamTimeoutWait($seconds)
	{
		if ($seconds >= 0)
		{
			$this->streamTimeoutWait = (int) $seconds;
		}
	}

	/**
	 * Check if email address is well formed
	 *
	 * @param string $email
	 *
	 * @return boolean True if valid
	 *
	 * @since 3.0.0
	 */
	public static function validate($email)
	{
		return (boolean) filter_var($email, FILTER_VALIDATE_EMAIL);
	}

	/**
	 * Get array of MX records for host, sorted by weight information.
	 *
	 * @param string $hostname The Internet host name
	 *
	 * @return array Array of the found MX records
	 *
	 * @since 3.0.0
	 */
	public function getMxRecords($hostname)
	{
		$mxHosts   = array();
		$mxWeights = array();

		$mxCheck = getmxrr($hostname, $mxHosts, $mxWeights);

		if ($mxCheck)
		{
			array_multisort($mxWeights, $mxHosts);
		}
		else
		{
			$message = Text::_('COM_BWPOSTMAN_SUB_ERROR_VALIDATING_EMAIL_NO_MX_RECORDS');
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'mailcheck'));
		}

		 // Last chance: Add A-record (i.e. if no MX record exists)
		if (empty($mxHosts))
		{
			$mxHosts[] = $hostname;
		}

		return $mxHosts;
	}

	/**
	 * Split input email to array
	 * - 0=>user
	 * - 1=>domain
	 *
	 * @param string  $email
	 * @param boolean $domainOnly
	 *
	 * @return string|array
	 *
	 * @since 3.0.0
	 */
	public static function parseEmail($email, $domainOnly = true)
	{
		sscanf($email, "%[^@]@%s", $user, $domain);

		if ($domainOnly)
		{
			return $domain;
		}

		return array($user, $domain);
	}

	/**
	 * Add an error message to the error property
	 *
	 * @param string $msg
	 *
	 * @return void
	 *
	 * @since 3.0.0
	 */
	protected function setError($msg)
	{
		$this->errorCounter++;
		$this->recentError = $msg;
	}

	/**
	 * Complete validation of email
	 *
	 * @param string $email Email address
	 *
	 * @return boolean|string   True if the valid email also exist, else error message
	 *
	 * @since 3.0.0
	 */
	public function check($email)
	{
		if (!self::validate($email))
		{
			$message = Text::sprintf('COM_BWPOSTMAN_SUB_ERROR_VALIDATING_EMAIL_WRONG_FORMAT', $email);
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'mailcheck'));

			return false;
		}

		$this->errorCounter = 0; // Reset errors
		$this->stream       = false;

		$mxRecords = $this->getMxRecords(self::parseEmail($email));
		$timeout   = ceil($this->maxConnectionTimeout/count($mxRecords));

		foreach ($mxRecords as $mxRecord)
		{
			// Suppress stream errors
			$remoteSocket = "tcp://" . $mxRecord . ":" . $this->port;
			$this->stream = @stream_socket_client($remoteSocket, $errno, $errstr, $timeout);

			if ($this->stream === false)
			{
				if ($errno == 0)
				{
					$message = Text::sprintf('COM_BWPOSTMAN_SUB_ERROR_VALIDATING_EMAIL_SOCKET_PROBLEM', $email);
					$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'mailcheck'));

					return false;
				}
				else
				{
					$message = Text::sprintf('COM_BWPOSTMAN_SUB_ERROR_VALIDATING_EMAIL_SOCKET_PROBLEM_2', $mxRecord, $errstr);
					$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'mailcheck'));
				}
			}
			else
			{
				stream_set_timeout($this->stream, $this->streamTimeout);
				stream_set_blocking($this->stream, 1);
				$streamResponse = $this->streamCode($this->streamResponse());

				if ($streamResponse == '220')
				{
					$message = sprintf('Connection success for host %s', $mxRecord);
					$this->logger->addEntry(new LogEntry($message, BwLogger::BW_DEBUG, 'mailcheck'));

					break;
				}
				else
				{
					fclose($this->stream);
					$this->stream = false;
				}
			}
		}

		if ($this->stream === false)
		{
			$message = Text::sprintf('COM_BWPOSTMAN_SUB_ERROR_VALIDATING_EMAIL_SOCKET_PROBLEM_GENERAL', $email);
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'mailcheck'));

			return false;
		}

		$this->streamQuery("HELO " . self::parseEmail($this->from));
		$response = $this->streamResponse();
		$response .= "\n";

		$this->streamQuery("MAIL FROM:<{$this->from}>");
		$response .= $this->streamResponse();
		$response .= "\n";

		$message = Text::sprintf('Current return code after MAIL FROM: %s', $response);
		$this->logger->addEntry(new LogEntry($message, BwLogger::BW_DEBUG, 'mailcheck'));

		$this->streamQuery("RCPT TO:<{$email}>");
		$code = $this->streamCode($this->streamResponse());
		$response .= $code;
		$response .= "\n";

		$message = Text::sprintf('Current return code after RCPT TO : %s', $code);
		$this->logger->addEntry(new LogEntry($message, BwLogger::BW_DEBUG, 'mailcheck'));

		$this->streamQuery("RSET");

		$code2 = $this->streamCode($this->streamResponse());
		$response .= $code2;
		$response .= "\n";

		$message = Text::sprintf('Current return code after RSET : %s', $code2);
		$this->logger->addEntry(new LogEntry($message, BwLogger::BW_DEBUG, 'mailcheck'));

		$this->streamQuery("QUIT");

		fclose($this->stream);

//		$code = !empty($code2)?$code2:$code;

		switch ($code)
		{
			case '250':
				/**
				 * http://www.ietf.org/rfc/rfc0821.txt
				 * 250 Requested mail action okay, completed
				 * email address was accepted
				 */
			case '450':
			case '451':
			case '452':
				/**
				 * http://www.ietf.org/rfc/rfc0821.txt
				 * 450 Requested action not taken: the remote mail server
				 * does not want to accept mail from your server for
				 * some reason (IP address, blacklisting, etc..)
				 * 451 Requested action aborted: local error in processing
				 * 452 Requested action not taken: insufficient system storage
				 * email address was grey-listed (or some temporary error occurred on the MTA)
				 * I believe that e-mail exists
				 */
				return true;
			case '550':
			default :
				return false;
		}
	}

	/**
	 * writes the contents of query to the file stream pointed to by handle
	 *
	 * @param string $query The string that is to be written
	 *
	 * @return string|boolean Returns a result code as an integer, false on failure
	 *
	 * @since 3.0.0
	 */
	protected function streamQuery($query)
	{
		$message = Text::sprintf('COM_BWPOSTMAN_SUB_VALIDATING_CURRENT_QUERY', $query);
		$this->logger->addEntry(new LogEntry($message, BwLogger::BW_DEBUG, 'mailcheck'));

		return stream_socket_sendto($this->stream, $query . self::CRLF);
	}

	/**
	 * Reads the complete answer and analyze it
	 *
	 * @param integer $timed
	 *
	 * @return string|boolean Response, false on failure
	 *
	 * @since 3.0.0
	 */
	protected function streamResponse($timed = 0)
	{
		$reply = stream_get_line($this->stream, 1);
		$status = stream_get_meta_data($this->stream);

		if (!empty($status['timed_out']))
		{
			$message = Text::sprintf('COM_BWPOSTMAN_SUB_ERROR_VALIDATING_EMAIL_STREAM_TIMEOUT', $this->streamTimeout);
			$this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, 'mailcheck'));
		}

		if ($reply === false && $status['timed_out'] && $timed < $this->streamTimeoutWait)
		{
			return $this->streamResponse($timed + $this->streamTimeout);
		}


		if ($reply !== false && $status['unread_bytes'] > 0)
		{
			$reply .= stream_get_line($this->stream, $status['unread_bytes'], self::CRLF);
		}

		return $reply;
	}

	/**
	 * Get Response code from Response
	 *
	 * @param string $str
	 *
	 * @return string
	 *
	 * @since 3.0.0
	 */
	protected function streamCode($str)
	{
		preg_match('/^(?<code>[0-9]{3})(\s|-)(.*)$/ims', $str, $matches);

		if (isset($matches['code']) && $matches['code'])
		{
			return $matches['code'];
		}

		return false;
	}
}
