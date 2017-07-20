<?php

namespace Dvsa\OlcsTest\Api\Entity\Licence;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as CollectionInterface;
use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CaseEntity;
use Dvsa\Olcs\Api\Entity\Cases\Complaint as ComplaintEntity;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic as CommunityLicEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as Entity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\Organisation\TradingName as TradingNameEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

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
        /** @var RefData $goodsOrPsv */
        $goodsOrPsv = m::mock(RefData::class)->makePartial();
        $goodsOrPsv->setId(Entity::LICENCE_CATEGORY_PSV);

        $licence1 = m::mock(Entity::class)->makePartial();

        $licences = m::mock(ArrayCollection::class)->makePartial();
        $licences->add($licence1);

        /** @var OrganisationEntity $org */
        $org = m::mock(OrganisationEntity::class)->makePartial();
        $org->setLicences($licences);

        /** @var RefData $type1 */
        $type1 = m::mock(RefData::class)->makePartial();
        $type1->setId(Entity::LICENCE_TYPE_SPECIAL_RESTRICTED);

        /** @var RefData $type2 */
        $type2 = m::mock(RefData::class)->makePartial();
        $type2->setId(Entity::LICENCE_TYPE_STANDARD_NATIONAL);

        /** @var Entity $otherLicence1 */
        $otherLicence1 = m::mock(Entity::class)->makePartial();
        $otherLicence1->setLicenceType($type1);

        $otherLicence2 = m::mock(Entity::class)->makePartial();
        $otherLicence2->setLicenceType($type2);

        $otherActiveLicences = m::mock(ArrayCollection::class)->makePartial();
        $otherActiveLicences->add($otherLicence1);
        $otherActiveLicences->add($otherLicence2);

        $licences->shouldReceive('matching')
            ->with(m::type(Criteria::class))
            ->andReturn($otherActiveLicences);

        $licence = $this->instantiate(Entity::class);
        $licence->setId(111);
        $licence->setGoodsOrPsv($goodsOrPsv);
        $licence->setOrganisation($org);

        $this->assertSame($otherActiveLicences, $licence->getOtherActiveLicences());
        $this->assertFalse($otherActiveLicences->contains($otherLicence1));
        $this->assertTrue($otherActiveLicences->contains($otherLicence2));
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
    public function testGetSerialNoPrefixFromTrafficArea($isNi, $expected)
    {
        $sut = m::mock(Entity::class)->makePartial();
        $sut->shouldReceive('getTrafficArea')
            ->andReturn(
                m::mock()
                ->shouldReceive('getIsNi')
                ->andReturn($isNi)
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
            [true, CommunityLicEntity::PREFIX_NI],
            [false, CommunityLicEntity::PREFIX_GB],
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

        $this->assertNull($licence->isGoods());

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

        $this->assertNull($licence->isPsv());

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

        $this->assertNull($licence->isSpecialRestricted());

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
            ->shouldReceive('getTradingNames->getIterator')
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
            ->shouldReceive('getTradingNames')
            ->once()
            ->andReturn($tradingNames);

        $this->assertEquals('Bar', $licence->getTradingName());
    }

    public function testGetAllTradingNames()
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
            ->shouldReceive('getTradingNames')
            ->once()
            ->andReturn($tradingNames);

        $this->assertEquals(['Bar', 'Foo', 'Baz'], $licence->getAllTradingNames());
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

        $this->assertNull($licence->isRestricted());

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

        $this->assertNull($licence->isStandardInternational());

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

        $this->assertNull($licence->isStandardNational());

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

    public function testCopyInformationFromNewApplication()
    {
        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();

        $licenceType = m::mock(RefData::class)->makePartial();
        $licenceType->setId(Entity::LICENCE_TYPE_STANDARD_NATIONAL);
        $application->setLicenceType($licenceType);
        $application->shouldReceive('isVariation')->once()->withNoArgs()->andReturn(false);

        $goodsOrPsv = m::mock(RefData::class)->makePartial();
        $goodsOrPsv->setId(Entity::LICENCE_CATEGORY_GOODS_VEHICLE);
        $application->setGoodsOrPsv($goodsOrPsv);

        $application->setTotAuthTrailers(9);
        $application->setTotAuthVehicles(12);

        /** @var Entity $licence */
        $licence = $this->instantiate(Entity::class);

        $licence->copyInformationFromApplication($application);

        $this->assertSame($licenceType, $licence->getLicenceType());
        $this->assertSame($goodsOrPsv, $licence->getGoodsOrPsv());
        $this->assertEquals(9, $licence->getTotAuthTrailers());
        $this->assertEquals(12, $licence->getTotAuthVehicles());
    }

    public function testCopyInformationFromVariationApplication()
    {
        $appCompletion = m::mock(ApplicationCompletion::class);
        $appCompletion->shouldReceive('variationSectionUpdated')->with('typeOfLicence')->once()->andReturn(true);
        $appCompletion->shouldReceive('variationSectionUpdated')->with('operatingCentres')->once()->andReturn(true);

        $licenceType = m::mock(RefData::class);
        $goodsOrPsv = m::mock(RefData::class);
        $totAuthTrailers = 9;
        $totAuthVehicles = 12;

        $application = m::mock(Application::class);
        $application->shouldReceive('isVariation')->once()->withNoArgs()->andReturn(true);
        $application->shouldReceive('getApplicationCompletion')->once()->withNoArgs()->andReturn($appCompletion);
        $application->shouldReceive('getLicenceType')->once()->withNoArgs()->andReturn($licenceType);
        $application->shouldReceive('getGoodsOrPsv')->once()->withNoArgs()->andReturn($goodsOrPsv);
        $application->shouldReceive('getTotAuthTrailers')->once()->withNoArgs()->andReturn($totAuthTrailers);
        $application->shouldReceive('getTotAuthVehicles')->once()->withNoArgs()->andReturn($totAuthVehicles);

        /** @var Entity $licence */
        $licence = $this->instantiate(Entity::class);

        $licence->copyInformationFromApplication($application);

        $this->assertSame($licenceType, $licence->getLicenceType());
        $this->assertSame($goodsOrPsv, $licence->getGoodsOrPsv());
        $this->assertEquals($totAuthTrailers, $licence->getTotAuthTrailers());
        $this->assertEquals($totAuthVehicles, $licence->getTotAuthVehicles());
    }

    public function testCopyInformationFromUnchangedVariationApplication()
    {
        $appCompletion = m::mock(ApplicationCompletion::class);
        $appCompletion->shouldReceive('variationSectionUpdated')->with('typeOfLicence')->once()->andReturn(false);
        $appCompletion->shouldReceive('variationSectionUpdated')->with('operatingCentres')->once()->andReturn(false);

        $goodsOrPsv = m::mock(RefData::class);

        $application = m::mock(Application::class);
        $application->shouldReceive('isVariation')->once()->withNoArgs()->andReturn(true);
        $application->shouldReceive('getApplicationCompletion')->once()->withNoArgs()->andReturn($appCompletion);
        $application->shouldReceive('getGoodsOrPsv')->once()->withNoArgs()->andReturn($goodsOrPsv);
        $application->shouldReceive('getLicenceType')->never();
        $application->shouldReceive('getTotAuthTrailers')->never();
        $application->shouldReceive('getTotAuthVehicles')->never();

        $originalLicenceType = m::mock(RefData::class);
        $originalTotAuthTrailers = 6;
        $originalTotAuthVehicles = 10;

        /** @var Entity $licence */
        $licence = $this->instantiate(Entity::class);
        $licence->setLicenceType($originalLicenceType);
        $licence->setTotAuthTrailers($originalTotAuthTrailers);
        $licence->setTotAuthVehicles($originalTotAuthVehicles);

        $licence->copyInformationFromApplication($application);

        //only goodsOrPsv should have changed
        $this->assertSame($originalLicenceType, $licence->getLicenceType());
        $this->assertSame($goodsOrPsv, $licence->getGoodsOrPsv());
        $this->assertEquals($originalTotAuthTrailers, $licence->getTotAuthTrailers());
        $this->assertEquals($originalTotAuthVehicles, $licence->getTotAuthVehicles());
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

    public function testGetRemainingSpacesPsv()
    {
        $licence = m::mock(Entity::class)->makePartial()
            ->shouldReceive('getTotAuthVehicles')
            ->andReturn(2)
            ->once()
            ->shouldReceive('getPsvDiscsNotCeased')
            ->andReturn(new \ArrayObject([1, 2, 3]))
            ->once()
            ->getMock();

        $this->assertEquals(-1, $licence->getRemainingSpacesPsv());
    }

    /**
     * @dataProvider dpTestGetLicenceTypeShortCode
     * @param string $licenceType
     * @param string $shortCode
     */
    public function testGetLicenceTypeShortCode($licenceType, $shortCode)
    {
        $licence = m::mock(Entity::class)->makePartial();
        $licence->setLicenceType((new RefData())->setId($licenceType));

        $this->assertSame($shortCode, $licence->getLicenceTypeShortCode());
    }

    public function dpTestGetLicenceTypeShortCode()
    {
        return [
            ['ltyp_r', 'R'],
            ['ltyp_si', 'SI'],
            ['ltyp_sn', 'SN'],
            ['ltyp_sr', 'SR'],
            ['ltyp_cbp', 'CBP'],
            ['ltyp_dbp', 'DBP'],
            ['ltyp_lbp', 'LBP'],
            ['ltyp_sbp', 'SBP'],
            ['XXXX', null],
        ];
    }

    public function testGetContextValue()
    {
        $entity = $this->instantiate(Entity::class);
        $entity->setLicNo(111);

        $this->assertEquals(111, $entity->getContextValue());
    }

    public function testGetVariations()
    {
        $licence = $this->instantiate(Entity::class);
        $app1 = new Application($licence, $this->getRefData(Application::APPLICATION_STATUS_UNDER_CONSIDERATION), 0);
        $app1->setIsVariation(true);
        $app1->setId(1);
        $app2 = new Application($licence, $this->getRefData(Application::APPLICATION_STATUS_UNDER_CONSIDERATION), 0);
        $app2->setIsVariation(false);
        $app2->setId(2);
        $licence->setApplications(new \Doctrine\Common\Collections\ArrayCollection([$app1, $app2]));

        $this->assertEquals(1, $licence->getVariations()[0]->getId());
    }

    public function testGetNewApplications()
    {
        $licence = $this->instantiate(Entity::class);
        $app1 = new Application($licence, $this->getRefData(Application::APPLICATION_STATUS_UNDER_CONSIDERATION), 0);
        $app1->setIsVariation(false);
        $app1->setId(1);
        $app2 = new Application($licence, $this->getRefData(Application::APPLICATION_STATUS_UNDER_CONSIDERATION), 0);
        $app2->setIsVariation(true);
        $app2->setId(2);
        $licence->setApplications(new \Doctrine\Common\Collections\ArrayCollection([$app1, $app2]));

        $this->assertEquals(1, $licence->getNewApplications()[0]->getId());
    }

    protected function getRefData($id)
    {
        $refData = new RefData();
        $refData->setId($id);

        return $refData;
    }

    public function testGetCalculatedBundleValues()
    {
        $licence = m::mock(Entity::class)->makePartial();
        $licence->shouldReceive('getNiFlag')->andReturn('Y');

        $result = $licence->getCalculatedBundleValues();

        $expected = [
            'niFlag' => 'Y'
        ];

        $this->assertSame($expected, $result);
    }

    public function testGetCalculatedValues()
    {
        $licence = m::mock(Entity::class)->makePartial();

        $this->assertSame($licence->getCalculatedBundleValues(), $licence->getCalculatedValues());
    }

    public function testGetApplicationDocuments()
    {
        $licence = m::mock(Entity::class)->makePartial();
        $mockDocument1 = m::mock()
            ->shouldReceive('getcategory')
            ->andReturn('category')
            ->once()
            ->shouldReceive('getsubCategory')
            ->andReturn('subCategory')
            ->once()
            ->getMock();

        $mockDocument2 = m::mock()
            ->shouldReceive('getcategory')
            ->andReturn('category1')
            ->once()
            ->shouldReceive('getsubCategory')
            ->andReturn('subCategory1')
            ->never()
            ->getMock();

        $documentsCollection = new ArrayCollection([$mockDocument1, $mockDocument2]);
        $expected = new ArrayCollection([$mockDocument1]);

        $licence->setDocuments($documentsCollection);
        $this->assertEquals($expected, $licence->getLicenceDocuments('category', 'subCategory'));
    }

    public function testGetOutstandingOrganisations()
    {
        $licence = m::mock(Entity::class)->makePartial();

        $allApplications = m::mock(ArrayCollection::class);
        $outstandingApplications = m::mock(ArrayCollection::class);
        $licence->setApplications($allApplications);

        $allApplications->shouldReceive('matching')
            ->once()
            ->with(m::type(Criteria::class))
            ->andReturn($outstandingApplications);

        $this->assertEquals($outstandingApplications, $licence->getOutstandingApplications(true));

    }

    /**
     * @dataProvider firstApplicationIdProvider
     */
    public function testGetFirstApplicationId($status, $firstApplicationId)
    {
        $licence = m::mock(Entity::class)->makePartial();
        $licence->shouldReceive('getStatus')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->once()
                ->andReturn($status)
                ->getMock()
            )
            ->once()
            ->getMock();
        $application = m::mock(Entity::class)->makePartial();
        $application->shouldReceive('getId')
            ->andReturn($firstApplicationId)
            ->getMock();

        $applications = new ArrayCollection();
        $applications->add($application);
        $licence->setApplications($applications);
        $this->assertEquals($firstApplicationId, $licence->getFirstApplicationId());
    }

    public function firstApplicationIdProvider()
    {
        return [
            [Entity::LICENCE_STATUS_NOT_SUBMITTED, 1],
            [Entity::LICENCE_STATUS_VALID, null],
        ];
    }

    public function testGetApplicationsByStatus()
    {
        $licence = m::mock(Entity::class)->makePartial();

        $allApplications = m::mock(ArrayCollection::class);
        $outstandingApplications = m::mock(ArrayCollection::class);
        $licence->setApplications($allApplications);

        $allApplications->shouldReceive('matching')
            ->once()
            ->with(m::type(Criteria::class))
            ->andReturn($outstandingApplications);

        $this->assertEquals($outstandingApplications, $licence->getApplicationsByStatus(['foo']));
    }

    public function testGetTrafficAreaForTaskAllocationNonMlh()
    {
        $licence = m::mock(Entity::class)->makePartial()
            ->shouldReceive('getOrganisation')
            ->andReturn(
                m::mock()
                    ->shouldReceive('isMlh')
                    ->andReturn(false)
                    ->once()
                    ->getMock()
            )
            ->once()
            ->shouldReceive('getTrafficArea')
            ->andReturn('B')
            ->once()
            ->shouldReceive('isGoods')
            ->andReturn(null)
            ->once()
            ->shouldReceive('isGoodsApplication')
            ->andReturn(true)
            ->once()
            ->getMock();

        $this->assertEquals('B', $licence->getTrafficAreaForTaskAllocation());
    }

    public function testGetTrafficAreaForTaskAllocationGoodsMlh()
    {
        $licence = m::mock(Entity::class)->makePartial()
            ->shouldReceive('getOrganisation')
            ->andReturn(
                m::mock()
                    ->shouldReceive('isMlh')
                    ->andReturn(true)
                    ->once()
                    ->shouldReceive('getLeadTcArea')
                    ->andReturn('B')
                    ->twice()
                    ->getMock()
            )
            ->once()
            ->shouldReceive('isGoods')
            ->andReturn(true)
            ->twice()
            ->getMock();

        $this->assertEquals('B', $licence->getTrafficAreaForTaskAllocation());
    }

    public function testGetTrafficAreaForTaskAllocationGoodsMlhLeadTcEmpty()
    {
        $licence = m::mock(Entity::class)->makePartial()
            ->shouldReceive('getOrganisation')
            ->andReturn(
                m::mock()
                    ->shouldReceive('isMlh')
                    ->andReturn(true)
                    ->once()
                    ->shouldReceive('getLeadTcArea')
                    ->andReturn(null)
                    ->once()
                    ->getMock()
            )
            ->once()
            ->shouldReceive('getTrafficArea')
            ->andReturn('B')
            ->once()
            ->shouldReceive('isGoods')
            ->andReturn(null)
            ->once()
            ->shouldReceive('isGoodsApplication')
            ->andReturn(true)
            ->once()
            ->getMock();

        $this->assertEquals('B', $licence->getTrafficAreaForTaskAllocation());
    }

    public function testIsGoodsApplication()
    {
        $application = m::mock()
            ->shouldReceive('isGoods')
            ->andReturn(true)
            ->getMock();

        $applications = new ArrayCollection();
        $applications->add($application);

        $licence = m::mock(Entity::class)->makePartial();
        $licence->shouldReceive('getStatus')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getId')
                    ->once()
                    ->andReturn(Entity::LICENCE_STATUS_NOT_SUBMITTED)
                    ->getMock()
            )
            ->once()
            ->shouldReceive('getApplications')
            ->andReturn($applications)
            ->once()
            ->getMock();

        $this->assertTrue($licence->isGoodsApplication());
    }

    /**
     * @dataProvider expiryDateProvider
     */
    public function testGetExpiryDateAsDate($expiryDate, $expected)
    {
        /** @var Entity $sut */
        $sut = m::mock(Entity::class)->makePartial()
            ->shouldReceive('getExpiryDate')
            ->andReturn($expiryDate)
            ->once()
            ->getMock();

        $this->assertEquals($sut->getExpiryDateAsDate(), $expected);
    }

    public function expiryDateProvider()
    {
        $today = new DateTime('now');
        $date2017 = \DateTime::createFromFormat('Y-m-d', '2017-01-01');
        $date2017->setTime(0, 0, 0);
        $date2017->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        return [
            [
                null,
                null
            ],
            [
                $today,
                $today
            ],
            [
                '2017-01-01',
                $date2017
            ]
        ];
    }

    /**
     * @dataProvider getLatestBusVariationProvider
     *
     * @param $isEmpty
     * @param $expectedResult
     * @param $expectedCriteria
     * @param $statuses
     */
    public function testGetLatestBusVariation($isEmpty, $expectedResult, $expectedCriteria, $statuses)
    {
        $busRegCollection = m::mock(CollectionInterface::class);
        $matchedCollection = m::mock(CollectionInterface::class);
        $matchedCollection->shouldReceive('isEmpty')->times()->andReturn($isEmpty);
        $matchedCollection->shouldReceive('current')->times($isEmpty ? 0 : 1)->andReturn($expectedResult);

        $busRegCollection->shouldReceive('matching')
            ->once()
            ->with(m::type(Criteria::class))->andReturnUsing(
                function (Criteria $criteria) use ($matchedCollection, $expectedCriteria) {
                    $this->assertEquals($expectedCriteria, $criteria);
                    return $matchedCollection;
                }
            );

        /** @var Entity|\Mockery\MockInterface $licence */
        $licence = m::mock(Entity::class)->makePartial();
        $licence->shouldReceive('getBusRegs')->andReturn($busRegCollection);
        $this->assertEquals($expectedResult, $licence->getLatestBusVariation('1234567', $statuses));
    }

    /**
     * Data provider for testGetLatestBusVariation
     *
     * @return array
     */
    public function getLatestBusVariationProvider()
    {
        $mockBusReg = m::mock(BusRegEntity::class);

        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('regNo', '1234567'))
            ->orderBy(array('variationNo' => Criteria::DESC))
            ->setMaxResults(1);

        $notInStatus = ['status'];
        $criteriaWithStatus = clone $criteria;
        $criteriaWithStatus->andWhere(Criteria::expr()->notIn('status', $notInStatus));

        return [
            [true, null, $criteria, []],
            [false, $mockBusReg, $criteriaWithStatus, $notInStatus],
            [true, null, $criteriaWithStatus, $notInStatus],
            [false, $mockBusReg, $criteria, []]
        ];
    }

    /**
     * @dataProvider operatorLocationProvider
     */
    public function testGetOperatorLocation($niFlag, $expected)
    {
        $mockLicence = m::mock(Entity::class)->makePartial();
        $mockLicence->shouldReceive('getNiFlag')
            ->andReturn($niFlag)
            ->once()
            ->getMock();

        $this->assertEquals($expected, $mockLicence->getOperatorLocation());
    }

    public function operatorLocationProvider()
    {
        return [
            [
                'Y', 'Northern Ireland'
            ],
            [
                'N', 'Great Britain'
            ]
        ];
    }

    public function testGetOperatorType()
    {
        $mockLicence1 = m::mock(Entity::class)->makePartial();
        $mockLicence1->shouldReceive('isGoods')
            ->andReturn(true)
            ->once()
            ->getMock();

        $mockLicence2 = m::mock(Entity::class)->makePartial();
        $mockLicence2->shouldReceive('isGoods')
            ->andReturn(false)
            ->once()
            ->getMock();

        $this->assertEquals('Goods', $mockLicence1->getOperatorType());
        $this->assertEquals('PSV', $mockLicence2->getOperatorType());
    }
}
