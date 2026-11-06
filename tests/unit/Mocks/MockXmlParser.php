<?php
/**
 * @package
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace BoldtWebservice\Component\BwPostman\Tests\Unit\Mocks;

use BoldtWebservice\Component\BwPostman\Interfaces\FileServiceInterface;

class MockFileService implements FileServiceInterface
{
    public $files = [];
    public $handles = [];
    public $lastOpenedFile = null;
    public $tempPath = '/tmp';

    public function exists(string $path): bool
    {
        return isset($this->files[$path]);
    }

    public function open(string $path, string $mode)
    {
        $this->lastOpenedFile = $path;
        $handle = uniqid();
        $this->handles[$handle] = $path;
        return $handle;
    }

    public function close($handle): bool
    {
        unset($this->handles[$handle]);
        return true;
    }

    public function write($handle, string $data): int|false
    {
        return strlen($data);
    }

    public function truncate($handle, int $size): bool
    {
        return true;
    }

    public function getTempPath(): string
    {
        return $this->tempPath;
    }
}
