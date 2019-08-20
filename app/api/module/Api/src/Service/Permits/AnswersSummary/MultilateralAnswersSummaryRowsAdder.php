<?php

namespace Dvsa\Olcs\Api\Service\Permits\AnswersSummary;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Zend\View\Renderer\RendererInterface;

class MultilateralAnswersSummaryRowsAdder implements AnswersSummaryRowsAdderInterface
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
     * @return MultilateralAnswersSummaryRowsAdder
     */
    public function __construct(AnswersSummaryRowFactory $answersSummaryRowFactory, RendererInterface $viewRenderer)
    {
        $this->answersSummaryRowFactory = $answersSummaryRowFactory;
        $this->viewRenderer = $viewRenderer;
    }

    /**
     * {@inheritdoc}
     */
    public function addRows(AnswersSummary $answersSummary, IrhpApplicationEntity $irhpApplication, $isSnapshot)
    {
        $irhpPermitApplications = $irhpApplication->getIrhpPermitApplications();

        foreach ($irhpPermitApplications as $irhpPermitApplication) {
            $validityYear = $irhpPermitApplication->getIrhpPermitWindow()
                ->getIrhpPermitStock()
                ->getValidityYear();

            $rows[] = [
                'permitsRequired' => $irhpPermitApplication->getPermitsRequired(),
                'year' => $validityYear
            ];
        }

        $formattedAnswer = $this->viewRenderer->render(
            self::TEMPLATE_DIRECTORY . 'multilateral-permits-required',
            ['rows' => $rows]
        );

        $answersSummaryRow = $this->answersSummaryRowFactory->create(
            'permits.irhp.application.question.no-of-permits',
            $formattedAnswer,
            'no-of-permits'
        );

        $answersSummary->addRow($answersSummaryRow);
    }
}
