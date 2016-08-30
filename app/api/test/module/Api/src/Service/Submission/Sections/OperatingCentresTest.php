<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @covers \Dvsa\Olcs\Api\Service\Submission\Sections\OperatingCentres
 */
class OperatingCentresTest extends SubmissionSectionTest
{
    protected $submissionSection = \Dvsa\Olcs\Api\Service\Submission\Sections\OperatingCentres::class;

    /**
     * Filter provider
     *
     * @return array
     */
    public function sectionTestProvider()
    {
        $case = $this->getApplicationCase();

        $case2 = $this->getApplicationCase();
        $oc = $case2->getLicence()->getOperatingCentres();
        $oc->current()->getOperatingCentre()->setAddress(null);

        return [
            [
                'input' => $case,
                'expectedResutl' => [
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
                                        'countryCode' => null,
                                    ],
                                ],
                                [
                                    'id' => 2,
                                    'version' => 2,
                                    'totAuthVehicles' => 6,
                                    'totAuthTrailers' => 4,
                                    'OcAddress' => [
                                        'addressLine1' => '2_a1',
                                        'addressLine2' => '2_a2',
                                        'addressLine3' => '2_a3',
                                        'addressLine4' => null,
                                        'town' => '2t',
                                        'postcode' => 'pc21PC',
                                        'countryCode' => null,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'input' => $case2,
                'expectedResutl' => [
                    'data' => [
                        'tables' => [
                            'operating-centres' => [
                                [
                                    'id' => 1,
                                    'version' => 1,
                                    'totAuthVehicles' => 6,
                                    'totAuthTrailers' => 4,
                                    'OcAddress' => [],
                                ],
                                [
                                    'id' => 2,
                                    'version' => 2,
                                    'totAuthVehicles' => 6,
                                    'totAuthTrailers' => 4,
                                    'OcAddress' => [
                                        'addressLine1' => '2_a1',
                                        'addressLine2' => '2_a2',
                                        'addressLine3' => '2_a3',
                                        'addressLine4' => null,
                                        'town' => '2t',
                                        'postcode' => 'pc21PC',
                                        'countryCode' => null,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
