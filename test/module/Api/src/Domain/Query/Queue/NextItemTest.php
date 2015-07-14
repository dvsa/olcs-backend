<?php

namespace Dvsa\OlcsTest\Api\Domain\Query\Queue;

use PHPUnit_Framework_TestCase;
use Dvsa\Olcs\Api\Domain\Query\Queue\NextItem;

/**
 * Application test
 */
class ApplicationTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $query = NextItem::create(['type' => 'foo']);

        $this->assertEquals('foo', $query->getType());
    }
}
