<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class TransportManagersTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class TransportManagersTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\TransportManagers';

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
                    'transport-managers' => [
                        0 => [
                            'id' => 153,
                            'version' => 306,
                            'licNo' => 'OB12345',
                            'tmType' => 'tm_type1-desc',
                            'title' => 'title-desc',
                            'forename' => 'fn22',
                            'familyName' => 'sn22',
                            'dob' => new \DateTime('1977-01-22'),
                            'qualifications' => [],
                            'otherLicences' => []
                        ],
                        1 => [
                            'id' => 153,
                            'version' => 306,
                            'licNo' => 'OB12345',
                            'tmType' => 'tm_type1-desc',
                            'title' => 'title-desc',
                            'forename' => 'fn22',
                            'familyName' => 'sn22',
                            'dob' => new \DateTime('1977-01-22'),
                            'qualifications' => [
                                0 => 'tm-qual-desc'
                            ],
                            'otherLicences' => [
                                0 => [
                                    'licNo' => '1-licNo',
                                    'applicationId' => 2255
                                ]
                            ]
                        ],
                        2 => [
                            'id' => 153,
                            'version' => 306,
                            'licNo' => 'OB12345',
                            'tmType' => 'tm_type1-desc',
                            'title' => 'title-desc',
                            'forename' => 'fn22',
                            'familyName' => 'sn22',
                            'dob' => new \DateTime('1977-01-22'),
                            'qualifications' => [
                                0 => 'tm-qual-desc'
                            ],
                            'otherLicences' => [
                                0 => [
                                    'licNo' => '1-licNo',
                                    'applicationId' => 2255
                                ]
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
