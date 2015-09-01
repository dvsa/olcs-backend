<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class OppositionsTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class OppositionsTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\Oppositions';

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
                    'oppositions' => [
                        0  => [
                            'id' => 253,
                            'version' => 255,
                            'dateReceived' => new \DateTime('2008-08-11'),
                            'oppositionType' => 'opposition_type253-desc',
                            'contactName' => [
                                'title' => 'title-desc',
                                'forename' => 'fn22',
                                'familyName' => 'sn22',
                                'birthDate' => new \DateTime('1977-01-22'),
                                'birthPlace' => 'bp'

                            ],
                            'grounds' => [
                                'g1-desc',
                                'g2-desc'
                            ],
                            'isValid' => 1,
                            'isCopied' => 1,
                            'isInTime' => 1,
                            'isPublicInquiry' => 0,
                            'isWithdrawn' => 0
                        ],
                        1 => [
                            'id' => 263,
                            'version' => 265,
                            'dateReceived' => new \DateTime('2008-08-11'),
                            'oppositionType' => 'opposition_type263-desc',
                            'contactName' => [
                                'title' => 'title-desc',
                                'forename' => 'fn22',
                                'familyName' => 'sn22',
                                'birthDate' => new \DateTime('1977-01-22'),
                                'birthPlace' => 'bp'
                            ],
                            'grounds' => [
                                'g1-desc',
                                'g2-desc'
                            ],
                            'isValid' => 1,
                            'isCopied' => 1,
                            'isInTime' => 1,
                            'isPublicInquiry' => 0,
                            'isWithdrawn' => 0
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
