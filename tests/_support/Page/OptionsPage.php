<?php
namespace Page;

use Page\MainviewPage as MainView;

/**
 * Class MaintenancePage
 *
 * @package Page
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
 *
 * @since   2.0.0
 */
class OptionsPage
{
	/*
	 * Declare UI map for this page here. CSS or XPath allowed.
	 * public static $usernameField = '#username';
	 * public static $formSubmitButton = "#mainForm input[type=submit]";
	 */

	// Tabs

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $tab_basics       = ".//*[@id='configTabs']/li[1]/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $tab_registration = ".//*[@id='configTabs']/li[2]/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $tab_activation   = ".//*[@id='configTabs']/li[3]/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $tab_lists_view   = ".//*[@id='configTabs']/li[4]/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $tab_single_view  = ".//*[@id='configTabs']/li[5]/a";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $tab_permissions  = ".//*[@id='configTabs']/li[6]/a";

	/*
	 * Tab basic settings
	 */

	/*
	* Tab registration
	*/
	// UI

	/*
	* Tab activation
	*/
	// UI

	/*
	 * Tab lists view
	 */
	// UI

	/*
	 * Tab single view
	 */
	// UI

	/*
	 * Tab permissions
	 */
	// UI

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider= ".//*/a[contains(@href, '#permission-%s')]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanAdmin = ".//*[@id='permissions-sliders']/ul/li[*]/a[contains(text(), 'BwPostmanAdmin')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanPublisher = ".//*[@id='permissions-sliders']/ul/li[*]/a[contains(text(), 'BwPostmanPublisher')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanEditor = ".//*[@id='permissions-sliders']/ul/li[*]/a[contains(text(), 'BwPostmanEditor')]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanManager = ".//*[@id='permissions-sliders']/ul/li[*]/a[contains(text(), 'BwPostmanManager')]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanCampaignAdmin = ".//*[@id='permissions-sliders']/ul/li[*]/a[contains(text(), 'BwPostmanCampaignAdmin')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanCampaignPublisher
		= ".//*[@id='permissions-sliders']/ul/li[*]/a[contains(text(), 'BwPostmanCampaignPublisher')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanCampaignEditor = ".//*[@id='permissions-sliders']/ul/li[*]/a[contains(text(), 'BwPostmanCampaignEditor')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanMailinglistAdmin
		= ".//*[@id='permissions-sliders']/ul/li[*]/a[contains(text(), 'BwPostmanMailinglistAdmin')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanMailinglistPublisher
		= ".//*[@id='permissions-sliders']/ul/li[*]/a[contains(text(), 'BwPostmanMailinglistPublisher')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanMailinglistEditor
		= ".//*[@id='permissions-sliders']/ul/li[*]/a[contains(text(), 'BwPostmanMailinglistEditor')]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanNewsletterAdmin = ".//*[@id='permissions-sliders']/ul/li[*]/a[contains(text(), 'BwPostmanNewsletterAdmin')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanNewsletterPublisher
		= ".//*[@id='permissions-sliders']/ul/li[*]/a[contains(text(), 'BwPostmanNewsletterPublisher')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanNewsletterEditor
		= ".//*[@id='permissions-sliders']/ul/li[*]/a[contains(text(), 'BwPostmanNewsletterEditor')]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanSubscriberAdmin = ".//*[@id='permissions-sliders']/ul/li[*]/a[contains(text(), 'BwPostmanSubscriberAdmin')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanSubscriberPublisher
		= ".//*[@id='permissions-sliders']/ul/li[*]/a[contains(text(), 'BwPostmanSubscriberPublisher')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanSubscriberEditor
		= ".//*[@id='permissions-sliders']/ul/li[*]/a[contains(text(), 'BwPostmanSubscriberEditor')]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanTemplateAdmin = ".//*[@id='permissions-sliders']/ul/li[*]/a[contains(text(), 'BwPostmanTemplateAdmin')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanTemplatePublisher
		= ".//*[@id='permissions-sliders']/ul/li[*]/a[contains(text(), 'BwPostmanTemplatePublisher')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $perm_slider_BwPostmanTemplateEditor = ".//*[@id='permissions-sliders']/ul/li[*]/a[contains(text(), 'BwPostmanTemplateEditor')]";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $select_permission    = ".//*/[@id='jform_rules_core.admin_1']";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $permission_allowed   = "Allowed";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $permission_denied    = "Denied";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $permission_inherited = "Inherited";


	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $action       = ".//*[@id='permission-%s']/table/tbody/tr[%s]/td[2]/select";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $result_row   = ".//*[@id='permission-%s']/table/tbody/tr[%s]/td[3]/span";


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $bwpm_groups    = array(  'BwPostmanAdmin'
											=> array (
												'permissions' => array(
													'core.admin' => 'Allowed',
													'core.manage' => 'Allowed',
													'bwpm.create' => 'Allowed',
													'bwpm.edit' => 'Allowed',
													'bwpm.edit.own' => 'Allowed',
													'bwpm.edit.state' => 'Allowed',
													'bwpm.archive' => 'Allowed',
													'bwpm.restore' => 'Allowed',
													'bwpm.delete' => 'Allowed',
													'bwpm.send' => 'Allowed',
													'bwpm.view.newsletter' => 'Allowed',
													'bwpm.view.subscriber' => 'Allowed',
													'bwpm.view.campaign' => 'Allowed',
													'bwpm.view.mailinglist' => 'Allowed',
													'bwpm.view.template' => 'Allowed',
													'bwpm.view.archive' => 'Allowed',
													'bwpm.view.manage' => 'Allowed',
													'bwpm.view.maintenance' => 'Allowed',
												),
												'parent'    => 'Manager',
											),

											'BwPostmanManager'
											=> array (
												'permissions' => array(
													'core.admin' => 'Denied',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Inherited',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Inherited',
													'bwpm.archive' => 'Inherited',
													'bwpm.restore' => 'Inherited',
													'bwpm.delete' => 'Inherited',
													'bwpm.send' => 'Inherited',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Inherited',
													'bwpm.view.manage' => 'Denied',
													'bwpm.view.maintenance' => 'Denied',
												),
												'parent'    => 'BwPostmanAdmin',
											),

											'BwPostmanPublisher'
											=> array (
												'permissions' => array(
													'core.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Inherited',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Inherited',
													'bwpm.archive' => 'Denied',
													'bwpm.restore' => 'Denied',
													'bwpm.delete' => 'Denied',
													'bwpm.send' => 'Inherited',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Denied',
													'bwpm.view.manage' => 'Inherited',
													'bwpm.view.maintenance' => 'Inherited',
												),
												'parent'    => 'BwPostmanManager',
											),

											'BwPostmanEditor'
											=> array (
												'permissions' => array(
													'core.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Denied',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Denied',
													'bwpm.archive' => 'Inherited',
													'bwpm.restore' => 'Inherited',
													'bwpm.delete' => 'Inherited',
													'bwpm.send' => 'Denied',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Inherited',
													'bwpm.view.manage' => 'Inherited',
													'bwpm.view.maintenance' => 'Inherited',
												),
												'parent'    => 'BwPostmanPublisher',
											),

											'BwPostmanMailinglistAdmin'
											=> array (
												'permissions' => array(
													'core.admin' => 'Denied',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Inherited',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Inherited',
													'bwpm.archive' => 'Inherited',
													'bwpm.restore' => 'Inherited',
													'bwpm.delete' => 'Inherited',
													'bwpm.send' => 'Denied',
													'bwpm.view.newsletter' => 'Denied',
													'bwpm.view.subscriber' => 'Denied',
													'bwpm.view.campaign' => 'Denied',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Denied',
													'bwpm.view.archive' => 'Inherited',
													'bwpm.view.manage' => 'Denied',
													'bwpm.view.maintenance' => 'Denied',
												),
												'parent'    => 'BwPostmanAdmin',
											),

											'BwPostmanMailinglistPublisher'
											=> array (
												'permissions' => array(
													'core.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Inherited',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Inherited',
													'bwpm.archive' => 'Denied',
													'bwpm.restore' => 'Denied',
													'bwpm.delete' => 'Denied',
													'bwpm.send' => 'Inherited',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Denied',
													'bwpm.view.manage' => 'Inherited',
													'bwpm.view.maintenance' => 'Inherited',
												),
												'parent'    => 'BwPostmanMailinglistAdmin',
											),

											'BwPostmanMailinglistEditor'
											=> array (
												'permissions' => array(
													'core.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Denied',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Denied',
													'bwpm.archive' => 'Inherited',
													'bwpm.restore' => 'Inherited',
													'bwpm.delete' => 'Inherited',
													'bwpm.send' => 'Inherited',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Inherited',
													'bwpm.view.manage' => 'Inherited',
													'bwpm.view.maintenance' => 'Inherited',
												),
												'parent'    => 'BwPostmanMailinglistPublisher',
											),

											'BwPostmanSubscriberAdmin'
											=> array (
												'permissions' => array(
													'core.admin' => 'Denied',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Inherited',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Inherited',
													'bwpm.archive' => 'Inherited',
													'bwpm.restore' => 'Inherited',
													'bwpm.delete' => 'Inherited',
													'bwpm.send' => 'Denied',
													'bwpm.view.newsletter' => 'Denied',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Denied',
													'bwpm.view.mailinglist' => 'Denied',
													'bwpm.view.template' => 'Denied',
													'bwpm.view.archive' => 'Inherited',
													'bwpm.view.manage' => 'Denied',
													'bwpm.view.maintenance' => 'Denied',
												),
												'parent'    => 'BwPostmanAdmin',
											),

											'BwPostmanSubscriberPublisher'
											=> array (
												'permissions' => array(
													'core.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Inherited',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Inherited',
													'bwpm.archive' => 'Denied',
													'bwpm.restore' => 'Denied',
													'bwpm.delete' => 'Denied',
													'bwpm.send' => 'Inherited',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Denied',
													'bwpm.view.manage' => 'Inherited',
													'bwpm.view.maintenance' => 'Inherited',
												),
												'parent'    => 'BwPostmanSubscriberAdmin',
											),

											'BwPostmanSubscriberEditor'
											=> array (
												'permissions' => array(
													'core.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Denied',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Denied',
													'bwpm.archive' => 'Inherited',
													'bwpm.restore' => 'Inherited',
													'bwpm.delete' => 'Inherited',
													'bwpm.send' => 'Inherited',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Inherited',
													'bwpm.view.manage' => 'Inherited',
													'bwpm.view.maintenance' => 'Inherited',
												),
												'parent'    => 'BwPostmanSubscriberPublisher',
											),

											'BwPostmanNewsletterAdmin'
											=> array (
												'permissions' => array(
													'core.admin' => 'Denied',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Inherited',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Inherited',
													'bwpm.archive' => 'Inherited',
													'bwpm.restore' => 'Inherited',
													'bwpm.delete' => 'Inherited',
													'bwpm.send' => 'Inherited',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Denied',
													'bwpm.view.campaign' => 'Denied',
													'bwpm.view.mailinglist' => 'Denied',
													'bwpm.view.template' => 'Denied',
													'bwpm.view.archive' => 'Inherited',
													'bwpm.view.manage' => 'Denied',
													'bwpm.view.maintenance' => 'Denied',
												),
												'parent'    => 'BwPostmanAdmin',
											),

											'BwPostmanNewsletterPublisher'
											=> array (
												'permissions' => array(
													'core.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Inherited',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Inherited',
													'bwpm.archive' => 'Denied',
													'bwpm.restore' => 'Denied',
													'bwpm.delete' => 'Denied',
													'bwpm.send' => 'Inherited',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Denied',
													'bwpm.view.manage' => 'Inherited',
													'bwpm.view.maintenance' => 'Inherited',
												),
												'parent'    => 'BwPostmanNewsletterAdmin',
											),

											'BwPostmanNewsletterEditor'
											=> array (
												'permissions' => array(
													'core.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Denied',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Denied',
													'bwpm.archive' => 'Inherited',
													'bwpm.restore' => 'Inherited',
													'bwpm.delete' => 'Inherited',
													'bwpm.send' => 'Denied',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Inherited',
													'bwpm.view.manage' => 'Inherited',
													'bwpm.view.maintenance' => 'Inherited',
												),
												'parent'    => 'BwPostmanNewsletterPublisher',
											),

											'BwPostmanCampaignAdmin'
											=> array (
												'permissions' => array(
													'core.admin' => 'Denied',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Inherited',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Inherited',
													'bwpm.archive' => 'Inherited',
													'bwpm.restore' => 'Inherited',
													'bwpm.delete' => 'Inherited',
													'bwpm.send' => 'Denied',
													'bwpm.view.newsletter' => 'Denied',
													'bwpm.view.subscriber' => 'Denied',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Denied',
													'bwpm.view.template' => 'Denied',
													'bwpm.view.archive' => 'Inherited',
													'bwpm.view.manage' => 'Denied',
													'bwpm.view.maintenance' => 'Denied',
												),
												'parent'    => 'BwPostmanAdmin',
											),

											'BwPostmanCampaignPublisher'
											=> array (
												'permissions' => array(
													'core.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Inherited',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Inherited',
													'bwpm.archive' => 'Denied',
													'bwpm.restore' => 'Denied',
													'bwpm.delete' => 'Denied',
													'bwpm.send' => 'Inherited',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Denied',
													'bwpm.view.manage' => 'Inherited',
													'bwpm.view.maintenance' => 'Inherited',
												),
												'parent'    => 'BwPostmanCampaignAdmin',
											),

											'BwPostmanCampaignEditor'
											=> array (
												'permissions' => array(
													'core.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Denied',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Denied',
													'bwpm.archive' => 'Inherited',
													'bwpm.restore' => 'Inherited',
													'bwpm.delete' => 'Inherited',
													'bwpm.send' => 'Inherited',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Inherited',
													'bwpm.view.manage' => 'Inherited',
													'bwpm.view.maintenance' => 'Inherited',
												),
												'parent'    => 'BwPostmanCampaignPublisher',
											),

											'BwPostmanTemplateAdmin'
											=> array (
												'permissions' => array(
													'core.admin' => 'Denied',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Inherited',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Inherited',
													'bwpm.archive' => 'Inherited',
													'bwpm.restore' => 'Inherited',
													'bwpm.delete' => 'Inherited',
													'bwpm.send' => 'Denied',
													'bwpm.view.newsletter' => 'Denied',
													'bwpm.view.subscriber' => 'Denied',
													'bwpm.view.campaign' => 'Denied',
													'bwpm.view.mailinglist' => 'Denied',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Inherited',
													'bwpm.view.manage' => 'Denied',
													'bwpm.view.maintenance' => 'Denied',
												),
												'parent'    => 'BwPostmanAdmin',
											),

											'BwPostmanTemplatePublisher'
											=> array (
												'permissions' => array(
													'core.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Inherited',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Inherited',
													'bwpm.archive' => 'Denied',
													'bwpm.restore' => 'Denied',
													'bwpm.delete' => 'Denied',
													'bwpm.send' => 'Inherited',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Denied',
													'bwpm.view.manage' => 'Inherited',
													'bwpm.view.maintenance' => 'Inherited',
												),
												'parent'    => 'BwPostmanTemplateAdmin',
											),

											'BwPostmanTemplateEditor'
											=> array (
												'permissions' => array(
													'core.admin' => 'Inherited',
													'core.manage' => 'Inherited',
													'bwpm.create' => 'Inherited',
													'bwpm.edit' => 'Denied',
													'bwpm.edit.own' => 'Inherited',
													'bwpm.edit.state' => 'Denied',
													'bwpm.archive' => 'Inherited',
													'bwpm.restore' => 'Inherited',
													'bwpm.delete' => 'Inherited',
													'bwpm.send' => 'Inherited',
													'bwpm.view.newsletter' => 'Inherited',
													'bwpm.view.subscriber' => 'Inherited',
													'bwpm.view.campaign' => 'Inherited',
													'bwpm.view.mailinglist' => 'Inherited',
													'bwpm.view.template' => 'Inherited',
													'bwpm.view.archive' => 'Inherited',
													'bwpm.view.manage' => 'Inherited',
													'bwpm.view.maintenance' => 'Inherited',
												),
												'parent'    => 'BwPostmanTemplatePublisher',
											),
										);

	// buttons

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $label_allowed               = ".//*table/tbody/tr[%s]/td[3]/span[contains(text(), 'Allowed')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $label_not_allowed           = ".//*table/tbody/tr[%s]/td[3]/span[contains(text(), 'Not Allowed')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $label_inherited_allowed     = ".//*table/tbody/tr[%s]/td[3]/span[contains(text(), 'Allowed (Inherited)')]";

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $label_inherited_not_allowed = ".//*table/tbody/tr[%s]/td[3]/span[contains(text(), 'Not Allowed (Locked)')]";


	/**
	 * @var array
	 *
	 * @since 2.0.0
	 */
	public static $bwpm_group_permissions = array(  'BwPostmanAdmin'
											=> array(
												'core.manage' => 'Allowed',
												'core.admin' => 'Allowed',
												'bwpm.create' => 'Allowed',
												'bwpm.edit' => 'Allowed',
												'bwpm.edit.own' => 'Allowed',
												'bwpm.edit.state' => 'Allowed',
												'bwpm.archive' => 'Allowed',
												'bwpm.restore' => 'Allowed',
												'bwpm.delete' => 'Allowed',
												'bwpm.send' => 'Allowed',
												'bwpm.view.newsletter' => 'Allowed',
												'bwpm.view.subscriber' => 'Allowed',
												'bwpm.view.campaign' => 'Allowed',
												'bwpm.view.mailinglist' => 'Allowed',
												'bwpm.view.template' => 'Allowed',
												'bwpm.view.archive' => 'Allowed',
												'bwpm.view.manage' => 'Allowed',
												'bwpm.view.maintenance' => 'Allowed',
											),
											'BwPostmanManager'
											=> array(
												'core.admin' => 'Not Allowed',
												'core.manage' => 'Allowed (Inherited)',
												'bwpm.create' => 'Allowed (Inherited)',
												'bwpm.edit' => 'Allowed (Inherited)',
												'bwpm.edit.own' => 'Allowed (Inherited)',
												'bwpm.edit.state' => 'Allowed (Inherited)',
												'bwpm.archive' => 'Allowed (Inherited)',
												'bwpm.restore' => 'Allowed (Inherited)',
												'bwpm.delete' => 'Allowed (Inherited)',
												'bwpm.send' => 'Allowed (Inherited)',
												'bwpm.view.newsletter' => 'Allowed (Inherited)',
												'bwpm.view.subscriber' => 'Allowed (Inherited)',
												'bwpm.view.campaign' => 'Allowed (Inherited)',
												'bwpm.view.mailinglist' => 'Allowed (Inherited)',
												'bwpm.view.template' => 'Allowed (Inherited)',
												'bwpm.view.archive' => 'Allowed (Inherited)',
												'bwpm.view.manage' => 'Not Allowed',
												'bwpm.view.maintenance' => 'Not Allowed',
											),
											'BwPostmanPublisher'
											=> array(
												'core.admin' => 'Not Allowed (Locked)',
												'core.manage' => 'Allowed (Inherited)',
												'bwpm.create' => 'Allowed (Inherited)',
												'bwpm.edit' => 'Allowed (Inherited)',
												'bwpm.edit.own' => 'Allowed (Inherited)',
												'bwpm.edit.state' => 'Allowed (Inherited)',
												'bwpm.archive' => 'Not Allowed',
												'bwpm.restore' => 'Not Allowed',
												'bwpm.delete' => 'Not Allowed',
												'bwpm.send' => 'Allowed (Inherited)',
												'bwpm.view.newsletter' => 'Allowed (Inherited)',
												'bwpm.view.subscriber' => 'Allowed (Inherited)',
												'bwpm.view.campaign' => 'Allowed (Inherited)',
												'bwpm.view.mailinglist' => 'Allowed (Inherited)',
												'bwpm.view.template' => 'Allowed (Inherited)',
												'bwpm.view.archive' => 'Not Allowed',
												'bwpm.view.manage' => 'Not Allowed (Locked)',
												'bwpm.view.maintenance' => 'Not Allowed (Locked)',
											),
											'BwPostmanEditor'
											=> array(
												'core.admin' => 'Not Allowed (Locked)',
												'core.manage' => 'Allowed (Inherited)',
												'bwpm.create' => 'Allowed (Inherited)',
												'bwpm.edit' => 'Not Allowed',
												'bwpm.edit.own' => 'Allowed (Inherited)',
												'bwpm.edit.state' => 'Not Allowed',
												'bwpm.archive' => 'Not Allowed (Locked)',
												'bwpm.restore' => 'Not Allowed (Locked)',
												'bwpm.delete' => 'Not Allowed (Locked)',
												'bwpm.send' => 'Not Allowed',
												'bwpm.view.newsletter' => 'Allowed (Inherited)',
												'bwpm.view.subscriber' => 'Allowed (Inherited)',
												'bwpm.view.campaign' => 'Allowed (Inherited)',
												'bwpm.view.mailinglist' => 'Allowed (Inherited)',
												'bwpm.view.template' => 'Allowed (Inherited)',
												'bwpm.view.archive' => 'Not Allowed (Locked)',
												'bwpm.view.manage' => 'Not Allowed (Locked)',
												'bwpm.view.maintenance' => 'Not Allowed (Locked)',
											),
											'BwPostmanMailinglistAdmin'
											=> array(
												'core.admin' => 'Not Allowed',
												'core.manage' => 'Allowed (Inherited)',
												'bwpm.create' => 'Allowed (Inherited)',
												'bwpm.edit' => 'Allowed (Inherited)',
												'bwpm.edit.own' => 'Allowed (Inherited)',
												'bwpm.edit.state' => 'Allowed (Inherited)',
												'bwpm.archive' => 'Allowed (Inherited)',
												'bwpm.restore' => 'Allowed (Inherited)',
												'bwpm.delete' => 'Allowed (Inherited)',
												'bwpm.send' => 'Not Allowed',
												'bwpm.view.newsletter' => 'Not Allowed',
												'bwpm.view.subscriber' => 'Not Allowed',
												'bwpm.view.campaign' => 'Not Allowed',
												'bwpm.view.mailinglist' => 'Allowed (Inherited)',
												'bwpm.view.template' => 'Not Allowed',
												'bwpm.view.archive' => 'Allowed (Inherited)',
												'bwpm.view.manage' => 'Not Allowed',
												'bwpm.view.maintenance' => 'Not Allowed',
											),
											'BwPostmanMailinglistPublisher'
											=> array(
												'core.admin' => 'Not Allowed (Locked)',
												'core.manage' => 'Allowed (Inherited)',
												'bwpm.create' => 'Allowed (Inherited)',
												'bwpm.edit' => 'Allowed (Inherited)',
												'bwpm.edit.own' => 'Allowed (Inherited)',
												'bwpm.edit.state' => 'Allowed (Inherited)',
												'bwpm.archive' => 'Not Allowed',
												'bwpm.restore' => 'Not Allowed',
												'bwpm.delete' => 'Not Allowed',
												'bwpm.send' => 'Not Allowed (Locked)',
												'bwpm.view.newsletter' => 'Not Allowed (Locked)',
												'bwpm.view.subscriber' => 'Not Allowed (Locked)',
												'bwpm.view.campaign' => 'Not Allowed (Locked)',
												'bwpm.view.mailinglist' => 'Allowed (Inherited)',
												'bwpm.view.template' => 'Not Allowed (Locked)',
												'bwpm.view.archive' => 'Not Allowed',
												'bwpm.view.manage' => 'Not Allowed (Locked)',
												'bwpm.view.maintenance' => 'Not Allowed (Locked)',
											),
											'BwPostmanMailinglistEditor'
											=> array(
												'core.admin' => 'Not Allowed (Locked)',
												'core.manage' => 'Allowed (Inherited)',
												'bwpm.create' => 'Allowed (Inherited)',
												'bwpm.edit' => 'Not Allowed',
												'bwpm.edit.own' => 'Allowed (Inherited)',
												'bwpm.edit.state' => 'Not Allowed',
												'bwpm.archive' => 'Not Allowed (Locked)',
												'bwpm.restore' => 'Not Allowed (Locked)',
												'bwpm.delete' => 'Not Allowed (Locked)',
												'bwpm.send' => 'Not Allowed (Locked)',
												'bwpm.view.newsletter' => 'Not Allowed (Locked)',
												'bwpm.view.subscriber' => 'Not Allowed (Locked)',
												'bwpm.view.campaign' => 'Not Allowed (Locked)',
												'bwpm.view.mailinglist' => 'Allowed (Inherited)',
												'bwpm.view.template' => 'Not Allowed (Locked)',
												'bwpm.view.archive' => 'Not Allowed (Locked)',
												'bwpm.view.manage' => 'Not Allowed (Locked)',
												'bwpm.view.maintenance' => 'Not Allowed (Locked)',
											),
											'BwPostmanSubscriberAdmin'
											=> array(
												'core.admin' => 'Not Allowed',
												'core.manage' => 'Allowed (Inherited)',
												'bwpm.create' => 'Allowed (Inherited)',
												'bwpm.edit' => 'Allowed (Inherited)',
												'bwpm.edit.own' => 'Allowed (Inherited)',
												'bwpm.edit.state' => 'Allowed (Inherited)',
												'bwpm.archive' => 'Allowed (Inherited)',
												'bwpm.restore' => 'Allowed (Inherited)',
												'bwpm.delete' => 'Allowed (Inherited)',
												'bwpm.send' => 'Not Allowed',
												'bwpm.view.newsletter' => 'Not Allowed',
												'bwpm.view.subscriber' => 'Allowed (Inherited)',
												'bwpm.view.campaign' => 'Not Allowed',
												'bwpm.view.mailinglist' => 'Not Allowed',
												'bwpm.view.template' => 'Not Allowed',
												'bwpm.view.archive' => 'Allowed (Inherited)',
												'bwpm.view.manage' => 'Not Allowed',
												'bwpm.view.maintenance' => 'Not Allowed',
											),
											'BwPostmanSubscriberPublisher'
											=> array(
												'core.admin' => 'Not Allowed (Locked)',
												'core.manage' => 'Allowed (Inherited)',
												'bwpm.create' => 'Allowed (Inherited)',
												'bwpm.edit' => 'Allowed (Inherited)',
												'bwpm.edit.own' => 'Allowed (Inherited)',
												'bwpm.edit.state' => 'Allowed (Inherited)',
												'bwpm.archive' => 'Not Allowed',
												'bwpm.restore' => 'Not Allowed',
												'bwpm.delete' => 'Not Allowed',
												'bwpm.send' => 'Not Allowed (Locked)',
												'bwpm.view.newsletter' => 'Not Allowed (Locked)',
												'bwpm.view.subscriber' => 'Allowed (Inherited)',
												'bwpm.view.campaign' => 'Not Allowed (Locked)',
												'bwpm.view.mailinglist' => 'Not Allowed (Locked)',
												'bwpm.view.template' => 'Not Allowed (Locked)',
												'bwpm.view.archive' => 'Not Allowed',
												'bwpm.view.manage' => 'Not Allowed (Locked)',
												'bwpm.view.maintenance' => 'Not Allowed (Locked)',
											),
											'BwPostmanSubscriberEditor'
											=> array(
												'core.admin' => 'Not Allowed (Locked)',
												'core.manage' => 'Allowed (Inherited)',
												'bwpm.create' => 'Allowed (Inherited)',
												'bwpm.edit' => 'Not Allowed',
												'bwpm.edit.own' => 'Allowed (Inherited)',
												'bwpm.edit.state' => 'Not Allowed',
												'bwpm.archive' => 'Not Allowed (Locked)',
												'bwpm.restore' => 'Not Allowed (Locked)',
												'bwpm.delete' => 'Not Allowed (Locked)',
												'bwpm.send' => 'Not Allowed (Locked)',
												'bwpm.view.newsletter' => 'Not Allowed (Locked)',
												'bwpm.view.subscriber' => 'Allowed (Inherited)',
												'bwpm.view.campaign' => 'Not Allowed (Locked)',
												'bwpm.view.mailinglist' => 'Not Allowed (Locked)',
												'bwpm.view.template' => 'Not Allowed (Locked)',
												'bwpm.view.archive' => 'Not Allowed (Locked)',
												'bwpm.view.manage' => 'Not Allowed (Locked)',
												'bwpm.view.maintenance' => 'Not Allowed (Locked)',
											),
											'BwPostmanNewsletterAdmin'
											=> array(
												'core.admin' => 'Not Allowed',
												'core.manage' => 'Allowed (Inherited)',
												'bwpm.create' => 'Allowed (Inherited)',
												'bwpm.edit' => 'Allowed (Inherited)',
												'bwpm.edit.own' => 'Allowed (Inherited)',
												'bwpm.edit.state' => 'Allowed (Inherited)',
												'bwpm.archive' => 'Allowed (Inherited)',
												'bwpm.restore' => 'Allowed (Inherited)',
												'bwpm.delete' => 'Allowed (Inherited)',
												'bwpm.send' => 'Allowed (Inherited)',
												'bwpm.view.newsletter' => 'Allowed (Inherited)',
												'bwpm.view.subscriber' => 'Not Allowed',
												'bwpm.view.campaign' => 'Not Allowed',
												'bwpm.view.mailinglist' => 'Not Allowed',
												'bwpm.view.template' => 'Not Allowed',
												'bwpm.view.archive' => 'Allowed (Inherited)',
												'bwpm.view.manage' => 'Not Allowed',
												'bwpm.view.maintenance' => 'Not Allowed',
											),
											'BwPostmanNewsletterPublisher'
											=> array(
												'core.admin' => 'Not Allowed (Locked)',
												'core.manage' => 'Allowed (Inherited)',
												'bwpm.create' => 'Allowed (Inherited)',
												'bwpm.edit' => 'Allowed (Inherited)',
												'bwpm.edit.own' => 'Allowed (Inherited)',
												'bwpm.edit.state' => 'Allowed (Inherited)',
												'bwpm.archive' => 'Not Allowed',
												'bwpm.restore' => 'Not Allowed',
												'bwpm.delete' => 'Not Allowed',
												'bwpm.send' => 'Allowed (Inherited)',
												'bwpm.view.newsletter' => 'Allowed (Inherited)',
												'bwpm.view.subscriber' => 'Not Allowed (Locked)',
												'bwpm.view.campaign' => 'Not Allowed (Locked)',
												'bwpm.view.mailinglist' => 'Not Allowed (Locked)',
												'bwpm.view.template' => 'Not Allowed (Locked)',
												'bwpm.view.archive' => 'Not Allowed',
												'bwpm.view.manage' => 'Not Allowed (Locked)',
												'bwpm.view.maintenance' => 'Not Allowed (Locked)',
											),
											'BwPostmanNewsletterEditor'
											=> array(
												'core.admin' => 'Not Allowed (Locked)',
												'core.manage' => 'Allowed (Inherited)',
												'bwpm.create' => 'Allowed (Inherited)',
												'bwpm.edit' => 'Not Allowed',
												'bwpm.edit.own' => 'Allowed (Inherited)',
												'bwpm.edit.state' => 'Not Allowed',
												'bwpm.archive' => 'Not Allowed (Locked)',
												'bwpm.restore' => 'Not Allowed (Locked)',
												'bwpm.delete' => 'Not Allowed (Locked)',
												'bwpm.send' => 'Not Allowed',
												'bwpm.view.newsletter' => 'Allowed (Inherited)',
												'bwpm.view.subscriber' => 'Not Allowed (Locked)',
												'bwpm.view.campaign' => 'Not Allowed (Locked)',
												'bwpm.view.mailinglist' => 'Not Allowed (Locked)',
												'bwpm.view.template' => 'Not Allowed (Locked)',
												'bwpm.view.archive' => 'Not Allowed (Locked)',
												'bwpm.view.manage' => 'Not Allowed (Locked)',
												'bwpm.view.maintenance' => 'Not Allowed (Locked)',
											),
											'BwPostmanCampaignAdmin'
											=> array(
												'core.admin' => 'Not Allowed',
												'core.manage' => 'Allowed (Inherited)',
												'bwpm.create' => 'Allowed (Inherited)',
												'bwpm.edit' => 'Allowed (Inherited)',
												'bwpm.edit.own' => 'Allowed (Inherited)',
												'bwpm.edit.state' => 'Allowed (Inherited)',
												'bwpm.archive' => 'Allowed (Inherited)',
												'bwpm.restore' => 'Allowed (Inherited)',
												'bwpm.delete' => 'Allowed (Inherited)',
												'bwpm.send' => 'Not Allowed',
												'bwpm.view.newsletter' => 'Not Allowed',
												'bwpm.view.subscriber' => 'Not Allowed',
												'bwpm.view.campaign' => 'Allowed (Inherited)',
												'bwpm.view.mailinglist' => 'Not Allowed',
												'bwpm.view.template' => 'Not Allowed',
												'bwpm.view.archive' => 'Allowed (Inherited)',
												'bwpm.view.manage' => 'Not Allowed',
												'bwpm.view.maintenance' => 'Not Allowed',
											),
											'BwPostmanCampaignPublisher'
											=> array(
												'core.admin' => 'Not Allowed (Locked)',
												'core.manage' => 'Allowed (Inherited)',
												'bwpm.create' => 'Allowed (Inherited)',
												'bwpm.edit' => 'Allowed (Inherited)',
												'bwpm.edit.own' => 'Allowed (Inherited)',
												'bwpm.edit.state' => 'Allowed (Inherited)',
												'bwpm.archive' => 'Not Allowed',
												'bwpm.restore' => 'Not Allowed',
												'bwpm.delete' => 'Not Allowed',
												'bwpm.send' => 'Not Allowed (Locked)',
												'bwpm.view.newsletter' => 'Not Allowed (Locked)',
												'bwpm.view.subscriber' => 'Not Allowed (Locked)',
												'bwpm.view.campaign' => 'Allowed (Inherited)',
												'bwpm.view.mailinglist' => 'Not Allowed (Locked)',
												'bwpm.view.template' => 'Not Allowed (Locked)',
												'bwpm.view.archive' => 'Not Allowed',
												'bwpm.view.manage' => 'Not Allowed (Locked)',
												'bwpm.view.maintenance' => 'Not Allowed (Locked)',
											),
											'BwPostmanCampaignEditor'
											=> array(
												'core.admin' => 'Not Allowed (Locked)',
												'core.manage' => 'Allowed (Inherited)',
												'bwpm.create' => 'Allowed (Inherited)',
												'bwpm.edit' => 'Not Allowed',
												'bwpm.edit.own' => 'Allowed (Inherited)',
												'bwpm.edit.state' => 'Not Allowed',
												'bwpm.archive' => 'Not Allowed (Locked)',
												'bwpm.restore' => 'Not Allowed (Locked)',
												'bwpm.delete' => 'Not Allowed (Locked)',
												'bwpm.send' => 'Not Allowed (Locked)',
												'bwpm.view.newsletter' => 'Not Allowed (Locked)',
												'bwpm.view.subscriber' => 'Not Allowed (Locked)',
												'bwpm.view.campaign' => 'Allowed (Inherited)',
												'bwpm.view.mailinglist' => 'Not Allowed (Locked)',
												'bwpm.view.template' => 'Not Allowed (Locked)',
												'bwpm.view.archive' => 'Not Allowed (Locked)',
												'bwpm.view.manage' => 'Not Allowed (Locked)',
												'bwpm.view.maintenance' => 'Not Allowed (Locked)',
											),
											'BwPostmanTemplateAdmin'
											=> array(
												'core.admin' => 'Not Allowed',
												'core.manage' => 'Allowed (Inherited)',
												'bwpm.create' => 'Allowed (Inherited)',
												'bwpm.edit' => 'Allowed (Inherited)',
												'bwpm.edit.own' => 'Allowed (Inherited)',
												'bwpm.edit.state' => 'Allowed (Inherited)',
												'bwpm.archive' => 'Allowed (Inherited)',
												'bwpm.restore' => 'Allowed (Inherited)',
												'bwpm.delete' => 'Allowed (Inherited)',
												'bwpm.send' => 'Not Allowed',
												'bwpm.view.newsletter' => 'Not Allowed',
												'bwpm.view.subscriber' => 'Not Allowed',
												'bwpm.view.campaign' => 'Not Allowed',
												'bwpm.view.mailinglist' => 'Not Allowed',
												'bwpm.view.template' => 'Allowed (Inherited)',
												'bwpm.view.archive' => 'Allowed (Inherited)',
												'bwpm.view.manage' => 'Not Allowed',
												'bwpm.view.maintenance' => 'Not Allowed',
											),
											'BwPostmanTemplatePublisher'
											=> array(
												'core.admin' => 'Not Allowed (Locked)',
												'core.manage' => 'Allowed (Inherited)',
												'bwpm.create' => 'Allowed (Inherited)',
												'bwpm.edit' => 'Allowed (Inherited)',
												'bwpm.edit.own' => 'Allowed (Inherited)',
												'bwpm.edit.state' => 'Allowed (Inherited)',
												'bwpm.archive' => 'Not Allowed',
												'bwpm.restore' => 'Not Allowed',
												'bwpm.delete' => 'Not Allowed',
												'bwpm.send' => 'Not Allowed (Locked)',
												'bwpm.view.newsletter' => 'Not Allowed (Locked)',
												'bwpm.view.subscriber' => 'Not Allowed (Locked)',
												'bwpm.view.campaign' => 'Not Allowed (Locked)',
												'bwpm.view.mailinglist' => 'Not Allowed (Locked)',
												'bwpm.view.template' => 'Allowed (Inherited)',
												'bwpm.view.archive' => 'Not Allowed',
												'bwpm.view.manage' => 'Not Allowed (Locked)',
												'bwpm.view.maintenance' => 'Not Allowed (Locked)',
											),
											'BwPostmanTemplateEditor'
											=> array(
												'core.admin' => 'Not Allowed (Locked)',
												'core.manage' => 'Allowed (Inherited)',
												'bwpm.create' => 'Allowed (Inherited)',
												'bwpm.edit' => 'Not Allowed',
												'bwpm.edit.own' => 'Allowed (Inherited)',
												'bwpm.edit.state' => 'Not Allowed',
												'bwpm.archive' => 'Not Allowed (Locked)',
												'bwpm.restore' => 'Not Allowed (Locked)',
												'bwpm.delete' => 'Not Allowed (Locked)',
												'bwpm.send' => 'Not Allowed (Locked)',
												'bwpm.view.newsletter' => 'Not Allowed (Locked)',
												'bwpm.view.subscriber' => 'Not Allowed (Locked)',
												'bwpm.view.campaign' => 'Not Allowed (Locked)',
												'bwpm.view.mailinglist' => 'Not Allowed (Locked)',
												'bwpm.view.template' => 'Allowed (Inherited)',
												'bwpm.view.archive' => 'Not Allowed (Locked)',
												'bwpm.view.manage' => 'Not Allowed (Locked)',
												'bwpm.view.maintenance' => 'Not Allowed (Locked)',
											),
	);


	/*
	 * Messages
	 */

	/**
	 * @var string
	 *
	 * @since 2.0.0
	 */
	public static $optionsSuccessMsg    = "Configuration successfully saved.";

	/**
	 * Test method to save defaults once of BwPostman
	 *
	 * @param   \AcceptanceTester                $I
	 *
	 * @before  _login
	 *
	 * @after   _logout
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public static function saveDefaults(\AcceptanceTester $I)
	{
		$I->wantTo("Save Default Options BwPostman");
		$I->expectTo("see success message and component in menu");
		$I->amOnPage(MainView::$url);

		$I->see(Generals::$extension, Generals::$pageTitle);

		$I->clickAndWait(Generals::$toolbar['Options'], 1);

		$I->clickAndWait(Generals::$toolbar['Save & Close'], 1);

		$I->see("Message", Generals::$alert_header);
		$I->see(Generals::$alert_msg_txt, Generals::$alert_success);
	}
}
