<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Element;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\Answer as AnswerRepository;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Generic\QuestionText as QuestionTextEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Element\ApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\Element\ApplicationStepGenerator;
use Dvsa\Olcs\Api\Service\Qa\Element\SelfservePage;
use Dvsa\Olcs\Api\Service\Qa\Element\SelfservePageFactory;
use Dvsa\Olcs\Api\Service\Qa\Element\SelfservePageGenerator;
use Dvsa\Olcs\Api\Service\Qa\Element\QuestionText;
use Dvsa\Olcs\Api\Service\Qa\Element\QuestionTextGenerator;
use Dvsa\Olcs\Api\Service\Qa\FormControlStrategyProvider;
use Dvsa\Olcs\Api\Service\Qa\Strategy\FormControlStrategyInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * SelfservePageGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class SelfservePageGeneratorTest extends MockeryTestCase
{
    public function testGenerate()
    {
        $selfservePage = m::mock(SelfservePage::class);

        $applicationReference = 'OB1234567 / 12390';

        $nextStepSlug = 'removals-cabotage';

        $applicationStep = m::mock(ApplicationStep::class);

        $questionText = m::mock(QuestionText::class);

        $questionTextEntity = m::mock(QuestionTextEntity::class);

        $applicationStepEntity = m::mock(ApplicationStepEntity::class);
        $applicationStepEntity->shouldReceive('getQuestion->getActiveQuestionText')
            ->andReturn($questionTextEntity);
        $applicationStepEntity->shouldReceive('getNextStepSlug')
            ->andReturn($nextStepSlug);

        $irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);
        $irhpApplicationEntity->shouldReceive('getApplicationRef')
            ->andReturn($applicationReference);

        $selfservePageFactory = m::mock(SelfservePageFactory::class);
        $selfservePageFactory->shouldReceive('create')
            ->with($applicationReference, $applicationStep, $questionText, $nextStepSlug)
            ->andReturn($selfservePage);

        $questionTextGenerator = m::mock(QuestionTextGenerator::class);
        $questionTextGenerator->shouldReceive('generate')
            ->with($questionTextEntity)
            ->andReturn($questionText);
        
        $applicationStepGenerator = m::mock(ApplicationStepGenerator::class);
        $applicationStepGenerator->shouldReceive('generate')
            ->with($applicationStepEntity, $irhpApplicationEntity)
            ->andReturn($applicationStep);

        $formControlStrategy = m::mock(FormControlStrategyInterface::class);
        $formControlStrategy->shouldReceive('postProcessSelfservePage')
            ->with($selfservePage)
            ->once();

        $formControlStrategyProvider = m::mock(FormControlStrategyProvider::class);
        $formControlStrategyProvider->shouldReceive('get')
            ->with($applicationStepEntity)
            ->andReturn($formControlStrategy);

        $selfservePageGenerator = new SelfservePageGenerator(
            $selfservePageFactory,
            $questionTextGenerator,
            $applicationStepGenerator,
            $formControlStrategyProvider
        );

        $this->assertSame(
            $selfservePage,
            $selfservePageGenerator->generate($applicationStepEntity, $irhpApplicationEntity)
        );
    }
}
