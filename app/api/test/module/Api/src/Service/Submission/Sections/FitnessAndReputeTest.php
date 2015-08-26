<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

/**
 * Class FitnessAndReputeTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class FitnessAndReputeTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\FitnessAndRepute';

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
