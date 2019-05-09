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

defined ('_JEXEC') or die ();

/**
 * Class BwPostmanHTMLHelper
 *
 * @since
 */
abstract class BwPostmanHTMLHelper {

	/**
	 * Method to create a checkbox for a grid row. Expanded version of Joomla grid.
	 *
	 * @param	integer		$rowNum			The row index
	 * @param	integer		$recId			The record id
	 * @param	boolean		$checkedOut		True if item is checked out
	 * @param	string		$name			The name of the form element
	 * @param	string		$id_name		The id-name of the form element
	 *
	 * @return	mixed		string of html with a checkbox if item is not checked out, null if checked out.
	 */
/*	public static function id($rowNum, $recId, $checkedOut = false, $name = 'cid', $id_name = 'cb')
	{
		if ($checkedOut)
		{
			return '';
		}
		else
		{
			return '<input type="checkbox" id="' . $id_name . $rowNum . '" name="' . $name . '[]" value="' . $recId
				. '" onclick="Joomla.isChecked(this.checked);" />';
		}
	}
*/
	/**
	 * Creates the buttons view at the start page
	 * --> from administrator/mod_quickicon/mod_quickicon.php
	 *
	 * @access	public
	 * @param	string	$link       URL target
	 * @param	string	$image      Image path
	 * @param	string	$text       Image description
	 * @param	int		$x_size     x_size
	 * @param	int		$y_size     y_size
	 * @param 	string	$target     target
	 * @param	string	$onclick    onclick action
	 * @param   boolean $closable   modal window closeable
	 *
	 * @since
	 */
	public static function quickiconButton($link, $image, $text, $x_size = 0, $y_size = 0, $target = '', $onclick = '', $closable = true)
	{
		$lang = JFactory::getLanguage();
		$closable = $closable != true ? ', closable: false' : '';
		($x_size && $y_size) ? $modal_text	= 'class="modal" rel="{handler: \'iframe\', size: {x: ' . $x_size . ', y: ' . $y_size . '}' . $closable . '}"' : $modal_text	= '';
		?>
		<div class="btn text-center" style="float:<?php echo ($lang->isRtl()) ? 'right' : 'left'; ?>;">
			<div class="icon" >
				<a href="<?php echo $link; ?>" <?php if ($target != '') echo 'target="' . $target . '"'; ?> <?php if ($onclick != '') echo 'onclick="' . $onclick . '"'; ?> <?php echo $modal_text; ?>> <?php echo JHtml::_('image', 'administrator/components/com_bwpostman/assets/images/'.$image, $text); ?>
					<span><?php echo $text; ?></span>
				</a>
			</div>
		</div>

		<?php
	}
	/**
	 * Method to get the export fields list
	 *
	 * @access	public
	 *
	 * @return 	string	export fields list, html select list multiple
	 *
	 * @since
	 */
	static public function getExportFieldsList()
	{
		$_db			= JFactory::getDbo();
		$export_fields	= array();

		$query = "SHOW COLUMNS FROM {$_db->quoteName('#__bwpostman_subscribers')}
			WHERE {$_db->quoteName('Field')} NOT IN (
				{$_db->quote('activation')},
				{$_db->quote('editlink')},
				{$_db->quote('checked_out')},
				{$_db->quote('checked_out_time')})"
			;

		$_db->setQuery($query);

		try
		{
			$columns = $_db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}

		foreach ($columns AS $column)
		{
			$export_fields[] = JHtml::_('select.option', $column->Field, $column->Field);
		}

		$export_list	= JHtml::_('select.genericlist', $export_fields, 'export_fields[]', 'class="inputbox" size="20" multiple="multiple" style="padding: 6px; width: 260px;"', 'value', 'text');

		return $export_list;
	}

	/**
	 * Method to get the file format list
	 *
	 * @access	public
	 *
	 * @param	string	$selected
	 *
	 * @return 	string	file format list, html select list
	 *
	 * @since
	 */
	static public function getFileFormatList($selected = '')
	{
		$fileformat 	= array();

		$fileformat[] 	= JHtml::_('select.option', 'csv', JText::_('COM_BWPOSTMAN_CSV'));
		$fileformat[] 	= JHtml::_('select.option', 'xml', JText::_('COM_BWPOSTMAN_XML'));
		$format_list	= JHtml::_('select.radiolist', $fileformat, 'fileformat', 'class="inputbox"', 'value', 'text', $selected);

		return $format_list;
	}

	/**
	 * Method to get the delimiter list
	 *
	 * @access	public
	 *
	 * @param	string	$selected
	 *
	 * @return 	string	delimiter list, html select list
	 *
	 * @since
	 */
	static public function getDelimiterList($selected = ';')
	{
		$delimiter	= array();

		$delimiter[] = JHtml::_('select.option', ',', JText::_('COM_BWPOSTMAN_SUB_DELIMITER_COMMA'));
		$delimiter[] = JHtml::_('select.option', ';', JText::_('COM_BWPOSTMAN_SUB_DELIMITER_SEMICOLON'));
		$delimiter[] = JHtml::_('select.option', '\t', JText::_('COM_BWPOSTMAN_SUB_DELIMITER_TABULATOR'));
		$delimiter[] = JHtml::_('select.option', ' ', JText::_('COM_BWPOSTMAN_SUB_DELIMITER_WHITESPACE'));

		$delimiter_list	= JHtml::_('select.genericlist', $delimiter, 'delimiter', 'class="inputbox" size="1"', 'value', 'text', $selected);

		return $delimiter_list;
	}

	/**
	 * Method to get the enclosure list
	 *
	 * @access	public
	 *
	 * @param	string	$selected
	 *
	 * @return 	string	enclosure list, html select list
	 *
	 * @since
	 */
	static public function getEnclosureList($selected = '"')
	{
		$enclosure	= array();

		$enclosure[] = JHtml::_('select.option', '', JText::_('COM_BWPOSTMAN_SUB_EXPORT_ENCLOSURE_NOSEPARATION'));
		$enclosure[] = JHtml::_('select.option', "'", JText::_('COM_BWPOSTMAN_SUB_EXPORT_ENCLOSURE_QUOTE'));
		$enclosure[] = JHtml::_('select.option', '"', JText::_('COM_BWPOSTMAN_SUB_EXPORT_ENCLOSURE_DOUBLEQUOTE'));

		$enclosure_list	= JHtml::_('select.genericlist', $enclosure, 'enclosure', 'class="inputbox" size="1"', 'value', 'text', $selected);

		return $enclosure_list;
	}

	/**
	 * Method to get the mail format list
	 *
	 * @access	public
	 *
	 * @param	string	$selected
	 *
	 * @return 	string	mail format list, html select list
	 *
	 * @since
	 */
	static public function getMailFormatList($selected = '1')
	{
		$emailformat 	= array();

		$emailformat[] 	= JHtml::_('select.option', '0', JText::_('COM_BWPOSTMAN_TEXT'));
		$emailformat[] 	= JHtml::_('select.option', '1', JText::_('COM_BWPOSTMAN_HTML'));
		$format_list	= JHtml::_('select.radiolist', $emailformat, 'emailformat', 'class="inputbox" ', 'value', 'text', $selected);

		return $format_list;
	}

	/**
	 * Method to get the database fields list
	 *
	 * @access	public
	 *
	 * @return 	string	database fields list, html select list
	 *
	 * @since
	 */
	static public function getDbFieldsList()
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
			$db_fields[] = JHtml::_('select.option', $column, $column);
		}
		$db_fields = JHtml::_('select.genericlist', $db_fields, 'db_fields[]', 'class="inputbox" size="10" multiple="multiple" style="padding: 6px; width: 240px;"', 'value', 'text');

		return $db_fields;
	}

	/**
	 * Method to get the mailinglists select list
	 *
	 * @access	public
	 *
	 * @param	array	$mailinglists
	 *
	 * @return 	string	mailinglists select list, html select list
	 *
	 * @since
	 */
	static public function getMlSelectList($mailinglists = array())
	{
		$import_mailinglists	= array();
		$bwp_mailinglist_values = '';

		if (($mailinglists['public']) || ($mailinglists['special']))
		{
			$import_mailinglists[] = JHtml::_('select.option', '- - - - - - - - - - - - - - - - - - - - - - - - - - - -');
			$import_mailinglists[] = JHtml::_('select.option', '- - - '.JText::_('COM_BWPOSTMAN_ML_PUBLIC').' - - -');
			$import_mailinglists[] = JHtml::_('select.option', '- - - - - - - - - - - - - - - - - - - - - - - - - - - -');
			if ($mailinglists['public'])
			{
				$import_mailinglists[] = JHtml::_('select.option', '- '.JText::_('COM_BWPOSTMAN_ML_PUBLIC_PUBLIC').' -');
				foreach ($mailinglists['public'] AS $mailinglist)
				{
					$import_mailinglists[] = JHtml::_('select.option', $mailinglist['id'], $mailinglist['title'] .': '.$mailinglist['description']);
				}
			}
			if ($mailinglists['special'])
			{
				$import_mailinglists[] = JHtml::_('select.option', '- '.JText::_('COM_BWPOSTMAN_ML_PUBLIC_REGISTERED_AND_MORE').' -');
				foreach ($mailinglists['special'] AS $mailinglist)
				{
					$import_mailinglists[] = JHtml::_('select.option', $mailinglist['id'], $mailinglist['title'] .': '.$mailinglist['description']);
				}
			}
		}
		if ($mailinglists['internal'])
		{
			$import_mailinglists[] = JHtml::_('select.option', '- - - - - - - - - - - - - - - - - - - - - - - - - - - -');
			$import_mailinglists[] = JHtml::_('select.option', '- - - '.JText::_('COM_BWPOSTMAN_ML_INTERNAL').' - - -');
			$import_mailinglists[] = JHtml::_('select.option', '- - - - - - - - - - - - - - - - - - - - - - - - - - - -');
			foreach ($mailinglists['internal'] AS $mailinglist)
			{
				$import_mailinglists[] = JHtml::_('select.option', $mailinglist['id'], $mailinglist['title'] .': '.$mailinglist['description']);
			}
		}
		$import_mailinglists	= JHtml::_('select.genericlist', $import_mailinglists, 'import_mailinglists[]', 'class="inputbox" size="10" multiple="multiple" style="padding: 6px; width: 250px;"', 'value', 'text', $bwp_mailinglist_values);
		$import_mailinglists	= str_replace('>-', ' disabled="disabled">-', $import_mailinglists);

		return $import_mailinglists;
	}

	/**
	 *
	 * @return string
	 *
	 * @since 2.0.0
	 */
	static function getForumLink()
	{
		$lang = JFactory::getLanguage();

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
	 * @return string
	 *
	 * @since 2.2.0
	 */
	static function getManualLink($section)
	{
		$lang = JFactory::getLanguage();
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
			$baseLink = "https://www.boldt-webservice.de/index.php/de/forum/handb%C3%BCcher/";

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
		$link   = $baseLink . $sectionPart;

		return $link;
	}

	/**
	 * Returns a switch action a grid
	 * (switches between boolean values)
	 *
	 * @param   integer      $i              The row index
	 * @param   boolean      $value          current value
	 * @param   string       $task           The task to fire
	 * @param   string|array $prefix         An optional task prefix or an array of options
	 * @param   string       $active_title   An optional active tooltip to display if $enable is true
	 * @param   string       $inactive_title An optional inactive tooltip to display if $enable is true
	 * @param   boolean      $tip            An optional setting for tooltip
	 * @param   string       $active_class   An optional active HTML class
	 * @param   string       $inactive_class An optional inactive HTML class
	 * @param   boolean      $translate      An optional setting for translation.
	 * @param   string       $checkbox       An optional prefix for checkboxes.
	 * @param   boolean      $access         An optional setting for access control on the action.
	 *
	 * @return  string  The HTML markup
	 *
	 * @see 	/libraries/cms/html/jgrid.php->action()

	 * @since   2.2.0
	 */
	public static function switchGridValue($i, $value, $task, $prefix = '', $active_title = '', $inactive_title = '',
		$tip = false, $active_class = '', $inactive_class = '', $translate = true, $checkbox = 'cb', $access = true)
	{
		if (is_array($prefix))
		{
			$options        = $prefix;
			$active_title   = array_key_exists('active_title', $options) ? $options['active_title'] : $active_title;
			$inactive_title = array_key_exists('inactive_title', $options) ? $options['inactive_title'] : $inactive_title;
			$tip            = array_key_exists('tip', $options) ? $options['tip'] : $tip;
			$active_class   = array_key_exists('active_class', $options) ? $options['active_class'] : $active_class;
			$inactive_class = array_key_exists('inactive_class', $options) ? $options['inactive_class'] : $inactive_class;
			$value          = array_key_exists('value', $options) ? $options['Value'] : $value;
			$translate      = array_key_exists('translate', $options) ? $options['translate'] : $translate;
			$checkbox       = array_key_exists('checkbox', $options) ? $options['checkbox'] : $checkbox;
			$prefix         = array_key_exists('prefix', $options) ? $options['prefix'] : '';
		}

		if ($tip)
		{
			JHtml::_('bootstrap.tooltip');

			$title = $value ? $active_title : $inactive_title;
			$title = $translate ? JText::_($title) : $title;
			$title = JHtml::_('tooltipText', $title, '', 0);
		}

		if ($value)
		{
			$html[] = '<a class="btn btn-micro ' . $active_class . ($tip ? ' hasTooltip' : '') . '"';
			if ($access)
			{
				$html[] = ' href="javascript:void(0);" onclick="return Joomla.listItemTask(\'' . $checkbox . $i . '\',\'' . $prefix . $task . '\')"';
			}
			$html[] = $tip ? ' title="' . $title . '"' : '';
			$html[] = '>';
			$html[] = '<span class="icon-' . $active_class . '" aria-hidden="true"></span>';
			$html[] = '</a>';
		}
		else
		{
			$html[] = '<a class="btn btn-micro jgrid' . ($tip ? ' hasTooltip' : '') . '"';
			if ($access)
			{
				$html[] = ' href="javascript:void(0);" onclick="return Joomla.listItemTask(\'' . $checkbox . $i . '\',\'' . $prefix . $task . '\')"';
			}
			$html[] = $tip ? ' title="' . $title . '"' : '';
			$html[] = '>';
			$html[] = '<span class="icon-' . $inactive_class . '"></span>';
			$html[] = '</a>';
		}

		return implode($html);
	}
}
