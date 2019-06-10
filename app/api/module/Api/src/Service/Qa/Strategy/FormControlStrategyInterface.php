<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Qa\Element\SelfservePage;

interface FormControlStrategyInterface
{
    /**
     * Get the name used by the frontend to render this form control
     *
     * @return string
     */
    public function getFrontendType();

    /**
     * Get an instance of ElementInterface representing this form control
     *
     * @param ApplicationStep $applicationStep
     * @param IrhpApplication $irhpApplication
     * @param Answer $answer (optional)
     *
     * @return ElementInterface
     */
    public function getElement(
        ApplicationStep $applicationStep,
        IrhpApplication $irhpApplication,
        ?Answer $answer = null
    );

    /**
     * Save the data for this form control to the persistent data store
     *
     * @param ApplicationStep $applicationStep
     * @param IrhpApplication $irhpApplication
     * @param array $postData
     */
    public function saveFormData(ApplicationStep $applicationStep, IrhpApplication $irhpApplication, array $postData);

    /**
     * Make any required changes to the Selfserve object
     *
     * @param SelfservePage $page
     */
    public function postProcessSelfservePage(SelfservePage $page);
}
