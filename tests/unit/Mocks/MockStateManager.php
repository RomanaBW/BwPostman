<?php
/**
 * @package
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

<?php
namespace BoldtWebservice\Component\BwPostman\Tests\Unit\Mocks;

use BoldtWebservice\Component\BwPostman\Interfaces\BwDatabaseInterface;

class MockDatabase implements BwDatabaseInterface
{
    public $prefix = 'jos_';

    public function getPrefix(): string
    {
        return $this->prefix;
    }
}
