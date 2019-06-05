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

/**
 * GenericAnswerWriterTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class GenericAnswerWriterTest extends MockeryTestCase
{
    private $questionId;

    private $questionType;

    private $irhpApplicationId;

    private $answerValue;

    private $question;

    private $applicationStep;

    private $irhpApplication;

    private $answer;

    private $answerRepo;

    private $answerFactory;

    private $genericAnswerWriter;

    public function setUp()
    {
        $this->questionId = 43;
        $this->questionType = Question::QUESTION_TYPE_STRING;
        $this->irhpApplicationId = 124;
        $this->answerValue = '866';

        $this->question = m::mock(Question::class);
        $this->question->shouldReceive('getId')
            ->andReturn($this->questionId);
        $this->question->shouldReceive('getQuestionType')
            ->andReturn($this->questionType);

        $this->applicationStep = m::mock(ApplicationStep::class);
        $this->applicationStep->shouldReceive('getQuestion')
            ->andReturn($this->question);

        $this->irhpApplication = m::mock(IrhpApplication::class);
        $this->irhpApplication->shouldReceive('getId')
            ->andReturn($this->irhpApplicationId);

        $this->answer = m::mock(Answer::class);
        $this->answer->shouldReceive('setValue')
            ->with($this->questionType, $this->answerValue)
            ->once()
            ->ordered()
            ->globally();

        $this->answerRepo = m::mock(AnswerRepository::class);

        $this->answerFactory = m::mock(AnswerFactory::class);

        $this->genericAnswerWriter = new GenericAnswerWriter($this->answerRepo, $this->answerFactory);
    }

    public function testSaveAnswerAlreadyExists()
    {
        $this->answerRepo->shouldReceive('fetchByQuestionIdAndIrhpApplicationId')
            ->with($this->questionId, $this->irhpApplicationId)
            ->andReturn($this->answer);
        $this->answerRepo->shouldReceive('save')
            ->with($this->answer)
            ->once()
            ->ordered()
            ->globally();

        $this->genericAnswerWriter->write($this->applicationStep, $this->irhpApplication, $this->answerValue);
    }

    public function testSaveAnswerRequiresCreation()
    {
        $questionText = m::mock(QuestionText::class);

        $this->question->shouldReceive('getActiveQuestionText')
            ->andReturn($questionText);

        $this->answerRepo->shouldReceive('fetchByQuestionIdAndIrhpApplicationId')
            ->andThrow(new NotFoundException());
        $this->answerRepo->shouldReceive('save')
            ->with($this->answer)
            ->once()
            ->ordered()
            ->globally();

        $this->answerFactory->shouldReceive('create')
            ->with($questionText, $this->irhpApplication)
            ->andReturn($this->answer);

        $this->genericAnswerWriter->write($this->applicationStep, $this->irhpApplication, $this->answerValue);
    }
}
