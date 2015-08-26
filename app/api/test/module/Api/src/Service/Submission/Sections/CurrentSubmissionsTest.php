<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

/**
 * Class CurrentSubmissionsTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class CurrentSubmissionsTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\CurrentSubmissions';

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
