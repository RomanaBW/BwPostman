<?php
/**
 * BwPostman Console Commands Mixin InitialiseComponent
 *
 * BwPostman console command mixin to initialise component
 *
 * @version %%version_number%%
 * @package BwPostman-CLI commands
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
 * @license GNU/GPL, see LICENCE.txt
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public Licence as published by
 * the Free Software Foundation, either version 3 of the Licence, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public Licence for more details.
 *
 * You should have received a copy of the GNU General Public Licence
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace BoldtWebservice\Component\BwPostman\Administrator\CliCommands\MixIn;

defined('_JEXEC') || die;

use BoldtWebservice\Component\BwPostman\Administrator\Extension\BwPostmanComponent;
use Exception;
use Joomla\Application\ApplicationInterface;
use Joomla\Console\Application;

/**
 * A trait to initialise BwPostman for use outside the front- and backend site application.
 *
 * @since  9.2.0
 */
trait InitialiseComponent
{
    private BwPostmanComponent $componentObject;

    protected ApplicationInterface $application;

    protected function getComponentObject(ApplicationInterface $app): BwPostmanComponent
    {
        if (empty($this->componentObject))
        {
            $this->componentObject = $app->bootComponent('com_bwpostman');
        }

        return $this->componentObject;
    }

    /**
     * Initialise the BwPostman component.
     *
     * @param   ApplicationInterface  $app
     *
     * @return  void
     *
     * @throws  Exception
     *
     * @since   4.4.0
     */
    protected function initialiseComponent(ApplicationInterface $app): void
    {
        // Load the BoldtWebservice language files
        $lang = $this->getApplication()->getLanguage();
        $lang->load('com_bwpostman', JPATH_SITE, 'en-GB', true, true);
        $lang->load('com_bwpostman', JPATH_SITE, null, true, false);
        $lang->load('com_bwpostman', JPATH_ADMINISTRATOR, 'en-GB', true, true);
        $lang->load('com_bwpostman', JPATH_ADMINISTRATOR, null, true, false);

        /** @noinspection PhpUnusedLocalVariableInspection */
        $componentObject = $this->getComponentObject($this->getApplication());
        $this->application = $app;
    }
}
