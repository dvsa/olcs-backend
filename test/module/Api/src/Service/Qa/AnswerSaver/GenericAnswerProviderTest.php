<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\AnswerSaver;

use Dvsa\Olcs\Api\Domain\Repository\Answer as AnswerRepository;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerProvider;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * GenericAnswerProviderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class GenericAnswerProviderTest extends MockeryTestCase
{
    public function testGet()
    {
        $questionId = 43;
        $irhpApplicationId = 124;

        $question = m::mock(Question::class);
        $question->shouldReceive('getId')
            ->andReturn($questionId);
        $question->shouldReceive('isCustom')
            ->andReturn(false);

        $applicationStep = m::mock(ApplicationStep::class);
        $applicationStep->shouldReceive('getQuestion')
            ->andReturn($question);

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('getId')
            ->andReturn($irhpApplicationId);

        $answer = m::mock(Answer::class);

        $answerRepo = m::mock(AnswerRepository::class);
        $answerRepo->shouldReceive('fetchByQuestionIdAndIrhpApplicationId')
            ->with($questionId, $irhpApplicationId)
            ->andReturn($answer);

        $genericAnswerProvider = new GenericAnswerProvider($answerRepo);

        $this->assertSame(
            $answer,
            $genericAnswerProvider->get($applicationStep, $irhpApplication)
        );
    }
}
