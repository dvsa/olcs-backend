<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Common;

use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\ApplicationAnswersClearer;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\QaContext;

class PermitUsageUpdater
{
    /** @var GenericAnswerWriter */
    private $genericAnswerWriter;

    /** @var ApplicationAnswersClearer */
    private $applicationAnswersClearer;

    /**
     * Create service instance
     *
     * @param GenericAnswerWriter $genericAnswerWriter
     * @param ApplicationAnswersClearer $applicationAnswersClearer
     *
     * @return PermitUsageUpdater
     */
    public function __construct(
        GenericAnswerWriter $genericAnswerWriter,
        ApplicationAnswersClearer $applicationAnswersClearer
    ) {
        $this->genericAnswerWriter = $genericAnswerWriter;
        $this->applicationAnswersClearer = $applicationAnswersClearer;
    }

    /**
     * Update the permit usage value relating to a specific country within a bilateral application
     *
     * @param QaContext $qaContext
     * @param string $newAnswer
     */
    public function update(QaContext $qaContext, $newAnswer)
    {
        $existingAnswer = $qaContext->getQaEntity()->getBilateralPermitUsageSelection();

        if (!is_null($existingAnswer) && $existingAnswer != $newAnswer) {
            $this->applicationAnswersClearer->clearAfterApplicationStep($qaContext);
        }

        $this->genericAnswerWriter->write($qaContext, $newAnswer);
    }
}
