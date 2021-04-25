<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman single text (raw) newsletters view for backend.
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

namespace BoldtWebservice\Component\BwPostman\Administrator\View\Newsletter;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use BoldtWebservice\Component\BwPostman\Administrator\Helper\BwPostmanHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * Class BwPostmanViewNewsletter Raw View
 *
 * @package 	BwPostman-Admin
 * @subpackage 	Newsletters
 *
 * @since   2.0.0
 */
class RawView extends BaseHtmlView
{
	/**
	 * property to hold selected item
	 *
	 * @var object   $item
	 *
	 * @since   2.0.0
	 */
	protected $item;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @throws Exception
	 *
	 * @since   2.0.0
	 */
	public function display($tpl = null)
	{
		$app 	= Factory::getApplication();
		$jinput	= $app->input;

		if (!BwPostmanHelper::canView('newsletter'))
		{
			$app->enqueueMessage(Text::sprintf('COM_BWPOSTMAN_VIEW_NOT_ALLOWED', Text::_('COM_BWPOSTMAN_NLS')), 'error');
			$app->redirect('index.php?option=com_bwpostman');
		}

		$model	= $this->getModel('Newsletter');
		$task	= $jinput->get('task', 'previewHTML');
		$nl_id	= $jinput->get('nl_id');
		$app->setUserState('com_bwpostman.viewraw.newsletter.id', $nl_id);

		if ($task == 'insideModal')
		{
			// Get the newsletter
			$this->item	= $model->getItem($nl_id);
			$this->item	= $model->getSingleNewsletter();
		}
		else
		{
			// Get the newsletter
			$this->item	= $model->getSingleNewsletter();
		}

		// Call parent display
		parent::display($tpl);
		return $this;
	}
}
