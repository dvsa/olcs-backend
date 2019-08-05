<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\ApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\Structure\ApplicationStepGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\FormFragment;
use Dvsa\Olcs\Api\Service\Qa\Structure\FormFragmentFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\FormFragmentGenerator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * FormFragmentGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class FormFragmentGeneratorTest extends MockeryTestCase
{
    public function testGenerate()
    {
        $irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);

        $applicationStep1 = m::mock(ApplicationStep::class);
        $applicationStep2 = m::mock(ApplicationStep::class);

        $applicationStepEntity1 = m::mock(ApplicationStepEntity::class);
        $applicationStepEntity2 = m::mock(ApplicationStepEntity::class);
        $applicationStepEntities = [$applicationStepEntity1, $applicationStepEntity2];

        $applicationStepGenerator = m::mock(ApplicationStepGenerator::class);
        $applicationStepGenerator->shouldReceive('generate')
            ->with($applicationStepEntity1, $irhpApplicationEntity)
            ->andReturn($applicationStep1);
        $applicationStepGenerator->shouldReceive('generate')
            ->with($applicationStepEntity2, $irhpApplicationEntity)
            ->andReturn($applicationStep2);

        $formFragment = m::mock(FormFragment::class);
        $formFragment->shouldReceive('addApplicationStep')
            ->with($applicationStep1)
            ->once()
            ->ordered();
        $formFragment->shouldReceive('addApplicationStep')
            ->with($applicationStep2)
            ->once()
            ->ordered();

        $formFragmentFactory = m::mock(FormFragmentFactory::class);
        $formFragmentFactory->shouldReceive('create')
            ->withNoArgs()
            ->andReturn($formFragment);
       
        $formFragmentGenerator = new FormFragmentGenerator(
            $formFragmentFactory,
            $applicationStepGenerator
        );

        $this->assertSame(
            $formFragment,
            $formFragmentGenerator->generate($applicationStepEntities, $irhpApplicationEntity)
        );
    }
}
