<?php

namespace Dvsa\OlcsTest\Api\Entity\Licence;

use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Mockery as m;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Licence\Licence as Entity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic as CommunityLicEntity;

/**
 * Licence Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class LicenceEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * @dataProvider licenceTypeProvider
     */
    public function testCanBecomeSpecialRestricted($goodsOrPsv, $licenceType, $expected)
    {
        /** @var Entity $licence */
        $licence = m::mock(Entity::class)->makePartial();

        $licence->shouldReceive('getGoodsOrPsv->getId')
            ->andReturn($goodsOrPsv);

        $licence->shouldReceive('getLicenceType->getId')
            ->andReturn($licenceType);

        $this->assertEquals($expected, $licence->canBecomeSpecialRestricted());
    }

    /**
     * @dataProvider updateSafetyDetails
     */
    public function testUpdateSafetyDetails(
        $safetyInsVehicles,
        $safetyInsTrailers,
        $tachographIns,
        $tachographInsName,
        $safetyInsVaries,
        $expectedException = null
    ) {
        /** @var Entity $licence */
        $licence = $this->instantiate(Entity::class);

        if ($expectedException !== null) {
            $this->setExpectedException($expectedException);
        }

        $licence->updateSafetyDetails(
            $safetyInsVehicles,
            $safetyInsTrailers,
            $tachographIns,
            $tachographInsName,
            $safetyInsVaries
        );

        if ($expectedException == null) {
            $this->assertEquals($safetyInsVehicles, $licence->getSafetyInsVehicles());
            $this->assertEquals($safetyInsTrailers, $licence->getSafetyInsTrailers());
            $this->assertEquals($tachographIns, $licence->getTachographIns());
            $this->assertEquals($tachographInsName, $licence->getTachographInsName());
            $this->assertEquals($safetyInsVaries, $licence->getSafetyInsVaries());
        }
    }

    public function updateSafetyDetails()
    {
        return [
            [
                2,
                1,
                'tach_external',
                'Some name',
                'Y',
                null
            ],
            [
                2,
                1,
                'tach_external',
                '',
                'Y',
                ValidationException::class
            ],
            [
                2,
                1,
                'tach_internal',
                '',
                'Y',
                ValidationException::class
            ],
            [
                2,
                1,
                'tach_na',
                '',
                'Y',
                null
            ]
        ];
    }

    public function licenceTypeProvider()
    {
        return [
            [
                Entity::LICENCE_CATEGORY_GOODS_VEHICLE,
                Entity::LICENCE_TYPE_STANDARD_NATIONAL,
                false
            ],
            [
                Entity::LICENCE_CATEGORY_PSV,
                Entity::LICENCE_TYPE_STANDARD_NATIONAL,
                false
            ],
            [
                Entity::LICENCE_CATEGORY_PSV,
                Entity::LICENCE_TYPE_RESTRICTED,
                false
            ],
            [
                Entity::LICENCE_CATEGORY_PSV,
                Entity::LICENCE_TYPE_SPECIAL_RESTRICTED,
                true
            ]
        ];
    }

    public function testUpdateTotalCommunityLicences()
    {
        $sut = m::mock(Entity::class)->makePartial();
        $sut->updateTotalCommunityLicences(10);
        $this->assertEquals(10, $sut->getTotCommunityLicences());
    }

    /**
     * @dataProvider trafficAreaProvider
     */
    public function testGetSerialNoPrefixFromTrafficArea($trafficArea, $expected)
    {
        $sut = m::mock(Entity::class)->makePartial();
        $sut->shouldReceive('getTrafficArea')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn($trafficArea)
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $this->assertEquals($expected, $sut->getSerialNoPrefixFromTrafficArea());
    }

    public function trafficAreaProvider()
    {
        return [
            [TrafficAreaEntity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE, CommunityLicEntity::PREFIX_NI],
            [TrafficAreaEntity::NORTH_WESTERN_TRAFFIC_AREA_CODE, CommunityLicEntity::PREFIX_GB],
        ];
    }
}
