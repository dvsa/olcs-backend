<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\QaEntityInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\ValidatorList;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * ElementGeneratorContextTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ElementGeneratorContextTest extends MockeryTestCase
{
    private $validatorList;

    private $applicationStepEntity;

    private $qaEntity;

    private $qaContext;

    private $elementGeneratorContext;

    public function setUp(): void
    {
        $this->validatorList = m::mock(ValidatorList::class);

        $this->applicationStepEntity = m::mock(ApplicationStepEntity::class);

        $this->qaEntity = m::mock(QaEntityInterface::class);

        $this->qaContext = m::mock(QaContext::class);
        $this->qaContext->shouldReceive('getApplicationStepEntity')
            ->withNoArgs()
            ->andReturn($this->applicationStepEntity);
        $this->qaContext->shouldReceive('getQaEntity')
            ->withNoArgs()
            ->andReturn($this->qaEntity);

        $this->elementGeneratorContext = new ElementGeneratorContext(
            $this->validatorList,
            $this->qaContext
        );
    }

    public function testGetValidatorList()
    {
        $this->assertSame(
            $this->validatorList,
            $this->elementGeneratorContext->getValidatorList()
        );
    }

    public function testGetApplicationStepEntity()
    {
        $this->assertSame(
            $this->applicationStepEntity,
            $this->elementGeneratorContext->getApplicationStepEntity()
        );
    }

    public function testGetQaEntity()
    {
        $this->assertSame(
            $this->qaEntity,
            $this->elementGeneratorContext->getQaEntity()
        );
    }

    public function testGetQaContext()
    {
        $this->assertSame(
            $this->qaContext,
            $this->elementGeneratorContext->getQaContext()
        );
    }

    public function testGetAnswerValue()
    {
        $answerValue = 'foo';

        $this->qaContext->shouldReceive('getAnswerValue')
            ->withNoArgs()
            ->andReturn($answerValue);

        $this->elementGeneratorContext->getAnswerValue();
    }
}
