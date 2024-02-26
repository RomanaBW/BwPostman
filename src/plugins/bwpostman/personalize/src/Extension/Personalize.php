<?php
/**
 * BwPostman Personalize Plugin
 *
 * BwPostman Personalize Plugin main file for BwPostman.
 *
 * @version %%version_number%%
 * @package BwPostman Personalize Plugin
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

namespace BoldtWebservice\Plugin\BwPostman\Personalize\Extension;

defined('_JEXEC') or die('Restricted access');

use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use Exception;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Event\Event;
use Joomla\Event\SubscriberInterface;
use RuntimeException;

/**
 * Class Personalize
 *
 * @since       2.0.0
 */
final class Personalize extends CMSPlugin implements SubscriberInterface, DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    /**
     * Definition of which contexts to allow in this plugin
     *
     * @var    array
     *
     * @since  0.9.0
     */
    protected $allowedContext = array(
        'com_bwpostman.send',
        'com_bwpostman.view',
    );

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
                'onBwPostmanPersonalize' => 'doBwPostmanPersonalize',
            ];
        }
    }

    /**
     * Method to write enhanced personalization in the body of the newsletter
     *
     * Inserts male or female string depending on gender of subscriber. If no gender is available, male string is used.
     * At incomplete plugin parameters an empty string is inserted. We don't want incomplete plugin characters in
     * newsletter.
     *
     * @param Event $event
     *
     * @eventArgs   string $context context of the newsletter to display
     * @eventArgs   string $body    the body of the newsletter
     * @eventArgs   int    $id      subscriber ID or user ID, depends on the context     * @return void
     *
     * @throws Exception
     *
     * @since       2.0.0
     */
	public function doBwPostmanPersonalize(Event $event): void
	{
        $context = $event->getArgument('context');

        if (!in_array($context, $this->allowedContext))
        {
            return;
        }

        $body    = $event->getArgument('body');
        $id      = $event->getArgument('id');

        $gender = 2;

		// get gender
		if ($context === 'com_bwpostman.send')
		{
			$gender = $this->getGenderFromSubscriberId($id);
		}
		elseif ($id > 0)
		{
			$gender = $this->getGenderFromUserId($id);
		}

		// Start Plugin
		$regex_one		= '/(\[bwpostman_personalize\s*)(.*?)(\])/is';
		$regex_all		= '/\[bwpostman_personalize\s*.*?\]/si';
		$matches 		= array();
		$count_matches	= preg_match_all($regex_all, $body, $matches, PREG_OFFSET_CAPTURE | PREG_PATTERN_ORDER);

		for($j = 0; $j < $count_matches; $j++)
		{
			// Get plugin parameters
			$bwpm_personalize	= $matches[0][$j][0];
			preg_match($regex_one, $bwpm_personalize, $bwpm_personalize_parts);

			$gender_strings = $this->extractGenderStrings($bwpm_personalize_parts);

			// set replace value depending on gender
			$replace_value = $gender_strings[$gender];

			// modify newsletter body
			$body = preg_replace($regex_all, $replace_value, $body, 1);
		}

        $result   = $event->getArgument('result') ?? [];
        $result[] = $body;

        $event->setArgument('result', $result);
	}

	/**
	 * Method to get the gender of the subscriber by subscriber_id
	 *
	 * @param int $id subscriber ID
	 *
	 * @return int  $gender gender of subscriber, 0 = male, 1 = female, 2 = n.a.
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0
	 */
	protected function getGenderFromSubscriberId(int $id): int
	{
		$gender = 2;
        $db = $this->getDatabase();
        $query  = $db->getQuery(true);

		$query->select($db->quoteName('gender'));
		$query->from('#__bwpostman_subscribers');
		$query->where($db->quoteName('id') . ' = ' . $id);

		try
		{
			$db->setQuery($query);

			$gender = $db->loadResult();

			if ($gender === null)
			{
				$gender = 2;
			}
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'Plg Personalize FE');

            $this->getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return $gender;
	}

	/**
	 * Method to get the gender of the subscriber by user_id
	 *
	 * @param int $id user ID
	 *
	 * @return int  $gender gender of subscriber
	 *
	 * @throws Exception
	 *
	 * @since       2.0.0
	 */
	protected function getGenderFromUserId(int $id): int
	{
		$gender = 2;

        $db    = $this->getDatabase();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('gender'));
		$query->from('#__bwpostman_subscribers');
		$query->where($db->quoteName('user_id') . ' = ' . $id);

		try
		{
			$db->setQuery($query);

			$gender = $db->loadResult();

			if ($gender === null)
			{
				$gender = 2;
			}
		}
		catch (RuntimeException $exception)
		{
            BwPostmanHelper::logException($exception, 'Plg Personalize FE');

            $this->getApplication()->enqueueMessage($exception->getMessage(), 'error');
		}

		return $gender;
	}

	/**
	 * Method to extract the gender related strings form the plugin string
	 *
	 * @param array $bwpm_personalize_parts
	 *
	 * @return array    $parts
	 *
	 * @since       2.0.0
	 */
	protected function extractGenderStrings(array $bwpm_personalize_parts): array
	{
		$parts = explode("|", $bwpm_personalize_parts[2]);
		array_shift($parts);
		$gender_string  = array();

		foreach ($parts as $part) {
			// extract sting between double quote
			$start  = strpos($part, '"');
			$end    = strrpos($part, '"');
			if (($start !== false) && ($end !== false))
			{
				$gender_string[] = substr($part, $start + 1, $end - $start - 1);
			}
			// if there are not two double quotes set string to original string (do nothing)
			else
			{
				$gender_string[] = $bwpm_personalize_parts[0];
			}
		}

		// if personalization parameters are incomplete, fill with original string (do nothing)
		while (count($gender_string) < 3)
		{
			$gender_string[] = $bwpm_personalize_parts[0];
		}

		return $gender_string;
	}
}
