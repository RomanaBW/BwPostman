<?php
/**
 * @package
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace BoldtWebservice\Component\BwPostman\Tests\Unit\Mocks;

use BoldtWebservice\Component\BwPostman\Interfaces\XmlParserInterface;
use SimpleXMLElement;

class MockXmlParser implements XmlParserInterface
{
    public $shouldThrow = false;
    public $mockTables = [];
    public $mockGeneralData = [];
    public $mockComponentAssets = [];
    public $mockUsergroups = [];

    public function parse(string $file): SimpleXMLElement
    {
        if ($this->shouldThrow)
        {
            throw new \Exception("Invalid XML");
        }

        return new SimpleXMLElement('<bwpostman></bwpostman>');
    }

    public function validate(SimpleXMLElement $xml, string $file): void
    {
        if ($this->shouldThrow)
        {
            throw new \Exception("Invalid XML in file: " . $file);
        }
    }

    public function getTables(SimpleXMLElement $xml): array
    {
        return $this->mockTables;
    }

    public function getGeneralData(SimpleXMLElement $xml): array
    {
        return $this->mockGeneralData;
    }

    public function getComponentAssets(SimpleXMLElement $xml): array
    {
        return $this->mockComponentAssets;
    }

    public function getUsergroups(SimpleXMLElement $xml): array
    {
        return $this->mockUsergroups;
    }
}
