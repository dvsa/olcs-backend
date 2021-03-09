<?php

namespace Dvsa\OlcsTest\Api\Service\Qa;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\QaEntityInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * QaContextTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class QaContextTest extends MockeryTestCase
{
    private $applicationStep;

    private $qaEntity;

    private $qaContext;

    public function setUp(): void
    {
        $this->applicationStep = m::mock(ApplicationStep::class);

        $this->qaEntity = m::mock(QaEntityInterface::class);

        $this->qaContext = new QaContext($this->applicationStep, $this->qaEntity);
    }

    public function testGetApplicationStepEntity()
    {
        $this->assertSame(
            $this->applicationStep,
            $this->qaContext->getApplicationStepEntity()
        );
    }

    public function testGetQaEntity()
    {
        $this->assertSame(
            $this->qaEntity,
            $this->qaContext->getQaEntity()
        );
    }

    public function testGetAnswerValue()
    {
        $answerValue = 'foo';

        $this->qaEntity->shouldReceive('getAnswer')
            ->with($this->applicationStep)
            ->andReturn($answerValue);

        $this->assertEquals(
            $answerValue,
            $this->qaContext->getAnswerValue()
        );
    }

    /**
     * @dataProvider dpIsApplicationStepEnabled
     */
    public function testIsApplicationStepEnabled($isNotYetSubmitted, $enabledAfterSubmission, $expected)
    {
        $this->qaEntity->shouldReceive('isNotYetSubmitted')
            ->withNoArgs()
            ->andReturn($isNotYetSubmitted);

        $this->applicationStep->shouldReceive('getEnabledAfterSubmission')
            ->withNoArgs()
            ->andReturn($enabledAfterSubmission);

        $this->assertEquals(
            $expected,
            $this->qaContext->isApplicationStepEnabled()
        );
    }

    public function dpIsApplicationStepEnabled()
    {
        return [
            [true, null, true],
            [false, true, true],
            [false, false, false],
        ];
    }
}
