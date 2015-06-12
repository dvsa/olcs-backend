<?php

/**
 * Licence test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic as CommunityLicEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;

/**
 * Licence test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceTest extends RepositoryTestCase
{
    public function setUp()
    {
        $this->setUpSut(LicenceRepo::class);
    }

    public function testGetSerialNoPrefixFromTrafficArea()
    {
        $licenceId = 1;
        $mockLicence = m::mock()
            ->shouldReceive('getTrafficArea')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(TrafficAreaEntity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE)
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $sut = m::mock(LicenceRepo::class)
            ->makePartial()
            ->shouldReceive('fetchById')
            ->with($licenceId)
            ->andReturn($mockLicence)
            ->getMock();

        $this->assertEquals(CommunityLicEntity::PREFIX_NI, $sut->getSerialNoPrefixFromTrafficArea($licenceId));
    }
}
