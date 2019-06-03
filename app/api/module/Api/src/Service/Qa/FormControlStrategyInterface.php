<?php

namespace Dvsa\Olcs\Api\Service\Qa;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;

interface FormControlStrategyInterface
{
    /**
     * Returns a presentation-neutral representation of the form layout corresponding to the specified applicationStep,
     * irhpApplication and answer instances
     *
     * @param ApplicationStep $applicationStep
     * @param IrhpApplication $irhpApplication
     * @param Answer $answer
     *
     * @return array
     */
    public function getFormRepresentation(ApplicationStep $applicationStep, IrhpApplication $irhpApplication, ?Answer $answer);

    /**
     * Saves an answer to persistent storage for the specified applicationStep and irhpApplication using the supplied
     * postData
     *
     * @param ApplicationStep $applicationStep
     * @param IrhpApplication $irhpApplication
     * @param array $postData
     */
    public function saveFormData(ApplicationStep $applicationStep, IrhpApplication $irhpApplication, array $postData);

    /**
     * Makes any required changes to the template variables
     *
     * @param ApplicationStep $applicationStep
     * @param IrhpApplication $irhpApplication
     * @param array $templateVars
     *
     * @return array
     */
    public function processTemplateVars(
        ApplicationStep $applicationStep,
        IrhpApplication $irhpApplication,
        array $templateVars
    );
}
