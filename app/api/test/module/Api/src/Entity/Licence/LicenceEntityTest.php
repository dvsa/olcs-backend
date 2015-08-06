<?php

namespace Dvsa\OlcsTest\Api\Entity\Licence;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CaseEntity;
use Dvsa\Olcs\Api\Entity\Cases\Complaint as ComplaintEntity;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic as CommunityLicEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as Entity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\Organisation\TradingName as TradingNameEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Mockery as m;

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

    public function testCanBecomeSpecialRestrictedNull()
    {
        /** @var Entity $licence */
        $licence = m::mock(Entity::class)->makePartial();
        $licence->shouldReceive('getGoodsOrPsv')->andReturn(null);
        $licence->shouldReceive('getLicenceType')->andReturn(null);

        $this->assertEquals(true, $licence->canBecomeSpecialRestricted());
    }

    /**
     * @dataProvider licenceTypeProvider
     */
    public function testCanBecomeSpecialRestricted($goodsOrPsv, $licenceType, $expected)
    {
        /** @var Entity $licence */
        $licence = m::mock(Entity::class)->makePartial();
        $licence->shouldReceive('getGoodsOrPsv->getId')->andReturn($goodsOrPsv);
        $licence->shouldReceive('getLicenceType->getId')->andReturn($licenceType);

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

        $org = m::mock(OrganisationEntity::class)->makePartial();
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

    public function testIsGoods()
    {
        $goodsOrPsv = m::mock(RefData::class)->makePartial();
        $goodsOrPsv->setId(Entity::LICENCE_CATEGORY_GOODS_VEHICLE);

        /** @var Entity $licence */
        $licence = $this->instantiate(Entity::class);
        $licence->setGoodsOrPsv($goodsOrPsv);

        $this->assertTrue($licence->isGoods());
    }

    public function testIsGoodsFalse()
    {
        $goodsOrPsv = m::mock(RefData::class)->makePartial();
        $goodsOrPsv->setId(Entity::LICENCE_CATEGORY_PSV);

        /** @var Entity $licence */
        $licence = $this->instantiate(Entity::class);
        $licence->setGoodsOrPsv($goodsOrPsv);

        $this->assertFalse($licence->isGoods());
    }

    public function testIsPsv()
    {
        $goodsOrPsv = m::mock(RefData::class)->makePartial();
        $goodsOrPsv->setId(Entity::LICENCE_CATEGORY_PSV);

        /** @var Entity $licence */
        $licence = $this->instantiate(Entity::class);
        $licence->setGoodsOrPsv($goodsOrPsv);

        $this->assertTrue($licence->isPsv());
    }

    public function testIsPsvFalse()
    {
        $goodsOrPsv = m::mock(RefData::class)->makePartial();
        $goodsOrPsv->setId(Entity::LICENCE_CATEGORY_GOODS_VEHICLE);

        /** @var Entity $licence */
        $licence = $this->instantiate(Entity::class);
        $licence->setGoodsOrPsv($goodsOrPsv);

        $this->assertFalse($licence->isPsv());
    }

    public function testIsSpecialRestricted()
    {
        $licenceType = m::mock(RefData::class)->makePartial();
        $licenceType->setId(Entity::LICENCE_TYPE_SPECIAL_RESTRICTED);

        /** @var Entity $licence */
        $licence = $this->instantiate(Entity::class);
        $licence->setLicenceType($licenceType);

        $this->assertTrue($licence->isSpecialRestricted());
    }

    public function testIsSpecialRestrictedFalse()
    {
        $licenceType = m::mock(RefData::class)->makePartial();
        $licenceType->setId(Entity::LICENCE_TYPE_STANDARD_NATIONAL);

        /** @var Entity $licence */
        $licence = $this->instantiate(Entity::class);
        $licence->setLicenceType($licenceType);

        $this->assertFalse($licence->isSpecialRestricted());
    }

    public function testGetTradingNameNone()
    {
        $licence = m::mock(Entity::class)->makePartial();
        $licence
            ->shouldReceive('getOrganisation->getTradingNames->getIterator')
            ->once()
            ->andReturn([]);

        $this->assertEquals('None', $licence->getTradingName());
    }

    public function testGetTradingNamesSorting()
    {
        $tradingName1 = m::mock(TradingNameEntity::class)
            ->makePartial()
            ->setName('Foo')
            ->setCreatedOn('2015-06-01 00:00:00');
        $tradingName2 = m::mock(TradingNameEntity::class)
            ->makePartial()
            ->setName('Bar')
            ->setCreatedOn('2015-06-01 00:00:00');
        $tradingName3 = m::mock(TradingNameEntity::class)
            ->makePartial()
            ->setName('Baz')
            ->setCreatedOn('2015-07-01 00:00:00');
        $tradingNames = new ArrayCollection();
        $tradingNames->add($tradingName1);
        $tradingNames->add($tradingName2);
        $tradingNames->add($tradingName3);

        $licence = m::mock(Entity::class)->makePartial();
        $licence
            ->shouldReceive('getOrganisation->getTradingNames')
            ->once()
            ->andReturn($tradingNames);

        $this->assertEquals('Bar', $licence->getTradingName());
    }

    public function testGetOpenComplaintsCount()
    {
        $case1 = m::mock(CaseEntity::class)->makePartial();
        $case2 = m::mock(CaseEntity::class)->makePartial();
        $cases = new ArrayCollection();
        $cases->add($case1);
        $cases->add($case2);

        $case1
            ->shouldReceive('getComplaints')
            ->andReturn(
                [
                    m::mock(ComplaintEntity::class)
                        ->makePartial()
                        ->shouldReceive('getIsCompliance')
                        ->andReturn(0)
                        ->shouldReceive('isOpen')
                        ->andReturn(true)
                        ->getMock()
                ]
            );
        $case2
            ->shouldReceive('getComplaints')
            ->andReturn(
                [
                    m::mock(ComplaintEntity::class)
                        ->makePartial()
                        ->shouldReceive('getIsCompliance')
                        ->andReturn(1)
                        ->shouldReceive('isOpen')
                        ->andReturn(true)
                        ->getMock()
                ]
            );

        $licence = m::mock(Entity::class)->makePartial();
        $licence->setCases($cases);

        $this->assertEquals(1, $licence->getOpenComplaintsCount());
    }

    public function testGetOpenCases()
    {
        $case1 = m::mock(CaseEntity::class)->makePartial();
        $case2 = m::mock(CaseEntity::class)->makePartial();
        $cases = new ArrayCollection();
        $cases->add($case1);
        $cases->add($case2);

        $case1
            ->shouldReceive('isOpen')
            ->andReturn(true);
        $case2
            ->shouldReceive('isOpen')
            ->andReturn(false);

        $licence = m::mock(Entity::class)->makePartial();
        $licence->setCases($cases);

        $this->assertEquals([$case1], $licence->getOpenCases());
    }

    public function testGetOcForInspectionRequest()
    {
        $mockOperatingCentre = m::mock()
            ->shouldReceive('getOperatingCentre')
            ->andReturn('oc')
            ->once()
            ->getMock();

        $mockLicence = m::mock(Entity::class)->makePartial();
        $mockLicence->shouldReceive('getOperatingCentres')
            ->andReturn([$mockOperatingCentre])
            ->once()
            ->getMock();

        $this->assertEquals(['oc'], $mockLicence->getOcForInspectionRequest());
    }

    public function testIsRestricted()
    {
        /** @var Entity $licence */
        $licence = $this->instantiate(Entity::class);

        $licenceType = m::mock(RefData::class)->makePartial();
        $licenceType->setId(Entity::LICENCE_TYPE_STANDARD_NATIONAL);
        $licence->setLicenceType($licenceType);

        $this->assertFalse($licence->isRestricted());

        $licenceType = m::mock(RefData::class)->makePartial();
        $licenceType->setId(Entity::LICENCE_TYPE_RESTRICTED);
        $licence->setLicenceType($licenceType);

        $this->assertTrue($licence->isRestricted());
    }

    public function testIsStandardInternational()
    {
        /** @var Entity $licence */
        $licence = $this->instantiate(Entity::class);

        $licenceType = m::mock(RefData::class)->makePartial();
        $licenceType->setId(Entity::LICENCE_TYPE_STANDARD_NATIONAL);
        $licence->setLicenceType($licenceType);

        $this->assertFalse($licence->isStandardInternational());

        $licenceType = m::mock(RefData::class)->makePartial();
        $licenceType->setId(Entity::LICENCE_TYPE_STANDARD_INTERNATIONAL);
        $licence->setLicenceType($licenceType);

        $this->assertTrue($licence->isStandardInternational());
    }

    public function testIsStandardNational()
    {
        /** @var Entity $licence */
        $licence = $this->instantiate(Entity::class);

        $licenceType = m::mock(RefData::class)->makePartial();
        $licenceType->setId(Entity::LICENCE_TYPE_STANDARD_INTERNATIONAL);
        $licence->setLicenceType($licenceType);

        $this->assertFalse($licence->isStandardNational());

        $licenceType = m::mock(RefData::class)->makePartial();
        $licenceType->setId(Entity::LICENCE_TYPE_STANDARD_NATIONAL);
        $licence->setLicenceType($licenceType);

        $this->assertTrue($licence->isStandardNational());
    }

    public function testCanHaveCommunityLicencesStandardInt()
    {
        /** @var Entity $licence */
        $licence = $this->instantiate(Entity::class);

        $licenceType = m::mock(RefData::class)->makePartial();
        $licenceType->setId(Entity::LICENCE_TYPE_STANDARD_INTERNATIONAL);
        $licence->setLicenceType($licenceType);

        $this->assertTrue($licence->canHaveCommunityLicences());
    }

    public function testCanHaveCommunityLicencesPsvRestricted()
    {
        /** @var Entity $licence */
        $licence = $this->instantiate(Entity::class);

        $goodsOrPsv = m::mock(RefData::class)->makePartial();
        $goodsOrPsv->setId(Entity::LICENCE_CATEGORY_PSV);
        $licence->setGoodsOrPsv($goodsOrPsv);

        $licenceType = m::mock(RefData::class)->makePartial();
        $licenceType->setId(Entity::LICENCE_TYPE_RESTRICTED);
        $licence->setLicenceType($licenceType);

        $this->assertTrue($licence->canHaveCommunityLicences());
    }

    public function testCanHaveCommunityLicencesSomethingElse()
    {
        /** @var Entity $licence */
        $licence = $this->instantiate(Entity::class);

        $goodsOrPsv = m::mock(RefData::class)->makePartial();
        $goodsOrPsv->setId(Entity::LICENCE_CATEGORY_GOODS_VEHICLE);
        $licence->setGoodsOrPsv($goodsOrPsv);

        $licenceType = m::mock(RefData::class)->makePartial();
        $licenceType->setId(Entity::LICENCE_TYPE_STANDARD_NATIONAL);
        $licence->setLicenceType($licenceType);

        $this->assertFalse($licence->canHaveCommunityLicences());
    }

    public function testCopyInformationFromApplication()
    {
        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();

        $licenceType = m::mock(RefData::class)->makePartial();
        $licenceType->setId(Entity::LICENCE_TYPE_STANDARD_NATIONAL);
        $application->setLicenceType($licenceType);

        $goodsOrPsv = m::mock(RefData::class)->makePartial();
        $goodsOrPsv->setId(Entity::LICENCE_CATEGORY_GOODS_VEHICLE);
        $application->setGoodsOrPsv($goodsOrPsv);

        $application->setTotAuthTrailers(9);
        $application->setTotAuthVehicles(12);
        $application->setTotAuthSmallVehicles(4);
        $application->setTotAuthMediumVehicles(5);
        $application->setTotAuthLargeVehicles(3);
        $application->setNiFlag('Y');

        /** @var Entity $licence */
        $licence = $this->instantiate(Entity::class);

        $licence->copyInformationFromApplication($application);

        $this->assertSame($licenceType, $licence->getLicenceType());
        $this->assertSame($goodsOrPsv, $licence->getGoodsOrPsv());
        $this->assertEquals(9, $licence->getTotAuthTrailers());
        $this->assertEquals(12, $licence->getTotAuthVehicles());
        $this->assertEquals(4, $licence->getTotAuthSmallVehicles());
        $this->assertEquals(5, $licence->getTotAuthMediumVehicles());
        $this->assertEquals(3, $licence->getTotAuthLargeVehicles());
        $this->assertEquals('Y', $licence->getNiFlag());
    }

    public function testGetPsvDiscsNotCeased()
    {
        $psvDiscsCollection = m::mock(ArrayCollection::class);
        $psvDiscsNotCeasedCollection = m::mock(ArrayCollection::class);

        $psvDiscsCollection->shouldReceive('matching')->once()->with(m::type(Criteria::class))->andReturnUsing(
            function (Criteria $criteria) use ($psvDiscsNotCeasedCollection) {
                $expectedCriteria = Criteria::create()
                    ->where(Criteria::expr()->isNull('ceasedDate'));

                $this->assertEquals($expectedCriteria, $criteria);

                return $psvDiscsNotCeasedCollection;
            }
        );

        $licence = $this->instantiate(Entity::class);

        $licence->setPsvDiscs($psvDiscsCollection);
        $this->assertSame($psvDiscsNotCeasedCollection, $licence->getPsvDiscsNotCeased());
    }

    /**
     * @dataProvider canHaveLargeVehicleProvider
     */
    public function testCanHaveLargeVehicles($isPsv, $licenceType, $expected)
    {
        /** @var Entity $licence */
        $licence = m::mock(Entity::class)->makePartial();
        $licence->shouldReceive('isPsv')
            ->andReturn($isPsv);

        $licence->shouldReceive('getLicenceType->getId')
            ->andReturn($licenceType);

        $this->assertEquals($expected, $licence->canHaveLargeVehicles());
    }

    public function canHaveLargeVehicleProvider()
    {
        return [
            'PSV SN' => [
                true,
                Entity::LICENCE_TYPE_STANDARD_NATIONAL,
                true
            ],
            'PSV SI' => [
                true,
                Entity::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                true
            ],
            'GV SN' => [
                false,
                Entity::LICENCE_TYPE_STANDARD_NATIONAL,
                false
            ],
            'GV SI' => [
                false,
                Entity::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                false
            ],
            'PSV SR' => [
                true,
                Entity::LICENCE_TYPE_SPECIAL_RESTRICTED,
                false
            ],
            'PSV R' => [
                true,
                Entity::LICENCE_TYPE_RESTRICTED,
                false
            ],
        ];
    }

    /**
     * @dataProvider testCanHaveVariationProvider
     */
    public function testCanHaveVariation($status, $expected)
    {
        $licence = m::mock(Entity::class)->makePartial();

        $licence->shouldReceive('getStatus->getId')
            ->andReturn($status);

        $this->assertEquals($licence->canHaveVariation(), $expected);
    }

    public function testCanHaveVariationProvider()
    {
        return [
            [
                Entity::LICENCE_STATUS_VALID,
                true
            ],
            [
                Entity::LICENCE_STATUS_SURRENDERED,
                false
            ],
            [
                Entity::LICENCE_STATUS_REVOKED,
                false
            ],
            [
                Entity::LICENCE_STATUS_TERMINATED,
                false
            ]
        ];
    }

    /**
     * @param string $categoryId 'lcat_psv'|'lcat_gv'
     * @param string $expected 'O'|'P'
     * @dataProvider categoryPrefixDp
     */
    public function testGetCategoryPrefix($categoryId, $expected)
    {
        $category = new RefData($categoryId);

        $licence = $this->instantiate(Entity::class);
        $licence->setGoodsOrPsv($category);

        $this->assertEquals($expected, $licence->getCategoryPrefix());
    }

    public function categoryPrefixDp()
    {
        return [
            [Entity::LICENCE_CATEGORY_PSV, 'P'],
            [Entity::LICENCE_CATEGORY_GOODS_VEHICLE, 'O'],
        ];
    }
}
