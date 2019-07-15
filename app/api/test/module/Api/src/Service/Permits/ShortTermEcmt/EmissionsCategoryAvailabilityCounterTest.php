<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\ShortTermEcmt;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitRange as IrhpPermitRangeRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepository;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt\EmissionsCategoryAvailabilityCounter;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * EmissionsCategoryAvailabilityCounterTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class EmissionsCategoryAvailabilityCounterTest extends MockeryTestCase
{
    private $irhpPermitRangeRepo;

    private $irhpPermitApplicationRepo;

    private $irhpPermitRepo;

    private $emissionsCategoryAvailabilityCounter;

    public function setUp()
    {
        $this->irhpPermitRangeRepo = m::mock(IrhpPermitRangeRepository::class);

        $this->irhpPermitApplicationRepo = m::mock(IrhpPermitApplicationRepository::class);

        $this->irhpPermitRepo = m::mock(IrhpPermitRepository::class);

        $this->emissionsCategoryAvailabilityCounter = new EmissionsCategoryAvailabilityCounter(
            $this->irhpPermitRangeRepo,
            $this->irhpPermitApplicationRepo,
            $this->irhpPermitRepo
        );
    }

    public function testGetCount()
    {
        $irhpPermitStockId = 22;
        $emissionsCategoryId = RefData::EMISSIONS_CATEGORY_EURO5_REF;

        $this->irhpPermitRangeRepo->shouldReceive('getCombinedRangeSize')
            ->with($irhpPermitStockId, $emissionsCategoryId)
            ->andReturn(40);

        $this->irhpPermitApplicationRepo->shouldReceive('getRequiredPermitCountWhereApplicationAwaitingPayment')
            ->with($irhpPermitStockId, $emissionsCategoryId)
            ->andReturn(8);

        $this->irhpPermitRepo->shouldReceive('getPermitCount')
            ->with($irhpPermitStockId, $emissionsCategoryId)
            ->andReturn(14);

        $this->assertEquals(
            18,
            $this->emissionsCategoryAvailabilityCounter->getCount($irhpPermitStockId, $emissionsCategoryId)
        );
    }

    public function testReturnZeroOnNullCombinedRangeSize()
    {
        $irhpPermitStockId = 47;
        $emissionsCategoryId = RefData::EMISSIONS_CATEGORY_EURO6_REF;

        $this->irhpPermitRangeRepo->shouldReceive('getCombinedRangeSize')
            ->with($irhpPermitStockId, $emissionsCategoryId)
            ->andReturn(null);

        $this->assertEquals(
            0,
            $this->emissionsCategoryAvailabilityCounter->getCount($irhpPermitStockId, $emissionsCategoryId)
        );
    }
}
