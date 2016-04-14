<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman html helper class for backend.
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

defined ('_JEXEC') or die ();

/**
 * Class BwPostmanHTMLHelper
 */
abstract class BwPostmanHTMLHelper {

	/**
	 * Method to create a checkbox for a grid row. Expanded vesion of Joomla grid.
	 *
	 * @param	integer		$rowNum			The row index
	 * @param	integer		$recId			The record id
	 * @param	boolean		$checkedOut		True if item is checke out
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
	 */
	public static function quickiconButton($link, $image, $text, $x_size = 0, $y_size = 0, $target = '', $onclick = '', $closable = true)
	{
		$lang = JFactory::getLanguage();
		$closable = $closable != true ? ', closable: false' : '';
		($x_size && $y_size) ? $modal_text	= 'class="modal" rel="{handler: \'iframe\', size: {x: ' . $x_size . ', y: ' . $y_size . '}' . $closable . '}"' : $modal_text	= '';
		?>
		<div class="btn text-center" style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon" >
				<a href="<?php echo $link; ?>" <?php if ($target != '') echo 'target="' . $target . '"'; ?> <?php if ($onclick != '') echo 'onclick="' . $onclick . '"'; ?> <?php echo $modal_text; ?>> <?php echo JHTML::_('image', 'administrator/components/com_bwpostman/assets/images/'.$image, $text); ?>
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
	 */
	static public function getExportFieldsList()
	{
		$_db			= JFactory::getDBO();
		$query			= $_db->getQuery(true);
		$export_fields	= array();

		$query = "SHOW COLUMNS FROM {$_db->quoteName('#__bwpostman_subscribers')}
			WHERE {$_db->quoteName('Field')} NOT IN (
				{$_db->Quote('activation')},
				{$_db->Quote('editlink')},
				{$_db->Quote('checked_out')},
				{$_db->Quote('checked_out_time')})"
			;

		$_db->setQuery($query);
		$columns = $_db->loadObjectList();

		foreach ($columns AS $column) {
		$export_fields[] = JHTML::_('select.option', $column->Field, $column->Field);
		}

		$export_list	= JHTML::_('select.genericlist', $export_fields, 'export_fields[]', 'class="inputbox" size="20" multiple="multiple" style="padding: 6px; width: 260px;"', 'value', 'text');

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
	 */
	static public function getFileFormatList($selected = '')
	{
		$fileformat 	= array();

		$fileformat[] 	= JHTML::_('select.option', 'csv', JText::_('COM_BWPOSTMAN_CSV'));
		$fileformat[] 	= JHTML::_('select.option', 'xml', JText::_('COM_BWPOSTMAN_XML'));
		$format_list	= JHTML::_('select.radiolist', $fileformat, 'fileformat', 'class="inputbox"', 'value', 'text', $selected);

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
	 */
	static public function getDelimiterList($selected = ';')
	{
		$delimiter	= array();

		$delimiter[] = JHTML::_('select.option', ',', JTEXT::_('COM_BWPOSTMAN_SUB_DELIMITER_COMMA'));
		$delimiter[] = JHTML::_('select.option', ';', JTEXT::_('COM_BWPOSTMAN_SUB_DELIMITER_SEMICOLON'));
		$delimiter[] = JHTML::_('select.option', '\t', JTEXT::_('COM_BWPOSTMAN_SUB_DELIMITER_TABULATOR'));
		$delimiter[] = JHTML::_('select.option', ' ', JTEXT::_('COM_BWPOSTMAN_SUB_DELIMITER_WHITESPACE'));

		$delimiter_list	= JHTML::_('select.genericlist', $delimiter, 'delimiter', 'class="inputbox" size="1"', 'value', 'text', $selected);

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
	 */
	static public function getEnclosureList($selected = '"')
	{
		$enclosure	= array();

		$enclosure[] = JHTML::_('select.option', '', JTEXT::_('COM_BWPOSTMAN_SUB_EXPORT_ENCLOSURE_NOSEPARATION'));
		$enclosure[] = JHTML::_('select.option', "'", JTEXT::_('COM_BWPOSTMAN_SUB_EXPORT_ENCLOSURE_QUOTE'));
		$enclosure[] = JHTML::_('select.option', '"', JTEXT::_('COM_BWPOSTMAN_SUB_EXPORT_ENCLOSURE_DOUBLEQUOTE'));

		$enclosure_list	= JHTML::_('select.genericlist', $enclosure, 'enclosure', 'class="inputbox" size="1"', 'value', 'text', $selected);

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
	 */
	static public function getMailFormatList($selected = '1')
	{
		$emailformat 	= array();

		$emailformat[] 	= JHTML::_('select.option', '0', JText::_('COM_BWPOSTMAN_TEXT'));
		$emailformat[] 	= JHTML::_('select.option', '1', JText::_('COM_BWPOSTMAN_HTML'));
		$format_list	= JHTML::_('select.radiolist', $emailformat, 'emailformat', 'class="inputbox" ', 'value', 'text', $selected);

		return $format_list;
	}

	/**
	 * Method to get the database fields list
	 *
	 * @access	public
	 *
	 * @return 	string	database fields list, html select list
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

		foreach ($columns AS $column) {
			$db_fields[] = JHTML::_('select.option', $column, $column);
		}
		$db_fields = JHTML::_('select.genericlist', $db_fields, 'db_fields[]', 'class="inputbox" size="10" multiple="multiple" style="padding: 6px; width: 240px;"', 'value', 'text');

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
	 */
	static public function getMlSelectList($mailinglists = array())
	{
		$import_mailinglists	= array();
		$bwp_mailinglist_values = '';

		if (($mailinglists['public']) || ($mailinglists['special'])) {
			$import_mailinglists[] = JHTML::_('select.option', '- - - - - - - - - - - - - - - - - - - - - - - - - - - -');
			$import_mailinglists[] = JHTML::_('select.option', '- - - '.JText::_('COM_BWPOSTMAN_ML_PUBLIC').' - - -');
			$import_mailinglists[] = JHTML::_('select.option', '- - - - - - - - - - - - - - - - - - - - - - - - - - - -');
			if ($mailinglists['public']) {
				$import_mailinglists[] = JHTML::_('select.option', '- '.JText::_('COM_BWPOSTMAN_ML_PUBLIC_PUBLIC').' -');
				foreach ($mailinglists['public'] AS $mailinglist) {
					$import_mailinglists[] = JHTML::_('select.option', $mailinglist['id'], $mailinglist['title'] .': '.$mailinglist['description']);
				}
			}
			if ($mailinglists['special']) {
				$import_mailinglists[] = JHTML::_('select.option', '- '.JText::_('COM_BWPOSTMAN_ML_PUBLIC_REGISTERED_AND_MORE').' -');
				foreach ($mailinglists['special'] AS $mailinglist) {
					$import_mailinglists[] = JHTML::_('select.option', $mailinglist['id'], $mailinglist['title'] .': '.$mailinglist['description']);
				}
			}
		}
		if ($mailinglists['internal']) {
			$import_mailinglists[] = JHTML::_('select.option', '- - - - - - - - - - - - - - - - - - - - - - - - - - - -');
			$import_mailinglists[] = JHTML::_('select.option', '- - - '.JText::_('COM_BWPOSTMAN_ML_INTERNAL').' - - -');
			$import_mailinglists[] = JHTML::_('select.option', '- - - - - - - - - - - - - - - - - - - - - - - - - - - -');
			foreach ($mailinglists['internal'] AS $mailinglist) {
				$import_mailinglists[] = JHTML::_('select.option', $mailinglist['id'], $mailinglist['title'] .': '.$mailinglist['description']);
			}
		}
		$import_mailinglists	= JHTML::_('select.genericlist', $import_mailinglists, 'import_mailinglists[]', 'class="inputbox" size="10" multiple="multiple" style="padding: 6px; width: 250px;"', 'value', 'text', $bwp_mailinglist_values);
		$import_mailinglists	= str_replace('>-', ' disabled="disabled">-', $import_mailinglists);

		return $import_mailinglists;
	}
}
