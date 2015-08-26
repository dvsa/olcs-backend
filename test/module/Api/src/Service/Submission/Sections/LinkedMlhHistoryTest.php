<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

/**
 * Class LinkedMlhHistoryTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class LinkedMlhHistoryTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\LinkedMlhHistory';

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
