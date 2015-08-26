<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

/**
 * Class RegistrationDetailsTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class RegistrationDetailsTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\RegistrationDetails';

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
