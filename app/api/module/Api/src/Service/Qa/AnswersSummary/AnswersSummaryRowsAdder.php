<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswersSummary;

use Dvsa\Olcs\Api\Service\Permits\AnswersSummary\AnswersSummary;
use Dvsa\Olcs\Api\Service\Permits\AnswersSummary\AnswersSummaryRowsAdderInterface;
use Dvsa\Olcs\Api\Service\Qa\Facade\SupplementedApplicationSteps\SupplementedApplicationStepsProvider;
use Dvsa\Olcs\Api\Service\Qa\QaEntityInterface;

class AnswersSummaryRowsAdder implements AnswersSummaryRowsAdderInterface
{
    /** @var SupplementedApplicationStepsProvider */
    private $supplementedApplicationStepsProvider;

    /** @var AnswersSummaryRowGenerator */
    private $answersSummaryRowGenerator;

    /**
     * Create service instance
     *
     * @param SupplementedApplicationStepsProvider $supplementedApplicationStepsProvider
     * @param AnswersSummaryRowGenerator $answersSummaryRowGenerator
     *
     * @return AnswersSummaryRowsAdder
     */
    public function __construct(
        SupplementedApplicationStepsProvider $supplementedApplicationStepsProvider,
        AnswersSummaryRowGenerator $answersSummaryRowGenerator
    ) {
        $this->supplementedApplicationStepsProvider = $supplementedApplicationStepsProvider;
        $this->answersSummaryRowGenerator = $answersSummaryRowGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function addRows(AnswersSummary $answersSummary, QaEntityInterface $qaEntity, $isSnapshot)
    {
        $supplementedApplicationSteps = $this->supplementedApplicationStepsProvider->get($qaEntity);

        foreach ($supplementedApplicationSteps as $supplementedApplicationStep) {
            $answersSummaryRow = $this->answersSummaryRowGenerator->generate(
                $supplementedApplicationStep,
                $qaEntity,
                $isSnapshot
            );

            $answersSummary->addRow($answersSummaryRow);
        }
    }
}
