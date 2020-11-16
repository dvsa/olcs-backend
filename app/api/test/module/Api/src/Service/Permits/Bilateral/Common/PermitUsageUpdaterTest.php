<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Bilateral\Common;

use Dvsa\Olcs\Api\Service\Permits\Bilateral\Common\PermitUsageUpdater;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\ApplicationAnswersClearer;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * PermitUsageUpdaterTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class PermitUsageUpdaterTest extends MockeryTestCase
{
    private $qaContext;

    private $genericAnswerWriter;

    private $applicationAnswersClearer;

    private $permitUsageUpdater;

    public function setUp(): void
    {
        $this->qaContext = m::mock(QaContext::class);

        $this->genericAnswerWriter = m::mock(GenericAnswerWriter::class);

        $this->applicationAnswersClearer = m::mock(ApplicationAnswersClearer::class);

        $this->permitUsageUpdater = new PermitUsageUpdater(
            $this->genericAnswerWriter,
            $this->applicationAnswersClearer
        );
    }

    public function testUpdateAnswerChanged()
    {
        $newAnswer = 'new_answer';

        $this->qaContext->shouldReceive('getQaEntity->getBilateralPermitUsageSelection')
            ->withNoArgs()
            ->andReturn('old_answer');

        $this->applicationAnswersClearer->shouldReceive('clearAfterApplicationStep')
            ->with($this->qaContext)
            ->once();

        $this->genericAnswerWriter->shouldReceive('write')
            ->with($this->qaContext, $newAnswer)
            ->once();

        $this->permitUsageUpdater->update($this->qaContext, $newAnswer);
    }

    /**
     * @dataProvider dpUpdateOldAnswerNullOrAnswerNotChanged
     */
    public function testUpdateOldAnswerNullOrAnswerNotChanged($oldAnswer)
    {
        $newAnswer = 'answer';

        $this->qaContext->shouldReceive('getQaEntity->getBilateralPermitUsageSelection')
            ->withNoArgs()
            ->andReturn($oldAnswer);

        $this->genericAnswerWriter->shouldReceive('write')
            ->with($this->qaContext, $newAnswer)
            ->once();

        $this->permitUsageUpdater->update($this->qaContext, $newAnswer);
    }

    public function dpUpdateOldAnswerNullOrAnswerNotChanged()
    {
        return [
            ['answer'],
            [null],
        ];
    }
}
