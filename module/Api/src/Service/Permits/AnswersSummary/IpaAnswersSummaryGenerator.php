<?php

namespace Dvsa\Olcs\Api\Service\Permits\AnswersSummary;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;

class IpaAnswersSummaryGenerator
{
    /** @var AnswersSummaryFactory */
    private $answersSummaryFactory;

    /** @var AnswersSummaryRowsAdderInterface */
    private $defaultAnswersSummaryRowsAdder;

    /** @var array */
    private $customRowsAdders = [];

    /**
     * Create service instance
     *
     * @param AnswersSummaryFactory $answersSummaryFactory
     * @param AnswersSummaryRowsAdderInterface $defaultAnswersSummaryRowsAdder
     *
     * @return AnswersSummaryGenerator
     */
    public function __construct(
        AnswersSummaryFactory $answersSummaryFactory,
        AnswersSummaryRowsAdderInterface $defaultAnswersSummaryRowsAdder
    ) {
        $this->answersSummaryFactory = $answersSummaryFactory;
        $this->defaultAnswersSummaryRowsAdder = $defaultAnswersSummaryRowsAdder;
    }

    /**
     * Build and return a AnswersSummary instance using the appropriate data sources
     *
     * @param IrhpPermitApplicationEntity $irhpPermitApplicationEntity
     * @param bool $isSnapshot
     *
     * @return AnswersSummary
     */
    public function generate(IrhpPermitApplicationEntity $irhpPermitApplicationEntity, $isSnapshot = false)
    {
        $answersSummary = $this->answersSummaryFactory->create();

        $irhpPermitTypeId = $irhpPermitApplicationEntity->getIrhpApplication()->getIrhpPermitType()->getId();
        $rowsAdder = $this->defaultAnswersSummaryRowsAdder;

        if (isset($this->customRowsAdders[$irhpPermitTypeId])) {
            $rowsAdder = $this->customRowsAdders[$irhpPermitTypeId];
        }

        $rowsAdder->addRows($answersSummary, $irhpPermitApplicationEntity, $isSnapshot);

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
