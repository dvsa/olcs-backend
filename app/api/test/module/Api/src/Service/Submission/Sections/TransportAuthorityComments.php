<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

/**
 * Class TransportAuthorityTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class TransportAuthorityTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\TransportAuthority';

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
