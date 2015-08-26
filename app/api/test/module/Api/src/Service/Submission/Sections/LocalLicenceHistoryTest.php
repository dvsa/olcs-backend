<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

/**
 * Class LocalLicenceHistoryTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class LocalLicenceHistoryTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\LocalLicenceHistory';

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
