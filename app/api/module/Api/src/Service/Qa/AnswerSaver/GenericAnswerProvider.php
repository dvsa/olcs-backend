<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswerSaver;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Domain\Repository\Answer as AnswerRepository;
use RuntimeException;

class GenericAnswerProvider
{
    const ERR_CUSTOM_UNSUPPORTED = 'GenericAnswerProvider should not be used by custom questions';

    /** @var AnswerRepository */
    private $answerRepo;

    /**
     * Create service instance
     *
     * @param AnswerRepository $answerRepo
     *
     * @return GenericAnswerProvider
     */
    public function __construct(AnswerRepository $answerRepo)
    {
        $this->answerRepo = $answerRepo;
    }

    /**
     * Get the answer entity corresponding to the specified application step and irhp application
     *
     * @param ApplicationStep $applicationStep
     * @param IrhpApplication $irhpApplication
     */
    public function get(ApplicationStep $applicationStep, IrhpApplication $irhpApplication)
    {
        $question = $applicationStep->getQuestion();
        if ($question->isCustom()) {
            throw new RuntimeException(self::ERR_CUSTOM_UNSUPPORTED);
        }

        return $this->answerRepo->fetchByQuestionIdAndIrhpApplicationId(
            $question->getId(),
            $irhpApplication->getId()
        );
    }
}
