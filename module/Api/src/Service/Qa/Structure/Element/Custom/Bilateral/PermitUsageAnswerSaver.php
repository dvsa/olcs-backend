<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\ApplicationAnswersClearer;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\AnswerSaverInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerFetcher;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\GenericAnswerSaver;
use Dvsa\Olcs\Api\Service\Qa\Supports\IrhpPermitApplicationOnlyTrait;

class PermitUsageAnswerSaver implements AnswerSaverInterface
{
    use IrhpPermitApplicationOnlyTrait;

    /** @var GenericAnswerFetcher */
    private $genericAnswerFetcher;

    /** @var ApplicationAnswersClearer */
    private $applicationAnswersClearer;

    /** @var GenericAnswerSaver */
    private $genericAnswerSaver;

    /**
     * Create service instance
     *
     * @param GenericAnswerFetcher $genericAnswerFetcher
     * @param ApplicationAnswersClearer $applicationAnswersClearer
     * @param GenericAnswerSaver $genericAnswerSaver
     *
     * @return PermitUsageAnswerSaver
     */
    public function __construct(
        GenericAnswerFetcher $genericAnswerFetcher,
        ApplicationAnswersClearer $applicationAnswersClearer,
        GenericAnswerSaver $genericAnswerSaver
    ) {
        $this->genericAnswerFetcher = $genericAnswerFetcher;
        $this->applicationAnswersClearer = $applicationAnswersClearer;
        $this->genericAnswerSaver = $genericAnswerSaver;
    }

    /**
     * {@inheritdoc}
     */
    public function save(QaContext $qaContext, array $postData)
    {
        $existingAnswer = $qaContext->getQaEntity()->getBilateralPermitUsageSelection();

        if (!is_null($existingAnswer)) {
            $newAnswer = $this->genericAnswerFetcher->fetch(
                $qaContext->getApplicationStepEntity(),
                $postData
            );

            if ($existingAnswer != $newAnswer) {
                $this->applicationAnswersClearer->clearAfterApplicationStep($qaContext);
            }
        }

        $this->genericAnswerSaver->save($qaContext, $postData);
    }
}
