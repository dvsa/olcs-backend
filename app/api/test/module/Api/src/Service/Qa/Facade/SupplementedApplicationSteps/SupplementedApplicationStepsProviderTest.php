<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Facade\SubmittedApplicationSteps;

use DateTime;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationPath;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Qa\FormControlStrategyProvider;
use Dvsa\Olcs\Api\Service\Qa\Strategy\FormControlStrategyInterface;
use Dvsa\Olcs\Api\Service\Qa\Facade\SupplementedApplicationSteps\SupplementedApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\Facade\SupplementedApplicationSteps\SupplementedApplicationStepFactory;
use Dvsa\Olcs\Api\Service\Qa\Facade\SupplementedApplicationSteps\SupplementedApplicationStepsProvider;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * SupplementedApplicationStepsProviderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class SupplementedApplicationStepsProviderTest extends MockeryTestCase
{
    public function testGet()
    {
        $applicationStep1 = m::mock(ApplicationStep::class);
        $applicationStep2 = m::mock(ApplicationStep::class);

        $applicationSteps = [
            $applicationStep1,
            $applicationStep2
        ];

        $applicationPath = m::mock(ApplicationPath::class);
        $applicationPath->shouldReceive('getApplicationSteps')
            ->andReturn($applicationSteps);

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getActiveApplicationPath')
            ->andReturn($applicationPath);

        $formControlStrategy1 = m::mock(FormControlStrategyInterface::class);
        $formControlStrategy2 = m::mock(FormControlStrategyInterface::class);

        $formControlStrategyProvider = m::mock(FormControlStrategyProvider::class);
        $formControlStrategyProvider->shouldReceive('get')
            ->with($applicationStep1)
            ->andReturn($formControlStrategy1);
        $formControlStrategyProvider->shouldReceive('get')
            ->with($applicationStep2)
            ->andReturn($formControlStrategy2);

        $supplementedApplicationStep1 = m::mock(SupplementedApplicationStep::class);
        $supplementedApplicationStep2 = m::mock(SupplementedApplicationStep::class);

        $supplementedApplicationStepFactory = m::mock(SupplementedApplicationStepFactory::class);
        $supplementedApplicationStepFactory->shouldReceive('create')
            ->with($applicationStep1, $formControlStrategy1)
            ->andReturn($supplementedApplicationStep1);
        $supplementedApplicationStepFactory->shouldReceive('create')
            ->with($applicationStep2, $formControlStrategy2)
            ->andReturn($supplementedApplicationStep2);

        $supplementedApplicationStepsProvider = new SupplementedApplicationStepsProvider(
            $formControlStrategyProvider,
            $supplementedApplicationStepFactory
        );

        $expected = [
            $supplementedApplicationStep1,
            $supplementedApplicationStep2
        ];

        $this->assertEquals(
            $expected,
            $supplementedApplicationStepsProvider->get($irhpApplication)
        );
    }
}
