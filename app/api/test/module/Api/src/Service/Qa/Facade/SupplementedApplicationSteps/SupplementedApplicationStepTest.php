<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Facade\SubmittedApplicationSteps;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\Strategy\FormControlStrategyInterface;
use Dvsa\Olcs\Api\Service\Qa\Facade\SupplementedApplicationSteps\SupplementedApplicationStep;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * SupplementedApplicationStepTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class SupplementedApplicationStepTest extends MockeryTestCase
{
    private $applicationStep;

    private $formControlStrategy;

    private $supplementedApplicationStep;

    public function setUp(): void
    {
        $this->applicationStep = m::mock(ApplicationStep::class);

        $this->formControlStrategy = m::mock(FormControlStrategyInterface::class);

        $this->supplementedApplicationStep = new SupplementedApplicationStep(
            $this->applicationStep,
            $this->formControlStrategy
        );
    }

    public function testGetApplicationStep()
    {
        $this->assertSame(
            $this->applicationStep,
            $this->supplementedApplicationStep->getApplicationStep()
        );
    }

    public function testGetFormControlStrategy()
    {
        $this->assertSame(
            $this->formControlStrategy,
            $this->supplementedApplicationStep->getFormControlStrategy()
        );
    }
}
