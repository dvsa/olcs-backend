<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Bilateral\Common;

use Dvsa\Olcs\Api\Service\Permits\Bilateral\Common\ModifiedAnswerUpdater;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\ApplicationAnswersClearer;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * ModifiedAnswerUpdaterTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ModifiedAnswerUpdaterTest extends MockeryTestCase
{
    private $qaContext;

    private $genericAnswerWriter;

    private $applicationAnswersClearer;

    private $modifiedAnswerUpdater;

    public function setUp(): void
    {
        $this->qaContext = m::mock(QaContext::class);

        $this->genericAnswerWriter = m::mock(GenericAnswerWriter::class);

        $this->applicationAnswersClearer = m::mock(ApplicationAnswersClearer::class);

        $this->modifiedAnswerUpdater = new ModifiedAnswerUpdater(
            $this->genericAnswerWriter,
            $this->applicationAnswersClearer
        );
    }

    public function testUpdateAnswerChanged()
    {
        $newAnswer = 'new_answer';

        $this->applicationAnswersClearer->shouldReceive('clearAfterApplicationStep')
            ->with($this->qaContext)
            ->once();

        $this->genericAnswerWriter->shouldReceive('write')
            ->with($this->qaContext, $newAnswer)
            ->once();

        $this->modifiedAnswerUpdater->update($this->qaContext, 'old_answer', $newAnswer);
    }

    /**
     * @dataProvider dpUpdateOldAnswerNullOrAnswerNotChanged
     */
    public function testUpdateOldAnswerNullOrAnswerNotChanged($oldAnswer)
    {
        $newAnswer = 'answer';

        $this->genericAnswerWriter->shouldReceive('write')
            ->with($this->qaContext, $newAnswer)
            ->once();

        $this->modifiedAnswerUpdater->update($this->qaContext, $oldAnswer, $newAnswer);
    }

    public function dpUpdateOldAnswerNullOrAnswerNotChanged()
    {
        return [
            ['answer'],
            [null],
        ];
    }
}
