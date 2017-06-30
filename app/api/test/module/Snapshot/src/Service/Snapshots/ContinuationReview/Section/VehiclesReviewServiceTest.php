<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ContinuationReview\Section;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Section\VehiclesReviewService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * Vehicles review service test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class VehiclesReviewServiceTest extends MockeryTestCase
{
    /** @var VehiclesReviewService review service */
    protected $sut;

    public function setUp()
    {
        $this->sut = new VehiclesReviewService();
    }

    public function testGetConfigFromData()
    {
        $continuationDetail = new ContinuationDetail();

        $licenceVehicles = new ArrayCollection();

        $licenceVehicle1 = m::mock()
            ->shouldReceive('getVehicle')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getVrm')
                    ->andReturn('VRM456')
                    ->once()
                    ->shouldReceive('getPlatedWeight')
                    ->andReturn(1000)
                    ->once()
                    ->getMock()
            )
            ->once()
            ->getMock();

        $licenceVehicle2 = m::mock()
            ->shouldReceive('getVehicle')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getVrm')
                    ->andReturn('VRM123')
                    ->once()
                    ->shouldReceive('getPlatedWeight')
                    ->andReturn(2000)
                    ->once()
                    ->getMock()
            )
            ->once()
            ->getMock();

        $licenceVehicles->add($licenceVehicle1);
        $licenceVehicles->add($licenceVehicle2);

        $mockLicence = m::mock(Licence::class)
            ->shouldReceive('getLicenceVehicles')
            ->andReturn($licenceVehicles)
            ->once()
            ->shouldReceive('getGoodsOrPsv')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(Licence::LICENCE_CATEGORY_GOODS_VEHICLE)
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $continuationDetail->setLicence($mockLicence);

        $expected =[
            [
                ['value' => 'continuations.vehicles-section.table.vrm', 'header' => true],
                ['value' => 'continuations.vehicles-section.table.weight', 'header' => true]
            ],
            [
                ['value' => 'VRM123'],
                ['value' => '2000']
            ],
            [
                ['value' => 'VRM456'],
                ['value' => '1000']
            ]
        ];

        $this->assertEquals($expected, $this->sut->getConfigFromData($continuationDetail));
    }
}
