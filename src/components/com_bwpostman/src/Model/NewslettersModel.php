<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletter all model for frontend.
 *
 * @version %%version_number%%
 * @package BwPostman-Site
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

namespace BoldtWebservice\Component\BwPostman\Site\Model;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\TagsHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Table\Table;
use Joomla\Database\QueryInterface;
use Joomla\Registry\Registry;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Database\DatabaseQuery;
use Joomla\Utilities\ArrayHelper;
use RuntimeException;

/**
 * Class BwPostmanModelNewsletters
 *
 * @since       0.9.1
 */
class NewslettersModel extends ListModel
{

	/**
	 * property to hold context
	 *
	 * @var string $context
	 *
	 * @since       0.9.1
	 */
	public $context = 'com_bwpostman.newsletters';

	/**
	 * property to hold extension name
	 *
	 * @var string
	 *
	 * @since       0.9.1
	 */
	protected string $extension = 'com_bwpostman';

	/**
	 * property to hold newsletters
	 *
	 * @var ?array
	 *
	 * @since       0.9.1
	 */
	protected ?array $newsletters = null;

	/**
	 * Newsletter id
	 *
	 * @var ?integer
	 *
	 * @since       0.9.1
	 */
	private ?int $id = null;

	/**
	 * Newsletter data
	 *
	 * @var ?array
	 *
	 * @since       0.9.1
	 */
	private ?array $data = null;

	/**
	 * Pagination object
	 *
	 * @var ?Pagination
	 *
	 * @since       0.9.1
	 */
	private $pagination = null;

	/**
	 * Constructor
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function __construct()
	{
		parent::__construct();

		$jinput	= Factory::getApplication()->input;

		$id = $jinput->get('id');
		$this->setId((int) $id);

		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'a.subject', 'subject',
				'a.mailing_date', 'mailing_date',
				'a.id', 'id',
				'a.id', 'id',
				'a.mailinglist', 'mailinglist',
				'a.published, published',
				'a.archive_date, archive_date',
				'access','access_level',
				'language',
				'a.hits', 'hits',
				'a.ordering, ordering'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	string $name    The table type to instantiate
	 * @param	string $prefix  A prefix for the table class name. Optional.
	 * @param	array  $options Configuration array for model. Optional.
	 *
	 * @return	Table	A database object
	 *
	 * @throws Exception
	 *
	 * @since  1.0.1
	 */
	public function getTable($name = 'Newsletter', $prefix = 'Administrator', $options = array()): Table
	{
		return parent::getTable($name, $prefix, $options);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param string|null    $ordering
	 * @param string|null    $direction
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialize variables.
		$app	= Factory::getApplication();
		$jinput	= $app->input;
		$pk		= $jinput->getInt('id');

		// Load state from the request.
		$this->setState('newsletter.id', $pk);

		// Load the parameters. Merge Global and Menu Item params into new object
		$params     = ComponentHelper::getParams($this->extension);
		$menuParams = new Registry;
		$menu       = $app->getMenu()->getActive();

		if ($menu)
		{
			$menuParams->loadString($menu->getParams());
		}

		$mergedParams = clone $menuParams;
		$params->merge($mergedParams);

		$this->setState('params', $params);
		$app->setUserState('com_bwpostman.newsletters.params', $params);

		// Set module ID
		$this->setState('module.id', $app->input->getInt('mid'));

		// Filter on month, year
		$this->setState('filter.month', $app->input->getString('month'));
		$this->setState('filter.year', $app->input->getString('year'));

		// Optional filter text
		$this->setState('filter.search', $jinput->getString('filter_search'));

		$mailinglist = $this->getUserStateFromRequest('com_bwpostman.newsletters.filter.mailinglist', 'filter_mailinglist', '');
		$this->setState('filter.mailinglist', $mailinglist);

		$campaign = $this->getUserStateFromRequest('com_bwpostman.newsletters.filter.campaign', 'filter_campaign', '');
		$this->setState('filter.campaign', $campaign);

		$usergroup = $this->getUserStateFromRequest('com_bwpostman.newsletters.filter.usergroup', 'filter_usergroup', '');
		$this->setState('filter.usergroup', $usergroup);

		// filter.order
		$orderCol	= $app->getUserStateFromRequest('com_bwpostman.newsletters.filter_order', 'filter_order', 'mailing_date', 'string');
		$this->setState('list.ordering', $orderCol);

		$listOrder = $app->getUserStateFromRequest('com_bwpostman.newsletters.list.filter_order_Dir', 'filter_order_Dir', 'DESC', 'cmd');
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', '')))
		{
			$listOrder = 'DESC';
		}

		$this->setState('list.direction', $listOrder);

		$this->setState('layout', $jinput->getCmd('layout'));

		$limit = (int) $app->getUserStateFromRequest('com_bwpostman.newsletters.list.limit', 'limit', $params->get('display_num', '10'), 'uint');
		$this->setState('list.limit', $limit);

		$limitstart = $app->input->get('start');

		if ($limitstart === null)
		{
			$limitstart = $app->input->get('limitstart');
		}

		$this->setState('list.start', $limitstart);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.

	 * @since	1.0.1
	 */
	protected function getStoreId($id = ''): string
	{
		// Compile the store id.
		$id .= ':' . serialize($this->getState('filter.published'));
		$id .= ':' . $this->getState('filter.access');
		$id .= ':' . $this->getState('filter.subject');
		$id .= ':' . $this->getState('filter.mailing_date');
		$id .= ':' . $this->getState('filter.hits');
		$id	.= ':' . $this->getState('filter.mailinglist');
		$id	.= ':' . $this->getState('filter.campaign');
		$id	.= ':' . $this->getState('filter.usergroup');
		$id	.= ':' . $this->getState('filter.month');
		$id	.= ':' . $this->getState('filter.year');
		$id	.= ':' . $this->getState('getTotal');

		return parent::getStoreId($id);
	}

	/**
	 * Method to reset the newsletter ID and newsletter data
	 *
	 * @access	public
	 *
	 * @param int $id Newsletter ID
	 *
	 * @since       0.9.1
	 */
	public function setId(int $id = 0)
	{
		// Set new venue ID and wipe data
		$this->id   = $id;
		$this->data = null;
	}

	/**
	 * Returns a record count for the query.
	 * Override because fast COUNT version will result in wrong number
	 *
	 * @param	DatabaseQuery|string  $query  The query.
	 *
	 * @return	integer  Number of rows for query.
	 *
	 * @throws Exception
	 *
	 * @since	1.2.0
	 */
	protected function _getListCount($query): int
	{
		// fall back to inefficient way of counting all results.
		$result = 0;

		// Remove the limit and offset part if it's a DatabaseQuery object
		if ($query instanceof DatabaseQuery)
		{
			$query = clone $query;
			$query->clear('limit');
		}

		try
		{
			$this->_db->setQuery($query);
			$this->_db->execute();

			$result = $this->_db->getNumRows();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $result;
	}

	/**
	 * Method to get a list of newsletters.
	 *
	 * Overridden to inject convert the attributes field into a Parameter object.
	 *
	 * @return	array|null	An array of objects on success, false on failure.
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	public function getItems(): ?array
	{
		$items	= parent::getItems();
		$user	= Factory::getApplication()->getIdentity();
		$userId	= $user->get('id');
		$guest	= $user->get('guest');
		$groups	= $user->getAuthorisedViewLevels();

//		$this->pagination = parent::getPagination();

		// Convert the parameter fields into objects.
		foreach ($items as $item)
		{
			$item->params = clone $this->getState('params');

			// Get display date
			switch ($item->params->get('list_show_date', ''))
			{
				case 'modified_time':
					$item->displayDate = $item->modified_time;
					break;

				case 'published':
					$item->displayDate = ($item->publish_up == 0) ? $item->created_date : $item->publish_up;
					break;

				case 'created_date':
					$item->displayDate = $item->created_date;
					break;

				default:
				case 'mailing_date':
					$item->displayDate = $item->mailing_date;
					break;
			}

			// Compute the asset access permissions.
			// Technically guest could edit a newsletter, but let's not check that to improve performance a little.
			if (!$guest)
			{
				$asset = 'com_bwpostman.newsletter.' . $item->id;

				// Check general edit permission first.
				if ($user->authorise('bwpm.edit', $asset))
				{
					$item->params->set('access-edit', true);
				}

				// Now check if edit.own is available.
				elseif (!empty($userId) && $user->authorise('bwpm.edit.own', $asset))
				{
					// Check for a valid user and that they are the owner.
					if ($userId == $item->created_by)
					{
						$item->params->set('access-edit', true);
					}
				}
			}

			$access = $this->getState('filter.access');

			if ($access)
			{
				// If the access filter has been set, we already have only the newsletters this user can view.
				$item->params->set('access-view', true);
			}
			else
			{
				// If no access filter is set, the layout takes some responsibility for display of limited information.
				$item->params->set('access-view', in_array($item->access, $groups));
			}

			// Get the tags
			$item->tags = new TagsHelper();
			$item->tags->getItemTags('com_bwpostman.newsletter', $item->id);
		}

		return $items;
	}

	/**
	 * Method to get mailing dates options of listed newsletters.
	 *
	 *
	 * @return    array|null    An array of options for date filter.
	 *
	 * @throws Exception
	 *
	 * @since    4.1.3
	 */
	public function getDateOptions(): ?array
	{
		// unset/set list limit and get all items
		$limit	= $this->getState('list.limit', 0);
		$limitstart = $this->getState('list.start', 0);

		$this->setState('list.limit', 0);
		$this->setState('list.start', 0);

		$items	= $this->getItems();

		$this->setState('list.limit', $limit);
		$this->setState('list.start', $limitstart);


		// Substrings of mailingdate
		$ms = array(); //months
		$ys = array(); //years
		foreach ($items as $item)
		{
			$ms[] = substr($item->mailing_date,5,2);
			$ys[] = substr($item->mailing_date,0,4);
		}

		// Month Field
		$months = array(
			'01' => Text::_('JANUARY_SHORT'),
			'02' => Text::_('FEBRUARY_SHORT'),
			'03' => Text::_('MARCH_SHORT'),
			'04' => Text::_('APRIL_SHORT'),
			'05' => Text::_('MAY_SHORT'),
			'06' => Text::_('JUNE_SHORT'),
			'07' => Text::_('JULY_SHORT'),
			'08' => Text::_('AUGUST_SHORT'),
			'09' => Text::_('SEPTEMBER_SHORT'),
			'10' => Text::_('OCTOBER_SHORT'),
			'11' => Text::_('NOVEMBER_SHORT'),
			'12' => Text::_('DECEMBER_SHORT')
		);

		// unique month
		foreach ($months as $key=>$month) {
			if (!in_array($key, array_unique($ms))) {
				unset($months[$key]);
			}
		}
		$date_options['months'] = array('' => Text::_('COM_BWPOSTMAN_MONTH')) + $months;

		// Year Field
		$years = array();

		// sorted unique years
		$ys = array_unique($ys);
		asort($ys);
		foreach($ys as $v) {
			$years[$v] = $v;
		}
		$date_options['years'] = array('' => Text::_('JYEAR')) + $years;

		return $date_options;
	}

	/**
	 * Method to build the MySQL query
	 *
	 * @return false|QueryInterface Query
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	protected function getListQuery()
	{
		// define variables
		$db		= $this->_db;
		$query		= $db->getQuery(true);

		// Define null and now dates, get params
		$nullDate	= $db->quote($db->getNullDate());
		$nowDate	= $db->quote(Factory::getDate()->toSql());
		$params      = $this->getAppropriateParams();

		// get accessible mailing lists
		$mls	= $this->getAccessibleMailinglists(false);

		$groups	= $this->getAccessibleUsergroups(false);

		if (is_array($groups) && count($groups) > 0)
		{
			// merge mailinglists and usergroups and remove multiple values
			$mls	= array_merge($mls, $groups);
			$mls	= array_unique($mls);
		}

		// get accessible campaigns
		$cams	= $this->getAccessibleCampaigns(false);

		// Filter by mailing list
		$mailinglist = $this->getState('filter.mailinglist');

		if ($mailinglist)
		{
			$filter_mls	= array();

			$filter_mls[]	= $mls[array_search($mailinglist, $mls)];
			$mls			= $filter_mls;
			$cams			= array(0 => 0);
			$this->setState('filter.campaign', '');
			$this->setState('filter.usergroup', '');
		}

		// Filter by user group
		$usergroup = $this->getState('filter.usergroup');

		if ($usergroup)
		{
			$filter_mls	= array();

			$filter_mls[]	= $mls[array_search($usergroup, $mls)];
			$mls			= $filter_mls;
			$cams			= array(0 => 0);
			$this->setState('filter.campaign', '');
			$this->setState('filter.mailinglist', '');
		}

		// Filter by campaign
		$campaign = $this->getState('filter.campaign');

		if ($campaign)
		{
			$filter_cam	= array();

			$filter_cam[]	= $cams[array_search($campaign, $cams)];
			$cams			= $filter_cam;
			$mls			= array(0 => 0);
			$this->setState('filter.usergroup', '');
			$this->setState('filter.mailinglist', '');
		}

		// build query
		$query->select(
			$this->getState(
				'list.select',
				'DISTINCT(a.id), a.subject, a.mailing_date, a.hits, a.campaign_id, a.access, a.created_date, a.attachment, ' .
				// Use mailing date if publish_up is 0
				'CASE WHEN (a.publish_up = ' . $nullDate . ' OR a.publish_up = null) THEN a.mailing_date ELSE a.publish_up END as publish_up,' .
				'publish_down'
			)
		);

		$query->from($db->quoteName('#__bwpostman_newsletters') . ' AS ' . $db->quoteName('a'));
		// in front end only sent and published newsletters are shown!
		$query->where($db->quoteName('a') . '.' . $db->quoteName('published') . ' = ' . 1);
		$query->where($db->quoteName('a') . '.' . $db->quoteName('mailing_date') . ' != ' . $db->quote($db->getNullDate())
		. ' AND ' . $db->quoteName('a') . '.' . $db->quoteName('mailing_date') . ' IS NOT NULL');

		// Filter by mailing lists, user groups and campaigns
		$query->leftJoin('#__bwpostman_newsletters_mailinglists AS m ON a.id = m.newsletter_id');

		$whereMlsCamsClause = BwPostmanHelper::getWhereMlsCamsClause($mls, $cams);

		$query->where($whereMlsCamsClause);

		switch ($params->get('show_type', 'not_arc_down'))
		{
			default:
			case 'all_not_arc':
					$query->where('a.archive_flag = 0');
				break;
			case 'not_arc_down':
					$query->where('a.archive_flag = 0');
					$query->where('a.publish_up <= ' . $nowDate);
					$query->where('(a.publish_down >= ' . $nowDate . ' OR a.publish_down = ' . $nullDate . ' OR a.publish_down IS NULL)');
				break;
			case 'not_arc_but_down':
					$query->where('a.archive_flag = 0');
					$query->where('a.publish_up <= ' . $nowDate);
					$query->where('a.publish_down <> ' . $nullDate . 'a.publish_down IS NOT NULL');
					$query->where('a.publish_down <= ' . $nowDate);
				break;
			case 'arc':
					$query->where('a.archive_flag = 1');
				break;
			case 'down':
					$query->where('a.publish_up <= ' . $nowDate);
					$query->where('a.publish_down <> ' . $nullDate . 'a.publish_down IS NOT NULL');
					$query->where('a.publish_down <= ' . $nowDate);
				break;
			case 'arc_and_down':
					$query->where('a.archive_flag = 1');
					$query->where('a.publish_up <= ' . $nowDate);
					$query->where('a.publish_down <> ' . $nullDate . 'a.publish_down IS NOT NULL');
					$query->where('a.publish_down <= ' . $nowDate);
				break;
			case 'arc_or_down':
					$query->where(
						'(a.archive_flag = 1
							OR (
								(a.publish_down <> ' . $nullDate . ' OR a.publish_down IS NOT NULL) 
								AND a.publish_down <= ' . $nowDate . '
								AND a.publish_up <= ' . $nowDate . '
							))'
					);
				break;
			case 'all':
				break;
		}

		// Filter by search word.
		$searchword	= $this->getState('filter.search');
		if (($params->get('filter_field', '1') != 'hide') && !empty($searchword))
		{
			$search	= '%' . $db->escape($this->getState('filter.search'), true) . '%';
			$query->where('subject LIKE ' . $db->quote($search, false));
		}

		// Filter on month
		$month = $this->getState('filter.month', '');
		if ($month !== '')
		{
			$query->where($query->month('a.mailing_date') . ' = ' . $month);
		}

		// Filter on year
		$year = $this->getState('filter.year', '');
		if ($year !== '')
		{
			$query->where($query->year('a.mailing_date') . ' = ' . $year);
		}

		// Set the list limit
		$limit	= (int) $params->get('display_num', '10');
		$limit	= $this->getState('filter.limit', $limit);
		$this->setState('filter.limit', $limit);

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'a.mailing_date');
		$orderDirn	= $this->state->get('list.direction', 'DESC');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));
		$query->group($db->quoteName('a.mailing_date'));

		try
		{
			$db->setQuery($query);
		}
		catch (RuntimeException $e)
		{
			$msg = Text::_('COM_BWPOSTMAN_ERROR_GET_LIST_QUERY_ERROR'). ' ' . $e->getMessage();
			Factory::getApplication()->enqueueMessage($msg, 'error');
			return false;
		}

		return $query;
	}

	/**
	 * Method to get the params from selected menu item
	 *
	 * @param integer $menuItem
	 *
	 * @return 	Registry
	 *
	 * @throws Exception
	 *
	 * @since       3.0.0
	 */
	public function getParamsFromSelectedMenuEntry(int $menuItem): Registry
	{
		$menu	= Factory::getApplication()->getMenu();

		return $menu->getParams($menuItem);
	}

	/**
	 * Method to get the menu item ID which will be needed for some links
	 *
	 * @return 	int menu item ID
	 *
	 * @throws Exception
	 *
	 * @since       3.0.0
	 */
	public function getMenuItemid(): int
	{
		$app    = Factory::getApplication();
		$itemid = $app->getUserState('com_bwpostman.newsletters.itemid');

		if ($itemid === null)
		{
			$db   = $this->_db;
			$query = $db->getQuery(true);

			$query->select($db->quoteName('id'));
			$query->from($db->quoteName('#__menu'));
			$query->where($db->quoteName('link') . ' = ' . $db->quote('index.php?option=com_bwpostman&view=newsletters'));
			$query->where($db->quoteName('client_id') . ' = ' . 0);

			try
			{
				$db->setQuery((string) $query);

				$itemid = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				$app->enqueueMessage($e->getMessage(), 'error');
			}
		}

		if (is_null($itemid))
		{
			$itemid = 0;
		}

		return $itemid;
	}

	/**
	 * Method to get all published mailing lists which the user is authorized to see
	 *
	 * @return 	array	ID and title of allowed mailinglists
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	public function getAllowedMailinglists(): array
	{
		$user = Factory::getApplication()->getIdentity();

		// get authorized viewlevels
		$viewLevels	= Access::getAuthorisedViewLevels($user->id);

		return $this->getTable('_Mailinglist')->getAllowedMailinglists($viewLevels);
	}

	/**
	 * Method to get all published mailing lists which the user is authorized to see and which are selected in menu
	 *
	 * @param boolean $title with title
	 *
	 * @return 	array	$mailinglists   ID and title of allowed mailinglists
	 *
	 * @throws Exception
	 *
	 * @since	1.2.0
	 */
	public function getAccessibleMailinglists(bool $title = true): array
	{
		$params        = $this->getAppropriateParams();
		$check         = $params->get('access-check', '1');
		$mlTable       = $this->getTable('Mailinglist');

		// fetch only from mailing lists, which are selected, if so
		$all_mls = $params->get('ml_selected_all', 'no');
		$sel_mls = $params->get('ml_available', '');
		$mls     = $sel_mls;

		if ($all_mls)
		{
			$mls = $mlTable->getPublishedMailinglistsIds();
		}

		// if no mailinglist is left, make array
		if (!is_array($mls))
		{
			$mls[]	= 0;
		}

		// Check permission, if desired
		if ($all_mls || $check != 'no')
		{
			$acc_mls = $this->getMailinglistsByViewlevel();

			$mls	= array_intersect($mls, $acc_mls);
		}

		// if no mailinglist is left, make array
		if (!is_array($mls))
		{
			$mls[]	= 0;
		}

		$mls = ArrayHelper::toInteger($mls);

		$mailinglists	= $mls;

		if ($title === true && count($mls))
		{
			$mailinglists	= $mlTable->getMailinglistsIdTitle($mls);
		}

		return $mailinglists;
	}

	/**
	 * Method to get all campaigns which the user is authorized to see
	 *
	 * @param boolean $title with title
	 *
	 * @return array
	 *
	 * @throws Exception
	 *
	 * @since	1.2.0
	 */
	public function getAccessibleCampaigns(bool $title = true): array
	{
		$params       = $this->getAppropriateParams();
		$check        = $params->get('access-check', '1');

		// fetch only from campaigns, which are selected, if so
		$all_cams = $params->get('cam_selected_all', 'no');
		$sel_cams = $params->get('cam_available', '');
		$cams     = $sel_cams;

		if ($all_cams)
		{
			$cams	= $this->getTable('Campaign')->getAllCampaignIds();
		}

		// if no cam is left, make array
		if (!is_array($cams) || count($cams) === 0)
		{
			$cams[]	= 0;
		}

		$cams = ArrayHelper::toInteger($cams);

		// Check permission, if desired
		if ($all_cams || $check != 'no')
		{
			$mailinglists = $this->getMailinglistsByViewlevel();
			$cams    = $this->getTable('CampaignsMailinglists')->getAllCampaignIdsByMlCam($mailinglists, $cams);
		}

		// if no cam is left, make array to return
		if (count($cams) === 0)
		{
			$cams[]	= 0;
		}

		$campaigns	= $cams;

		if ($title === true)
		{
			$campaigns	= $this->getTable('Campaign')->getCampaignsIdTitle($cams);
		}

		return $campaigns;
	}

	/**
	 * Method to get all user groups which the user is authorized to see
	 *
	 * @param boolean $title with title
	 *
	 * @return array|null $groups     ID of allowed campaigns
	 *
	 * @throws Exception
	 * @since    1.2.0
	 */
	public function getAccessibleUsergroups(bool $title = true): ?array
	{
		$app       = Factory::getApplication();
		$db		= $this->_db;
		$query		= $db->getQuery(true);
		$groups     = null;
		$params      = $this->getAppropriateParams();

		$check		= $params->get('access-check', '1');

		// fetch only from usergroups, which are selected, if so
		$all_groups	= $params->get('groups_selected_all', 'no');
		$sel_groups	= $params->get('groups_available', '');
		$c_groups	= $sel_groups;

		if ($all_groups)
		{
			$query->select('id');
			$query->from('#__usergroups');

			try
			{
				$this->_db->setQuery($query);

				$groups	= $db->loadColumn();
			}
			catch (RuntimeException $e)
			{
				$app->enqueueMessage($e->getMessage(), 'error');
			}

			//convert usergroups to match bwPostman's needs
			$c_groups	= array();
			if (is_array($groups) && count($groups) > 0)
			{
				foreach ($groups as $value) {
					$c_groups[]	= '-' . $value;
				}
			}
			else
			{
				$c_groups[]	= 0;
			}
		}

		if (!is_array($c_groups))
		{
			$c_groups[]	= 0;
		}

		// Check permission, if desired
		if ($all_groups || $check != 'no')
		{
			$user		= $app->getIdentity();
			$acc_groups	= $user->getAuthorisedGroups();

			//convert usergroups to match bwPostman's needs
			$a_groups	= array();
			if (is_array($acc_groups) && count($acc_groups) > 0)
			{
				foreach ($acc_groups as $value)
				{
					$a_groups[]	= '-' . $value;
				}
			}
			else
			{
				$a_groups[]	= 0;
			}

			$sel_groups	= array_intersect($a_groups, $c_groups);
		}

		if (count($sel_groups) == 0)
		{
			$sel_groups[]	= 0;
		}

		if ($title === true)
		{
			$query	= $db->getQuery(true);
			$query->select('id');
			$query->select('title');
			$query->from($db->quoteName('#__usergroups'));
			$query->where($db->quoteName('id') . ' IN (' . implode(',', $sel_groups) . ')');

			try
			{
				$this->_db->setQuery($query);

				$groups	= $db->loadAssocList();
			}
			catch (RuntimeException $e)
			{
				$app->enqueueMessage($e->getMessage(), 'error');
			}
		}
		else
		{
			$groups	= $sel_groups;
		}

		return $groups;
	}

	/**
	 * Method to get all user groups which the user is authorized to see
	 *
	 * @param int $id module ID
	 *
	 * @return 	?object	$module module object
	 *
	 * @throws Exception
	 *
	 * @since	1.2.0
	 */
	private function getModuleById(int $id = 0): ?object
	{
		$module = null;
		$db	= $this->getDbo();
		$query	= $db->getQuery(true);

		$query->select('m.id, m.title, m.module, m.position, m.content, m.showtitle, m.params');
		$query->from('#__modules AS m');
		$query->where('m.id = ' . $id);

		try
		{
			$this->_db->setQuery($query);

			$module	= $db->loadObject();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $module;
	}

	/**
	 * Method to get appropriate params. If we come directly from menu item, take the params from state.
	 * If we come from a module and there is set to take the params from a menu item, take them. If no menu item is
	 * selected, take the module params.
	 *
	 * @return Registry
	 *
	 * @throws Exception
	 *
	 * @since 3.0.0
	 */
	protected function getAppropriateParams(): Registry
	{
		$params = $this->state->params;
		$mod_id = $this->getState('module.id');

		if (!is_null($mod_id))
		{
			$module = $this->getModuleById($mod_id);
			$params = new Registry($module->params);

			$menuItem = $params->get('menu_item', '');

			if ($menuItem !== '')
			{
				$params = $this->getParamsFromSelectedMenuEntry($menuItem);
			}
		}

		return $params;
	}

	/**
	 * Method to get the mailinglists for a specific user by its view level
	 *
	 * @return array
	 *
	 * @throws Exception
	 *
	 * @since 3.0.0
 */
	private function getMailinglistsByViewlevel(): array
	{
		$viewLevelKeys = null;

		// get authorized viewlevels
		$viewLevels = Access::getAuthorisedViewLevels(Factory::getApplication()->getIdentity()->id);

//		@ToDo: Why this? $viewLevelKeys is never used…
		if (is_array($viewLevels) && count($viewLevels) > 0)
		{
			foreach ($viewLevels as $key => $value)
			{
				$viewLevelKeys[] = $key;
			}
		}
		else
		{
			$viewLevelKeys[] = 0;
		}

		return $this->getTable('Mailinglist')->getAllowedMailinglists($viewLevels);
	}
}
