<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\Answer as AnswerRepository;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerProvider;

class GenericAnswerClearer implements AnswerClearerInterface
{
    /** @var GenericAnswerProvider */
    private $genericAnswerProvider;

    /** @var AnswerRepository */
    private $answerRepo;

    /**
     * Create service instance
     *
     * @param GenericAnswerProvider $genericAnswerProvider
     * @param AnswerRepo $answerRepo
     *
     * @return GenericAnswerClearer
     */
    public function __construct(GenericAnswerProvider $genericAnswerProvider, AnswerRepository $answerRepo)
    {
        $this->genericAnswerProvider = $genericAnswerProvider;
        $this->answerRepo = $answerRepo;
    }

    /**
     * {@inheritdoc}
     */
    public function clear(ApplicationStep $applicationStep, IrhpApplication $irhpApplication)
    {
        $answer = null;
        try {
            $answer = $this->genericAnswerProvider->get($applicationStep, $irhpApplication);
        } catch (NotFoundException $e) {
        }

        if (is_object($answer)) {
            $this->answerRepo->delete($answer);
        }
    }
}
