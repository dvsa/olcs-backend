<?php

namespace Dvsa\Olcs\Api\Service\Qa\FormControlStrategy;

use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Qa\FormControlStrategyInterface;
use Dvsa\Olcs\Api\Service\Qa\FormControlStrategy\Common\GenericAnswerSaver;

class Text implements FormControlStrategyInterface
{
    /** @var GenericAnswerSaver */
    private $genericAnswerSaver;

    /**
     * Create service instance
     *
     * @param GenericAnswerSaver $genericAnswerSaver
     *
     * @return Text
     */
    public function __construct(GenericAnswerSaver $genericAnswerSaver)
    {
        $this->genericAnswerSaver = $genericAnswerSaver;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormRepresentation(
        ApplicationStep $applicationStep,
        IrhpApplication $irhpApplication,
        ?Answer $answer
    ) {
        $question = $applicationStep->getQuestion();
        $optionSource = $question->getDecodedOptionSource();

        return [
            'type' => 'text',
            'validators' => $applicationStep->getValidatorsRepresentation(),
            'data' => [
                'label' => $optionSource['label'],
                'hint' => $optionSource['hint'],
                'value' => (is_object($answer) ? $answer->getValue() : ''),
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function saveFormData(ApplicationStep $applicationStep, IrhpApplication $irhpApplication, array $postData)
    {
        $this->genericAnswerSaver->save($applicationStep, $irhpApplication, $postData['fields']['qaElement']);
    }

    /**
     * {@inheritdoc}
     */
    public function processTemplateVars(
        ApplicationStep $applicationStep,
        IrhpApplication $irhpApplication,
        array $templateVars
    ) {
        return $templateVars;
    }
}
