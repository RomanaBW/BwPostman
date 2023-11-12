<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman installer.
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Log\LogEntry;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\Component\Users\Administrator\Model\GroupModel;
use Joomla\Component\Users\Administrator\Model\LevelModel;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanInstallHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwLogger;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwException;

/**
 * Class Com_BwPostmanInstallerScript
 *
 * @since       0.9.6.3
 */
class com_bwpostmanInstallerScript
{
	/**
	 * @var InstallerAdapter $parentInstaller
	 *
	 * @since       0.9.6.3
	 */
	public $parentInstaller;

	/**
	 * @var string $minimum_joomla_release
	 *
	 * @since       2.0.0
	 */
	private $minimum_joomla_release = "4.0.0";

	/**
	 * @var string $minimum_php_version
	 *
	 * @since       4.2.5
	 */
	private $minimum_php_version = "7.2.5";

	/**
	 * @var string release
	 *
	 * @since       2.0.0
	 */
	private $release = null;

	/**
	 * @var string  $reference_table        reference table to check if it is converted already
	 *
	 * @since       2.0.0
	 */
	private $reference_table = 'bwpostman_campaigns';

	/**
	 * @var string  $conversion_file        file name of sql conversion file
	 *
	 * @since       2.0.0
	 */
	private $conversion_file = '/components/com_bwpostman/sql/utf8mb4conversion/utf8mb4-conversion-01.sql';

	/**
	 * @var array $all_bwpm_groups          array which holds user groups of BwPostman
	 *
	 * @since       2.0.0
	 */
	private $all_bwpm_groups    = array(
									'bwpm_usergroups'           => array(
										'BwPostmanPublisher',
										'BwPostmanEditor',
									),
									'mailinglist_usergroups'    => array(
										'BwPostmanMailinglistAdmin',
										'BwPostmanMailinglistPublisher',
										'BwPostmanMailinglistEditor',
									),
									'subscriber_usergroups'     => array(
										'BwPostmanSubscriberAdmin',
										'BwPostmanSubscriberPublisher',
										'BwPostmanSubscriberEditor',
									),
									'newsletter_usergroups'     => array(
										'BwPostmanNewsletterAdmin',
										'BwPostmanNewsletterPublisher',
										'BwPostmanNewsletterEditor',
									),
									'campaign_usergroups'       => array(
										'BwPostmanCampaignAdmin',
										'BwPostmanCampaignPublisher',
										'BwPostmanCampaignEditor',
									),
									'template_usergroups'       => array(
										'BwPostmanTemplateAdmin',
										'BwPostmanTemplatePublisher',
										'BwPostmanTemplateEditor',
									),
									);

	/**
	 * @var string ID of BwPostmanAdmin usergroup
	 *
	 * @since       2.0.0
	 */
	private $adminUsergroup = null;

	/**
	 * Property to hold logger
	 *
	 * @var    object
	 *
	 * @since  3.0.0
	 */
	private $logger;

	/**
	 * Property to hold log category
	 *
	 * @var    string
	 *
	 * @since  3.0.0
	 */
	private $log_cat = 'Installer';

	/**
	 * Executes additional installation processes
	 *
	 * @since       0.9.6.3
	 */
	private function bwpostman_install()
	{
		/*
		$db = Factory::getDbo();
		$query = 'INSERT INTO '. $db->quoteName('#__postinstall_messages') .
		' ( `extension_id`,
				  `title_key`,
				  `description_key`,
				  `action_key`,
				  `language_extension`,
				  `language_client_id`,
				  `type`,
				  `action_file`,
				  `action`,
				  `condition_file`,
				  `condition_method`,
				  `version_introduced`,
				  `enabled`) VALUES '
				.'( 700,
			   "COM_BWPOSTMAN_POSTINSTALL_TITLE",
			   "COM_BWPOSTMAN_POSTINSTALL_BODY",
			   "COM_BWPOSTMAN_POSTINSTALL_ACTION",
			   "com_bwpostman",
				1,
			   "link",
			   "admin://components/com_bwpostman/postinstall/actions.php",
			   "index.php?option=com_bwpostman&view=templates",
			   "admin://components/com_bwpostman/postinstall/actions.php",
			   "com_bwpostman_postinstall_condition",
			   "1.2.3",
			   1)';

		$db->setQuery($query);
		$db->execute();
		*/
	}

	/**
	 * Called before any type of action
	 *
	 * @param string             $type   Which action is happening (install|uninstall|discover_install|update)
	 * @param   InstallerAdapter $parent The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 *
	 * @throws Exception
	 *
	 * @since       0.9.6.3
	 */

	public function preflight(string $type, InstallerAdapter $parent): bool
	{
		$app     = Factory::getApplication();
		$session = $app->getSession();

		if (function_exists('set_time_limit'))
		{
			set_time_limit(0);
		}

		$this->parentInstaller = $parent->getParent();
		$manifest              = $parent->getManifest();

		// Get component manifest file version
		$this->release	= (string)$manifest->version;
		$session->set('release', $this->release, 'bwpostman');

		// Manifest file minimum Joomla version
//		$this->minimum_joomla_release = $manifest->attributes()->version;

		// abort if the current Joomla release is older
		if(version_compare(JVERSION, $this->minimum_joomla_release, 'lt'))
		{
			$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_INSTALL_ERROR_JVERSION', $this->minimum_joomla_release), 'error');
			return false;
		}

		if(version_compare(phpversion(), $this->minimum_php_version, 'lt'))
		{
			$app->enqueueMessage(Text::_('COM_BWPOSTMAN_USES_PHP7'), 'error');
			return false;
		}

		// abort if the component being installed is not newer than the currently installed version
		if ($type == 'update')
		{
			$oldRelease = $this->getManifestVar('version');
			$app->setUserState('com_bwpostman.update.oldRelease', $oldRelease);

			if (version_compare($this->release, $oldRelease, 'lt'))
			{
				$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_INSTALL_ERROR_INCORRECT_VERSION_SEQUENCE', $oldRelease, $this->release), 'error');
				return false;
			}
		}

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('params'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element') . " = " . $db->quote('com_bwpostman'));

		try
		{
			$db->setQuery($query);

			$params_default = $db->loadResult();
			$app->setUserState('com_bwpostman.install.params', $params_default);
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		if ($type !== 'uninstall')
		{
			// Check if utf8mb4 is supported; if so, copy utf8mb4 file as sql installation file
			$tmp_path   = $this->parentInstaller->getPath('source') . '/admin';

			require_once($tmp_path . '/Helper/BwPostmanInstallHelper.php');

			$name = $db->getName();

			if (BwPostmanInstallHelper::serverClaimsUtf8mb4Support($name))
			{
				copy($tmp_path . '/sql/utf8mb4conversion/utf8mb4-install.sql', $tmp_path . '/sql/install.sql');
			}
		}

		return true;
	}


	/**
	 * Called after any type of action
	 *
	 * @param string $type Which action is happening (install|uninstall|discover_install)
	 * @param   InstallerAdapter $parent The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 *
	 * @throws Exception
	 *
	 * @since       0.9.6.3
	 */

	public function postflight(string $type, InstallerAdapter $parent): bool
	{
		$this->parentInstaller = $parent->getParent();
		$m_params = ComponentHelper::getParams('com_media');
		$this->copyTemplateImagesToMedia($m_params);

		// Make new folder and copy template thumbnails to folder "images" if image_path is not "images"
		if ($m_params->get('image_path', 'images') != 'images')
		{
			$this->copyTemplateImagesToImages();
		}

		if ($type == 'install' || $type == 'update')
		{
			$tmp_path   = $this->parentInstaller->getPath('source') . '/admin';

			if (!class_exists('\BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwLogger'))
			{
				require_once($tmp_path . '/libraries/BwLogger.php');
			}

			$log_options  = array();

			try
			{
				$this->logger = BwLogger::getInstance($log_options);
			}
			catch (Exception $e)
			{
				$this->logger = new BwLogger($log_options);
			}
		}

		if ($type == 'install')
		{
			// Set BwPostman default settings in the extensions table at install
			$this->setDefaultParams();

			// Create sample user groups, set viewlevel
			$this->installSampleUsergroups();

			// check if sample templates exists
			$this->checkSampleTemplates();

			// update/complete component rules
			$this->updateRules();
		}

		if ($type == 'update')
		{
			// check if sample templates exists
			$this->checkSampleTemplates();

			$this->logger->addEntry(new LogEntry("Postflight checkSampleTemplates passed", BwLogger::BW_DEBUG, $this->log_cat));

			// update/complete component rules
			$this->updateRules();

			$this->logger->addEntry(new LogEntry("Postflight updateRules passed", BwLogger::BW_DEBUG, $this->log_cat));

			$app        = Factory::getApplication();
			$oldRelease = $app->getUserState('com_bwpostman.update.oldRelease', '');

			if (version_compare($oldRelease, '1.0.1', 'lt'))
			{
				BwPostmanInstallHelper::adjustMLAccess();
			}

			if (version_compare($oldRelease, '1.2.0', 'lt'))
			{
				$this->correctCamId();
			}

			if (version_compare($oldRelease, '1.2.0', 'lt'))
			{
				$this->fillCamCrossTable();
			}

			if (version_compare($oldRelease, '2.3.0', 'lt'))
			{
				$this->removeOwnManagerFiles();
			}

			$this->installSampleUsergroups();

			$this->logger->addEntry(new LogEntry("Postflight installSampleUserGroups passed", BwLogger::BW_DEBUG, $this->log_cat));

			$this->repairRootAsset();

			$this->logger->addEntry(new LogEntry("Postflight repairRootAsset passed", BwLogger::BW_DEBUG, $this->log_cat));

			// convert tables to UTF8MB4
			BwPostmanInstallHelper::convertToUtf8mb4($this->reference_table, JPATH_ADMINISTRATOR . $this->conversion_file);

			// remove double entries in table extensions
			$this->removeDoubleExtensionsEntries();

			$this->logger->addEntry(new LogEntry("Postflight removeDoubleExtensionsEntries passed", BwLogger::BW_DEBUG, $this->log_cat));

			// remove obsolete files
			$this->removeObsoleteFilesAndFolders();

			$this->logger->addEntry(new LogEntry("Postflight removeObsoleteFilesAndFolders passed", BwLogger::BW_DEBUG, $this->log_cat));

			// Align subscribers with joomla users
			$this->alignSubscribersWithUsers();

			$this->logger->addEntry(new LogEntry("Postflight removeDoubleExtensionsEntries passed", BwLogger::BW_DEBUG, $this->log_cat));

		}

		return true;
	}

	/**
	 * Called on installation
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since       0.9.6.3
	 */

	public function install()
	{
		$session = Factory::getApplication()->getSession();
		$session->set('update', false, 'bwpostman');
		$this->bwpostman_install();
		$this->showFinished(false);
	}

	/**
	 * Called on update
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since       0.9.6.3
	 */

	public function update()
	{
		$session = Factory::getApplication()->getSession();
		$session->set('update', true, 'bwpostman');
		$this->bwpostman_install();

		$this->showFinished(true);
	}


	/**
	 * Called on un-installation
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since       0.9.6.3
	 */

	public function uninstall()
	{
		$this->deleteBwPmAdminFromRootAsset();
		$this->deleteBwPmAdminFromViewlevels();
		$this->deleteSampleUsergroups();

		Factory::getApplication()->enqueueMessage(Text::_('COM_BWPOSTMAN_UNINSTALL_THANKYOU'), 'message');
		//  notice that folder image/com_bwpostman is not removed
		$m_params   = ComponentHelper::getParams('com_media');
		$image_path = $m_params->get('image_path', 'images');

		Factory::getApplication()->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_UNINSTALL_FOLDER_BWPOSTMAN', $image_path), 'warning');

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__postinstall_messages'));
		$query->where($db->quoteName('language_extension') . ' = ' . $db->quote('com_bwpostman'));

		try
		{
			$db->setQuery($query);
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
	}

	/**
	 * get a variable from the manifest file (actually, from the manifest cache).
	 *
	 * @param string $name
	 *
	 * @return  mixed  $manifest
	 *
	 * @throws Exception
	 *
	 * @since       0.9.6.3
	 */
	private function getManifestVar(string $name)
	{
		$manifest = array();
		$db       = Factory::getDbo();
		$query    = $db->getQuery(true);

		$query->select($db->quoteName('manifest_cache'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element') . " = " . $db->quote('com_bwpostman'));

		try
		{
			$db->setQuery($query);

			$manifest = json_decode($db->loadResult(), true);
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $manifest[$name];
	}


	/**
	 * Correct campaign_id in newsletters because of an error previous version
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	private function correctCamId()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->update($db->quoteName('#__bwpostman_newsletters'));
		$query->set($db->quoteName('campaign_id') . " = " .   -1);
		$query->where($db->quoteName('campaign_id') . " = " . 0);

		try
		{
			$db->setQuery($query);
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
	}

	/**
	 * Fill cross table campaigns mailinglists with values from all newsletters of the specific campaign
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	private function fillCamCrossTable()
	{
		$all_cams = array();
		$db       = Factory::getDbo();
		$query    = $db->getQuery(true);

		// First get all campaigns
		$query->select($db->quoteName('id') . ' AS ' . $db->quoteName('campaign_id'));
		$query->from($db->quoteName('#__bwpostman_campaigns'));

		try
		{
			$db->setQuery($query);

			$all_cams = $db->loadAssocList();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		if (count($all_cams) > 0)
		{
			foreach ($all_cams as $cam)
			{
				$cross_values = array();
				$query        = $db->getQuery(true);

				$query->select('DISTINCT(' . $db->quoteName('cross1') . '.' . $db->quoteName('mailinglist_id') . ')');
				$query->from($db->quoteName('#__bwpostman_newsletters_mailinglists') . ' AS ' . $db->quoteName('cross1'));
				$query->leftJoin('#__bwpostman_newsletters AS n ON cross1.newsletter_id = n.id');
				$query->where($db->quoteName('n') . '.' . $db->quoteName('campaign_id') . ' = ' . $cam['campaign_id']);

				try
				{
					$db->setQuery($query);

					$cross_values = $db->loadAssocList();
				}
				catch (RuntimeException $e)
				{
					Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				}

				if (count($cross_values) > 0)
				{
					foreach ($cross_values as $item)
					{
						$query = $db->getQuery(true);

						$query->insert($db->quoteName('#__bwpostman_campaigns_mailinglists'));
						$query->columns(
							array(
								$db->quoteName('campaign_id'),
								$db->quoteName('mailinglist_id')
							)
						);
							$query->values(
								$db->quote((int)$cam['campaign_id']) . ',' .
								$db->quote((int)$item['mailinglist_id'])
							);

						try
						{
							$db->setQuery($query);
							$db->execute();
						}
						catch (RuntimeException $e)
						{
							Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
						}
					}
				}
			}
		}
	}

	/**
	 * Method to copy the provided template thumbnails to media folder
	 *
	 * @param object $m_params params of com_media
	 *
	 * @return void
	 *
	 * @since   2.0.0
	 */
	private function copyTemplateImagesToMedia(object $m_params)
	{
		$image_path = JPATH_ROOT . '/' . $m_params->get('image_path', 'images') . '/com_bwpostman/';
		$media_path = JPATH_ROOT . '/media/com_bwpostman/images/';

		// make new folder and copy template thumbnails
		if (!Folder::exists($image_path))
		{
			Folder::create($image_path);
		}

		if (!File::exists($image_path . '/index.html'))
		{
			File::copy($media_path . 'index.html', $image_path . '/index.html');
		}

		$tpl_images = array(
			"deep_blue.png",
			"soft_blue.png",
			"creme.png",
			"sample_html.png",
			"text_template_1.png",
			"text_template_2.png",
			"text_template_3.png",
			"sample_text.png",
			"joomla_black.gif",
			"standard_basic.png",
			"sample_html_2018.png",
		);

		foreach ($tpl_images as $tpl_image)
		{
			if (!File::exists($image_path . "/" . $tpl_image))
			{
				File::copy($media_path . $tpl_image, $image_path . "/" . $tpl_image);
			}
		}
	}

	/**
	 * Method to copy the provided template thumbnails to /images/bwpostman
	 *
	 * @return void
	 *
	 * @since   2.0.0
	 */
	private function copyTemplateImagesToImages()
	{
		$dest = JPATH_ROOT . '/images/com_bwpostman';

		if (!Folder::exists($dest))
		{
			Folder::create(JPATH_ROOT . '/images/com_bwpostman');
		}

		if (!File::exists(JPATH_ROOT . '/images/com_bwpostman/index.html'))
		{
			File::copy(JPATH_ROOT . '/images/index.html', JPATH_ROOT . '/images/com_bwpostman/index.html');
		}

		$tpl_images = array(
			"deep_blue.png",
			"soft_blue.png",
			"creme.png",
			"sample_html.png",
			"text_template_1.png",
			"text_template_2.png",
			"text_template_3.png",
			"sample_text.png",
			"joomla_black.gif",
			"standard_basic.png",
			"sample_html_2018.png",
		);

		foreach ($tpl_images as $tpl_image)
		{
			if (!File::exists(JPATH_ROOT . "/images/com_bwpostman/$tpl_image"))
			{
				File::copy(JPATH_ROOT . "/media/com_bwpostman/images/$tpl_image", JPATH_ROOT . "/images/com_bwpostman/$tpl_image");
			}
		}
	}

	/**
	 * Method to check, if sample templates are installed. If not, install sample templates
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function checkSampleTemplates()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__bwpostman_templates'));
		try
		{
			$db->setQuery($query);

			$templateFields = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		$query  = $db->getQuery(true);

		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__bwpostman_templates_tpl'));

		try
		{
			$db->setQuery($query);

			$templatetplFields = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		// if not install sample data
		$templatessql = 'bwp_templates.sql';

		if (!isset($templateFields))
		{
			$this->installdata($templatessql);
		}

		$templatestplsql = 'bwp_templatestpl.sql';
		if (!isset($templatetplFields))
		{
			$this->installdata($templatestplsql);
		}
	}

	/**
	 * Method to create sample user groups and access levels
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function createSampleUsergroups()
	{
		try
		{
			// get the model for user groups
			$groupModel = new GroupModel();

			$public_id = 1;

			// Ensure user group BwPostmanAdmin exists
			$groupExists = $this->getGroupId('BwPostmanAdmin');

			if (!$groupExists)
			{
				$ret = $groupModel->save(array('id' => 0, 'parent_id' => $public_id, 'title' => 'BwPostmanAdmin'));

				if (!$ret)
				{
					echo Text::sprintf('COM_BWPOSTMAN_INSTALLATION_ERROR_CREATING_USERGROUPS: %s', false);
					throw new Exception(Text::sprintf('COM_BWPOSTMAN_INSTALLATION_ERROR_CREATING_USERGROUPS: %s',
						false));
				}
			}

			$admin_groupId = $this->getGroupId('BwPostmanAdmin');
			$this->adminUsergroup = $admin_groupId;

			// Ensure user group BwPostmanManager exists
			$manager_groupId = $this->getGroupId('BwPostmanManager');

			if (!$manager_groupId)
			{
				$manager_groupId = 0;
			}

			$ret = $groupModel->save(array('id' => $manager_groupId, 'parent_id' => $admin_groupId, 'title' => 'BwPostmanManager'));

			if (!$ret)
			{
				echo Text::sprintf('COM_BWPOSTMAN_INSTALLATION_ERROR_CREATING_USERGROUPS: %s', false);
				throw new Exception(Text::sprintf('COM_BWPOSTMAN_INSTALLATION_ERROR_CREATING_USERGROUPS: %s',
					false));
			}

			$manager_groupId = $this->getGroupId('BwPostmanManager');

			// Create BwPostman user groups section-wise
			foreach ($this->all_bwpm_groups as $groups)
			{
				$parent_id  = $manager_groupId;

				foreach ($groups as $item)
				{
					$groupId = $this->getGroupId($item);

					if (!$groupId)
					{
						$groupId = 0;
					}

					$ret = $groupModel->save(array('id' => $groupId, 'parent_id' => $parent_id, 'title' => $item));

					if (!$ret)
					{
						throw new Exception(Text::_('COM_BWPOSTMAN_INSTALLATION_ERROR_CREATING_USERGROUPS'));
					}

					$parent_id = $this->getGroupId($item);
				}
			}
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
	}

	/**
	 * Method to add BwPostmanAdmin to view level special
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function addBwPmAdminToViewlevel()
	{
		try
		{
			if (!(int) $this->adminUsergroup)
			{
				return;
			}

			// get the model for viewlevels
			$viewlevelModel = new LevelModel();

			// Get viewlevel special
			$specialLevel = $viewlevelModel->getItem(3);

			// Insert BwPostmanAdmin to the rules of viewlevel special
			array_unshift($specialLevel->rules, (int) $this->adminUsergroup);
			$specialLevelArray = ArrayHelper::fromObject($specialLevel, false);

			// Save viewlevel special
			$viewlevelModel->save($specialLevelArray);
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
	}

	/**
	 * Method to add BwPostmanAdmin to root asset
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function addBwPmAdminToRootAsset()
	{
		try
		{
			// Get group ID of BwPostmanAdmin and section admins
			$adminGroup             = $this->getGroupId('BwPostmanAdmin');
			$campaignAdminGroup     = $this->getGroupId('BwPostmanCampaignAdmin');
			$mailinglistAdminGroup  = $this->getGroupId('BwPostmanMailinglistAdmin');
			$newsletterAdminGroup   = $this->getGroupId('BwPostmanNewsletterAdmin');
			$subscriberAdminGroup   = $this->getGroupId('BwPostmanSubscriberAdmin');
			$templateAdminGroup     = $this->getGroupId('BwPostmanTemplateAdmin');

			if (!$adminGroup || !$campaignAdminGroup || !$mailinglistAdminGroup || !$newsletterAdminGroup || !$subscriberAdminGroup || !$templateAdminGroup)
			{
				return;
			}

			// Get root asset
			$rootRules = $this->getRootAsset();

			// Insert BwPostmanAdmin to root asset
			$tmpRules = json_decode($rootRules, true);

			// @ToDo: J4 only grants access to component options with core.manage, but with this permission is a lot possible.
			// Better would be to only grant core.options, but this needs core change as suggested at issue #26606.
			$tmpRules['core.login.site'][$adminGroup]  = 1;
			$tmpRules['core.login.admin'][$adminGroup] = 1;
			$tmpRules['core.manage'][$adminGroup]      = 1;
			$tmpRules['core.options'][$adminGroup]     = 1;
			$tmpRules['core.create'][$adminGroup]      = 1;
			$tmpRules['core.delete'][$adminGroup]      = 1;
			$tmpRules['core.edit'][$adminGroup]        = 1;
			$tmpRules['core.edit.own'][$adminGroup]    = 1;
			$tmpRules['core.edit.state'][$adminGroup]  = 1;

//			$tmpRules['core.manage'][$campaignAdminGroup]     = 0;
//			$tmpRules['core.manage'][$mailinglistAdminGroup]  = 0;
//			$tmpRules['core.manage'][$newsletterAdminGroup]   = 0;
//			$tmpRules['core.manage'][$subscriberAdminGroup]   = 0;
//			$tmpRules['core.manage'][$templateAdminGroup]     = 0;

			$newRootRules = json_encode($tmpRules);

			// Save root asset
			$this->saveRootAsset($newRootRules);
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
	}

	/**
	 * Method to delete sample user groups and access levels
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function deleteSampleUsergroups()
	{
		try
		{
			$db               = Factory::getDbo();
			$user_id          = Factory::getApplication()->getIdentity()->get('id');
			$bwpostman_groups = array(0);
			$query            = $db->getQuery(true);

			// get group ids of BwPostman user groups
			$query->select($db->quoteName('id'));
			$query->from($db->quoteName('#__usergroups'));
			$query->where($db->quoteName('title') . ' LIKE ' . $db->quote('%BwPostman%'));

			try
			{
				$db->setQuery($query);

				$bwpostman_groups = $db->loadColumn();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			// get group id of BwPostman main user group
			$bwpostman_main_group = '';
			$query                = $db->getQuery(true);

			$query->select($db->quoteName('id'));
			$query->from($db->quoteName('#__usergroups'));
			$query->where($db->quoteName('title') . ' = ' . $db->quote('BwPostmanAdmin'));

			try
			{
				$db->setQuery($query);

				$bwpostman_main_group = $db->loadResult();
				$bwpostman_main_group = array((int) $bwpostman_main_group);
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			// get group ids of BwPostman user groups, where actual user is member
			$member_ids = '';

			if (is_array($bwpostman_groups) && !empty($bwpostman_groups))
			{
				$bwpostman_groups = ArrayHelper::toInteger($bwpostman_groups);
				$query = $db->getQuery(true);
				$query->select($db->quoteName('group_id'));
				$query->from($db->quoteName('#__user_usergroup_map'));
				$query->where($db->quoteName('user_id') . ' = ' . (int) $user_id);
				$query->where($db->quoteName('group_id') . ' IN (' . implode(',', $bwpostman_groups) . ')');

				try
				{
					$db->setQuery($query);

					$member_ids = $db->loadColumn();
				}
				catch (RuntimeException $e)
				{
					Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				}
			}

			// delete current user from BwPostman user groups
			if (is_array($member_ids) || is_object($member_ids))
			{
				foreach ($member_ids as $item)
				{
					UserHelper::removeUserFromGroup($user_id, (int)$item);
				}
			}

			// get the model for user groups
			$groupModel = new GroupModel();

			Access::clearStatics();

			// delete main user group of BwPostman (all other (sub) user groups of BwPostman will be deleted automatically by Joomla)
			$res = $groupModel->delete($bwpostman_main_group);
			if (!$res)
			{
				throw new BwException(Text::_('COM_BWPOSTMAN_DEINSTALLATION_ERROR_REMOVE_USERGROUPS'));
			}
		}
		catch (RuntimeException | BwException $e)
		{
			echo $e->getMessage();
		}
	}

	/**
	 * Method to add BwPostmanAdmin to view level special
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function deleteBwPmAdminFromViewlevels()
	{
		// get group ID of BwPostmanAdmin
		$adminGroup = $this->getGroupId('BwPostmanAdmin');

		if ($adminGroup)
		{
			try
			{
				// get the model for viewlevels
				$viewlevelModel = new LevelModel();

				// Get viewlevel special
				$specialLevel = $viewlevelModel->getItem(3);

				// Remove BwPostmanAdmin from to the rules of viewlevel special
				if (in_array($adminGroup, $specialLevel->rules))
				{
					array_shift($specialLevel->rules);
					$specialLevelArray = ArrayHelper::fromObject($specialLevel);

					// Save viewlevel special
					$viewlevelModel->save($specialLevelArray);
				}
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}
	}

	/**
	 * Method to add BwPostmanAdmin to view level special
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function deleteBwPmAdminFromRootAsset()
	{
		try
		{
			// Get group ID of BwPostmanAdmin
			$adminGroup = $this->getGroupId('BwPostmanAdmin');

			// Get root asset
			$rootRules = $this->getRootAsset();

			// Insert BwPostmanAdmin to root asset
			$tmpRules = json_decode($rootRules, true);

			unset($tmpRules['core.login.site'][$adminGroup]);
			unset($tmpRules['core.login.admin'][$adminGroup]);
			unset($tmpRules['core.create'][$adminGroup]);
			unset($tmpRules['core.delete'][$adminGroup]);
			unset($tmpRules['core.edit'][$adminGroup]);
			unset($tmpRules['core.edit.own'][$adminGroup]);
			unset($tmpRules['core.edit.state'][$adminGroup]);

			$newRootRules = json_encode($tmpRules);

			// Save root asset
			$this->saveRootAsset($newRootRules);
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
	}

	/**
	 * Remove files of own media manager. With version 2.3.0 not more needed
	 *
	 * @since 2.3.0
	 */

	private function removeOwnManagerFiles()
	{
		$removeFolders = array(
			JPATH_ROOT . '/media/com_bwpostman/js',
			JPATH_ROOT . '/administrator/components/com_bwpostman/views/media',
			JPATH_ROOT . '/administrator/components/com_bwpostman/views/medialist');
		// Remove views and js
		foreach ($removeFolders as $folder)
		{
			if (Folder::exists($folder))
			{
				Folder::delete($folder);
			}
		}

		if (File::exists(JPATH_ROOT . '/administrator/components/com_bwpostman/models/fields/allmedia.php'))
		{
			File::delete(JPATH_ROOT . "/administrator/components/com_bwpostman/models/fields/allmedia.php");
		}
	}

	/**
	 * Gets the group Id of the selected group name
	 *
	 * @param string $name The name of the group
	 *
	 * @return  int  the ID of the group or false, if group not exists
	 *
	 * @throws Exception
	 *
	 * @since
	 */

	private function getGroupId(string $name): int
	{
		$result = false;
		$db     = Factory::getDbo();
		$query  = $db->getQuery(true);

		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__usergroups'));
		$query->where("`title` LIKE '" . $db->escape($name) . "'");

		try
		{
			$db->setQuery($query);

			$result = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage('Error GroupId: ' . $e->getMessage() . '<br />', 'error');
		}

		return (int)$result;
	}


	/**
	 * Method to remove multiple entries in table extensions. Needed because joomla update may show updates for these unnecessary entries
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function removeDoubleExtensionsEntries()
	{
		$db          = Factory::getDbo();
		$extensionId = $this->getExtensionId(0);

		if ($extensionId)
		{
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__extensions'));
			$query->where($db->quoteName('extension_id') . ' =  ' . $db->quote($extensionId));

			try
			{
				$db->setQuery($query);
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}
	}

	/**
	 * Method to remove obsolete files and folders
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	private function removeObsoleteFilesAndFolders()
	{
		$feFilesArray = array(
			'views/edit/metadata.xml',
			'views/newsletter/metadata.xml',
			'views/newsletters/metadata.xml',
			'views/register/metadata.xml',
			'helpers/subscriberhelper.php',
			'layouts/subscriber/bootstrap2.php',
			'layouts/subscriber/bootstrap4.php',
			'layouts/subscriber/emailformat_bs2.php',
			'layouts/subscriber/emailformat_bs4.php',
			'layouts/subscriber/gender_bs4.php',
		);

		foreach ($feFilesArray as $file)
		{
			if (File::exists(JPATH_ROOT . '/components/com_bwpostman/' . $file))
			{
				File::delete(JPATH_ROOT . '/components/com_bwpostman/' . $file);
			}
		}

		$feFoldersArray = array(
//			'',
		);

		foreach ($feFoldersArray as $folder)
		{
			if (Folder::exists(JPATH_ROOT . '/components/com_bwpostman/' . $folder))
			{
				Folder::delete(JPATH_ROOT . '/components/com_bwpostman/' . $folder);
			}
		}

		$beFilesArray = array(
			'controllers/file.json.php',
			'controllers/file.json.php_x',
			'controllers/file.php',
			'controllers/file.php_x',
			'assets/js/bwpm_j3_nl_send.js',
			'assets/js/bwpm_tabshelper.js',
			'assets/js/bwpm_checktables.js',
			'assets/js/bwpm_do_restore.js',
			'assets/js/bwpm_maintenance_doAjax.js',
			'assets/js/bwpm_update_checksave.js',
		);

		foreach ($beFilesArray as $file)
		{
			if (File::exists(JPATH_ROOT . '/administrator/components/com_bwpostman/' . $file))
			{
				File::delete(JPATH_ROOT . '/administrator/components/com_bwpostman/' . $file);
			}
		}

		$beFoldersArray     = array(
		);

		foreach ($beFoldersArray as $folder)
		{
			if (Folder::exists(JPATH_ROOT . '/administrator/components/com_bwpostman/' . $folder))
			{
				Folder::delete(JPATH_ROOT . '/administrator/components/com_bwpostman/' . $folder);
			}
		}

		$mediaFilesArray  = array(
			'css/iconfonts.css',
			'css/admin-bwpostman.css',
			'css/admin-bwpostman_backend.css',
			'css/install_j4.css',
		);

		foreach ($mediaFilesArray as $file)
		{
			if (File::exists(JPATH_ROOT . '/media/com_bwpostman/' . $file))
			{
				File::delete(JPATH_ROOT . '/media/com_bwpostman/' . $file);
			}
		}

//		$imagesFilesArray = array();
//
//		foreach ($imagesFilesArray as $file)
//		{
//			if (File::exists(JPATH_ROOT . '/images/com_bwpostman/' . $file))
//			{
//				File::delete(JPATH_ROOT . '/images/com_bwpostman/' . $file);
//			}
//		}

//		$regModFilesArray = array();
//
//		foreach ($regModFilesArray as $file)
//		{
//			if (File::exists(JPATH_ROOT . '/images/com_bwpostman/' . $file))
//			{
//				File::delete(JPATH_ROOT . '/images/com_bwpostman/' . $file);
//			}
//		}

//		Remove files from J3 and sql update files
		$obsoleteJ3 = array(
			'/administrator/components/com_bwpostman/sql/updates/mysql',
			'/administrator/components/com_bwpostman/controllers',
			'/administrator/components/com_bwpostman/helpers',
			'/administrator/components/com_bwpostman/models',
			'/administrator/components/com_bwpostman/tables',
			'/administrator/components/com_bwpostman/views',
			'/administrator/components/com_bwpostman/libraries/access',
			'/administrator/components/com_bwpostman/libraries/exceptions',
			'/administrator/components/com_bwpostman/libraries/logging',
			'/administrator/components/com_bwpostman/libraries/mailverification',
			'/administrator/components/com_bwpostman/libraries/webapp',
			'/administrator/components/com_bwpostman/classes/admin.class.php',
			'/administrator/components/com_bwpostman/elements/singlenews.php',
			'/administrator/components/com_bwpostman/bwpostman.php',
			'/administrator/components/com_bwpostman/controller.php',
			'/components/com_bwpostman/controllers',
			'/components/com_bwpostman/models',
			'/components/com_bwpostman/views',
			'/components/com_bwpostman/assets/images',
			'/components/com_bwpostman/classes/bwpostman.class.php',
			'/components/com_bwpostman/helpers/query.php',
			'/components/com_bwpostman/bwpostman.php',
			'/components/com_bwpostman/controller.php',
			'/components/com_bwpostman/router.php',
			'/media/com_bwpostman/images/images',
		);

		foreach ($obsoleteJ3 as $path)
		{
			$this->removeFilesAndFoldersRecursive(JPATH_ROOT . $path);
		}
	}

	/**
	 * Method to remove obsolete files and folders
	 *
	 * @param string $path  can be file or folder
	 *
	 * @return bool
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */
	private function removeFilesAndFoldersRecursive(string $path): bool
	{
		if (is_dir($path) === true)
		{
			$files = array_diff(scandir($path), array('.', '..'));

			foreach ($files as $file)
			{
				$this->removeFilesAndFoldersRecursive(realpath($path) . '/' . $file);
			}

			return rmdir($path);
		}
		elseif (is_file($path) === true || is_file(realpath($path)))
		{
			if (!str_contains($path, '/administrator/components/com_bwpostman/sql/updates/mysql/4.')
				&& !str_contains($path, '/administrator/components/com_bwpostman/sql/updates/mysql/index.html'))
			{
				return unlink($path);
			}
		}

		return false;
	}

	/**
	 * Method to align subscribers with users
	 * This is necessary because BwPostman don't get user ID if account is created after subscription until plugin
	 * userAccount is provided
	 *
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since 4.1.0
	 */

	private function alignSubscribersWithUsers(): void
	{
		// Get all subscriber email entries without joomla user ID
		$subscribersWithoutUserId = false;
		$joomlaUserId = null;

		$db     = Factory::getDbo();
		$query  = $db->getQuery(true);

		$query->select($db->quoteName('id'));
		$query->select($db->quoteName('email'));
//		$query->select($db->quoteName('user_id'));
		$query->from($db->quoteName('#__bwpostman_subscribers'));
		$query->where("`user_id` = '0'");

		try
		{
			$db->setQuery($query);

			$subscribersWithoutUserId = $db->loadAssocList();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage('Error alignSubscribers: ' . $e->getMessage() . '<br />', 'error');
		}

		// Search for user IDs at Joomla
		foreach ($subscribersWithoutUserId as $subscriberWithoutUserId)
		{
			$query  = $db->getQuery(true);

			$query->select($db->quoteName('id'));
			$query->from($db->quoteName('#__users'));
			$query->where("`email` = '" . $subscriberWithoutUserId['email'] . "'");

			try
			{
				$db->setQuery($query);

				$joomlaUserId = $db->loadResult();
			}
			catch (RuntimeException $e)
			{
				Factory::getApplication()->enqueueMessage('Error alignSubscribers: ' . $e->getMessage() . '<br />', 'error');
			}

			// Update subscriber data for found user IDs
			if ($joomlaUserId)
			{
				$query2  = $db->getQuery(true);

				$query2->update($db->quoteName('#__bwpostman_subscribers'));
				$query2->set($db->quoteName('user_id') . " = " . $db->quote($joomlaUserId));
				$query2->where("`email` = '" . $subscriberWithoutUserId['email'] . "'");

				try
				{
					$db->setQuery($query2);

					$db->execute();
				}
				catch (RuntimeException $e)
				{
					Factory::getApplication()->enqueueMessage('Error alignSubscribers: ' . $e->getMessage() . '<br />', 'error');
				}
			}

		}

	}


	/**
	 * installs sample usergroups and add BwPostmanAdmin to viewlevel and root asset
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since 2.1.1
	 */
	protected function installSampleUsergroups()
	{
		$this->createSampleUsergroups();
		$this->addBwPmAdminToViewlevel();
		$this->addBwPmAdminToRootAsset();
		/*
		 * Rewrite section assets
		 *
		 */

		// BwPostman Administration Component
		define('BWPM_ADMINISTRATOR_INSTALL', JPATH_ADMINISTRATOR.'/components/com_bwpostman');

		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Extension', BWPM_ADMINISTRATOR_INSTALL . '/src/Extension');
		JLoader::registerNamespace('BoldtWebservice\\Component\\BwPostman\\Administrator\\Service\\Html', BWPM_ADMINISTRATOR_INSTALL . '/src/Service/Html');

		$component = Factory::getApplication()->bootComponent('com_bwpostman');
		$componentFactory = $component->getMVCFactory();
		$maintenanceModel = $componentFactory->createModel('Maintenance', 'Administrator', ['ignore_request' => true]);


		$maintenanceModel->createBaseAssets();
	}

	/**
	 * removes empty user groups from root asset
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since 2.1.1
	 */
	protected function repairRootAsset()
	{
		$rootRules = $this->getRootAsset();

		$repairedRules = str_replace('"":1,', '', $rootRules);

		$this->saveRootAsset($repairedRules);
	}

	/**
	 * shows the HTML after installation/update
	 *
	 * @param boolean $update
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	public function showFinished(bool $update)
	{
		$lang = Factory::getApplication()->getLanguage();
		//Load first english files
		$lang->load('com_bwpostman.sys', JPATH_ADMINISTRATOR, 'en_GB', true);
		$lang->load('com_bwpostman', JPATH_ADMINISTRATOR, 'en_GB', true);

		//load specific language
		$lang->load('com_bwpostman.sys', JPATH_ADMINISTRATOR, null, true);
		$lang->load('com_bwpostman', JPATH_ADMINISTRATOR, null, true);

		$show_update = false;
		$show_right  = false;
		$lang_ver    = substr($lang->getTag(), 0, 2);
		if ($lang_ver != 'de')
		{
			$forum    = "https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html";
			$manual = "https://www.boldt-webservice.de/index.php/en/forum-en/manuals/bwpostman-manual.html";
		}
		else
		{
			$forum = "https://www.boldt-webservice.de/de/forum/bwpostman.html";
			$manual = "https://www.boldt-webservice.de/index.php/de/forum/handb%C3%BCcher/handbuch-zu-bwpostman.html";
		}

		if ($update)
		{
			$string_special = Text::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_SPECIAL_NOTE_DESC');
		}
		else
		{
			$string_special = Text::_('COM_BWPOSTMAN_INSTALLATION_INSTALL_SPECIAL_NOTE_DESC');
		}

		$string_new         = Text::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_NEW_DESC');
		$string_improvement = Text::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_IMPROVEMENT_DESC');
		$string_bugfix      = Text::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_BUGFIX_DESC');

		if (($string_bugfix != '' || $string_improvement != '' || $string_new != '') && $update)
		{
			$show_update = true;
		}

		if ($show_update || $string_special != '')
		{
			$show_right = true;
		}

		$asset_path = 'media/com_bwpostman';
		$image_path = 'media/com_bwpostman/images';
		?>

		<div id="com_bwp_install_header" class="text-center">
			<a href="https://www.boldt-webservice.de" target="_blank">
				<img class="img-fluid border-0" src="<?php echo Uri::root() . $asset_path . '/images/bw_header.png'; ?>" alt="Boldt Webservice" />
			</a>
		</div>
		<div class="top_line"></div>

		<div id="com_bwp_install_outer" class="row">
			<div class="col-lg-12 text-center p-2 mt-2">
				<h1><?php echo Text::_('COM_BWPOSTMAN_INSTALLATION_WELCOME') ?></h1>
			</div>
			<div id="com_bwp_install_left" class="col-lg-6 mb-2">
				<div class="com_bwp_install_welcome">
					<p><?php echo Text::_('COM_BWPOSTMAN_DESCRIPTION') ?></p>
				</div>
				<div class="com_bwp_install_finished text-center">
					<h2>
						<?php
						if ($update)
						{
							echo Text::sprintf('COM_BWPOSTMAN_UPGRADE_SUCCESSFUL', $this->release);
							echo '<br /><br />' . Text::_('COM_BWPOSTMAN_EXTENSION_UPGRADE_REMIND');
						}
						else
						{
							echo Text::sprintf('COM_BWPOSTMAN_INSTALLATION_SUCCESSFUL', $this->release);
						}
						?>
					</h2>
				</div>
				<?php
				if ($show_right)
				{ ?>
					<div class="cpanel text-center mb-3">
						<div class="icon btn" >
							<a href="<?php echo Route::_('index.php?option=com_bwpostman'); ?>">
								<?php echo HtmlHelper::_(
									'image',
									$image_path . '/icon-48-bwpostman.png',
									Text::_('COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN')
								); ?>
								<span><?php echo Text::_('COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN'); ?></span>
							</a>
						</div>
						<div class="icon btn">
							<a href="<?php echo $manual; ?>" target="_blank">
								<?php echo HtmlHelper::_(
									'image',
									$image_path . '/icon-48-manual.png',
									Text::_('COM_BWPOSTMAN_INSTALL_MANUAL')
								); ?>
								<span><?php echo Text::_('COM_BWPOSTMAN_INSTALL_MANUAL'); ?></span>
							</a>
						</div>
						<div class="icon btn">
							<a href="<?php echo $forum; ?>" target="_blank">
								<?php echo HtmlHelper::_(
									'image',
									$image_path . '/icon-48-forum.png',
									Text::_('COM_BWPOSTMAN_INSTALL_FORUM')
								); ?>
								<span><?php echo Text::_('COM_BWPOSTMAN_INSTALL_FORUM'); ?></span>
							</a>
						</div>
					</div>
					<?php
				} ?>
			</div>

			<div id="com_bwp_install_right" class="col-lg-6">
				<?php
				if ($show_right)
				{
					if ($string_special != '')
					{ ?>
						<div class="com_bwp_install_specialnote">
							<h2><?php echo Text::_('COM_BWPOSTMAN_INSTALLATION_SPECIAL_NOTE_LBL') ?></h2>
							<p class="urgent"><?php echo $string_special; ?></p>
						</div>
						<?php
					} ?>

					<?php
					if ($show_update)
					{ ?>
						<div class="com_bwp_install_updateinfo mb-3 p-3">
							<h2 class="mb-3"><?php echo Text::_('COM_BWPOSTMAN_INSTALLATION_UPDATEINFO') ?></h2>
							<?php echo Text::_('COM_BWPOSTMAN_INSTALLATION_CHANGELOG_INFO'); ?>
							<?php if ($string_new != '') { ?>
								<h3 class="mb-2"><?php echo Text::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_NEW_LBL') ?></h3>
								<p><?php echo $string_new; ?></p>
							<?php } ?>
							<?php if ($string_improvement != '') { ?>
								<h3 class="mb-2"><?php echo Text::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_IMPROVEMENT_LBL') ?></h3>
								<p><?php echo $string_improvement; ?></p>
							<?php } ?>
							<?php if ($string_bugfix != '') { ?>
								<h3 class="mb-2"><?php echo Text::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_BUGFIX_LBL') ?></h3>
								<p><?php echo $string_bugfix; ?></p>
							<?php } ?>
						</div>
						<?php
					}
				}
				else
				{ ?>
					<div class="cpanel text-center mb-3">
						<div class="icon btn" >
							<a href="<?php echo Route::_('index.php?option=com_bwpostman&token=' . Session::getFormToken()); ?>">
								<?php echo HtmlHelper::_(
									'image',
									$image_path . '/icon-48-bwpostman.png',
									Text::_('COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN')
								); ?>
								<span><?php echo Text::_('COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN'); ?></span>
							</a>
						</div>
						<div class="icon btn">
							<a href="<?php echo $manual; ?>" target="_blank">
								<?php echo HtmlHelper::_(
									'image',
									$image_path . '/icon-48-bwpostman.png',
									Text::_('COM_BWPOSTMAN_INSTALL_MANUAL')
								); ?>
								<span><?php echo Text::_('COM_BWPOSTMAN_INSTALL_MANUAL'); ?></span>
							</a>
						</div>
						<div class="icon btn">
							<a href="<?php echo $forum; ?>" target="_blank">
								<?php echo HtmlHelper::_(
									'image',
									$image_path . '/icon-48-bwpostman.png',
									Text::_('COM_BWPOSTMAN_INSTALL_FORUM')
								); ?>
								<span><?php echo Text::_('COM_BWPOSTMAN_INSTALL_FORUM'); ?></span>
							</a>
						</div>
					</div>
					<?php
				} ?>
			</div>
			<div class="clr clearfix"></div>

			<div class="com_bwp_install_footer col-12 text-center my-3">
				<p class="small">
					<?php echo Text::_('&copy; 2012-');
					echo date(" Y") ?> by
					<a href="https://www.boldt-webservice.de" target="_blank">Boldt Webservice</a>
				</p>
			</div>
		</div>

		<?php
		// check all tables of BwPostman
		// Let Ajax client redirect
		if ($update)
		{
			$modal = $this->getModal();
			echo $modal;
		}
	}

	/**
	 * Get the HTML-String for popup modal
	 *
	 * @return	string
	 *
	 * @since	2.2.0 (since 3.1.4 here)
	 */
	private function getModal(): string
	{
		$url = Uri::root() . '/administrator/index.php?option=com_bwpostman&view=maintenance&tmpl=component&layout=updateCheckSave';

		$html    = '
		<div id="bwp_Modal" class="bwp_modal">
			<div id="bwp_modal-content">
				<div id="bwp_modal-header"><span class="bwp_close" style="display:none;">&times;</span></div>
				<div id="bwp_wrapper"></div>
			</div>
		</div>
	';
		$css     = "#bwpostman .bwp_modal{display:none;position:fixed;z-index:99999;padding-top:10px;left:0;top:0;width:100%;height:100%;overflow:auto;background-color:#000;background-color:rgba(0,0,0,0.4)}#bwpostman #bwp_modal-content{position:relative;background-color:#fefefe;margin:auto;border:1px solid #888;border-radius:6px;box-shadow:0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);height:100%;display:-ms-flexbox;display:flex;-ms-flex-direction:column;flex-direction:column;pointer-events:auto;outline:0;padding:15px}#bwpostman #bwp_modal-header{height:35px}#bwpostman #bwp_wrapper{position:relative;-ms-flex:1 1 auto;flex:1 1 auto}#bwpostman .bwp_close{color:#aaa;float:right;font-size:28px;font-weight:700;line-height:28px;-webkit-appearance:non}#bwpostman .bwp_close:hover,#bwpostman .bwp_close:focus{color:#000;text-decoration:none;cursor:pointer}";
		$percent = 0.10;

		$js = "
			var css = '$css',
				head = document.head || document.getElementsByTagName('head')[0],
				style = document.createElement('style');

			style.type = 'text/css';
			if (style.styleSheet){
				// This is required for IE8 and below.
				style.styleSheet.cssText = css;
			} else {
				style.appendChild(document.createTextNode(css));
			}
			head.appendChild(style);

			function setModal() {
				// Set the modal height and width 90%
				if (typeof window.innerWidth != 'undefined')
				{
					viewportwidth = window.innerWidth,
						viewportheight = window.innerHeight
				}
				else if (typeof document.documentElement != 'undefined'
					&& typeof document.documentElement.clientWidth !=
					'undefined' && document.documentElement.clientWidth != 0)
				{
					viewportwidth = document.documentElement.clientWidth,
						viewportheight = document.documentElement.clientHeight
				}
				else
				{
					viewportwidth = document.getElementsByTagName('body')[0].clientWidth,
						viewportheight = document.getElementsByTagName('body')[0].clientHeight
				}
				var modalcontent = document.getElementById('bwp_modal-content');
				modalcontent.style.height = viewportheight-(viewportheight*$percent)+'px';
				";

		$js .= "
								modalcontent.style.width = viewportwidth-(viewportwidth*0.10)+'px';
						";

		$js .= "

				// Get the modal
				var modal = document.getElementById('bwp_Modal');

				// Get the Iframe-Wrapper and set Iframe
				var wrapper = document.getElementById('bwp_wrapper');
				var html = '<iframe id=\"iFrame\" name=\"iFrame\" src=\"$url\" style=\"width:100%; height:100%;\"></iframe>';

				// Open the modal
					wrapper.innerHTML = html;
                    wrapper.style.opacity = '1';
					modal.style.display = 'block';

			}
			setModal();

		";

		return <<<EOS
		<div id="bwpostman">$html</div><script>$js</script>
EOS;
	}

	/**
	 * Method to install sample templates
	 *
	 * @param string $sql
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since
	 */

	private function installdata(string $sql)
	{
		$app = Factory::getApplication();
		$db  = Factory::getDbo();

		//we call sql file for the templates data
		$buffer = file_get_contents(JPATH_ADMINISTRATOR . '/components/com_bwpostman/sql/' . $sql);

		// Graceful exit and rollback if read not successful
		if ($buffer)
		{
			// Create an array of queries from the sql file
			$queries = JDatabaseDriver::splitSql($buffer);

			// No queries to process
			if (count($queries) != 0)
			{
				// Process each query in the $queries array (split out of sql file).
				foreach ($queries as $query)
				{
					$query = trim($query);
					if ($query != '' && $query[0] != '#')
					{
						$query = str_replace("`DUMMY`", "'DUMMY'", $query);

						try
						{
							$db->setQuery($query);
							$db->execute();
						}
						catch (RuntimeException $e)
						{
							$app->enqueueMessage(Text::_('COM_BWPOSTMAN_TEMPLATES_NOT_INSTALLED'), 'warning');
						}
					}
				}//end foreach
			}
		}
	}

	/**
	 * returns default values for params
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	private function setDefaultParams()
	{
		$params_default = array();
		$config	= Factory::getApplication()->getConfig();

		$params_default['default_from_name']               = $config->get('fromname');
		$params_default['default_from_email']              = $config->get('mailfrom');
		$params_default['default_reply_email']             = $config->get('mailfrom');
		$params_default['legal_information_text']          = "";
		$params_default['default_mails_per_pageload']      = "100";
		$params_default['mails_per_pageload_delay']        = 10;
		$params_default['mails_per_pageload_delay_unit']   = "1000";
		$params_default['publish_nl_by_default']           = "0";
		$params_default['compress_backup']                 = "0";
		$params_default['show_boldt_link']                 = "1";
		$params_default['loglevel']                        = "BW_ERROR";
		$params_default['pretext']                         = "BWPM_MAILINGLIST_GDPR";
		$params_default['show_gender']                     = "1";
		$params_default['show_firstname_field']            = "1";
		$params_default['firstname_field_obligation']      = "1";
		$params_default['show_name_field']                 = "1";
		$params_default['name_field_obligation']           = "1";
		$params_default['show_special']                    = "1";
		$params_default['special_field_obligation']        = "0";
		$params_default['special_label']                   = "Mitgliedsnummer";
		$params_default['special_desc']                    = "Bitte geben Sie ihre Mitgliedsnummer ein.";
		$params_default['show_emailformat']                = "1";
		$params_default['default_emailformat']             = "0";
		$params_default['verify_mailaddress']              = "0";
		$params_default['show_desc']                       = "1";
		$params_default['desc_length']                     = "150";
		$params_default['disclaimer']                      = "0";
		$params_default['disclaimer_selection']            = "1";
		$params_default['disclaimer_link']                 = "https:\/\/www.disclaimer.de\/disclaimer.htm";
		$params_default['article_id']                      = "70";
		$params_default['disclaimer_menuitem']             = "108";
		$params_default['disclaimer_target']               = "0";
		$params_default['showinmodal']	                   = "1";
		$params_default['use_captcha']                     = "0";
		$params_default['security_question']               = "Wie viele Beine hat ein Pferd? (1, 2, ...)";
		$params_default['security_answer']                 = "4";
		$params_default['activation_salutation_text']      = "Hello";
		$params_default['activation_text']                 = "text for activation";
		$params_default['permission_text']                 = "text for agreement";
		$params_default['activation_to_webmaster']         = "0";
		$params_default['activation_from_name']            = "";
		$params_default['activation_to_webmaster_email']   = "";
		$params_default['del_sub_1_click']                 = "0";
		$params_default['deactivation_to_webmaster']       = "0";
		$params_default['deactivation_from_name']          = "";
		$params_default['deactivation_to_webmaster_email'] = "";
		$params_default['filter_field']	                   = "1";
		$params_default['date_filter_enable']              = "1";
		$params_default['ml_filter_enable']                = "1";
		$params_default['cam_filter_enable']               = "1";
		$params_default['group_filter_enable']             = "1";
		$params_default['attachment_enable']               = "1";
		$params_default['access-check']                    = "1";
		$params_default['display_num']                     = "10";
		$params_default['attachment_single_enable']        = "1";
		$params_default['subject_as_title']                = "1";
		$params_default['excluded_categories']             = "";
		$params_default['fe_layout_list']                  = "cassiopeia";
		$params_default['fe_layout']                       = "cassiopeia";
		$params_default['fe_layout_detail']                = "cassiopeia";

		$params	= json_encode($params_default);

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->update($db->quoteName('#__extensions'));
		$query->set($db->quoteName('params') . " = " . $db->quote($params));
		$query->where($db->quoteName('element') . " = " . $db->quote('com_bwpostman'));

		try
		{
			$db->setQuery($query);
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
	}

	/**
	 * install or update access rules for component
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since	1.2.0
	 */
	private function updateRules()
	{
		$default_rules	= array(
								"core.admin" => array('7' => 1),
								"core.manage" => array('7' => 1, '6' => 1),
								"bwpm.create" => array('7' => 1, '6' => 1),
								"bwpm.edit" => array('7' => 1, '6' => 1),
								"bwpm.edit.own" => array('7' => 1, '6' => 1),
								"bwpm.edit.state" => array('7' => 1, '6' => 1),
								"bwpm.archive" => array('7' => 1, '6' => 1),
								"bwpm.restore" => array('7' => 1, '6' => 1),
								"bwpm.delete" => array('7' => 1, '6' => 1),
								"bwpm.send" => array('7' => 1, '6' => 1),
								"bwpm.view.newsletter" => array('7' => 1, '6' => 1),
								"bwpm.view.subscriber" => array('7' => 1, '6' => 1),
								"bwpm.view.campaign" => array('7' => 1, '6' => 1),
								"bwpm.view.mailinglist" => array('7' => 1, '6' => 1),
								"bwpm.view.template" => array('7' => 1, '6' => 1),
								"bwpm.view.archive" => array('7' => 1, '6' => 1),
								"bwpm.view.maintenance" => array('7' => 1, '6' => 1),
							);
		// get stored component rules
		$current_rules = array();
		$db            = Factory::getDbo();
		$query         = $db->getQuery(true);

		$query->select($db->quoteName('rules'));
		$query->from($db->quoteName('#__assets'));
		$query->where($db->quoteName('name') . " = " . $db->quote('com_bwpostman'));

		try
		{
			$db->setQuery($query);
			$current_rules = json_decode($db->loadResult(), true);
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		//detect missing component rules
		foreach ($default_rules as $key => $value)
		{
			if (is_array($current_rules) && !array_key_exists($key, $current_rules))
			{
				$current_rules[$key] = $value;
			}
		}

		$rules = json_encode($current_rules);

		// update component rules in asset table
		$query = $db->getQuery(true);

		$query->update($db->quoteName('#__assets'));
		$query->set($db->quoteName('rules') . " = " . $db->quote($rules));
		$query->where($db->quoteName('name') . " = " . $db->quote('com_bwpostman'));

		try
		{
			$db->setQuery($query);
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
	}

	/**
	 *
	 * @return mixed
	 *
	 * @throws Exception
	 *
	 * @since version
	 */
	private function getRootAsset()
	{
		$rootRules = null;

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('rules'));
		$query->from($db->quoteName('#__assets'));
		$query->where($db->quoteName('name') . ' = ' . $db->Quote('root.1'));

		try
		{
			$db->setQuery($query);

			$rootRules = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $rootRules;
	}

	/**
	 * @param $newRootRules
	 *
	 * @throws Exception
	 *
	 * @since version
	 */
	private function saveRootAsset($newRootRules)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->update($db->quoteName('#__assets'));
		$query->set($db->quoteName('rules') . " = " . $db->Quote($newRootRules));
		$query->where($db->quoteName('name') . ' = ' . $db->Quote('root.1'));

		try
		{
			$db->setQuery($query);
			$db->execute();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
		$db->execute();
	}

	/**
	 * Get id of installed extension
	 *
	 * @param integer $clientId
	 * @param string  $extensionName
	 *
	 * @return string
	 *
	 * @throws Exception
	 *
	 * @since version
	 */
	private function getExtensionId(int $clientId, string $extensionName = 'com_bwpostman')
	{
		$db    = Factory::getDbo();
		$result = 0;

		$query = $db->getQuery(true);
		$query->select($db->quoteName('extension_id'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element') . ' = ' . $db->quote($extensionName));
		$query->where($db->quoteName('client_id') . ' = ' . $db->quote($clientId));

		try
		{
			$this->logger->addEntry(new LogEntry(sprintf("Postflight getExtensionId Query: %s", $query), BwLogger::BW_DEBUG, $this->log_cat));

			$db->setQuery($query);

			$result = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $result;
	}
}
