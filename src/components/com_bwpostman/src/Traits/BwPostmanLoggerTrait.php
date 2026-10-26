<?php
/**
 * BwPostman Newsletter Component
 *
 * Trait for logging functionality used across BwPostman component.
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

namespace BoldtWebservice\Component\BwPostman\Traits;

use BoldtWebservice\Component\BwPostman\Administrator\Libraries\BwLogger;
use Joomla\CMS\Log\LogEntry;

/**
 * Trait providing logging methods for BwPostman components.
 * Can be used in both backend and frontend contexts.
 *
 * @package BwPostman-Admin
 * @since 4.4.0
 */
trait BwPostmanLoggerTrait
{
    /**
     * Get the log category to use for log entries.
     * Supports $this->log_cat (from plugins), $this->logCategory (from classes), or falls back to 'maintenance'.
     *
     * @return string The log category
     *
     * @since 4.4.0
     */
    protected function getLogCategory(): string
    {
        // Check if the class has a $log_cat property (used in many existing classes)
        if (property_exists($this, 'log_cat')) {
            return $this->log_cat;
        }

        // Check if the class has a $logCategory property (used in most classes)
        if (property_exists($this, 'logCategory')) {
            return $this->logCategory;
        }

        // Fallback to default category
        return 'maintenance';
    }

    /**
     * Method to write debug messages to the log
     *
     * @param   string  $message  The message to log
     * @param   string|null  $category  Optional category, uses getLogCategory() if not provided
     *
     * @since 4.4.0
     */
    protected function logDebug(string $message, ?string $category = null): void
    {
        $cat = $category ?? $this->getLogCategory();
        $this->logger->addEntry(new LogEntry($message, BwLogger::BW_DEBUG, $cat));
    }

    /**
     * Method to write error messages to the log
     *
     * @param   string  $message  The message to log
     * @param   string|null  $category  Optional category, uses getLogCategory() if not provided
     *
     * @since 4.4.0
     */
    protected function logError(string $message, ?string $category = null): void
    {
        $cat = $category ?? $this->getLogCategory();
        $this->logger->addEntry(new LogEntry($message, BwLogger::BW_ERROR, $cat));
    }

    /**
     * Method to write development messages to the log
     *
     * @param   string  $message  The message to log
     * @param   string|null  $category  Optional category, uses getLogCategory() if not provided
     *
     * @since 4.4.0
     */
    protected function logDevelopment(string $message, ?string $category = null): void
    {
        $cat = $category ?? $this->getLogCategory();
        $this->logger->addEntry(new LogEntry($message, BwLogger::BW_DEVELOPMENT, $cat));
    }

    /**
     * Method to write info messages to the log
     *
     * @param   string  $message  The message to log
     * @param   string|null  $category  Optional category, uses getLogCategory() if not provided
     *
     * @since 4.4.0
     */
    protected function logInfo(string $message, ?string $category = null): void
    {
        $cat = $category ?? $this->getLogCategory();
        $this->logger->addEntry(new LogEntry($message, BwLogger::BW_INFO, $cat));
    }

    /**
     * Method to write warning messages to the log
     *
     * @param   string  $message  The message to log
     * @param   string|null  $category  Optional category, uses getLogCategory() if not provided
     *
     * @since 4.4.0
     */
    protected function logWarning(string $message, ?string $category = null): void
    {
        $cat = $category ?? $this->getLogCategory();
        $this->logger->addEntry(new LogEntry($message, BwLogger::BW_WARNING, $cat));
    }
}
