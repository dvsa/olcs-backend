<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

/**
 * Class TransportAuthorityCommentsTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class TransportAuthorityCommentsTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\TransportAuthorityComments';

    /**
     * Filter provider
     *
     * @return array
     */
    public function sectionTestProvider()
    {
        $case = $this->getCase();

        $expectedResult = ['data' => []];

        return [
            [$case, $expectedResult],
        ];
    }
}
