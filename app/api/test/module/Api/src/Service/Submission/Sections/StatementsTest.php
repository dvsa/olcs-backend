<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class StatementsTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class StatementsTest extends AbstractSubmissionSectionTest
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
                            'requestedDate' => '11/08/2008',
                            'requestedBy' => [
                                'forename' => 'fn22',
                                'title' => 'title-desc',
                                'familyName' => 'sn22',
                                'birthDate' => '22/01/1977',
                                'birthPlace' => 'bp'
                            ],
                            'statementType' => 'statement_type1-desc',
                            'stoppedDate' => '26/03/2009',
                            'requestorsBody' => 'req body',
                            'issuedDate' => '30/03/2009',
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
