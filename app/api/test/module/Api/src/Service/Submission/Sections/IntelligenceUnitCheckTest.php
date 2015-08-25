<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

/**
 * Class IntelligenceUnitCheckTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class IntelligenceUnitCheckTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\IntelligenceUnitCheck';

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
