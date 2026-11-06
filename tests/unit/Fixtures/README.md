# BwPostman Test Fixtures

This directory contains test fixtures for BwPostman unit tests.

## Files

### `bwpostman_test_structure.xml`
A minimal valid BwPostman backup XML file structure used for testing the parsing methods.

This file contains:
- Root element: `<mysqldump>` with `xmlns:xsi` attribute
- `<database>` element with `name` attribute
- `<Generals>` section with:
  - `<BwPostmanVersion>` - BwPostman component version
  - `<SaveDate>` - Date when the backup was created
  - `<component_assets>` - Array of dataset elements for component assets
  - `<component_usergroups>` - Array of usergroup elements
- `<tables>` section with:
  - `<table_structure>` elements - Database table structure definitions
  - `<table_data>` elements - Actual data rows for tables
  - `<table_assets>` elements - Asset information for tables

### `bwpostman_backup.dtd`
Document Type Definition (DTD) for BwPostman backup XML files.

This DTD defines the complete structure of BwPostman backup files, including:
- The root `<mysqldump>` element
- `<database>` element with required `name` attribute
- `<Generals>` section structure
- `<tables>` section with all child element types
- Field definitions within table structures
- Key definitions within table structures

## XML Structure Overview

```
<mysqldump xmlns:xsi="https://www.w3.org/TR/xmlschema-1">
  <database name="database_name">
    <Generals>
      <BwPostmanVersion>X.Y.Z</BwPostmanVersion>
      <SaveDate>YYYY-MM-DD_HH:MM</SaveDate>
      <component_assets>
        <dataset>...</dataset>
        ...
      </component_assets>
      <component_usergroups>
        <usergroup>...</usergroup>
        ...
      </component_usergroups>
    </Generals>
    <tables>
      <table_structure table="table_name">...</table_structure>
      <table_data table="table_name">...</table_data>
      <table_assets table="table_name">...</table_assets>
      ...
    </tables>
  </database>
</mysqldump>
```

## Usage

These fixtures are used by the unit tests in `tests/unit/Models/`:
- `MaintenanceModelGetImportedTablesTest`
- `MaintenanceModelGetImportedGeneralDataTest`
- `MaintenanceModelGetImportedComponentAssetsTest`
- `MaintenanceModelGetImportedUsergroupsTest`
- `MaintenanceModelCheckForValidXmlTest`

## Note on XML Parsing

The BwPostman code uses SimpleXMLElement for parsing. When iterating over XML elements:

- `$xml->database->tables` returns the `<tables>` element itself when there's only one
- To get the children, use: `$xml->database->tables->children()`
- Or iterate directly: `foreach ($xml->database->tables->children() as $table)`

This is important because the original `getImportedTables` method had a bug where it used:
```php
foreach ($xml->database->tables as $table)
```

This only works if there are multiple `<tables>` elements, which is not the case. The correct code is:
```php
foreach ($xml->database->tables->children() as $table)
```

The same applies to other similar methods that iterate over child elements.
