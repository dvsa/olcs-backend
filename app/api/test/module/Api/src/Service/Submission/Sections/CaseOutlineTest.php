<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

/**
 * Class CaseOutlineTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class CaseOutlineTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\CaseOutline';

    /**
     * Filter provider
     *
     * @return array
     */
    public function sectionTestProvider()
    {
        $case = $this->getCase();

        $expectedResult = ['data' => ['text' => 'case description']];

        return [
            [$case, $expectedResult],
        ];
    }
}
