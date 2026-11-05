<?php
namespace BoldtWebservice\Component\BwPostman\Administrator\Service;

use Joomla\Filesystem\File;
/**
 * The ONLY class of the component touching Joomla's filesystem classes.
 * That is exactly why: this class is not unit tested (integration test!),
 * everything else is mocked against the interface.
 *
 * @since 4.4.0
 */
class BwFilesystemService implements BwFilesystemInterface
{
    public function exists(string $path): bool
    {
        return File::exists($path);
    }

    public function write($path, string $content): bool
    {
        return File::write($path, $content) !== false;
    }

    public function delete(string $path): bool
    {
        return File::delete($path);
    }
}
