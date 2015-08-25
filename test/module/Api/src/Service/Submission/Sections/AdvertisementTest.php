<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

/**
 * Class AdvertisementTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class AdvertisementTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\Advertisement';

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
