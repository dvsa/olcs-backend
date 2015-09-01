<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

/**
 * Class AnnualTestHistoryTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class AnnualTestHistoryTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\AnnualTestHistory';

    /**
     * Filter provider
     *
     * @return array
     */
    public function sectionTestProvider()
    {
        $case = $this->getCase();

        $expectedResult = ['data' => ['text' => 'ath']];

        return [
            [$case, $expectedResult],
        ];
    }
}
