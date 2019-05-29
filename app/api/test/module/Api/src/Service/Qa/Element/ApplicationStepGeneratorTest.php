<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Element;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\Answer as AnswerRepository;
use Dvsa\Olcs\Api\Entity\Generic\Answer as AnswerEntity;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Element\ApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\Element\ApplicationStepFactory;
use Dvsa\Olcs\Api\Service\Qa\Element\ApplicationStepGenerator;
use Dvsa\Olcs\Api\Service\Qa\Element\ElementInterface;
use Dvsa\Olcs\Api\Service\Qa\Element\ValidatorList;
use Dvsa\Olcs\Api\Service\Qa\Element\ValidatorListGenerator;
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
    private $frontendType;

    private $fieldsetName;

    private $questionId;

    private $irhpApplicationId;

    private $applicationStepEntity;

    private $irhpApplicationEntity;

    private $element;

    private $formControlStrategy;

    private $formControlStrategyProvider;

    private $applicationStep;

    private $validatorList;

    private $applicationStepFactory;

    private $answerRepo;

    private $validatorListGenerator;

    private $applicationStepGenerator;

    public function setUp()
    {
        $this->frontendType = 'checkbox';
        $this->fieldsetName = 'fieldset123';
        $this->questionId = 47;
        $this->irhpApplicationId = 82;

        $this->applicationStepEntity = m::mock(ApplicationStepEntity::class);
        $this->applicationStepEntity->shouldReceive('getFieldsetName')
            ->andReturn($this->fieldsetName);
        $this->applicationStepEntity->shouldReceive('getQuestion->getId')
            ->andReturn($this->questionId);

        $this->irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);
        $this->irhpApplicationEntity->shouldReceive('getId')
            ->andReturn($this->irhpApplicationId);

        $this->element = m::mock(ElementInterface::class);

        $this->formControlStrategy = m::mock(FormControlStrategyInterface::class);
        $this->formControlStrategy->shouldReceive('getFrontendType')
            ->andReturn($this->frontendType);

        $this->formControlStrategyProvider = m::mock(FormControlStrategyProvider::class);
        $this->formControlStrategyProvider->shouldReceive('get')
            ->with($this->applicationStepEntity)
            ->andReturn($this->formControlStrategy);

        $this->applicationStep = m::mock(ApplicationStep::class);

        $this->validatorList = m::mock(ValidatorList::class);

        $this->applicationStepFactory = m::mock(ApplicationStepFactory::class);
        $this->applicationStepFactory->shouldReceive('create')
            ->with($this->frontendType, $this->fieldsetName, $this->element, $this->validatorList)
            ->andReturn($this->applicationStep);

        $this->answerRepo = m::mock(AnswerRepository::class);

        $this->validatorListGenerator = m::mock(ValidatorListGenerator::class);
        $this->validatorListGenerator->shouldReceive('generate')
            ->with($this->applicationStepEntity)
            ->andReturn($this->validatorList);

        $this->applicationStepGenerator = new ApplicationStepGenerator(
            $this->formControlStrategyProvider,
            $this->applicationStepFactory,
            $this->answerRepo,
            $this->validatorListGenerator
        );
    }

    public function testGenerateAnswerEntityExists()
    {
        $answerEntity = m::mock(AnswerEntity::class);

        $this->formControlStrategy->shouldReceive('getElement')
            ->with($this->applicationStepEntity, $this->irhpApplicationEntity, $answerEntity)
            ->andReturn($this->element);

        $this->answerRepo->shouldReceive('fetchByQuestionIdAndIrhpApplicationId')
            ->with($this->questionId, $this->irhpApplicationId)
            ->andReturn($answerEntity);

        $this->assertSame(
            $this->applicationStep,
            $this->applicationStepGenerator->generate($this->applicationStepEntity, $this->irhpApplicationEntity)
        );
    }

    public function testGenerateAnswerEntityNotFound()
    {
        $this->formControlStrategy->shouldReceive('getElement')
            ->with($this->applicationStepEntity, $this->irhpApplicationEntity, null)
            ->andReturn($this->element);

        $this->answerRepo->shouldReceive('fetchByQuestionIdAndIrhpApplicationId')
            ->with($this->questionId, $this->irhpApplicationId)
            ->andThrow(new NotFoundException());

        $this->assertSame(
            $this->applicationStep,
            $this->applicationStepGenerator->generate($this->applicationStepEntity, $this->irhpApplicationEntity)
        );
    }
}
