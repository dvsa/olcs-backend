<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class LinkedLicencesTest
 * @to-do this currently doesnt really test anything because the criteria object on licence::getLinkedLicences
 * seems to work for the application but not for unit testing.
 *
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class LinkedLicencesTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\LinkedLicences';

    protected $expectedResult = [
        'data' => [
            'tables' => [
                'linked-licences-app-numbers' => [

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
