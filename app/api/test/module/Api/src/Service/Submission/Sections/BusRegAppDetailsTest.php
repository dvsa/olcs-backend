<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

/**
 * Class BusRegAppDetailsTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class BusRegAppDetailsTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\BusRegAppDetails';

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
