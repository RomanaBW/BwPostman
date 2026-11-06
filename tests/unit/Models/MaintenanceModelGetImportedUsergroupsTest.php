<?php
/**
 * BwPostman Newsletter Component
 *
 * Unit tests for MaintenanceModel::getImportedUsergroups method.
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
 * Unit tests for MaintenanceModel::getImportedUsergroups method.
 * Uses a test double to access the private method.
 */
class MaintenanceModelGetImportedUsergroupsTest extends TestCase
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
            public function publicGetImportedUsergroups(SimpleXMLElement $xml): array
            {
                return $this->getImportedUsergroups($xml);
            }
        };
    }

    public function testGetImportedUsergroupsWithValidXml(): void
    {
        $xml = new SimpleXMLElement('<?xml version="1.0"?>' . PHP_EOL .
            '<bwpostman>' . PHP_EOL .
            '    <usergroups>' . PHP_EOL .
            '        <group><id>1</id><name>Administrator</name></group>' . PHP_EOL .
            '        <group><id>2</id><name>Registered</name></group>' . PHP_EOL .
            '    </usergroups>' . PHP_EOL .
            '</bwpostman>');

        $result = $this->testModel->publicGetImportedUsergroups($xml);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('Administrator', $result[0]->name);
        $this->assertEquals('Registered', $result[1]->name);
    }

    public function testGetImportedUsergroupsWithEmptyXml(): void
    {
        $xml = new SimpleXMLElement('<bwpostman><usergroups></usergroups></bwpostman>');
        
        $result = $this->testModel->publicGetImportedUsergroups($xml);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetImportedUsergroupsWithNoElement(): void
    {
        $xml = new SimpleXMLElement('<bwpostman></bwpostman>');
        
        $result = $this->testModel->publicGetImportedUsergroups($xml);

        $this->assertIsArray($result);
    }
}
