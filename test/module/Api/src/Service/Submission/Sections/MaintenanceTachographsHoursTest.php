<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

/**
 * Class MaintenanceTachographsHoursTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class MaintenanceTachographsHoursTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\MaintenanceTachographsHours';

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
