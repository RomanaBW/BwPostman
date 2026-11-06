<?php
/**
 * BwPostman Newsletter Component
 *
 * Unit tests for MaintenanceModel::getImportedComponentAssets method.
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
 * Unit tests for MaintenanceModel::getImportedComponentAssets method.
 * Uses a test double to access the private method.
 */
class MaintenanceModelGetImportedComponentAssetsTest extends TestCase
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
            public function publicGetImportedComponentAssets(SimpleXMLElement $xml): array
            {
                return $this->getImportedComponentAssets($xml);
            }
        };
    }

    public function testGetImportedComponentAssetsWithValidXml(): void
    {
        $xml = new SimpleXMLElement('<?xml version="1.0"?>' . PHP_EOL .
            '<bwpostman>' . PHP_EOL .
            '    <component_assets>' . PHP_EOL .
            '        <asset><name>test_asset</name><value>test_value</value></asset>' . PHP_EOL .
            '    </component_assets>' . PHP_EOL .
            '</bwpostman>');

        $result = $this->testModel->publicGetImportedComponentAssets($xml);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    public function testGetImportedComponentAssetsWithEmptyXml(): void
    {
        $xml = new SimpleXMLElement('<bwpostman><component_assets></component_assets></bwpostman>');
        
        $result = $this->testModel->publicGetImportedComponentAssets($xml);

        $this->assertIsArray($result);
    }

    public function testGetImportedComponentAssetsWithNoElement(): void
    {
        $xml = new SimpleXMLElement('<bwpostman></bwpostman>');
        
        $result = $this->testModel->publicGetImportedComponentAssets($xml);

        $this->assertIsArray($result);
    }
}
