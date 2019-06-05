<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswerSaver;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;

interface AnswerSaverInterface
{
    /**
     * Save an answer corresponding to the supplied application step and application to persistent storage using
     * the supplied post data as the source of the answer
     *
     * @param ApplicationStep $applicationStep
     * @param IrhpApplication $irhpApplication
     * @param array $postData
     */
    public function save(ApplicationStep $applicationStep, IrhpApplication $irhpApplication, array $postData);
}
