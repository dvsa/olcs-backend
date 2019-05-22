<?php

namespace Dvsa\Olcs\Api\Service\Qa\FormControlStrategy;

use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Qa\FormControlStrategyInterface;

class EcmtRemovalNoOfPermits implements FormControlStrategyInterface
{
    /** @var Text */
    private $text;

    /**
     * Create service instance
     *
     * @param Text $text
     *
     * @return EcmtRemovalNoOfPermits
     */
    public function __construct(Text $text)
    {
        $this->text = $text;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormRepresentation(
        ApplicationStep $applicationStep,
        IrhpApplication $irhpApplication,
        ?Answer $answer
    ) {
        $formRepresentation = $this->text->getFormRepresentation($applicationStep, $irhpApplication, $answer);

        // TODO: get from database
        $maxPermits = 37;

        $formRepresentation['data']['hint'] = sprintf(
            $formRepresentation['data']['hint'],
            $maxPermits
        );

        $validators = $formRepresentation['validators'];
        foreach ($validators as $key => $validator) {
            if ($validator['rule'] == 'Between') {
                $formRepresentation['validators'][$key]['parameters']['max'] = $maxPermits;
            }
        }

        return $formRepresentation;
    }

    /**
     * {@inheritdoc}
     */
    public function saveFormData(ApplicationStep $applicationStep, IrhpApplication $irhpApplication, array $postData)
    {
        // TODO: create an IrhpPermitApplication against the appropriate window if one does not exist
        // set permitsRequired to the value in postData
    }

    /**
     * {@inheritdoc}
     */
    public function processTemplateVars(
        ApplicationStep $applicationStep,
        IrhpApplication $irhpApplication,
        array $templateVars
    ) {
        // TODO: get from database
        $feePerPermit = 22;

        $templateVars['guidance']['value'] = sprintf(
            $templateVars['guidance']['value'],
            $feePerPermit
        );

        return $templateVars;
    }
}
