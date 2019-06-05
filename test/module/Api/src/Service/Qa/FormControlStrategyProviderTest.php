<?php

namespace Dvsa\OlcsTest\Api\Service\Qa;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Service\Qa\FormControlStrategyProvider;
use Dvsa\Olcs\Api\Service\Qa\Strategy\FormControlStrategyInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use RuntimeException;

/**
 * FormControlStrategyProviderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class FormControlStrategyProviderTest extends MockeryTestCase
{
    private $checkboxFormControlStrategy;

    private $formControlStrategyProvider;

    private $applicationStep;

    public function setUp()
    {
        $this->checkboxFormControlStrategy = m::mock(FormControlStrategyInterface::class);
        
        $this->formControlStrategyProvider = new FormControlStrategyProvider();
        $this->formControlStrategyProvider->registerStrategy(
            Question::FORM_CONTROL_TYPE_RADIO,
            m::mock(FormControlStrategyInterface::class)
        );
        $this->formControlStrategyProvider->registerStrategy(
            Question::FORM_CONTROL_TYPE_CHECKBOX,
            $this->checkboxFormControlStrategy
        );
        $this->formControlStrategyProvider->registerStrategy(
            Question::FORM_CONTROL_TYPE_TEXT,
            m::mock(FormControlStrategyInterface::class)
        );

        $this->applicationStep = m::mock(ApplicationStep::class);
    }

    public function testGet()
    {
        $this->applicationStep->shouldReceive('getQuestion->getFormControlType->getId')
            ->andReturn(Question::FORM_CONTROL_TYPE_CHECKBOX);

        $this->assertSame(
            $this->checkboxFormControlStrategy,
            $this->formControlStrategyProvider->get($this->applicationStep)
        );
    }

    public function testExceptionOnUnknownType()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No FormControlStrategy found for form control type unknownType');

        $this->applicationStep->shouldReceive('getQuestion->getFormControlType->getId')
            ->andReturn('unknownType');

        $this->formControlStrategyProvider->get($this->applicationStep);
    }
}
