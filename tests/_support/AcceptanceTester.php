<?php
use Page\Generals as Generals;


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
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

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
		$set_invisible .= "document.getElementsByName('$radio_id')[i].setAttribute('style', 'display: one');";
		$set_invisible .= "};";
//		$set_invisible    = "for each (button in radio_buttons) {button.setAttribute('style', 'display: none');};";
		$this->executeJS($set_visible);
		$this->wait(10);

//		$this->executeJS('$(\'#genFemale\').setAttribute(\'style\', \'display: visible\');');
		$this->selectOption("#genFemale", $value_text);
		$this->executeJS($set_invisible);
	}

	/**
	 * Method to assert, that all search results are present and all rows shown in list contain search value
	 *
	 * @param string            $search_value
	 * @param int               $expected_nbr
	 *
	 * @since   2.0.0
	 */

	public function assertTableSearchResult($search_value, $expected_nbr)
	{
		$row_values_actual = $this->GetTableRows($this, true);
		$res_nbr           = count($row_values_actual);
		$this->assertEquals($expected_nbr, $res_nbr);
		// assert that all rows contain search value
		foreach ($row_values_actual as $item)
		{
			$this->assertContains($search_value, $item);
		}
	}

	/**
	 * Method to assert, that all filter results are present
	 *
	 * @param array             $filter_values
	 *
	 * @since   2.0.0
	 */

	public function assertFilterResult($filter_values)
	{
		$row_values_actual = $this->GetTableRows($this, true);
		$res_nbr           = count($row_values_actual);
		$this->assertEquals(count($filter_values), $res_nbr);
		// assert that all rows contain filtered values
		for ($i = 0; $i < $res_nbr; $i++)
		{
			$this->assertContains($filter_values[$i], $row_values_actual[$i]);
		}
	}

	/**
	 * Method to get the number of a table row for a specific search value at a given column
	 *
	 * @param string            $search_value
	 *
	 * @return int              $id
	 *
	 * @since   2.0.0
	 */

	public function getTableRowIdBySearchValue($search_value)
	{
		$id             = 0;
		$row_values     = $this->GetTableRows($this, false);

		for ($i = 1; $i < count($row_values); $i++)
		{
			$found_value    = strpos($row_values[$i], $search_value);

			if ($found_value !== false)
			{
				$id = $i;
				break;
			}
		}
		return $id;
	}

	/**
	 * Method to
	 *
	 * @param string            $search_value
	 *
	 * @return boolean
	 *
	 * @since   2.0.0
	 */

	public function findPageWithItemAndScrollToItem($search_value)
	{
		$found      = false;
		$count      = 1;
		$last_page  = 1;

		while (!$found)
		{
			$table_search_result  = $this->getTableRowIdBySearchValue($search_value);

			if ($table_search_result > 0)
			{
				$position   = sprintf(".//*[@id='j-main-container']/*/table/tbody/tr[%s]", $table_search_result);
				$this->scrollTo($position, 0, -100);
				$found  = true;
			}
			else
			{
				if ($count == 1)
				{
					$this->scrollTo(Generals::$pagination_bar);
					$this->click(Generals::$last_page);

					$last_page  = $this->grabTextFrom(".//*/ul[contains(@class, 'pagination')]/li[contains(@class, 'active hidden-phone')]/a");

					$this->scrollTo(Generals::$pagination_bar);
					$this->click(Generals::$first_page);
				}
				$this->scrollTo(Generals::$pagination_bar);
				$this->click(Generals::$next_page);

				$this->waitForElement(Generals::$pageTitle, 30);
				$count++;
				if ($count > $last_page)
				{
					$found = true;
					return false;
				}
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
	 * @since   2.0.0
	 */

	public function clickSelectList($select_list, $select_value, $select_list_id)
	{
		// open select list
		$this->click($select_list);
		$this->waitForElementVisible(sprintf(Generals::$select_list_open, $select_list_id));

		// click wanted value
		$this->click($select_value);
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


}
