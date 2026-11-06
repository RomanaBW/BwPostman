<?php
/**
 * BwPostman Newsletter Component
 *
 * Unit tests for MaintenanceModel::getImportedGeneralData method.
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
 * Unit tests for MaintenanceModel::getImportedGeneralData method.
 * Uses a test double to access the private method.
 */
class MaintenanceModelGetImportedGeneralDataTest extends TestCase
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
            public function publicGetImportedGeneralData(SimpleXMLElement $xml): array
            {
                return $this->getImportedGeneralData($xml);
            }
        };
    }

    public function testGetImportedGeneralDataWithValidXml(): void
    {
        $xml = new SimpleXMLElement('<?xml version="1.0"?>' . PHP_EOL .
            '<bwpostman>' . PHP_EOL .
            '    <general>' . PHP_EOL .
            '        <data><key>value</key></data>' . PHP_EOL .
            '    </general>' . PHP_EOL .
            '</bwpostman>');

        $result = $this->testModel->publicGetImportedGeneralData($xml);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
    }

    public function testGetImportedGeneralDataWithEmptyXml(): void
    {
        $xml = new SimpleXMLElement('<bwpostman><general></general></bwpostman>');
        
        $result = $this->testModel->publicGetImportedGeneralData($xml);

        $this->assertIsArray($result);
    }

    public function testGetImportedGeneralDataWithNoGeneralElement(): void
    {
        $xml = new SimpleXMLElement('<bwpostman></bwpostman>');
        
        $result = $this->testModel->publicGetImportedGeneralData($xml);

        $this->assertIsArray($result);
    }
}
