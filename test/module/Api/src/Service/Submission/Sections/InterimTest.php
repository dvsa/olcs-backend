<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

/**
 * Class InterimTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class InterimTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\Interim';

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
