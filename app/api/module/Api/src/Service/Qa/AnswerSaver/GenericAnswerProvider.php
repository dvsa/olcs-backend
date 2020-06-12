<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswerSaver;

use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Domain\Repository\Answer as AnswerRepository;

class GenericAnswerProvider
{
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
     * Get the answer entity corresponding to the specified application step and entity
     *
     * @param QaContext $qaContext
     */
    public function get(QaContext $qaContext)
    {
        $qaEntity = $qaContext->getQaEntity();

        return $this->answerRepo->fetchByQuestionIdAndEntityTypeAndId(
            $qaContext->getApplicationStepEntity()->getQuestion()->getId(),
            $qaEntity->getCamelCaseEntityName(),
            $qaEntity->getId()
        );
    }
}
