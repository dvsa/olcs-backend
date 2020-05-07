<?php

namespace Dvsa\Olcs\Api\Service\Permits\AnswersSummary;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Qa\QaEntityInterface;
use Zend\View\Renderer\RendererInterface;

class BilateralAnswersSummaryRowsAdder implements AnswersSummaryRowsAdderInterface
{
    const TEMPLATE_DIRECTORY = 'answers-summary/';

    /** @var AnswersSummaryRowFactory */
    private $answersSummaryRowFactory;

    /** @var RendererInterface */
    private $viewRenderer;

    /** @var BilateralIpaAnswersSummaryRowsAdder */
    private $bilateralIpaAnswersSummaryRowsAdder;

    /**
     * Create service instance
     *
     * @param AnswersSummaryRowFactory $answersSummaryRowFactory
     * @param RendererInterface $viewRenderer
     * @param BilateralIpaAnswersSummaryRowsAdder $bilateralIpaAnswersSummaryRowsAdder
     *
     * @return BilateralAnswersSummaryRowsAdder
     */
    public function __construct(
        AnswersSummaryRowFactory $answersSummaryRowFactory,
        RendererInterface $viewRenderer,
        BilateralIpaAnswersSummaryRowsAdder $bilateralIpaAnswersSummaryRowsAdder
    ) {
        $this->answersSummaryRowFactory = $answersSummaryRowFactory;
        $this->viewRenderer = $viewRenderer;
        $this->bilateralIpaAnswersSummaryRowsAdder = $bilateralIpaAnswersSummaryRowsAdder;
    }

    /**
     * {@inheritdoc}
     */
    public function addRows(AnswersSummary $answersSummary, QaEntityInterface $irhpApplication, $isSnapshot)
    {
        $irhpPermitApplications = $irhpApplication->getIrhpPermitApplicationsByCountryName();

        $answersSummary->addRow(
            $this->getCountryNamesRow($irhpPermitApplications)
        );

        foreach ($irhpPermitApplications as $irhpPermitApplication) {
            $answersSummary->addRow(
                $this->getCountryNameRow($irhpPermitApplication)
            );

            $this->bilateralIpaAnswersSummaryRowsAdder->addRows($answersSummary, $irhpPermitApplication, $isSnapshot);
        }
    }

    /**
     * Get a row representing the country names for a bilateral application
     *
     * @param mixed $irhpPermitApplications
     *
     * @return AnswersSummaryRow
     */
    private function getCountryNamesRow($irhpPermitApplications)
    {
        $countryNames = [];

        foreach ($irhpPermitApplications as $irhpPermitApplication) {
            $countryNames[] = $irhpPermitApplication->getIrhpPermitWindow()
                ->getIrhpPermitStock()
                ->getCountry()
                ->getCountryDesc();
        }

        $templateVariables = [
            'countryNames' => array_values(array_unique($countryNames))
        ];

        $formattedAnswer = $this->viewRenderer->render(
            self::TEMPLATE_DIRECTORY . 'bilateral-country-names',
            $templateVariables
        );
 
        return $this->answersSummaryRowFactory->create(
            'permits.irhp.application.question.countries',
            $formattedAnswer
        );
    }

    /**
     * Get a row representing the country name for a bilateral application
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     *
     * @return AnswersSummaryRow
     */
    private function getCountryNameRow(IrhpPermitApplication $irhpPermitApplication)
    {
        $countryName = $irhpPermitApplication->getIrhpPermitWindow()
            ->getIrhpPermitStock()
            ->getCountry()
            ->getCountryDesc();

        $templateVariables = [
            'answer' => $countryName
        ];

        $formattedAnswer = $this->viewRenderer->render(
            self::TEMPLATE_DIRECTORY . 'generic',
            $templateVariables
        );

        return $this->answersSummaryRowFactory->create(
            'permits.irhp.application.question.country',
            $formattedAnswer
        );
    }
}
