<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

/**
 * Class CaseSummaryTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class CaseSummaryTest extends AbstractSubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\CaseSummary';

    protected $licenceStartDate = '2012-01-01 15:00:00';

    protected $baseExpectedResult = [
        'id' => 99,
        'caseType' => 'case type 1',
        'ecmsNo' => 'ecms1234',
        'organisationName' => 'Org name',
        'isMlh' => false,
        'organisationType' => 'org_type-desc',
        'businessType' => 'nob1',
        'licNo' => 'OB12345',
        'licenceStartDate' => '01/01/2012',
        'licenceType' => 'lic_type-desc',
        'goodsOrPsv' => 'goods-desc',
        'licenceStatus' => 'lic_status-desc',
        'vehiclesInPossession' => 3,
        'serviceStandardDate' => '',
        'disqualificationStatus' => 'None',
    ];

    /**
     * Filter provider
     *
     * @return array
     */
    public function sectionTestProvider()
    {
        /* test data and expected result for existing pre lgv scenario */

        $preLgvCase = $this->getCase();
        $preLgvCase->getLicence()->shouldReceive('getApplicableAuthProperties')
            ->withNoArgs()
            ->andReturn(['totAuthVehicles', 'totAuthTrailers']);

        $preLgvExpectedResult = [
            'data' => [
                'overview' => array_merge(
                    $this->baseExpectedResult,
                    [
                        'totAuthorisedVehicles' => null,
                        'totAuthorisedTrailers' => 5,
                        'trailersInPossession' => 5
                    ]
                )
            ]
        ];

        /* test data and expected result for mixed fleet with lgv */

        $mixedFleetCase = $this->getCase();
        $mixedFleetCase->getLicence()->shouldReceive('getApplicableAuthProperties')
            ->withNoArgs()
            ->andReturn(['totAuthHgvVehicles', 'totAuthLgvVehicles', 'totAuthTrailers']);
        $mixedFleetCase->getLicence()->setTotAuthHgvVehicles(7);
        $mixedFleetCase->getLicence()->setTotAuthLgvVehicles(3);

        $mixedFleetExpectedResult = [
            'data' => [
                'overview' => array_merge(
                    $this->baseExpectedResult,
                    [
                        'totAuthorisedHgvVehicles' => 7,
                        'totAuthorisedLgvVehicles' => 3,
                        'totAuthorisedTrailers' => 5,
                        'trailersInPossession' => 5
                    ]
                )
            ]
        ];

        /* test data and expected result for lgv only */

        $lgvOnlyCase = $this->getCase();
        $lgvOnlyCase->getLicence()->shouldReceive('getApplicableAuthProperties')
            ->withNoArgs()
            ->andReturn(['totAuthLgvVehicles']);
        $lgvOnlyCase->getLicence()->setTotAuthLgvVehicles(4);

        $lgvOnlyExpectedResult = [
            'data' => [
                'overview' => array_merge(
                    $this->baseExpectedResult,
                    [
                        'totAuthorisedLgvVehicles' => 4
                     ]
                )
            ]
        ];

        return [
            [$preLgvCase, $preLgvExpectedResult],
            [$mixedFleetCase, $mixedFleetExpectedResult],
            [$lgvOnlyCase, $lgvOnlyExpectedResult],
        ];
    }

    protected function getCase()
    {
        $case = parent::getCase();

        $case->getLicence()->setInForceDate($this->licenceStartDate);

        return $case;
    }
}
