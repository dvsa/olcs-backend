<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\Answer as AnswerRepository;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerProvider;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Supports\AnyTrait;

class GenericAnswerClearer implements AnswerClearerInterface
{
    use AnyTrait;

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
    public function clear(QaContext $qaContext)
    {
        $answer = null;
        try {
            $answer = $this->genericAnswerProvider->get($qaContext);
        } catch (NotFoundException $e) {
        }

        if (is_object($answer)) {
            $qaContext->getQaEntity()->getAnswers()->remove($answer);
            $this->answerRepo->delete($answer);
        }
    }
}
