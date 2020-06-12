<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Bilateral\Metadata;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationPathGroup;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata\CurrentFieldValuesGenerator;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata\FieldsGenerator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * FieldsGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class FieldsGeneratorTest extends MockeryTestCase
{
    /**
     * @dataProvider dpGenerate
     */
    public function testGenerate($applicationPathGroupId, $permitUsageList, $currentFieldValues, $expected)
    {
        $irhpPermitStock = m::mock(IrhpPermitStock::class);
        $irhpPermitStock->shouldReceive('getApplicationPathGroup->getId')
            ->withNoArgs()
            ->andReturn($applicationPathGroupId);
        $irhpPermitStock->shouldReceive('getPermitUsageList')
            ->withNoArgs()
            ->andReturn($permitUsageList);

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);

        $currentFieldValuesGenerator = m::mock(CurrentFieldValuesGenerator::class);
        $currentFieldValuesGenerator->shouldReceive('generate')
            ->with($irhpPermitStock, $irhpPermitApplication)
            ->andReturn($currentFieldValues);

        $fieldsGenerator = new FieldsGenerator($currentFieldValuesGenerator);

        $this->assertEquals(
            $expected,
            $fieldsGenerator->generate($irhpPermitStock, $irhpPermitApplication)
        );
    }

    public function dpGenerate()
    {
        $singlePermitUsage = m::mock(RefData::class);
        $singlePermitUsage->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn(RefData::JOURNEY_SINGLE);

        $multiplePermitUsage = m::mock(RefData::class);
        $multiplePermitUsage->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn(RefData::JOURNEY_MULTIPLE);

        $singleOnlyPermitUsageList = [$singlePermitUsage];
        $multipleOnlyPermitUsageList = [$multiplePermitUsage];
        $singleAndMultiplePermitUsageList = [$singlePermitUsage, $multiplePermitUsage];

        return [
            'standard only, single only' => [
                ApplicationPathGroup::BILATERALS_STANDARD_PERMITS_ONLY_ID,
                $singleOnlyPermitUsageList,
                [
                    RefData::JOURNEY_SINGLE => [
                        IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 5
                    ]
                ],
                [
                    [
                        'journey' => RefData::JOURNEY_SINGLE,
                        'cabotage' => IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED,
                        'value' => 5
                    ]
                ]
            ],
            'standard only, multiple only' => [
                ApplicationPathGroup::BILATERALS_STANDARD_PERMITS_ONLY_ID,
                $multipleOnlyPermitUsageList,
                [
                    RefData::JOURNEY_MULTIPLE => [
                        IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 6
                    ]
                ],
                [
                    [
                        'journey' => RefData::JOURNEY_MULTIPLE,
                        'cabotage' => IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED,
                        'value' => 6
                    ]
                ]
            ],
            'cabotage only, single only' => [
                ApplicationPathGroup::BILATERALS_CABOTAGE_PERMITS_ONLY_ID,
                $singleOnlyPermitUsageList,
                [
                    RefData::JOURNEY_SINGLE => [
                        IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 7
                    ]
                ],
                [
                    [
                        'journey' => RefData::JOURNEY_SINGLE,
                        'cabotage' => IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED,
                        'value' => 7
                    ]
                ]
            ],
            'cabotage only, multiple only' => [
                ApplicationPathGroup::BILATERALS_CABOTAGE_PERMITS_ONLY_ID,
                $multipleOnlyPermitUsageList,
                [
                    RefData::JOURNEY_MULTIPLE => [
                        IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 8
                    ]
                ],
                [
                    [
                        'journey' => RefData::JOURNEY_MULTIPLE,
                        'cabotage' => IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED,
                        'value' => 8
                    ]
                ]
            ],
            'standard only, single and multiple' => [
                ApplicationPathGroup::BILATERALS_STANDARD_PERMITS_ONLY_ID,
                $singleAndMultiplePermitUsageList,
                [
                    RefData::JOURNEY_SINGLE => [
                        IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 9
                    ],
                    RefData::JOURNEY_MULTIPLE => [
                        IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 10
                    ]
                ],
                [
                    [
                        'journey' => RefData::JOURNEY_SINGLE,
                        'cabotage' => IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED,
                        'value' => 9
                    ],
                    [
                        'journey' => RefData::JOURNEY_MULTIPLE,
                        'cabotage' => IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED,
                        'value' => 10
                    ]
                ]
            ],
            'cabotage only, single and multiple' => [
                ApplicationPathGroup::BILATERALS_CABOTAGE_PERMITS_ONLY_ID,
                $singleAndMultiplePermitUsageList,
                [
                    RefData::JOURNEY_SINGLE => [
                        IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 11
                    ],
                    RefData::JOURNEY_MULTIPLE => [
                        IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 12
                    ]
                ],
                [
                    [
                        'journey' => RefData::JOURNEY_SINGLE,
                        'cabotage' => IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED,
                        'value' => 11
                    ],
                    [
                        'journey' => RefData::JOURNEY_MULTIPLE,
                        'cabotage' => IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED,
                        'value' => 12
                    ]
                ]
            ],
            'standard and cabotage, single only' => [
                ApplicationPathGroup::BILATERALS_STANDARD_AND_CABOTAGE_PERMITS_ID,
                $singleOnlyPermitUsageList,
                [
                    RefData::JOURNEY_SINGLE => [
                        IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 13,
                        IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 14
                    ]
                ],
                [
                    [
                        'journey' => RefData::JOURNEY_SINGLE,
                        'cabotage' => IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED,
                        'value' => 13
                    ],
                    [
                        'journey' => RefData::JOURNEY_SINGLE,
                        'cabotage' => IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED,
                        'value' => 14
                    ]
                ]
            ],
            'standard and cabotage, multiple only' => [
                ApplicationPathGroup::BILATERALS_STANDARD_AND_CABOTAGE_PERMITS_ID,
                $multipleOnlyPermitUsageList,
                [
                    RefData::JOURNEY_MULTIPLE => [
                        IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 15,
                        IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 16
                    ]
                ],
                [
                    [
                        'journey' => RefData::JOURNEY_MULTIPLE,
                        'cabotage' => IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED,
                        'value' => 15
                    ],
                    [
                        'journey' => RefData::JOURNEY_MULTIPLE,
                        'cabotage' => IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED,
                        'value' => 16
                    ]
                ]
            ],
            'standard and cabotage, single and multiple' => [
                ApplicationPathGroup::BILATERALS_STANDARD_AND_CABOTAGE_PERMITS_ID,
                $singleAndMultiplePermitUsageList,
                [
                    RefData::JOURNEY_SINGLE => [
                        IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 17,
                        IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 18
                    ],
                    RefData::JOURNEY_MULTIPLE => [
                        IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 19,
                        IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 20
                    ]
                ],
                [
                    [
                        'journey' => RefData::JOURNEY_SINGLE,
                        'cabotage' => IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED,
                        'value' => 17
                    ],
                    [
                        'journey' => RefData::JOURNEY_MULTIPLE,
                        'cabotage' => IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED,
                        'value' => 19
                    ],
                    [
                        'journey' => RefData::JOURNEY_SINGLE,
                        'cabotage' => IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED,
                        'value' => 18
                    ],
                    [
                        'journey' => RefData::JOURNEY_MULTIPLE,
                        'cabotage' => IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED,
                        'value' => 20
                    ]
                ]
            ],
        ];
    }
}
