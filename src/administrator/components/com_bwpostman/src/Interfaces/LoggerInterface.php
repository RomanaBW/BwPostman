<?php
/**
 * BwPostman Newsletter Component
 *
 * Logger interface for BwPostman.
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
 * Interface for logging functionality.
 * Can be used in both backend and frontend contexts.
 *
 * @package BwPostman-Interfaces
 * @since 4.4.0
 */
interface LoggerInterface
{
    /**
     * Log a debug message.
     *
     * @param string      $message  The message to log
     * @param string|null $category Optional category
     *
     * @return void
     */
    public function logDebug(string $message, ?string $category = null): void;

    /**
     * Log an error message.
     *
     * @param string      $message  The message to log
     * @param string|null $category Optional category
     *
     * @return void
     */
    public function logError(string $message, ?string $category = null): void;

    /**
     * Log an info message.
     *
     * @param string      $message  The message to log
     * @param string|null $category Optional category
     *
     * @return void
     */
    public function logInfo(string $message, ?string $category = null): void;

    /**
     * Log a warning message.
     *
     * @param string      $message  The message to log
     * @param string|null $category Optional category
     *
     * @return void
     */
    public function logWarning(string $message, ?string $category = null): void;

    /**
     * Log a development message.
     *
     * @param string      $message  The message to log
     * @param string|null $category Optional category
     *
     * @return void
     */
    public function logDevelopment(string $message, ?string $category = null): void;

    /**
     * Get the log category to use for log entries.
     *
     * @return string The log category
     */
    public function getLogCategory(): string;
}
