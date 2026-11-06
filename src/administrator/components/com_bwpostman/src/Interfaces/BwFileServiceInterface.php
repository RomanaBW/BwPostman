<?php
/**
 * BwPostman Newsletter Component
 *
 * File service interface for BwPostman.
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
 * Interface for file operations in BwPostman.
 * Provides abstraction for file system operations to enable testing.
 *
 * @package BwPostman-Interfaces
 * @since 4.4.0
 */
interface BwFileServiceInterface
{
    /**
     * Check if a file exists.
     *
     * @param string $path The file path
     *
     * @return bool True if the file exists, false otherwise
     */
    public function exists(string $path): bool;

    /**
     * Open a file.
     *
     * @param string $path The file path
     * @param string $mode The open mode (e.g., 'r', 'w', 'rb')
     *
     * @return resource|false The file handle or false on failure
     */
    public function open(string $path, string $mode);

    /**
     * Close a file handle.
     *
     * @param resource $handle The file handle
     *
     * @return bool True on success, false on failure
     */
    public function close($handle): bool;

    /**
     * Write data to a file.
     *
     * @param resource $handle The file handle
     * @param string   $data   The data to write
     *
     * @return int|false Number of bytes written or false on failure
     */
    public function write($handle, string $data): int|false;

    /**
     * Truncate a file to a given length.
     *
     * @param resource $handle The file handle
     * @param int      $size   The new size
     *
     * @return bool True on success, false on failure
     */
    public function truncate($handle, int $size): bool;

    /**
     * Get the temporary directory path.
     *
     * @return string The temporary directory path
     */
    public function getTempPath(): string;
}
