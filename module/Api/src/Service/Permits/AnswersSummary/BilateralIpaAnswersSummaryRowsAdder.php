<?php

namespace Dvsa\Olcs\Api\Service\Permits\AnswersSummary;

use DateTime;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepository;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\QaEntityInterface;
use Zend\View\Renderer\RendererInterface;

class BilateralIpaAnswersSummaryRowsAdder implements AnswersSummaryRowsAdderInterface
{
    const TEMPLATE_DIRECTORY = 'answers-summary/';

    /** @var AnswersSummaryRowFactory */
    private $answersSummaryRowFactory;

    /** @var RendererInterface */
    private $viewRenderer;

    /** @var AnswersSummaryRowsAdderInterface */
    private $qaAnswersSummaryRowsAdder;

    /** @var IrhpPermitStockRepository */
    private $irhpPermitStockRepo;

    /**
     * Create service instance
     *
     * @param AnswersSummaryRowFactory $answersSummaryRowFactory
     * @param RendererInterface $viewRenderer
     * @param AnswersSummaryRowsAdderInterface $qaAnswersSummaryRowsAdder
     * @param IrhpPermitStockRepository $irhpPermitStockRepo
     *
     * @return BilateralIpaAnswersSummaryRowsAdder
     */
    public function __construct(
        AnswersSummaryRowFactory $answersSummaryRowFactory,
        RendererInterface $viewRenderer,
        AnswersSummaryRowsAdderInterface $qaAnswersSummaryRowsAdder,
        IrhpPermitStockRepository $irhpPermitStockRepo
    ) {
        $this->answersSummaryRowFactory = $answersSummaryRowFactory;
        $this->viewRenderer = $viewRenderer;
        $this->qaAnswersSummaryRowsAdder = $qaAnswersSummaryRowsAdder;
        $this->irhpPermitStockRepo = $irhpPermitStockRepo;
    }

    /**
     * {@inheritdoc}
     */
    public function addRows(AnswersSummary $answersSummary, QaEntityInterface $irhpPermitApplication, $isSnapshot)
    {
        $answersSummary->addRow(
            $this->getPeriodRequiredRow($irhpPermitApplication, $isSnapshot)
        );

        $this->qaAnswersSummaryRowsAdder->addRows($answersSummary, $irhpPermitApplication, $isSnapshot);
    }

    /**
     * Get a row representing the period required for a bilateral application
     *
     * @param IrhpPermitApplicationEntity $irhpPermitApplication
     * @param bool $isSnapshot
     *
     * @return AnswersSummaryRow
     */
    private function getPeriodRequiredRow(IrhpPermitApplicationEntity $irhpPermitApplication, $isSnapshot)
    {
        $stock = $irhpPermitApplication->getIrhpPermitWindow()
            ->getIrhpPermitStock();

        $templateVariables = [
            'answer' => $stock->getPeriodNameKey()
        ];

        $formattedAnswer = $this->viewRenderer->render(
            self::TEMPLATE_DIRECTORY . 'generic',
            $templateVariables
        );

        $slug = null;

        if (!$isSnapshot) {
            $availableStocks = $this->irhpPermitStockRepo->fetchOpenBilateralStocksByCountry(
                $stock->getCountry()->getId(),
                new DateTime()
            );
            $slug = (count($availableStocks) > 1) ? 'period' : null;
        }

        return $this->answersSummaryRowFactory->create(
            $stock->getBilateralAnswerSummaryLabelKey(),
            $formattedAnswer,
            $slug
        );
    }
}
