<?php

namespace Dvsa\OlcsTest\Cli\Domain\Query\Permits;

use Dvsa\Olcs\Cli\Domain\Query\Permits\StockLackingRandomisedScore;

/**
 * Stock Lacking Randomised Score test
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class StockLackingRandomisedScoreTest extends \PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $sut = StockLackingRandomisedScore::create(
            [
                'stockId' => 13
            ]
        );

        static::assertEquals(13, $sut->getStockId());
    }
}
