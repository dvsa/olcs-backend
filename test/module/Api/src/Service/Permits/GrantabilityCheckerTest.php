<?php

namespace Dvsa\OlcsTest\Api\Service\Permits;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt\EmissionsCategoryAvailabilityCounter;
use Dvsa\Olcs\Api\Service\Permits\GrantabilityChecker;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use RuntimeException;

/**
 * GrantabilityCheckerTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class GrantabilityCheckerTest extends MockeryTestCase
{
    private $irhpApplication;

    private $emissionsCategoryAvailabilityCounter;

    private $grantabilityChecker;

    public function setUp()
    {
        $this->irhpApplication = m::mock(IrhpApplication::class);
    
        $this->emissionsCategoryAvailabilityCounter = m::mock(EmissionsCategoryAvailabilityCounter::class);

        $this->grantabilityChecker = new GrantabilityChecker($this->emissionsCategoryAvailabilityCounter);
    }

    /**
     * @dataProvider dpTestIsGrantable
     */
    public function testIsGrantable($requiredEuro5, $availableEuro5, $requiredEuro6, $availableEuro6, $isGrantable)
    {
        $irhpPermitStockId = 57;

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getId')
            ->andReturn($irhpPermitStockId);
        $irhpPermitApplication->shouldReceive('getRequiredEuro5')
            ->andReturn($requiredEuro5);
        $irhpPermitApplication->shouldReceive('getRequiredEuro6')
            ->andReturn($requiredEuro6);

        $this->irhpApplication->shouldReceive('getIrhpPermitType->isEcmtShortTerm')
            ->andReturn(true);
        $this->irhpApplication->shouldReceive('getFirstIrhpPermitApplication')
            ->andReturn($irhpPermitApplication);

        $this->emissionsCategoryAvailabilityCounter->shouldReceive('getCount')
            ->with($irhpPermitStockId, RefData::EMISSIONS_CATEGORY_EURO5_REF)
            ->andReturn($availableEuro5);
        $this->emissionsCategoryAvailabilityCounter->shouldReceive('getCount')
            ->with($irhpPermitStockId, RefData::EMISSIONS_CATEGORY_EURO6_REF)
            ->andReturn($availableEuro6);

        $this->assertEquals(
            $isGrantable,
            $this->grantabilityChecker->isGrantable($this->irhpApplication)
        );
    }

    public function dpTestIsGrantable()
    {
        return [
            [5, 5, 5, 5, true],
            [6, 5, 5, 5, false],
            [5, 5, 6, 5, false],
            [6, 5, 6, 5, false],
            [5, 6, 5, 5, true],
            [5, 5, 5, 6, true],
            [5, 6, 5, 6, true],
            [5, 6, 6, 5, false],
        ];
    }

    public function testExceptionWhenNotEcmtShortTerm()
    {
        $this->irhpApplication->shouldReceive('getIrhpPermitType->isEcmtShortTerm')
            ->andReturn(false);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('GrantabilityChecker is only implemented for ecmt short term');

        $this->grantabilityChecker->isGrantable($this->irhpApplication);
    }
}
