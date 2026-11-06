<?php
/**
 * @package
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace BoldtWebservice\Component\BwPostman\Tests\Unit\Mocks;

use BoldtWebservice\Component\BwPostman\Interfaces\LoggerInterface;

class MockLogger implements LoggerInterface
{
    public $lastEntry = null;
    public $entries = [];
    public $callCount = 0;

    public function logDebug(string $message, ?string $category = null): void
    {
        $this->lastEntry = $message;
        $this->entries[] = ['type' => 'debug', 'message' => $message];
        $this->callCount++;
    }

    public function logError(string $message, ?string $category = null): void
    {
        $this->lastEntry = $message;
        $this->entries[] = ['type' => 'error', 'message' => $message];
        $this->callCount++;
    }

    public function logInfo(string $message, ?string $category = null): void
    {
        $this->lastEntry = $message;
        $this->entries[] = ['type' => 'info', 'message' => $message];
        $this->callCount++;
    }

    public function logWarning(string $message, ?string $category = null): void
    {
        $this->lastEntry = $message;
        $this->entries[] = ['type' => 'warning', 'message' => $message];
        $this->callCount++;
    }

    public function logDevelopment(string $message, ?string $category = null): void
    {
        $this->lastEntry = $message;
        $this->entries[] = ['type' => 'development', 'message' => $message];
        $this->callCount++;
    }

    public function getLogCategory(): string
    {
        return 'test';
    }
}
