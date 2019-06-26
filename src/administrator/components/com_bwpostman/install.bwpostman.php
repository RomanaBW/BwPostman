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

//use Joomla\Registry\Format\Json;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\Component\Users\Administrator\Model\GroupModel as GroupModel;
/**
 * Class Com_BwPostmanInstallerScript
 *
 * @since       0.9.6.3
 */
class Com_BwPostmanInstallerScript
{
	/**
	 * @var JAdapterInstance $parentInstaller
	 *
	 * @since       0.9.6.3
	 */
	public $parentInstaller;

	/**
	 * @var string $minimum_joomla_release
	 *
	 * @since       2.0.0
	 */
	private $minimum_joomla_release;

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
	 * @since  2.4.0
	 */
	private $logger;

	/**
	 * Property to hold log category
	 *
	 * @var    string
	 *
	 * @since  2.4.0
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
		$_db = JFactory::getDbo();
		$query = 'INSERT INTO '. $_db->quoteName('#__postinstall_messages') .
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

		$_db->setQuery($query);
		$_db->execute();
		*/
	}

	/**
	 * Called before any type of action
	 *
	 * @param   string  			                    $type		Which action is happening (install|uninstall|discover_install|update)
	 * @param   Joomla\CMS\Installer\InstallerAdapter	$parent		The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 *
	 * @throws Exception
	 *
	 * @since       0.9.6.3
	 */

	public function preflight($type, Joomla\CMS\Installer\InstallerAdapter $parent)
	{
		$app 		= JFactory::getApplication();
		$session	= JFactory::getSession();
		$jversion	= new JVersion();

		if (function_exists('set_time_limit'))
		{
			set_time_limit(0);
		}

		$this->parentInstaller	= $parent->getParent();
		$manifest = $parent->getManifest();

		// Get component manifest file version
		$this->release	= $manifest->version;
		$session->set('release', $this->release->__toString(), 'bwpostman');

		// Manifest file minimum Joomla version
		$this->minimum_joomla_release = $manifest->attributes()->version;

		// abort if the current Joomla release is older
		if(version_compare($jversion->getShortVersion(), $this->minimum_joomla_release, 'lt'))
		{
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_INSTALL_ERROR_JVERSION', $this->minimum_joomla_release), 'error');
			return false;
		}

		if(version_compare(phpversion(), '5.3.10', 'lt'))
		{
			$app->enqueueMessage(JText::_('COM_BWPOSTMAN_USES_PHP5'), 'error');
			return false;
		}

		// abort if the component being installed is not newer than the currently installed version
		if ($type == 'update')
		{
			$oldRelease = $this->getManifestVar('version');
			$app->setUserState('com_bwpostman.update.oldRelease', $oldRelease);

			if (version_compare($this->release, $oldRelease, 'lt'))
			{
				$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_INSTALL_ERROR_INCORRECT_VERSION_SEQUENCE', $oldRelease, $this->release), 'error');
				return false;
			}

			// delete existing files in frontend and backend to prevent conflicts with previous relicts
			jimport('joomla.filesystem.folder');
		}

		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('params'));
		$query->from($_db->quoteName('#__extensions'));
		$query->where($_db->quoteName('element') . " = " . $_db->quote('com_bwpostman'));

		$_db->setQuery($query);
		try
		{
			$params_default = $_db->loadResult();
			$app->setUserState('com_bwpostman.install.params', $params_default);
		}
		catch (RuntimeException $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
		}

		if ($type !== 'uninstall')
		{
			// Check if utf8mb4 is supported; if so, copy utf8mb4 file as sql installation file
			jimport('joomla.filesystem.file');
			$tmp_path   = $this->parentInstaller->getPath('source') . '/admin';

			require_once($tmp_path . '/helpers/installhelper.php');

			$name = $_db->getName();
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
	 * @param   string  			$type		Which action is happening (install|uninstall|discover_install)
	 *
	 * @return  boolean  True on success
	 *
	 * @throws Exception
	 *
	 * @since       0.9.6.3
	 */

	public function postflight($type)
	{
		$m_params   = JComponentHelper::getParams('com_media');
		$this->copyTemplateImagesToMedia($m_params);

		// Make new folder and copy template thumbnails to folder "images" if image_path is not "images"
		if ($m_params->get('image_path', 'images') != 'images')
		{
			$this->copyTemplateImagesToImages();
		}

		if ($type == 'install' || $type == 'update')
		{
			require_once(JPATH_ADMINISTRATOR . '/components/com_bwpostman/libraries/logging/BwLogger.php');

			$log_options  = array('text_file' => 'bwpostman/BwPostman.log');
			$this->logger = new BwLogger($log_options);
		}

		if ($type == 'install')
		{
			// Set BwPostman default settings in the extensions table at install
			$this->setDefaultParams();

			// Create sample user groups, set viewlevel
			$this->installSampleUsergroups();
		}

		// check if sample templates exists
//		$this->checkSampleTemplates();

		// update/complete component rules
//		$this->updateRules();

		if ($type == 'update')
		{
			$app 		= JFactory::getApplication();
			$oldRelease	= $app->getUserState('com_bwpostman.update.oldRelease', '');

			if (version_compare($oldRelease, '1.0.1', 'lt'))
			{
				$this->adjustMLAccess();
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

			$this->repairRootAsset();

			// convert tables to UTF8MB4
			jimport('joomla.filesystem.file');
			$tmp_path   = $this->parentInstaller->getPath('source');
			require_once($tmp_path . '/admin/helpers/installhelper.php');
			BwPostmanInstallHelper::convertToUtf8mb4($this->reference_table, JPATH_ADMINISTRATOR . $this->conversion_file);

			// remove double entries in table extensions
			$this->removeDoubleExtensionsEntries();

			// check all tables of BwPostman
			// Let Ajax client redirect
			$modal = $this->getModal();
			$app->enqueueMessage(JText::_('Installing BwPostman ... ') . $modal);
		}

		return true;
	}

	/**
	 * Called on installation
	 *
	 * @return  void
	 *
	 * @since       0.9.6.3
	 */

	public function install()
	{
		$session	= JFactory::getSession();
		$session->set('update', false, 'bwpostman');
		$this->bwpostman_install();
		$this->showFinished(false);
	}

	/**
	 * Called on update
	 *
	 * @return  void
	 *
	 * @since       0.9.6.3
	 */

	public function update()
	{
		$session	= JFactory::getSession();
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
//		$this->deleteBwPmAdminFromRootAsset();
//		$this->deleteBwPmAdminFromViewlevels();
//		$this->deleteSampleUsergroups();

		JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_UNINSTALL_THANKYOU'), 'message');
		//  notice that folder image/bw_postman is not removed
		$m_params   = JComponentHelper::getParams('com_media');
		$image_path = $m_params->get('image_path', 'images');

		JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_UNINSTALL_FOLDER_BWPOSTMAN', $image_path), 'notice');

		$_db		= JFactory::getDbo();
		$query  = $_db->getQuery(true);
		$query->delete($_db->quoteName('#__postinstall_messages'));
		$query->where($_db->quoteName('language_extension') . ' = ' . $_db->quote('com_bwpostman'));
		$_db->setQuery($query);

		try
		{
			$_db->execute();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
	}

	/**
	 * get a variable from the manifest file (actually, from the manifest cache).
	 *
	 * @param   string  $name
	 *
	 * @return  mixed  $manifest
	 *
	 * @throws Exception
	 *
	 * @since       0.9.6.3
	 */
	private function getManifestVar($name)
	{
		$manifest   = array();
		$_db		= JFactory::getDbo();
		$query	    = $_db->getQuery(true);

		$query->select($_db->quoteName('manifest_cache'));
		$query->from($_db->quoteName('#__extensions'));
		$query->where($_db->quoteName('element') . " = " . $_db->quote('com_bwpostman'));
		$_db->setQuery($query);

		try
		{
			$manifest = json_decode($_db->loadResult(), true);
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return $manifest[$name];
	}


	/**
	 * Correct campaign_id in newsletters because of an error previous version
	 *
	 * @return  boolean  True on success
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	private function correctCamId()
	{
		$_db		= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->update($_db->quoteName('#__bwpostman_newsletters'));
		$query->set($_db->quoteName('campaign_id') . " = " . (int) -1);
		$query->where($_db->quoteName('campaign_id') . " = " . (int) 0);
		$_db->setQuery($query);

		try
		{
			$_db->execute();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		return true;
	}

	/**
	 * Fill cross table campaigns mailinglists with values from all newsletters of the specific campaign
	 *
	 * @return  boolean  True on success
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	private function fillCamCrossTable()
	{
		$all_cams   = array();
		$_db	    = JFactory::getDbo();
		$query	    = $_db->getQuery(true);

		// First get all campaigns
		$query->select($_db->quoteName('id') . ' AS ' . $_db->quoteName('campaign_id'));
		$query->from($_db->quoteName('#__bwpostman_campaigns'));
		$_db->setQuery($query);

		try
		{
			$all_cams	= $_db->loadAssocList();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		if (count($all_cams) > 0) {
			foreach ($all_cams as $cam) {
				$cross_values   = array();
				$query			= $_db->getQuery(true);

				$query->select('DISTINCT(' . $_db->quoteName('cross1') . '.' . $_db->quoteName('mailinglist_id') . ')');
				$query->from($_db->quoteName('#__bwpostman_newsletters_mailinglists') . ' AS ' . $_db->quoteName('cross1'));
				$query->leftJoin('#__bwpostman_newsletters AS n ON cross1.newsletter_id = n.id');
				$query->where($_db->quoteName('n') . '.' . $_db->quoteName('campaign_id') . ' = ' . $cam['campaign_id']);
				$_db->setQuery($query);

				try
				{
					$cross_values	= $_db->loadAssocList();
				}
				catch (RuntimeException $e)
				{
					JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				}

				if (count($cross_values) > 0) {
					foreach ($cross_values as $item) {
						$query	= $_db->getQuery(true);

						$query->insert($_db->quoteName('#__bwpostman_campaigns_mailinglists'));
						$query->columns(
							array(
								$_db->quoteName('campaign_id'),
								$_db->quoteName('mailinglist_id')
							)
						);
							$query->values(
								$_db->quote($cam['campaign_id']) . ',' .
								$_db->quote($item['mailinglist_id'])
							);
						$_db->setQuery($query);

						try
						{
							$_db->execute();
						}
						catch (RuntimeException $e)
						{
							JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
						}
					}
				}
			}
		}

		return true;
	}

	/**
	 * Method to adjust field access in table mailinglists
	 *
	 * in prior versions of BwPostman access holds the values like viewlevels, but beginning with 0.
	 * But 0 is in Joomla the value for new dataset, so in version 1.0.1 of BwPostman this will be adjusted (incremented)
	 *
	 * @return	void
	 *
	 * @throws Exception
	 *
	 * @since	1.0.1
	 */
	private function adjustMLAccess()
	{
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->update($_db->quoteName('#__bwpostman_mailinglists'));
		$query->set($_db->quoteName('access') . " = " . $_db->quoteName('access') . '+1');
		$_db->setQuery($query);

		try
		{
			$_db->execute();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
	}

	/**
	 * Method to copy the provided template thumbnails to media folder
	 *
	 * @param object    $m_params   params of com_media
	 *
	 * @return void
	 *
	 * @since   2.0.0
	 */
	private function copyTemplateImagesToMedia($m_params)
	{
		$image_path = JPATH_ROOT . '/' . $m_params->get('image_path', 'images') . '/bw_postman';
		$media_path = JPATH_ROOT . '/media/bw_postman/images/';

		// make new folder and copy template thumbnails
		if (!JFolder::exists($image_path))
		{
			JFolder::create($image_path);
		}

		if (!JFile::exists($image_path . '/index.html'))
		{
			JFile::copy($media_path . 'index.html', $image_path . '/index.html');
		}

		if (!JFile::exists($image_path . '/deep_blue.png'))
		{
			JFile::copy($media_path . 'deep_blue.png', $image_path . '/deep_blue.png');
		}

		if (!JFile::exists($image_path . '/soft_blue.png'))
		{
			JFile::copy($media_path . 'soft_blue.png', $image_path . '/soft_blue.png');
		}

		if (!JFile::exists($image_path . '/creme.png'))
		{
			JFile::copy($media_path . 'creme.png', $image_path . '/creme.png');
		}

		if (!JFile::exists($image_path . '/sample_html.png'))
		{
			JFile::copy($media_path . 'sample_html.png', $image_path . '/sample_html.png');
		}

		if (!JFile::exists($image_path . '/text_template_1.png'))
		{
			JFile::copy($media_path . 'text_template_1.png', $image_path . '/text_template_1.png');
		}

		if (!JFile::exists($image_path . '/text_template_2.png'))
		{
			JFile::copy($media_path . 'text_template_2.png', $image_path . '/text_template_2.png');
		}

		if (!JFile::exists($image_path . '/text_template_3.png'))
		{
			JFile::copy($media_path . 'text_template_3.png', $image_path . '/text_template_3.png');
		}

		if (!JFile::exists($image_path . '/sample_text.png'))
		{
			JFile::copy($media_path . 'sample_text.png', $image_path . '/sample_text.png');
		}

		if (!JFile::exists($image_path . '/joomla_black.gif'))
		{
			JFile::copy($media_path . 'joomla_black.gif', $image_path . '/joomla_black.gif');
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
		$dest = JPATH_ROOT . '/images/bw_postman';
		if (!JFolder::exists($dest))
		{
			JFolder::create(JPATH_ROOT . '/images/bw_postman');
		}

		if (!JFile::exists(JPATH_ROOT . '/images/bw_postman/index.html'))
		{
			JFile::copy(JPATH_ROOT . '/images/index.html', JPATH_ROOT . '/images/bw_postman/index.html');
		}

		if (!JFile::exists(JPATH_ROOT . '/images/bw_postman/deep_blue.png'))
		{
			JFile::copy(JPATH_ROOT . '/media/bw_postman/images/deep_blue.png', JPATH_ROOT . '/images/bw_postman/deep_blue.png');
		}

		if (!JFile::exists(JPATH_ROOT . '/images/bw_postman/soft_blue.png'))
		{
			JFile::copy(JPATH_ROOT . '/media/bw_postman/images/soft_blue.png', JPATH_ROOT . '/images/bw_postman/soft_blue.png');
		}

		if (!JFile::exists(JPATH_ROOT . '/images/bw_postman/creme.png'))
		{
			JFile::copy(JPATH_ROOT . '/media/bw_postman/images/creme.png', JPATH_ROOT . '/images/bw_postman/creme.png');
		}

		if (!JFile::exists(JPATH_ROOT . '/images/bw_postman/sample_html.png'))
		{
			JFile::copy(JPATH_ROOT . '/media/bw_postman/images/sample_html.png', JPATH_ROOT . '/images/bw_postman/sample_html.png');
		}

		if (!JFile::exists(JPATH_ROOT . '/images/bw_postman/text_template_1.png'))
		{
			JFile::copy(JPATH_ROOT . '/media/bw_postman/images/text_template_1.png', JPATH_ROOT . '/images/bw_postman/text_template_1.png');
		}

		if (!JFile::exists(JPATH_ROOT . '/images/bw_postman/text_template_2.png'))
		{
			JFile::copy(JPATH_ROOT . '/media/bw_postman/images/text_template_2.png', JPATH_ROOT . '/images/bw_postman/text_template_2.png');
		}

		if (!JFile::exists(JPATH_ROOT . '/images/bw_postman/text_template_3.png'))
		{
			JFile::copy(JPATH_ROOT . '/media/bw_postman/images/text_template_3.png', JPATH_ROOT . '/images/bw_postman/text_template_3.png');
		}

		if (!JFile::exists(JPATH_ROOT . '/images/bw_postman/sample_text.png'))
		{
			JFile::copy(JPATH_ROOT . '/media/bw_postman/images/sample_text.png', JPATH_ROOT . '/images/bw_postman/sample_text.png');
		}

		if (!JFile::exists(JPATH_ROOT . '/images/bw_postman/joomla_black.gif'))
		{
			JFile::copy(JPATH_ROOT . '/media/bw_postman/images/joomla_black.gif', JPATH_ROOT . '/images/bw_postman/joomla_black.gif');
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
		$_db	= JFactory::getDbo();
		$query  = $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__bwpostman_templates'));
		$_db->setQuery($query);

		try
		{
			$templateFields = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		$query  = $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__bwpostman_templates_tpl'));
		$_db->setQuery($query);

		try
		{
			$templatetplFields = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
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
	 * @return boolean  true on success
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
			$jversion = new JVersion();
			if(version_compare($jversion->getShortVersion(), '4.0.0', 'lt'))
			{
				JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_users/models');
				$groupModel = JModelLegacy::getInstance('Group', 'UsersModel');
			}
			else
			{
				$groupModel = new Joomla\Component\Users\Administrator\Model\GroupModel;
			}

			$this->logger->addEntry(new JLogEntry('GroupModel 1: ' . var_dump($groupModel), JLog::DEBUG, $this->log_cat));

			// get group ID of public
			$public_id = $this->getGroupId('Public');

			// Ensure user group BwPostmanAdmin exists
			$groupExists = $this->getGroupId('BwPostmanAdmin');
			$this->logger->addEntry(new JLogEntry('Group BwPostmanAdmin exists: ' . $groupExists, JLog::DEBUG, $this->log_cat));
			$this->logger->addEntry(new JLogEntry('J-Version: ' . $jversion->getShortVersion(), JLog::DEBUG, $this->log_cat));

			if (!$groupExists)
			{
				if(version_compare($jversion->getShortVersion(), '4.0.0', 'lt'))
				{
					$ret = $groupModel->save(array('id' => 0, 'parent_id' => $public_id, 'title' => 'BwPostmanAdmin'));
				}
				else
				{
					$this->logger->addEntry(new JLogEntry('GroupModel 2: ' . var_dump(Joomla\Component\Users\Administrator\Model\GroupModel), JLog::DEBUG, $this->log_cat));

					$ret = GroupModel::save(array('id' => 0, 'parent_id' => $public_id, 'title' => 'BwPostmanAdmin'));
				}

				if (!$ret)
				{
					echo JText::sprintf('COM_BWPOSTMAN_INSTALLATION_ERROR_CREATING_USERGROUPS: %s', $ret);
					throw new Exception(JText::sprintf('COM_BWPOSTMAN_INSTALLATION_ERROR_CREATING_USERGROUPS: %s',
						$ret));
				}
			}

			$admin_groupId = $this->getGroupId('BwPostmanAdmin');
			$this->adminUsergroup = $admin_groupId;

			// Ensure user group BwPostmanManager exists
			$manager_groupId = $this->getGroupId('BwPostmanManager');

//			if (!$manager_groupId)
//			{
//				$manager_groupId = 0;
//			}
//
//			$ret = $groupModel->save(array('id' => $manager_groupId, 'parent_id' => $admin_groupId, 'title' => 'BwPostmanManager'));
//
//			if (!$ret)
//			{
//				echo JText::sprintf('COM_BWPOSTMAN_INSTALLATION_ERROR_CREATING_USERGROUPS: %s', $ret);
//				throw new Exception(JText::sprintf('COM_BWPOSTMAN_INSTALLATION_ERROR_CREATING_USERGROUPS: %s',
//					$ret));
//			}

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

//					$ret = $groupModel->save(array('id' => $groupId, 'parent_id' => $parent_id, 'title' => $item));

//					if (!$ret)
//					{
//						throw new Exception(JText::_('COM_BWPOSTMAN_INSTALLATION_ERROR_CREATING_USERGROUPS'));
//					}

					$parent_id = $this->getGroupId($item);
				}
			}

			return true;
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}
	}

	/**
	 * Method to add BwPostmanAdmin to view level special
	 *
	 * @return boolean  true on success
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
				return false;
			}

			// get the model for viewlevels
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_users/models');
			$viewlevelModel = JModelLegacy::getInstance('Level', 'UsersModel');

			// Get viewlevel special
			$specialLevel = $viewlevelModel->getItem(3);

			// Insert BwPostmanAdmin to the rules of viewlevel special
			array_unshift($specialLevel->rules, (int) $this->adminUsergroup);
			$specialLevelArray = \Joomla\Utilities\ArrayHelper::fromObject($specialLevel, false);

			// Save viewlevel special
			$viewlevelModel->save($specialLevelArray);

			return true;
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}
	}

	/**
	 * Method to add BwPostmanAdmin to view level special
	 *
	 * @return boolean  true on success
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function addBwPmAdminToRootAsset()
	{
		try
		{
			// Get group ID of BwPostmanAdmin
			$adminGroup = $this->getGroupId('BwPostmanAdmin');

			if (!$adminGroup)
			{
				return false;
			}

			// Get root asset
			$rootRules = $this->getRootAsset();

			// Insert BwPostmanAdmin to root asset
			$tmpRules   = json_decode($rootRules, true);

			$tmpRules['core.login.site'][$adminGroup]  = 1;
			$tmpRules['core.login.admin'][$adminGroup] = 1;
			$tmpRules['core.create'][$adminGroup]      = 1;
			$tmpRules['core.delete'][$adminGroup]      = 1;
			$tmpRules['core.edit'][$adminGroup]        = 1;
			$tmpRules['core.edit.own'][$adminGroup]    = 1;
			$tmpRules['core.edit.state'][$adminGroup]  = 1;

			$newRootRules = json_encode($tmpRules);

			// Save root asset
			$this->saveRootAsset($newRootRules);

			return true;
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}
	}

	/**
	 * Method to delete sample user groups and access levels
	 *
	 * @return boolean  true on success
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	private function deleteSampleUsergroups()
	{
		try
		{
			$_db	            = JFactory::getDbo();
			$user_id            = JFactory::getUser()->get('id');
			$bwpostman_groups   = array(0);
			$query              = $_db->getQuery(true);

			// get group ids of BwPostman user groups
			$query->select($_db->quoteName('id'));
			$query->from($_db->quoteName('#__usergroups'));
			$query->where($_db->quoteName('title') . ' LIKE ' . $_db->quote('%BwPostman%'));
			$_db->setQuery($query);

			try
			{
				$bwpostman_groups  = $_db->loadColumn();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			// get group id of BwPostman main user group
			$bwpostman_main_group   = '';
			$query	                = $_db->getQuery(true);

			$query->select($_db->quoteName('id'));
			$query->from($_db->quoteName('#__usergroups'));
			$query->where($_db->quoteName('title') . ' = ' . $_db->quote('BwPostmanAdmin'));
			$_db->setQuery($query);

			try
			{
				$bwpostman_main_group  = $_db->loadResult();
				$bwpostman_main_group  = (int) $bwpostman_main_group;
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			// get group ids of BwPostman user groups, where actual user is member
			$member_ids = '';
			if (is_array($bwpostman_groups) && !empty($bwpostman_groups))
			{
				$query	    = $_db->getQuery(true);
				$query->select($_db->quoteName('group_id'));
				$query->from($_db->quoteName('#__user_usergroup_map'));
				$query->where($_db->quoteName('user_id') . ' = ' . (int) $user_id);
				$query->where($_db->quoteName('group_id') . ' IN (' . implode(',', $bwpostman_groups) . ')');

				$_db->setQuery($query);

				try
				{
					$member_ids  = $_db->loadColumn();
				}
				catch (RuntimeException $e)
				{
					JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				}
			}

			// delete current user from BwPostman user groups
			if (is_array($member_ids) || is_object($member_ids))
			{
				foreach ($member_ids as $item)
				{
					JUserHelper::removeUserFromGroup($user_id, $item);
				}
			}

			// get the model for user groups
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_users/models', 'UsersModel');
			$groupModel = JModelLegacy::getInstance('Group', 'UsersModel');

			JAccess::clearStatics();

			// delete main user group of BwPostman (all other (sub) user groups of BwPostman will be deleted automatically by Joomla)
			$res = $groupModel->delete($bwpostman_main_group);
			if (!$res)
			{
				throw new BwException(JText::_('COM_BWPOSTMAN_DEINSTALLATION_ERROR_REMOVE_USERGROUPS'));
			}

			return true;
		}
		catch (RuntimeException $e)
		{
			echo $e->getMessage();
			return false;
		}
		catch (BwException $e)
		{
			echo $e->getMessage();
			return false;
		}
	}

	/**
	 * Method to add BwPostmanAdmin to view level special
	 *
	 * @return boolean  true on success
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
				JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_users/models');
				$viewlevelModel = JModelLegacy::getInstance('Level', 'UsersModel');

				// Get viewlevel special
				$specialLevel = $viewlevelModel->getItem(3);

				// Remove BwPostmanAdmin from to the rules of viewlevel special
				if (in_array($adminGroup, $specialLevel->rules))
				{
					array_shift($specialLevel->rules);
					$specialLevelArray = \Joomla\Utilities\ArrayHelper::fromObject($specialLevel);

					// Save viewlevel special
					$viewlevelModel->save($specialLevelArray);
				}
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				return false;
			}
		}

		return true;
	}

	/**
	 * Method to add BwPostmanAdmin to view level special
	 *
	 * @return boolean  true on success
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
			$tmpRules   = json_decode($rootRules, true);

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

			return true;
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
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
			JPATH_ROOT . '/media/bw_postman/js',
			JPATH_ROOT . '/administrator/components/com_bwpostman/views/media',
			JPATH_ROOT . '/administrator/components/com_bwpostman/views/medialist');
		// Remove views and js
		foreach ($removeFolders as $folder)
		{
			if (JFolder::exists($folder))
			{
				JFolder::delete($folder);
			}
		}

		if (JFile::exists(JPATH_ROOT . '/administrator/components/com_bwpostman/models/fields/allmedia.php'))
		{
			JFile::delete(JPATH_ROOT . "/administrator/components/com_bwpostman/models/fields/allmedia.php");
		}



		// Remove field
	}

	/**
	 * Gets the group Id of the selected group name
	 *
	 * @param   string  $name  The name of the group
	 *
	 * @return  int|bool  the ID of the group or false, if group not exists
	 *
	 * @throws Exception
	 *
	 * @since
	 */

	private function getGroupId($name)
	{
		$result = false;
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__usergroups'));
		$query->where("`title` LIKE '" . $_db->escape($name) . "'");

		$_db->setQuery($query);

		try
		{
			$result = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage('Error GroupId: ' . $e->getMessage() . '<br />', 'error');
		}

		return $result;
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
		$_db	= JFactory::getDbo();
		$result = 0;

		$query = $_db->getQuery(true);
		$query->select($_db->quoteName('extension_id'));
		$query->from($_db->quoteName('#__extensions'));
		$query->where($_db->quoteName('element') . ' = ' . $_db->quote('com_bwpostman'));
		$query->where($_db->quoteName('client_id') . ' = ' . $_db->quote('0'));

		$_db->setQuery($query);

		try
		{
			$result = $_db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		if ($result)
		{
			$query = $_db->getQuery(true);
			$query->delete($_db->quoteName('#__extensions'));
			$query->where($_db->quoteName('extension_id') . ' =  ' . $_db->quote($result));

			$_db->setQuery($query);

			try
			{
				$_db->execute();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
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
//		$this->addBwPmAdminToViewlevel();
//		$this->addBwPmAdminToRootAsset();
//		/*
//		 * Rewrite section assets
//		 *
//		 */
//		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_bwpostman/models');
//		$maintenanceModel = JModelLegacy::getInstance('Maintenance', 'BwPostmanModel');
//
//		$maintenanceModel->createBaseAssets();
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
	 * sets parameter values in the component's row of the extension table
	 *
	 * @param array     $param_array
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	private function setParams($param_array)
	{
		if (count($param_array) > 0)
		{
			// read the existing component value(s)
			$_db	= JFactory::getDbo();
			$query	= $_db->getQuery(true);
			$params = '';

			$query->select($_db->quoteName('params'));
			$query->from($_db->quoteName('#__extensions'));
			$query->where($_db->quoteName('element') . " = " . $_db->quote('com_bwpostman'));
			$_db->setQuery($query);

			try
			{
				$params = json_decode($_db->loadResult(), true);
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			// add the new variable(s) to the existing one(s)
			foreach ($param_array as $name => $value)
			{
				$params[(string) $name] = (string) $value;
			}

			// store the combined new and existing values back as a JSON string
			$paramsString = json_encode($params);
			$query	= $_db->getQuery(true);

			$query->update($_db->quoteName('#__extensions'));
			$query->set($_db->quoteName('params') . " = " . $_db->quote($paramsString));
			$query->where($_db->quoteName('element') . " = " . $_db->quote('com_bwpostman'));
			$_db->setQuery($query);

			try
			{
				$_db->execute();
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}
	}

	/**
	 * shows the HTML after installation/update
	 *
	 * @param   boolean $update
	 *
	 * @return  void
	 *
	 * @since
	 */
	public function showFinished($update)
	{

		$lang = JFactory::getLanguage();
		//Load first english files
		$lang->load('com_bwpostman.sys', JPATH_ADMINISTRATOR, 'en_GB', true);
		$lang->load('com_bwpostman', JPATH_ADMINISTRATOR, 'en_GB', true);

		//load specific language
		$lang->load('com_bwpostman.sys', JPATH_ADMINISTRATOR, null, true);
		$lang->load('com_bwpostman', JPATH_ADMINISTRATOR, null, true);

		$show_update	= false;
		$show_right		= false;
		$release		= str_replace('.', '-', $this->release);
		$lang_ver		= substr($lang->getTag(), 0, 2);
		if ($lang_ver != 'de')
		{
			$lang_ver = 'en';
			$forum	= "https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html";
		}
		else
		{
			$forum	= "https://www.boldt-webservice.de/de/forum/bwpostman.html";
		}

		$manual	= "https://www.boldt-webservice.de/$lang_ver/downloads/bwpostman/bwpostman-$lang_ver-$release.html";

		if ($update)
		{
			$string_special		= JText::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_SPECIAL_NOTE_DESC');
		}
		else
		{
			$string_special		= JText::_('COM_BWPOSTMAN_INSTALLATION_INSTALL_SPECIAL_NOTE_DESC');
		}

		$string_new			= JText::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_NEW_DESC');
		$string_improvement	= JText::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_IMPROVEMENT_DESC');
		$string_bugfix		= JText::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_BUGFIX_DESC');

		if (($string_bugfix != '' || $string_improvement != '' || $string_new != '') && $update)
		{
			$show_update	= true;
		}

		if ($show_update || $string_special != '')
		{
			$show_right	= true;
		}

		$asset_path = 'components/com_bwpostman/assets';
		?>

<link rel="stylesheet" href="<?php echo JRoute::_($asset_path . '/css/install.css'); ?>" type="text/css" />

<div id="com_bwp_install_header">
	<a href="https://www.boldt-webservice.de" target="_blank">
		<img border="0" align="center" src="<?php echo JRoute::_($asset_path . '/images/bw_header.png'); ?>" alt="Boldt Webservice" />
	</a>
</div>
<div class="top_line"></div>

<div id="com_bwp_install_outer">
	<h1><?php echo JText::_('COM_BWPOSTMAN_INSTALLATION_WELCOME') ?></h1>
	<div id="com_bwp_install_left">
		<div class="com_bwp_install_welcome">
			<p><?php echo JText::_('COM_BWPOSTMAN_DESCRIPTION') ?></p>
		</div>
		<div class="com_bwp_install_finished">
			<h2>
			<?php
			if($update)
			{
				echo JText::sprintf('COM_BWPOSTMAN_UPGRADE_SUCCESSFUL', $this->release);
				echo '<br />' . JText::_('COM_BWPOSTMAN_EXTENSION_UPGRADE_REMIND');
			}
			else
			{
				echo JText::sprintf('COM_BWPOSTMAN_INSTALLATION_SUCCESSFUL', $this->release);
			}
			?>
			</h2>
		</div>
		<?php
		if ($show_right)
		{ ?>
			<div class="cpanel">
				<div class="icon" >
					<a href="<?php echo JRoute::_('index.php?option=com_bwpostman'); ?>">
						<?php echo JHtml::_(
							'image',
							'administrator/components/com_bwpostman/assets/images/icon-48-bwpostman.png',
							JText::_('COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN')
						); ?>
						<span><?php echo JText::_('COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $manual; ?>" target="_blank">
						<?php echo JHtml::_(
							'image',
							'administrator/components/com_bwpostman/assets/images/icon-48-manual.png',
							JText::_('COM_BWPOSTMAN_INSTALL_MANUAL')
						); ?>
						<span><?php echo JText::_('COM_BWPOSTMAN_INSTALL_MANUAL'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $forum; ?>" target="_blank">
						<?php echo JHtml::_(
							'image',
							'administrator/components/com_bwpostman/assets/images/icon-48-forum.png',
							JText::_('COM_BWPOSTMAN_INSTALL_FORUM')
						); ?>
						<span><?php echo JText::_('COM_BWPOSTMAN_INSTALL_FORUM'); ?></span>
					</a>
				</div>
			</div>
		<?php
		} ?>
	</div>

	<div id="com_bwp_install_right">
		<?php
		if ($show_right)
		{
			if ($string_special != '')
			{ ?>
				<div class="com_bwp_install_specialnote">
					<h2><?php echo JText::_('COM_BWPOSTMAN_INSTALLATION_SPECIAL_NOTE_LBL') ?></h2>
					<p class="urgent"><?php echo $string_special; ?></p>
				</div>
			<?php
			}?>

			<?php
			if ($show_update)
			{ ?>
				<div class="com_bwp_install_updateinfo">
					<h2><?php echo JText::_('COM_BWPOSTMAN_INSTALLATION_UPDATEINFO') ?></h2>
					<?php echo JText::_('COM_BWPOSTMAN_INSTALLATION_CHANGELOG_INFO'); ?>
					<?php if ($string_new != '') { ?>
						<h3><?php echo JText::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_NEW_LBL') ?></h3>
						<p><?php echo $string_new; ?></p>
					<?php }?>
					<?php if ($string_improvement != '') { ?>
					<h3><?php echo JText::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_IMPROVEMENT_LBL') ?></h3>
						<p><?php echo $string_improvement; ?></p>
					<?php }?>
					<?php if ($string_bugfix != '') { ?>
						<h3><?php echo JText::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_BUGFIX_LBL') ?></h3>
						<p><?php echo $string_bugfix; ?></p>
					<?php }?>
				</div>
			<?php
			}
		}
		else
		{ ?>
			<div class="cpanel">
				<div class="icon" >
					<a href="<?php echo JRoute::_('index.php?option=com_bwpostman&token=' . JSession::getFormToken()); ?>">
						<?php echo JHtml::_(
							'image',
							'administrator/components/com_bwpostman/assets/images/icon-48-bwpostman.png',
							JText::_('COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN')
						); ?>
						<span><?php echo JText::_('COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $manual; ?>" target="_blank">
						<?php echo JHtml::_(
							'image',
							'administrator/components/com_bwpostman/assets/images/icon-48-bwpostman.png',
							JText::_('COM_BWPOSTMAN_INSTALL_MANUAL')
						); ?>
						<span><?php echo JText::_('COM_BWPOSTMAN_INSTALL_MANUAL'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $forum; ?>" target="_blank">
						<?php echo JHtml::_(
							'image',
							'administrator/components/com_bwpostman/assets/images/icon-48-bwpostman.png',
							JText::_('COM_BWPOSTMAN_INSTALL_FORUM')
						); ?>
						<span><?php echo JText::_('COM_BWPOSTMAN_INSTALL_FORUM'); ?></span>
					</a>
				</div>
			</div>
		<?php
		} ?>
	</div>
	<div class="clr"></div>

	<div class="com_bwp_install_footer">
		<p class="small">
			<?php echo JText::_('&copy; 2012-'); echo date(" Y")?> by <a href="https://www.boldt-webservice.de" target="_blank">Boldt Webservice</a>
		</p>
	</div>
</div>

	<?php
	}


	/**
	 * Method to install sample templates
	 *
	 * @param string    $sql
	 *
	 * @return  void
	 *
	 * @throws Exception
	 *
	 * @since
	 */

	private function installdata(&$sql)
	{
		$app	= JFactory::getApplication();
		$_db	= JFactory::getDbo();

		//we call sql file for the templates data
		$buffer = file_get_contents(JPATH_ADMINISTRATOR . '/components/com_bwpostman/sql/' . $sql);

		// Graceful exit and rollback if read not successful
		if ($buffer)
		{
			// Create an array of queries from the sql file
			//			jimport('joomla.installer.helper');
			$queries = JDatabaseDriver::splitSql($buffer);

			// No queries to process
			if (count($queries) != 0)
			{
				// Process each query in the $queries array (split out of sql file).
				foreach ($queries as $query)
				{
					$query = trim($query);
					if ($query != '' && $query{0} != '#')
					{
						$_db->setQuery($query);

						try
						{
							$_db->execute();
						}
						catch (RuntimeException $e)
						{
							$app->enqueueMessage(JText::_('COM_BWPOSTMAN_TEMPLATES_NOT_INSTALLED'), 'warning');
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
		$css_styles
			= "
			body	{
			font-family: Tahoma, Arial, Helvetica, Univers, sans-serif;
			font-size: 15px;
			background:#E9EDF0;
			padding:0px;
			margin:0px;
			padding-bottom:40px;
			color: #3F3F3F;
		}

		.outer	{
			margin: 0 auto;
		}
		
		.header	{
			padding: 10px auto;
			border-bottom: 5px solid #599DCA;
			text-align: center;
			width: 100%;
		}
		
		.logo	{
			max-width: 100%;
		}
		
		.content-outer	{
			max-width: 1000px;
			margin: 10px auto;
		}
		
		.content	{
			text-align: left;
			background: #E9EDF0;
			border-radius: 8px 8px 8px 8px;
			box-shadow: 1px 1px 3px 2px #599DCA;
			margin: 0 5px;
			padding: 0;
		}
		
		.content-inner	{
			padding: 20px 15px;
		}
		
		H1	{
			color: #fff;
			background: #599DCA;
			border-radius: 8px 8px 0 0;
			font-size: 16px;
			font-weight: bold;
			text-align: center;
			padding: 10px 0;
		}
		
		H2	{
			border-radius: 8px 8px 8px 8px;
			box-shadow: 1px 1px 3px 2px #599DCA;
			color: #3061AF;
			padding: 5px;
		}
		
		.footer-outer	{
			max-width: 1000px;
			margin: 10px auto;
		}
		
		.footer-inner	{
			margin: 0 5px;
		}
		";

		$params_default = array();
		$config	= JFactory::getConfig();

		$params_default['default_from_name']			= $config->get('fromname');
		$params_default['default_from_email']			= $config->get('mailfrom');
		$params_default['default_reply_email']			= $config->get('mailfrom');
		$params_default['default_mails_per_pageload']	= "100";
		$params_default['use_css_for_html_newsletter'] 	= "1";
		$params_default['css_for_html_newsletter']		= $css_styles;
		$params_default['newsletter_show_author']	 	= "1";
		$params_default['newsletter_show_createdate'] 	= "1";
		$params_default['show_name_field'] 				= "1";
		$params_default['show_firstname_field']			= "1";
		$params_default['name_field_obligation']		= "1";
		$params_default['firstname_field_obligation']	= "1";
		$params_default['show_emailformat']				= "1";
		$params_default['default_emailformat']			= "1";
		$params_default['disclaimer']					= "0";
		$params_default['disclaimer_link']				= "http://www.disclaimer.de/disclaimer.htm";
		$params_default['disclaimer_target']			= "0";
		$params_default['use_captcha']					= "0";
		$params_default['pretext']						= "";

		$params	= json_encode($params_default);

		$_db		= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->update($_db->quoteName('#__extensions'));
		$query->set($_db->quoteName('params') . " = " . $_db->quote($params));
		$query->where($_db->quoteName('element') . " = " . $_db->quote('com_bwpostman'));

		$_db->setQuery($query);

		try
		{
			$_db->execute();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
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
		$current_rules  = array();
		$_db		    = JFactory::getDbo();
		$query	        = $_db->getQuery(true);

		$query->select($_db->quoteName('rules'));
		$query->from($_db->quoteName('#__assets'));
		$query->where($_db->quoteName('name') . " = " . $_db->quote('com_bwpostman'));
		$_db->setQuery($query);

		try
		{
			$current_rules = json_decode($_db->loadResult(), true);
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		//detect missing component rules
		foreach ($default_rules as $key => $value)
		{
			if (is_array($current_rules) && !array_key_exists($key, $current_rules))
			{
				$current_rules[$key] = $value;
			}
		}

		$rules	= json_encode($current_rules);

		// update component rules in asset table
		$query	= $_db->getQuery(true);

		$query->update($_db->quoteName('#__assets'));
		$query->set($_db->quoteName('rules') . " = " . $_db->quote($rules));
		$query->where($_db->quoteName('name') . " = " . $_db->quote('com_bwpostman'));
		$_db->setQuery($query);

		try
		{
			$_db->execute();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
	}

	/**
	 *
	 * @return mixed
	 *
	 * @since version
	 */
	private function getRootAsset()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('rules'));
		$query->from($db->quoteName('#__assets'));
		$query->where($db->quoteName('name') . ' = ' . $db->Quote('root.1'));

		$db->setQuery($query);

		$rootRules = $db->loadResult();

		return $rootRules;
	}

	/**
	 * @param $newRootRules
	 *
	 *
	 * @since version
	 */
	private function saveRootAsset($newRootRules)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->update($db->quoteName('#__assets'));
		$query->set($db->quoteName('rules') . " = " . $db->Quote($newRootRules));
		$query->where($db->quoteName('name') . ' = ' . $db->Quote('root.1'));

		$db->setQuery($query);

		$db->execute();
	}

	/**
	 * Get the HTML-String for popup modal
	 *
	 * @return	string
	 *
	 * @since	2.2.0
	 */
	private function getModal()
	{
		$url = JUri::root() . 'administrator/index.php?option=com_bwpostman&view=maintenance&tmpl=component&layout=updateCheckSave';

		$html =	'
			<div id="bwp_Modal" class="bwp_modal">
				<div id="bwp_modal-content">
					<div id="bwp_modal-header"><span class="bwp_close" style="display:none;">&times;</span></div>
					<div id="bwp_wrapper"></div>
				</div>
			</div>
		';
		$css = "#bwpostman .bwp_modal{display:none;position:fixed;z-index:99999;padding-top:10px;left:0;top:0;width:100%;height:100%;overflow:auto;background-color:#000;background-color:rgba(0,0,0,0.4)}#bwpostman #bwp_modal-content{position:relative;background-color:#fefefe;margin:auto;border:1px solid #888;border-radius:6px;box-shadow:0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);height:100%;display:-ms-flexbox;display:flex;-ms-flex-direction:column;flex-direction:column;pointer-events:auto;outline:0;padding:15px}#bwpostman #bwp_modal-header{height:35px}#bwpostman #bwp_wrapper{position:relative;-ms-flex:1 1 auto;flex:1 1 auto}#bwpostman .bwp_close{color:#aaa;float:right;font-size:28px;font-weight:700;line-height:28px;-webkit-appearance:non}#bwpostman .bwp_close:hover,#bwpostman .bwp_close:focus{color:#000;text-decoration:none;cursor:pointer}";

		$js = "
			var css = '{$css}',
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
				modalcontent.style.height = viewportheight-(viewportheight*0.10)+'px';
				modalcontent.style.width = viewportwidth-(viewportwidth*0.10)+'px';

				// Get the modal
				var modal = document.getElementById('bwp_Modal');

				// Get the Iframe-Wrapper and set Iframe
				var wrapper = document.getElementById('bwp_wrapper');
				var html = '<iframe id=\"iFrame\" name=\"iFrame\" src=\"{$url}\" frameborder=\"0\" style=\"width:100%; height:100%;\"></iframe>';

				// Open the modal
					wrapper.innerHTML = html;
					modal.style.display = 'block';

			}
			setModal();

		";

		$modal = <<<EOS
		<div id="bwpostman">{$html}</div><script>{$js}</script>
EOS;
		return $modal;
	}
}
