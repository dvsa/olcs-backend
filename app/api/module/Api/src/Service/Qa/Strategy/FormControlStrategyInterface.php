<?php

namespace Dvsa\Olcs\Api\Service\Qa\Strategy;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Qa\AnswersSummary\AnswerSummaryProviderInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementInterface;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionText;

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
     * @param ElementGeneratorContext $context
     *
     * @return ElementInterface
     */
    public function getElement(ElementGeneratorContext $context);

    /**
     * Save the data for this form control to the persistent data store
     *
     * @param ApplicationStep $applicationStep
     * @param IrhpApplication $irhpApplication
     * @param array $postData
     */
    public function saveFormData(ApplicationStep $applicationStep, IrhpApplication $irhpApplication, array $postData);

    /**
     * Remove or reset to the default state any answer present for this form control
     *
     * @param ApplicationStep $applicationStep
     * @param IrhpApplication $irhpApplication
     */
    public function clearAnswer(ApplicationStep $applicationStep, IrhpApplication $irhpApplication);

    /**
     * Get a QuestionText instance corresponding to this form control
     *
     * @param QuestionTextGeneratorContext $context
     *
     * @return QuestionText
     */
    public function getQuestionText(QuestionTextGeneratorContext $context);

    /**
     * Get the appropriate instance of AnswerSummaryProviderInterface for this form control
     *
     * @return AnswerSummaryProviderInterface
     */
    public function getAnswerSummaryProvider();
}
