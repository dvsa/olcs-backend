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

        $permitsRequiredFormattedAnswer = 'permits required line 1<br>permits required line 2';
        $permitsRequiredAnswersSummaryRow = m::mock(AnswersSummaryRow::class);

        $answersSummary = m::mock(AnswersSummary::class);
        $answersSummary->shouldReceive('addRow')
            ->with($countryNamesAnswersSummaryRow)
            ->once()
            ->ordered();
        $answersSummary->shouldReceive('addRow')
            ->with($permitsRequiredAnswersSummaryRow)
            ->once()
            ->ordered();

        $irhpPermitApplication1ValidityYear = 2019;
        $irhpPermitApplication1PermitsRequired = 8;
        $irhpPermitApplication1CountryName = 'Spain';
        $irhpPermitApplication1 = $this->createMockIrhpPermitApplication(
            $irhpPermitApplication1ValidityYear,
            $irhpPermitApplication1PermitsRequired,
            $irhpPermitApplication1CountryName
        );

        $irhpPermitApplication2ValidityYear = 2020;
        $irhpPermitApplication2PermitsRequired = 10;
        $irhpPermitApplication2CountryName = 'Spain';
        $irhpPermitApplication2 = $this->createMockIrhpPermitApplication(
            $irhpPermitApplication2ValidityYear,
            $irhpPermitApplication2PermitsRequired,
            $irhpPermitApplication2CountryName
        );

        $irhpPermitApplication3ValidityYear = 2019;
        $irhpPermitApplication3PermitsRequired = 12;
        $irhpPermitApplication3CountryName = 'Hungary';
        $irhpPermitApplication3 = $this->createMockIrhpPermitApplication(
            $irhpPermitApplication3ValidityYear,
            $irhpPermitApplication3PermitsRequired,
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
        $answersSummaryRowFactory->shouldReceive('create')
            ->with('permits.irhp.application.question.no-of-permits', $permitsRequiredFormattedAnswer, 'no-of-permits')
            ->once()
            ->andReturn($permitsRequiredAnswersSummaryRow);

        $expectedCountryNamesTemplateVariables = [
            'countryNames' => [
                $irhpPermitApplication1CountryName,
                $irhpPermitApplication3CountryName
            ]
        ];

        $expectedPermitsRequiredTemplateVariables = [
            'rows' => [
                [
                    'permitsRequired' => $irhpPermitApplication1PermitsRequired,
                    'countryName' => $irhpPermitApplication1CountryName,
                    'year' => $irhpPermitApplication1ValidityYear
                ],
                [
                    'permitsRequired' => $irhpPermitApplication2PermitsRequired,
                    'countryName' => $irhpPermitApplication2CountryName,
                    'year' => $irhpPermitApplication2ValidityYear
                ],
                [
                    'permitsRequired' => $irhpPermitApplication3PermitsRequired,
                    'countryName' => $irhpPermitApplication3CountryName,
                    'year' => $irhpPermitApplication3ValidityYear
                ],
            ]
        ];

        $viewRenderer = m::mock(RendererInterface::class);
        $viewRenderer->shouldReceive('render')
            ->with('answers-summary/bilateral-country-names', $expectedCountryNamesTemplateVariables)
            ->once()
            ->andReturn($countryNamesFormattedAnswer);
        $viewRenderer->shouldReceive('render')
            ->with('answers-summary/bilateral-permits-required', $expectedPermitsRequiredTemplateVariables)
            ->once()
            ->andReturn($permitsRequiredFormattedAnswer);

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

    private function createMockIrhpPermitApplication($validityYear, $permitsRequired, $countryName)
    {
        $irhpPermitStock = m::mock(IrhpPermitStockEntity::class);
        $irhpPermitStock->shouldReceive('getValidityYear')
            ->withNoArgs()
            ->andReturn($validityYear);

        $irhpPermitStock->shouldReceive('getCountry->getCountryDesc')
            ->withNoArgs()
            ->andReturn($countryName);

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock')
            ->withNoArgs()
            ->andReturn($irhpPermitStock);
        $irhpPermitApplication->shouldReceive('getPermitsRequired')
            ->withNoArgs()
            ->andReturn($permitsRequired);

        return $irhpPermitApplication;
    }
}
