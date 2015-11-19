<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single text (raw) newsletters view for backend.
 *
 * @version 1.2.4 bwpm
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

// Import VIEW object class
jimport('joomla.application.component.view');

/**
 * BwPostman Newsletter RAW View
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Newsletters
 */
class BwPostmanViewNewsletter extends JViewLegacy
{
	public function display ($tpl = Null)
	{
		$app 	= JFactory::getApplication();
		$jinput	= JFactory::getApplication()->input;
		

		// Get the params
		$params			= JComponentHelper::getParams('com_bwpostman');
		$mails_per_step	= (int) $app->getUserState('com_bwpostman.newsletters.mails_per_pageload', $params->get('default_mails_per_pageload'));
		$sendandpublish	= $app->getUserState('com_bwpostman.newsletters.sendmailandpublish', 0);
		$id				= $app->getUserState('com_bwpostman.newsletters.publish_id', 0);
		$delay			= (int) $params->get('mails_per_pageload_delay') * (int) $params->get('mails_per_pageload_delay_unit');
		
		// Build delay message
		if ((int) $params->get('mails_per_pageload_delay_unit') == 1000) {
			if ((int) $params->get('mails_per_pageload_delay') == 1) {
				$delay_message	= JText::sprintf('COM_BWPOSTMAN_MAILS_DELAY_MESSAGE', JText::sprintf('COM_BWPOSTMAN_MAILS_DELAY_TEXT_1_SECONDS',$delay/1000));
			}
			else {
				$delay_message	= JText::sprintf('COM_BWPOSTMAN_MAILS_DELAY_MESSAGE', JText::sprintf('COM_BWPOSTMAN_MAILS_DELAY_TEXT_N_SECONDS',$delay/1000));
			}
		}
		else {
					if ((int) $params->get('mails_per_pageload_delay') == 1) {
				$delay_message	= JText::sprintf('COM_BWPOSTMAN_MAILS_DELAY_MESSAGE', JText::sprintf('COM_BWPOSTMAN_MAILS_DELAY_TEXT_1_MINUTES',$delay/1000));
			}
			else {
				$delay_message	= JText::sprintf('COM_BWPOSTMAN_MAILS_DELAY_MESSAGE', JText::sprintf('COM_BWPOSTMAN_MAILS_DELAY_TEXT_N_MINUTES',$delay/1000));
			}
		}

		$model	= $this->getModel('newsletter');
		$task	= $jinput->get('task', 'previewHTML');
		$nl_id	= $jinput->get('nl_id');
		$app->setUserState('com_bwpostman.viewraw.newsletter.id', $nl_id);
		
		if ($task == 'continue_sending'){
			// set number of queue entries before start sending
			$sumentries	= is_null($app->getUserState('com_bwpostman.newsletters.entries', null)) ? $app->setUserState('com_bwpostman.newsletters.entries', $model->checkTrials(2,1)) : $app->getUserState('com_bwpostman.newsletters.entries', null);

			if ($model->checkTrials(2)) {
				echo '<div class="modal" style="height: 150px;overflow: auto;margin-bottom: 15px;">';
				$ret	= $model->sendMailsFromQueue($mails_per_step);
				echo '</div>';
				// number of queue entries during sending
				$entries = $model->checkTrials(2,1);
				$percent = empty($sumentries) ? 0 : ceil(100/$sumentries*($sumentries-$entries));
				// progressbar
				echo '<div id="progress" style="border: 1px solid silver; width: 98%; line-height: 30px; padding: 2px;"><span style="position: absolute; left: 48%;"><b>'. $percent .' %</b></span><div style="background-color: green; width: '.$percent.'%; height: 30px;"></div></div><br />'. JText::sprintf('COM_BWPOSTMAN_NL_SENT_MESSAGE', $entries, $sumentries).'<br />';

				if ($ret == 1){   // There are more mails in the queue.
					echo $delay_message;
					echo '<script type="text/javascript">'."\n";
					echo "setTimeout('window.location.reload()'," .$delay. "); \n";
					echo "</script>\n";
				}
				if ($ret == 0){   // No more mails to send.
					// reset number of queue entries before start sending
					$app->setUserState('com_bwpostman.newsletters.entries', null);
					echo JText::_('COM_BWPOSTMAN_NL_QUEUE_COMPLETED');
					ob_flush();
					flush();
					echo '<script type="text/javascript">'."\n";
					// We cannot replace the "&" with an "&amp;" because it's JavaScript and not HTML
					echo "function goBackToQueue(){window.parent.location.href = 'index.php?option=com_bwpostman&view=newsletters&layout=queue';} \n";
					echo "setTimeout('goBackToQueue()',5000); \n";
					echo "</script>\n";
					if ($sendandpublish == 1){
						if ($model->publish($id, 1) === true) {
							echo "<br /><br /><font color='green'>" . JText::_('COM_BWPOSTMAN_NLS_N_ITEMS_PUBLISHED_1') . "</font>";
						}
						else {
							echo "<br /><br /><font color='red'>" . JText::_('COM_BWPOSTMAN_NLS_N_ITEMS_PUBLISHED_0') . "</font>";
						}
					}
					// clear data
					$app->setUserState('com_bwpostman.newsletters.sendmailandpublish', null);
					$app->setUserState('com_bwpostman.newsletters.publish_id', null);
				}
			}
			else {
				// reset number of queue entries before start sending
				$app->setUserState('com_bwpostman.newsletters.entries', null);
				echo JText::_('COM_BWPOSTMAN_NL_SENDING_NO_QUEUE_ENTRIES_TO_SEND');
			}
		}
		elseif ($task == 'insideModal') {
			// Get the newsletter 
			$this->item	= $model->getItem($nl_id);
			$this->item	= $model->getSingleNewsletter();
		} 
		else {
			// Get the newsletter 
			$this->item	= $model->getSingleNewsletter();
		}
		// Call parent display
		parent::display($tpl);
	}
}