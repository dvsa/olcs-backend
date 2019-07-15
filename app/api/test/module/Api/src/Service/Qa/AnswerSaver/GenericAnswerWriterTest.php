<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\AnswerSaver;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\Answer as AnswerRepository;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Generic\QuestionText;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerProvider;
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

    private $answer;

    private $answerRepo;

    private $answerFactory;

    private $question;

    private $applicationStep;

    private $irhpApplication;

    private $genericAnswerProvider;

    private $genericAnswerWriter;

    public function setUp()
    {
        $this->questionId = 43;

        $this->questionType = Question::QUESTION_TYPE_STRING;

        $this->irhpApplicationId = 47;

        $this->answerValue = 866;

        $this->answer = m::mock(Answer::class);

        $this->answerRepo = m::mock(AnswerRepository::class);

        $this->answerFactory = m::mock(AnswerFactory::class);

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

        $this->genericAnswerProvider = m::mock(GenericAnswerProvider::class);

        $this->genericAnswerWriter = new GenericAnswerWriter(
            $this->genericAnswerProvider,
            $this->answerFactory,
            $this->answerRepo
        );
    }

    public function testSaveAnswerAlreadyExists()
    {
        $this->answer->shouldReceive('setValue')
            ->with($this->questionType, $this->answerValue)
            ->once()
            ->ordered()
            ->globally();
        $this->answerRepo->shouldReceive('save')
            ->with($this->answer)
            ->once()
            ->ordered()
            ->globally();

        $this->genericAnswerProvider->shouldReceive('get')
            ->with($this->applicationStep, $this->irhpApplication)
            ->andReturn($this->answer);

        $this->genericAnswerWriter->write($this->applicationStep, $this->irhpApplication, $this->answerValue);
    }

    public function testSaveAnswerRequiresCreation()
    {
        $questionText = m::mock(QuestionText::class);

        $this->question->shouldReceive('getActiveQuestionText')
            ->andReturn($questionText);

        $this->genericAnswerProvider->shouldReceive('get')
            ->with($this->applicationStep, $this->irhpApplication)
            ->andThrow(new NotFoundException());

        $this->answer->shouldReceive('setValue')
            ->with($this->questionType, $this->answerValue)
            ->once()
            ->ordered()
            ->globally();

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
