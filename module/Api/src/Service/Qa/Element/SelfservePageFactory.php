<?php

namespace Dvsa\Olcs\Api\Service\Qa\Element;

class SelfservePageFactory
{
    /**
     * Create and return an ApplicationStep instance
     *
     * @param string $applicationReference
     * @param ApplicationStep $applicationStep
     * @param QuestionText $questionText
     * @param string $nextStepSlug
     *
     * @return SelfservePage
     */
    public function create(
        $applicationReference,
        ApplicationStep $applicationStep,
        QuestionText $questionText,
        $nextStepSlug
    ) {
        return new SelfservePage($applicationReference, $applicationStep, $questionText, $nextStepSlug);
    }
}
