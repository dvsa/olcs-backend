<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\Answer as AnswerRepository;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Generic\QuestionText as QuestionTextEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\ApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\Structure\ApplicationStepGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\SelfservePage;
use Dvsa\Olcs\Api\Service\Qa\Structure\SelfservePageFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\SelfservePageGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionText;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorContextFactory;
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

        $questionTextGeneratorContext = m::mock(QuestionTextGeneratorContext::class);

        $questionTextGeneratorContextFactory = m::mock(QuestionTextGeneratorContextFactory::class);
        $questionTextGeneratorContextFactory->shouldReceive('create')
            ->with($applicationStepEntity, $irhpApplicationEntity)
            ->andReturn($questionTextGeneratorContext);

        $selfservePageFactory = m::mock(SelfservePageFactory::class);
        $selfservePageFactory->shouldReceive('create')
            ->with($applicationReference, $applicationStep, $questionText, $nextStepSlug)
            ->andReturn($selfservePage);
       
        $applicationStepGenerator = m::mock(ApplicationStepGenerator::class);
        $applicationStepGenerator->shouldReceive('generate')
            ->with($applicationStepEntity, $irhpApplicationEntity)
            ->andReturn($applicationStep);

        $formControlStrategy = m::mock(FormControlStrategyInterface::class);
        $formControlStrategy->shouldReceive('getQuestionText')
            ->with($questionTextGeneratorContext)
            ->andReturn($questionText);

        $formControlStrategyProvider = m::mock(FormControlStrategyProvider::class);
        $formControlStrategyProvider->shouldReceive('get')
            ->with($applicationStepEntity)
            ->andReturn($formControlStrategy);

        $selfservePageGenerator = new SelfservePageGenerator(
            $selfservePageFactory,
            $applicationStepGenerator,
            $formControlStrategyProvider,
            $questionTextGeneratorContextFactory
        );

        $this->assertSame(
            $selfservePage,
            $selfservePageGenerator->generate($applicationStepEntity, $irhpApplicationEntity)
        );
    }
}
