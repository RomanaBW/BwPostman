<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single text (raw) subscribers view for backend.
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

namespace BoldtWebservice\Component\BwPostman\Administrator\View\Subscriber;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Environment\Browser;
use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwWebApp;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * BwPostman Subscriber RAW View
 *
 * @package 	BwPostman-Admin
 *
 * @subpackage 	Subscribers
 *
 * @since       0.9.1
 */
class RawView extends BaseHtmlView
{
	/**
	 * property to hold form data
	 *
	 * @var array   $form
	 *
	 * @since       4.0.0
	 */
	protected $form;

	/**
	 * property to hold selected item
	 *
	 * @var object   $item
	 *
	 * @since       4.0.0
	 */
	protected $item;

	/**
	 * property to hold subscriber data
	 *
	 * @var object   $sub
	 *
	 * @since       4.0.0
	 */
	protected $sub;

	/**
	 * property to hold row object
	 *
	 * @var object   $row
	 *
	 * @since       0.9.1
	 */
	protected $row;

	/**
	 * property to hold permissions as array
	 *
	 * @var array $permissions
	 *
	 * @since       2.0.0
	 */
	public $permissions;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  RawView|bool  A string if successful, otherwise a JError object.
	 *
	 * @throws Exception
	 *
	 * @since       0.9.1
	 */
	public function display($tpl = null)
	{
		$app 	  = Factory::getApplication();
		$jinput	  = $app->input;
		$task	  = $jinput->get('task', 'export');
		$model    = $this->getModel('subscriber');
		$document = $app->getDocument();
		$document->getWebAssetManager()->registerAndUseScript('com_bwpostman.admin-bwpm_subscriber', 'com_bwpostman/admin-bwpm_subscriber.js');

		if ($task == 'insideModal')
		{
			// Get the data from the model
			$this->form		= $this->get('Form');
			$this->item		= $this->get('Item');
			$this->sub	= $model->getSubscriberData((int) $this->item->id);

			// Call parent display
			parent::display($tpl);
		}
		else
		{
			$this->permissions = $app->getUserState('com_bwpm.permissions', []);

			if (!$this->permissions['view']['subscriber'])
			{
				$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', Text::_('COM_BWPOSTMAN_SUBS')),
					'error');
				$app->redirect('index.php?option=com_bwpostman');
			}

			// Get the post data
			$post = $app->getUserState('com_bwpostman.subscribers.export.data', []);

			if ($post['fileformat'] == 'csv')
			{
				$mime_type = "application/csv";
			}
			else
			{
				$mime_type = "application/xml";
			}

			$date     = Factory::getDate();
			$filename = "BackupList_BwPostman_from_" . $date->format("Y-m-d");

			// Maybe we need other headers depending on browser type...
			$browser      = Browser::getInstance();
			$user_browser = $browser->getBrowser();
			$appWeb       = new BwWebApp();

			$appWeb->clearHeaders();

			$appWeb->setHeader('Content-Type', $mime_type, true); // Joomla will overwrite this...
			if ($post['fileformat'] == 'csv')
			{
				$appWeb->setHeader('Content-Disposition', "attachment; filename=\"$filename.csv\"", true);
			}
			else
			{
				$appWeb->setHeader('Content-Disposition', "attachment; filename=\"$filename.xml\"", true);
			}

			$appWeb->setHeader('Expires', gmdate('D, d M Y H:i:s') . ' GMT', true);
			$appWeb->setHeader('Pragma', 'no-cache', true);

			if ($user_browser == "msie")
			{
				$appWeb->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
				$appWeb->setHeader('Pragma', 'public', true);
			}

			// Joomla overwrites content-type, we can't use $appWeb->setHeader()
			$document->setMimeEncoding($mime_type);

			@ob_end_clean();
			ob_start();

			$appWeb->sendHeaders();

			// Get the export data
			echo $model->export($post);
		}

		return $this;
	}
}
