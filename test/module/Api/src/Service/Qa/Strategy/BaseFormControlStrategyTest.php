<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Strategy;

use Dvsa\Olcs\Api\Entity\Generic\Answer as AnswerEntity;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Element\ElementGeneratorInterface;
use Dvsa\Olcs\Api\Service\Qa\Element\ElementInterface;
use Dvsa\Olcs\Api\Service\Qa\Element\SelfservePage;
use Dvsa\Olcs\Api\Service\Qa\PostProcessor\SelfservePagePostProcessorInterface;
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

    private $baseFormControlStrategy;

    public function setUp()
    {
        $this->frontendType = 'checkbox';

        $this->elementGenerator = m::mock(ElementGeneratorInterface::class);

        $this->answerSaver = m::mock(AnswerSaverInterface::class);

        $this->selfservePagePostProcessor = m::mock(SelfservePagePostProcessorInterface::class);

        $this->baseFormControlStrategy = new BaseFormControlStrategy(
            $this->frontendType,
            $this->elementGenerator,
            $this->answerSaver,
            $this->selfservePagePostProcessor
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
        $applicationStepEntity = m::mock(ApplicationStepEntity::class);

        $irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);

        $answerEntity = m::mock(AnswerEntity::class);

        $element = m::mock(ElementInterface::class);

        $this->elementGenerator->shouldReceive('generate')
            ->with($applicationStepEntity, $irhpApplicationEntity, $answerEntity)
            ->andReturn($element);

        $this->assertSame(
            $element,
            $this->baseFormControlStrategy->getElement($applicationStepEntity, $irhpApplicationEntity, $answerEntity)
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

    public function testPostProcessSelfservePage()
    {
        $selfservePage = m::mock(SelfservePage::class);

        $this->selfservePagePostProcessor->shouldReceive('process')
            ->with($selfservePage)
            ->once();

        $this->baseFormControlStrategy->postProcessSelfservePage($selfservePage);
    }
}
