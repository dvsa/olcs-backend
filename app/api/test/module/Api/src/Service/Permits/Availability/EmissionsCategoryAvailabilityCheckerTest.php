<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Availability;

use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Permits\Availability\EmissionsCategoryAvailabilityChecker;
use Dvsa\Olcs\Api\Service\Permits\Availability\EmissionsCategoryAvailabilityCounter;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * EmissionsCategoryAvailabilityCheckerTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class EmissionsCategoryAvailabilityCheckerTest extends MockeryTestCase
{
    /**
     * @dataProvider dpTestHasAvailability
     */
    public function testHasAvailability($irhpPermitStockId, $emissionsCategoryId, $availableCount, $expectedReturn)
    {
        $emissionsCategoryAvailabilityCounter = m::mock(EmissionsCategoryAvailabilityCounter::class);
        $emissionsCategoryAvailabilityCounter->shouldReceive('getCount')
            ->with($irhpPermitStockId, $emissionsCategoryId)
            ->andReturn($availableCount);

        $emissionsCategoryAvailabilityChecker = new EmissionsCategoryAvailabilityChecker(
            $emissionsCategoryAvailabilityCounter
        );

        $this->assertEquals(
            $expectedReturn,
            $emissionsCategoryAvailabilityChecker->hasAvailability($irhpPermitStockId, $emissionsCategoryId)
        );
    }

    public function dpTestHasAvailability()
    {
        return [
            [1, RefData::EMISSIONS_CATEGORY_EURO5_REF, 0, false],
            [2, RefData::EMISSIONS_CATEGORY_EURO5_REF, 1, true],
            [3, RefData::EMISSIONS_CATEGORY_EURO5_REF, 2, true],
            [4, RefData::EMISSIONS_CATEGORY_EURO6_REF, 0, false],
            [5, RefData::EMISSIONS_CATEGORY_EURO6_REF, 1, true],
            [6, RefData::EMISSIONS_CATEGORY_EURO6_REF, 2, true]
        ];
    }
}
