<?php

namespace Dvsa\Olcs\Api\Entity\Generic;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;

/**
 * ApplicationStep Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="application_step",
 *    indexes={
 *        @ORM\Index(name="fk_application_path_steps_application_paths1_idx",
     *     columns={"application_path_id"}),
 *        @ORM\Index(name="fk_application_path_steps_questions1_idx", columns={"question_id"}),
 *        @ORM\Index(name="fk_application_step_application_step1_idx", columns={"parent_id"})
 *    }
 * )
 */
class ApplicationStep extends AbstractApplicationStep
{
    /**
     * Get the slug of the application step immediately following this one
     *
     * @return string
     */
    public function getNextStepSlug()
    {
        $applicationSteps = $this->getApplicationPath()->getApplicationSteps()->getValues();
        $thisIndex = array_search($this, $applicationSteps, true);
        $nextIndex = $thisIndex + 1;

        if (!isset($applicationSteps[$nextIndex])) {
            return 'check-answers';
        }

        $nextApplicationStep = $applicationSteps[$nextIndex];
        return $nextApplicationStep->getQuestion()->getSlug();
    }

    /**
     * Get the instance of the application step immediately preceding this one
     *
     * @return ApplicationStep
     *
     * @throws NotFoundException if there is no step preceding this one
     */
    public function getPreviousApplicationStep()
    {
        $applicationSteps = $this->getApplicationPath()->getApplicationSteps()->getValues();
        $thisIndex = array_search($this, $applicationSteps, true);

        if ($thisIndex == 0) {
            throw new NotFoundException('No previous application step found');
        }

        return $applicationSteps[$thisIndex - 1];
    }

    /**
     * Get a platform/framework-neutral representation of the validators applicable to this application step
     *
     * @return array
     */
    public function getValidatorsRepresentation()
    {
        $validators = [];

        foreach ($this->question->getApplicationValidations() as $applicationValidation) {
            $validators[] = $applicationValidation->getRepresentation();
        }

        return $validators;
    }
}
