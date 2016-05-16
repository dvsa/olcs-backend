<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ConditionsAndUndertakingsTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class ConditionsAndUndertakingsTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\ConditionsAndUndertakings';

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
                'tables' => [
                    'conditions' => [
                        0 => [
                            'id' => 58,
                            'version' => 158,
                            'createdOn' => '23/01/2011',
                            'parentId' => 'OB12345',
                            'addedVia' => 'cav_lic-desc',
                            'isFulfilled' => 'Y',
                            'isDraft' => 'N',
                            'attachedTo' => 'cat_lic-desc',
                            'OcAddress' => [],
                            'notes' => null
                        ],
                        1 => [
                            'id' => 29,
                            'version' => 129,
                            'createdOn' => '23/01/2011',
                            'parentId' => 99,
                            'addedVia' => 'cav_case-desc',
                            'isFulfilled' => 'Y',
                            'isDraft' => 'N',
                            'attachedTo' => 'cat_lic-desc',
                            'OcAddress' => [],
                            'notes' => null
                        ]
                    ],
                    'undertakings' => [
                        0 => [
                            'id' => 34,
                            'version' => 134,
                            'createdOn' => '23/01/2011',
                            'parentId' => 777,
                            'addedVia' => 'cav_app-desc',
                            'isFulfilled' => 'Y',
                            'isDraft' => 'N',
                            'attachedTo' => 'cat_oc-desc',
                            'OcAddress' => [
                                'addressLine1' => '1_a1',
                                'addressLine2' => '1_a2',
                                'addressLine3' => '1_a3',
                                'addressLine4' => null,
                                'town' => '1t',
                                'postcode' => 'pc11PC',
                                'countryCode' => null
                            ],
                            'notes' => null
                        ],
                        1 => [
                            'id' => 34,
                            'version' => 134,
                            'createdOn' => '23/01/2011',
                            'parentId' => 75,
                            'addedVia' => 'cav_app-desc',
                            'isFulfilled' => 'Y',
                            'isDraft' => 'N',
                            'attachedTo' => 'cat_oc-desc',
                            'OcAddress' => [
                                'addressLine1' => '1_a1',
                                'addressLine2' => '1_a2',
                                'addressLine3' => '1_a3',
                                'addressLine4' => null,
                                'town' => '1t',
                                'postcode' => 'pc11PC',
                                'countryCode' => null
                            ],
                            'notes' => null
                        ],
                        2 => [
                            'id' => 34,
                            'version' => 134,
                            'createdOn' => '23/01/2011',
                            'parentId' => 75,
                            'addedVia' => 'cav_app-desc',
                            'isFulfilled' => 'Y',
                            'isDraft' => 'N',
                            'attachedTo' => 'cat_oc-desc',
                            'OcAddress' => [
                                'addressLine1' => '1_a1',
                                'addressLine2' => '1_a2',
                                'addressLine3' => '1_a3',
                                'addressLine4' => null,
                                'town' => '1t',
                                'postcode' => 'pc11PC',
                                'countryCode' => null
                            ],
                            'notes' => null
                        ],
                        3 => [
                            'id' => 34,
                            'version' => 134,
                            'createdOn' => '23/01/2011',
                            'parentId' => 75,
                            'addedVia' => 'cav_app-desc',
                            'isFulfilled' => 'Y',
                            'isDraft' => 'N',
                            'attachedTo' => 'cat_oc-desc',
                            'OcAddress' => [
                                'addressLine1' => '1_a1',
                                'addressLine2' => '1_a2',
                                'addressLine3' => '1_a3',
                                'addressLine4' => null,
                                'town' => '1t',
                                'postcode' => 'pc11PC',
                                'countryCode' => null
                            ],
                            'notes' => null
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
