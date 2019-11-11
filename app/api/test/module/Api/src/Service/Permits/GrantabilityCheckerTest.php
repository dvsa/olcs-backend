<?php

namespace Dvsa\OlcsTest\Api\Service\Permits;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt\EmissionsCategoriesGrantabilityChecker;
use Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt\CandidatePermitsGrantabilityChecker;
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

    private $emissionsCategoriesGrantabilityChecker;

    private $candidatePermitsGrantabilityChecker;

    private $grantabilityChecker;

    public function setUp()
    {
        $this->irhpApplication = m::mock(IrhpApplication::class);

        $this->emissionsCategoriesGrantabilityChecker = m::mock(EmissionsCategoriesGrantabilityChecker::class);

        $this->candidatePermitsGrantabilityChecker = m::mock(CandidatePermitsGrantabilityChecker::class);
    
        $this->grantabilityChecker = new GrantabilityChecker(
            $this->emissionsCategoriesGrantabilityChecker,
            $this->candidatePermitsGrantabilityChecker
        );
    }

    /**
     * @dataProvider dpTrueFalse
     */
    public function testIsGrantableEmissionsCategories($isGrantable)
    {
        $this->irhpApplication->shouldReceive('getAllocationMode')
            ->withNoArgs()
            ->andReturn(IrhpPermitStock::ALLOCATION_MODE_EMISSIONS_CATEGORIES);
        $this->irhpApplication->shouldReceive('getIrhpPermitType->isEcmtShortTerm')
            ->withNoArgs()
            ->andReturn(true);

        $this->emissionsCategoriesGrantabilityChecker->shouldReceive('isGrantable')
            ->with($this->irhpApplication)
            ->once()
            ->andReturn($isGrantable);

        $this->assertEquals(
            $isGrantable,
            $this->grantabilityChecker->isGrantable($this->irhpApplication)
        );
    }

    /**
     * @dataProvider dpTrueFalse
     */
    public function testIsGrantableCandidatePermits($isGrantable)
    {
        $this->irhpApplication->shouldReceive('getAllocationMode')
            ->withNoArgs()
            ->andReturn(IrhpPermitStock::ALLOCATION_MODE_CANDIDATE_PERMITS);
        $this->irhpApplication->shouldReceive('getIrhpPermitType->isEcmtShortTerm')
            ->withNoArgs()
            ->andReturn(true);

        $this->candidatePermitsGrantabilityChecker->shouldReceive('isGrantable')
            ->with($this->irhpApplication)
            ->once()
            ->andReturn($isGrantable);

        $this->assertEquals(
            $isGrantable,
            $this->grantabilityChecker->isGrantable($this->irhpApplication)
        );
    }

    public function dpTrueFalse()
    {
        return [
            [true],
            [false],
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

    /**
     * @dataProvider dpExceptionUnsupportedAllocationMode
     */
    public function testExceptionUnsupportedAllocationMode($allocationMode)
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to grant application due to unsupported allocation mode');

        $this->irhpApplication->shouldReceive('getAllocationMode')
            ->withNoArgs()
            ->andReturn($allocationMode);
        $this->irhpApplication->shouldReceive('getIrhpPermitType->isEcmtShortTerm')
            ->withNoArgs()
            ->andReturn(true);

        $this->grantabilityChecker->isGrantable($this->irhpApplication);
    }

    public function dpExceptionUnsupportedAllocationMode()
    {
        return [
            [IrhpPermitStock::ALLOCATION_MODE_STANDARD],
            [IrhpPermitStock::ALLOCATION_MODE_STANDARD_WITH_EXPIRY],
        ];
    }
}
