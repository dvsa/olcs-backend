<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Strategy;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionText;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Strategy\BaseFormControlStrategy;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * BaseFormControlStrategyTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class BaseFormControlStrategyTest extends MockeryTestCase
{
    private $frontendType;

    private $elementGenerator;

    private $answerSaver;

    private $questionTextGenerator;

    private $baseFormControlStrategy;

    public function setUp()
    {
        $this->frontendType = 'checkbox';

        $this->elementGenerator = m::mock(ElementGeneratorInterface::class);

        $this->answerSaver = m::mock(AnswerSaverInterface::class);

        $this->questionTextGenerator = m::mock(QuestionTextGeneratorInterface::class);

        $this->baseFormControlStrategy = new BaseFormControlStrategy(
            $this->frontendType,
            $this->elementGenerator,
            $this->answerSaver,
            $this->questionTextGenerator
        );
    }

    public function testGetFrontendType()
    {
        $this->assertEquals(
            $this->frontendType,
            $this->baseFormControlStrategy->getFrontendType()
        );
    }

    public function testGetElement()
    {
        $elementGeneratorContext = m::mock(ElementGeneratorContext::class);

        $element = m::mock(ElementInterface::class);

        $this->elementGenerator->shouldReceive('generate')
            ->with($elementGeneratorContext)
            ->andReturn($element);

        $this->assertSame(
            $element,
            $this->baseFormControlStrategy->getElement($elementGeneratorContext)
        );
    }

    public function testSaveFormData()
    {
        $applicationStepEntity = m::mock(ApplicationStepEntity::class);

        $irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);

        $postData = [
            'fields123' => [
                'cabotage' => '1'
            ]
        ];

        $this->answerSaver->shouldReceive('save')
            ->with($applicationStepEntity, $irhpApplicationEntity, $postData)
            ->once();

        $this->baseFormControlStrategy->saveFormData($applicationStepEntity, $irhpApplicationEntity, $postData);
    }

    public function testGetQuestionText()
    {
        $questionText = m::mock(QuestionText::class);

        $questionTextGeneratorContext = m::mock(QuestionTextGeneratorContext::class);

        $this->questionTextGenerator->shouldReceive('generate')
            ->with($questionTextGeneratorContext)
            ->andReturn($questionText);

        $this->assertSame(
            $questionText,
            $this->baseFormControlStrategy->getQuestionText($questionTextGeneratorContext)
        );
    }
}
