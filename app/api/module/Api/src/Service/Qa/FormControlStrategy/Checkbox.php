<?php

namespace Dvsa\Olcs\Api\Service\Qa\FormControlStrategy;

use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Qa\FormControlStrategyInterface;
use Dvsa\Olcs\Api\Service\Qa\FormControlStrategy\Common\GenericAnswerSaver;
use RuntimeException;

class Checkbox implements FormControlStrategyInterface
{
    /** @var GenericAnswerSaver */
    private $genericAnswerSaver;

    /**
     * Create service instance
     *
     * @param GenericAnswerSaver $genericAnswerSaver
     *
     * @return Checkbox
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

        $dataType = $question->getQuestionType();
        if ($dataType != Question::QUESTION_TYPE_BOOLEAN) {
            throw new RuntimeException('Data type for ' . __CLASS__ . ' must be boolean');
        }

        $checked = false;
        if (!is_null($answer) && $answer->isEqualTo(true)) {
            $checked = true;
        }

        $optionSource = $question->getDecodedOptionSource();

        return [
            'type' => 'checkbox',
            'validators' => $applicationStep->getValidatorsRepresentation(),
            'data' => [
                'label' => $optionSource['label'],
                'not_checked_message' => $optionSource['not_checked_message'],
                'checked' => $checked,
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function saveFormData(ApplicationStep $applicationStep, IrhpApplication $irhpApplication, array $postData)
    {
        $answerValue = false;
        if (isset($postData['fields']['qaElement'])) {
            $answerValue = true;
        }

        $this->genericAnswerSaver->save($applicationStep, $irhpApplication, $answerValue);
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
