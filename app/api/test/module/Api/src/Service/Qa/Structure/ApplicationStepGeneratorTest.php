<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\ApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\Structure\ApplicationStepFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\ApplicationStepGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContextFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\ValidatorList;
use Dvsa\Olcs\Api\Service\Qa\Structure\ValidatorListGenerator;
use Dvsa\Olcs\Api\Service\Qa\FormControlStrategyProvider;
use Dvsa\Olcs\Api\Service\Qa\Strategy\FormControlStrategyInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * ApplicationStepGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ApplicationStepGeneratorTest extends MockeryTestCase
{
    public function testGenerate()
    {
        $frontendType = 'checkbox';
        $fieldsetName = 'fieldset123';
        $questionShortKey = 'Cabotage';
        $questionId = 47;
        $irhpApplicationId = 82;

        $question = m::mock(Question::class);
        $question->shouldReceive('getId')
            ->andReturn($questionId);
        $question->shouldReceive('getActiveQuestionText->getQuestionShortKey')
            ->withNoArgs()
            ->andReturn($questionShortKey);


        $applicationStepEntity = m::mock(ApplicationStepEntity::class);
        $applicationStepEntity->shouldReceive('getFieldsetName')
            ->andReturn($fieldsetName);
        $applicationStepEntity->shouldReceive('getQuestion')
            ->andReturn($question);

        $irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);
        $irhpApplicationEntity->shouldReceive('getId')
            ->andReturn($irhpApplicationId);

        $element = m::mock(ElementInterface::class);

        $elementGeneratorContext = m::mock(ElementGeneratorContext::class);

        $formControlStrategy = m::mock(FormControlStrategyInterface::class);
        $formControlStrategy->shouldReceive('getFrontendType')
            ->andReturn($frontendType);
        $formControlStrategy->shouldReceive('getElement')
            ->with($elementGeneratorContext)
            ->andReturn($element);

        $formControlStrategyProvider = m::mock(FormControlStrategyProvider::class);
        $formControlStrategyProvider->shouldReceive('get')
            ->with($applicationStepEntity)
            ->andReturn($formControlStrategy);

        $applicationStep = m::mock(ApplicationStep::class);

        $validatorList = m::mock(ValidatorList::class);

        $applicationStepFactory = m::mock(ApplicationStepFactory::class);
        $applicationStepFactory->shouldReceive('create')
            ->with($frontendType, $fieldsetName, $questionShortKey, $element, $validatorList)
            ->andReturn($applicationStep);

        $validatorListGenerator = m::mock(ValidatorListGenerator::class);
        $validatorListGenerator->shouldReceive('generate')
            ->with($applicationStepEntity)
            ->andReturn($validatorList);

        $elementGeneratorContextFactory = m::mock(ElementGeneratorContextFactory::class);
        $elementGeneratorContextFactory->shouldReceive('create')
            ->with($validatorList, $applicationStepEntity, $irhpApplicationEntity)
            ->andReturn($elementGeneratorContext);

        $applicationStepGenerator = new ApplicationStepGenerator(
            $formControlStrategyProvider,
            $applicationStepFactory,
            $validatorListGenerator,
            $elementGeneratorContextFactory
        );

        $this->assertSame(
            $applicationStep,
            $applicationStepGenerator->generate($applicationStepEntity, $irhpApplicationEntity)
        );
    }
}
