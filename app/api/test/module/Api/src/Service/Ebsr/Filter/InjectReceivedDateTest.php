<?php

namespace Dvsa\OlcsTest\Api\Service\Ebsr\Filter;

use Dvsa\Olcs\Api\Service\Ebsr\Filter\InjectReceivedDate;
use \PHPUnit\Framework\TestCase as TestCase;

/**
 * Class InjectReceivedDateTest
 * @package Dvsa\OlcsTest\Api\Service\Ebsr\Filter
 */
class InjectReceivedDateTest extends TestCase
{
    public function testFilter()
    {
        $sut = new InjectReceivedDate();

        $result = $sut->filter([]);

        $this->assertEquals(date('Y-m-d'), $result['receivedDate']);
    }
}
