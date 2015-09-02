<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class StatementsTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class StatementsTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\Statements';

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
                    'statements' => [
                        0 => [
                            'id' => 253,
                            'version' => 255,
                            'requestedDate' => new \DateTime('2008-08-11'),
                            'requestedBy' => [
                                'forename' => 'fn22',
                                'title' => 'title-desc',
                                'familyName' => 'sn22'
                            ],
                            'statementType' => 'statement_type1-desc',
                            'stoppedDate' => new \DateTime('2009-03-26'),
                            'requestorsBody' => 'req body',
                            'issuedDate' => new \DateTime('2009-03-30'),
                            'vrm' => 'VR12 MAB'
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
