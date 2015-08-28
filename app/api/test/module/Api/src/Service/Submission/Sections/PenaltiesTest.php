<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class PenaltiesTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class PenaltiesTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\Penalties';

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
                'overview' => [
                    'vrm' => 'erruVrm1',
                    'transportUndertakingName' => 'tun',
                    'originatingAuthority' => 'erru_oa',
                    'infringementId' => 734,
                    'notificationNumber' => 'notificationNo734',
                    'infringementDate' => new \DateTime('2009-11-30'),
                    'checkDate' =>  new \DateTime('2010-07-20'),
                    'category' => 'sicatdesc',
                    'categoryType' => 'sicattypedesc',
                    'memberState' => 'GB-desc'
                ],
                'text' => 'pen-notes1',
                'tables' => [
                    'applied-penalties' => [
                        0 => [
                            'id' => 1,
                            'version' => 6,
                            'penaltyType' => '533-desc',
                            'startDate' => new \DateTime('2013-07-01'),
                            'endDate' => new \DateTime('2013-08-31'),
                            'imposed' => 'imposed'
                        ]
                    ],
                    'imposed-penalties' => [
                        0 => [
                            'id' => 1,
                            'version' => 23,
                            'penaltyType' => '42-desc',
                            'finalDecisionDate' => new \DateTime('2014-12-31'),
                            'startDate' => new \DateTime('2014-07-01'),
                            'endDate' => new \DateTime('2014-08-31'),
                            'executed' => 'executed'
                        ]
                    ],
                    'requested-penalties' => [
                        0 => [
                            'id' => 1,
                            'version' => 34,
                            'penaltyType' => '952-desc',
                            'duration' => 'duration1'
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
