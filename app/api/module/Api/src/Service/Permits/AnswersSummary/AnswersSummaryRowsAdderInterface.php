<?php

namespace Dvsa\Olcs\Api\Service\Permits\AnswersSummary;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;

interface AnswersSummaryRowsAdderInterface
{
    /**
     * Add one or more AnswersSummaryRow instances to the provided AnswersSummary instance
     *
     * @param AnswersSummary $answersSummary
     * @param IrhpApplicationEntity $irhpApplication
     * @param bool $isSnapshot
     */
    public function addRows(AnswersSummary $answersSummary, IrhpApplicationEntity $irhpApplication, $isSnapshot);
}
