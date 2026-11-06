<?php
/**
 * BwPostman Newsletter Component
 *
 * XML parser interface for BwPostman.
 *
 * @version %%version_number%%
 * @package BwPostman-Interfaces
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
 * @license GNU/GPL, see LICENCE.txt
 */

namespace BoldtWebservice\Component\BwPostman\Interfaces;

use SimpleXMLElement;

/**
 * Interface for XML parsing operations.
 * Provides abstraction for XML processing to enable testing.
 *
 * @package BwPostman-Interfaces
 * @since 4.4.0
 */
interface XmlParserInterface
{
    /**
     * Parse an XML file.
     *
     * @param string $file The path to the XML file
     *
     * @return SimpleXMLElement The parsed XML element
     *
     * @throws \Exception If the XML cannot be parsed
     */
    public function parse(string $file): SimpleXMLElement;

    /**
     * Validate an XML element.
     *
     * @param SimpleXMLElement $xml  The XML element to validate
     * @param string            $file The original file path (for error messages)
     *
     * @return void
     *
     * @throws \Exception If the XML is invalid
     */
    public function validate(SimpleXMLElement $xml, string $file): void;

    /**
     * Get table data from XML.
     *
     * @param SimpleXMLElement $xml The XML element
     *
     * @return array Array of table data
     */
    public function getTables(SimpleXMLElement $xml): array;

    /**
     * Get general data from XML.
     *
     * @param SimpleXMLElement $xml The XML element
     *
     * @return array General data array
     */
    public function getGeneralData(SimpleXMLElement $xml): array;

    /**
     * Get component assets from XML.
     *
     * @param SimpleXMLElement $xml The XML element
     *
     * @return array Component assets array
     */
    public function getComponentAssets(SimpleXMLElement $xml): array;

    /**
     * Get user groups from XML.
     *
     * @param SimpleXMLElement $xml The XML element
     *
     * @return array User groups array
     */
    public function getUsergroups(SimpleXMLElement $xml): array;
}
