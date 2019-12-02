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

    protected $expectedResult = [
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
        'totAuthorisedVehicles' => null,
        'totAuthorisedTrailers' => 5,
        'vehiclesInPossession' => 3,
        'trailersInPossession' => 5,
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
        $case = $this->getCase();

        $expectedResult = ['data' => ['overview' => $this->expectedResult]];

        return [
            [$case, $expectedResult],
        ];
    }

    protected function getCase()
    {
        $case = parent::getCase();

        $case->getLicence()->setInForceDate($this->licenceStartDate);

        return $case;
    }
}
