<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class OutstandingApplicationsTest
 * @to-do this currently doesnt really test anything because the criteria object on licence::getOutstandingApplications
 * seems to work for the application but not for unit testing.
 *
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class OutstandingApplicationsTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\OutstandingApplications';

    protected $expectedResult = [
        'data' => [
            'tables' => [
                'outstanding-applications' => [
                    0 => [

                        'id' => 1,
                        'version' => 2,
                        'applicationType' => 'TBC',
                        'receivedDate' => '05/05/2014',
                        'oor' => 'Unknown',
                        'ooo' => 'Unknown'
                    ],
                    1 => [

                        'id' => 101,
                        'version' => 202,
                        'applicationType' => 'TBC',
                        'receivedDate' => '05/05/2014',
                        'oor' => 'Unknown',
                        'ooo' => 'Unknown'
                    ],
                    2 => [

                        'id' => 2,
                        'version' => 4,
                        'applicationType' => 'TBC',
                        'receivedDate' => '05/05/2014',
                        'oor' => 'Unknown',
                        'ooo' => 'Unknown'
                    ],
                    3 => [

                        'id' => 102,
                        'version' => 204,
                        'applicationType' => 'TBC',
                        'receivedDate' => '05/05/2014',
                        'oor' => 'Unknown',
                        'ooo' => 'Unknown'
                    ]

                ]
            ]
        ]
    ];

    /**
     * Filter provider
     *
     * @return array
     */
    public function sectionTestProvider()
    {
        $case = $this->getCase();

        return [
            [$case, $this->expectedResult],
        ];
    }
}
