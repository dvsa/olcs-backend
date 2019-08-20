<?php

namespace Dvsa\Olcs\Api\Service\Permits\AnswersSummary;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Zend\View\Renderer\RendererInterface;

class BilateralAnswersSummaryRowsAdder implements AnswersSummaryRowsAdderInterface
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
     * @return BilateralAnswersSummaryRowsAdder
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

        $answersSummary->addRow(
            $this->getCountryNamesRow($irhpPermitApplications)
        );

        $answersSummary->addRow(
            $this->getPermitsRequiredRow($irhpPermitApplications)
        );
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
            $formattedAnswer,
            'countries'
        );
    }

    /**
     * Get a row representing the permits required for a bilateral application
     *
     * @param mixed $irhpPermitApplications
     *
     * @return AnswersSummaryRow
     */
    private function getPermitsRequiredRow($irhpPermitApplications)
    {
        $rows = [];

        foreach ($irhpPermitApplications as $irhpPermitApplication) {
            $irhpPermitStock = $irhpPermitApplication->getIrhpPermitWindow()
                ->getIrhpPermitStock();

            $rows[] = [
                'permitsRequired' => $irhpPermitApplication->getPermitsRequired(),
                'countryName' => $irhpPermitStock->getCountry()->getCountryDesc(),
                'year' => $irhpPermitStock->getValidityYear()
            ];
        }

        $formattedAnswer = $this->viewRenderer->render(
            self::TEMPLATE_DIRECTORY . 'bilateral-permits-required',
            ['rows' => $rows]
        );

        return $this->answersSummaryRowFactory->create(
            'permits.irhp.application.question.no-of-permits',
            $formattedAnswer,
            'no-of-permits'
        );
    }
}
