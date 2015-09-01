<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class AuthRequestedAppliedForTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class AuthRequestedAppliedForTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\AuthRequestedAppliedFor';

    /**
     * Filter provider
     *
     * @return array
     */
    public function sectionTestProvider()
    {
        $case = $this->getCase();

        $expectedResult = [
            'data' => [
                'tables' => [
                    'auth-requested-applied-for' => [
                        0 => [
                            'id' => 75,
                            'version' => 150,
                            'currentVehiclesInPossession' => '0',
                            'currentTrailersInPossession' => '0',
                            'currentVehicleAuthorisation' => '0',
                            'currentTrailerAuthorisation' => '0',
                            'requestedVehicleAuthorisation' => '0',
                            'requestedTrailerAuthorisation' => '0',
                        ],
                        1 => [
                            'id' => 75,
                            'version' => 150,
                            'currentVehiclesInPossession' => '0',
                            'currentTrailersInPossession' => '0',
                            'currentVehicleAuthorisation' => '0',
                            'currentTrailerAuthorisation' => '0',
                            'requestedVehicleAuthorisation' => '0',
                            'requestedTrailerAuthorisation' => '0',
                        ],
                        2 => [
                            'id' => 75,
                            'version' => 150,
                            'currentVehiclesInPossession' => 3,
                            'currentTrailersInPossession' => 5,
                            'currentVehicleAuthorisation' => '0',
                            'currentTrailerAuthorisation' => 5,
                            'requestedVehicleAuthorisation' => '0',
                            'requestedTrailerAuthorisation' => '0',
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
