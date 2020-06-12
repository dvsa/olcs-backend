<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\AnswerSaver;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\ApplicationAnswersClearer;
use Dvsa\Olcs\Api\Service\Qa\Facade\SupplementedApplicationSteps\SupplementedApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\Facade\SupplementedApplicationSteps\SupplementedApplicationStepsProvider;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\QaContextFactory;
use Dvsa\Olcs\Api\Service\Qa\QaEntityInterface;
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
        $qaEntity = m::mock(QaEntityInterface::class);

        $applicationStep1 = m::mock(ApplicationStep::class);

        $qaContext1 = m::mock(QaContext::class);

        $qaContextFactory = m::mock(QaContextFactory::class);

        $qaContextFactory->shouldReceive('create')
            ->with($applicationStep1, $qaEntity)
            ->andReturn($qaContext1);

        $formControlStrategy1 = m::mock(FormControlStrategyInterface::class);
        $formControlStrategy1->shouldReceive('clearAnswer')
            ->with($qaContext1)
            ->once()
            ->ordered()
            ->globally();

        $supplementedApplicationStep1 = m::mock(SupplementedApplicationStep::class);
        $supplementedApplicationStep1->shouldReceive('getFormControlStrategy')
            ->andReturn($formControlStrategy1);
        $supplementedApplicationStep1->shouldReceive('getApplicationStep')
            ->andReturn($applicationStep1);

        $applicationStep2 = m::mock(ApplicationStep::class);

        $qaContext2 = m::mock(QaContext::class);

        $qaContextFactory->shouldReceive('create')
            ->with($applicationStep2, $qaEntity)
            ->andReturn($qaContext2);

        $formControlStrategy2 = m::mock(FormControlStrategyInterface::class);
        $formControlStrategy2->shouldReceive('clearAnswer')
            ->with($qaContext2)
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
            ->with($qaEntity)
            ->andReturn($supplementedApplicationSteps);

        $applicationAnswersClearer = new ApplicationAnswersClearer(
            $supplementedApplicationStepsProvider,
            $qaContextFactory
        );

        $applicationAnswersClearer->clear($qaEntity);
    }
}
