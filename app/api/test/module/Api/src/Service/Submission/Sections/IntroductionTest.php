<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

/**
 * Class IntroductionTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class IntroductionTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\Introduction';

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
