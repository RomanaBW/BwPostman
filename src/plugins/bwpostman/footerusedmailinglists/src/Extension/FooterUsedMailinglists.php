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

namespace BoldtWebservice\Plugin\BwPostman\FooterUsedMailinglists\Extension;

defined('_JEXEC') or die('Restricted access');

use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\User\UserFactoryAwareInterface;
use Joomla\CMS\User\UserFactoryAwareTrait;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Event\DispatcherInterface;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use RuntimeException;

/**
 * Class PlgBwPostmanFooterUsedMailinglists
 *
 * @since       2.3.0
 */
final class FooterUsedMailinglists extends CMSPlugin implements SubscriberInterface, DatabaseAwareInterface, UserFactoryAwareInterface
{
    use DatabaseAwareTrait;
    use UserFactoryAwareTrait;

    /**
	 * Database object
	 *
	 * @var    DatabaseDriver
	 *
	 * @since       2.3.0
	 */
	protected $db;

    /**
     * PlgBwPostmanFooterUsedMailinglists constructor.
     *
     * @param DispatcherInterface $dispatcher
     * @param array               $config
     *
     * @since       2.3.0
     */
	public function __construct(DispatcherInterface $dispatcher, array $config)
	{
		parent::__construct($dispatcher, $config);

		$this->loadLanguage();
	}

    /**
     * Returns an array of events this subscriber will listen to.
     *
     * @return  array
     *
     * @since 4.2.6
     */
    public static function getSubscribedEvents(): array
    {
        // Only subscribe events if the component is installed and enabled
        if (!ComponentHelper::isEnabled('com_bwpostman'))
        {
            return [];
        }
        else
        {
            return [
                'onBwPostmanBeforeObligatoryFooterHtml' => 'doBwPostmanBeforeObligatoryFooterHtml',
                'onBwPostmanBeforeObligatoryFooterText' => 'doBwPostmanBeforeObligatoryFooterText',
            ];
        }
    }

    /**
     * Method to insert the used mailing lists in the footer of the HTML newsletter
     *
     * @param Event $event
     *
     * @return void
     *
     * @throws Exception
     *
     * @since       2.3.0
     */
	public function doBwPostmanBeforeObligatoryFooterHtml(Event $event): void
	{
        $text = $event->getArgument('text');

		$app       = $this->getApplication();
		$usedUgIds = $app->getUserState('com_bwpostman.edit.newsletter.data.usergroups', array());

		$mlAvailable = $app->getUserState('com_bwpostman.edit.newsletter.data.ml_available', array());
		$mlUnavailable = $app->getUserState('com_bwpostman.edit.newsletter.data.ml_unavailable', array());
		$mlIntern = $app->getUserState('com_bwpostman.edit.newsletter.data.ml_intern', array());
		$usedMlIds = array_merge($mlAvailable, $mlUnavailable, $mlIntern);

		$usedMailinglists = $this->getUsedMailinglists($usedMlIds);
		$usedUsergroups   = $this->getUsedUsergroups($usedUgIds);
		$insertText       = '';
		$additionalFooter = '';

		if ($this->params->get('show_mailinglists', '0'))
		{
			$insertText .= $this->insertMailinglistsAtHtmlFooter($usedMailinglists);
		}

		if ($this->params->get('show_usergroups', '0'))
		{
			$insertText .= $this->insertUsergroupsAtHtmlFooter($usedUsergroups);
		}

		if ($this->params->get('show_all_recipients', '0'))
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
			$additionalFooter = "\n\t\t" . '<table class="show-subscribers" style="table-layout: fixed; width: 100%;">' . "\n";
			$additionalFooter .= "\t\t\t" . "<tr>" . "\n";
			$additionalFooter .= "\t\t\t\t" . "<td>";
			$additionalFooter .= $insertText;
			$additionalFooter .= "\t\t\t\t" . "</td>" . "\n";
			$additionalFooter .= "\t\t\t" . "</tr>" . "\n";
			$additionalFooter .= "\t\t" . "</table>" . "\n";
		}

		$text = str_replace('[%impressum%]', "\n" . $additionalFooter . '[%impressum%]', $text);
        $result = $event->getArgument('result') ?? [];
        $result[] = $text;

        $event->setArgument('result', $result);
	}

    /**
     * Method to insert the used mailing lists in the footer of the text newsletter
     *
     * @param Event $event
     *
     * @return void
     *
     * @throws Exception
     *
     * @since       2.3.0
     */
	public function doBwPostmanBeforeObligatoryFooterText(Event $event): void
	{
        $text = $event->getArgument('text');

        $app       = $this->getApplication();
		$usedUgIds = $app->getUserState('com_bwpostman.edit.newsletter.data.usergroups', array());

		$mlAvailable = $app->getUserState('com_bwpostman.edit.newsletter.data.ml_available', array());
		$mlUnavailable = $app->getUserState('com_bwpostman.edit.newsletter.data.ml_unavailable', array());
		$mlIntern = $app->getUserState('com_bwpostman.edit.newsletter.data.ml_intern', array());
		$usedMlIds = array_merge($mlAvailable, $mlUnavailable, $mlIntern);

		$usedMailinglists = $this->getUsedMailinglists($usedMlIds);
		$usedUsergroups   = $this->getUsedUsergroups($usedUgIds);
		$insertText       = '';

		if ($this->params->get('show_mailinglists', '0'))
		{
			$insertText .= $this->insertMailinglistsAtTextFooter($usedMailinglists);
		}

		if ($this->params->get('show_usergroups', '0'))
		{
			$insertText .= $this->insertUsergroupsAtTextFooter($usedUsergroups);
		}

		if ($this->params->get('show_all_recipients', '0'))
		{
			$nbrAllRecipients = $this->getNbrAllRecipients($usedMailinglists, $usedUsergroups);

			$insertText .= "\n";
			$insertText .= Text::sprintf('PLG_BWPOSTMAN_FOOTER_USED_MAILINGLISTS_SHOW_ALL_RECIPIENTS_TEXT', $nbrAllRecipients) . "\n";
			$insertText .= "\n";
		}

		$text = str_replace('[%impressum%]', "\n" . $insertText . '[%impressum%]', $text);

        $result   = $event->getArgument('result') ?? [];
        $result[] = $text;

        $event->setArgument('result', $result);
    }

	/**
	 * Method to get the names of the used mailinglists by newsletter or campaign id
	 *
	 * @param array $usedMlIds array of mailinglist ids
	 *
	 * @return array
	 *
	 * @throws Exception
	 *
	 * @since       2.3.0
	 */
	protected function getUsedMailinglists(array $usedMlIds): array
	{
		$mailinglists = $this->getUsedMailinglistsFromDb($usedMlIds);

		if (count($mailinglists))
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
	 * @param array $usedUgIds array of usergroup ids
	 *
	 * @return array
	 *
	 * @throws Exception
	 *
	 * @since       2.3.0
	 */
	protected function getUsedUsergroups(array $usedUgIds): array
	{
		$usergroups = $this->getUsedUsergroupsFromDb($usedUgIds);

		if (count($usergroups))
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
	 * @param int $id              newsletter  id
	 * @param int $checkUsergroups deliver mailinglists -> false or usergroups ->true
	 *
	 * @return array
	 *
	 * @throws Exception
	 *
	 * @since       2.3.0
	 */
	protected function getUsedRecipientsByNewsletter(int $id, int $checkUsergroups): array
	{
		$recipients = array();
		$db 	= $this->getDatabase();
		$query  = $db->getQuery(true);

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

		try
		{
			$db->setQuery($query);

			$recipients = $db->loadColumn();

			if ($recipients === null)
			{
				$recipients = array();
			}
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'Plg FUM FE');
        }

		return $recipients;
	}

	/**
	 * Method to get the used mailinglists or usergroups by campaigns
	 *
	 * @param int $id              newsletter  id
	 * @param int $checkUsergroups deliver mailinglists -> false or usergroups ->true
	 *
	 * @return array
	 *
	 * @throws Exception
	 *
	 * @since       2.3.0
	 */
	protected function getUsedRecipientsByCampaign(int $id, int $checkUsergroups): array
	{
		$recipients = array();
		$db 	= $this->getDatabase();
		$query  = $db->getQuery(true);

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

		try
		{
			$db->setQuery($query);

			$recipients = $db->loadColumn();

			if ($recipients === null)
			{
				$recipients = array();
			}
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'Plg FUM FE');
        }

		return $recipients;
	}

	/**
	 * Method to get the campaign id for newsletter if exists
	 *
	 * @param int $id newsletter  id
	 *
	 * @return integer
	 *
	 * @throws Exception
	 *
	 * @since       2.3.0
	 */
	protected function getCampaignIdByNewsletterId(int $id): int
	{
		$db 	= $this->getDatabase();
		$query  = $db->getQuery(true);

		$query->select($db->quoteName('campaign_id'));
		$query->from($db->quoteName('#__bwpostman_newsletters'));
		$query->where($db->quoteName('id') . ' = ' . $db->quote($id));

		try
		{
			$db->setQuery($query);

			$camId = (int)$db->loadResult();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'Plg FUM FE');

            return 0;
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
	private function insertMailinglistsAtHtmlFooter(array $usedMailinglists): string
	{
		$insertText = '';

		if (count($usedMailinglists))
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

				if ($this->params->get('show_mailinglists_recipients', '0'))
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
	private function insertMailinglistsAtTextFooter(array $usedMailinglists): string
	{
		$insertText = '';

		if (count($usedMailinglists))
		{
			$insertText .= "\n";
			$insertText .= Text::_('PLG_BWPOSTMAN_FOOTER_USED_MAILINGLISTS_SHOW_MAILINGLISTS_TEXT');
			$insertText .= "\n";

			foreach ($usedMailinglists as $usedMailinglist)
			{
				$insertText .= "\n\t";
				$insertText .= $usedMailinglist['title'];

				if ($this->params->get('show_mailinglists_recipients', '0'))
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
	private function insertUsergroupsAtHtmlFooter(array $usedUsergroups): string
	{
		$insertText = '';

		if (count($usedUsergroups))
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

				if ($this->params->get('show_usergroups_recipients', '0'))
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
	private function insertUsergroupsAtTextFooter(array $usedUsergroups): string
	{
		$insertText = '';

		if (count($usedUsergroups))
		{
			$insertText .= "\n";
			$insertText .= Text::_('PLG_BWPOSTMAN_FOOTER_USED_MAILINGLISTS_SHOW_USERGROUPS_TEXT');
			$insertText .= "\n";

			foreach ($usedUsergroups as $usedUsergroup)
			{
				$insertText .= "\n\t";
				$insertText .= $usedUsergroup['title'];

				if ($this->params->get('show_usergroups_recipients', '0'))
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
	 * @return array
	 *
	 * @since 2.3.0
	 *
	 * @throws Exception
	 */
	protected function getUsedMailinglistsFromDb(array $usedMlIds): array
	{
		$mailinglists = array();

		if (count($usedMlIds))
		{
			$db    = $this->getDatabase();
			$query = $db->getQuery(true);

			$query->select($db->quoteName('id'));
			$query->select($db->quoteName('title'));
			$query->from($db->quoteName('#__bwpostman_mailinglists'));
			$query->where($db->quoteName('id') . ' IN (' . implode(',', $usedMlIds) . ')');

			try
			{
				$db->setQuery($query);

				$mailinglists = $db->loadAssocList();

				if ($mailinglists === null)
				{
					$mailinglists = array();
				}
			}
			catch (RuntimeException $exception)
			{
                BwPostmanHelper::logException($exception, 'Plg FUM FE');
            }
		}

		return $mailinglists;
	}

	/**
	 * Method to get the number of recipients of a mailing list
	 *
	 * @param string $mlId
	 *
	 * @return integer
	 *
	 * @throws Exception
	 *@since 2.3.0
	 *
	 */
	protected function getNumberOfRecipientsByMailinglist(string $mlId): int
	{
		$nbrRecipients = 0;

		$activeRecipients = $this->getActiveRecipients();

		if (count($activeRecipients))
		{
			$db    = $this->getDatabase();
			$query = $db->getQuery(true);

			$query->select('COUNT(DISTINCT ' . $db->quoteName('subscriber_id') . ')');
			$query->from($db->quoteName('#__bwpostman_subscribers_mailinglists'));
			$query->where($db->quoteName('subscriber_id') . ' IN (' . implode(',', $activeRecipients) . ')');
			$query->where($db->quoteName('mailinglist_id') . ' = ' . $db->quote($mlId));

			try
			{
				$db->setQuery($query);

				$nbrRecipients = (int)$db->loadResult();
			}
			catch (RuntimeException $exception)
			{
                BwPostmanHelper::logException($exception, 'Plg FUM FE');
            }
		}

		return $nbrRecipients;
	}

	/**
	 * @param array $usedUgIds
	 *
	 * @return array
	 *
	 * @since 2.3.0
	 *
	 * @throws Exception
	 */
	protected function getUsedUsergroupsFromDb(array $usedUgIds): array
	{
		$usergroups = array();

		if (count($usedUgIds))
		{
			$db    = $this->getDatabase();
			$query = $db->getQuery(true);

			$query->select($db->quoteName('id'));
			$query->select($db->quoteName('title'));
			$query->from($db->quoteName('#__usergroups'));
			$query->where($db->quoteName('id') . ' IN (' . implode($usedUgIds) . ')');

			try
			{
				$db->setQuery($query);

				$usergroups = $db->loadAssocList();

				if ($usergroups === null)
				{
					$usergroups = array();
				}
			}
			catch (RuntimeException $exception)
			{
                BwPostmanHelper::logException($exception, 'Plg FUM FE');
            }
		}

		return $usergroups;
	}

	/**
	 * Method to get the number of recipients of a mailing list
	 *
	 * @param string $gid
	 *
	 * @return integer
	 *
	 * @throws Exception
	 *@since 2.3.0
	 *
	 */
	protected function getNumberOfRecipientsByUsergroup(string $gid): int
	{
		$nbrRecipients = 0;

		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

		$query->select('COUNT(' . $db->quoteName('user_id') . ')');
		$query->from($db->quoteName('#__user_usergroup_map'));
		$query->where($db->quoteName('group_id') . ' = ' . $db->quote($gid));

		try
		{
			$db->setQuery($query);

			$nbrRecipients = (int)$db->loadResult();
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'Plg FUM FE');
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
	protected function getNbrAllRecipients(array $usedMailinglists, array $usedUsergroups): int
	{
		$sumRecipients = 0;

		if (count($usedMailinglists))
		{
			foreach ($usedMailinglists as $mailinglist)
			{
				$sumRecipients += $this->getNumberOfRecipientsByMailinglist($mailinglist['id']);
			}
		}

		if (count($usedUsergroups))
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
	protected function getActiveRecipients(): array
	{
		$activeRecipients = array();
		$addWhere = '';

		$alsoUnconfirmed	= Factory::getApplication()->getUserState('bwpostman.send.alsoUnconfirmed', false);

		$db    = $this->getDatabase();
		$query = $db->getQuery(true);

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

		try
		{
			$db->setQuery($query);

			$activeRecipients = $db->loadColumn();

			if ($activeRecipients === null)
			{
				$activeRecipients = array();
			}
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'Plg FUM FE');
        }

		return $activeRecipients;
	}

	/**
	 * Method to insert default css for the additional messages in the footer of the HTML newsletter
	 *
	 * @param string $text html of the newsletter
	 *
	 * @return bool
	 *
	 * @throws Exception
	 *
	 * @since       2.3.0
	 */
	public function onBwPostmanBeforeCustomCss(string &$text): bool
	{
		$cssFile        = JPATH_ROOT . '/media/plg_bwpostman_footerusedmailinglists/css/default.css';
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
