<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswersSummary;

use Dvsa\Olcs\Api\Service\Permits\AnswersSummary\AnswersSummaryRowFactory;
use Dvsa\Olcs\Api\Service\Qa\Facade\SupplementedApplicationSteps\SupplementedApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\QaContextFactory;
use Dvsa\Olcs\Api\Service\Qa\QaEntityInterface;
use RuntimeException;
use Zend\View\Renderer\RendererInterface;

class AnswersSummaryRowGenerator
{
    const TEMPLATE_DIRECTORY = 'answers-summary/';

    /** @var AnswersSummaryRowFactory */
    private $answersSummaryRowFactory;

    /** @var RendererInterface */
    private $viewRenderer;

    /** @var QaContextFactory */
    private $qaContextFactory;

    /**
     * Create service instance
     *
     * @param AnswersSummaryRowFactory $answersSummaryRowFactory
     * @param RendererInterface $viewRenderer
     * @param QaContextFactory $qaContextFactory
     *
     * @return AnswersSummaryRowGenerator
     */
    public function __construct(
        AnswersSummaryRowFactory $answersSummaryRowFactory,
        RendererInterface $viewRenderer,
        QaContextFactory $qaContextFactory
    ) {
        $this->answersSummaryRowFactory = $answersSummaryRowFactory;
        $this->viewRenderer = $viewRenderer;
        $this->qaContextFactory = $qaContextFactory;
    }

    /**
     * Build and return a AnswersSummaryRow instance using the appropriate data sources
     *
     * @param SupplementedApplicationStep $supplementedApplicationStep
     * @param QaEntityInterface $qaEntity
     * @param bool $isSnapshot
     *
     * @return AnswersSummaryRow
     */
    public function generate(
        SupplementedApplicationStep $supplementedApplicationStep,
        QaEntityInterface $qaEntity,
        $isSnapshot
    ) {
        $formControlStrategy = $supplementedApplicationStep->getFormControlStrategy();
        $answerSummaryProvider = $formControlStrategy->getAnswerSummaryProvider();

        if (!$answerSummaryProvider->supports($qaEntity)) {
            throw new RuntimeException(
                sprintf(
                    'Answer summary provider does not support entity type %s',
                    $qaEntity->getCamelCaseEntityName()
                )
            );
        }

        $applicationStep = $supplementedApplicationStep->getApplicationStep();
        $qaContext = $this->qaContextFactory->create($applicationStep, $qaEntity);
        $questionText = $formControlStrategy->getQuestionText($qaContext);

        $templatePath = self::TEMPLATE_DIRECTORY . $answerSummaryProvider->getTemplateName();
        $templateVariables = $answerSummaryProvider->getTemplateVariables($qaContext, $isSnapshot);

        $question = $applicationStep->getQuestion();
        $formattedAnswer = $this->viewRenderer->render($templatePath, $templateVariables);

        $slug = (!$isSnapshot && $answerSummaryProvider->shouldIncludeSlug($qaEntity)) ? $question->getSlug() : null;

        return $this->answersSummaryRowFactory->create(
            $questionText->getQuestion()->getTranslateableText()->getKey(),
            $formattedAnswer,
            $slug
        );
    }
}
