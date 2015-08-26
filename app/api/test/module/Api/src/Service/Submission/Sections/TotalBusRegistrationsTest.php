<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

/**
 * Class TotalBusRegistrationsTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class TotalBusRegistrationsTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\TotalBusRegistrations';

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
