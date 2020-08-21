<?php
/**
 * BwPostman  Plugin Footer Used Mailinglists
 *
 * BwPostman Plugin Footer Used Mailinglists main file for BwPostman.
 *
 * @version %%version_number%%
 * @package BwPostman Plugin Footer Used Mailinglists
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
 * @license GNU/GPL v3, see LICENSE.txt
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

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Component\ComponentHelper;

jimport('joomla.plugin.plugin');

if (!ComponentHelper::isEnabled('com_bwpostman')) {
	Factory::getApplication()->enqueueMessage(
		Text::_('PLG_BWPOSTMAN_PLUGIN_FOOTER_USED_MAILINGLISTS_ERROR') . ', ' . Text::_('PLG_BWPOSTMAN_PLUGIN_FOOTER_USED_MAILINGLISTS_COMPONENT_NOT_INSTALLED'),
		'error'
	);
	return false;
}

/**
 * Class PlgBwPostmanFooterUsedMailinglists
 *
 * @since       2.3.0
 */
class PlgBwPostmanFooterUsedMailinglists extends JPlugin
{
	/**
	 * Database object
	 *
	 * @var    DatabaseDriver
	 *
	 * @since       2.3.0
	 */
	protected $db;

	/**
	 * Application object
	 *
	 * @var    JApplication
	 *
	 * @since       2.3.0
	 */
	protected $app;

	/**
	 * PlgBwPostmanFooterUsedMailinglists constructor.
	 *
	 * @param object $subject
	 * @param array  $config
	 *
	 * @since       2.3.0
	 */
	function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
//		$this->_enabled = false;

//		$log_options    = array();
//		$this->logger   = BwLogger::getInstance($log_options);
//		$this->debug    = false;

//		// Do not load if BwPostman version is not supported or BwPostman isn't detected
//		$this->setBwPostmanComponentStatus();
//		$this->setBwPostmanComponentVersion();
		$this->loadLanguage();
	}

	/**
	 * Method to insert the used mailing lists in the footer of the HTML newsletter
	 *
	 * @param string $text the footer of the newsletter
	 *
	 * @return bool
	 *
	 * @throws \Exception
	 *
	 * @since       2.3.0
	 */
	public function onBwPostmanBeforeObligatoryFooterHtml(&$text)
	{
		$usedUgIds = Factory::getApplication()->getUserState('com_bwpostman.edit.newsletter.data.usergroups', array());

		$mlAvailable = Factory::getApplication()->getUserState('com_bwpostman.edit.newsletter.data.ml_available', array());
		$mlUnavailable = Factory::getApplication()->getUserState('com_bwpostman.edit.newsletter.data.ml_unavailable', array());
		$mlIntern = Factory::getApplication()->getUserState('com_bwpostman.edit.newsletter.data.ml_intern', array());
		$usedMlIds = array_merge($mlAvailable, $mlUnavailable, $mlIntern);

		$usedMailinglists = $this->getUsedMailinglists($usedMlIds);
		$usedUsergroups   = $this->getUsedUsergroups($usedUgIds);
		$insertText       = '';
		$additionalFooter = '';

		if ($this->params->get('show_mailinglists'))
		{
			$insertText .= $this->insertMailinglistsAtHtmlFooter($usedMailinglists);
		}

		if ($this->params->get('show_usergroups'))
		{
			$insertText .= $this->insertUsergroupsAtHtmlFooter($usedUsergroups);
		}

		if ($this->params->get('show_all_recipients'))
		{
			$nbrAllRecipients = $this->getNbrAllRecipients($usedMailinglists, $usedUsergroups);

			$insertText .= "\t\t\t" . '<table id="show-all" style="border-collapse: collapse;border-spacing: 0;">' . "\n";
			$insertText .= "\t\t\t\t" . "<tr>" . "\n";
			$insertText .= "\t\t\t\t\t" . "<td>";
			$insertText .= Text::sprintf('PLG_BWPOSTMAN_FOOTER_USED_MAILINGLISTS_SHOW_ALL_RECIPIENTS_TEXT', $nbrAllRecipients) . "\n";
			$insertText .= "\t\t\t\t\t" . "</td>" . "\n";
			$insertText .= "\t\t\t\t" . "</tr>" . "\n";
			$insertText .= "\t\t\t" . "</table>" . "\n";
		}

		if ($insertText !== '')
		{
			$additionalFooter = "\n\t\t" . '<table class="show-subscribers" style="table-layout: fixed; width: 100%;" border="0" cellspacing="0" cellpadding="0">' . "\n";
			$additionalFooter .= "\t\t\t" . "<tr>" . "\n";
			$additionalFooter .= "\t\t\t\t" . "<td>";
			$additionalFooter .= $insertText;
			$additionalFooter .= "\t\t\t\t" . "</td>" . "\n";
			$additionalFooter .= "\t\t\t" . "</tr>" . "\n";
			$additionalFooter .= "\t\t" . "</table>" . "\n";
		}

		$text = str_replace('[%impressum%]', "\n" . $additionalFooter . '[%impressum%]', $text);

		return true;
	}

	/**
	 * Method to insert the used mailing lists in the footer of the text newsletter
	 *
	 * @param string $text the footer of the newsletter
	 *
	 * @return bool
	 *
	 * @throws \Exception
	 *
	 * @since       2.3.0
	 */
	public function onBwPostmanBeforeObligatoryFooterText(&$text)
	{
		$usedUgIds = Factory::getApplication()->getUserState('com_bwpostman.edit.newsletter.data.usergroups', array());

		$mlAvailable = Factory::getApplication()->getUserState('com_bwpostman.edit.newsletter.data.ml_available', array());
		$mlUnavailable = Factory::getApplication()->getUserState('com_bwpostman.edit.newsletter.data.ml_unavailable', array());
		$mlIntern = Factory::getApplication()->getUserState('com_bwpostman.edit.newsletter.data.ml_intern', array());
		$usedMlIds = array_merge($mlAvailable, $mlUnavailable, $mlIntern);

		$usedMailinglists = $this->getUsedMailinglists($usedMlIds);
		$usedUsergroups   = $this->getUsedUsergroups($usedUgIds);
		$insertText       = '';

		if ($this->params->get('show_mailinglists'))
		{
			$insertText .= $this->insertMailinglistsAtTextFooter($usedMailinglists);
		}

		if ($this->params->get('show_usergroups'))
		{
			$insertText .= $this->insertUsergroupsAtTextFooter($usedUsergroups);
		}

		if ($this->params->get('show_all_recipients'))
		{
			$nbrAllRecipients = $this->getNbrAllRecipients($usedMailinglists, $usedUsergroups);

			$insertText .= "\n";
			$insertText .= Text::sprintf('PLG_BWPOSTMAN_FOOTER_USED_MAILINGLISTS_SHOW_ALL_RECIPIENTS_TEXT', $nbrAllRecipients) . "\n";
			$insertText .= "\n";
		}

		$text = str_replace('[%impressum%]', "\n" . $insertText . '[%impressum%]', $text);

		return true;
	}

	/**
	 * Method to get the names of the used mailinglists by newsletter or campaign id
	 *
	 * @param array $usedMlIds     array of mailinglist ids
	 *
	 * @return array
	 *
	 * @throws \Exception
	 *
	 * @since       2.3.0
	 */
	protected function getUsedMailinglists($usedMlIds)
	{
		$mailinglists = $this->getUsedMailinglistsFromDb($usedMlIds);

		if (is_array($mailinglists) && count($mailinglists))
		{
			for ($i = 0; $i < count($mailinglists); $i++)
			{
				$mailinglists[$i]['nbrRecipients'] = $this->getNumberOfRecipientsByMailinglist($mailinglists[$i]['id']);
			}
		}

		return $mailinglists;
	}

	/**
	 * Method to get the names of the used mailinglists by newsletter or campaign id
	 *
	 * @param array   $usedUgIds  array of usergroup ids
	 *
	 * @return array
	 *
	 * @throws \Exception
	 *
	 * @since       2.3.0
	 */
	protected function getUsedUsergroups($usedUgIds)
	{
		$usergroups = $this->getUsedUsergroupsFromDb($usedUgIds);

		if (is_array($usergroups) && count($usergroups))
		{
			for ($i = 0; $i < count($usergroups); $i++)
			{
				$usergroups[$i]['nbrRecipients'] = $this->getNumberOfRecipientsByUsergroup($usergroups[$i]['id']);
			}
		}

		return $usergroups;
	}

	/**
	 * Method to get the used mailinglists or usergroups by newsletter id
	 *
	 * @param int   $id                 newsletter  id
	 * @param int   $checkUsergroups    deliver mailinglists -> false or usergroups ->true
	 *
	 * @return array
	 *
	 * @throws \Exception
	 *
	 * @since       2.3.0
	 */
	protected function getUsedRecipientsByNewsletter($id, $checkUsergroups)
	{
		$recipients = array();
		$db 	= $this->db;
		$query  = $this->db->getQuery(true);

		$query->select($db->quoteName('mailinglist_id'));
		$query->from($db->quoteName('#__bwpostman_newsletters_mailinglists'));
		$query->where($db->quoteName('newsletter_id') . ' = ' . $db->quote($id));

		if ($checkUsergroups)
		{
			$query->where($db->quoteName('mailinglist_id') . ' < ' . $db->quote(0));
		}
		else
		{
			$query->where($db->quoteName('mailinglist_id') . ' > ' . $db->quote(0));
		}

		$db->setQuery($query);

		try
		{
			$recipients = $db->loadColumn();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $recipients;
	}

	/**
	 * Method to get the used mailinglists or usergroups by campaigns
	 *
	 * @param int   $id                 newsletter  id
	 * @param int   $checkUsergroups    deliver mailinglists -> false or usergroups ->true
	 *
	 * @return array
	 *
	 * @throws \Exception
	 *
	 * @since       2.3.0
	 */
	protected function getUsedRecipientsByCampaign($id, $checkUsergroups)
	{
		$recipients = array();
		$db 	= $this->db;
		$query  = $this->db->getQuery(true);

		$query->select($db->quoteName('mailinglist_id'));
		$query->from($db->quoteName('#__bwpostman_campaigns_mailinglists'));
		$query->where($db->quoteName('campaign_id') . ' = ' . $db->quote($id));

		if ($checkUsergroups)
		{
			$query->where($db->quoteName('mailinglist_id') . ' < ' . $db->quote(0));
		}
		else
		{
			$query->where($db->quoteName('mailinglist_id') . ' > ' . $db->quote(0));
		}

		$db->setQuery($query);

		try
		{
			$recipients = $db->loadColumn();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $recipients;
	}

	/**
	 * Method to get the campaign id for newsletter if exists
	 *
	 * @param int   $id                 newsletter  id
	 *
	 * @return integer
	 *
	 * @throws \Exception
	 *
	 * @since       2.3.0
	 */
	protected function getCampaignIdByNewsletterId($id)
	{
		$camId = null;
		$db 	= $this->db;
		$query  = $this->db->getQuery(true);

		$query->select($db->quoteName('campaign_id'));
		$query->from($db->quoteName('#__bwpostman_newsletters'));
		$query->where($db->quoteName('id') . ' = ' . $db->quote($id));
		$db->setQuery($query);

		try
		{
			$camId = (int)$db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $camId;
	}

	/**
	 * Method to get text to insert used mailing lists
	 *
	 * @param array     $usedMailinglists
	 *
	 * @return string
	 *
	 * @since 2.3.0
	 */
	private function insertMailinglistsAtHtmlFooter(array $usedMailinglists)
	{
		$insertText = '';

		if (is_array($usedMailinglists) && count($usedMailinglists))
		{
			$insertText .= "\t\t\t" . '<table id="show-mailinglists" style="border-collapse: collapse;border-spacing: 0;">' . "\n";
			$insertText .= "\t\t\t\t" . '<tr class="show-mailinglists-head">' . "\n";
			$insertText .= "\t\t\t\t\t" . "<td>" . "\n";
			$insertText .= Text::_('PLG_BWPOSTMAN_FOOTER_USED_MAILINGLISTS_SHOW_MAILINGLISTS_TEXT');
			$insertText .= "\t\t\t\t\t" . "</td>" . "\n";
			$insertText .= "\t\t\t\t" . "</tr>" . "\n";

			$i = 0;

			foreach ($usedMailinglists as $usedMailinglist)
			{
				$insertText .= "\t\t\t\t" . '<tr class="show-mailinglists-row row-' . $i % 2 . '">' . "\n";
				$i++;
				$insertText .= "\t\t\t\t\t" . "<td>" . "\n";
				$insertText .= $usedMailinglist['title'];

				if ($this->params->get('show_mailinglists_recipients'))
				{
					$insertText .= Text::sprintf('PLG_BWPOSTMAN_FOOTER_USED_MAILINGLISTS_SHOW_MAILINGLISTS_RECIPIENTS', $usedMailinglist['nbrRecipients']);
				}

				$insertText .= "\t\t\t\t\t" . "</td>" . "\n";
				$insertText .= "\t\t\t\t" . "</tr>" . "\n";
			}

			$insertText .= "\t\t\t" . '</table>' . "\n";
		}

		return $insertText;
	}

	/**
	 * Method to get text to insert used mailing lists
	 *
	 * @param array     $usedMailinglists
	 *
	 * @return string
	 *
	 * @since 2.3.0
	 */
	private function insertMailinglistsAtTextFooter(array $usedMailinglists)
	{
		$insertText = '';

		if (is_array($usedMailinglists) && count($usedMailinglists))
		{
			$insertText .= "\n";
			$insertText .= Text::_('PLG_BWPOSTMAN_FOOTER_USED_MAILINGLISTS_SHOW_MAILINGLISTS_TEXT');
			$insertText .= "\n";

			foreach ($usedMailinglists as $usedMailinglist)
			{
				$insertText .= "\n\t";
				$insertText .= $usedMailinglist['title'];

				if ($this->params->get('show_mailinglists_recipients'))
				{
					$insertText .= Text::sprintf('PLG_BWPOSTMAN_FOOTER_USED_MAILINGLISTS_SHOW_MAILINGLISTS_RECIPIENTS', $usedMailinglist['nbrRecipients']);
				}

				$insertText .= "\n";
			}
		}

		return $insertText;
	}

	/**
	 * Method to get text to insert used user groups
	 *
	 * @param array     $usedUsergroups
	 *
	 * @return string
	 *
	 * @since 2.3.0
	 */
	private function insertUsergroupsAtHtmlFooter(array $usedUsergroups)
	{
		$insertText = '';

		if (is_array($usedUsergroups) && count($usedUsergroups))
		{
			$insertText .= "\t\t\t" . '<table id="show-usergroups" style="border-collapse: collapse;border-spacing: 0;">' . "\n";
			$insertText .= "\t\t\t\t" . '<tr class="show-usergroups-head">' . "\n";
			$insertText .= "\t\t\t\t\t" . "<td>" . "\n";
			$insertText .= Text::_('PLG_BWPOSTMAN_FOOTER_USED_MAILINGLISTS_SHOW_USERGROUPS_TEXT');
			$insertText .= "\t\t\t\t\t" . "</td>" . "\n";
			$insertText .= "\t\t\t\t" . "</tr>" . "\n";

			$i = 0;

			foreach ($usedUsergroups as $usedUsergroup)
			{
				$insertText .= "\t\t\t\t" . '<tr class="show-usergroups-row row-' . $i % 2 . '">' . "\n";
				$i++;
				$insertText .= "\t\t\t\t\t" . "<td>" . "\n";
				$insertText .= $usedUsergroup['title'];

				if ($this->params->get('show_usergroups_recipients'))
				{
					$insertText .= Text::sprintf('PLG_BWPOSTMAN_FOOTER_USED_MAILINGLISTS_SHOW_USERGROUPS_RECIPIENTS', $usedUsergroup['nbrRecipients']);
				}

				$insertText .= "\t\t\t\t\t" . "</td>" . "\n";
				$insertText .= "\t\t\t\t" . "</tr>" . "\n";
				$insertText .= "\t\t\t" . '</table>' . "\n";
			}
		}

		return $insertText;
	}

	/**
	 * Method to get text to insert used user groups
	 *
	 * @param array     $usedUsergroups
	 *
	 * @return string
	 *
	 * @since 2.3.0
	 */
	private function insertUsergroupsAtTextFooter(array $usedUsergroups)
	{
		$insertText = '';

		if (is_array($usedUsergroups) && count($usedUsergroups))
		{
			$insertText .= "\n";
			$insertText .= Text::_('PLG_BWPOSTMAN_FOOTER_USED_MAILINGLISTS_SHOW_USERGROUPS_TEXT');
			$insertText .= "\n";

			foreach ($usedUsergroups as $usedUsergroup)
			{
				$insertText .= "\n\t";
				$insertText .= $usedUsergroup['title'];

				if ($this->params->get('show_usergroups_recipients'))
				{
					$insertText .= Text::sprintf('PLG_BWPOSTMAN_FOOTER_USED_MAILINGLISTS_SHOW_USERGROUPS_RECIPIENTS', $usedUsergroup['nbrRecipients']);
				}

				$insertText .= "\n";
			}
		}

		return $insertText;
	}

	/**
	 * Method to get title of used mailing lists
	 *
	 * @param array $usedMlIds
	 *
	 * @return array|mixed
	 *
	 * @since 2.3.0
	 *
	 * @throws Exception
	 */
	protected function getUsedMailinglistsFromDb(array $usedMlIds)
	{
		$mailinglists = array();

		if (count($usedMlIds))
		{
			$db           = $this->db;
			$query        = $this->db->getQuery(true);

			$query->select($db->quoteName('id'));
			$query->select($db->quoteName('title'));
			$query->from($db->quoteName('#__bwpostman_mailinglists'));
			$query->where($db->quoteName('id') . ' IN (' . implode(',', $usedMlIds) . ')');

			$db->setQuery($query);

			try
			{
				$mailinglists = $db->loadAssocList();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}

		return $mailinglists;
	}

	/**
	 * Method to get the number of recipients of a mailing list
	 *
	 * @param string $mlId
	 *
	 * @return string|mixed
	 *
	 * @since 2.3.0
	 *
	 * @throws Exception
	 */
	protected function getNumberOfRecipientsByMailinglist($mlId)
	{
		$nbrRecipients = 0;

		$activeRecipients = $this->getActiveRecipients();

		if (is_array($activeRecipients) && count($activeRecipients))
		{
			$db       = $this->db;
			$query    = $this->db->getQuery(true);

			$query->select('COUNT(DISTINCT ' . $db->quoteName('subscriber_id') . ')');
			$query->from($db->quoteName('#__bwpostman_subscribers_mailinglists'));
			$query->where($db->quoteName('subscriber_id') . ' IN (' . implode(',', $activeRecipients) . ')');
			$query->where($db->quoteName('mailinglist_id') . ' = ' . $db->quote($mlId));

			$db->setQuery($query);

			try
			{
				$nbrRecipients = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}

		return $nbrRecipients;
	}

	/**
	 * @param array $usedUgIds
	 *
	 * @return array|mixed
	 *
	 * @since 2.3.0
	 *
	 * @throws Exception
	 */
	protected function getUsedUsergroupsFromDb(array $usedUgIds)
	{
		$usergroups = array();

		if (count($usedUgIds))
		{
			$db    = $this->db;
			$query = $db->getQuery(true);

			$query->select($db->quoteName('id'));
			$query->select($db->quoteName('title'));
			$query->from($db->quoteName('#__usergroups'));
			$query->where($db->quoteName('id') . ' IN (' . implode($usedUgIds) . ')');

			$db->setQuery($query);

			try
			{
				$usergroups = $db->loadAssocList();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}

		return $usergroups;
	}

	/**
	 * Method to get the number of recipients of a mailing list
	 *
	 * @param string $gid
	 *
	 * @return string|mixed
	 *
	 * @since 2.3.0
	 *
	 * @throws Exception
	 */
	protected function getNumberOfRecipientsByUsergroup($gid)
	{
		$nbrRecipients = 0;

		$db    = $this->db;
		$query = $db->getQuery(true);

		$query->select('COUNT(' . $db->quoteName('user_id') . ')');
		$query->from($db->quoteName('#__user_usergroup_map'));
		$query->where($db->quoteName('group_id') . ' = ' . $db->quote($gid));

		$db->setQuery($query);

		try
		{
			$nbrRecipients = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $nbrRecipients;
	}

	/**
	 * @param array $usedMailinglists
	 * @param array $usedUsergroups
	 *
	 * @return integer
	 *
	 * @since 2.3.0
	 *
	 * @throws Exception
	 */
	protected function getNbrAllRecipients(array $usedMailinglists, array $usedUsergroups)
	{
		$sumRecipients = 0;

		if (is_array($usedMailinglists) && count($usedMailinglists))
		{
			foreach ($usedMailinglists as $mailinglist)
			{
				$sumRecipients += $this->getNumberOfRecipientsByMailinglist($mailinglist['id']);
			}
		}

		if (is_array($usedUsergroups) && count($usedUsergroups))
		{
			foreach ($usedUsergroups as $usergroup)
			{
				$sumRecipients += $this->getNumberOfRecipientsByUsergroup($usergroup['id']);
			}
		}

		return $sumRecipients;
	}

	/**
	 *
	 * @return array
	 *
	 * @since version
	 * @throws Exception
	 */
	protected function getActiveRecipients()
	{
		$activeRecipients = array();
		$addWhere = '';

		$alsoUnconfirmed	= Factory::getApplication()->getUserState('bwpostman.send.alsoUnconfirmed', false);

		$db    = $this->db;
		$query = $this->db->getQuery(true);

		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__bwpostman_subscribers'));
		$query->where($db->quoteName('archive_flag') . ' = ' . $db->quote('0'));

		if ($alsoUnconfirmed)
		{
			$addWhere = "(";
		}

		$addWhere .= $db->quoteName('status') . ' = ' . $db->quote('1');

		if ($alsoUnconfirmed)
		{
			$addWhere .= " OR " . $db->quoteName('status') . ' = ' . $db->quote('0') . ')';
		}
		$query->where($addWhere);

		$db->setQuery($query);

		try
		{
			$activeRecipients = $db->loadColumn();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $activeRecipients;
	}

	/**
	 * Method to insert default css for the additional messages in the footer of the HTML newsletter
	 *
	 * @param string $text      html of the newsletter
	 *
	 * @return bool
	 *
	 * @throws \Exception
	 *
	 * @since       2.3.0
	 */
	public function onBwPostmanBeforeCustomCss(&$text)
	{
		$cssFile        = JPATH_PLUGINS . '/bwpostman/footerusedmailinglists/assets/css/default.css';
		$fileContent    = array();
		$cleanedContent = array();
		$fh             = fopen($cssFile, 'r');

		// Get default css from file at assets
		if ($fh === false)
		{
			return true;
		}

		// get file content
		while(!feof($fh))
		{
			$fileContent[] = fgets($fh);
		}

		fclose($fh);

		// Remove unneeded rows (comments, empty lines)
		foreach ($fileContent as $row)
		{
			if ((strpos($row, '/**') === false) && (stripos($row, ' *') === false) && (trim($row) != ''))
			{
				$cleanedContent[] = $row;
			}
		}

		$cssFromFile = implode("", $cleanedContent);

		// Add css to css of newsletter
		$text .= $cssFromFile;

		return true;
	}
}
