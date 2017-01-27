<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman installer.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2017 Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/bwpostman.html
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
defined ('_JEXEC') or die ('Restricted access');

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
	var $parentInstaller;

	/**
	 * @var string $minimum_joomla_release
	 *
	 * @since       2.0.0
	 */
	var $minimum_joomla_release;

	/**
	 * @var string release
	 *
	 * @since       2.0.0
	 */
	var $release = null;

	/**
	 * @var string  $reference_table        reference table to check if it is converted already
	 *
	 * @since       2.0.0
	 */
	var $reference_table = 'bwpostman_campaigns';

	/**
	 * @var string  $conversion_file        file name of sql conversion file
	 *
	 * @since       2.0.0
	 */
	var $conversion_file = '/components/com_bwpostman/sql/utf8mb4conversion/utf8mb4-conversion-01.sql';

	/**
	 * @var array $all_bwpm_groups          array which holds user groups of BwPostman
	 *
	 * @since       2.0.0
	 */
	var $all_bwpm_groups    = array('bwpm_usergroups'           => array('BwPostmanManager', 'BwPostmanPublisher', 'BwPostmanEditor'),
	                                'mailinglist_usergroups'    => array('BwPostmanMailinglistAdmin', 'BwPostmanMailinglistPublisher', 'BwPostmanMailinglistEditor'),
	                                'subscriber_usergroups'     => array('BwPostmanSubscriberAdmin', 'BwPostmanSubscriberPublisher', 'BwPostmanSubscriberEditor'),
	                                'newsletter_usergroups'     => array('BwPostmanNewsletterAdmin', 'BwPostmanNewsletterPublisher', 'BwPostmanNewsletterEditor'),
	                                'campaign_usergroups'       => array('BwPostmanCampaignAdmin', 'BwPostmanCampaignPublisher', 'BwPostmanCampaignEditor'),
	                                'template_usergroups'       => array('BwPostmanTemplateAdmin', 'BwPostmanTemplatePublisher', 'BwPostmanTemplateEditor'),
									);


	/**
	 * Constructor
	 *
	 * @since       2.0.0
	 */
		public function __constructor()
	 {
		$this->reference_table  = 'bwpostman_mailinglists';
		$this->conversion_file  = '/components/com_bwpostman/sql/utf8mb4conversion/utf8mb4-conversion-01.sql';
		$this->all_bwpm_groups  = array('bwpm_usergroups'           => array('BwPostmanManager', 'BwPostmanPublisher', 'BwPostmanEditor'),
		                                'mailinglist_usergroups'    => array('BwPostmanMailinglistAdmin', 'BwPostmanMailinglistPublisher', 'BwPostmanMailinglistEditor'),
		                                'subscriber_usergroups'     => array('BwPostmanSubscriberAdmin', 'BwPostmanSubscriberPublisher', 'BwPostmanSubscriberEditor'),
		                                'newsletter_usergroups'     => array('BwPostmanNewsletterAdmin', 'BwPostmanNewsletterPublisher', 'BwPostmanNewsletterEditor'),
		                                'campaign_usergroups'       => array('BwPostmanCampaignAdmin', 'BwPostmanCampaignPublisher', 'BwPostmanCampaignEditor'),
		                                'template_usergroups'       => array('BwPostmanTemplateAdmin', 'BwPostmanTemplatePublisher', 'BwPostmanTemplateEditor'),
								);
	 }

	/**
	 * Executes additional installation processes
	 *
	 * @since       0.9.6.3
	 */
	private function _bwpostman_install()
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
	 * @param   string  			$type		Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance	$parent		The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 *
	 * @since       0.9.6.3
	 */

	public function preflight($type, JAdapterInstance $parent)
	{
		$app 		= JFactory::getApplication ();
		$session	= JFactory::getSession();
		$jversion	= new JVersion();

		$this->parentInstaller	= $parent->getParent();
		// Get component manifest file version
		$this->release	= $parent->get("manifest")->version;
		$session->set('release', $this->release->__toString(), 'bwpostman');

		// Manifest file minimum Joomla version
		$this->minimum_joomla_release = $parent->get("manifest")->attributes()->version;

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

			if (version_compare( $this->release, $oldRelease, 'lt'))
			{
				$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_INSTALL_ERROR_INCORRECT_VERSION_SEQUENCE', $oldRelease, $this->release), 'error');
				return false;
			}

			// delete existing files in frontend and backend to prevent conflicts with previous relicts
			jimport('joomla.filesystem.folder');
			$admin_path	= JPATH_ADMINISTRATOR . '/components/com_bwpostman';
			$site_path	= JPATH_SITE . '/components/com_bwpostman';

			if (JFolder::exists($admin_path) === true)
			{
				JFolder::delete($admin_path);
			}

			if (JFolder::exists($site_path) === true)
			{
				JFolder::delete($site_path);
			}
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


		// Check if utf8mb4 is supported; if so, copy utf8mb4 file as sql installation file
		jimport('joomla.filesystem.file');
		$tmp_path   = $this->parentInstaller->getPath('source');
		require_once ($tmp_path.'/admin/helpers/installhelper.php');

		$name = $_db->getName();
		if (BwPostmanInstallHelper::serverClaimsUtf8mb4Support($name))
		{
			copy($tmp_path . '/admin/sql/utf8mb4conversion/utf8mb4-install.sql', $tmp_path . '/admin/sql/install.sql');
		}
		return true;
	}


	/**
	 * Called after any type of action
	 *
	 * @param   string  			$type		Which action is happening (install|uninstall|discover_install)
	 * @param   JAdapterInstance	$parent		The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 *
	 * @since       0.9.6.3
	 */

	public function postflight($type, JAdapterInstance $parent)
	{
		$m_params   = JComponentHelper::getParams('com_media');
		$this->_copyTemplateImagesToMedia($m_params);

		// make new folder and copy template thumbnails to folder "images" if image_path is not "images"
		if ($m_params->get('image_path', 'images') != 'images')
		{
			$this->_copyTemplateImagesToImages();
		}

		if ($type == 'install')
		{
			// Set BwPostman default settings in the extensions table at install
			$this->_setDefaultParams();

			// create sample user groups and access levels
			//@ToDo: deactivated for testing, activate for release
//			$this->_createSampleUsergroups();
		}

		// check if sample templates exits
		$this->_checkSampleTemplates();

		// update/complete component rules
		$this->_updateRules();

		if ($type == 'update')
		{
			$app 		= JFactory::getApplication ();
			$oldRelease	= $app->getUserState('com_bwpostman.update.oldRelease', '');

			if (version_compare($oldRelease, '1.0.1', 'lt'))
				$this->_adjustMLAccess();

			if (version_compare($oldRelease, '1.2.0', 'lt'))
				$this->_correctCamId();
			if (version_compare($oldRelease, '1.2.0', 'lt'))
				$this->_fillCamCrossTable();

			// @ToDo: Reflect, how to reinstall sample groups, if user deleted them and wants them back
			if (version_compare($oldRelease, '2.0.0', 'lt'))
				$this->_createSampleUsergroups();

			// convert tables to UTF8MB4
			jimport('joomla.filesystem.file');
			$tmp_path   = $this->parentInstaller->getPath('source');
			require_once ($tmp_path.'/admin/helpers/installhelper.php');
			BwPostmanInstallHelper::convertToUtf8mb4($this->reference_table, JPATH_ADMINISTRATOR . $this->conversion_file);

			// remove double entries in table extensions
			$this->_removeDoubleExtensionsEntries();

			// check all tables of BwPostman
			// Let Ajax client redirect
			$modal =	'<script type="text/javascript">'."\n".
						'	var w = 700, h = 600;'."\n".
						'	if (window.outerWidth) { w = window.outerWidth * 80 / 100;}'."\n".
						'	if (window.outerHeight) { h = window.outerHeight * 80 / 100;}'."\n".
						'	window.open("' . JUri::root() . 'administrator/index.php?option=com_bwpostman&view=maintenance&layout=updateCheckSave", "popup", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no, width="+Math.round(w)+", height="+Math.round(h)+"");'."\n".
						'</script>';
			$app->enqueueMessage(JText::_('Installing BwPostman ... ').$modal);
		}
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
		$this->_bwpostman_install();
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
		$this->_bwpostman_install();
		$this->showFinished(true);
	}


	/**
	 * Called on un-installation
	 *
	 * @return  void
	 *
	 * @since       0.9.6.3
	 */

	public function uninstall()
	{

		//@ToDo: deactivated for testing, activate for release
//		$this->_deleteSampleUsergroups();

		JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_UNINSTALL_THANKYOU'), 'message');
		//  notice that folder image/bw_postman is not removed
		$m_params   = JComponentHelper::getParams('com_media');
		$image_path = $m_params->get('image_path', 'images');

		JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_UNINSTALL_FOLDER_BWPOSTMAN', $image_path), 'notice');

		$_db		= JFactory::getDbo();
		$query  = $_db->getQuery(true);
		$query->delete($_db->quoteName('#__postinstall_messages'));
		$query->where($_db->quoteName('language_extension').' = '.$_db->quote('com_bwpostman'));
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
	 * @return  array  $manifest
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
	 * @since
	 */
	private function _correctCamId()
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
	 * @since
	 */
	private function _fillCamCrossTable()
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

				$query->select('DISTINCT(' . $_db->quoteName('cross1')  . '.' . $_db->quoteName('mailinglist_id') . ')');
				$query->from($_db->quoteName('#__bwpostman_newsletters_mailinglists') . ' AS ' . $_db->quoteName('cross1'));
				$query->leftJoin('#__bwpostman_newsletters AS n ON cross1.newsletter_id = n.id');
				$query->where($_db->quoteName('n')  . '.' . $_db->quoteName('campaign_id') . ' = ' . $cam['campaign_id']);
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
						$query->columns(array(
							$_db->quoteName('campaign_id'),
							$_db->quoteName('mailinglist_id')
							));
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
	 * @since	1.0.1
	 */
	private function _adjustMLAccess()
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
	private function _copyTemplateImagesToMedia($m_params)
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
	private function _copyTemplateImagesToImages()
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
	 * @since   2.0.0
	 */
	private function _checkSampleTemplates()
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
			$this->_installdata($templatessql);
		}

		$templatestplsql = 'bwp_templatestpl.sql';
		if (!isset($templatetplFields))
		{
			$this->_installdata($templatestplsql);
		}
	}

	/**
	 * Method to create sample user groups and access levels
	 *
	 * @return boolean  true on success
	 *
	 * @throws Exception
	 * @throws BwException
	 *
	 * @since   2.0.0
	 */
	private function _createSampleUsergroups()
	{
		try
		{
			// get the model for user groups
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_users/models');
			$groupModel = JModelLegacy::getInstance('Group', 'UsersModel');

			// get group ID of manager
			$manager_id = $this->_getGroupId('Manager');

			// Create user group BwPostmanAdmin
			if (!$ret = $groupModel->save(array('id' => 0, 'parent_id' => $manager_id, 'title' => 'BwPostmanAdmin')))
			{
				echo JText::sprintf('COM_BWPOSTMAN_INSTALLATION_ERROR_CREATING_USERGROUPS: %s', $ret);
				throw new Exception(JText::sprintf('COM_BWPOSTMAN_INSTALLATION_ERROR_CREATING_USERGROUPS: %s', $ret));
			}
			$admin_groupId = $this->_getGroupId('BwPostmanAdmin');

			// Create user group BwPostmanSectionAdmin
			if (!$groupModel->save(array('id' => 0, 'parent_id' => $admin_groupId, 'title' => 'BwPostmanSectionAdmin')))
			{
				throw new Exception(JText::_('COM_BWPOSTMAN_INSTALLATION_ERROR_CREATING_USERGROUPS'));
			}

			// Create BwPostman user groups section-wise
			foreach ($this->all_bwpm_groups as $groups)
			{
				$parent_id  = $admin_groupId;
				foreach ($groups as $item)
				{
					if (!$groupModel->save(array('id' => 0, 'parent_id' => $parent_id, 'title' => $item)))
					{
						throw new Exception(JText::_('COM_BWPOSTMAN_INSTALLATION_ERROR_CREATING_USERGROUPS'));
					}
					$parent_id = $this->_getGroupId($item);
				}
			}
			return true;
		}
		catch (BwException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
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
	 * @since   2.0.0
	 */
	private function _deleteSampleUsergroups()
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
			}
			catch (RuntimeException $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}

			// get group ids of BwPostman user groups, where actual user is member
			$member_ids = '';
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

			// delete actual user from BwPostman user groups
			foreach ($member_ids as $item)
			{
				JUserHelper::removeUserFromGroup($user_id, $item);
			}
			JAccess::clearStatics();

			// get the model for user groups
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_users/models');
			$groupModel = JModelLegacy::getInstance('Group', 'UsersModel');

			// delete main user group of BwPostman (all other user groups of BwPostman will be deleted automatically by Joomla)
			if (!$groupModel->delete($bwpostman_main_group))
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
	 * Gets the group Id of the selected group name
	 *
	 * @param   string  $name  The name of the group
	 *
	 * @return  int  the ID of the group
	 *
	 * @since
	 */

	private function _getGroupId($name)
	{
		$result = 0;
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		$query->select($_db->quoteName('id'));
		$query->from($_db->quoteName('#__usergroups'));
		$query->where("`title` LIKE '". $_db->escape($name)."'");

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
	 * @since   2.0.0
	 */
	private function _removeDoubleExtensionsEntries()
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
	 * sets parameter values in the component's row of the extension table
	 *
	 * @param array     $param_array
	 *
	 * @return  void
	 *
	 * @since
	 */
	private function setParams($param_array)
	{
		if ( count($param_array) > 0 )
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
			foreach ( $param_array as $name => $value ) {
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
		$lang->load('com_bwpostman.sys',JPATH_ADMINISTRATOR,'en_GB',true);
		$lang->load('com_bwpostman',JPATH_ADMINISTRATOR,'en_GB',true);

		//load specific language
		$lang->load('com_bwpostman.sys',JPATH_ADMINISTRATOR,null,true);
		$lang->load('com_bwpostman',JPATH_ADMINISTRATOR,null,true);

		$show_update	= false;
		$show_right		= false;
		$release		= str_replace('.', '-', $this->release);
		$lang_ver		= substr($lang->getTag(), 0, 2);
		if ($lang_ver != 'de')
		{
			$lang_ver = 'en';
			$forum	= "https://www.boldt-webservice.de/en/forum-en/bwpostman.html";
		}
		else
		{
			$forum	= "http://www.boldt-webservice.de/de/forum/bwpostman.html";
		}
		$manual	= "http://www.boldt-webservice.de/$lang_ver/downloads/bwpostman/bwpostman-$lang_ver-$release.html";

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
		?>

<link rel="stylesheet" href="components/com_bwpostman/assets/css/install.css" type="text/css" />

<div id="com_bwp_install_header">
	<a href="http://www.boldt-webservice.de" target="_blank">
		<img border="0" align="center" src="components/com_bwpostman/assets/images/bw_header.png" alt="Boldt Webservice" />
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
			if($update){
				echo JText::sprintf('COM_BWPOSTMAN_UPGRADE_SUCCESSFUL', $this->release);
				echo '<br />'.JText::_('COM_BWPOSTMAN_EXTENSION_UPGRADE_REMIND');
			} else {
				echo JText::sprintf('COM_BWPOSTMAN_INSTALLATION_SUCCESSFUL', $this->release);
			}
			?>
			</h2>
		</div>
		<?php if ($show_right) { ?>
			<div class="cpanel">
				<div class="icon" >
					<a href="<?php echo JRoute::_('index.php?option=com_bwpostman'); ?>"> <?php echo JHtml::_('image', 'administrator/components/com_bwpostman/assets/images/icon-48-bwpostman.png', JText::_('COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN')); ?>
						<span><?php echo JText::_('COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $manual; ?>" target="_blank">
						<?php echo JHtml::_('image', 'administrator/components/com_bwpostman/assets/images/icon-48-manual.png', JText::_('COM_BWPOSTMAN_INSTALL_MANUAL')); ?>
						<span><?php echo JText::_('COM_BWPOSTMAN_INSTALL_MANUAL'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $forum; ?>" target="_blank">
						<?php echo JHtml::_('image', 'administrator/components/com_bwpostman/assets/images/icon-48-forum.png', JText::_('COM_BWPOSTMAN_INSTALL_FORUM')); ?>
						<span><?php echo JText::_('COM_BWPOSTMAN_INSTALL_FORUM'); ?></span>
					</a>
				</div>
			</div>
		<?php }?>
	</div>

	<div id="com_bwp_install_right">
		<?php if ($show_right)
		{ ?>
			<?php if ($string_special != '')
			{ ?>
				<div class="com_bwp_install_specialnote">
					<h2><?php echo JText::_('COM_BWPOSTMAN_INSTALLATION_SPECIAL_NOTE_LBL') ?></h2>
					<p class="urgent"><?php echo $string_special; ?></p>
				</div>
			<?php
			}?>

			<?php if ($show_update)
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
			}?>
		<?php
		}
		else
		{ ?>
			<div class="cpanel">
				<div class="icon" >
					<a href="<?php echo JRoute::_('index.php?option=com_bwpostman&token='.JSession::getFormToken()); ?>"> <?php echo JHtml::_('image', 'administrator/components/com_bwpostman/assets/images/icon-48-bwpostman.png', JText::_('COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN')); ?>
						<span><?php echo JText::_('COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $manual; ?>" target="_blank">
						<?php echo JHtml::_('image', 'administrator/components/com_bwpostman/assets/images/icon-48-bwpostman.png', JText::_('COM_BWPOSTMAN_INSTALL_MANUAL')); ?>
						<span><?php echo JText::_('COM_BWPOSTMAN_INSTALL_MANUAL'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $forum; ?>" target="_blank">
						<?php echo JHtml::_('image', 'administrator/components/com_bwpostman/assets/images/icon-48-bwpostman.png', JText::_('COM_BWPOSTMAN_INSTALL_FORUM')); ?>
						<span><?php echo JText::_('COM_BWPOSTMAN_INSTALL_FORUM'); ?></span>
					</a>
				</div>
			</div>
		<?php
		} ?>
	</div>
	<div class="clr"></div>

	<div class="com_bwp_install_footer">
		<p class="small"><?php echo JText::_('&copy; 2012-'); echo date (" Y")?> by <a href="http://www.boldt-webservice.de" target="_blank">Boldt Webservice</a></p>
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
	 * @since
	 */

	private function _installdata(&$sql)
	{
		$app	= JFactory::getApplication ();
		$_db		= JFactory::getDbo();

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
	 * @return  string	Json-encoded default values for params
	 *
	 * @since
	 */

	private function _setDefaultParams()
	{
		$css_styles =
"
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

		$params_default =  array();
		$config	= JFactory::getConfig();

		$params_default['default_from_name']			=  $config->get('fromname');
		$params_default['default_from_email']			=  $config->get('mailfrom');
		$params_default['default_reply_email']			=  $config->get('mailfrom');
		$params_default['default_mails_per_pageload']	=  "100";
		$params_default['use_css_for_html_newsletter'] 	=  "1";
		$params_default['css_for_html_newsletter']		=  $css_styles;
		$params_default['newsletter_show_author']	 	=  "1";
		$params_default['newsletter_show_createdate'] 	=  "1";
		$params_default['show_name_field'] 				=  "1";
		$params_default['show_firstname_field']			=  "1";
		$params_default['name_field_obligation']		=  "1";
		$params_default['firstname_field_obligation']	=  "1";
		$params_default['show_emailformat']				=  "1";
		$params_default['default_emailformat']			=  "1";
		$params_default['disclaimer']					=  "0";
		$params_default['disclaimer_link']				=  "http://www.disclaimer.de/disclaimer.htm";
		$params_default['disclaimer_target']			=  "0";
		$params_default['use_captcha']					=  "0";
		$params_default['pretext']						=  "";

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
	 * @since	1.2.0
	 */
	private function _updateRules()
	{
		$default_rules	=	array(
								"core.admin" => array('7' => 1),
								"core.manage" => array('7' => 1, '6' => 1),
								"bwpm.create" => array('7' => 1, '6' => 1),
								"bwpm.edit" => array('7' => 1, '6' => 1),
								"bwpm.edit.own" => array('7' => 1, '6' => 1),
								"bwpm.edit.state" => array('7' => 1, '6' => 1),
								"bwpm.archive" => array('7' => 1, '6' => 1),
								"bwpm.restore" => array('7' => 1, '6' => 1),
								"bwpm.delete" => array('7' => 1, '6' => 1),
								"bwpm.send" =>array('7' => 1, '6' => 1),
								"bwpm.view.newsletters" => array('7' => 1, '6' => 1),
								"bwpm.view.subscribers" => array('7' => 1, '6' => 1),
								"bwpm.view.campaigns" => array('7' => 1, '6' => 1),
								"bwpm.view.mailinglists" => array('7' => 1, '6' => 1),
								"bwpm.view.templates" => array('7' => 1, '6' => 1),
								"bwpm.view.archive" => array('7' => 1, '6' => 1),
								"bwpm.view.manage" => array('7' => 1, '6' => 1),
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
}
