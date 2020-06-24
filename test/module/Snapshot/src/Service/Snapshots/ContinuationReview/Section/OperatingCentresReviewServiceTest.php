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

    public function testGetConfigFromData()
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
            ->once()
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
            ->once()
            ->getMock();

        $licenceOperatingCentres->add($loc1);
        $licenceOperatingCentres->add($loc2);

        $mockLicence = m::mock(Licence::class)
            ->shouldReceive('getOperatingCentres')
            ->andReturn($licenceOperatingCentres)
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
        ];

        $this->assertEquals($expected, $this->sut->getConfigFromData($continuationDetail));
    }

    public function testGetSummaryFromData()
    {
        $continuationDetail = new ContinuationDetail();

        $mockLicence = m::mock(Licence::class)
            ->shouldReceive('getTotAuthVehicles')
            ->andReturn(1)
            ->once()
            ->shouldReceive('getTotAuthTrailers')
            ->andReturn(2)
            ->once()
            ->getMock();

        $continuationDetail->setLicence($mockLicence);

        $expected = [
            [
                ['value' => 'continuations.oc-section.table.vehicles', 'header' => true],
                ['value' => 1]
            ],
            [
                ['value' => 'continuations.oc-section.table.trailers', 'header' => true],
                ['value' => 2]
            ]
        ];

        $this->assertEquals($expected, $this->sut->getSummaryFromData($continuationDetail));
    }

    public function testGetSummaryHeader()
    {
        $this->assertEquals(
            'continuations.oc-section.table.authorisation',
            $this->sut->getSummaryHeader(new ContinuationDetail())
        );
    }
}
