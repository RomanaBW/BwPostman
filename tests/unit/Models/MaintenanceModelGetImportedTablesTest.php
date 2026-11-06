<?php
/**
 * BwPostman Newsletter Component
 *
 * Unit tests for MaintenanceModel::getImportedTables method.
 *
 * @version %%version_number%%
 * @package BwPostman-Tests
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
 * @license GNU/GPL, see LICENCE.txt
 */

namespace BoldtWebservice\Component\BwPostman\Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use SimpleXMLElement;

/**
 * Unit tests for MaintenanceModel::getImportedTables method.
 * Uses a test double to access the private method.
 */
class MaintenanceModelGetImportedTablesTest extends TestCase
{
    /**
     * Test double that exposes protected/private methods for testing
     */
    private $testModel;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test double that exposes the private method
        $this->testModel = new class extends \BoldtWebservice\Component\BwPostman\Administrator\Model\MaintenanceModel {
            public function __construct()
            {
                // Skip Joomla constructor requirements
            }
            
            // Expose the private method as public for testing
            public function publicGetImportedTables(SimpleXMLElement $xml): array
            {
                return $this->getImportedTables($xml);
            }
        };
    }

    public function testGetImportedTablesWithValidXml(): void
    {
        $xml = new SimpleXMLElement('<?xml version="1.0"?>' . PHP_EOL .
            '<bwpostman>' . PHP_EOL .
            '    <tables>' . PHP_EOL .
            '        <table>' . PHP_EOL .
            '            <table_structure>' . PHP_EOL .
            '                <table_name><name>test_table_1</name></table_name>' . PHP_EOL .
            '            </table_structure>' . PHP_EOL .
            '        </table>' . PHP_EOL .
            '        <table>' . PHP_EOL .
            '            <table_structure>' . PHP_EOL .
            '                <table_name><name>test_table_2</name></table_name>' . PHP_EOL .
            '            </table_structure>' . PHP_EOL .
            '        </table>' . PHP_EOL .
            '    </tables>' . PHP_EOL .
            '</bwpostman>');

        $result = $this->testModel->publicGetImportedTables($xml);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('test_table_1', (string) $result[0]->table_structure->table_name->name);
        $this->assertEquals('test_table_2', (string) $result[1]->table_structure->table_name->name);
    }

    public function testGetImportedTablesWithEmptyXml(): void
    {
        $xml = new SimpleXMLElement('<bwpostman><tables></tables></bwpostman>');
        
        $result = $this->testModel->publicGetImportedTables($xml);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetImportedTablesWithNoTablesElement(): void
    {
        $xml = new SimpleXMLElement('<bwpostman></bwpostman>');
        
        $result = $this->testModel->publicGetImportedTables($xml);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
