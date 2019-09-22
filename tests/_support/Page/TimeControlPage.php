<?php
namespace Page;

/**
 * Class TimeControlPage
 *
 * @package BwPostman Plugin TimeControl
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
 *
 * @since   0.9.5
 */
class TimeControlPage
{
	/*
	 * Declare UI map for this page here. CSS or XPath allowed.
	 */

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $installFileTC                = "pkg_bwtimecontrol.zip";


	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $pluginName                  = "BwPostman Timecontrol plugin";

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $pluginExtensionName          = "BwPostman Timecontrol Extension";

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $pluginOptionUserName         = "Cronuser";

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $pluginOptionUserNameField    = '//*[@id="jform_params_bwtimecontrol_username"]';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $pluginOptionPassword         = "Cronuser";

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $userTimezoneTab    = 'html/body/div[2]/section/div/div/form/fieldset/ul/li[3]/a';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $pluginUserTimezoneField    = '//*[@id="jform_params_timezone_chzn"]/a';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $pluginUserTimezoneFieldId    = 'jform_params_timezone_chzn';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $pluginUserTimezoneValue     = '//*[@id="jform_params_timezone_chzn"]/div/ul/li[text()="Berlin"]';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $pluginOptionPasswordField    = '//*[@id="jform_params_bwtimecontrol_passwd"]';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $pluginOptionLicence         = "d95001f188d99569f8bc1220374f6254";

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $pluginOptionLicenceField    = '//*[@id="jform_params_bwtimecontrol_licence_code"]';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $pluginOptionIntervalField    = '//*[@id="jform_params_bwtimecontrol_cron_intval_chzn"]/a';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $pluginOptionIntervalFieldId    = 'jform_params_bwtimecontrol_cron_intval_chzn';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $pluginOptionIntervalValue1    = '//*[@id="jform_params_bwtimecontrol_cron_intval_chzn"]/div/ul/li[text()="1"]';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $pluginOptionIntervalValue6    = '//*[@id="jform_params_bwtimecontrol_cron_intval_chzn"]/div/ul/li[text()="6"]';

	/**
	 * @var array
	 *
	 * @since 0.9.5
	 */
	public static $user1    = array(
		'name' => 'Cronuser',
		'password' => '@Miriam01#',
	);

	/**
	 * @var array
	 *
	 * @since 0.9.5
	 */
	public static $user2    = array(
		'name' => 'Cronuser',
		'password' => '@Miriam02#',
	);

	/**
	 * @var array
	 *
	 * @since 0.9.5
	 */
	public static $user3    = array(
		'name' => 'Egon',
		'password' => '@Miriam01#',
	);

	/**
	 * @var array
	 *
	 * @since 0.9.5
	 */
	public static $user4    = array(
		'name' => 'Egon',
		'password' => '@Miriam02#',
	);

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $maintenanceUrl                   = "/administrator/index.php?option=com_bwpostman&view=maintenance";

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $startCronServerButton            = '/html/body/div[2]/section/div/div/div[2]/div[1]/div[2]/table/tbody/tr/td/div/div[5]/div/a';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $stopCronServerButton             = '/html/body/div[2]/section/div/div/div[2]/div[1]/div[2]/table/tbody/tr/td/div/div[6]/div/a';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $infoStartingCronServer           = 'Starting Cron Server. This could last some time…';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $infoStoppingCronServer           = 'Stopping Cron Server, finishing current loop. This could last some time…';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $infoCronServerStarted            = 'Cron Server started';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $infoCronServerStopped            = 'Cron Server stopped';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $errorCronServerNoCredentials     = 'There are no credentials for the cron user entered at plugin options.';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $errorCronServerWrongCredentials  = 'Cron user could not login. Are the entered credentials at plugin options correct?';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $errorCronServerNotStarted        = 'Cron Server not started!';


	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $nlListsPage                      = '/administrator/index.php?option=com_bwpostman&view=newsletters';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $nlListUnsentSubjectField         = '//*[@id="main-table"]/tbody/tr[1]/td[3]/a';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $nlListSentSubjectField           = '//*[@id="main-table"]/tbody/tr[1]/td[3]/p[1]/a';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $scheduledDateField               = '//*[@id="jform_scheduled_date"]';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $scheduledDateOffset              = '-119 minutes';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $scheduledActivatedField          = '//*[@id="jform_ready_to_send"]';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $scheduledActivatedFieldId        = 'jform_ready_to_send';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $scheduledActivatedFieldTrue      = '1';

	/**
	 * @var string
	 *
	 * @since 0.9.5
	 */
	public static $scheduledActivatedFieldFalse     = '0';

}

