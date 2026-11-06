<?php
/**
 * BwPostman Newsletter Component
 *
 * Database interface for BwPostman.
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
 * Interface for database operations in BwPostman.
 * Provides abstraction for database access to enable testing.
 *
 * @package BwPostman-Interfaces
 * @since 4.4.0
 */
interface BwDatabaseInterface
{
    /**
     * Get the database table prefix.
     *
     * @return string The database prefix (e.g., "jos_")
     */
    public function getPrefix(): string;
}
