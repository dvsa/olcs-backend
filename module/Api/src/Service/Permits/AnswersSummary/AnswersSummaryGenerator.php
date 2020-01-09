<?php

namespace Dvsa\Olcs\Api\Service\Permits\AnswersSummary;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;

class AnswersSummaryGenerator
{
    /** @var AnswersSummaryFactory */
    private $answersSummaryFactory;

    /** @var AnswersSummaryRowsAdderInterface */
    private $headerAnswersSummaryRowsAdder;

    /** @var AnswersSummaryRowsAdderInterface */
    private $defaultAnswersSummaryRowsAdder;

    /** @var array */
    private $customRowsAdders = [];

    /**
     * Create service instance
     *
     * @param AnswersSummaryFactory $answersSummaryFactory
     * @param AnswersSummaryRowsAdderInterface $headerAnswersSummaryRowsAdder
     * @param AnswersSummaryRowsAdderInterface $defaultAnswersSummaryRowsAdder
     *
     * @return AnswersSummaryGenerator
     */
    public function __construct(
        AnswersSummaryFactory $answersSummaryFactory,
        AnswersSummaryRowsAdderInterface $headerAnswersSummaryRowsAdder,
        AnswersSummaryRowsAdderInterface $defaultAnswersSummaryRowsAdder
    ) {
        $this->answersSummaryFactory = $answersSummaryFactory;
        $this->headerAnswersSummaryRowsAdder = $headerAnswersSummaryRowsAdder;
        $this->defaultAnswersSummaryRowsAdder = $defaultAnswersSummaryRowsAdder;
    }

    /**
     * Build and return a AnswersSummary instance using the appropriate data sources
     *
     * @param IrhpApplicationEntity $irhpApplicationEntity
     * @param bool $isSnapshot
     *
     * @return AnswersSummary
     */
    public function generate(IrhpApplicationEntity $irhpApplicationEntity, $isSnapshot = false)
    {
        $answersSummary = $this->answersSummaryFactory->create();
        $this->headerAnswersSummaryRowsAdder->addRows($answersSummary, $irhpApplicationEntity, $isSnapshot);

        $irhpPermitTypeId = $irhpApplicationEntity->getIrhpPermitType()->getId();
        $rowsAdder = $this->defaultAnswersSummaryRowsAdder;
        if (isset($this->customRowsAdders[$irhpPermitTypeId])) {
            $rowsAdder = $this->customRowsAdders[$irhpPermitTypeId];
        }

        $rowsAdder->addRows($answersSummary, $irhpApplicationEntity, $isSnapshot);

        return $answersSummary;
    }

    /**
     * Register a custom rows adder to accommodate old non-q&a types
     *
     * @param int $irhpPermitTypeId
     * @param AnswersSummaryRowsAdderInterface $rowsAdder
     */
    public function registerCustomRowsAdder($irhpPermitTypeId, AnswersSummaryRowsAdderInterface $rowsAdder)
    {
        $this->customRowsAdders[$irhpPermitTypeId] = $rowsAdder;
    }
}
