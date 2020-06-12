<?php

namespace Dvsa\Olcs\Api\Service\Permits\AnswersSummary;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Zend\View\Renderer\RendererInterface;
use Dvsa\Olcs\Api\Service\Qa\QaEntityInterface;

class HeaderAnswersSummaryRowsAdder implements AnswersSummaryRowsAdderInterface
{
    const TEMPLATE_DIRECTORY = 'answers-summary/';

    /** @var AnswersSummaryRowFactory */
    private $answersSummaryRowFactory;

    /** @var RendererInterface */
    private $viewRenderer;

    /**
     * Create service instance
     *
     * @param AnswersSummaryRowFactory $answersSummaryRowFactory
     * @param RendererInterface $viewRenderer
     *
     * @return HeaderAnswersSummaryRowsAdder
     */
    public function __construct(AnswersSummaryRowFactory $answersSummaryRowFactory, RendererInterface $viewRenderer)
    {
        $this->answersSummaryRowFactory = $answersSummaryRowFactory;
        $this->viewRenderer = $viewRenderer;
    }

    /**
     * {@inheritdoc}
     */
    public function addRows(AnswersSummary $answersSummary, QaEntityInterface $entity, $isSnapshot)
    {
        if (!$isSnapshot) {
            $answersSummary->addRow(
                $this->generatePermitTypeRow($entity)
            );
        }

        $answersSummary->addRow(
            $this->generateLicenceRow($entity)
        );
    }

    /**
     * Return a row representing the selected permit type
     *
     * @param IrhpApplicationEntity $irhpApplicationEntity
     *
     * @return AnswersSummaryRow
     */
    private function generatePermitTypeRow(IrhpApplicationEntity $irhpApplicationEntity)
    {
        $answer = $irhpApplicationEntity->getIrhpPermitType()->getName()->getDescription();

        $formattedAnswer = $this->viewRenderer->render(
            self::TEMPLATE_DIRECTORY . 'generic',
            ['answer' => $answer]
        );

        return $this->answersSummaryRowFactory->create(
            'permits.page.fee.permit.type',
            $formattedAnswer
        );
    }

    /**
     * Return a row representing the selected licence
     *
     * @param IrhpApplicationEntity $irhpApplicationEntity
     *
     * @return AnswersSummaryRow
     */
    private function generateLicenceRow(IrhpApplicationEntity $irhpApplicationEntity)
    {
        $licence = $irhpApplicationEntity->getLicence();

        $formattedAnswer = $this->viewRenderer->render(
            self::TEMPLATE_DIRECTORY . 'licence',
            [
                'licenceNo' => $licence->getLicNo(),
                'trafficAreaName' => $licence->getTrafficArea()->getName()
            ]
        );

        return $this->answersSummaryRowFactory->create(
            'permits.check-answers.page.question.licence',
            $formattedAnswer,
            'licence'
        );
    }
}
