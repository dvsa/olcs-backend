<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswerSaver;

use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\Answer as AnswerRepository;

class GenericAnswerWriter
{
    /** @var GenericAnswerProvider */
    private $genericAnswerProvider;

    /** @var AnswerFactory */
    private $answerFactory;

    /** @var AnswerRepository */
    private $answerRepo;

    /**
     * Create service instance
     *
     * @param GenericAnswerProvider $genericAnswerProvider
     * @param AnswerFactory $answerFactory
     * @param AnswerRepository $answerRepo
     *
     * @return GenericAnswerWriter
     */
    public function __construct(
        GenericAnswerProvider $genericAnswerProvider,
        AnswerFactory $answerFactory,
        AnswerRepository $answerRepo
    ) {
        $this->genericAnswerProvider = $genericAnswerProvider;
        $this->answerFactory = $answerFactory;
        $this->answerRepo = $answerRepo;
    }

    /**
     * Save an answer corresponding to the supplied application step and application to persistent storage using
     * the supplied answer value
     *
     * @param QaContext $qaContext
     * @param mixed $answerValue
     * @param string|null $forceQuestionType
     */
    public function write(
        QaContext $qaContext,
        $answerValue,
        $forceQuestionType = null
    ) {
        $applicationStep = $qaContext->getApplicationStepEntity();
        $qaEntity = $qaContext->getQaEntity();

        $question = $applicationStep->getQuestion();

        try {
            $answer = $this->genericAnswerProvider->get($qaContext);
        } catch (NotFoundException $e) {
            $answer = $this->answerFactory->create(
                $question->getActiveQuestionText(),
                $qaEntity
            );
        }

        $questionType = $question->getQuestionType();
        if (!is_null($forceQuestionType)) {
            $questionType = $forceQuestionType;
        }

        $answer->setValue($questionType, $answerValue);
        $qaEntity->addAnswers($answer);

        $this->answerRepo->save($answer);
    }
}
