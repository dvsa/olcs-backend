<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\AnswersSummary;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Generic\Question as QuestionEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Permits\AnswersSummary\AnswersSummaryRow;
use Dvsa\Olcs\Api\Service\Permits\AnswersSummary\AnswersSummaryRowFactory;
use Dvsa\Olcs\Api\Service\Qa\AnswersSummary\AnswersSummaryRowGenerator;
use Dvsa\Olcs\Api\Service\Qa\AnswersSummary\AnswerSummaryProviderInterface;
use Dvsa\Olcs\Api\Service\Qa\Facade\SupplementedApplicationSteps\SupplementedApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\Strategy\FormControlStrategyInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionText;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorContextFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\View\Renderer\RendererInterface;

/**
 * AnswersSummaryRowGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class AnswersSummaryRowGeneratorTest extends MockeryTestCase
{
    /**
     * @dataProvider dpSnapshot
     */
    public function testGenerate($isSnapshot)
    {
        $questionTranslationKey = 'question.translation.key';
        $formattedAnswer = 'line 1<br>line 2';
        $questionSlug = 'no-of-permits';

        $templateName = 'irhp-no-of-permits';
        $expectedTemplatePath = 'answers-summary/irhp-no-of-permits';

        $templateVariables = [
            'key1' => 'value1',
            'key2' => 'value2'
        ];

        $questionEntity = m::mock(QuestionEntity::class);
        $questionEntity->shouldReceive('getSlug')
            ->withNoArgs()
            ->andReturn($questionSlug);

        $irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);

        $applicationStepEntity = m::mock(ApplicationStepEntity::class);
        $applicationStepEntity->shouldReceive('getQuestion')
            ->withNoArgs()
            ->andReturn($questionEntity);

        $questionTextGeneratorContext = m::mock(QuestionTextGeneratorContext::class);

        $questionTextGeneratorContextFactory = m::mock(QuestionTextGeneratorContextFactory::class);
        $questionTextGeneratorContextFactory->shouldReceive('create')
            ->with($applicationStepEntity, $irhpApplicationEntity)
            ->once()
            ->andReturn($questionTextGeneratorContext);

        $answerSummaryProvider = m::mock(AnswerSummaryProviderInterface::class);
        $answerSummaryProvider->shouldReceive('getTemplateName')
            ->withNoArgs()
            ->andReturn($templateName);
        $answerSummaryProvider->shouldReceive('getTemplateVariables')
            ->with($applicationStepEntity, $irhpApplicationEntity, $isSnapshot)
            ->andReturn($templateVariables);

        $questionText = m::mock(QuestionText::class);
        $questionText->shouldReceive('getQuestion->getTranslateableText->getKey')
            ->withNoArgs()
            ->andReturn($questionTranslationKey);

        $formControlStrategy = m::mock(FormControlStrategyInterface::class);
        $formControlStrategy->shouldReceive('getAnswerSummaryProvider')
            ->withNoArgs()
            ->once()
            ->andReturn($answerSummaryProvider);
        $formControlStrategy->shouldReceive('getQuestionText')
            ->with($questionTextGeneratorContext)
            ->andReturn($questionText);

        $supplementedApplicationStep = m::mock(SupplementedApplicationStep::class);
        $supplementedApplicationStep->shouldReceive('getFormControlStrategy')
            ->withNoArgs()
            ->andReturn($formControlStrategy);
        $supplementedApplicationStep->shouldReceive('getApplicationStep')
            ->withNoArgs()
            ->andReturn($applicationStepEntity);

        $viewRenderer = m::mock(RendererInterface::class);
        $viewRenderer->shouldReceive('render')
            ->with($expectedTemplatePath, $templateVariables)
            ->once()
            ->andReturn($formattedAnswer);

        $answersSummaryRow = m::mock(AnswersSummaryRow::class);

        $answersSummaryRowFactory = m::mock(AnswersSummaryRowFactory::class);
        $answersSummaryRowFactory->shouldReceive('create')
            ->with($questionTranslationKey, $formattedAnswer, $questionSlug)
            ->once()
            ->andReturn($answersSummaryRow);

        $answersSummaryRowGenerator = new AnswersSummaryRowGenerator(
            $answersSummaryRowFactory,
            $viewRenderer,
            $questionTextGeneratorContextFactory
        );

        $this->assertSame(
            $answersSummaryRow,
            $answersSummaryRowGenerator->generate(
                $supplementedApplicationStep,
                $irhpApplicationEntity,
                $isSnapshot
            )
        );
    }

    public function dpSnapshot()
    {
        return [
            [true],
            [false]
        ];
    }
}
