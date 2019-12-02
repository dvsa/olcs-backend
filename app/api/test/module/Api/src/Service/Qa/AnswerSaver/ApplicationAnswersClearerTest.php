<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\AnswerSaver;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\ApplicationAnswersClearer;
use Dvsa\Olcs\Api\Service\Qa\Facade\SupplementedApplicationSteps\SupplementedApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\Facade\SupplementedApplicationSteps\SupplementedApplicationStepsProvider;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * ApplicationAnswersClearerTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ApplicationAnswersClearerTest extends MockeryTestCase
{
    public function testClear()
    {
        $irhpApplication = m::mock(IrhpApplication::class);

        $applicationStep1 = m::mock(ApplicationStep::class);

        $formControlStrategy1 = m::mock(FormControlStrategyInterface::class);
        $formControlStrategy1->shouldReceive('clearAnswer')
            ->with($applicationStep1, $irhpApplication)
            ->once()
            ->ordered()
            ->globally();

        $supplementedApplicationStep1 = m::mock(SupplementedApplicationStep::class);
        $supplementedApplicationStep1->shouldReceive('getFormControlStrategy')
            ->andReturn($formControlStrategy1);
        $supplementedApplicationStep1->shouldReceive('getApplicationStep')
            ->andReturn($applicationStep1);

        $applicationStep2 = m::mock(ApplicationStep::class);

        $formControlStrategy2 = m::mock(FormControlStrategyInterface::class);
        $formControlStrategy2->shouldReceive('clearAnswer')
            ->with($applicationStep2, $irhpApplication)
            ->once()
            ->ordered()
            ->globally();

        $supplementedApplicationStep2 = m::mock(SupplementedApplicationStep::class);
        $supplementedApplicationStep2->shouldReceive('getFormControlStrategy')
            ->andReturn($formControlStrategy2);
        $supplementedApplicationStep2->shouldReceive('getApplicationStep')
            ->andReturn($applicationStep2);

        $supplementedApplicationSteps = [
            $supplementedApplicationStep1,
            $supplementedApplicationStep2
        ];

        $supplementedApplicationStepsProvider = m::mock(SupplementedApplicationStepsProvider::class);
        $supplementedApplicationStepsProvider->shouldReceive('get')
            ->with($irhpApplication)
            ->andReturn($supplementedApplicationSteps);

        $applicationAnswersClearer = new ApplicationAnswersClearer($supplementedApplicationStepsProvider);
        $applicationAnswersClearer->clear($irhpApplication);
    }
}
