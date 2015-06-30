<?php

namespace Dvsa\OlcsTest\Api\Entity\Licence;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\System\RefData;
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

    public function testGetRemainingSpaces()
    {
        $lvCollection = m::mock(ArrayCollection::class);
        $activeCollection = m::mock(ArrayCollection::class);

        $lvCollection->shouldReceive('matching')
            ->once()
            ->with(m::type(Criteria::class))
            ->andReturn($activeCollection);

        $activeCollection->shouldReceive('count')
            ->andReturn(6);

        $licence = $this->instantiate(Entity::class);

        $licence->setTotAuthVehicles(10);
        $licence->setLicenceVehicles($lvCollection);

        $this->assertEquals(4, $licence->getRemainingSpaces());
    }

    public function testGetActiveVehiclesCount()
    {
        $lvCollection = m::mock(ArrayCollection::class);
        $activeCollection = m::mock(ArrayCollection::class);

        $lvCollection->shouldReceive('matching')
            ->once()
            ->with(m::type(Criteria::class))
            ->andReturn($activeCollection);

        $activeCollection->shouldReceive('count')
            ->andReturn(6);

        $licence = $this->instantiate(Entity::class);
        $licence->setLicenceVehicles($lvCollection);

        $this->assertEquals(6, $licence->getActiveVehiclesCount());
    }

    public function testGetActiveVehicles()
    {
        $lvCollection = m::mock(ArrayCollection::class);
        $activeCollection = m::mock(ArrayCollection::class);

        $lvCollection->shouldReceive('matching')
            ->once()
            ->with(m::type(Criteria::class))
            ->andReturn($activeCollection);

        $licence = $this->instantiate(Entity::class);
        $licence->setLicenceVehicles($lvCollection);

        $this->assertSame($activeCollection, $licence->getActiveVehicles());
    }

    public function testGetOtherActiveLicences()
    {
        $goodsOrPsv = m::mock(RefData::class)->makePartial();
        $goodsOrPsv->setId(Entity::LICENCE_CATEGORY_PSV);

        $licence1 = m::mock(Entity::class)->makePartial();

        $licences = m::mock(ArrayCollection::class)->makePartial();
        $licences->add($licence1);

        $org = m::mock(Organisation::class)->makePartial();
        $org->setLicences($licences);

        $licences->shouldReceive('matching')
            ->with(m::type(Criteria::class))
            ->andReturn(['RETURN']);

        $licence = $this->instantiate(Entity::class);
        $licence->setId(111);
        $licence->setGoodsOrPsv($goodsOrPsv);
        $licence->setOrganisation($org);

        $this->assertEquals(['RETURN'], $licence->getOtherActiveLicences());
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
                null
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

    public function testHasCommunityLicenceOfficeCopy()
    {
        $licence = m::mock(Entity::class)->makePartial();
        $licence->shouldReceive('getCommunityLics->matching')
            ->with(m::type(Criteria::class))
            ->andReturnUsing(
                function (Criteria $criteria) {

                    /** @var \Doctrine\Common\Collections\Expr\Comparison $expr */
                    $compositeExpression = $criteria->getWhereExpression();
                    $expressions = $compositeExpression->getExpressionList();

                    $this->assertEquals('issueNo', $expressions[0]->getField());
                    $this->assertEquals('=', $expressions[0]->getOperator());
                    $this->assertEquals(0, $expressions[0]->getValue()->getValue());

                    $this->assertEquals('status', $expressions[1]->getField());
                    $this->assertEquals('IN', $expressions[1]->getOperator());
                    $this->assertEquals(
                        [
                            CommunityLicEntity::STATUS_PENDING,
                            CommunityLicEntity::STATUS_ACTIVE,
                            CommunityLicEntity::STATUS_WITHDRAWN,
                            CommunityLicEntity::STATUS_SUSPENDED
                        ],
                        $expressions[1]->getValue()->getValue()
                    );

                    $mockCollection = m::mock()
                        ->shouldReceive('current')
                        ->andReturn(
                            m::mock()
                            ->shouldReceive('getId')
                            ->andReturn(1)
                            ->once()
                            ->getMock()
                        )
                        ->once()
                        ->getMock();

                    return $mockCollection;
                }
            );

        $this->assertTrue($licence->hasCommunityLicenceOfficeCopy([1]));
    }

    public function testHasApprovedUnfulfilledConditions()
    {
        /** @var Entity $licence */
        $licence = m::mock(Entity::class)->makePartial();
        $licence->shouldReceive('getConditionUndertakings->matching->count')
            ->andReturn(1);

        $this->assertTrue($licence->hasApprovedUnfulfilledConditions());
    }

    public function testHasApprovedUnfulfilledConditionsNone()
    {
        /** @var Entity $licence */
        $licence = m::mock(Entity::class)->makePartial();
        $licence->shouldReceive('getConditionUndertakings->matching->count')
            ->andReturn(0);

        $this->assertFalse($licence->hasApprovedUnfulfilledConditions());
    }
}
