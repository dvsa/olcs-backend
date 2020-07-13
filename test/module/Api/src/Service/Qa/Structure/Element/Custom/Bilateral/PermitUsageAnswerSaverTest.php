<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\ApplicationAnswersClearer;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\PermitUsageAnswerSaver;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerSaver;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * PermitUsageAnswerSaverTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class PermitUsageAnswerSaverTest extends MockeryTestCase
{
    const POST_DATA = [
        'key1' => 'value1',
        'key2' => 'value2',
    ];

    private $qaContext;

    private $genericAnswerFetcher;

    private $applicationAnswersClearer;

    private $genericAnswerSaver;

    private $permitUsageAnswerSaver;

    public function setUp(): void
    {
        $this->qaContext = m::mock(QaContext::class);

        $this->genericAnswerFetcher = m::mock(GenericAnswerFetcher::class);

        $this->applicationAnswersClearer = m::mock(ApplicationAnswersClearer::class);

        $this->genericAnswerSaver = m::mock(GenericAnswerSaver::class);
        $this->genericAnswerSaver->shouldReceive('save')
            ->with($this->qaContext, self::POST_DATA)
            ->once();

        $this->permitUsageAnswerSaver = new PermitUsageAnswerSaver(
            $this->genericAnswerFetcher,
            $this->applicationAnswersClearer,
            $this->genericAnswerSaver
        );
    }

    public function testSaveExistingAnswerNull()
    {
        $this->qaContext->shouldReceive('getQaEntity->getBilateralPermitUsageSelection')
            ->withNoArgs()
            ->andReturnNull();

        $this->permitUsageAnswerSaver->save($this->qaContext, self::POST_DATA);
    }

    public function testSaveExistingAnswerMatchesNewAnswer()
    {
        $previousAndNewAnswer = 'journey_single';

        $applicationStep = m::mock(ApplicationStep::class);

        $this->qaContext->shouldReceive('getQaEntity->getBilateralPermitUsageSelection')
            ->withNoArgs()
            ->andReturn($previousAndNewAnswer);
        $this->qaContext->shouldReceive('getApplicationStepEntity')
            ->withNoArgs()
            ->andReturn($applicationStep);

        $this->genericAnswerFetcher->shouldReceive('fetch')
            ->with($applicationStep, self::POST_DATA)
            ->andReturn($previousAndNewAnswer);

        $this->permitUsageAnswerSaver->save($this->qaContext, self::POST_DATA);
    }

    public function testSaveExistingAnswerDiffersFromNewAnswer()
    {
        $applicationStep = m::mock(ApplicationStep::class);

        $this->qaContext->shouldReceive('getQaEntity->getBilateralPermitUsageSelection')
            ->withNoArgs()
            ->andReturn('journey_single');
        $this->qaContext->shouldReceive('getApplicationStepEntity')
            ->withNoArgs()
            ->andReturn($applicationStep);

        $this->genericAnswerFetcher->shouldReceive('fetch')
            ->with($applicationStep, self::POST_DATA)
            ->andReturn('journey_multiple');

        $this->applicationAnswersClearer->shouldReceive('clearAfterApplicationStep')
            ->with($this->qaContext)
            ->once();

        $this->permitUsageAnswerSaver->save($this->qaContext, self::POST_DATA);
    }
}
