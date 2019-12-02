<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Date;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Date\Date;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Date\DateFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Date\DateGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * DateGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class DateGeneratorTest extends MockeryTestCase
{
    public function testGenerate()
    {
        $answerValue = '2020-04-28';

        $date = m::mock(Date::class);

        $dateFactory = m::mock(DateFactory::class);
        $dateFactory->shouldReceive('create')
            ->with($answerValue)
            ->andReturn($date);

        $applicationStepEntity = m::mock(ApplicationStepEntity::class);

        $irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);
        $irhpApplicationEntity->shouldReceive('getAnswer')
            ->with($applicationStepEntity)
            ->andReturn($answerValue);

        $elementGeneratorContext = m::mock(ElementGeneratorContext::class);
        $elementGeneratorContext->shouldReceive('getApplicationStepEntity')
            ->andReturn($applicationStepEntity);
        $elementGeneratorContext->shouldReceive('getIrhpApplicationEntity')
            ->andReturn($irhpApplicationEntity);

        $dateGenerator = new DateGenerator($dateFactory);

        $this->assertSame(
            $date,
            $dateGenerator->generate($elementGeneratorContext)
        );
    }
}
