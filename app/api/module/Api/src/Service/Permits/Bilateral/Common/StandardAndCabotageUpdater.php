<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Common;

use Dvsa\Olcs\Api\Service\Qa\QaContext;

class StandardAndCabotageUpdater
{
    /** @var ModifiedAnswerUpdater */
    private $modifiedAnswerUpdater;

    /**
     * Create service instance
     *
     * @param ModifiedAnswerUpdater $modifiedAnswerUpdater
     *
     * @return StandardAndCabotageUpdater
     */
    public function __construct(ModifiedAnswerUpdater $modifiedAnswerUpdater)
    {
        $this->modifiedAnswerUpdater = $modifiedAnswerUpdater;
    }

    /**
     * Update the standard and cabotage value relating to a specific country within a bilateral application
     *
     * @param QaContext $qaContext
     * @param string $newAnswer
     */
    public function update(QaContext $qaContext, $newAnswer)
    {
        $this->modifiedAnswerUpdater->update(
            $qaContext,
            $qaContext->getQaEntity()->getBilateralCabotageSelection(),
            $newAnswer
        );
    }
}
