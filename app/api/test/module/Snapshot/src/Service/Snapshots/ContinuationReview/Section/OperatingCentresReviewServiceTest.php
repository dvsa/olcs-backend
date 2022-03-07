<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ContinuationReview\Section;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Section\OperatingCentresReviewService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * OperatingCentres review service test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class OperatingCentresReviewServiceTest extends MockeryTestCase
{
    /** @var OperatingCentresReviewService review service */
    protected $sut;

    public function setUp(): void
    {
        $this->sut = new OperatingCentresReviewService();
    }

    /**
     * @dataProvider dpGetConfigFromData
     */
    public function testGetConfigFromData($canHaveTrailer, $isVehicleTypeMixedWithLgv, $expected)
    {
        $continuationDetail = new ContinuationDetail();

        $licenceOperatingCentres = new ArrayCollection();

        $loc1 = m::mock()
            ->shouldReceive('getOperatingCentre')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getAddress')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getAddressLine1')
                            ->andReturn('Foo')
                            ->once()
                            ->shouldReceive('getTown')
                            ->andReturn('Bar')
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->getMock()
            )
            ->once()
            ->shouldReceive('getNoOfVehiclesRequired')
            ->andReturn(1)
            ->once()
            ->shouldReceive('getNoOfTrailersRequired')
            ->andReturn(2)
            ->withNoArgs()
            ->getMock();

        $loc2 = m::mock()
            ->shouldReceive('getOperatingCentre')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getAddress')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getAddressLine1')
                            ->andReturn('Baz')
                            ->once()
                            ->shouldReceive('getTown')
                            ->andReturn('Cake')
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->getMock()
            )
            ->once()
            ->shouldReceive('getNoOfVehiclesRequired')
            ->andReturn(3)
            ->once()
            ->shouldReceive('getNoOfTrailersRequired')
            ->andReturn(4)
            ->withNoArgs()
            ->getMock();

        $licenceOperatingCentres->add($loc1);
        $licenceOperatingCentres->add($loc2);

        $mockLicence = m::mock(Licence::class)
            ->shouldReceive('getOperatingCentres')
            ->andReturn($licenceOperatingCentres)
            ->once()
            ->shouldReceive('canHaveTrailer')
            ->andReturn($canHaveTrailer)
            ->withNoArgs()
            ->once()
            ->shouldReceive('isVehicleTypeMixedWithLgv')
            ->andReturn($isVehicleTypeMixedWithLgv)
            ->withNoArgs()
            ->once()
            ->getMock();

        $continuationDetail->setLicence($mockLicence);

        $this->assertEquals($expected, $this->sut->getConfigFromData($continuationDetail));
    }

    public function dpGetConfigFromData()
    {
        return [
            'licence cannot have trailers, vehicle type not mixed with lgv' => [
                false,
                false,
                [
                    [
                        ['value' => 'continuations.oc-section.table.name', 'header' => true],
                        ['value' => 'continuations.oc-section.table.vehicles', 'header' => true]
                    ],
                    [
                        ['value' => 'Baz, Cake'],
                        ['value' => '3']
                    ],
                    [
                        ['value' => 'Foo, Bar'],
                        ['value' => '1']
                    ]
                ]
            ],
            'licence cannot have trailers, vehicle type mixed with lgv' => [
                false,
                true,
                [
                    [
                        ['value' => 'continuations.oc-section.table.name', 'header' => true],
                        ['value' => 'continuations.oc-section.table.heavy-goods-vehicles', 'header' => true]
                    ],
                    [
                        ['value' => 'Baz, Cake'],
                        ['value' => '3']
                    ],
                    [
                        ['value' => 'Foo, Bar'],
                        ['value' => '1']
                    ]
                ]
            ],
            'licence can have trailers, vehicle type not mixed with lgv' => [
                true,
                false,
                [
                    [
                        ['value' => 'continuations.oc-section.table.name', 'header' => true],
                        ['value' => 'continuations.oc-section.table.vehicles', 'header' => true],
                        ['value' => 'continuations.oc-section.table.trailers', 'header' => true]
                    ],
                    [
                        ['value' => 'Baz, Cake'],
                        ['value' => '3'],
                        ['value' => '4']
                    ],
                    [
                        ['value' => 'Foo, Bar'],
                        ['value' => '1'],
                        ['value' => '2']
                    ]
                ]
            ],
            'licence can have trailers, vehicle type mixed with lgv' => [
                true,
                true,
                [
                    [
                        ['value' => 'continuations.oc-section.table.name', 'header' => true],
                        ['value' => 'continuations.oc-section.table.heavy-goods-vehicles', 'header' => true],
                        ['value' => 'continuations.oc-section.table.trailers', 'header' => true]
                    ],
                    [
                        ['value' => 'Baz, Cake'],
                        ['value' => '3'],
                        ['value' => '4']
                    ],
                    [
                        ['value' => 'Foo, Bar'],
                        ['value' => '1'],
                        ['value' => '2']
                    ]
                ]
            ],
        ];
    }

    /**
     * @dataProvider dpGetSummaryFromData
     */
    public function testGetSummaryFromData($applicableAuthProperties, $expected)
    {
        $continuationDetail = new ContinuationDetail();

        $mockLicence = m::mock(Licence::class)
            ->shouldReceive('getTotAuthVehicles')
            ->withNoArgs()
            ->andReturn(8)
            ->shouldReceive('getTotAuthHgvVehicles')
            ->withNoArgs()
            ->andReturn(5)
            ->shouldReceive('getTotAuthLgvVehicles')
            ->withNoArgs()
            ->andReturn(3)
            ->shouldReceive('getTotAuthTrailers')
            ->withNoArgs()
            ->andReturn(2)
            ->shouldReceive('getApplicableAuthProperties')
            ->withNoArgs()
            ->andReturn($applicableAuthProperties)
            ->getMock();

        $continuationDetail->setLicence($mockLicence);

        $this->assertEquals($expected, $this->sut->getSummaryFromData($continuationDetail));
    }

    public function dpGetSummaryFromData()
    {
        return [
            'vehicles and trailers' => [
                [
                    'totAuthVehicles',
                    'totAuthTrailers',
                ],
                [
                    [
                        ['value' => 'continuations.oc-section.table.vehicles', 'header' => true],
                        ['value' => 8]
                    ],
                    [
                        ['value' => 'continuations.oc-section.table.trailers', 'header' => true],
                        ['value' => 2]
                    ]
                ]
            ],
            'hgv, lgv and trailers' => [
                [
                    'totAuthHgvVehicles',
                    'totAuthLgvVehicles',
                    'totAuthTrailers',
                ],
                [
                    [
                        ['value' => 'continuations.oc-section.table.heavy-goods-vehicles', 'header' => true],
                        ['value' => 5]
                    ],
                    [
                        ['value' => 'continuations.oc-section.table.light-goods-vehicles', 'header' => true],
                        ['value' => 3]
                    ],
                    [
                        ['value' => 'continuations.oc-section.table.trailers', 'header' => true],
                        ['value' => 2]
                    ]
                ]
            ],
        ];
    }

    public function testGetSummaryHeader()
    {
        $this->assertEquals(
            'continuations.oc-section.table.authorisation',
            $this->sut->getSummaryHeader(new ContinuationDetail())
        );
    }
}
