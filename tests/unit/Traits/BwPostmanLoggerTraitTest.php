<?php
/**
 * BwPostman Newsletter Component
 *
 * Unit tests for BwPostmanLoggerTrait
 *
 * @version %%version_number%%
 * @package BwPostman-Tests
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
 * @license GNU/GPL, see LICENCE.txt
 */

namespace BoldtWebservice\Component\BwPostman\Tests\Unit\Traits;

use PHPUnit\Framework\TestCase;

/**
 * Mock class for LogEntry to avoid Joomla dependencies
 */
class MockLogEntry
{
    public $message;
    public $priority;
    public $category;

    public function __construct($message, $priority, $category)
    {
        $this->message = $message;
        $this->priority = $priority;
        $this->category = $category;
    }
}

/**
 * Mock class for BwLogger to avoid Joomla dependencies
 */
class MockBwLogger
{
    const BW_DEBUG = 8;
    const BW_ERROR = 1;
    const BW_DEVELOPMENT = 16;
    const BW_INFO = 4;
    const BW_WARNING = 2;

    public $lastEntry = null;
    public $entries = [];
    public $callCount = 0;

    public function addEntry($entry): void
    {
        $this->lastEntry = $entry;
        $this->entries[] = $entry;
        $this->callCount++;
    }

    public function getLastEntry()
    {
        return $this->lastEntry;
    }

    public function getCallCount(): int
    {
        return $this->callCount;
    }

    public function getEntries(): array
    {
        return $this->entries;
    }

    public function reset(): void
    {
        $this->lastEntry = null;
        $this->entries = [];
        $this->callCount = 0;
    }
}

/**
 * Trait providing logging methods for testing - uses MockLogEntry instead of Joomla LogEntry
 */
trait TestableBwPostmanLoggerTrait
{
    /**
     * Get the log category to use for log entries.
     * Supports $this->log_cat (from plugins), $this->logCategory (from classes), or falls back to 'maintenance'.
     *
     * @return string The log category
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
     */
    protected function logDebug(string $message, ?string $category = null): void
    {
        $cat = $category ?? $this->getLogCategory();
        $this->logger->addEntry(new MockLogEntry($message, MockBwLogger::BW_DEBUG, $cat));
    }

    /**
     * Method to write error messages to the log
     *
     * @param   string  $message  The message to log
     * @param   string|null  $category  Optional category, uses getLogCategory() if not provided
     */
    protected function logError(string $message, ?string $category = null): void
    {
        $cat = $category ?? $this->getLogCategory();
        $this->logger->addEntry(new MockLogEntry($message, MockBwLogger::BW_ERROR, $cat));
    }

    /**
     * Method to write development messages to the log
     *
     * @param   string  $message  The message to log
     * @param   string|null  $category  Optional category, uses getLogCategory() if not provided
     */
    protected function logDevelopment(string $message, ?string $category = null): void
    {
        $cat = $category ?? $this->getLogCategory();
        $this->logger->addEntry(new MockLogEntry($message, MockBwLogger::BW_DEVELOPMENT, $cat));
    }

    /**
     * Method to write info messages to the log
     *
     * @param   string  $message  The message to log
     * @param   string|null  $category  Optional category, uses getLogCategory() if not provided
     */
    protected function logInfo(string $message, ?string $category = null): void
    {
        $cat = $category ?? $this->getLogCategory();
        $this->logger->addEntry(new MockLogEntry($message, MockBwLogger::BW_INFO, $cat));
    }

    /**
     * Method to write warning messages to the log
     *
     * @param   string  $message  The message to log
     * @param   string|null  $category  Optional category, uses getLogCategory() if not provided
     */
    protected function logWarning(string $message, ?string $category = null): void
    {
        $cat = $category ?? $this->getLogCategory();
        $this->logger->addEntry(new MockLogEntry($message, MockBwLogger::BW_WARNING, $cat));
    }
}

/**
 * Test class for BwPostmanLoggerTrait
 *
 * @package BwPostman-Tests
 * @since 4.4.0
 */
class BwPostmanLoggerTraitTest extends TestCase
{
    /**
     * @var MockBwLogger
     */
    private $loggerMock;

    /**
     * Set up test fixtures
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->loggerMock = new MockBwLogger();
    }

    // ==================== HELPER: CREATE TEST OBJECTS ====================

    /**
     * Create a test object with logCategory property and public log methods
     */
    private function createTestObjectWithLogCategory(string $category): object
    {
        return new class($category) {
            use TestableBwPostmanLoggerTrait;

            public string $logCategory;
            public $logger;

            public function __construct(string $category)
            {
                $this->logCategory = $category;
            }

            public function getCategory(): string
            {
                return $this->getLogCategory();
            }

            // Make protected methods public for testing
            public function publicLogDebug(string $message, ?string $category = null): void
            {
                $this->logDebug($message, $category);
            }

            public function publicLogError(string $message, ?string $category = null): void
            {
                $this->logError($message, $category);
            }

            public function publicLogDevelopment(string $message, ?string $category = null): void
            {
                $this->logDevelopment($message, $category);
            }

            public function publicLogInfo(string $message, ?string $category = null): void
            {
                $this->logInfo($message, $category);
            }

            public function publicLogWarning(string $message, ?string $category = null): void
            {
                $this->logWarning($message, $category);
            }
        };
    }

    /**
     * Create a test object with log_cat property and public log methods
     */
    private function createTestObjectWithLogCat(string $category): object
    {
        return new class($category) {
            use TestableBwPostmanLoggerTrait;

            public string $log_cat;
            public $logger;

            public function __construct(string $category)
            {
                $this->log_cat = $category;
            }

            public function getCategory(): string
            {
                return $this->getLogCategory();
            }

            // Make protected methods public for testing
            public function publicLogDebug(string $message, ?string $category = null): void
            {
                $this->logDebug($message, $category);
            }

            public function publicLogError(string $message, ?string $category = null): void
            {
                $this->logError($message, $category);
            }

            public function publicLogDevelopment(string $message, ?string $category = null): void
            {
                $this->logDevelopment($message, $category);
            }

            public function publicLogInfo(string $message, ?string $category = null): void
            {
                $this->logInfo($message, $category);
            }

            public function publicLogWarning(string $message, ?string $category = null): void
            {
                $this->logWarning($message, $category);
            }
        };
    }

    /**
     * Create a test object without category property and public log methods
     */
    private function createTestObjectWithFallback(): object
    {
        return new class {
            use TestableBwPostmanLoggerTrait;

            public $logger;

            public function getCategory(): string
            {
                return $this->getLogCategory();
            }

            // Make protected methods public for testing
            public function publicLogDebug(string $message, ?string $category = null): void
            {
                $this->logDebug($message, $category);
            }

            public function publicLogError(string $message, ?string $category = null): void
            {
                $this->logError($message, $category);
            }

            public function publicLogDevelopment(string $message, ?string $category = null): void
            {
                $this->logDevelopment($message, $category);
            }

            public function publicLogInfo(string $message, ?string $category = null): void
            {
                $this->logInfo($message, $category);
            }

            public function publicLogWarning(string $message, ?string $category = null): void
            {
                $this->logWarning($message, $category);
            }
        };
    }

    // ==================== GET CATEGORY TESTS ====================

    /**
     * Test getLogCategory with logCategory property
     */
    public function testGetLogCategoryWithLogCategoryProperty(): void
    {
        $testObj = $this->createTestObjectWithLogCategory('test_category');
        $category = $testObj->getCategory();

        $this->assertEquals('test_category', $category);
    }

    /**
     * Test getLogCategory with log_cat property (plugin style)
     */
    public function testGetLogCategoryWithLogCatProperty(): void
    {
        $testObj = $this->createTestObjectWithLogCat('plugin_category');
        $category = $testObj->getCategory();

        $this->assertEquals('plugin_category', $category);
    }

    /**
     * Test getLogCategory with fallback when no category property exists
     */
    public function testGetLogCategoryWithFallback(): void
    {
        $testObj = $this->createTestObjectWithFallback();
        $category = $testObj->getCategory();

        $this->assertEquals('maintenance', $category);
    }

    // ==================== LOG METHODS TESTS ====================

    /**
     * Test logDebug method
     */
    public function testLogDebug(): void
    {
        $testObj = $this->createTestObjectWithLogCategory('test');
        $testObj->logger = $this->loggerMock;

        $testObj->publicLogDebug('Test debug message');

        $this->assertEquals(1, $this->loggerMock->callCount);
        $this->assertNotNull($this->loggerMock->lastEntry);
        $this->assertEquals('Test debug message', $this->loggerMock->lastEntry->message);
        $this->assertEquals(MockBwLogger::BW_DEBUG, $this->loggerMock->lastEntry->priority);
        $this->assertEquals('test', $this->loggerMock->lastEntry->category);
    }

    /**
     * Test logError method
     */
    public function testLogError(): void
    {
        $testObj = $this->createTestObjectWithLogCategory('test');
        $testObj->logger = $this->loggerMock;

        $testObj->publicLogError('Test error message');

        $this->assertEquals(1, $this->loggerMock->callCount);
        $this->assertEquals('Test error message', $this->loggerMock->lastEntry->message);
        $this->assertEquals(MockBwLogger::BW_ERROR, $this->loggerMock->lastEntry->priority);
        $this->assertEquals('test', $this->loggerMock->lastEntry->category);
    }

    /**
     * Test logDevelopment method
     */
    public function testLogDevelopment(): void
    {
        $testObj = $this->createTestObjectWithLogCategory('test');
        $testObj->logger = $this->loggerMock;

        $testObj->publicLogDevelopment('Test development message');

        $this->assertEquals(1, $this->loggerMock->callCount);
        $this->assertEquals('Test development message', $this->loggerMock->lastEntry->message);
        $this->assertEquals(MockBwLogger::BW_DEVELOPMENT, $this->loggerMock->lastEntry->priority);
    }

    /**
     * Test logInfo method
     */
    public function testLogInfo(): void
    {
        $testObj = $this->createTestObjectWithLogCategory('test');
        $testObj->logger = $this->loggerMock;

        $testObj->publicLogInfo('Test info message');

        $this->assertEquals(1, $this->loggerMock->callCount);
        $this->assertEquals('Test info message', $this->loggerMock->lastEntry->message);
        $this->assertEquals(MockBwLogger::BW_INFO, $this->loggerMock->lastEntry->priority);
    }

    /**
     * Test logWarning method
     */
    public function testLogWarning(): void
    {
        $testObj = $this->createTestObjectWithLogCategory('test');
        $testObj->logger = $this->loggerMock;

        $testObj->publicLogWarning('Test warning message');

        $this->assertEquals(1, $this->loggerMock->callCount);
        $this->assertEquals('Test warning message', $this->loggerMock->lastEntry->message);
        $this->assertEquals(MockBwLogger::BW_WARNING, $this->loggerMock->lastEntry->priority);
    }

    // ==================== CUSTOM CATEGORY TESTS ====================

    /**
     * Test logDebug with custom category parameter
     */
    public function testLogDebugWithCustomCategory(): void
    {
        $testObj = $this->createTestObjectWithLogCategory('default_category');
        $testObj->logger = $this->loggerMock;

        $testObj->publicLogDebug('Test message', 'custom_category');

        $this->assertEquals(1, $this->loggerMock->callCount);
        $this->assertEquals('custom_category', $this->loggerMock->lastEntry->category);
    }

    /**
     * Test logError with custom category parameter
     */
    public function testLogErrorWithCustomCategory(): void
    {
        $testObj = $this->createTestObjectWithLogCategory('default');
        $testObj->logger = $this->loggerMock;

        $testObj->publicLogError('Error message', 'custom_error_category');

        $this->assertEquals(1, $this->loggerMock->callCount);
        $this->assertEquals('custom_error_category', $this->loggerMock->lastEntry->category);
    }

    // ==================== LOG_CAT (PLUGIN) TESTS ====================

    /**
     * Test that log methods work with log_cat property (plugin style)
     */
    public function testLogMethodsWithLogCatProperty(): void
    {
        $testObj = $this->createTestObjectWithLogCat('plugin_cat');
        $testObj->logger = $this->loggerMock;

        $testObj->publicLogInfo('Plugin message');

        $this->assertEquals(1, $this->loggerMock->callCount);
        $this->assertEquals('plugin_cat', $this->loggerMock->lastEntry->category);
    }

    // ==================== MULTIPLE CALLS TESTS ====================

    /**
     * Test that multiple log calls work correctly
     */
    public function testMultipleLogCalls(): void
    {
        $testObj = $this->createTestObjectWithLogCategory('test');
        $testObj->logger = $this->loggerMock;

        $testObj->publicLogDebug('Message 1');
        $testObj->publicLogInfo('Message 2');
        $testObj->publicLogError('Message 3');

        $this->assertEquals(3, $this->loggerMock->callCount);
        $this->assertCount(3, $this->loggerMock->entries);
    }

    // ==================== EDGE CASES ====================

    /**
     * Test that log methods work when category parameter is null
     */
    public function testLogMethodWithNullCategory(): void
    {
        $testObj = $this->createTestObjectWithLogCategory('fallback');
        $testObj->logger = $this->loggerMock;

        $testObj->publicLogDebug('Message', null);

        $this->assertEquals(1, $this->loggerMock->callCount);
        // When category is null, should use the object's logCategory
        $this->assertEquals('fallback', $this->loggerMock->lastEntry->category);
    }

    /**
     * Test that all log methods use the correct category
     */
    public function testAllLogMethodsUseCorrectCategory(): void
    {
        $testObj = $this->createTestObjectWithLogCategory('test_category');
        $testObj->logger = $this->loggerMock;

        $methods = [
            'publicLogDebug' => MockBwLogger::BW_DEBUG,
            'publicLogError' => MockBwLogger::BW_ERROR,
            'publicLogDevelopment' => MockBwLogger::BW_DEVELOPMENT,
            'publicLogInfo' => MockBwLogger::BW_INFO,
            'publicLogWarning' => MockBwLogger::BW_WARNING
        ];

        foreach ($methods as $method => $expectedPriority) {
            $this->loggerMock->reset();
            $testObj->$method('Test message');
            $this->assertEquals(1, $this->loggerMock->callCount, "Method $method failed");
            $this->assertEquals('test_category', $this->loggerMock->lastEntry->category);
            $this->assertEquals($expectedPriority, $this->loggerMock->lastEntry->priority);
        }
    }

    /**
     * Test that category priority is used from BwLogger constants
     */
    public function testLogPriorityConstants(): void
    {
        $testObj = $this->createTestObjectWithLogCategory('test');
        $testObj->logger = $this->loggerMock;

        // Test all priority levels
        $testObj->publicLogError('Error');
        $this->assertEquals(MockBwLogger::BW_ERROR, $this->loggerMock->lastEntry->priority);

        $testObj->publicLogWarning('Warning');
        $this->assertEquals(MockBwLogger::BW_WARNING, $this->loggerMock->lastEntry->priority);

        $testObj->publicLogInfo('Info');
        $this->assertEquals(MockBwLogger::BW_INFO, $this->loggerMock->lastEntry->priority);

        $testObj->publicLogDebug('Debug');
        $this->assertEquals(MockBwLogger::BW_DEBUG, $this->loggerMock->lastEntry->priority);

        $testObj->publicLogDevelopment('Development');
        $this->assertEquals(MockBwLogger::BW_DEVELOPMENT, $this->loggerMock->lastEntry->priority);
    }

    /**
     * Test that getLogCategory prioritizes log_cat over logCategory
     */
    public function testGetLogCategoryPriority(): void
    {
        // Create object with both properties
        $testObj = new class('category_from_logCategory', 'category_from_log_cat') {
            use TestableBwPostmanLoggerTrait;

            public string $logCategory;
            public string $log_cat;

            public function __construct(string $logCategory, string $log_cat)
            {
                $this->logCategory = $logCategory;
                $this->log_cat = $log_cat;
            }

            public function getCategory(): string
            {
                return $this->getLogCategory();
            }
        };

        // log_cat should be prioritized
        $category = $testObj->getCategory();
        $this->assertEquals('category_from_log_cat', $category);
    }

    /**
     * Test that log methods work with empty category property
     */
    public function testLogWithEmptyCategory(): void
    {
        $testObj = $this->createTestObjectWithLogCategory('');
        $testObj->logger = $this->loggerMock;
        $testObj->publicLogInfo('Test message');

        $this->assertEquals(1, $this->loggerMock->callCount);
        // Empty string should be used as category
        $this->assertEquals('', $this->loggerMock->lastEntry->category);
    }
}
