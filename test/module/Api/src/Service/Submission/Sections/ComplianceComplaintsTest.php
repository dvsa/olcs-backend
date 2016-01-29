<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ComplianceComplaintsTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class ComplianceComplaintsTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\ComplianceComplaints';

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
                    'compliance-complaints' => [
                        0 => [
                            'id' => 543,
                            'version' => 545,
                            'complainantForename' => 'fn22',
                            'complainantFamilyName' => 'sn22',
                            'description' => null,
                            'complaintDate' => '03/06/2006'
                        ],
                        1 => [
                            'id' => 253,
                            'version' => 255,
                            'complainantForename' => 'fn22',
                            'complainantFamilyName' => 'sn22',
                            'description' => null,
                            'complaintDate' => '03/06/2006'
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
