<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman subscribers table for backend.
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

namespace BoldtWebservice\Component\BwPostman\Administrator\Table;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwException;
use DateTime;
use Exception;
use JAccessRules;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Mail\MailHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Versioning\VersionableTableInterface;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;
use Joomla\Filter\InputFilter;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\User\UserHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanSubscriberHelper;
use RuntimeException;

/**
 * #__bwpostman_subscribers table handler
 * Table for storing the subscriber data
 *
 * @package		BwPostman-Admin
 * @subpackage	Subscribers
 *
 * @since       0.9.1
 */
class SubscriberTable extends Table implements VersionableTableInterface
{
	/**
	 * @var int Primary Key
	 *
	 * @since       0.9.1
	 */
	public $id = 0;

	/**
	 * @var int asset_id
	 *
	 * @since       1.0.1
	 */
	public $asset_id = null;

	/**
	 * @var int User-ID --> 0 = subscriber is not registered for the website, another ID = Subscriber is registered for
	 *      the website (ID comes from users-table)
	 *
	 * @since       0.9.1
	 */
	public $user_id = null;

	/**
	 * @var string Name
	 *
	 * @since       0.9.1
	 */
	public $name = null;

	/**
	 * @var string Firstname
	 *
	 * @since       0.9.1
	 */
	public $firstname = null;

	/**
	 * @var string Email
	 *
	 * @since       0.9.1
	 */
	public $email = null;

	/**
	 * @var int newsletter format --> 0 = text, 1 = html
	 *
	 * @since       0.9.1
	 */
	public $emailformat = null;

	/**
	 * @var int gender --> 0 = male, 1 = female NULL = unknown
	 *
	 * @since       0.9.1
	 */
	public $gender = 2;

	/**
	 * @var string special field
	 *
	 * @since       0.9.1
	 */
	public $special = null;

	/**
	 * @var int Subscriber status --> 0 = not confirmed, 1 = confirmed, 9 = test-recipient
	 *
	 * @since       0.9.1
	 */
	public $status = 0;

	/**
	 * @var string Activation code for the subscription
	 *
	 * @since       0.9.1
	 */
	public $activation = null;

	/**
	 * @var string Code for editing the subscription in the frontend
	 *
	 * @since       0.9.1
	 */
	public $editlink = null;

	/**
	 * @var int access level/view level --> 1 = Public, 2 = Registered, 3 = Special, >3 = user defined viewlevels
	 *
	 * @since       0.9.1
	 */
	public $access = 1;

	/**
	 * @var datetime Registration date
	 *
	 * @since       0.9.1
	 */
	public $registration_date = null;

	/**
	 * @var int ID --> 0 = subscriber registered himself, another ID = administrator from users-table
	 *
	 * @since       0.9.1
	 */
	public $registered_by = null;

	/**
	 * @var string Registration IP
	 *
	 * @since       0.9.1
	 */
	public $registration_ip = null;

	/**
	 * @var datetime Confirmation date of the subscription
	 *
	 * @since       0.9.1
	 */
	public $confirmation_date = null;

	/**
	 * @var int ID --> -1 = account is not confirmed, 0 = subscriber confirmed the subscription by himself, another ID
	 *      = administrator from users-table
	 *
	 * @since       0.9.1
	 */
	public $confirmed_by = null;

	/**
	 * @var string Confirmation IP
	 *
	 * @since       0.9.1
	 */
	public $confirmation_ip = null;

	/**
	 * @var datetime last modification date of the subscriber
	 *
	 * @since       0.9.1
	 */
	public $modified_time = null;

	/**
	 * @var int user ID
	 *
	 * @since       0.9.1
	 */
	public $modified_by = 0;

	/**
	 * @var int Checked-out owner
	 *
	 * @since       0.9.1
	 */
	public $checked_out = 0;

	/**
	 * @var datetime Checked-out time
	 *
	 * @since       0.9.1
	 */
	public $checked_out_time = null;

	/**
	 * @var int Archive-flag --> 0 = not archived, 1 = archived
	 *
	 * @since       0.9.1
	 */
	public $archive_flag = 0;

	/**
	 * @var datetime Archive-date
	 *
	 * @since       0.9.1
	 */
	public $archive_date = null;

	/**
	 * @var int ID --> -1 = account is not archived, 0 = account is archived by the subscriber himself, another ID =
	 *      account is archived by an administrator
	 *
	 * @since       0.9.1
	 */
	public $archived_by = -1;

	/**
	 * Constructor
	 *
	 * @param 	DatabaseDriver  $db Database object
	 *
	 * @since       0.9.1
	 */
	public function __construct($db = null)
	{
		parent::__construct('#__bwpostman_subscribers', 'id', $db);
	}

	/**
	 * Alias function
	 *
	 * @return  string
	 *
	 * @since   1.0.1
	 */
	public function getAssetName(): string
	{
		return self::_getAssetName();
	}

	/**
	 * Alias function
	 *
	 * @return  string
	 *
	 * @since   1.0.1
	 */
	public function getAssetTitle(): ?string
	{
		return self::_getAssetTitle();
	}

	/**
	 * Alias function
	 *
	 * @return  int
	 *
	 * @throws Exception
	 *
	 * @since   1.0.1
	 */
	public function getAssetParentId(): int
	{
		return self::_getAssetParentId();
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 *
	 * @since   1.0.1
	 */
	protected function _getAssetName(): string
	{
		$k = $this->_tbl_key;
		return 'com_bwpostman.subscriber.' . (int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return  string
	 *
	 * @since   1.0.1
	 */
	protected function _getAssetTitle(): ?string
	{
		return $this->name;
	}

	/**
	 * Method to get the parent asset id for the record
	 *
	 * @param Table|null $table A Table object (optional) for the asset parent
	 * @param null       $id    The id (optional) of the content.
	 *
	 * @return  integer
	 *
	 * @throws Exception
	 * @since   11.1
	 */
	protected function _getAssetParentId(Table $table = null, $id = null): int
	{
//		$MvcFactory = Factory::getApplication()->bootComponent('com_bwpostman')->getMVCFactory();
//		$asset      = $MvcFactory->createTable('Asset', 'Administrator');
		$asset = Table::getInstance('Asset');

		$asset->loadByName('com_bwpostman.subscriber');
		return $asset->id;
	}

	/**
	 * Overloaded bind function
	 *
	 * @access public
	 *
	 * @param   array|object  $src     An associative array or object to bind to the Table instance.
	 * @param   array|string  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return boolean
	 *
	 * @throws  BwException
	 *
	 * @since       0.9.1
	 */
	public function bind($src, $ignore=''): bool
	{
		// Bind the rules.
		if (is_object($src))
		{
			if (property_exists($src, 'rules') && is_array($src->rules))
			{
				$rules = new JAccessRules($src->rules);
				$this->setRules($rules);
			}
		}
		elseif (is_array($src))
		{
			if (array_key_exists('rules', $src) && is_array($src['rules']))
			{
				$rules = new JAccessRules($src['rules']);
				$this->setRules($rules);
			}
		}
		else
		{
			throw new BwException(Text::sprintf('JLIB_DATABASE_ERROR_BIND_FAILED_INVALID_SOURCE_ARGUMENT', get_class($this)));
		}

		return parent::bind($src, $ignore);
	}

	/**
	 * Overloaded check method to ensure data integrity of a subscriber
	 *
	 * @access public

	 * @return boolean True on success
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function check(): bool
	{
		//Initialize
		$params = ComponentHelper::getParams('com_bwpostman');
		$app    = Factory::getApplication();
		$import = $app->getUserState('com_bwpostman.subscriber.import', false);
		$xtest  = $app->getUserState('com_bwpostman.subscriber.new_test', $this->status);
		$data   = $app->getUserState('com_bwpostman.subscriber.register.data', []);

		if ($app->isClient('site') && !empty($data['mod_id']))
		{
			// if data are from module, we need module params
			// we can't use JoomlaModuleHelper, because module isn't shown on frontend
			$params = BwPostmanSubscriberHelper::getModParams((int)$data['mod_id']);
			$module_params  = new Registry($params->params);

			if ($module_params->get('com_params', '1') == 0)
			{
				$params = $module_params;
			}
		}

		$session = $app->getSession();
		$err     = $session->get('session_error');
		$fault   = false;

		$isTester   = false;
		$format_txt	= array(0 => 'Text', 1 => 'HTML');

		if ($xtest == '9')
		{
			$isTester = true;
		}

		if ($import && $this->status == '9')
		{
			$isTester = true;
		}

		// Cleanup all vulnerable values
		$filter = new InputFilter(array(), array(), 0, 0);

		$this->id                = $filter->clean($this->id, 'UINT');
		$this->asset_id          = $filter->clean($this->asset_id, 'UINT');
		$this->name              = $filter->clean($this->name);
		$this->firstname         = $filter->clean($this->firstname);
		$this->email             = $filter->clean($this->email);
		$this->emailformat       = $filter->clean($this->emailformat, 'INT');
		$this->gender            = $filter->clean($this->gender, 'UINT');
		$this->special           = $filter->clean($this->special);
		$this->status            = $filter->clean($this->status, 'UINT');
		$this->activation        = $filter->clean($this->activation);
		$this->editlink          = $filter->clean($this->editlink);
		$this->access            = $filter->clean($this->access, 'UINT');
		$this->registration_date = $filter->clean($this->registration_date);
		$this->registered_by     = $filter->clean($this->registered_by, 'INT');
		$this->registration_ip   = $filter->clean($this->registration_ip);
		$this->confirmation_date = $filter->clean($this->confirmation_date);
		$this->confirmed_by      = $filter->clean($this->confirmed_by, 'INT');
		$this->modified_by       = $filter->clean($this->modified_by, 'INT');
		$this->confirmation_ip   = $filter->clean($this->confirmation_ip);
		$this->modified_time     = $filter->clean($this->modified_time);
		$this->checked_out       = $filter->clean($this->checked_out, 'INT');
		$this->checked_out_time  = $filter->clean($this->checked_out_time);
		$this->archive_flag      = $filter->clean($this->archive_flag, 'UINT');
		$this->archive_date      = $filter->clean($this->archive_date);
		$this->archived_by       = $filter->clean($this->archived_by, 'INT');

		$missingValues = array();

		if (!$import)
		{
			// Check for valid first name
			if ($params->get('firstname_field_obligation', '1'))
			{
				if (trim($this->firstname) === '')
				{
					$app->enqueueMessage(Text::_('COM_BWPOSTMAN_SUB_ERROR_FIRSTNAME'), 'error');
					$missingValues[] = 411;
					$fault	= true;
				}
			}

			// Check for valid name
			if ($params->get('name_field_obligation', '1'))
			{
				if (trim($this->name) === '')
				{
					$app->enqueueMessage(Text::_('COM_BWPOSTMAN_SUB_ERROR_NAME'), 'error');
					$missingValues[] = 412;
					$fault	= true;
				}
			}

			// Check for valid additional field
			if ($params->get('special_field_obligation', '0'))
			{
				if (trim($this->special) === '')
				{
					$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_SUB_ERROR_SPECIAL', $params->get('special_label', '') != '' ? Text::_($params->get('special_label', '')) : Text::_('COM_BWPOSTMAN_SPECIAL')), 'error');
					$missingValues[] = 413;
					$fault	= true;
				}
			}
		}

		// Check for valid email address
		if (trim($this->email) === '')
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_SUB_ERROR_EMAIL'), 'error');
			$missingValues[] = 414;
			$fault	= true;
		}
		// If there is a email address check if the address is valid
		elseif (!MailHelper::isEmailAddress(trim($this->email)))
		{
			$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_SUB_ERROR_EMAIL_INVALID', $this->email), 'error');
			$fault	= true;
		}

		if ($app->isClient('site') && !$this->id)
		{
			// Check if any mailinglist is checked
			if(!$data['mailinglists'])
			{
				$app->enqueueMessage(Text::_('COM_BWPOSTMAN_ERROR_LISTCHECK'), 'error');
				$missingValues[] = 415;
				$fault	= true;
			}

			// agreecheck
			if ($params->get('disclaimer', '0') == 1)
			{
				if(!isset($data['agreecheck']) || (isset($data['mod_id']) && $fault))
				{
					$app->enqueueMessage(Text::_('COM_BWPOSTMAN_ERROR_AGREECHECK'), 'error');
					$missingValues[] = 416;
					$fault	= true;
				}
			}

			// Spamcheck 1
			// Set error message if a not visible (top: -5000px) input field is not empty
			if($data['falle'] !== '')
			{
				// input wrong - set error
				$app->enqueueMessage(Text::_('COM_BWPOSTMAN_ERROR_SPAMCHECK'), 'error');
				$missingValues[] = 417;
				$fault	= true;
			}

			// Spamcheck 2
			// Set error message if check of a dynamic time variable failed
			if(!isset($data['bwp-' . BwPostmanHelper::getCaptcha()]) && !isset($data['bwp-' . BwPostmanHelper::getCaptcha(2)]))
			{
				// input wrong - set error
				$app->enqueueMessage(Text::_('COM_BWPOSTMAN_ERROR_SPAMCHECK2'), 'error');
				$missingValues[] = 418;
				$fault	= true;
			}

			// Captcha check 1
			// Set error message if captcha test failed
			if ($params->get('use_captcha', '0') == 1)
			{
				// start check
				if(trim($data['stringQuestion']) !== trim($params->get('security_answer', '4')) || (isset($data['mod_id']) && $fault))
				{
					// input wrong - set error
					$app->enqueueMessage(Text::_('COM_BWPOSTMAN_ERROR_CAPTCHA'), 'error');
					$missingValues[] = 419;
					$fault	= true;
				}
			}

			// Captcha check 2
			if ($params->get('use_captcha', '0') == 2)
			{
				// Temp folder of captcha-images
				$captchaDir = JPATH_COMPONENT_SITE . '/assets/capimgdir/';
				// del old images after ? minutes
				$delFile = 10;
				// start check
				$resultCaptcha = BwPostmanHelper::CheckCaptcha($data['codeCaptcha'], $data['stringCaptcha'], $captchaDir, $delFile);
				if(!$resultCaptcha || (isset($data['mod_id']) && $fault))
				{
					// input wrong - set error
					$app->enqueueMessage(Text::_('COM_BWPOSTMAN_ERROR_CAPTCHA'), 'error');
					$missingValues[] = 420;
					$fault	= true;
				}
			}
		}

		if ($fault)
		{
			$app->setUserState('com_bwpostman.edit.subscriber.data', $data);
			$session->set('subscriber.register.missingValues', $missingValues);
			return false;
		}

		// Check for existing email
		$xids = $this->getSubscriberIdByEmail($this->email, $isTester);

		//Test-recipient may have multiple entries, but may not be archived
		if ($isTester)
		{
			foreach ($xids AS $xid)
			{
				$xid = intval($xid);

				if ($xid && $xid !== intval($this->id))
				{
					$testrecipient = $this->getSubscriberNewsletterData($xid, true);

					// Account with this emailformat already exists
					if (($testrecipient->archive_flag === 0) && ($testrecipient->emailformat === $this->emailformat))
					{
						$app->enqueueMessage(
							Text::sprintf(
								'COM_BWPOSTMAN_TEST_ERROR_ACCOUNTEXISTS',
								$this->email,
								$format_txt[$this->emailformat],
								$testrecipient->id
							),
							'error'
						);
						$err_msg = Text::sprintf(
							'COM_BWPOSTMAN_TEST_ERROR_ACCOUNTEXISTS',
							$this->email,
							$format_txt[$this->emailformat],
							$testrecipient->id
						);
						$err[] = array(
							'err_code' => 409,
							'err_msg' => $err_msg,
							'err_id' => $xid
						);
						$app->setUserState('com_bwpostman.subscriber.register.error', $err);
						$app->enqueueMessage($err_msg,  'error');
						$session->set('session_error', $err);
						return false;
					}

					// Account is archived
					if (($testrecipient->archive_flag === 1) && ($testrecipient->emailformat === $this->emailformat))
					{
						$err_msg = Text::sprintf(
							'COM_BWPOSTMAN_TEST_ERROR_ACCOUNTARCHIVED',
							$this->email,
							$format_txt[$this->emailformat],
							$testrecipient->id
						);
						$app->enqueueMessage($err_msg, 'error');
						$err[] = array(
							'err_code' => 410,
							'err_msg' => $err_msg,
							'err_id' => $xid,
						);
						$app->setUserState('com_bwpostman.subscriber.register.error', $err);
						$session->set('session_error', $err);
						return false;
					}
				}
			}
		}
		//Subscriber may only have one subscription, that ist not archived of blocked
		else
		{
			if ($xids && $xids !== intval($this->id))
			{
				$subscriber = $this->getSubscriberNewsletterData($xids);

				// Account is blocked by system/administrator
				if (($subscriber->archive_flag === 1) && ($subscriber->archived_by > 0))
				{
					$err = array(
						'err_code' => 405,
						'err_msg' => Text::sprintf('COM_BWPOSTMAN_SUB_ERROR_DB_ACCOUNTBLOCKED_BY_SYSTEM', $data['email'], $xids),
						'err_id' => $xids,
						'err_email' => $data['email'],
					);
					$app->setUserState('com_bwpostman.subscriber.register.error', $err);
					$session->set('session_error', $err);
					return false;
				}

				// Account is not activated
				if ($subscriber->status == 0)
				{
					$err = array(
						'err_code' => 406,
						'err_msg' => Text::sprintf('COM_BWPOSTMAN_SUB_ERROR_DB_ACCOUNTNOTACTIVATED', $data['email'], $xids),
					 	'err_id' => $xids,
						'err_email' => $data['email'],
					);

					$app->setUserState('com_bwpostman.subscriber.register.error', $err);
					$session->set('session_error', $err);

					return false;
				}

				// Account already exists
				if (($subscriber->status == 1) && ($subscriber->archive_flag != 1))
				{
					$link = Uri::base() . 'index.php?option=com_bwpostman&view=edit';
					$err_msg = Text::sprintf('COM_BWPOSTMAN_SUB_ERROR_DB_ACCOUNTEXISTS', $data['email']->email, $link);
					//@ToDo: With the following routing with SEO activated don't work
					$err = array(
						'err_code' => 407,
						'err_msg' => $err_msg,
						'err_id' => $xids,
						'err_email' => $data['email'],
					);
					$app->setUserState('com_bwpostman.subscriber.register.error', $err);
					$session->set('session_error', $err);
					$this->setError(Text::sprintf('COM_BWPOSTMAN_SUB_ERROR_DB_ACCOUNTEXISTS', $data['email'],  $xids));
					return false;
				}
			}

			// Existing confirmation ip and confirmed_by = -1 is a strong indication, that subscriber wants to change
			// his mail address, so ensure, confirmation date is reset.
			if ($this->confirmation_ip !== null && $this->confirmed_by === -1)
			{
				$this->confirmation_date = null;
			}
		}

		return true;
	}

	/**
	 * Method to check if test-recipients exists
	 *
	 * @return 	boolean
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function checkForTestrecipients(): bool
	{
		$testrecipients = null;

		$db	   = $this->_db;
		$query = $db->getQuery(true);

		$query->select('COUNT(' . $db->quoteName('id') . ')');
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('status') . ' = ' . 9);
		$query->where($db->quoteName('archive_flag') . ' = ' . 0);

		try
		{
			$db->setQuery($query);

			$testrecipients = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		if (!$testrecipients)
		{
			return false;
		}

		return true;
	}

	/**
	 * Overloaded load method to get all test-recipients when a newsletter shall be sent to them
	 *
	 * @access	public

	 * @return 	array|null
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function loadTestrecipients(): ?array
	{
		$result = array();
		$this->reset();
		$db	= $this->_db;
		$query	= $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('status') . ' = ' . 9);
		$query->where($db->quoteName('archive_flag') . ' = ' . 0);

		try
		{
			$db->setQuery($query);

			$result = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $result;
	}

	/**
	 * Overridden Table::store to set created/modified and user id.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws Exception
	 *
	 * @since   1.0.1
	 */
	public function store($updateNulls = true): bool
	{
		$app  = Factory::getApplication();
		$date = Factory::getDate();
		$user = $app->getIdentity();

		if ($this->gender === '')
		{
			$this->gender = 2;
		}

		if ($this->id)
		{
			// Existing subscriber
			$this->modified_time = $date->toSql();
			$this->modified_by   = $user->get('id');
		}
		else
		{
			// New subscriber
			$this->registration_date = $date->toSql();
			$this->registered_by     = $user->get('id');
		}

		// Ensure nulldate columns have correct nulldate
		$nulldateCols = array(
			'confirmation_date',
			'modified_time',
			'checked_out_time',
			'archive_date',
		);

		foreach ($nulldateCols as $nulldateCol)
		{
			if ($this->$nulldateCol === '' || $this->$nulldateCol === $this->_db->getNullDate())
			{
				$this->$nulldateCol = null;
			}
		}


		// Existing confirmation ip and confirmed_by = -1 is a strong indication, that subscriber wants to change
		// his mail address, so ensure, confirmation date is reset.
		if ($this->confirmation_ip !== null && $this->confirmed_by === -1)
		{
			$this->confirmation_date = null;
		}

		$res = parent::store($updateNulls);

		if ($res !== true)
		{
			$app->enqueueMessage($this->getError());
		}

		$app->setUserState('com_bwpostman.subscriber.id', $this->id);
		$app->setUserState('subscriber.id', $this->id);

		return $res;
	}

	/**
	 * Overridden Table::delete
	 *
	 * @param	mixed	$pk     An optional primary key value to delete.  If not set the
	 *				        	instance property value is used.
	 *
	 * @return	boolean	True on success.
	 *
	 * @since   1.0.1
	 */
	public function delete($pk = null): bool
	{
		return parent::delete($pk);
	}

	/**
	 * Returns the identity (primary key) value of this record
	 *
	 * @return  mixed
	 *
	 * @since  3.0.0
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
	 * @since   3.0.0
	 */
	public function hasField($key): bool
	{
		$key = $this->getColumnAlias($key);

		return property_exists($this, $key);
	}

	/**
	 * Method to check if a subscriber is archived
	 *
	 * @param integer $subsId ID of the subscriber to check
	 * @param bool    $isTester Don't reset table if tester is asked
	 *
	 * @return    object|null
	 *
	 * @throws Exception
	 * @since 3.0.0
	 */
	public function getSubscriberNewsletterData(int $subsId, bool $isTester =false): ?object
	{
		$result = false;

		if (!$isTester)
		{
			$this->reset();
		}

		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->select($db->quoteName('id'));
		$query->select($db->quoteName('editlink'));
		$query->select($db->quoteName('archive_flag'));
		$query->select($db->quoteName('archived_by'));
		$query->select($db->quoteName('status'));
		$query->select($db->quoteName('emailformat'));
		$query->from($this->_tbl);
		$query->where($db->quoteName('id') . ' = ' . $db->quote($subsId));

		try
		{
			$db->setQuery($query);

			$result = $db->loadObject();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $result;
	}

	/**
	 * Method to check by user ID if a user has a newsletter account (user = no guest)
	 *
	 * Returns 0 if user has no newsletter subscription
	 *
	 * @param int $uid user ID
	 *
	 * @return    int $id     subscriber ID
	 *
	 * @throws Exception
	 *
	 * @since       3.0.0 (here, before since 2.0.0 at subscriber helper)
	 */
	public function getSubscriberIdByUserId(int $uid): int
	{
		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->select($db->quoteName('id'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('user_id') . ' = ' . $uid);
		$query->where($db->quoteName('status') . ' != ' . 9);

		try
		{
			$db->setQuery($query);

			$id = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		if (empty($id))
		{
			$id = 0;
		}

		return (int)$id;
	}

	/**
	 * Method to check if an email address exists in the subscribers-table
	 *
	 * @param string  $email    subscriber email
	 * @param boolean $isTester is tester?
	 *
	 * @return    int|array     $id     single subscriber ID if regular subscriber, array if testrecipient
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function getSubscriberIdByEmail(string $email, bool $isTester = false)
	{
		$db    = $this->_db;
		$query = $db->getQuery(true);
		$id    = 0;

		// Sanitize values
		$email = $db->escape($email);

		$query->select($db->quoteName('id'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('email') . ' = ' . $db->quote($email));
		if (!$isTester)
		{
			$query->where($db->quoteName('status') . ' != ' . 9);
		}
		try
		{
			$db->setQuery($query);

			if (!$isTester)
			{
				$id = (int)$this->_db->loadResult();
			}
			else
			{
				$id = $this->_db->loadColumn();
			}
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $id;
	}

	/**
	 * Method to get the data of a subscriber who has a newsletter account from the subscribers-table
	 * because we need to know if his account is okay or archived or not activated (user = no guest)
	 *
	 * @access    public
	 *
	 * @param int $id subscriber ID
	 *
	 * @return    object|null  $subscriber subscriber object
	 *
	 * @throws Exception
	 *
	 * @since       3.0.0 (here, before since 2.0.0 at subscriber helper)
	 */
	public function getSubscriberState(int $id): ?object
	{
		$subscriber = null;
		$db         = $this->_db;
		$query      = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('id') . ' = ' . $id);
		$query->where($db->quoteName('status') . ' != ' . 9);

		try
		{
			$db->setQuery($query);

			$subscriber = $db->loadObject();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $subscriber;
	}

	/**
	 * Method to get the user ID of a subscriber from the subscribers-table depending on the subscriber ID
	 * --> is needed for the constructor
	 *
	 * @param int $id subscriber ID
	 *
	 * @return 	int|null user ID
	 *
	 * @throws Exception
	 *
	 * @since       3.0.0 (here, before since 2.0.0 at subscriber helper)
	 */
	public function getUserIdOfSubscriber(int $id): ?int
	{
		$user_id    = null;
		$db	    = $this->_db;
		$query	    = $db->getQuery(true);

		$query->select($db->quoteName('user_id'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('id') . ' = ' . $id);
		$query->where($db->quoteName('status') . ' != ' . 9);

		try
		{
			$db->setQuery($query);

			$user_id = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		if (is_null($user_id))
		{
			$user_id = 0;
		}

		return $user_id;
	}

	/**
	 * Method to get the number of subscribers depending on provided sending and archive state
	 *
	 * @param boolean $tester
	 * @param boolean $archived
	 *
	 * @return 	integer|null number of subscribers or false
	 *
	 * @throws Exception
	 *
	 * @since       3.0.0 (here, before since 2.3.0 at subscriber helper)
	 */
	public function getNbrOfSubscribers(bool $tester, bool $archived): ?int
	{
		$archiveFlag    = 0;
		$statusOperator = "!=";

		if ($tester)
		{
			$statusOperator = "=";
		}

		if ($archived)
		{
			$archiveFlag = 1;
		}

		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->select('COUNT(*)');
		$query->from($db->quoteName($this->_tbl));

		if (!$archived)
		{
			$query->where($db->quoteName('status') . $statusOperator . 9);
		}

		$query->where($db->quoteName('archive_flag') . ' = ' . $archiveFlag);

		try
		{
			$db->setQuery($query);

			return $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
		return false;
	}

	/**
	 * Method to create the editlink and check if the string does not exist twice or more
	 *
	 * @return string   $editlink
	 *
	 * @throws Exception
	 *
	 * @since 3.0.0 here
	 */
	public function getEditlink(): string
	{
		$db              = $this->_db;
		$newEditlink     = "";
		$editlinkMatches = true;

		while ($editlinkMatches)
		{
			$newEditlink = ApplicationHelper::getHash(UserHelper::genRandomPassword());

			$query = $db->getQuery(true);

			$query->select($db->quoteName('editlink'));
			$query->from($db->quoteName($this->_tbl));
			$query->where($db->quoteName('editlink') . ' = ' . $db->quote($newEditlink));

			try
			{
				$db->setQuery($query);

				$editlink = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				return false;
			}
			if ($editlink !== $newEditlink)
			{
				$editlinkMatches = false;
			}
		}

		return $newEditlink;
	}

	/**
	 * Method to update the editlink for a specific subscriber
	 *
	 * @param int    $subscriberId
	 * @param string $editlink
	 *
	 * @return bool
	 *
	 * @throws Exception
	 * @since 4,1,6
	 */
	public function updateEditlink(int $subscriberId, string $editlink): bool
	{
		$db = $this->_db;

		$query = $db->getQuery(true);

		$query->update($db->quoteName($this->_tbl));
		$query->set($db->quoteName('editlink') . " = " . $db->quote($editlink));
		$query->where("`id` = '" . $subscriberId . "'");

		try
		{
			$db->setQuery($query);

			$executed = $db->execute();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		if ($executed === false)
		{
			return false;
		}

		return true;
	}

	/**
	 * Method to create the activation and check if the string does not exist twice or more
	 *
	 * @return string   $activation
	 *
	 * @throws Exception
	 *
	 * @since 3.0.0 here
	 */
	public function createActivation()
	{
		$db    = $this->_db;
		$query = $db->getQuery(true);

		$newActivation     = "";
		$activationMatches = true;

		while ($activationMatches)
		{
			$newActivation = ApplicationHelper::getHash(UserHelper::genRandomPassword());

			$query->select($db->quoteName('activation'));
			$query->from($db->quoteName($this->_tbl));
			$query->where($db->quoteName('activation') . ' = ' . $db->quote($newActivation));

			try
			{
				$db->setQuery($query);

				$activation = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				return false;
			}

			if ($activation !== $newActivation)
			{
				$activationMatches = false;
			}
		}

		return $newActivation;
	}

	/**
	 * Method to get the complete subscriber data by email
	 *
	 * @param integer $id
	 *
	 * @return  object
	 *
	 * @throws Exception
	 *
	 * @since   3.0.0
	 */
	public function getSingleSubscriberData(int $id): ?object
	{
		$subscriber = null;

		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('id') . ' = ' . $id);

		try
		{
			$db->setQuery($query);

			$subscriber = $db->loadObject();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $subscriber;
	}

	/**
	 * Method to get the complete subscriber data by email
	 *
	 * @param array $values
	 *
	 * @return  object|boolean|null
	 *
	 * @throws Exception
	 *
	 * @since   3.0.0 (here)
	 */
	public function getSubscriberDataByEmail(array $values): ?object
	{
		$db    = $this->_db;
		$query = $db->getQuery(true);

		// Sanitize values
		$email = $db->escape($values['email']);

		$query->select('*');
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('email') . ' = ' . $db->quote($email));

		if ((int)$values['status'] === 9)
		{
			$query->where($db->quoteName('emailformat') . ' = ' . $db->quote((int)$values['emailformat']));
			$query->where($db->quoteName('status') . ' = ' . 9);
		}
		else
		{
			$query->where($db->quoteName('status') . ' IN (0, 1)');
		}

		try
		{
			$db->setQuery($query);

			return $db->loadObject();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return false;
	}

	/**
	 * Method to get the subscriber activation data
	 *
	 * @param string $activation activation code for the newsletter account
	 *
	 * @return  object|null
	 *
	 * @throws Exception
	 *
	 * @since   3.0.0
	 */
	public function getSubscriberActivationData(string $activation): ?object
	{
		$subscriber = null;

		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->select($db->quoteName('id'));
		$query->select($db->quoteName('email'));
		$query->select($db->quoteName('editlink'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('activation') . ' = ' . $db->quote($activation));
		$query->where($db->quoteName('status') . ' = ' . 0);
		$query->where('(' . $db->quoteName('confirmation_date') . ' = ' . $db->quote($db->getNullDate())
		 . ' OR ' . $db->quoteName('confirmation_date') . ' IS NULL)');
		$query->where($db->quoteName('confirmed_by') . ' = ' . -1);
		$query->where($db->quoteName('archive_flag') . ' = ' . 0);
		$query->where($db->quoteName('archived_by') . ' = ' . -1);
		try
		{
			$db->setQuery($query);

			$subscriber = $db->loadObject();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $subscriber;
	}

	/**
	 * Method to store the subscriber activation
	 *
	 * @param integer $id            id of the subscriber to store activation
	 * @param string  $activation_ip IP used for activation
	 *
	 * @return  boolean true on success
	 *
	 * @throws Exception
	 *
	 * @since   3.0.0
	 */
	public function storeSubscriberActivation(int $id, string $activation_ip): bool
	{
		$date = Factory::getDate();
		$time = $date->toSql();

		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->update($db->quoteName($this->_tbl));
		$query->set($db->quoteName('status') . ' = ' . 1);
		$query->set($db->quoteName('activation') . ' = ' . $db->quote(''));
		$query->set($db->quoteName('confirmation_date') . ' = ' . $db->quote($time, false));
		$query->set($db->quoteName('confirmed_by') . ' = ' . 0);
		$query->set($db->quoteName('confirmation_ip') . ' = ' . $db->quote($activation_ip));
		$query->where($db->quoteName('id') . ' = ' . $id);
		try
		{
			$db->setQuery($query);

			$db->execute();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}

		return true;
	}

	/**
	 * Method to validate edit link, if exists return subscriber id
	 *
	 * @param string  $email    email of the demanded unsubscription
	 * @param string  $editlink editlink provided by the unsubscription
	 *
	 * @return  integer|boolean false on failure
	 *
	 * @throws Exception
	 *
	 * @since   3.0.0
	 */
	public function validateSubscriberEditlink(string $email, string $editlink): ?int
	{
		$db    = $this->_db;
		$query = $db->getQuery(true);

		// Sanitize values
		$email    = $db->escape($email);
		$editlink = $db->escape($editlink);

		$query->select($db->quoteName('id'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('email') . ' = ' . $db->quote($email));
		$query->where($db->quoteName('editlink') . ' = ' . $db->quote($editlink));
		$query->where($db->quoteName('status') . ' != ' . 9);

		try
		{
			$db->setQuery($query);
			$id = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}

		return $id;
	}

	/**
	 * Method to get the mail address of a subscriber from the subscribers-table depending on the subscriber ID
	 *
	 * @param int $id subscriber ID
	 *
	 * @return 	string	user ID
	 *
	 * @throws Exception
	 *
	 * @since  3.0.0
	 */
	public function getEmailaddress(int $id): ?string
	{
		$emailaddress = null;

		$db	   = $this->_db;
		$query = $db->getQuery(true);

		$query->select($db->quoteName('email'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('id') . ' = ' . $id);

		try
		{
			$db->setQuery($query);

			$emailaddress = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $emailaddress;
	}

	/**
	 * Checks if an editlink exists in the subscribers-table
	 *
	 * @param string $editlink to edit the subscriber data
	 *
	 * @return 	int subscriber ID
	 *
	 * @throws Exception
	 *
	 * @since  3.0.0
	 */
	public function checkEditlink(string $editlink): ?int
	{
		$id    = null;
		$db    = $this->_db;
		$query = $db->getQuery(true);

		$editlink = $db->escape($editlink);

		$query->select($db->quoteName('id'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('editlink') . ' = ' . $db->quote($editlink));
		$query->where($db->quoteName('status') . ' != ' . 9);

		try
		{
			$db->setQuery($query);

			$id = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $id;
	}

	/**
	 * Method to get the subscribers data for push to sendmailqueue
	 *
	 * @param int    $content_id  Content ID --> from the sendmailcontent-Table
	 * @param string $status      Status --> 0 = unconfirmed, 1 = confirmed
	 * @param array  $subscribers array of subscriber ids to get the data for
	 *
	 * @return 	array
	 *
	 * @throws Exception
	 *
	 * @since  3.0.0
	 */
	public function getSubscriberDataForSendmailqueue(int $content_id, string $status, array $subscribers): array
	{
		$data  = array();
		$db    = $this->_db;
		$query = $db->getQuery(true);

		$query->select($db->quote($content_id) . ' AS content_id');
		$query->select($db->quoteName('email') . ' AS ' . $db->quoteName('recipient'));
		$query->select($db->quoteName('emailformat') . ' AS ' . $db->quoteName('mode'));
		$query->select($db->quoteName('name') . ' AS ' . $db->quoteName('name'));
		$query->select($db->quoteName('firstname') . ' AS ' . $db->quoteName('firstname'));
		$query->select($db->quoteName('id') . ' AS ' . $db->quoteName('subscriber_id'));
		$query->from($db->quoteName($this->_tbl));
		$query->where($db->quoteName('status') . ' IN (' . $status . ')');
		$query->where($db->quoteName('archive_flag') . ' = ' . 0);

		if (count($subscribers))
		{
			$query->where($db->quoteName('id') . ' IN (' . implode(',', $subscribers) . ')');
		}

		try
		{
			$db->setQuery($query);

			$data = $db->loadRowList();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $data;
	}

	/**
	 * Get the type alias for the history table
	 *
	 * The type alias generally is the internal component name with the
	 * content type. Ex.: com_content.article
	 *
	 * @return  string  The alias as described above
	 *
	 * @since   4.0.0
	 */
	public function getTypeAlias(): string
	{
		return 'com_bwpostman.subscriber';
	}
}
