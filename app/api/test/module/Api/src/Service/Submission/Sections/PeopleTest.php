<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class PeopleTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class PeopleTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\People';

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
                    'people' => [
                        0 => [
                            'id' => 1,
                            'title' => 'title-desc',
                            'forename' => 'fn1',
                            'familyName' => 'sn1',
                            'birthDate' => '01/01/1977',
                            'disqualificationStatus' => 'None'
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
