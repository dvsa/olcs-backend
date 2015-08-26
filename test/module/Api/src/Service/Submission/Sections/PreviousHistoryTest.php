<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

/**
 * Class PreviousHistoryTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class PreviousHistoryTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\PreviousHistory';

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
