<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Bilateral\Metadata;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata\CurrentFieldValuesGenerator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * CurrentFieldValuesGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class CurrentFieldValuesGeneratorTest extends MockeryTestCase
{
    private $irhpPermitApplication;

    private $irhpPermitStock;

    private $currentFieldValuesGenerator;

    public function setUp()
    {
        $this->irhpPermitApplication = m::mock(IrhpPermitApplication::class);

        $this->irhpPermitStock = m::mock(IrhpPermitStock::class);

        $this->currentFieldValuesGenerator = new CurrentFieldValuesGenerator();
    }

    /**
     * @dataProvider dpGenerate
     */
    public function testGenerate($bilateralRequired, $permitUsageSelection, $expected)
    {
        $this->irhpPermitStock->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn(42);

        $this->irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getId')
            ->withNoArgs()
            ->andReturn(42);
        $this->irhpPermitApplication->shouldReceive('getBilateralRequired')
            ->withNoArgs()
            ->andReturn($bilateralRequired);
        $this->irhpPermitApplication->shouldReceive('getBilateralPermitUsageSelection')
            ->withNoArgs()
            ->andReturn($permitUsageSelection);

        $this->assertEquals(
            $expected,
            $this->currentFieldValuesGenerator->generate($this->irhpPermitStock, $this->irhpPermitApplication)
        );
    }

    public function dpGenerate()
    {
        return [
            [
                [
                    IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 10,
                    IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 15
                ],
                RefData::JOURNEY_SINGLE,
                [
                    RefData::JOURNEY_SINGLE => [
                        IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 10,
                        IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 15
                    ],
                    RefData::JOURNEY_MULTIPLE => [
                        IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => null,
                        IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => null
                    ]
                ]
            ],
            [
                [
                    IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 20,
                    IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 25
                ],
                RefData::JOURNEY_MULTIPLE,
                [
                    RefData::JOURNEY_SINGLE => [
                        IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => null,
                        IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => null
                    ],
                    RefData::JOURNEY_MULTIPLE => [
                        IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 20,
                        IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 25
                    ]
                ]
            ]
        ];
    }

    public function testGenerateNoIrhpPermitApplication()
    {
        $expected = [
            RefData::JOURNEY_SINGLE => [
                IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => null,
                IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => null
            ],
            RefData::JOURNEY_MULTIPLE => [
                IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => null,
                IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => null
            ]
        ];

        $this->assertEquals(
            $expected,
            $this->currentFieldValuesGenerator->generate($this->irhpPermitStock, null)
        );
    }

    public function testGenerateNonMatchingIrhpPermitApplication()
    {
        $this->irhpPermitStock->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn(44);

        $this->irhpPermitApplication->shouldReceive('getIrhpPermitWindow->getIrhpPermitStock->getId')
            ->withNoArgs()
            ->andReturn(42);

        $expected = [
            RefData::JOURNEY_SINGLE => [
                IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => null,
                IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => null
            ],
            RefData::JOURNEY_MULTIPLE => [
                IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => null,
                IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => null
            ]
        ];

        $this->assertEquals(
            $expected,
            $this->currentFieldValuesGenerator->generate($this->irhpPermitStock, $this->irhpPermitApplication)
        );
    }
}
