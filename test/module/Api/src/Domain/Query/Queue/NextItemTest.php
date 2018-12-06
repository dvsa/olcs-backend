<?php

namespace Dvsa\OlcsTest\Api\Domain\Query\Queue;

use PHPUnit_Framework_TestCase;
use Dvsa\Olcs\Api\Domain\Query\Queue\NextItem;

/**
 * NextItem test
 */
class NextItemTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $query = NextItem::create(['includeTypes' => 'foo', 'excludeTypes' => 'bar']);

        $this->assertEquals('foo', $query->getIncludeTypes());
        $this->assertEquals('bar', $query->getExcludeTypes());
    }
}
