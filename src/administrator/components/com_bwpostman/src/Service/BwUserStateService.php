<?php

/**
 * BwPostman component
 *
 * User state service – implementation.
 *
 * @version %%version_number%%
 * @package BwPostman-Admin
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

namespace BoldtWebservice\Component\BwPostman\Administrator\Service;

use Exception;
use Joomla\CMS\Factory;

/**
 * User state service implementation.
 *
 * Wraps access to session / user state values, scoped to the
 * component (prevents name collisions in the session store).
 *
 * Note: in Joomla 5/6 Factory::getSession() is deprecated.
 * The supported way is Factory::getApplication()->getSession().
 *
 * @since   4.4.0
 */
class BwUserStateService implements BwUserStateInterface
{
    /**
     * Returns a user state value from the session.
     *
     * @param   string  $key      State key (without component prefix).
     * @param   mixed   $default  Default value if the key does not exist.
     *
     * @return  mixed  The state value.
     *
     * @throws Exception
     *
     * @since   4.4.0
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Factory::getApplication()
            ->getSession()
            ->get('com_bwpostman.' . $key, $default);
    }

    /**
     * Stores a user state value in the session.
     *
     * @param   string  $key    State key (without component prefix).
     * @param   mixed   $value  Value to store.
     *
     * @return  mixed  The previous value.
     *
     * @throws Exception
     *
     * @since   4.4.0
     */
    public function set(string $key, mixed $value): mixed
    {
        return Factory::getApplication()
            ->getSession()
            ->set('com_bwpostman.' . $key, $value);
    }
}
