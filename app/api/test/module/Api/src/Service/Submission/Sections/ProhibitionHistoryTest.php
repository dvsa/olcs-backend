<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ProhibitionHistoryTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class ProhibitionHistoryTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\ProhibitionHistory';

    /**
     * Filter provider
     *
     * @return array
     */
    public function sectionTestProvider()
    {
        $case = $this->getCase();

        $expectedResult = [
            'data' => [
                'text' => 'prohibition-note',
                'tables' => [
                    'prohibition-history' => [
                        0 => [
                            'id' => 1,
                            'version' => 6,
                            'prohibitionDate' => new \DateTime('2008-08-11'),
                            'clearedDate' => new \DateTime('2012-08-11'),
                            'prohibitionType' => 'prohibition-type1-desc',
                            'vehicle' => 'VR12 MAB',
                            'trailer' => false,
                            'imposedAt' => 'imposed-at'
                        ]
                    ]
                ]
            ]
        ];

        return [
            [$case, $expectedResult],
        ];
    }
}
