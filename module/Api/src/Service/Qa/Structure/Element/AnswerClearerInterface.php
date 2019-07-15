<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;

interface AnswerClearerInterface
{
    /**
     * Clears from persistent storage the answer data corresponding to the supplied application step and application
     *
     * @param ApplicationStep $applicationStep
     * @param IrhpApplication $irhpApplication
     */
    public function clear(ApplicationStep $applicationStep, IrhpApplication $irhpApplication);
}
