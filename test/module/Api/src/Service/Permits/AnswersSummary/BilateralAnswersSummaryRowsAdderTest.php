<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\AnswersSummary;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as IrhpPermitStockEntity;
use Dvsa\Olcs\Api\Service\Permits\AnswersSummary\AnswersSummary;
use Dvsa\Olcs\Api\Service\Permits\AnswersSummary\AnswersSummaryRow;
use Dvsa\Olcs\Api\Service\Permits\AnswersSummary\AnswersSummaryRowFactory;
use Dvsa\Olcs\Api\Service\Permits\AnswersSummary\BilateralAnswersSummaryRowsAdder;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\View\Renderer\RendererInterface;

/**
 * BilateralAnswersSummaryRowsAdderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class BilateralAnswersSummaryRowsAdderTest extends MockeryTestCase
{
    /**
     * @dataProvider dpSnapshot
     */
    public function testAddRows($isSnapshot)
    {
        $countryNamesFormattedAnswer = 'country names line 1<br>country names line 2';
        $countryNamesAnswersSummaryRow = m::mock(AnswersSummaryRow::class);

        $answersSummary = m::mock(AnswersSummary::class);
        $answersSummary->shouldReceive('addRow')
            ->with($countryNamesAnswersSummaryRow)
            ->once()
            ->ordered();

        $irhpPermitApplication1CountryName = 'Spain';
        $irhpPermitApplication1 = $this->createMockIrhpPermitApplication(
            $irhpPermitApplication1CountryName
        );

        $irhpPermitApplication2CountryName = 'Spain';
        $irhpPermitApplication2 = $this->createMockIrhpPermitApplication(
            $irhpPermitApplication2CountryName
        );

        $irhpPermitApplication3CountryName = 'Hungary';
        $irhpPermitApplication3 = $this->createMockIrhpPermitApplication(
            $irhpPermitApplication3CountryName
        );

        $irhpPermitApplications = [
            $irhpPermitApplication1,
            $irhpPermitApplication2,
            $irhpPermitApplication3
        ];

        $irhpApplication = m::mock(IrhpApplicationEntity::class);
        $irhpApplication->shouldReceive('getIrhpPermitApplications')
            ->withNoArgs()
            ->andReturn($irhpPermitApplications);

        $answersSummaryRowFactory = m::mock(AnswersSummaryRowFactory::class);
        $answersSummaryRowFactory->shouldReceive('create')
            ->with('permits.irhp.application.question.countries', $countryNamesFormattedAnswer, 'countries')
            ->once()
            ->andReturn($countryNamesAnswersSummaryRow);

        $expectedCountryNamesTemplateVariables = [
            'countryNames' => [
                $irhpPermitApplication1CountryName,
                $irhpPermitApplication3CountryName
            ]
        ];

        $viewRenderer = m::mock(RendererInterface::class);
        $viewRenderer->shouldReceive('render')
            ->with('answers-summary/bilateral-country-names', $expectedCountryNamesTemplateVariables)
            ->once()
            ->andReturn($countryNamesFormattedAnswer);

        $bilateralAnswersSummaryRowsAdder = new BilateralAnswersSummaryRowsAdder(
            $answersSummaryRowFactory,
            $viewRenderer
        );

        $bilateralAnswersSummaryRowsAdder->addRows($answersSummary, $irhpApplication, $isSnapshot);
    }

    public function dpSnapshot()
    {
        return [
            [true],
            [false]
        ];
    }

    private function createMockIrhpPermitApplication($countryName)
    {
        $irhpPermitStock = m::mock(IrhpPermitStockEntity::class);
        $irhpPermitStock->shouldReceive('getCountry->getCountryDesc')
            ->withNoArgs()
            ->andReturn($countryName);

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock')
            ->withNoArgs()
            ->andReturn($irhpPermitStock);

        return $irhpPermitApplication;
    }
}
