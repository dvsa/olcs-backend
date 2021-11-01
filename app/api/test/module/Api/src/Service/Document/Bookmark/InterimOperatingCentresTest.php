<?php
namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\InterimOperatingCentres;
use Dvsa\Olcs\Api\Service\Document\Parser\RtfParser;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking as ConditionUndertakingEntity;

/**
 * Interim Operating Centres test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class InterimOperatingCentresTest extends \PHPUnit\Framework\TestCase
{
    public function testGetQuery()
    {
        $bookmark = new InterimOperatingCentres();
        $query = $bookmark->getQuery(['application' => 7]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRenderWithNoData()
    {
        $parser = new RtfParser();
        $bookmark = new InterimOperatingCentres();
        $bookmark->setData([]);
        $bookmark->setParser($parser);

        $result = $bookmark->render();

        $this->assertEquals('', $result);
    }

    public function testRenderWithGoodsLicence()
    {
        $data = [
            'id' => 50,
            'goodsOrPsv' => [
                'id' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
            ],
            'isEligibleForLgv' => true,
            'licence' => [
                'id' => 100
            ],
            'operatingCentres' => [
                [
                    'noOfVehiclesRequired' => 10,
                    'noOfTrailersRequired' => 5,
                    'operatingCentre' => [
                        'address' => [
                            'addressLine1' => 'Address 1',
                            'addressLine2' => 'Address 2'
                        ],
                        'conditionUndertakings' => [
                            [
                                'id' => 10,
                                'conditionType' => [
                                    'id' => ConditionUndertakingEntity::TYPE_CONDITION
                                ],
                                'attachedTo' => [
                                    'id' => ConditionUndertakingEntity::ATTACHED_TO_OPERATING_CENTRE
                                ],
                                'isFulfilled' => 'N',
                                'isDraft' => 'N',
                                'notes' => 'condition 1',
                                'action' => 'A',
                                'application' => [
                                    'id' => 50
                                ]
                            ], [
                                'id' => 20,
                                'conditionType' => [
                                    'id' => ConditionUndertakingEntity::TYPE_UNDERTAKING
                                ],
                                'attachedTo' => [
                                    'id' => ConditionUndertakingEntity::ATTACHED_TO_OPERATING_CENTRE
                                ],
                                'isFulfilled' => 'N',
                                'isDraft' => 'N',
                                'notes' => 'undertaking 1',
                                'licence' => [
                                    'id' => 100
                                ]
                            ], [
                                'id' => 30,
                                'conditionType' => [
                                    'id' => ConditionUndertakingEntity::TYPE_UNDERTAKING
                                ],
                                'attachedTo' => [
                                    'id' => ConditionUndertakingEntity::ATTACHED_TO_OPERATING_CENTRE
                                ],
                                'isFulfilled' => 'N',
                                'isDraft' => 'N',
                                'notes' => 'undertaking 1 UPDATED',
                                'application' => [
                                    'id' => 50
                                ],
                                'action' => 'U',
                                'licConditionVariation' => [
                                    'id' => 20 // delta of 20
                                ]
                            ], [
                                'id' => 40,
                                'conditionType' => [
                                    'id' => ConditionUndertakingEntity::TYPE_UNDERTAKING
                                ],
                                'attachedTo' => [
                                    'id' => ConditionUndertakingEntity::ATTACHED_TO_OPERATING_CENTRE
                                ],
                                'isFulfilled' => 'N',
                                'isDraft' => 'N',
                                'notes' => 'undertaking 4',
                                'licence' => [
                                    'id' => 100
                                ]
                            ], [
                                'id' => 50,
                                'conditionType' => [
                                    'id' => ConditionUndertakingEntity::TYPE_UNDERTAKING
                                ],
                                'attachedTo' => [
                                    'id' => ConditionUndertakingEntity::ATTACHED_TO_OPERATING_CENTRE
                                ],
                                'isFulfilled' => 'N',
                                'isDraft' => 'N',
                                'notes' => 'undertaking 4',
                                'licence' => [
                                    'id' => 100
                                ],
                                'action' => 'D',
                                'licConditionVariation' => [
                                    'id' => 40
                                ]
                            ], [
                                'id' => 60,
                                'conditionType' => [
                                    'id' => ConditionUndertakingEntity::TYPE_UNDERTAKING
                                ],
                                'attachedTo' => [
                                    'id' => ConditionUndertakingEntity::ATTACHED_TO_OPERATING_CENTRE
                                ],
                                'isFulfilled' => 'N',
                                'isDraft' => 'N',
                                'notes' => 'undertaking 6',
                                'licence' => [
                                    'id' => 100
                                ]
                            ], [
                                'id' => 70,
                                'conditionType' => [
                                    'id' => ConditionUndertakingEntity::TYPE_UNDERTAKING
                                ],
                                'attachedTo' => [
                                    'id' => ConditionUndertakingEntity::ATTACHED_TO_OPERATING_CENTRE
                                ],
                                'isFulfilled' => 'Y',
                                'isDraft' => 'N',
                                'notes' => 'undertaking 4',
                                'licence' => [
                                    'id' => 100
                                ],
                                'action' => 'U',
                                'licConditionVariation' => [
                                    'id' => 60
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $parser = $this->createPartialMock('Dvsa\Olcs\Api\Service\Document\Parser\RtfParser', ['replace']);

        $conditionUndertakings = "Conditions\n\n1).\tcondition 1\n\n" .
            "Undertakings\n\n1).\tundertaking 1 UPDATED";

        $expectedRow = [
            'TAB_OC_ADD' => "Address 1\nAddress 2",
            'TAB_VEH' => 'Heavy Goods Vehicles',
            'TAB_OC_VEH' => 10,
            'TAB_TRAILER' => 'Trailers',
            'TAB_OC_TRAILER' => 5,
            'TAB_OC_CONDS_UNDERS' => $conditionUndertakings
        ];

        $parser->expects($this->at(0))
            ->method('replace')
            ->with('snippet', $expectedRow)
            ->willReturn('foo');

        $bookmark = $this->createPartialMock(InterimOperatingCentres::class, ['getSnippet']);

        $bookmark->expects($this->any())
            ->method('getSnippet')
            ->willReturn('snippet');

        $bookmark->setData($data);
        $bookmark->setParser($parser);

        $result = $bookmark->render();
        $this->assertEquals('foo', $result);
    }

    public function testRenderWithPsvLicence()
    {
        $data = [
            'id' => 123,
            'goodsOrPsv' => [
                'id' => Licence::LICENCE_CATEGORY_PSV,
            ],
            'isEligibleForLgv' => false,
            'licence' => [
                'id' => 100
            ],
            'operatingCentres' => [
                [
                    'noOfVehiclesRequired' => 10,
                    'operatingCentre' => [
                        'address' => [
                            'addressLine1' => 'Address 1',
                            'addressLine2' => 'Address 2'
                        ],
                        'conditionUndertakings' => []
                    ]
                ]
            ]
        ];

        $parser = $this->createPartialMock('Dvsa\Olcs\Api\Service\Document\Parser\RtfParser', ['replace']);

        $expectedRow = [
            'TAB_OC_ADD' => "Address 1\nAddress 2",
            'TAB_VEH' => 'Vehicles',
            'TAB_OC_VEH' => 10,
            'TAB_TRAILER' => '',
            'TAB_OC_TRAILER' => '',
            'TAB_OC_CONDS_UNDERS' => ''
        ];

        $parser->expects($this->at(0))
            ->method('replace')
            ->with('snippet', $expectedRow)
            ->willReturn('foo');

        $bookmark = $this->createPartialMock(InterimOperatingCentres::class, ['getSnippet']);

        $bookmark->expects($this->any())
            ->method('getSnippet')
            ->willReturn('snippet');

        $bookmark->setData($data);
        $bookmark->setParser($parser);

        $result = $bookmark->render();
        $this->assertEquals('foo', $result);
    }
}
