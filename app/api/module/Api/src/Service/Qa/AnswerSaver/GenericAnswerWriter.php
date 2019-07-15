<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswerSaver;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\Answer as AnswerRepository;
use RuntimeException;

class GenericAnswerWriter
{
    const ERR_CUSTOM_UNSUPPORTED = 'GenericAnswerWriter should not be used by custom questions';

    /** @var AnswerRepository */
    private $answerRepo;

    /** @var AnswerFactory */
    private $answerFactory;

    /**
     * Create service instance
     *
     * @param AnswerRepository $answerRepo
     * @param AnswerFactory $answerFactory
     *
     * @return GenericAnswerWriter
     */
    public function __construct(
        AnswerRepository $answerRepo,
        AnswerFactory $answerFactory
    ) {
        $this->answerRepo = $answerRepo;
        $this->answerFactory = $answerFactory;
    }

    /**
     * Save an answer corresponding to the supplied application step and application to persistent storage using
     * the supplied answer value
     *
     * @param ApplicationStep $applicationStep
     * @param IrhpApplication $irhpApplication
     * @param mixed $answerValue
     */
    public function write(ApplicationStep $applicationStep, IrhpApplication $irhpApplication, $answerValue)
    {
        $question = $applicationStep->getQuestion();
        if ($question->isCustom()) {
            throw new RuntimeException(self::ERR_CUSTOM_UNSUPPORTED);
        }

        try {
            $answer = $this->answerRepo->fetchByQuestionIdAndIrhpApplicationId(
                $question->getId(),
                $irhpApplication->getId()
            );
        } catch (NotFoundException $e) {
            $answer = $this->answerFactory->create(
                $question->getActiveQuestionText(),
                $irhpApplication
            );
        }

        $answer->setValue($question->getQuestionType(), $answerValue);
        $this->answerRepo->save($answer);
    }
}
