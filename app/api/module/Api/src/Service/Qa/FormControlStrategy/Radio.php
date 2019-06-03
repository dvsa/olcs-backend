<?php

namespace Dvsa\Olcs\Api\Service\Qa\FormControlStrategy;

use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Qa\FormControlStrategyInterface;
use Dvsa\Olcs\Api\Service\Qa\FormControlStrategy\Common\GenericAnswerSaver;

class Radio implements FormControlStrategyInterface
{
    /** @var OptionsGeneratorProvider */
    private $optionsGeneratorProvider;

    /** @var GenericAnswerSaver */
    private $genericAnswerSaver;

    /**
     * Create service instance
     *
     * @param OptionsGeneratorProvider $optionsGeneratorProvider
     * @param GenericAnswerSaver $genericAnswerSaver
     *
     * @return Radio
     */
    public function __construct(
        OptionsGeneratorProvider $optionsGeneratorProvider,
        GenericAnswerSaver $genericAnswerSaver
    ) {
        $this->optionsGeneratorProvider = $optionsGeneratorProvider;
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

        $optionsGeneratorAttributes = $optionSource['options_generator'];
        $optionsGenerator = $this->optionsGeneratorProvider->get($optionsGeneratorAttributes['type']);
        $options = $optionsGenerator->get($optionsGeneratorAttributes['parameters']);

        $detailedOptions = [];
        foreach ($options as $option) {
            $selected = false;
            if (!is_null($answer)) {
                $selected = $answer->isEqualTo($option['value']);
            }

            $detailedOption = $option;
            $detailedOption['selected'] = $selected;
            $detailedOptions[] = $detailedOption;
        }

        return [
            'type' => 'radio',
            'validators' => $applicationStep->getValidatorsRepresentation(),
            'data' => [
                'options' => $detailedOptions
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
