<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswersSummary;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Permits\AnswersSummary\AnswersSummaryRowFactory;
use Dvsa\Olcs\Api\Service\Qa\Facade\SupplementedApplicationSteps\SupplementedApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorContextFactory;
use Zend\View\Renderer\RendererInterface;

class AnswersSummaryRowGenerator
{
    const TEMPLATE_DIRECTORY = 'answers-summary/';

    /** @var AnswersSummaryRowFactory */
    private $answersSummaryRowFactory;

    /** @var RendererInterface */
    private $viewRenderer;

    /** @var QuestionTextGeneratorContextFactory */
    private $questionTextGeneratorContextFactory;

    /**
     * Create service instance
     *
     * @param AnswersSummaryRowFactory $answersSummaryRowFactory
     * @param RendererInterface $viewRenderer
     * @param QuestionTextGeneratorContextFactory $questionTextGeneratorContextFactory
     *
     * @return AnswersSummaryRowGenerator
     */
    public function __construct(
        AnswersSummaryRowFactory $answersSummaryRowFactory,
        RendererInterface $viewRenderer,
        QuestionTextGeneratorContextFactory $questionTextGeneratorContextFactory
    ) {
        $this->answersSummaryRowFactory = $answersSummaryRowFactory;
        $this->viewRenderer = $viewRenderer;
        $this->questionTextGeneratorContextFactory = $questionTextGeneratorContextFactory;
    }

    /**
     * Build and return a AnswersSummaryRow instance using the appropriate data sources
     *
     * @param SupplementedApplicationStep $supplementedApplicationStep
     * @param IrhpApplicationEntity $irhpApplicationEntity
     * @param bool $isSnapshot
     *
     * @return AnswersSummaryRow
     */
    public function generate(
        SupplementedApplicationStep $supplementedApplicationStep,
        IrhpApplicationEntity $irhpApplicationEntity,
        $isSnapshot
    ) {
        $formControlStrategy = $supplementedApplicationStep->getFormControlStrategy();

        $questionTextGeneratorContext = $this->questionTextGeneratorContextFactory->create(
            $supplementedApplicationStep->getApplicationStep(),
            $irhpApplicationEntity
        );

        $questionText = $formControlStrategy->getQuestionText($questionTextGeneratorContext);

        $answerSummaryProvider = $formControlStrategy->getAnswerSummaryProvider();
        $templatePath = self::TEMPLATE_DIRECTORY . $answerSummaryProvider->getTemplateName();

        $applicationStepEntity = $supplementedApplicationStep->getApplicationStep();

        $templateVariables = $answerSummaryProvider->getTemplateVariables(
            $applicationStepEntity,
            $irhpApplicationEntity,
            $isSnapshot
        );

        $question = $applicationStepEntity->getQuestion();
        $formattedAnswer = $this->viewRenderer->render($templatePath, $templateVariables);

        return $this->answersSummaryRowFactory->create(
            $questionText->getQuestion()->getTranslateableText()->getKey(),
            $formattedAnswer,
            $question->getSlug()
        );
    }
}
