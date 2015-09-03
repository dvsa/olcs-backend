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

    protected $expectedResult = [
        'id' => 66,
        'notificationNumber' => 'not no 123',
        'siCategory' => 'si_cat-desc',
        'siCategoryType' => 'si_cat_type-desc',
        'infringementDate' => '2014-05-05',
        'checkDate' => '2014-01-01',
        'isMemberState' => true
    ];

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
                            'birthDate' => new \DateTime('1977-01-1')
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
