<?php
/**
 * BwPostman Newsletter Component
 *
 * BwPostman main router for frontend.
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

namespace BoldtWebservice\Component\BwPostman\Site\Service;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Menu\AbstractMenu;

/**
 * Routing class of com_bwpostman
 *
 * @since  4.0.0
 */
class Router extends RouterView
{

	/**
	 * BwPostman Component router constructor
	 *
	 * @param   SiteApplication  $app   The application object
	 * @param   AbstractMenu     $menu  The menu object to work with
	 *
	 * @since 4.0.0
	 */
	public function __construct(SiteApplication $app, AbstractMenu $menu)
	{
		$this->registerView(new RouterViewConfiguration('bwpostman'));
		$edit = new RouterViewConfiguration('edit');
		$edit->addLayout('editlink_form');
		$this->registerView($edit);
		$newsletter = new RouterViewConfiguration('newsletter');
		$newsletter->addLayout('nl_preview');
		$this->registerView($newsletter);
		$this->registerView(new RouterViewConfiguration('newsletters'));
		$register = new RouterViewConfiguration('register');
		$register->addLayout('error_accountblocked');
		$register->addLayout('error_accountgeneral');
		$register->addLayout('error_accountnotactivated');
		$register->addLayout('error_email');
		$register->addLayout('error_geteditlink');
		$register->addLayout('success_msg');
		$this->registerView($register);

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
	}

}
