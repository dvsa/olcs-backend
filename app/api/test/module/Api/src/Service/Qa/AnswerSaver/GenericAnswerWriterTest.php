<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\AnswerSaver;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\Answer as AnswerRepository;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Generic\QuestionText;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\AnswerFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use RuntimeException;

/**
 * GenericAnswerWriterTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class GenericAnswerWriterTest extends MockeryTestCase
{
    private $answerRepo;

    private $answerFactory;

    private $question;

    private $applicationStep;

    public function setUp()
    {
        $this->answerRepo = m::mock(AnswerRepository::class);

        $this->answerFactory = m::mock(AnswerFactory::class);

        $this->question = m::mock(Question::class);

        $this->applicationStep = m::mock(ApplicationStep::class);
        $this->applicationStep->shouldReceive('getQuestion')
            ->andReturn($this->question);

        $this->irhpApplication = m::mock(IrhpApplication::class);
    }

    public function testSaveAnswerAlreadyExists()
    {
        $questionId = 43;
        $questionType = Question::QUESTION_TYPE_STRING;
        $this->irhpApplicationId = 124;
        $answerValue = '866';

        $this->question = m::mock(Question::class);
        $this->question->shouldReceive('getId')
            ->andReturn($questionId);
        $this->question->shouldReceive('getQuestionType')
            ->andReturn($questionType);
        $this->question->shouldReceive('isCustom')
            ->andReturn(false);

        $this->applicationStep = m::mock(ApplicationStep::class);
        $this->applicationStep->shouldReceive('getQuestion')
            ->andReturn($this->question);

        $this->irhpApplication = m::mock(IrhpApplication::class);
        $this->irhpApplication->shouldReceive('getId')
            ->andReturn($this->irhpApplicationId);

        $answer = m::mock(Answer::class);
        $answer->shouldReceive('setValue')
            ->with($questionType, $answerValue)
            ->once()
            ->ordered()
            ->globally();

        $this->answerRepo->shouldReceive('save')
            ->with($answer)
            ->once()
            ->ordered()
            ->globally();
        $this->answerRepo->shouldReceive('fetchByQuestionIdAndIrhpApplicationId')
            ->with($questionId, $this->irhpApplicationId)
            ->andReturn($answer);

        $genericAnswerWriter = new GenericAnswerWriter($this->answerRepo, $this->answerFactory);
        $genericAnswerWriter->write($this->applicationStep, $this->irhpApplication, $answerValue);
    }

    public function testSaveAnswerRequiresCreation()
    {
        $questionId = 43;
        $questionType = Question::QUESTION_TYPE_STRING;
        $this->irhpApplicationId = 124;
        $answerValue = '866';

        $questionText = m::mock(QuestionText::class);

        $this->question->shouldReceive('getId')
            ->andReturn($questionId);
        $this->question->shouldReceive('getQuestionType')
            ->andReturn($questionType);
        $this->question->shouldReceive('isCustom')
            ->andReturn(false);
        $this->question->shouldReceive('getActiveQuestionText')
            ->andReturn($questionText);

        $this->irhpApplication->shouldReceive('getId')
            ->andReturn($this->irhpApplicationId);

        $answer = m::mock(Answer::class);
        $answer->shouldReceive('setValue')
            ->with($questionType, $answerValue)
            ->once()
            ->ordered()
            ->globally();

        $this->answerRepo->shouldReceive('fetchByQuestionIdAndIrhpApplicationId')
            ->andThrow(new NotFoundException());
        $this->answerRepo->shouldReceive('save')
            ->with($answer)
            ->once()
            ->ordered()
            ->globally();

        $this->answerFactory->shouldReceive('create')
            ->with($questionText, $this->irhpApplication)
            ->andReturn($answer);

        $genericAnswerWriter = new GenericAnswerWriter($this->answerRepo, $this->answerFactory);
        $genericAnswerWriter->write($this->applicationStep, $this->irhpApplication, $answerValue);
    }

    public function testExceptionOnCustomQuestion()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(GenericAnswerWriter::ERR_CUSTOM_UNSUPPORTED);

        $this->question->shouldReceive('isCustom')
            ->andReturn(true);

        $genericAnswerWriter = new GenericAnswerWriter($this->answerRepo, $this->answerFactory);
        $genericAnswerWriter->write($this->applicationStep, $this->irhpApplication, 47);
    }
}
