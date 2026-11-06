<?php
/**
 * BwPostman Newsletter Component
 *
 * State manager interface for BwPostman.
 *
 * @version %%version_number%%
 * @package BwPostman-Interfaces
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
 * @license GNU/GPL, see LICENCE.txt
 */

namespace BoldtWebservice\Component\BwPostman\Interfaces;

/**
 * Interface for managing application state in BwPostman.
 * Provides abstraction for user state operations to enable testing.
 *
 * @package BwPostman-Interfaces
 * @since 4.4.0
 */
interface BwStateManagerInterface
{
    /**
     * Set user state data.
     *
     * @param string $key   The state key
     * @param mixed  $value The value to store
     *
     * @return void
     */
    public function setUserState(string $key, mixed $value): void;

    /**
     * Get user state data.
     *
     * @param string $key     The state key
     * @param mixed  $default Default value if key does not exist
     *
     * @return mixed The stored value or default
     */
    public function getUserState(string $key, mixed $default = null): mixed;
}
