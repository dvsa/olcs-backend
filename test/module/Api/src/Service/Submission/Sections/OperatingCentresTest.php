<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class OperatingCentresTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class OperatingCentresTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\OperatingCentres';

    /**
     * Filter provider
     *
     * @return array
     */
    public function sectionTestProvider()
    {
        $case = $this->getApplicationCase();

        $expectedResult = [
            'data' => [
                'tables' => [
                    'operating-centres' => [
                        0 => [
                            'id' => 1,
                            'version' => 1,
                            'totAuthVehicles' => 6,
                            'totAuthTrailers' => 4,
                            'OcAddress' => [
                                'addressLine1' => '1_a1',
                                'addressLine2' => '1_a2',
                                'addressLine3' => '1_a3',
                                'addressLine4' => null,
                                'town' => '1t',
                                'postcode' => 'pc11PC',
                                'countryCode' => null
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return [
            [$case, $expectedResult],
        ];
    }
}
