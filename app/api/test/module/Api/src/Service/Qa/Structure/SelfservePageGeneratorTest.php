<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Generic\QuestionText as QuestionTextEntity;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\QaEntityInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\ApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\Structure\ApplicationStepGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\SelfservePage;
use Dvsa\Olcs\Api\Service\Qa\Structure\SelfservePageFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\SelfservePageGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionText;
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

        $questionKey = 'How will you use the permits';

        $additionalQaViewData = [
            'property1' => 'value1',
            'property2' => 'value2',
        ];

        $nextStepSlug = 'removals-cabotage';

        $applicationStep = m::mock(ApplicationStep::class);

        $questionText = m::mock(QuestionText::class);

        $questionTextEntity = m::mock(QuestionTextEntity::class);
        $questionTextEntity->shouldReceive('getTranslationKeyFromQuestionKey')
            ->withNoArgs()
            ->andReturn($questionKey);

        $applicationStepEntity = m::mock(ApplicationStepEntity::class);
        $applicationStepEntity->shouldReceive('getQuestion->getActiveQuestionText')
            ->withNoArgs()
            ->andReturn($questionTextEntity);
        $applicationStepEntity->shouldReceive('getNextStepSlug')
            ->withNoArgs()
            ->andReturn($nextStepSlug);

        $qaEntity = m::mock(QaEntityInterface::class);
        $qaEntity->shouldReceive('getAdditionalQaViewData')
            ->with($applicationStepEntity)
            ->andReturn($additionalQaViewData);

        $qaContext = m::mock(QaContext::class);
        $qaContext->shouldReceive('getApplicationStepEntity')
            ->withNoArgs()
            ->andReturn($applicationStepEntity);
        $qaContext->shouldReceive('getQaEntity')
            ->withNoArgs()
            ->andReturn($qaEntity);

        $selfservePageFactory = m::mock(SelfservePageFactory::class);
        $selfservePageFactory->shouldReceive('create')
            ->with($questionKey, $additionalQaViewData, $applicationStep, $questionText, $nextStepSlug)
            ->andReturn($selfservePage);
       
        $applicationStepGenerator = m::mock(ApplicationStepGenerator::class);
        $applicationStepGenerator->shouldReceive('generate')
            ->with($qaContext)
            ->andReturn($applicationStep);

        $formControlStrategy = m::mock(FormControlStrategyInterface::class);
        $formControlStrategy->shouldReceive('getQuestionText')
            ->with($qaContext)
            ->andReturn($questionText);

        $formControlStrategyProvider = m::mock(FormControlStrategyProvider::class);
        $formControlStrategyProvider->shouldReceive('get')
            ->with($applicationStepEntity)
            ->andReturn($formControlStrategy);

        $selfservePageGenerator = new SelfservePageGenerator(
            $selfservePageFactory,
            $applicationStepGenerator,
            $formControlStrategyProvider
        );

        $this->assertSame(
            $selfservePage,
            $selfservePageGenerator->generate($qaContext)
        );
    }
}
