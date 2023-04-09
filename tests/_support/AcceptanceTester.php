<?php
use Page\Generals as Generals;
use \Codeception\Lib\Actor\Shared\Friend;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)

 * @copyright (C) 2012-2017 Boldt Webservice <forum@boldt-webservice.de>
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
 * @since   2.0.0
 */
class AcceptanceTester extends \Codeception\Actor
{
	use _generated\AcceptanceTesterActions;
	use \Codeception\Lib\Actor\Shared\Friend;

	/**
	 * Define custom actions here
	 */

	/**
	 * Method to remove readonly attribute from a form field
	 *
	 * @param string            $input_element   ID of input element to remove readonly attribute
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function removeReadonlyAttribute($input_element = '')
	{
		// remove readonly
		$remove = 'document.getElementById("' . $input_element . '").removeAttribute("readonly");';
		$this->executeJS($remove);
	}

	/**
	 * Method to remove readonly attribute from a form field
	 *
	 * @param string            $input_element   ID of input element to remove readonly attribute
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function reassembleReadonlyAttribute($input_element = '')
	{
		// set readonly
		$set  = 'var readonly_attribute = document.createAttribute("readonly");';
		$set .= 'readonly_attribute.nodeValue = "readonly";';
		$set .= 'document.getElementById("' . $input_element . '").setAttributeNode(readonly_attribute);';
		$this->executeJS($set);
	}

	/**
	 * Method to remove selected attribute from an option of a select list
	 *
	 * @param string            $input_element   ID of input element to remove readonly attribute
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function removeSelectedAttribute($input_element = '')
	{
		$script = 'jQuery( "' . $input_element . ' option:selected" )';
		$script .= ".change(function () {";
		$script .= "	jQuery( this ).removeAttr('selected');";
		$script .= "})";
		$script .= ".change();";

		$this->executeJS($script);
	}

	/**
	 * Method to fill a form field with readonly attribute
	 *
	 * @param string            $input_element   ID of input element to remove readonly attribute
	 * @param string            $identifier      Xpath of element to fill
	 * @param string            $value           value to fill in
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function fillReadonlyInput($input_element = '', $identifier = '', $value = '')
	{
		// remove readonly
		$this->removeReadonlyAttribute($input_element);

		// fill field
		$this->fillField($identifier, $value);

		// set readonly
		$this->reassembleReadonlyAttribute($input_element);
	}

	/**
	 * Method to set JQuery formatted select list visible, select one value and reset to invisible
	 * If form_id is passed, the form will be submitted (useful for filter etc.)
	 *
	 * @param   string            $select_list ID of select list
	 * @param   string            $select_text value of selected item
	 * @param   string            $sort_order
	 * @param   string            $form_id     ID of form to submit
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function clickJQuerySelectedElement($select_list = '', $select_text = '', $sort_order = '', $form_id = '')
	{
		$this->executeJS("document.getElementById('" . $select_list . "').setAttribute('style', 'display: visible');");
		$this->selectOption("#" . $select_list, $select_text . ' ' . $sort_order);
		$this->executeJS("document.getElementById('" . $select_list . "').setAttribute('style', 'display: none');");
		if ($form_id != '')
		{
			$this->executeJS('document.getElementById("' . $form_id . '").submit();');
		}
	}

	/**
	 * Method to set JQuery formatted select list visible, select one value and reset to invisible
	 *
	 * @param string            $radio_id   ID of radio buttons
	 * @param string            $value_text value of selected item
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	public function clickJQueryRadioElement($radio_id = '', $value_text = '')
	{
		$set_visible = "var radio_buttons = document.getElementsByName('$radio_id');";
		$set_visible .= "for (var i = 0; i < document.getElementsByName('$radio_id').length; i++) {";
		$set_visible .= "document.getElementsByName('$radio_id')[i].setAttribute('style', 'display: visible');";
		$set_visible .= "};";
		$set_invisible = "var radio_buttons = document.getElementsByName('$radio_id');";
		$set_invisible .= "for (var i = 0; i < document.getElementsByName('$radio_id').length; i++) {";
		$set_invisible .= "document.getElementsByName('$radio_id')[i].setAttribute('style', 'display: none');";
		$set_invisible .= "};";
		$this->executeJS($set_visible);
		$this->wait(10);

		$this->selectOption("#genFemale", $value_text);
		$this->executeJS($set_invisible);
	}

	/**
	 * Method to assert, that all search results are present and all rows shown in list contain search value
	 *
	 * @param string            $search_value
	 * @param int               $expected_nbr
	 * @param string            $tableIdentifier
	 *
	 * @since   2.0.0
	 */

	public function assertTableSearchResult($search_value, $expected_nbr, $tableIdentifier = "//*[@id='main-table']")
	{
		$row_values_actual = $this->GetTableRows($this, $tableIdentifier);
		$res_nbr           = count($row_values_actual);
		$this->assertEquals($expected_nbr, $res_nbr);
		// assert that all rows contain search value
		foreach ($row_values_actual as $item)
		{
			$this->assertStringContainsString($search_value, $item);
		}
	}

	/**
	 * Method to assert, that all filter results are present
	 *
	 * @param array             $filter_values
	 * @param string            $tableIdentifier
	 *
	 * @since   2.0.0
	 */

	public function assertFilterResult($filter_values, $tableIdentifier = "//table[@id='main-table']")
	{
		$row_values_actual = $this->GetTableRows($this, $tableIdentifier);
		$res_nbr           = count($row_values_actual);
		$this->assertEquals(count($filter_values), $res_nbr);
		// assert that all rows contain filtered values
		for ($i = 0; $i < $res_nbr; $i++)
		{
			$this->assertStringContainsString($filter_values[$i], $row_values_actual[$i]);
		}
	}

	/**
	 * Method to get the number of a table row for a specific search value at a given column
	 *
	 * @param string            $search_value
	 * @param string            $tableId
	 *
	 * @return int              $id
	 *
	 * @since   2.0.0
	 */

	public function getTableRowIdBySearchValue($search_value, $tableId = 'main-table')
	{
		$id             = 0;
		$tableIdentifier = "//table[@id='" . $tableId . "']";
		$row_values     = $this->GetTableRows($this, $tableIdentifier);

		for ($i = 0; $i < count($row_values); $i++)
		{
			$found_value    = strpos($row_values[$i], $search_value);

			if ($found_value !== false)
			{
				$id = $i + 1;
				break;
			}
		}

		return $id;
	}

	/**
	 * Method to search for item to edit
	 *
	 * @param string $search_value
	 * @param string $tableId
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since   4.0.0
	 */

	public function filterForItemToEdit(string $search_value, string $tableId = 'main-table'): bool
	{
		$this->fillField(Generals::$search_field, $search_value);
		$this->clickAndWait(Generals::$search_button_direct, 2);

		$firstLine = $this->grabTextFrom("//*[@id='" . $tableId . "']/tbody/tr[1]/td[1]");
		codecept_debug('First line: ' . $firstLine);

		if (strpos($firstLine, 'There are no data available') === false)
		{
			return true;
		}

		return false;
	}

	/**
	 * Method to
	 *
	 * @param string            $button
	 * @param string            $search_value
	 * @param string            $tableId
	 *
	 * @return boolean
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */

	public function findPageWithItemAndScrollToItem($button, $search_value, $tableId = 'main-table')
	{
		$found      = false;
		$count      = 1;
		$last_page  = $this->getLastPageNumber();
		$yOffset    = -100;

		while (!$found)
		{
			$table_search_result  = $this->getTableRowIdBySearchValue($search_value, $tableId);

			if ($table_search_result > 0)
			{
				$position   = sprintf("//*[@id='" . $tableId . "']/tbody/tr[%s]", $table_search_result);
				$this->scrollTo($position, 0, $yOffset);
				$this->wait(1);
				$found  = true;
			}
			else
			{
				if ($count >= $last_page)
				{
					return false;
				}

				$this->scrollTo(Generals::$pagination_bar);
				$this->wait(1);
				$this->click(Generals::$next_page);

				$this->waitForElement(Generals::$pageTitle, 30);
				$count++;
			}
		}

		return true;
	}

	/**
	 * Method to click select list value at JQuery formatted select lists
	 *
	 * @param string            $select_list
	 * @param string            $select_value
	 * @param string            $select_list_id
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */

	public function clickSelectList($select_list, $select_value, $select_list_id)
	{
		// open select list
		$this->click($select_list);
//		$this->waitForElementVisible(sprintf(Generals::$select_list_open, $select_list_id), 30);

		// click wanted value
		$this->selectOption($select_list, $select_value);
	}

	/**
	 * Method to click select list value at JQuery formatted select lists
	 *
	 * @param string        $target     the target to click
	 * @param string        $time       the time to wait after click
	 *
	 * @since   2.0.0
	 */

	public function clickAndWait($target, $time)
	{
		$this->click($target);
		$this->wait($time);
	}

	/**
	 *
	 * @return int|mixed
	 *
	 * @since 2.0.0
	 */
	private function getLastPageNumber()
	{
		$last_page = 1;

		$this->scrollTo(Generals::$pagination_bar);
		$this->wait(1);

		$pagination_accessible   = count($this->grabMultiple(Generals::$last_page));

		if ($pagination_accessible > 0)
		{
			$this->click(Generals::$last_page);
			$this->scrollTo(Generals::$pagination_bar);
			$this->wait(1);
			$last_page = $this->grabTextFrom(Generals::$last_page_identifier);

			$this->scrollTo(Generals::$pagination_bar);
			$this->wait(1);
			$this->click(Generals::$first_page);
		}

		return $last_page;
	}

	/**
	 * Method to set the name attribute of an iframe (only one iframe on page!)
	 *
	 * @param   string            $iframeName   name the iframe should get
	 *
	 * @return  void
	 *
	 * @since   2.4.0
	 */
	public function setIframeName($iframeName)
	{
		$this->executeJS("document.getElementsByTagName('iframe')[0].setAttribute('name', '" . $iframeName . "');");
	}

	/**
	 * @param $table
	 * @param $criteria
	 *
	 * @since 2.0.0
	 */
	public function seeInMyDatabase($table, $criteria)
	{
		try
		{
			$this->seeInDatabase($table, $criteria);
		}
		catch (\RuntimeException $e)
		{
			codecept_debug('Error on ' . $table , ':');
			codecept_debug($e->getMessage());
			$this->assertEquals(0, 1);
		}
	}
}
