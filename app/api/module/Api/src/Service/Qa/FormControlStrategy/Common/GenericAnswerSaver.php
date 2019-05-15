<?php

namespace Dvsa\Olcs\Api\Service\Qa\FormControlStrategy\Common;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\Answer as AnswerRepository;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;

class GenericAnswerSaver
{
    /** @var AnswerRepository */
    private $answerRepo;

    /**
     * Create service instance
     *
     * @param AnswerRepository $answerRepo
     *
     * @return GenericAnswerSaver
     */
    public function __construct(AnswerRepository $answerRepo)
    {
        $this->answerRepo = $answerRepo;
    }

    /**
     * Save an answer to persistent storage using a generic strategy suitable for non-bespoke scenarios
     *
     * @param ApplicationStep $applicationStep
     * @param IrhpApplication $irhpApplication
     * @param mixed $answerValue
     */
    public function save(ApplicationStep $applicationStep, IrhpApplication $irhpApplication, $answerValue)
    {
        $question = $applicationStep->getQuestion();

        try {
            $answer = $this->answerRepo->fetchByQuestionIdAndIrhpApplicationId(
                $question->getId(),
                $irhpApplication->getId()
            );
        } catch (NotFoundException $e) {
            $answer = Answer::createNewForIrhpApplication(
                $question->getActiveQuestionText(),
                $irhpApplication
            );
        }

        $answer->setValue($question->getQuestionType(), $answerValue);
        $this->answerRepo->save($answer);
    }
}
