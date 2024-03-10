<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman html helper class for backend.
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

namespace BoldtWebservice\Component\BwPostman\Administrator\Helper;

defined ('_JEXEC') or die ();

use Exception;
use JButtonExtlink;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Class BwPostmanHTMLHelper
 *
 * @since
 */
class BwPostmanHTMLHelper {

	/**
	 * Creates the buttons view at the start page
	 * --> from administrator/mod_quickicon/mod_quickicon.php
	 *
	 * @param string  $link     URL target
	 * @param string  $image    Image path
	 * @param string  $text     Image description
	 * @param int     $x_size   x_size
	 * @param int     $y_size   y_size
	 * @param string  $target   target
	 * @param string  $onclick  onclick action
	 * @param boolean $closable modal window closeable
	 *
	 * @since
	 */
	public static function quickiconButton(string $link, string $image, string $text, int $x_size = 0, int $y_size = 0, string $target = '', string $onclick = '', bool $closable = true)
	{
		$closable = !$closable ? ', closable: false' : '';
		($x_size && $y_size) ? $modal_text	= 'class="modal" rel="{handler: \'iframe\', size: {x: ' . $x_size . ', y: ' . $y_size . '}' . $closable . '}"' : $modal_text	= '';
		?>
		<div class="col">
			<div class="card btn h-100">
				<a  class="card-body" href="<?php echo $link; ?>" <?php if ($target != '') echo 'target="' . $target . '"'; ?> <?php if ($onclick != '') echo 'onclick="' . $onclick . '"'; ?> <?php echo $modal_text; ?>>
					<span class="icon d-block mb-3"><?php echo HtmlHelper::_('image', 'media/com_bwpostman/images/'.$image, $text); ?></span>
					<span class="linktext"><?php echo $text; ?></span>
				</a>
			</div>
		</div>
		<?php
	}

	/**
	 * Method to get the fields list for subscriber export
	 *
	 * @return 	string	export fields list, html select list multiple
	 *
	 * @throws Exception
	 *
	 * @since
	 */
	static public function getExportFieldsList(): string
	{
		$export_fields	= array();

		$columns = BwPostmanSubscriberHelper::getExportFieldsList();

		foreach ($columns AS $column)
		{
			$export_fields[] = HtmlHelper::_('select.option', $column->Field, $column->Field);
		}

		return HtmlHelper::_('select.genericlist', $export_fields, 'export_fields[]', 'class="form-select" size="20" multiple multiple="multiple" style="padding: 6px; width: 260px;"', 'value', 'text');
	}

	/**
	 * Method to get the file format list
	 *
	 * @param string $selected
	 *
	 * @return 	string	file format list, html select list
	 *
	 * @since
	 */
	static public function getFileFormatList(string $selected = ''): string
	{
		$fileformat 	= array();

		$fileformat[] 	= HtmlHelper::_('select.option', 'csv', Text::_('COM_BWPOSTMAN_CSV'));
		$fileformat[] 	= HtmlHelper::_('select.option', 'xml', Text::_('COM_BWPOSTMAN_XML'));

		return HtmlHelper::_('select.radiolist', $fileformat, 'fileformat', '', 'value', 'text', $selected);
	}

	/**
	 * Method to get the delimiter list
	 *
	 * @param string $selected
	 *
	 * @return 	string	delimiter list, html select list
	 *
	 * @since
	 */
	static public function getDelimiterList(string $selected = ';'): string
	{
		$delimiter	= array();

		$delimiter[] = HtmlHelper::_('select.option', ',', Text::_('COM_BWPOSTMAN_SUB_DELIMITER_COMMA'));
		$delimiter[] = HtmlHelper::_('select.option', ';', Text::_('COM_BWPOSTMAN_SUB_DELIMITER_SEMICOLON'));
		$delimiter[] = HtmlHelper::_('select.option', '\t', Text::_('COM_BWPOSTMAN_SUB_DELIMITER_TABULATOR'));
		$delimiter[] = HtmlHelper::_('select.option', ' ', Text::_('COM_BWPOSTMAN_SUB_DELIMITER_WHITESPACE'));

		return HtmlHelper::_('select.genericlist', $delimiter, 'delimiter', 'class="custom-select w-auto" size="1"', 'value', 'text', $selected);
	}

	/**
	 * Method to get the enclosure list
	 *
	 * @param string $selected
	 *
	 * @return 	string	enclosure list, html select list
	 *
	 * @since
	 */
	static public function getEnclosureList(string $selected = '"'): string
	{
		$enclosure	= array();

		$enclosure[] = HtmlHelper::_('select.option', '', Text::_('COM_BWPOSTMAN_SUB_EXPORT_ENCLOSURE_NOSEPARATION'));
		$enclosure[] = HtmlHelper::_('select.option', "'", Text::_('COM_BWPOSTMAN_SUB_EXPORT_ENCLOSURE_QUOTE'));
		$enclosure[] = HtmlHelper::_('select.option', '"', Text::_('COM_BWPOSTMAN_SUB_EXPORT_ENCLOSURE_DOUBLEQUOTE'));

		return HtmlHelper::_('select.genericlist', $enclosure, 'enclosure', 'class="custom-select w-auto" size="1"', 'value', 'text', $selected);
	}

	/**
	 * Method to get the mail format list
	 *
	 * @param string $selected
	 *
	 * @return 	string	mail format list, html select list
	 *
	 * @since
	 */
	static public function getMailFormatList(string $selected = '1'): string
	{
		$emailformat 	= array();

		$emailformat[] 	= HtmlHelper::_('select.option', '0', Text::_('COM_BWPOSTMAN_TEXT'));
		$emailformat[] 	= HtmlHelper::_('select.option', '1', Text::_('COM_BWPOSTMAN_HTML'));

		return HtmlHelper::_('select.radiolist', $emailformat, 'emailformat', 'class="form-check-input" ', 'value', 'text', $selected);
	}

	/**
	 * Method to get the database fields list
	 *
	 * @return 	string	database fields list, html select list
	 *
	 * @since
	 */
	static public function getDbFieldsList(): string
	{
		$db_fields	= array();
		$columns	= array();

		$columns[]	= 'name';
		$columns[]	= 'firstname';
		$columns[]	= 'email';
		$columns[]	= 'emailformat';
		$columns[]	= 'status';

		foreach ($columns AS $column)
		{
			$db_fields[] = HtmlHelper::_('select.option', $column, $column);
		}

		return HtmlHelper::_('select.genericlist', $db_fields, 'db_fields[]', 'class="custom-select w-auto" size="10" multiple multiple="multiple"', 'value', 'text');
	}

	/**
	 * Method to get the mailinglists select list
	 *
	 * @param array $mailinglists
	 *
	 * @return 	string	mailinglists select list, html select list
	 *
	 * @since
	 */
//	static public function getMlSelectList(array $mailinglists = array()): string
//	{
//		$import_mailinglists	= array();
//		$bwp_mailinglist_values = '';
//
//		if (($mailinglists['public']) || ($mailinglists['special']))
//		{
//			$import_mailinglists[] = HtmlHelper::_('select.option', '- - - - - - - - - - - - - - - - - - - - - - - - - - - -');
//			$import_mailinglists[] = HtmlHelper::_('select.option', '- - - '.Text::_('COM_BWPOSTMAN_ML_PUBLIC').' - - -');
//			$import_mailinglists[] = HtmlHelper::_('select.option', '- - - - - - - - - - - - - - - - - - - - - - - - - - - -');
//			if ($mailinglists['public'])
//			{
//				$import_mailinglists[] = HtmlHelper::_('select.option', '- '.Text::_('COM_BWPOSTMAN_ML_PUBLIC_PUBLIC').' -');
//				foreach ($mailinglists['public'] AS $mailinglist)
//				{
//					$import_mailinglists[] = HtmlHelper::_('select.option', $mailinglist['id'], $mailinglist['title'] .': '.$mailinglist['description']);
//				}
//			}
//			if ($mailinglists['special'])
//			{
//				$import_mailinglists[] = HtmlHelper::_('select.option', '- '.Text::_('COM_BWPOSTMAN_ML_PUBLIC_REGISTERED_AND_MORE').' -');
//				foreach ($mailinglists['special'] AS $mailinglist)
//				{
//					$import_mailinglists[] = HtmlHelper::_('select.option', $mailinglist['id'], $mailinglist['title'] .': '.$mailinglist['description']);
//				}
//			}
//		}
//		if ($mailinglists['internal'])
//		{
//			$import_mailinglists[] = HtmlHelper::_('select.option', '- - - - - - - - - - - - - - - - - - - - - - - - - - - -');
//			$import_mailinglists[] = HtmlHelper::_('select.option', '- - - '.Text::_('COM_BWPOSTMAN_ML_INTERNAL').' - - -');
//			$import_mailinglists[] = HtmlHelper::_('select.option', '- - - - - - - - - - - - - - - - - - - - - - - - - - - -');
//			foreach ($mailinglists['internal'] AS $mailinglist)
//			{
//				$import_mailinglists[] = HtmlHelper::_('select.option', $mailinglist['id'], $mailinglist['title'] .': '.$mailinglist['description']);
//			}
//		}
//		$import_mailinglists	= HtmlHelper::_('select.genericlist', $import_mailinglists, 'import_mailinglists[]', 'class="w-auto" size="10" multiple multiple="multiple" ', 'value', 'text', $bwp_mailinglist_values);
//
//		return str_replace('>-', ' disabled="disabled">-', $import_mailinglists);
//	}
//
	/**
	 *
	 * @return string
	 *
	 * @throws Exception
	 *
	 * @since 2.0.0
	 */
	static function getForumLink(): string
	{
		$lang = Factory::getApplication()->getLanguage();

		$lang_ver		= substr($lang->getTag(), 0, 2);
		if ($lang_ver != 'de')
		{
			$link   = "https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html";
		}
		else
		{
			$link   = "https://www.boldt-webservice.de/de/forum/forum/bwpostman.html";
		}

		return $link;
	}

	/**
	 *
	 * @param string $section
	 *
	 * @return JButtonExtlink
	 *
	 * @throws Exception
	 *
	 * @since 2.4.0
	 */
	static function getManualButton(string $section): JButtonExtlink
	{
		$manualLink = self::getManualLink($section);
		$manualOptions = array('url' => $manualLink, 'icon-class' => 'book', 'idName' => 'manual', 'toolbar-class' => 'ms-auto');

		return new JButtonExtlink('Extlink', Text::_('COM_BWPOSTMAN_MANUAL'), $manualOptions);
	}

	/**
	 *
	 * @return JButtonExtlink
	 *
	 * @throws Exception
	 *
	 * @since 2.4.0
	 */
	static function getForumButton(): JButtonExtlink
	{
		$forumLink  = self::getForumLink();
		$forumOptions  = array('url' => $forumLink, 'icon-class' => 'users', 'idName' => 'forum');

		return new JButtonExtlink('Extlink', Text::_('COM_BWPOSTMAN_FORUM'), $forumOptions);
	}

	/**
	 *
	 * @param string $section
	 *
	 * @return string
	 *
	 * @throws Exception
	 *
	 * @since 2.2.0
	 */
	static function getManualLink(string $section): string
	{
		$lang = Factory::getApplication()->getLanguage();
		$sectionPart = "handbuch-zu-bwpostman.html";

		$lang_ver		= substr($lang->getTag(), 0, 2);
		if ($lang_ver != 'de')
		{
			$baseLink = "https://www.boldt-webservice.de/index.php/en/forum-en/manuals/";

			switch ($section)
			{
				case 'bwpostman':
					$sectionPart = "bwpostman-manual.html";
					break;
				case 'archive':
					$sectionPart = "bwpostman-manual/69-bwpostman-manual-advanced-use.html";
					break;
				case 'maintenance':
					$sectionPart = "bwpostman-manual/71-bwpostman-manual-maintenance.html";
					break;
				case 'campaign':
				case 'campaigns':
					$sectionPart = "bwpostman-manual/69-bwpostman-manual-advanced-use.html?start=1";
					break;
				case 'newsletter':
				case 'newsletters':
					$sectionPart = "bwpostman-manual/67-bwpostman-manual-the-back-end.html?start=3";
					break;
				case 'mailinglist':
				case 'mailinglists':
					$sectionPart = "bwpostman-manual/67-bwpostman-manual-the-back-end.html?start=1";
					break;
				case 'subscriber':
				case 'subscribers':
					$sectionPart = "bwpostman-manual/67-bwpostman-manual-the-back-end.html?start=2";
					break;
				case 'template':
				case 'templates':
					$sectionPart = "bwpostman-manual/83-templates-adjusting-the-appearance-of-a-newsletter.html";
					break;
			}
		}
		else
		{
			$baseLink = "https://www.boldt-webservice.de/index.php/de/forum/handb&#252;cher/";

			switch ($section)
			{
				case 'bwpostman':
					$sectionPart = "handbuch-zu-bwpostman.html";
					break;
				case 'archive':
					$sectionPart = "handbuch-zu-bwpostman/56-bwpostman-handbuch-fortgeschrittene-benutzung.html?start=4";
					break;
				case 'maintenance':
					$sectionPart = "handbuch-zu-bwpostman/58-bwpostman-handbuch-wartung.html";
					break;
				case 'campaign':
				case 'campaigns':
					$sectionPart = "handbuch-zu-bwpostman/56-bwpostman-handbuch-das-backend.html?start=1";
					break;
				case 'newsletter':
				case 'newsletters':
					$sectionPart = "handbuch-zu-bwpostman/54-bwpostman-handbuch-das-backend.html?start=3";
					break;
				case 'mailinglist':
				case 'mailinglists':
					$sectionPart = "handbuch-zu-bwpostman/54-bwpostman-handbuch-das-backend.html?start=1";
					break;
				case 'subscriber':
				case 'subscribers':
					$sectionPart = "handbuch-zu-bwpostman/54-bwpostman-handbuch-das-backend.html?start=2";
					break;
				case 'template':
				case 'templates':
					$sectionPart = "handbuch-zu-bwpostman/82-templates-anpassen-des-erscheinungsbildes-der-newsletter.html";
					break;
			}
		}

		return $baseLink . $sectionPart;
	}
}
