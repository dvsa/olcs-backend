<?php

namespace Dvsa\OlcsTest\Api\Domain\Query\Application;

use PHPUnit_Framework_TestCase;
use Dvsa\Olcs\Api\Domain\Query\Application\NotTakenUpList;

/**
 * NotTakenUpList test
 */
class NotTakenUpListTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $query = NotTakenUpList::create(['date' => 'foo']);

        $this->assertEquals('foo', $query->getDate());
    }
}
