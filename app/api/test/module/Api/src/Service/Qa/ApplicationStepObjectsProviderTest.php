<?php

namespace Dvsa\OlcsTest\Api\Service\Qa;

use DateTime;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Repository\ApplicationStep as ApplicationStepRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationPath;
use Dvsa\Olcs\APi\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Qa\ApplicationStepObjectsProvider;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * ApplicationStepObjectsProviderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ApplicationStepObjectsProviderTest extends MockeryTestCase
{
    private $slug;

    private $irhpApplicationId;

    private $applicationStepRepo;

    private $irhpApplicationRepo;

    private $applicationStepObjectsProvider;

    public function setUp()
    {
        $this->slug = 'removals-eligibility';
        $this->irhpApplicationId = 77;

        $this->applicationStepRepo = m::mock(ApplicationStepRepo::class);
        $this->irhpApplicationRepo = m::mock(IrhpApplicationRepo::class);

        $this->applicationStepObjectsProvider = new ApplicationStepObjectsProvider(
            $this->applicationStepRepo,
            $this->irhpApplicationRepo
        );
    }

    /**
     * @dataProvider dpTestGetObjects
     */
    public function testGetObjects($previousApplicationStep, $irhpApplicationAnswer)
    {
        $applicationPathId = 44;

        $applicationPath = m::mock(ApplicationPath::class);
        $applicationPath->shouldReceive('getId')
            ->andReturn($applicationPathId);

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('isNotYetSubmitted')
            ->andReturn(true);
        $irhpApplication->shouldReceive('getAnswer')
            ->andReturn($irhpApplicationAnswer);
        $irhpApplication->shouldReceive('getActiveApplicationPath')
            ->andReturn($applicationPath);

        $this->irhpApplicationRepo->shouldReceive('fetchById')
            ->with($this->irhpApplicationId)
            ->andReturn($irhpApplication);

        $applicationStep = m::mock(ApplicationStep::class);
        $applicationStep->shouldReceive('getPreviousApplicationStep')
            ->andReturn($previousApplicationStep);

        $this->applicationStepRepo->shouldReceive('fetchByApplicationPathIdAndSlug')
            ->with($applicationPathId, $this->slug)
            ->andReturn($applicationStep);

        $objects = $this->applicationStepObjectsProvider->getObjects($this->irhpApplicationId, $this->slug);

        $this->assertInternalType('array', $objects);
        $this->assertArrayHasKey('applicationStep', $objects);
        $this->assertArrayHasKey('irhpApplication', $objects);
        $this->assertCount(2, $objects);
        $this->assertSame($applicationStep, $objects['applicationStep']);
        $this->assertSame($irhpApplication, $objects['irhpApplication']);
    }

    public function dpTestGetObjects()
    {
        return [
            [null, null],
            [m::mock(ApplicationStep::class), '128']
        ];
    }

    public function testExceptionOnAlreadySubmitted()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage(ApplicationStepObjectsProvider::ERR_ALREADY_SUBMITTED);

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('isNotYetSubmitted')
            ->andReturn(false);

        $this->irhpApplicationRepo->shouldReceive('fetchById')
            ->with($this->irhpApplicationId)
            ->andReturn($irhpApplication);

        $this->applicationStepObjectsProvider->getObjects($this->irhpApplicationId, $this->slug);
    }

    public function testExceptionOnNotAccessible()
    {
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage(ApplicationStepObjectsProvider::ERR_NOT_ACCESSIBLE);

        $applicationPathId = 44;

        $applicationPath = m::mock(ApplicationPath::class);
        $applicationPath->shouldReceive('getId')
            ->andReturn($applicationPathId);

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('isNotYetSubmitted')
            ->andReturn(true);
        $irhpApplication->shouldReceive('getAnswer')
            ->andReturn(null);
        $irhpApplication->shouldReceive('getActiveApplicationPath')
            ->andReturn($applicationPath);

        $this->irhpApplicationRepo->shouldReceive('fetchById')
            ->with($this->irhpApplicationId)
            ->andReturn($irhpApplication);

        $previousApplicationStep = m::mock(ApplicationStep::class);

        $applicationStep = m::mock(ApplicationStep::class);
        $applicationStep->shouldReceive('getPreviousApplicationStep')
            ->andReturn($previousApplicationStep);

        $this->applicationStepRepo->shouldReceive('fetchByApplicationPathIdAndSlug')
            ->with($applicationPathId, $this->slug)
            ->andReturn($applicationStep);

        $this->applicationStepObjectsProvider->getObjects($this->irhpApplicationId, $this->slug);
    }
}
