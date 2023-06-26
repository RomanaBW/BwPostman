<?php
/**
 * BwPostman NewsletterContent Plugin
 *
 * BwPostman NewsletterContent Plugin main file for BwPostman.
 *
 * @version %%version_number%%
 * @package BwPostman NewsletterContent Plugin
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

use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Cache\Controller\CallbackController;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Database\ParameterType;

if (!ComponentHelper::isEnabled('com_bwpostman')) {
	Factory::getApplication()->enqueueMessage(
		Text::_('PLG_BWPOSTMAN_PLUGIN_NEWSLETTERCONTENT_ERROR') . ', ' . Text::_('PLG_BWPOSTMAN_PLUGIN_NEWSLETTERCONTENT_COMPONENT_NOT_INSTALLED'),
		'error'
	);
	return false;
}

/**
 * Class plgBwPostmanNewslettercontent
 *
 * @since       4.2.0
 */
class PlgBwPostmanNewslettercontent extends JPlugin
{
	/**
	 * Database object
	 *
	 * @var    DatabaseDriver
	 *
	 * @since       4.2.0
	 */
	protected $db;

	/**
	 * Method to process things on newsletter content after adding one article
	 *
	 * @param array  $nl_content List of articles used for newsletter
	 * @param object $tpl        HTML template
	 * @param object $text_tpl   Text template
	 * @param array  $content    The complete content of the newsletter up to now as html content and text content
	 * @param int    $content_id The id of the current handled article
	 * @param string $context    The context working in
	 *
	 * @return bool
	 *
	 * @throws Exception
	 *
	 * @since       4.2.0
	 */
	public function onBwpmAfterRenderNewsletterArticle(array &$nl_content, object &$tpl, object &$text_tpl, array &$content, int $content_id, $context): bool
	{

		return true;
	}

	/**
	 * Method to override the modules list from ModuleHelper::load()
	 *
	 * @param array  $modules      The modules list
	 *
	 * @return bool
	 *
	 * @throws Exception
	 *
	 * @since       4.2.0
	 */
	public function onPrepareModuleList(&$modules): bool
	{
		$application = 'both';

		$app      = Factory::getApplication();
		$itemId   = $app->getInput()->getInt('Itemid', 0);
		$groups   = $app->getIdentity()->getAuthorisedViewLevels();

		if ($application === 'site')
		{
			$clientId = array(1);
			$cacheId  = 1;
		}
		elseif ($application === 'administrator')
		{
			$clientId = array(0);
			$cacheId  = 0;
		}
		elseif ($application === 'both')
		{
			$clientId = array(0, 1);
			$cacheId  = 99;
		}

		// Build a cache ID for the resulting data object
		$cacheId = implode(',', $groups) . '.' . $cacheId . '.' . $itemId;

		$db      = Factory::getDbo();
		$query   = $db->getQuery(true);
		$nowDate = Factory::getDate()->toSql();

		$query->select($db->quoteName(['m.id', 'm.title', 'm.module', 'm.position', 'm.content', 'm.showtitle', 'm.params', 'mm.menuid']))
			->from($db->quoteName('#__modules', 'm'))
			->join(
				'LEFT',
				$db->quoteName('#__modules_menu', 'mm'),
				$db->quoteName('mm.moduleid') . ' = ' . $db->quoteName('m.id')
			)
			->join(
				'LEFT',
				$db->quoteName('#__extensions', 'e'),
				$db->quoteName('e.element') . ' = ' . $db->quoteName('m.module')
				. ' AND ' . $db->quoteName('e.client_id') . ' = ' . $db->quoteName('m.client_id')
			)
			->where(
				[
					$db->quoteName('m.published') . ' = 1',
					$db->quoteName('e.enabled') . ' = 1',
//					$db->quoteName('m.client_id') . ' = :clientId',
				]
			)
//			->bind(':clientId', $clientId, ParameterType::INTEGER)
			->whereIn($db->quoteName('m.client_id'), $clientId)
			->whereIn($db->quoteName('m.access'), $groups)
			->extendWhere(
				'AND',
				[
					$db->quoteName('m.publish_up') . ' IS NULL',
					$db->quoteName('m.publish_up') . ' <= :publishUp',
				],
				'OR'
			)
			->bind(':publishUp', $nowDate)
			->extendWhere(
				'AND',
				[
					$db->quoteName('m.publish_down') . ' IS NULL',
					$db->quoteName('m.publish_down') . ' >= :publishDown',
				],
				'OR'
			)
			->bind(':publishDown', $nowDate)
			->extendWhere(
				'AND',
				[
					$db->quoteName('mm.menuid') . ' = :itemId',
					$db->quoteName('mm.menuid') . ' <= 0',
				],
				'OR'
			)
			->bind(':itemId', $itemId, ParameterType::INTEGER);

		// Filter by language
//		if ($app->isClient('site') && $app->getLanguageFilter() || $app->isClient('administrator') && static::isAdminMultilang()) {
//			$language = $app->getLanguage()->getTag();
//
//			$query->whereIn($db->quoteName('m.language'), [$language, '*'], ParameterType::STRING);
//			$cacheId .= $language . '*';
//		}

		$query->order($db->quoteName(['m.position', 'm.ordering']));

		// Set the query
		$db->setQuery($query);

		try {
			/** @var CallbackController $cache */
			$cache = Factory::getContainer()->get(CacheControllerFactoryInterface::class)
				->createCacheController('callback', ['defaultgroup' => 'com_modules']);

			$modules = $cache->get([$db, 'loadObjectList'], [], md5($cacheId), false);
		} catch (\RuntimeException $e) {
			$app->getLogger()->warning(
				Text::sprintf('JLIB_APPLICATION_ERROR_MODULE_LOAD', $e->getMessage()),
				['category' => 'jerror']
			);

			return false;
		}


		return true;
	}
}
