<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionText;

class SelfservePage
{
    /** @var string */
    private $title;

    /** @var array */
    private $additionalViewData;

    /** @var ApplicationStep */
    private $applicationStep;

    /** @var QuestionText */
    private $questionText;

    /** @var string */
    private $nextStepSlug;

    /**
     * Create instance
     *
     * @param string $title
     * @param array $additionalViewData
     * @param ApplicationStep $applicationStep
     * @param QuestionText $questionText
     * @param string $nextStepSlug
     *
     * @return SelfservePage
     */
    public function __construct(
        $title,
        array $additionalViewData,
        ApplicationStep $applicationStep,
        QuestionText $questionText,
        $nextStepSlug
    ) {
        $this->title = $title;
        $this->additionalViewData = $additionalViewData;
        $this->applicationStep = $applicationStep;
        $this->questionText = $questionText;
        $this->nextStepSlug = $nextStepSlug;
    }

    /**
     * Get the representation of this class to be returned by the API endpoint
     *
     * @return array
     */
    public function getRepresentation()
    {
        return [
            'title' => $this->title,
            'additionalViewData' => $this->additionalViewData,
            'applicationStep' => $this->applicationStep->getRepresentation(),
            'questionText' => $this->questionText->getRepresentation(),
            'nextStepSlug' => $this->nextStepSlug,
        ];
    }

    /**
     * Get the embedded QuestionText instance
     *
     * @return QuestionText
     */
    public function getQuestionText()
    {
        return $this->questionText;
    }
}
