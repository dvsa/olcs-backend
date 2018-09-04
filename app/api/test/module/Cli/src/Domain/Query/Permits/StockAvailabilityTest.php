<?php

namespace Dvsa\OlcsTest\Cli\Domain\Query\Permits;

use Dvsa\Olcs\Cli\Domain\Query\Permits\StockAvailability;

/**
 * Stock Availability test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class StockAvailabilityTest extends \PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $sut = StockAvailability::create(
            [
                'stockId' => 7
            ]
        );

        static::assertEquals(7, $sut->getStockId());
    }
}
