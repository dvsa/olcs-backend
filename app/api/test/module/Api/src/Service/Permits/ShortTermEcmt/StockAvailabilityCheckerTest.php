<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\ShortTermEcmt;

use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt\StockAvailabilityChecker;
use Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt\EmissionsCategoryAvailabilityChecker;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * StockAvailabilityCheckerTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class StockAvailabilityCheckerTest extends MockeryTestCase
{
    /**
     * @dataProvider dpTestHasAvailability
     */
    public function testHasAvailability($euro5Available, $euro6Available, $expected)
    {
        $irhpPermitStockId = 48;

        $emissionsCategoryAvailabilityChecker = m::mock(EmissionsCategoryAvailabilityChecker::class);
        $emissionsCategoryAvailabilityChecker->shouldReceive('hasAvailability')
            ->with($irhpPermitStockId, RefData::EMISSIONS_CATEGORY_EURO5_REF)
            ->andReturn($euro5Available);
        $emissionsCategoryAvailabilityChecker->shouldReceive('hasAvailability')
            ->with($irhpPermitStockId, RefData::EMISSIONS_CATEGORY_EURO6_REF)
            ->andReturn($euro6Available);

        $stockAvailabilityChecker = new StockAvailabilityChecker($emissionsCategoryAvailabilityChecker);

        $this->assertEquals(
            $expected,
            $stockAvailabilityChecker->hasAvailability($irhpPermitStockId)
        );
    }

    public function dpTestHasAvailability()
    {
        return [
            [true, true, true],
            [true, false, true],
            [false, true, true],
            [false, false, false],
        ];
    }
}
