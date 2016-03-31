<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman installer.
 *
 * @version 2.0.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2016 Boldt Webservice <forum@boldt-webservice.de>
 * @support http://www.boldt-webservice.de/forum/bwpostman.html
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

use Joomla\Registry\Format\Json;

// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die ('Restricted access');

/**
 * Class Com_BwPostmanInstallerScript
 */
class Com_BwPostmanInstallerScript
{
	/** @var int asset_id */
	var $release = null;

	/**
	 * Constructor
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */
	/*	public function __constructor(JAdapterInstance $adapter)
	 {

	}
	*/

	/**
	 * Executes additional installation processes
	 */
	private function _bwpostman_install()
	{
/*
		$db = JFactory::getDbo();
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
	 * @param   string  			$type		Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance	$parent		The object responsible for running this script
	 *
	 * @return  boolean  True on success
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
		if(version_compare($jversion->getShortVersion(), $this->minimum_joomla_release, 'lt')) {
			$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_INSTALL_ERROR_JVERSION', $this->minimum_joomla_release), 'error');
			return false;
		}

		if(floatval(phpversion()) < 5)
		{
			$app->enqueueMessage(JText::_('COM_BWPOSTMAN_USES_PHP5'), 'error');
			return false;
		}

		// abort if the component being installed is not newer than the currently installed version
		if ($type == 'update') {
			$oldRelease = $this->getManifestVar('version');
			$app->setUserState('com_bwpostman.update.oldRelease', $oldRelease);

			if (version_compare( $this->release, $oldRelease, 'lt')) {
				$app->enqueueMessage(JText::sprintf('COM_BWPOSTMAN_INSTALL_ERROR_INCORRECT_VERSION_SEQUENCE', $oldRelease, $this->release), 'error');
				return false;
			}

			// delete existing files in frontend and backend to prevent conflicts with previous relicts
			jimport('joomla.filesystem.folder');
			$admin_path	= JPATH_ADMINISTRATOR . '/components/com_bwpostman';
			$site_path	= JPATH_SITE . '/components/com_bwpostman';

			if (JFolder::exists($admin_path) === true) {
				JFolder::delete($admin_path);
			}

			if (JFolder::exists($site_path) === true) {
				JFolder::delete($site_path);
			}
		}

		$db	= JFactory::getDBO();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('params'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element') . " = " . $db->quote('com_bwpostman'));

		$db->SetQuery($query);
		$params_default = $db->loadResult();
		$app->setUserState('com_bwpostman.install.params', $params_default);
		return true;
	}


	/**
	 * Called after any type of action
	 *
	 * @param   string  			$type		Which action is happening (install|uninstall|discover_install)
	 * @param   JAdapterInstance	$parent		The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */

	public function postflight($type, JAdapterInstance $parent)
	{
		$db	= JFactory::getDBO();


		// make new folder and copy template thumbnails
		$dest = JPATH_ROOT.'/images/bw_postman';
		if (!JFolder::exists($dest)) JFolder::create(JPATH_ROOT.'/images/bw_postman');
		if (!JFile::exists(JPATH_ROOT.'/images/bw_postman/index.html')) JFile::copy(JPATH_ROOT.'/images/index.html', JPATH_ROOT.'/images/bw_postman/index.html');
		if (!JFile::exists(JPATH_ROOT.'/images/bw_postman/deep_blue.png')) JFile::copy(JPATH_ROOT.'/media/bw_postman/images/deep_blue.png', JPATH_ROOT.'/images/bw_postman/deep_blue.png');
		if (!JFile::exists(JPATH_ROOT.'/images/bw_postman/soft_blue.png'))JFile::copy(JPATH_ROOT.'/media/bw_postman/images/soft_blue.png', JPATH_ROOT.'/images/bw_postman/soft_blue.png');
		if (!JFile::exists(JPATH_ROOT.'/images/bw_postman/creme.png')) JFile::copy(JPATH_ROOT.'/media/bw_postman/images/creme.png', JPATH_ROOT.'/images/bw_postman/creme.png');
		if (!JFile::exists(JPATH_ROOT.'/images/bw_postman/sample_html.png')) JFile::copy(JPATH_ROOT.'/media/bw_postman/images/sample_html.png', JPATH_ROOT.'/images/bw_postman/sample_html.png');
		if (!JFile::exists(JPATH_ROOT.'/images/bw_postman/text_template_1.png')) JFile::copy(JPATH_ROOT.'/media/bw_postman/images/text_template_1.png', JPATH_ROOT.'/images/bw_postman/text_template_1.png');
		if (!JFile::exists(JPATH_ROOT.'/images/bw_postman/text_template_2.png')) JFile::copy(JPATH_ROOT.'/media/bw_postman/images/text_template_2.png', JPATH_ROOT.'/images/bw_postman/text_template_2.png');
		if (!JFile::exists(JPATH_ROOT.'/images/bw_postman/text_template_3.png')) JFile::copy(JPATH_ROOT.'/media/bw_postman/images/text_template_3.png', JPATH_ROOT.'/images/bw_postman/text_template_3.png');
		if (!JFile::exists(JPATH_ROOT.'/images/bw_postman/sample_text.png')) JFile::copy(JPATH_ROOT.'/media/bw_postman/images/sample_text.png', JPATH_ROOT.'/images/bw_postman/sample_text.png');
		if (!JFile::exists(JPATH_ROOT.'/images/bw_postman/joomla_black.gif')) JFile::copy(JPATH_ROOT.'/media/bw_postman/images/joomla_black.gif', JPATH_ROOT.'/images/bw_postman/joomla_black.gif');

		if ($type == 'install') {
			// Set BwPostman default settings in the extensions table at install
			$this->_setDefaultParams();
		}

		// check if sample templates exits
		$q					= "SELECT `id` FROM `#__bwpostman_templates`";
		$db->setQuery($q);

		$templateFields		= $db->loadResult();

		$q					= "SELECT `id` FROM `#__bwpostman_templates_tpl`";
		$db->setQuery($q);

		$templatetplFields	= $db->loadResult();

		// if not install sampledata
		$templatessql		= 'bwp_templates.sql';
		if(!isset($templateFields)) $this->_installdata($templatessql);

		$templatestplsql	= 'bwp_templatestpl.sql';
		if(!isset($templatetplFields)) $this->_installdata($templatestplsql);

		// update/complete component rules
		$this->_updateRules($type);


		if ($type == 'update') {
//			require_once (JPATH_ADMINISTRATOR.'/components/com_bwpostman/helpers/tablehelper.php');

			$app 		= JFactory::getApplication ();
			$oldRelease	= $app->getUserState('com_bwpostman.update.oldRelease', '');

			if (version_compare($oldRelease, '1.0.1', 'lt')) $this->_adjustMLAccess();

			if (version_compare($oldRelease, '1.2.0', 'lt')) $this->_correctCamId();
			if (version_compare($oldRelease, '1.2.0', 'lt')) $this->_fillCamCrossTable();

			// remove double entries in table extensions
			$query	= $db->getQuery(true);
			$query->select($db->quoteName('extension_id'));
			$query->from($db->quoteName('#__extensions'));
			$query->where($db->quoteName('element') . ' = ' . $db->Quote('com_bwpostman'));
			$query->where($db->quoteName('client_id') . ' = ' . $db->Quote('0'));

			$db->setQuery($query);
			$result	= $db->loadResult();

			if ($result) {
				$query	= $db->getQuery(true);
				$query->delete($db->quoteName('#__extensions'));
				$query->where($db->quoteName('extension_id') . ' =  ' . $db->Quote($result));

				$db->setQuery($query);
				$db->execute();
			}

			// check all tables of BwPostman
			// Let Ajax client redirect
			echo '<script type="text/javascript">';
			echo '   var w = 700, h = 600;';
			echo '    if (window.outerWidth) { w = window.outerWidth * 80 / 100;}';
			echo '    if (window.outerHeight) { h = window.outerHeight * 80 / 100;}';
			echo 'window.open("' . JUri::root() . 'administrator/index.php?option=com_bwpostman&view=maintenance&layout=updateCheckSave", "popup", "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no, width="+Math.round(w)+", height="+Math.round(h)+"");';
			echo '</script>';
		}
	}


	/**
	 * Called on installation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */

	public function install(JAdapterInstance $adapter)
	{
		$session	= JFactory::getSession();
		$session->set('update', false, 'bwpostman');
		$this->_bwpostman_install();
		$this->showFinished(false);
	}


	/**
	 * Called on update
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */

	public function update(JAdapterInstance $adapter)
	{
		$session	= JFactory::getSession();
		$session->set('update', true, 'bwpostman');
		$this->_bwpostman_install();
		$this->showFinished(true);
	}


	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 */

	public function uninstall(JAdapterInstance $adapter)
	{
//		echo "<div>BwPostman is now removed from your system.</div>";
		JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_UNINSTALL_THANKYOU'), 'message');
		//  notice that folder image/bw_postman is not removed
		JFactory::getApplication()->enqueueMessage(JText::_('COM_BWPOSTMAN_UNINSTALL_FOLDER_BWPOSTMAN'), 'notice');
		$db		= JFactory::getDbo();
		$query	= 'DELETE FROM '.$db->quoteName('#__postinstall_messages').
		' WHERE '. $db->quoteName('language_extension').' = '.$db->quote('com_bwpostman');
		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * get a variable from the manifest file (actually, from the manifest cache).
	 *
	 * @param   string  $name
	 */
	private function getManifestVar($name) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('manifest_cache'));
		$query->from($db->quoteName('#__extensions'));
		$query->where($db->quoteName('element') . " = " . $db->quote('com_bwpostman'));
		$db->SetQuery($query);

		$manifest = json_decode($db->loadResult(), true);
		return $manifest[$name];
	}


	/**
	 * Correct campaign_id in newsletters because of an error previous version
	 */
	private function _correctCamId() {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->update($db->quoteName('#__bwpostman_newsletters'));
		$query->set($db->quoteName('campaign_id') . " = " . (int) -1);
		$query->where($db->quoteName('campaign_id') . " = " . (int) 0);
		$db->SetQuery($query);

		$db->execute();

		return true;
	}

	/**
	 * Fill cross table campaigns mailinglists with values from all newsletters of the specifix campaign
	 */
	private function _fillCamCrossTable() {
		$_db	= JFactory::getDbo();
		$query	= $_db->getQuery(true);

		// First get all campaigns
		$query->select($_db->quoteName('id') . ' AS ' . $_db->quoteName('campaign_id'));
		$query->from($_db->quoteName('#__bwpostman_campaigns'));
		$_db->setQuery($query);

		$all_cams	= $_db->loadAssocList();

		if (count($all_cams) > 0) {
			foreach ($all_cams as $cam) {
				$query			= $_db->getQuery(true);

				$query->select('DISTINCT(' . $_db->quoteName('cross1')  . '.' . $_db->quoteName('mailinglist_id') . ')');
				$query->from($_db->quoteName('#__bwpostman_newsletters_mailinglists') . ' AS ' . $_db->quoteName('cross1'));
				$query->leftJoin('#__bwpostman_newsletters AS n ON cross1.newsletter_id = n.id');
				$query->where($_db->quoteName('n')  . '.' . $_db->quoteName('campaign_id') . ' = ' . $cam['campaign_id']);
				$_db->setQuery($query);

				$cross_values	= $_db->loadAssocList();

				if (count($cross_values) > 0) {
					foreach ($cross_values as $item) {
						$query	= $_db->getQuery(true);

						$query->insert($_db->quoteName('#__bwpostman_campaigns_mailinglists'));
						$query->columns(array(
							$_db->quoteName('campaign_id'),
							$_db->quoteName('mailinglist_id')
							));
							$query->values(
							$_db->Quote($cam['campaign_id']) . ',' .
							$_db->Quote($item['mailinglist_id'])
							);
						$_db->setQuery($query);

						$_db->execute();
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
		$_db->execute();

		return;
	}

	/**
	 * sets parameter values in the component's row of the extension table
	 *
	 * @param array     $param_array
	 */
	private function setParams($param_array) {
		if ( count($param_array) > 0 ) {
			// read the existing component value(s)
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);

			$query->select($db->quoteName('params'));
			$query->from($db->quoteName('#__extensions'));
			$query->where($db->quoteName('element') . " = " . $db->quote('com_bwpostman'));
			$db->SetQuery($query);
			$params = json_decode($db->loadResult(), true);
			// add the new variable(s) to the existing one(s)
			foreach ( $param_array as $name => $value ) {
				$params[(string) $name] = (string) $value;
			}
			// store the combined new and existing values back as a JSON string
			$paramsString = json_encode($params);
			$query	= $db->getQuery(true);

			$query->update($db->quoteName('#__extensions'));
			$query->set($db->quoteName('params') . " = " . $db->quote($paramsString));
			$query->where($db->quoteName('element') . " = " . $db->quote('com_bwpostman'));
			$db->SetQuery($query);

			$db->execute();
		}
	}

	/**
	 * shows the HTML after installation/update
	 *
	 * @param   boolean $update
	 */
	public function showFinished($update){

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
		if ($lang_ver != 'de') {
			$lang_ver = 'en';
			$forum	= "http://www.boldt-webservice.de/en/forum-en/bwpostman.html";
		}
		else {
			$forum	= "http://www.boldt-webservice.de/de/forum/bwpostman.html";
		}
		$manual	= "http://www.boldt-webservice.de/$lang_ver/downloads/bwpostman/bwpostman-$lang_ver-$release.html";

		if ($update) {
			$string_special		= JText::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_SPECIAL_NOTE_DESC');
		}
		else {
			$string_special		= JText::_('COM_BWPOSTMAN_INSTALLATION_INSTALL_SPECIAL_NOTE_DESC');
		}
		$string_new			= JText::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_NEW_DESC');
		$string_improvement	= JText::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_IMPROVEMENT_DESC');
		$string_bugfix		= JText::_('COM_BWPOSTMAN_INSTALLATION_UPDATE_BUGFIX_DESC');

		if (($string_bugfix != '' || $string_improvement != '' || $string_new != '') && $update) {
			$show_update	= true;
		}
		if ($show_update || $string_special != '') {
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
					<a href="<?php echo JROUTE::_('index.php?option=com_bwpostman'); ?>"> <?php echo JHTML::_('image', 'administrator/components/com_bwpostman/assets/images/icon-48-bwpostman.png', JText::_('COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN')); ?>
						<span><?php echo JText::_('COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $manual; ?>" target="_blank">
						<?php echo JHTML::_('image', 'administrator/components/com_bwpostman/assets/images/icon-48-manual.png', JText::_('COM_BWPOSTMAN_INSTALL_MANUAL')); ?>
						<span><?php echo JText::_('COM_BWPOSTMAN_INSTALL_MANUAL'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $forum; ?>" target="_blank">
						<?php echo JHTML::_('image', 'administrator/components/com_bwpostman/assets/images/icon-48-forum.png', JText::_('COM_BWPOSTMAN_INSTALL_FORUM')); ?>
						<span><?php echo JText::_('COM_BWPOSTMAN_INSTALL_FORUM'); ?></span>
					</a>
				</div>
			</div>
		<?php }?>
	</div>

	<div id="com_bwp_install_right">
		<?php if ($show_right) { ?>
			<?php if ($string_special != '') { ?>
				<div class="com_bwp_install_specialnote">
					<h2><?php echo JText::_('COM_BWPOSTMAN_INSTALLATION_SPECIAL_NOTE_LBL') ?></h2>
					<p class="urgent"><?php echo $string_special; ?></p>
				</div>
			<?php }?>

			<?php if ($show_update) { ?>
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
			<?php }?>
		<?php }
		else { ?>
			<div class="cpanel">
				<div class="icon" >
					<a href="<?php echo JROUTE::_('index.php?option=com_bwpostman&token='.JSession::getFormToken()); ?>"> <?php echo JHTML::_('image', 'administrator/components/com_bwpostman/assets/images/icon-48-bwpostman.png', JText::_('COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN')); ?>
						<span><?php echo JText::_('COM_BWPOSTMAN_INSTALL_GO_BWPOSTMAN'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $manual; ?>" target="_blank">
						<?php echo JHTML::_('image', 'administrator/components/com_bwpostman/assets/images/icon-48-bwpostman.png', JText::_('COM_BWPOSTMAN_INSTALL_MANUAL')); ?>
						<span><?php echo JText::_('COM_BWPOSTMAN_INSTALL_MANUAL'); ?></span>
					</a>
				</div>
				<div class="icon">
					<a href="<?php echo $forum; ?>" target="_blank">
						<?php echo JHTML::_('image', 'administrator/components/com_bwpostman/assets/images/icon-48-bwpostman.png', JText::_('COM_BWPOSTMAN_INSTALL_FORUM')); ?>
						<span><?php echo JText::_('COM_BWPOSTMAN_INSTALL_FORUM'); ?></span>
					</a>
				</div>
			</div>
		<?php } ?>
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
	 */

	private function _installdata(&$sql)
	{
		$app	= JFactory::getApplication ();
		$db		= JFactory::getDBO();

		//we call sql file for the templates data
		$buffer = file_get_contents(JPATH_ADMINISTRATOR . '/components/com_bwpostman/sql/' . $sql);

		// Graceful exit and rollback if read not successful
		if ( $buffer ) {
			// Create an array of queries from the sql file
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);

			// No queries to process
			if (count($queries) != 0) {
				// Process each query in the $queries array (split out of sql file).
				foreach ($queries as $query){
					$query = trim($query);
					if ($query != '' && $query{0} != '#') {
						$db->setQuery($query);
						if ( !$db->query() ) {
							$app->enqueueMessage(JText::_('COM_BWPOSTMAN_TEMPLATES_NOT_INSTALLED'), 'warning');
							//return false;
						}
					}
				}//endfoearch
			}
		}
	}

	/**
	 * returns default values for params
	 *
	 * @return  string	Json-encoded default values for params
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
		$config	= Jfactory::getConfig();

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

		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);

		$query->update($db->quoteName('#__extensions'));
		$query->set($db->quoteName('params') . " = " . $db->quote($params));
		$query->where($db->quoteName('element') . " = " . $db->quote('com_bwpostman'));

		$db->SetQuery($query);
		$db->execute();
	}

	/**
	 * install or update access rules for component
	 *
	 * @param string    $type
	 *
	 * @since	1.2.0
	 */
	private function _updateRules($type)
	{
		$default_rules	=	array(
								"core.admin" => array('7' => 1),
								"core.archive" => array('7' => 1, '6' => 1),
								"core.create" => array('7' => 1, '6' => 1),
								"core.delete" => array('7' => 1, '6' => 1),
								"core.edit" => array('7' => 1, '6' => 1),
								"core.edit.own" => array('7' => 1, '6' => 1),
								"core.edit.state" => array('7' => 1, '6' => 1),
								"core.manage" => array('7' => 1, '6' => 1),
								"core.restore" => array('7' => 1, '6' => 1),
								"core.send" =>array('7' => 1, '6' => 1),
								"core.view.archive" => array('7' => 1, '6' => 1),
								"core.view.campaigns" => array('7' => 1, '6' => 1),
								"core.view.maintenance" => array('7' => 1, '6' => 1),
								"core.view.manage" => array('7' => 1, '6' => 1),
								"core.view.mailinglists" => array('7' => 1, '6' => 1),
								"core.view.newsletters" => array('7' => 1, '6' => 1),
								"core.view.subscribers" => array('7' => 1, '6' => 1),
								"core.view.templates" => array('7' => 1, '6' => 1)
							);
		// get stored component rules
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select($db->quoteName('rules'));
		$query->from($db->quoteName('#__assets'));
		$query->where($db->quoteName('name') . " = " . $db->quote('com_bwpostman'));
		$db->SetQuery($query);

		$current_rules = json_decode($db->loadResult(), true);

		//detect missing component rules
		foreach ($default_rules as $key => $value) {
			if (!array_key_exists($key, $current_rules)) {
				$current_rules[$key] = $value;
			}
		}
		$rules	= json_encode($current_rules);

		// update component rules in asset table
		$query	= $db->getQuery(true);

		$query->update($db->quoteName('#__assets'));
		$query->set($db->quoteName('rules') . " = " . $db->quote($rules));
		$query->where($db->quoteName('name') . " = " . $db->quote('com_bwpostman'));
		$db->SetQuery($query);

		$db->execute();
	}
}
