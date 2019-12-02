<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class LeadTcAreaTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class LeadTcAreaTest extends AbstractSubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\LeadTcArea';

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
                'text' => 'FOO'
            ]
        ];

        return [
            [$case, $expectedResult],
        ];
    }
}
