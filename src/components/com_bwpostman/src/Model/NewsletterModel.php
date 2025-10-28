<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman newsletter single model for frontend.
 *
 * @version %%version_number%%
 * @package BwPostman-Site
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

namespace BoldtWebservice\Component\BwPostman\Site\Model;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Plugin\PluginHelper;
use BoldtWebservice\Component\BwPostman\Administrator\Model\NewsletterModel as AdminNewsletterModel;
use Joomla\Event\Event;

/**
 * Class BwPostmanModelNewsletter
 *
 * @since       0.9.1
 */
class NewsletterModel extends BaseDatabaseModel
{
	/**
	 * Method to get  newsletter content
	 *
	 * @return	string|null	string on success, null on failure.
	 *
	 * @throws Exception
	 *
	 * @since	1.2.0
	 */
	public function getContent(): ?string
	{
		$app        = Factory::getApplication();
		$id         = $app->input->getInt('id', 0);
		$user       = $app->getIdentity();

		$newsletter = $this->getTable('Sendmailcontent')->getContent($id);

		// Get the dispatcher and include bwpostman plugins
		PluginHelper::importPlugin('bwpostman');

        $eventArgs = array(
            'context' => 'com_bwpostman.view',
            'body'    => $newsletter,
            'id'      => $user->id,
        );
        $event = new Event('onBwPostmanPersonalize', $eventArgs);
        $app->getDispatcher()->dispatch($event->getName(), $event);
        $eventResults = $event->getArgument('result', []);

        if ($eventResults)
        {
            $newsletter = $eventResults[0];
        }

		return $newsletter;
	}

	/**
	 * Method to get an item.
	 *
	 * @param integer|null $pk The id of the item
	 *
	 * @return  object
	 *
	 * @throws Exception
	 *
	 * @since 4.0.0
	 */
	public function getItem(?int $pk = null): object
	{
		$model = new AdminNewsletterModel();

		return $model->getItem((int)$pk);
	}
}
