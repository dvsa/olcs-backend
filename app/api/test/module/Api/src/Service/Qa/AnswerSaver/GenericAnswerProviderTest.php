<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\AnswerSaver;

use Dvsa\Olcs\Api\Domain\Repository\Answer as AnswerRepository;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerProvider;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use RuntimeException;

/**
 * GenericAnswerProviderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class GenericAnswerProviderTest extends MockeryTestCase
{
    private $answerRepo;

    private $question;

    private $applicationStep;

    private $irhpApplication;

    private $genericAnswerProvider;

    public function setUp()
    {
        $this->answerRepo = m::mock(AnswerRepository::class);

        $this->question = m::mock(Question::class);

        $this->applicationStep = m::mock(ApplicationStep::class);
        $this->applicationStep->shouldReceive('getQuestion')
            ->andReturn($this->question);

        $this->irhpApplication = m::mock(IrhpApplication::class);

        $this->genericAnswerProvider = new GenericAnswerProvider($this->answerRepo);
    }

    public function testGet()
    {
        $questionId = 43;
        $irhpApplicationId = 124;

        $this->question->shouldReceive('getId')
            ->andReturn($questionId);
        $this->question->shouldReceive('isCustom')
            ->andReturn(false);

        $this->applicationStep = m::mock(ApplicationStep::class);
        $this->applicationStep->shouldReceive('getQuestion')
            ->andReturn($this->question);

        $this->irhpApplication = m::mock(IrhpApplication::class);
        $this->irhpApplication->shouldReceive('getId')
            ->andReturn($irhpApplicationId);

        $answer = m::mock(Answer::class);

        $this->answerRepo->shouldReceive('fetchByQuestionIdAndIrhpApplicationId')
            ->with($questionId, $irhpApplicationId)
            ->andReturn($answer);

        $this->assertSame(
            $answer,
            $this->genericAnswerProvider->get($this->applicationStep, $this->irhpApplication)
        );
    }

    public function testExceptionOnCustomQuestion()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(GenericAnswerProvider::ERR_CUSTOM_UNSUPPORTED);

        $this->question->shouldReceive('isCustom')
            ->andReturn(true);

        $this->genericAnswerProvider->get($this->applicationStep, $this->irhpApplication);
    }
}
