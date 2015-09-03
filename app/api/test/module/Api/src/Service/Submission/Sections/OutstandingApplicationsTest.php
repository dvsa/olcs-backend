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
