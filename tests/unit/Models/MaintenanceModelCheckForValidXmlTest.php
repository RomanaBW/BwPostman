<?php
/**
 * BwPostman Newsletter Component
 *
 * Unit tests for MaintenanceModel::checkForValidXML method.
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
 * Unit tests for MaintenanceModel::checkForValidXML method.
 * Uses a test double to access the private method.
 */
class MaintenanceModelCheckForValidXmlTest extends TestCase
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
            public function publicCheckForValidXml(SimpleXMLElement $xml, string $file): void
            {
                $this->checkForValidXML($xml, $file);
            }
        };
    }

    public function testCheckForValidXmlWithValidXml(): void
    {
        $xml = new SimpleXMLElement('<?xml version="1.0"?><bwpostman></bwpostman>');
        $file = '/path/to/file.xml';
        
        // Should not throw an exception
        $this->testModel->publicCheckForValidXml($xml, $file);
        $this->assertTrue(true); // If we get here, no exception was thrown
    }

    public function testCheckForValidXmlWithInvalidXml(): void
    {
        $file = '/path/to/file.xml';
        
        // Create invalid XML (empty string)
        $invalidXml = '';
        
        $this->expectException(\Exception::class);
        $this->testModel->publicCheckForValidXml($invalidXml, $file);
    }

    public function testCheckForValidXmlWithMalformedXml(): void
    {
        $file = '/path/to/file.xml';
        $malformedXml = '<bwpostman><unclosed>';
        
        $this->expectException(\Exception::class);
        $this->testModel->publicCheckForValidXml($malformedXml, $file);
    }
}
