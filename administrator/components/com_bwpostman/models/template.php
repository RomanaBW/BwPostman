<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman template model for backend.
 *
 * @version 1.3.0 bwpm
 * @package BwPostman-Admin
 * @author Romana Boldt
 * @copyright (C) 2012-2015 Boldt Webservice <forum@boldt-webservice.de>
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

// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die ('Restricted access');

// Import MODEL object class
jimport('joomla.application.component.modeladmin');

// Require helper class
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/helper.php');

/**
 * BwPostman mailinglist model
 * Provides methodes to add and edit mailinglists
 *
 * @package		BwPostman-Admin
 * @subpackage	Mailinglists
 */
class BwPostmanModelTemplate extends JModelAdmin
{
	/**
	 * template ID
	 *
	 * @var int
	 *
	 * @since 1.1.0
	 */
	var $_id = null;

	/**
	 * template data
	 *
	 * @var array
	 *
	 * @since 1.1.0
	 */
	var $_data = null;

	/**
	 * Constructor
	 * Determines the template ID
	 *
	 *
	 * @since 1.1.0
	 */
	public function __construct()
	{
		parent::__construct();

		$jinput	= JFactory::getApplication()->input;
		$array	= $jinput->get('cid',  0, '', 'array');
		$this->setId((int)$array[0]);
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	 *
	 * @since  1.1.0
	*/
	public function getTable($type = 'Templates', $prefix = 'BwPostmanTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to reset the template ID and template data
	 *
	 * @access	public
	 *
	 * @param	int template ID
	 *
	 * @since 1.1.0
	 */
	function setId($id)
	{
		$this->_id	= $id;
		$this->_data	= null;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since	1.1.0
	 */
	protected function canDelete($record)
	{
		$user = JFactory::getUser();

		// Check general delete permission first.
		if ($user->authorise('core.delete', 'com_bwpostman'))
		{
			return true;
		}

		if (!empty($record->id)) {
			// Check specific delete permission.
			if ($user->authorise('core.delete', 'com_bwpostman.templates.' . (int) $record->id))
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since	1.1.0
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		// Check general edit state permission first.
		if ($user->authorise('core.edit.state', 'com_bwpostman'))
		{
			return true;
		}

		if (!empty($record->id)) {
			// Check specific edit state permission.
			if ($user->authorise('core.edit.state', 'com_bwpostman.templates.' . (int) $record->id))
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   1.1.0
	 */
	public function getItem($pk = null)
	{
		$app		= JFactory::getApplication();
		$cid		= $app->getUserState('com_bwpostman.edit.template.id', 0);
		$data		= $app->getUserState('com_bwpostman.edit.template.data', null);
		$jinput		= JFactory::getApplication()->input;
		$form_data	= $jinput->get('jform', '', 'array');

		// no $data and no $form_data - standard
		if (!$data && !$form_data) {
			// Initialise variables.
			if (is_array($cid)) {
				if (!empty($cid)) {
					$cid = $cid[0];
				}
				else {
					$cid = 0;
				}
			}
			(!empty($pk)) ? $pk	= $pk : $pk	= (int) $cid;
			$item	= parent::getItem($pk);
		}
		// if $data from table templates check()
		else if ($data) {
			$item	= new stdClass();
			foreach ($data as $key => $value) $item->$key	= $value;
		}
		else {
			// if §form_data - only when click to button preview
			$item	= new stdClass();
			foreach ($form_data as $key => $value) $item->$key	= $value;
		}

			// preinstalled html and text templates
			if (($item->tpl_id != 0) && ($item->tpl_id != 998)) {
				// Convert the fields to an array.
				if ($item->tpl_id < 999) {
					if (is_string($item->basics)) {
						$registry = new JRegistry;
						$registry->loadString($item->basics);
						$item->basics = $registry->toArray();
					}
				}
				if (is_string($item->header)){
					$registry = new JRegistry;
					$registry->loadString($item->header);
					$item->header = $registry->toArray();
				}
				if (is_string($item->intro)){
					$registry = new JRegistry;
					$registry->loadString($item->intro);
					$item->intro = $registry->toArray();
				}
				if (is_string($item->article)){
					$registry = new JRegistry;
					$registry->loadString($item->article);
					$item->article = $registry->toArray();
				}
				if (is_string($item->footer)){
					$registry = new JRegistry;
					$registry->loadString($item->footer);
					$item->footer = $registry->toArray();
				}
				if (is_string($item->button1)){
					$registry = new JRegistry;
					$registry->loadString($item->button1);
					$item->button1 = $registry->toArray();
				}
				if (is_string($item->button2)){
					$registry = new JRegistry;
					$registry->loadString($item->button2);
					$item->button2 = $registry->toArray();
				}
				if (is_string($item->button3)){
					$registry = new JRegistry;
					$registry->loadString($item->button3);
					$item->button3 = $registry->toArray();
				}
				if (is_string($item->button4)){
					$registry = new JRegistry;
					$registry->loadString($item->button4);
					$item->button4 = $registry->toArray();
				}
				if (is_string($item->button5)){
					$registry = new JRegistry;
					$registry->loadString($item->button5);
					$item->button5 = $registry->toArray();
				}
			}

		// only preinstalled html templates
		if (($item->tpl_id != 0) && ($item->tpl_id != 998) && ($item->tpl_id < 999)) {
			// call function to make html templatepreview
			$this->makePreview($item);
		}

		// usermade html templates
		if ($item->tpl_id == 0) {
			if (is_string($item->article)){
				$registry = new JRegistry;
				$registry->loadString($item->article);
				$item->article = $registry->toArray();
			}

			// call function to make templatepreview
			$this->makePreviewHtml($item);
		}

		// usermade text templates
		if ($item->tpl_id == 998) {
			if (is_string($item->article)) {
				$registry = new JRegistry;
				$registry->loadString($item->article);
				$item->article = $registry->toArray();
			}

			// call function to make templatepreview
			$this->makePreviewText($item);
		}

		// preinstalled text templates
		if ($item->tpl_id > 999) {
			// call function to make templatepreview
			$this->makePreviewTextStd($item);
		}

		return $item;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 *
	 * @since	1.1.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_bwpostman.template', 'template', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		$jinput = JFactory::getApplication()->input;

		// The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
		if ($jinput->get('a_id'))
		{
			$id = $jinput->get('a_id', 0);
		}
		// The back end uses id so we use that the rest of the time and set it to 0 by default.
		else
		{
			$id = $jinput->get('id', 0);
		}

		// Determine correct permissions to check.
		if ($this->getState('template.id'))
		{
			$id = $this->getState('template.id');
			// Existing record. Can only edit in selected parent.
			$form->setFieldAttribute('parent_id', 'action', 'core.edit');
			// Existing record. Can only edit own mailinglists in selected parent.
			$form->setFieldAttribute('parent_id', 'action', 'core.edit.own');
		}
		else
		{
			// New record. Can only create in selected parent.
			$form->setFieldAttribute('parent_id', 'action', 'core.create');
		}

		$user = JFactory::getUser();

		// Check for existing mailinglist.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (!$user->authorise('core.edit.state', 'com_bwpostman.template.'.(int) $id))
		|| ($id == 0 && !$user->authorise('core.edit.state', 'com_bwpostman'))
		)
		{
			// Disable fields for display.
			$form->setFieldAttribute('state', 'disabled', 'true');
			// Disable fields while saving.
			// The controller has already verified this is a mailinglist you can edit.
			$form->setFieldAttribute('state', 'filter', 'unset');

		}

		// Check to show created data
		$c_date	= $form->getValue('created_date');
		if ($c_date == '0000-00-00 00:00:00') {
			$form->setFieldAttribute('created_date', 'type', 'hidden');
			$form->setFieldAttribute('created_by', 'type', 'hidden');
		}

		// Check to show modified data
		$m_date	= $form->getValue('modified_time');
		if ($m_date == '0000-00-00 00:00:00') {
			$form->setFieldAttribute('modified_time', 'type', 'hidden');
			$form->setFieldAttribute('modified_by', 'type', 'hidden');
		}
		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 *
	 * @since	1.1.0
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_bwpostman.template.edit.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}

	/**
	 * Method to (un)archive a template
	 * --> when unarchiving it is called by the archive-controller
	 *
	 * @access	public
	 *
	 * @param	array template IDs
	 * @param	tinyint Task --> 1 = archive, 0 = unarchive
	 *
	 * @return	boolean
	 *
	 * @since 1.1.0
	 */
	public function archive($cid = array(), $archive = 1)
	{
		$_db	= $this->_db;
		$app	= JFactory::getApplication();
		$date	= JFactory::getDate();
		$uid	= JFactory::getUser()->get('id');

		if ($archive == 1) {
			$time = $date->toSql();

			// Access check.
			foreach ($cid as $i) {
				if (!BwPostmanHelper::allowArchive($i, 0, 'template'))
				{
					$app->enqueueMessage(JText::_('COM_BWPOSTMAN_TPL_ARCHIVE_RIGHTS_MISSING'), 'error');
					return false;
				}
			}
		}
		else {
			$time	= '0000-00-00 00:00:00';
			$uid	= 0;

			// Access check.
			foreach ($cid as $i) {
				if (!BwPostmanHelper::allowRestore($i, 0, 'template'))
				{
					$app->enqueueMessage(JText::_('COM_BWPOSTMAN_TPL_RESTORE_RIGHTS_MISSING'), 'error');
					return false;
				}
			}
		}

		if (count($cid))
		{
			JArrayHelper::toInteger($cid);
			$query	= $_db->getQuery(true);

			$query->update($_db->quoteName('#__bwpostman_templates'));
			$query->set($_db->quoteName('archive_flag') . " = " . $_db->Quote((int) $archive));
			$query->set($_db->quoteName('archive_date') . " = " . $_db->Quote($time, false));
			$query->set($_db->quoteName('archived_by') . " = " . (int) $uid);
			$query->where($_db->quoteName('id') . ' IN (' .implode(',', $cid) . ')');

			$_db->setQuery($query);

			if (!$_db->query()) {
				$this->setError($_db->getErrorMsg());
				return false;
			}
		}
		return true;
	}

	/**
	 * Method to remove one or more templates
	 * --> is called by the archive-controller
	 *
	 * @access	public
	 *
	 * @param	array template IDs
	 *
	 * @return	boolean
	 *
	 * @since 1.1.0
	 */
	public function delete(&$pks)
	{
		$app	= JFactory::getApplication();
		$result = false;

		// Access check.
		foreach ($pks as $i) {
			if (!BwPostmanHelper::allowDelete($i, 0, 'template'))
			{
				return false;
			}
		}

		if (count($pks))
		{
			JArrayHelper::toInteger($pks);
			$_db	= $this->getDbo();

			$lists_table	= JTable::getInstance('templates', 'BwPostmanTable');

			// Delete all entries from the templates-table
			foreach ($pks as $id) {
				if (!$lists_table->delete($id))
				{
					$app->enqueueMessage(JText::_('COM_BWPOSTMAN_ARC_ERROR_REMOVING_TPLS_NO_TPL_DELETED'), 'error');
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Method to set a template as default.
	 *
	 * @param   integer  $id  The primary key ID for the style.
	 *
	 * @return  boolean  True if successful.
	 *
	 * @throws	Exception
	 *
	 * @since 1.1.0
	 */
	public function setHome($id = 0)
	{
		$user = JFactory::getUser();
		$db   = $this->getDbo();

		// Access checks.
		if (!$user->authorise('core.edit.state', 'com_bwpostman'))
		{
			throw new Exception(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
		}

		$template = JTable::getInstance('Templates', 'BwPostmanTable');

		if (!$template->load((int) $id))
		{
			throw new Exception(JText::_('COM_BWPOSTMAN_ERROR_TEMPLATE_NOT_FOUND'));
		}

		// Reset the standard fields for the templates.
		if ($template->tpl_id < 988) {
			$db->setQuery(
					'UPDATE #__bwpostman_templates' .
					' SET standard = \'0\'' .
					' WHERE tpl_id < \'988\'' .
					' AND standard = \'1\''
			);
		}
		else {
			$db->setQuery(
					'UPDATE #__bwpostman_templates' .
					' SET standard = \'0\'' .
					' WHERE tpl_id > \'987\'' .
					' AND standard = \'1\''
			);
		}
		$db->execute();

		// Set the new home style.
		$db->setQuery(
				'UPDATE #__bwpostman_templates' .
				' SET standard = \'1\'' .
				' WHERE id = ' . (int) $id
		);
		$db->execute();

		return true;
	}

  	/**
	 * Method to make the template
	 *
	 * @access	public
	 *
	 * @return 	$html
	 *
	 * @since 1.1.0
	 */
	public function makeTemplate($item, $tpl)
	{
		// replace placeholders
		switch ($item->header['header_style']) {
			case 'logo_with_text':
				$header = $tpl->header_tpl['no_header'];
				$header .= $tpl->header_tpl['logo_with_text'];
				if ($item->header['alignment'] != 'right') {
					$header = str_replace('[%left%]', $tpl->header_tpl['header_image'], $header);
					$header = str_replace('[%right%]', $tpl->header_tpl['header_text'], $header);
				}
				else {
					$header = str_replace('[%right%]', $tpl->header_tpl['header_image'], $header);
					$header = str_replace('[%left%]', $tpl->header_tpl['header_text'], $header);
				}
				$header = str_replace('[%alignment%]', 'center', $header);
				$header = $item->header['logo_src'] != '' ? str_replace('[%logo_src%]', JURI::root() . $item->header['logo_src'], $header) : str_replace('[%logo_src%]', JURI::root() . '/administrator/components/com_bwpostman/assets/images/260x130.png' , $header);
				$header = $item->header['logo_width'] == '' ? str_replace('[%logo_width%]', 260, $header) : str_replace('[%logo_width%]', $item->header['logo_width'], $header);
				$header = str_replace('[%header_href%]', JURI::root(), $header);
				$header = str_replace('[%header_firstline%]', $item->header['firstline'], $header);
				$header = str_replace('[%header_size_firstline%]', $item->header['size_firstline'], $header);
				$header = str_replace('[%firstlineheight%]', ceil($item->header['size_firstline']*1.2), $header);
				$header = str_replace('[%header_secondline%]', $item->header['secondline'], $header);
				$header = str_replace('[%header_size_secondline%]', $item->header['size_secondline'], $header);
				$header = str_replace('[%secondlineheight%]', ceil($item->header['size_secondline']*1.2), $header);
				break;
			case 'only_text':
				$header = $tpl->header_tpl['no_header'];
				$header .= $tpl->header_tpl['only_text'];
				$header = str_replace('[%left%]', $tpl->header_tpl['header_text'], $header);
				$header = str_replace('[%alignment%]', $item->header['alignment'], $header);
				$header = str_replace('[%header_href%]', JURI::root(), $header);
				$header = str_replace('[%header_firstline%]', $item->header['firstline'], $header);
				$header = str_replace('[%header_size_firstline%]', $item->header['size_firstline'], $header);
				$header = str_replace('[%firstlineheight%]', ceil($item->header['size_firstline']*1.2), $header);
				$header = str_replace('[%header_secondline%]', $item->header['secondline'], $header);
				$header = str_replace('[%header_size_secondline%]', $item->header['size_secondline'], $header);
				$header = str_replace('[%secondlineheight%]', ceil($item->header['size_secondline']*1.2), $header);
				break;
			case 'only_logo':
				$header = $tpl->header_tpl['no_header'];
				$header .= $tpl->header_tpl['only_logo'];
				$header = str_replace('[%left%]', $tpl->header_tpl['header_image'], $header);
				$header = str_replace('[%alignment%]', $item->header['alignment'], $header);
				$header = $item->header['logo_src'] != '' ? str_replace('[%logo_src%]', JURI::root() . $item->header['logo_src'], $header) : str_replace('[%logo_src%]', JURI::root() . '/administrator/components/com_bwpostman/assets/images/580x130.png' , $header);
				$header = $item->header['logo_width'] == '' ? str_replace('[%logo_width%]', 580, $header) : str_replace('[%logo_width%]', $item->header['logo_width'], $header);
				$header = str_replace('[%header_href%]', JURI::root(), $header);
				break;
			case 'no_header':
				$header = $tpl->header_tpl['no_header'];
				break;
		}

		if ($item->intro['show_intro'] == 1) {
			$intro = $tpl->intro_tpl;
			if ($item->article['divider'] == 1) $intro .= $tpl->divider_tpl;
		}
		else {
			$intro = '          <div class="spacer" style="font-size: 1px;line-height: 20px">&nbsp;</div>';
		}
		$footer = $tpl->footer_tpl;
		$footer = $item->footer['show_address'] != 1 ? str_replace('[%address_text%]', '', $footer) : str_replace('[%address_text%]', nl2br($item->footer['address_text'], true), $footer);
		// buttons
		$i = 1;
		$button = '';
		while ($i <= 5) :
			$obj = 'button' . $i;
			if ($item->{$obj}['show_button'] == 1) :
				${'button'.$i} = $tpl->button_tpl;
				${'button'.$i} = str_replace('[%button_href%]', $item->{$obj}['button_href'], ${'button'.$i});
				${'button'.$i} = str_replace('[%button_text%]', $item->{$obj}['button_text'], ${'button'.$i});
				${'button'.$i} = str_replace('[%button_bg%]', $item->{$obj}['button_bg'], ${'button'.$i});
				${'button'.$i} = str_replace('[%button_shadow%]', $item->{$obj}['button_shadow'], ${'button'.$i});
				${'button'.$i} = str_replace('[%button_color%]', $item->{$obj}['button_color'], ${'button'.$i});
			endif;
			if (isset(${'button'.$i})) $button .= ${'button'.$i};
				$i++;
		endwhile;
		$footer	= str_replace('[%button%]', $button, $footer);
		$footer	= trim($button) != '' ? str_replace('[%button_headline%]', $item->footer['button_headline'], $footer) : str_replace('[%button_headline%]', '', $footer);

		$html	= $header . $intro . '[%content%]' . $footer;

		// replace [%zoom_padding%]
		$zp		= $item->basics['zoom_padding'];
		$html	= preg_replace_callback(
					'/\[\%zoom_padding[1-2]?[0-9]?[0-9]\%\]/',
					function($treffer) use ($zp) {
						preg_match('/\[\%zoom_padding([1-2]?[0-9]?[0-9])\%\]/', $treffer[0], $px);
						$treffer[0] = ceil($zp*$px[1]);
						return $treffer[0];
					},
				$html
				);
		// replace [%zoom%]
		$z		= $item->basics['zoom'];
		$html	= preg_replace_callback(
					'/\[\%zoom[1-2]?[0-9]?[0-9]\%\]/',
					function($treffer) use ($z) {
						preg_match('/\[\%zoom([1-2]?[0-9]?[0-9])\%\]/', $treffer[0], $px);
						$treffer[0] = ceil($z*$px[1]);
						return $treffer[0];
					},
					$html
			);

		// replace basics
		$html	= str_replace('[%width620%]', intval($item->basics['nl_width'])+20, $html);
		$html	= str_replace('[%width600%]', intval($item->basics['nl_width']), $html);
		$html	= str_replace('[%width310%]', intval($item->basics['nl_width']/2+10), $html);
		$html	= str_replace('[%width300%]', intval($item->basics['nl_width']/2), $html);
		$html	= str_replace('[%width270%]', intval($item->basics['nl_width']/2-30), $html);
		$html	= str_replace('[%width200%]', intval($item->basics['nl_width']/3), $html);
		$html	= str_replace('[%paper_bg%]', $item->basics['paper_bg'], $html);
		$html	= str_replace('[%article_bg%]', $item->basics['article_bg'], $html);
		$html	= str_replace('[%headline_color%]', $item->basics['headline_color'], $html);
		$html	= str_replace('[%content_color%]', $item->basics['content_color'], $html);
		$html	= str_replace('[%legal_color%]', $item->basics['legal_color'], $html);

		$html	= str_replace('[%header_bg%]', $item->header['header_bg'], $html);
		$html	= str_replace('[%header_shadow%]', $item->header['header_shadow'], $html);
		$html	= str_replace('[%header_color%]', $item->header['header_color'], $html);

		$html	= str_replace('[%divider_color%]', $item->article['divider_color'], $html);

		$html	= str_replace('[%footer_bg%]', $item->footer['footer_bg'], $html);
		$html	= str_replace('[%footer_shadow%]', $item->footer['footer_shadow'], $html);
		$html	= str_replace('[%footer_color%]', $item->footer['footer_color'], $html);

		return $html;
	}

	/**
	 * Method to make the texttemplate
	 *
	 * @access	public
	 * @return 	$html
	 *
	 * @since 1.1.0
	 */
	public function makeTexttemplate($item, $tpl)
	{
		$header = $tpl->header_tpl['logo_with_text'];
		$header = str_replace('[%header_firstline%]', $item->header['firstline'], $header);
		$header = str_replace('[%header_secondline%]', $item->header['secondline'], $header);

		if ($item->intro['show_intro'] == 1) {
			$intro = $tpl->intro_tpl;
			$intro .= $tpl->divider_tpl."\n";
		}
		else {
			$intro = '';
		}

		$footer	= $tpl->footer_tpl;
		$footer	= $item->footer['show_address'] != 1 ? str_replace('[%address_text%]', '', $footer) : str_replace('[%address_text%]', $item->footer['address_text'], $footer);
		// buttons
		$i = 1;
		$button = '';
		while ($i <= 5) :
			$obj = 'button' . $i;
			if ($item->{$obj}['show_button'] == 1) :
				${'button'.$i} = $tpl->button_tpl;
				${'button'.$i} = str_replace('[%button_text%]', $item->{$obj}['button_text'], ${'button'.$i});
				${'button'.$i} = str_replace('[%button_href%]', $item->{$obj}['button_href']."\n", ${'button'.$i});
			endif;
			if (isset(${'button'.$i})) $button .= ${'button'.$i};
			$i++;
		endwhile;
		$footer = str_replace('[%button%]', $button, $footer);
		$footer = trim($button) != '' ? str_replace('[%button_headline%]', $item->footer['button_headline'], $footer) : str_replace('[%button_headline%]', '', $footer);

		$html = $header . $intro . '[%content%]' . $footer;

		return $html;
	}

	/**
	 * Method to make button readon template
	 *
	 * @access	public
	 *
	 * @return 	$tpl
	 *
	 * @since 1.1.0
	 */
	public function makeButton(&$tpl, &$item)
	{
		$tpl = $this->replaceZooms($tpl, $item);
		$tpl = str_replace('[%readon_color%]', $item->article['readon_color'], $tpl);
		$tpl = str_replace('[%readon_bg%]', $item->article['readon_bg'], $tpl);
		$tpl = str_replace('[%readon_shadow%]', $item->article['readon_shadow'], $tpl);

		return $tpl;
	}

	/**
	 * Method to add the HTML-Tags and the css for templatepreview
	 *
	 * @access	public
	 *
	 * @return 	$text
	 *
	 * @since 1.1.0
	 */
	public function _addHtmlTags (&$text, &$css, &$basics)
	{
		// get legal info an ItemId's
		$params 			= JComponentHelper::getParams('com_bwpostman');
		$impressum			= "<br /><br />" . $params->get('legal_information_text')."<br /><br />";
		$impressum			= nl2br($impressum, true);

		// replace edit and unsubscribe link
		$replace1	= '<a href="[UNSUBSCRIBE_HREF]">' . JText::_('COM_BWPOSTMAN_TPL_UNSUBSCRIBE_LINK_TEXT') . '</a>';
		$text		= str_replace('[%unsubscribe_link%]', $replace1, $text);
		$replace2	= '<a href="[EDIT_HREF]">' . JText::_('COM_BWPOSTMAN_TPL_EDIT_LINK_TEXT') . '</a>';
		$text		= str_replace('[%edit_link%]', $replace2, $text);

		if (strpos($text, '[%impressum%]') !== false) {
			// replace [%impressum%]
			$replace3  = '   <table id="legal" cellspacing="0" cellpadding="0" border="0" style="table-layout: fixed; width: 100%;"><tbody>';
			$replace3 .= '     <tr>'."\n";
			$replace3 .= '       <td id="legal_td">'."\n";
			$replace3 .= '         <table class="one-col legal" style="border-collapse: collapse;border-spacing: 0;"><tbody>'."\n";
			$replace3 .= '          <tr>'."\n";
			$replace3 .= '           <td class="legal_td">'."\n";
			$replace3 .= "<br /><br />" . JText::sprintf('COM_BWPOSTMAN_NL_FOOTER_HTML', JURI::root(true));
			$replace3 .= $impressum."<br /><br />\n";
			$replace3 .= '           </td>'."\n";
			$replace3 .= '          </tr>'."\n";
			$replace3 .= '         </tbody></table>'."\n";
			$replace3 .= '       </td>'."\n";
			$replace3 .= '     </tr>'."\n";
			$replace3 .= '   </tbody></table>'."\n";
			$text = str_replace('[%impressum%]', $replace3, $text);
		}
//		else {
//			$replace = JText::_('COM_BWPOSTMAN_NL_FOOTER_HTML_LINE') . JText::sprintf('COM_BWPOSTMAN_NL_FOOTER_HTML', JURI::root(true), JURI::root(true), $itemid_unsubscribe, JURI::root(true), $itemid_edit) . $impressum;
//			$text .= "<div class=\"footer-outer\"><p class=\"footer-inner\">{$replace}</p></div></body>";
//		}

		$newtext = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
		$newtext .= '<html>'."\n";
		$newtext .= ' <head>'."\n";
		$newtext .= '   <title>Newsletter</title>'."\n";
		$newtext .= '   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'."\n";
		$newtext .= '   <meta name="robots" content="noindex,nofollow" />'."\n";
		$newtext .= '   <meta property="og:title" content="HTML Newsletter" />'."\n";
		$newtext .= '   <style type="text/css">'."\n";
		$newtext .= '   '.$css."\n";
		$newtext .= '   </style>'."\n";
		$newtext .= ' </head>'."\n";
			if (isset($basics['paper_bg'])) {
			$newtext .= ' <body bgcolor="'. $basics['paper_bg'] .'" emb-default-bgcolor="'. $basics['paper_bg'] .'" style="background-color:'. $basics['paper_bg'] .';color:'. $basics['legal_color'] .';">'."\n";
		}
		else {
			$newtext .= ' <body bgcolor="#ffffff" emb-default-bgcolor="#ffffff">'."\n";
		}
		$newtext .= $text."\n";
		$newtext .= ' </body>'."\n";
		$newtext .= '</html>'."\n";

		$text = $newtext;

		return $text;
	}

	/**
	 * Method to add sample article for templatepreview
	 *
	 * @access	private
	 *
	 * @return 	$article
	 *
	 * @since 1.1.0
	 */
	private function _sampleArticle(&$article, &$item)
	{
		$article = str_replace('[%content_title%]', JText::_('COM_BWPOSTMAN_DISCLAIMER_ARTICLE'), $article);
		$sample_content = "\n";
		if (($item['show_createdate'] == 1) || ($item['show_author'] == 1)) :
			$sample_content .= '<p>';
			if ($item['show_createdate'] == 1) :
				$sample_content .= '<span><small>';
				$sample_content .= JText::sprintf('COM_CONTENT_CREATED_DATE_ON', JHtml::_('date', JFactory::getDate(), JText::_('DATE_FORMAT_LC2')));
				$sample_content .= '&nbsp;&nbsp;&nbsp;&nbsp;</small></span>';
			endif;
			if ($item['show_author'] == 1) :
				$sample_content .= '<span><small>';
				$sample_content .= JText::sprintf('COM_CONTENT_WRITTEN_BY', 'Anonymus');
				$sample_content .= '</small></span>';
			endif;
			$sample_content .= '</p>'."\n\n";
		endif;
		$sample_content .= 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum vitae sollicitudin quam donec at mattis orci. Phasellus quam nulla, fringilla ut condimentum vel eros.';
		if ($item['show_readon'] == 1) $sample_content .= "\n";
		$article = str_replace('[%content_text%]', $sample_content, $article);
		$article = str_replace('[%readon_text%]', JText::_('READ_MORE'), $article);

		return $article;
	}

	/**
	 * Method to replace zoom and zoom_padding and basics
	 *
	 * @access	public
	 *
	 * @return 	$text
	 *
	 * @since 1.1.0
	 */
	public function replaceZooms(&$text, &$item)
	{
		// replace [%zoom_padding%]
		$zp		= $item->basics['zoom_padding'];
		$text	= preg_replace_callback(
					'/\[\%zoom_padding[1-2]?[0-9]?[0-9]\%\]/',
					function($treffer) use ($zp) {
						preg_match('/\[\%zoom_padding([1-2]?[0-9]?[0-9])\%\]/', $treffer[0], $px);
						$treffer[0] = ceil($zp*$px[1]);
						return $treffer[0];
					},
					$text
			);
		// replace [%zoom%]
		$z		= $item->basics['zoom'];
		$text	= preg_replace_callback(
					'/\[\%zoom[1-2]?[0-9]?[0-9]\%\]/',
					function($treffer) use ($z) {
						preg_match('/\[\%zoom([1-2]?[0-9]?[0-9])\%\]/', $treffer[0], $px);
						$treffer[0] = ceil($z*$px[1]);
						return $treffer[0];
					},
					$text
			);

		// replace basics
		$text	= str_replace('[%width620%]', intval($item->basics['nl_width']+20), $text);
		$text	= str_replace('[%width600%]', intval($item->basics['nl_width']), $text);
		$text	= str_replace('[%width310%]', intval($item->basics['nl_width']/2+10), $text);
		$text	= str_replace('[%width300%]', intval($item->basics['nl_width']/2), $text);
		$text	= str_replace('[%width270%]', intval($item->basics['nl_width']/2-30), $text);
		$text	= str_replace('[%width200%]', intval($item->basics['nl_width']/3), $text);
		$text	= str_replace('[%paper_bg%]', $item->basics['paper_bg'], $text);
		$text	= str_replace('[%article_bg%]', $item->basics['article_bg'], $text);
		$text	= str_replace('[%headline_color%]', $item->basics['headline_color'], $text);
		$text	= str_replace('[%content_color%]', $item->basics['content_color'], $text);
		$text	= str_replace('[%legal_color%]', $item->basics['legal_color'], $text);

		$text	= str_replace('[%header_bg%]', $item->header['header_bg'], $text);
		$text	= str_replace('[%header_shadow%]', $item->header['header_shadow'], $text);
		$text	= str_replace('[%header_color%]', $item->header['header_color'], $text);

		$text	= str_replace('[%readon_bg%]', $item->article['readon_bg'], $text);
		$text	= str_replace('[%readon_shadow%]', $item->article['readon_shadow'], $text);
		$text	= str_replace('[%readon_color%]', $item->article['readon_color'], $text);

		$text	= str_replace('[%divider_color%]', $item->article['divider_color'], $text);

		$text	= str_replace('[%footer_bg%]', $item->footer['footer_bg'], $text);
		$text	= str_replace('[%footer_shadow%]', $item->footer['footer_shadow'], $text);
		$text	= str_replace('[%footer_color%]', $item->footer['footer_color'], $text);

		return $text;
	}

	/**
	 * Method to make template preview
	 *
	 * @access	public
	 *
	 * @return
	 *
	 * @since 1.1.0
	 */
	public function makePreview($item)
	{
		// make preview
		// first get templates tpls
		$tpl_id		= $item->tpl_id;
		$tpl_model	= JModelLegacy::getInstance( 'templates_tpl', 'BwPostmanModel' );
		$tpl		= $tpl_model->getItem($tpl_id);

		// make html preview data
		$preview_html = $this->makeTemplate($item, $tpl);

		// make intro preview
		$preview_html = str_replace('[%intro_headline%]', $item->intro['intro_headline'], $preview_html);
		$preview_html = str_replace('[%intro_text%]', nl2br($item->intro['intro_text'], true), $preview_html);

		if ($item->footer['show_impressum'] == 1) $preview_html = $preview_html . '[%impressum%]';

		// make css data
		$preview_css = $this->replaceZooms($tpl->css, $item);

		// make article preview data
		$article = $this->replaceZooms($tpl->article_tpl, $item);
		if ($item->article['show_readon'] == 1) {
			$readon				= $this->makeButton($tpl->readon_tpl, $item);
			$preview_article	= str_replace('[%readon_button%]', $readon, $article);
		}
		else {
			$preview_article = str_replace('[%readon_button%]', '', $article);
		}

		//  set divider preview template and replace placeholder
		$preview_divider = $tpl->divider_tpl;
		$preview_divider = $this->replaceZooms($preview_divider, $item);
		$preview_divider = str_replace('[%divider_color%]', $item->article['divider_color'], $preview_divider);

		$text = $preview_html;
		$preview =  $this->_addHtmlTags($text, $preview_css, $item->basics);
		// load sample article
		$sample_article = $this->_sampleArticle($preview_article, $item->article);
		$sample_article = $item->article['divider'] == 1 ? $sample_article . $preview_divider . $sample_article : $sample_article . $sample_article;

		$preview =  str_replace('[%content%]', $sample_article, $preview);
		JFactory::getApplication()->setUserState('com_bwpostman.edit.template.tpldata', $preview);
		// end make preview
		return;
	}

	/**
	 * Method to make template preview for new html template
	 *
	 * @access	public
	 *
	 * @return
	 *
	 * @since 1.1.0
	 */
	public function makePreviewHtml($item)
	{
		// make preview

		// make html preview data
		$preview_html = $item->tpl_html;
		$this->_replaceLinks($preview_html);

		// make css data
		$preview_css = $item->tpl_css;

		// trim leading and last <style>-tag
		$preview_css = trim($preview_css);
		$preview_css = ltrim($preview_css, '<style type="text/css">');
		$preview_css = rtrim($preview_css, '</style>');

		$text		= $preview_html;
		$preview	= $this->_addHtmlTags($text, $preview_css, $item->basics);

		// make article preview data
		$preview_article = '<div class="intro_text"><h2>[%content_title%]</h2>[%content_text%]</div>';
		if ($item->article['show_readon'] == 1) $preview_article .='<div class="read_on"><p><a href="" class="readon">[%readon_text%]</a><br/><br/></p></div>';

		// load sample article
		$sample_article = $this->_sampleArticle($preview_article, $item->article);
		$sample_article = $sample_article . $sample_article;

		stripslashes($sample_article);

		$preview =  str_replace('[%content%]', $sample_article, $preview);
		JFactory::getApplication()->setUserState('com_bwpostman.edit.template.tpldata', $preview);
		// end make preview
		return;
	}

	/**
	 * Method to make text template preview for own templates
	 *
	 * @access	public
	 *
	 * @return
	 *
	 * @since 1.1.0
	 */
	public function makePreviewText($item)
	{
		$model				= JModelLegacy::getInstance( 'newsletter', 'BwPostmanModel' );
		$itemid_unsubscribe	= $model->getItemid('register');
		$itemid_edit		= $model->getItemid('edit');

		// make preview

		// make text preview data
		$preview_text = nl2br($item->tpl_html);

		// impressum
		if (strpos($preview_text, '[%impressum%]') !== false) {
			$uri  			= JFactory::getURI();
			$params 		= JComponentHelper::getParams('com_bwpostman');
			$impressum		= "<br /><br />" . JText::sprintf('COM_BWPOSTMAN_NL_FOOTER_TEXT', $uri->root(), $uri->root(), $itemid_unsubscribe, $uri->root(), $itemid_edit) . "<br /><br />" . $params->get('legal_information_text')."<br /><br />";
			$preview_text	= str_replace('[%impressum%]', nl2br($impressum, true), $preview_text);
		}

		// replace edit and unsubscribe link
//		$replace1		= '+ ' . JText::_('COM_BWPOSTMAN_TPL_UNSUBSCRIBE_LINK_TEXT') . ' +<br />&nbsp;&nbsp;' . JURI::root(true) . 'index.php?option=com_bwpostman&amp;Itemid='. $itemid_unsubscribe . '&amp;view=register&amp;task=unsubscribe&amp;email=[UNSUBSCRIBE_EMAIL]&amp;code=[UNSUBSCRIBE_CODE]';
		$replace1		= '+ ' . JText::_('COM_BWPOSTMAN_TPL_UNSUBSCRIBE_LINK_TEXT') . ' +<br />&nbsp;&nbsp;' . JURI::root(true) . 'index.php?option=com_bwpostman&amp;Itemid='. $itemid_edit . '&amp;view=edit&amp;task=unsub&amp;editlink=[EDITLINK]';
		$preview_text	= str_replace('[%unsubscribe_link%]', $replace1, $preview_text);
		$replace2		= '+ ' . JText::_('COM_BWPOSTMAN_TPL_EDIT_LINK_TEXT') . ' +<br />&nbsp;&nbsp;' . JURI::root(true) . 'index.php?option=com_bwpostman&amp;Itemid='. $itemid_edit . '&amp;view=edit&amp;editlink=[EDITLINK]';
		$preview_text	= str_replace('[%edit_link%]', $replace2, $preview_text);

		// make article preview data
		$article = "\n".'[%content_title%]'."\n".'[%content_text%]'."\n".'[%readon_button%]'."\n";
		if ($item->article['show_readon'] == 1) {
			$readon				= '[%readon_text%]'."\n".'[%readon_href%]'."\n";
			$preview_article	= str_replace('[%readon_button%]', $readon, $article);
			$preview_article	= str_replace('[%readon_href%]', 'http://www.mysite/sample_article.html', $preview_article);
		}
		else {
			$preview_article = str_replace('[%readon_button%]', '', $article);
		}

		// HTML-tags for iframe-preview
		$newtext = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
		$newtext .= '<html>'."\n";
		$newtext .= ' <head>'."\n";
		$newtext .= '   <title>Newsletter</title>'."\n";
		$newtext .= '   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'."\n";
		$newtext .= '   <meta name="robots" content="noindex,nofollow" />'."\n";
		$newtext .= ' </head>'."\n";
		$newtext .= ' <body style="margin:0; padding:10px;">'."\n";
		$newtext .= $preview_text."\n";
		$newtext .= ' </body>'."\n";
		$newtext .= '</html>'."\n";
		$preview = $newtext;

		// load sample article
		$sample_article	= nl2br(strip_tags($this->_sampleArticle($preview_article, $item->article)));
		$sample_article	= $sample_article . $sample_article;

		$preview		=  str_replace('[%content%]', $sample_article, $preview);
		JFactory::getApplication()->setUserState('com_bwpostman.edit.template.tpldata', $preview);
		// end make preview
		return;
	}

	/**
	 * Method to make text template preview for standard templates
	 *
	 * @access	public
	 *
	 * @return
	 *
	 * @since 1.1.0
	 */
	public function makePreviewTextStd($item)
	{
		$model				= JModelLegacy::getInstance( 'newsletter', 'BwPostmanModel' );
		$itemid_unsubscribe	= $model->getItemid('register');
		$itemid_edit		= $model->getItemid('edit');

		// make preview
		// first get templates tpls
		$tpl_id		= $item->tpl_id;
		$tpl_model	= JModelLegacy::getInstance( 'templates_tpl', 'BwPostmanModel' );
		$tpl		= $tpl_model->getItem($tpl_id);

		// make text preview data
		$preview_text = nl2br($this->makeTexttemplate($item, $tpl), true);

		// make intro preview
		$preview_text = str_replace('[%intro_headline%]', $item->intro['intro_headline'], $preview_text);
		$preview_text = str_replace('[%intro_text%]', $item->intro['intro_text'], $preview_text);

		// impressum
		if ($item->footer['show_impressum'] == 1) {
			$uri  			= JFactory::getURI();
			$params 		= JComponentHelper::getParams('com_bwpostman');
			$impressum		= "<br /><br />" . JText::sprintf('COM_BWPOSTMAN_NL_FOOTER_TEXT', $uri->root(), $uri->root(), $itemid_unsubscribe, $uri->root(), $itemid_edit) . "<br /><br />" . $params->get('legal_information_text')."<br /><br />";
			$preview_text	= $preview_text . nl2br($impressum, true);
		}

		// replace edit and unsubscribe link
//		$replace1			= '+ ' . JText::_('COM_BWPOSTMAN_TPL_UNSUBSCRIBE_LINK_TEXT') . ' +<br />&nbsp;&nbsp;' . JURI::root(true) . 'index.php?option=com_bwpostman&amp;Itemid='. $itemid_unsubscribe . '&amp;view=register&amp;task=unsubscribe&amp;email=[UNSUBSCRIBE_EMAIL]&amp;code=[UNSUBSCRIBE_CODE]';
		$replace1			= '+ ' . JText::_('COM_BWPOSTMAN_TPL_UNSUBSCRIBE_LINK_TEXT') . ' +<br />&nbsp;&nbsp;' . JURI::root(true) . 'index.php?option=com_bwpostman&amp;Itemid='. $itemid_edit . '&amp;view=edit&amp;task=unsub&amp;editlink=[EDITLINK]';
		$preview_text		= str_replace('[%unsubscribe_link%]', $replace1, $preview_text);
		$replace2			= '+ ' . JText::_('COM_BWPOSTMAN_TPL_EDIT_LINK_TEXT') . ' +<br />&nbsp;&nbsp;' . JURI::root(true) . 'index.php?option=com_bwpostman&amp;Itemid='. $itemid_edit . '&amp;view=edit&amp;editlink=[EDITLINK]';
		$preview_text		= str_replace('[%edit_link%]', $replace2, $preview_text);

		// make article preview data
		$article = "\n" . $tpl->article_tpl ;
		if ($item->article['show_readon'] == 1) {
			$readon = $tpl->readon_tpl . "\n";
			$preview_article = str_replace('[%readon_button%]', $readon, $article);
			$preview_article = str_replace('[%readon_href%]', 'http://www.mysite/sample_article.html', $preview_article);
		}
		else {
			$preview_article = str_replace('[%readon_button%]', '', $article);
		}

		//  set divider preview template
		$preview_divider = nl2br($tpl->divider_tpl . "\n");

		// HTML-tags for iframe-preview
		$newtext = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
		$newtext .= '<html>'."\n";
		$newtext .= ' <head>'."\n";
		$newtext .= '   <title>Newsletter</title>'."\n";
		$newtext .= '   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'."\n";
		$newtext .= '   <meta name="robots" content="noindex,nofollow" />'."\n";
		$newtext .= ' </head>'."\n";
		$newtext .= ' <body style="margin:0; padding:10px;">'."\n";
		$newtext .= $preview_text."\n";
		$newtext .= ' </body>'."\n";
		$newtext .= '</html>'."\n";
		$preview = $newtext;

		// load sample article
		$sample_article	= nl2br(strip_tags($this->_sampleArticle($preview_article, $item->article)));
		$sample_article	= $item->article['divider'] == 1 ? $sample_article . $preview_divider . $sample_article : $sample_article . $sample_article;

		$preview		=  str_replace('[%content%]', $sample_article, $preview);
		JFactory::getApplication()->setUserState('com_bwpostman.edit.template.tpldata', $preview);
		// end make preview
		return;
	}

	/**
	 * Method to replace the links in template preview to provide the correct preview
	 *
	 * @access	private
	 *
	 * @param 	string HTML-/Text-version
	 *
	 * @return 	boolean
	 *
	 * @since 1.1.0
	 */
	private function _replaceLinks(&$text)
	{
		$search_str	= '/\s+(href|src)\s*=\s*["\']?\s*(?!http|mailto)([\w\s&%=?#\/\.;:_-]+)\s*["\']?/i';
		$text		= preg_replace($search_str,' ${1}="'.JURI::root().'${2}"',$text);
		return true;
	}
}
