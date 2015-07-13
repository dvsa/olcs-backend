<?php

namespace Dvsa\OlcsTest\Api\Entity\Application;

use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Application\Application as Entity;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion as ApplicationCompletionEntity;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Mockery as m;

/**
 * Application Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class ApplicationEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * @var Entity
     */
    protected $entity;

    public function setUp()
    {
        $this->entity = $this->instantiate($this->entityClass);
    }

    public function dataProviderTestHasUpgrade()
    {
        return [
            [false, null, null],
            [false, Licence::LICENCE_TYPE_RESTRICTED, null],
            [false, null, Licence::LICENCE_TYPE_RESTRICTED],
            [false, Licence::LICENCE_TYPE_RESTRICTED, Licence::LICENCE_TYPE_RESTRICTED],
            [false, Licence::LICENCE_TYPE_STANDARD_NATIONAL, Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL],
            [false, Licence::LICENCE_TYPE_STANDARD_NATIONAL, Licence::LICENCE_TYPE_SPECIAL_RESTRICTED],
            [false, Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL, Licence::LICENCE_TYPE_SPECIAL_RESTRICTED],
            [true, Licence::LICENCE_TYPE_STANDARD_NATIONAL, Licence::LICENCE_TYPE_RESTRICTED],
            [true, Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL, Licence::LICENCE_TYPE_RESTRICTED],
            [true, Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL, Licence::LICENCE_TYPE_STANDARD_NATIONAL],
        ];
    }

    /**
     * @dataProvider dataProviderTestHasUpgrade
     */
    public function testHasUpgrade($expected, $applicationLicenceTypeId, $licenceTypeId)
    {
        $sut = m::mock(Entity::class)->makePartial();

        $mockApplicationLicenceType = m::mock();
        $sut->shouldReceive('getLicenceType')->with()->andReturn($mockApplicationLicenceType);
        $mockApplicationLicenceType->shouldReceive('getId')->with()->once()->andReturn($applicationLicenceTypeId);

        $mockLicence = m::mock();
        $sut->shouldReceive('getLicence')->with()->andReturn($mockLicence);
        $mockLicenceType = m::mock();
        $mockLicence->shouldReceive('getLicenceType')->with()->andReturn($mockLicenceType);
        $mockLicenceType->shouldReceive('getId')->with()->once()->andReturn($licenceTypeId);

        $this->assertSame($expected, $sut->hasUpgrade());
    }

    public function testHasUpgradeWithoutLicenceType()
    {
        $sut = m::mock(Entity::class)->makePartial();

        $sut->shouldReceive('getLicenceType')->with()->andReturn(null);

        $mockLicence = m::mock();
        $sut->shouldReceive('getLicence')->with()->andReturn($mockLicence);
        $mockLicence->shouldReceive('getLicenceType')->with()->andReturn(null);

        $this->assertFalse($sut->hasUpgrade());
    }

    public function dataProviderVehiclesIncreased()
    {
        return [
            [false, null, null],
            [false, null, 23],
            [true, 1, null],
            [false, 12, 22],
            [false, 4, 4],
            [true, 12, 11],
        ];
    }

    /**
     * @dataProvider dataProviderVehiclesIncreased
     */
    public function testHasAuthVehiclesIncrease($expected, $applicationCount, $licenceCount)
    {
        $sut = m::mock(Entity::class)->makePartial();

        $sut->shouldReceive('getTotAuthVehicles')->with()->andReturn($applicationCount);

        $mockLicence = m::mock();
        $sut->shouldReceive('getLicence')->with()->andReturn($mockLicence);
        $mockLicence->shouldReceive('getTotAuthVehicles')->with()->andReturn($licenceCount);

        $this->assertSame($expected, $sut->hasAuthVehiclesIncrease());
    }

    /**
     * @dataProvider dataProviderVehiclesIncreased
     */
    public function testHasAuthTrailersIncrease($expected, $applicationCount, $licenceCount)
    {
        $sut = m::mock(Entity::class)->makePartial();

        $sut->shouldReceive('getTotAuthTrailers')->with()->andReturn($applicationCount);

        $mockLicence = m::mock();
        $sut->shouldReceive('getLicence')->with()->andReturn($mockLicence);
        $mockLicence->shouldReceive('getTotAuthTrailers')->with()->andReturn($licenceCount);

        $this->assertSame($expected, $sut->hasAuthTrailersIncrease());
    }

    public function dataProviderTestHasNewOperatingCentre()
    {
        return [
            [false, ['D', 'D', 'D']],
            [false, ['D', 'U', 'D']],
            [false, ['U', 'U', 'U']],
            [true, ['D', 'A', 'D']],
            [true, ['A', 'U', 'U']],
            [true, ['A', 'A', 'A']],
        ];
    }

    /**
     * @group applicationEntity
     */
    public function testGetApplicationDocuments()
    {
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

        $this->entity->setDocuments($documentsCollection);
        $this->assertEquals($expected, $this->entity->getApplicationDocuments('category', 'subCategory'));
    }

    /**
     * @dataProvider notValidDataProvider
     * @group applicationEntity
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     */
    public function testUpdateFinancialHistoryNotValid(
        $bankrupt,
        $liquidation,
        $receivership,
        $administration,
        $disqualified,
        $insolvencyDetails,
        $insolvencyConfirmation
    ) {

        $this->entity->updateFinancialHistory(
            $bankrupt,
            $liquidation,
            $receivership,
            $administration,
            $disqualified,
            $insolvencyDetails,
            $insolvencyConfirmation
        );
    }

    public function notValidDataProvider()
    {
        return [
            ['Y', 'N', 'N', 'N', 'N', '123', '1'],
            ['Y', 'N', 'N', 'N', 'N', '', '1'],
        ];
    }

    /**
     * @dataProvider dataProviderTestHasNewOperatingCentre
     *
     * @param bool  $expected
     * @param array $operatingCenterActions
     */
    public function testHasNewOperatingCentre($expected, $operatingCenterActions)
    {
        $sut = m::mock(Entity::class)->makePartial();

        foreach ($operatingCenterActions as $action) {
            $applicationOperatingCentre = new \Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre();
            $applicationOperatingCentre->setAction($action);
            $centres[] = $applicationOperatingCentre;
        }

        $sut->shouldReceive('getOperatingCentres')->with()->andReturn($centres);

        $this->assertSame($expected, $sut->hasNewOperatingCentre());
    }


    public function setupOperatingCentres()
    {
        $operatingCentreValues = [
            [2, 6, 9],
            [3, 4, 4],
            [433, 5, 2],
        ];

        foreach ($operatingCentreValues as $values) {
            list($id, $noOfTrailersRequired, $noOfVehiclesRequired) = $values;
            $oc = new \Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre();
            $oc->setId($id);
            $aoc = new \Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre();
            $aoc->setOperatingCentre($oc);
            $aoc->setNoOfTrailersRequired($noOfTrailersRequired);
            $aoc->setNoOfVehiclesRequired($noOfVehiclesRequired);
            $centres[] = $aoc;
        }

        return $centres;
    }

    public function testHasIncreaseInOperatingCentreNoChanges()
    {
        $sut = m::mock(Entity::class)->makePartial();

        $applicationCentres = $this->setupOperatingCentres();
        $sut->shouldReceive('getOperatingCentres')->with()->andReturn($applicationCentres);
        $licenceCentres = $this->setupOperatingCentres();
        $sut->shouldReceive('getLicence->getOperatingCentres')->with()->andReturn($licenceCentres);

        $this->assertSame(false, $sut->hasIncreaseInOperatingCentre());
    }

    public function testHasIncreaseInOperatingCentreVehiclesIncreased()
    {
        $sut = m::mock(Entity::class)->makePartial();

        $applicationCentres = $this->setupOperatingCentres();
        $applicationCentres[1]->setNoOfVehiclesRequired(123);
        $sut->shouldReceive('getOperatingCentres')->with()->andReturn($applicationCentres);
        $licenceCentres = $this->setupOperatingCentres();
        $sut->shouldReceive('getLicence->getOperatingCentres')->with()->andReturn($licenceCentres);

        $this->assertSame(true, $sut->hasIncreaseInOperatingCentre());
    }

    public function testHasIncreaseInOperatingCentreTrailersIncreased()
    {
        $sut = m::mock(Entity::class)->makePartial();

        $applicationCentres = $this->setupOperatingCentres();
        $applicationCentres[1]->setNoOfTrailersRequired(123);
        $sut->shouldReceive('getOperatingCentres')->with()->andReturn($applicationCentres);
        $licenceCentres = $this->setupOperatingCentres();
        $sut->shouldReceive('getLicence->getOperatingCentres')->with()->andReturn($licenceCentres);

        $this->assertSame(true, $sut->hasIncreaseInOperatingCentre());
    }

    public function testHasIncreaseInOperatingCentreCentreDeleted()
    {
        $sut = m::mock(Entity::class)->makePartial();

        $applicationCentres = $this->setupOperatingCentres();
        $applicationCentres[1]->setAction('D');
        $applicationCentres[1]->setNoOfTrailersRequired(12121);
        $sut->shouldReceive('getOperatingCentres')->with()->andReturn($applicationCentres);
        $licenceCentres = $this->setupOperatingCentres();
        $sut->shouldReceive('getLicence->getOperatingCentres')->with()->andReturn($licenceCentres);

        $this->assertSame(false, $sut->hasIncreaseInOperatingCentre());
    }

    public function testHasIncreaseInOperatingCentreNoOperatingCentreChanges()
    {
        $sut = m::mock(Entity::class)->makePartial();

        $applicationCentres = [];
        $sut->shouldReceive('getOperatingCentres')->with()->andReturn($applicationCentres);
        $licenceCentres = $this->setupOperatingCentres();
        $sut->shouldReceive('getLicence->getOperatingCentres')->with()->andReturn($licenceCentres);

        $this->assertSame(false, $sut->hasIncreaseInOperatingCentre());
    }

    public function testCanHaveInterimLicencePsv()
    {
        $sut = m::mock(Entity::class)->makePartial();

        $sut->shouldReceive('getGoodsOrPsv->getId')->with()->once()->andReturn(Licence::LICENCE_CATEGORY_PSV);

        $this->assertFalse($sut->canHaveInterimLicence());
    }

    public function testCanHaveInterimLicenceApplication()
    {
        $sut = m::mock(Entity::class)->makePartial();

        $sut->shouldReceive('getGoodsOrPsv->getId')->with()->once()->andReturn(Licence::LICENCE_CATEGORY_GOODS_VEHICLE);
        $sut->shouldReceive('getIsVariation')->with()->once()->andReturn(false);

        $this->assertTrue($sut->canHaveInterimLicence());
    }

    public function testCanHaveInterimLicence1()
    {
        $sut = m::mock(Entity::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $sut->shouldReceive('getGoodsOrPsv->getId')->with()->once()->andReturn(Licence::LICENCE_CATEGORY_GOODS_VEHICLE);
        $sut->shouldReceive('getIsVariation')->with()->once()->andReturn(true);

        $sut->shouldReceive('hasAuthVehiclesIncrease')->with()->once()->andReturn(true);

        $this->assertSame(true, $sut->canHaveInterimLicence());
    }

    public function testCanHaveInterimLicence2()
    {
        $sut = m::mock(Entity::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $sut->shouldReceive('getGoodsOrPsv->getId')->with()->once()->andReturn(Licence::LICENCE_CATEGORY_GOODS_VEHICLE);
        $sut->shouldReceive('getIsVariation')->with()->once()->andReturn(true);

        $sut->shouldReceive('hasAuthVehiclesIncrease')->with()->once()->andReturn(false);
        $sut->shouldReceive('hasAuthTrailersIncrease')->with()->once()->andReturn(true);

        $this->assertSame(true, $sut->canHaveInterimLicence());
    }

    public function testCanHaveInterimLicence3()
    {
        $sut = m::mock(Entity::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $sut->shouldReceive('getGoodsOrPsv->getId')->with()->once()->andReturn(Licence::LICENCE_CATEGORY_GOODS_VEHICLE);
        $sut->shouldReceive('getIsVariation')->with()->once()->andReturn(true);

        $sut->shouldReceive('hasAuthVehiclesIncrease')->with()->once()->andReturn(false);
        $sut->shouldReceive('hasAuthTrailersIncrease')->with()->once()->andReturn(false);
        $sut->shouldReceive('hasUpgrade')->with()->once()->andReturn(true);

        $this->assertSame(true, $sut->canHaveInterimLicence());
    }

    public function testCanHaveInterimLicence4()
    {
        $sut = m::mock(Entity::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $sut->shouldReceive('getGoodsOrPsv->getId')->with()->once()->andReturn(Licence::LICENCE_CATEGORY_GOODS_VEHICLE);
        $sut->shouldReceive('getIsVariation')->with()->once()->andReturn(true);

        $sut->shouldReceive('hasAuthVehiclesIncrease')->with()->once()->andReturn(false);
        $sut->shouldReceive('hasAuthTrailersIncrease')->with()->once()->andReturn(false);
        $sut->shouldReceive('hasUpgrade')->with()->once()->andReturn(false);
        $sut->shouldReceive('hasNewOperatingCentre')->with()->once()->andReturn(true);

        $this->assertSame(true, $sut->canHaveInterimLicence());
    }

    public function testCanHaveInterimLicence5()
    {
        $sut = m::mock(Entity::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $sut->shouldReceive('getGoodsOrPsv->getId')->with()->once()->andReturn(Licence::LICENCE_CATEGORY_GOODS_VEHICLE);
        $sut->shouldReceive('getIsVariation')->with()->once()->andReturn(true);

        $sut->shouldReceive('hasAuthVehiclesIncrease')->with()->once()->andReturn(false);
        $sut->shouldReceive('hasAuthTrailersIncrease')->with()->once()->andReturn(false);
        $sut->shouldReceive('hasUpgrade')->with()->once()->andReturn(false);
        $sut->shouldReceive('hasNewOperatingCentre')->with()->once()->andReturn(false);
        $sut->shouldReceive('hasIncreaseInOperatingCentre')->with()->once()->andReturn(true);

        $this->assertSame(true, $sut->canHaveInterimLicence());
    }

    public function testCanHaveInterimLicence6()
    {
        $sut = m::mock(Entity::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $sut->shouldReceive('getGoodsOrPsv->getId')->with()->once()->andReturn(Licence::LICENCE_CATEGORY_GOODS_VEHICLE);
        $sut->shouldReceive('getIsVariation')->with()->once()->andReturn(true);

        $sut->shouldReceive('hasAuthVehiclesIncrease')->with()->once()->andReturn(false);
        $sut->shouldReceive('hasAuthTrailersIncrease')->with()->once()->andReturn(false);
        $sut->shouldReceive('hasUpgrade')->with()->once()->andReturn(false);
        $sut->shouldReceive('hasNewOperatingCentre')->with()->once()->andReturn(false);
        $sut->shouldReceive('hasIncreaseInOperatingCentre')->with()->once()->andReturn(false);

        $this->assertSame(false, $sut->canHaveInterimLicence());
    }

    public function testIsLicenceUpgradeApplication()
    {
        $sut = m::mock(Entity::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $sut->shouldReceive('getIsVariation')->with()->once()->andReturn(false);

        $this->assertSame(false, $sut->isLicenceUpgrade());
    }

    public function dataProviderTestIsLicenceUpgrade()
    {
        return [
            [false, Licence::LICENCE_TYPE_RESTRICTED, Licence::LICENCE_TYPE_RESTRICTED],
            [false, Licence::LICENCE_TYPE_RESTRICTED, Licence::LICENCE_TYPE_SPECIAL_RESTRICTED],
            [true, Licence::LICENCE_TYPE_RESTRICTED, Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL],
            [true, Licence::LICENCE_TYPE_RESTRICTED, Licence::LICENCE_TYPE_STANDARD_NATIONAL],
        ];
    }

    /**
     * @dataProvider dataProviderTestIsLicenceUpgrade
     *
     * @param bool   $expected
     * @param string $licenceType
     * @param string $applicationLicenceType
     */
    public function testIsLicenceUpgrade($expected, $licenceType, $applicationLicenceType)
    {
        $sut = m::mock(Entity::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $sut->shouldReceive('getIsVariation')->with()->once()->andReturn(true);

        $sut->shouldReceive('getLicence->getLicenceType->getId')->with()->once()->andReturn($licenceType);
        $sut->shouldReceive('getLicenceType->getId')->with()->once()->andReturn($applicationLicenceType);

        $this->assertSame($expected, $sut->isLicenceUpgrade());
    }

    /**
     * @dataProvider validDataProvider
     * @group applicationEntity
     */
    public function testUpdateFinancialHistoryValid(
        $bankrupt,
        $liquidation,
        $receivership,
        $administration,
        $disqualified,
        $insolvencyDetails,
        $insolvencyConfirmation
    ) {

        $this->assertTrue(
            $this->entity->updateFinancialHistory(
                $bankrupt,
                $liquidation,
                $receivership,
                $administration,
                $disqualified,
                $insolvencyDetails,
                $insolvencyConfirmation
            )
        );
        $this->assertEquals($this->entity->getBankrupt(), $bankrupt);
        $this->assertEquals($this->entity->getLiquidation(), $liquidation);
        $this->assertEquals($this->entity->getReceivership(), $receivership);
        $this->assertEquals($this->entity->getAdministration(), $administration);
        $this->assertEquals($this->entity->getDisqualified(), $disqualified);
        $this->assertEquals($this->entity->getInsolvencyDetails(), $insolvencyDetails);
        $this->assertEquals($this->entity->getInsolvencyConfirmation(), 'Y');
    }

    public function validDataProvider()
    {
        return [
            ['N', 'N', 'N', 'N', 'N', '', '1'],
            ['Y', 'N', 'N', 'N', 'N', str_repeat('X', 200), '1'],
        ];
    }

    /**
     * @group applicationEntity
     */
    public function testGetOtherLicencesByType()
    {
        $mockOtherLicence1 = m::mock()
            ->shouldReceive('getpreviousLicenceType')
            ->andReturn('type')
            ->once()
            ->getMock();

        $mockOtherLicence2 = m::mock()
            ->shouldReceive('getpreviousLicenceType')
            ->andReturn('type1')
            ->once()
            ->getMock();

        $otherLicencesCollection = new ArrayCollection([$mockOtherLicence1, $mockOtherLicence2]);
        $expected = new ArrayCollection([$mockOtherLicence1]);

        $this->entity->setOtherLicences($otherLicencesCollection);
        $this->assertEquals($expected, $this->entity->getOtherLicencesByType('type'));
    }

    public function testUpdateLicenceHistory()
    {
        $this->entity->updateLicenceHistory('Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y');
        $this->assertEquals($this->entity->getPrevHasLicence(), 'Y');
        $this->assertEquals($this->entity->getPrevHadLicence(), 'Y');
        $this->assertEquals($this->entity->getPrevBeenRefused(), 'Y');
        $this->assertEquals($this->entity->getPrevBeenRevoked(), 'Y');
        $this->assertEquals($this->entity->getPrevBeenAtPi(), 'Y');
        $this->assertEquals($this->entity->getPrevBeenDisqualifiedTc(), 'Y');
        $this->assertEquals($this->entity->getPrevPurchasedAssets(), 'Y');
    }

    /**
     * @dataProvider codeProvider
     */
    public function testGetCode($isVariation, $isUpgrade, $goodsOrPsv, $licenceType, $expected)
    {
        $sut = m::mock(Entity::class)->makePartial();

        $sut->shouldReceive('getIsVariation')->andReturn($isVariation);
        $sut->shouldReceive('getGoodsOrPsv->getId')->andReturn($goodsOrPsv);
        $sut->shouldReceive('getLicenceType->getId')->andReturn($licenceType);
        $sut->shouldReceive('isLicenceUpgrade')->andReturn($isUpgrade);

        $this->assertEquals($expected, $sut->getCode());
    }

    public function codeProvider()
    {
        return [
            'gv new app' => [
                false,
                null,
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                'GV79'
            ],
            'psv new app' => [
                false,
                null,
                Licence::LICENCE_CATEGORY_PSV,
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                'PSV421'
            ],
            'psv sr new app' => [
                false,
                null,
                Licence::LICENCE_CATEGORY_PSV,
                Licence::LICENCE_TYPE_SPECIAL_RESTRICTED,
                'PSV356'
            ],
            'gv variation upgrade' => [
                true,
                true,
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                'GV80A'
            ],
            'gv variation no upgrade' => [
                true,
                false,
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                'GV81'
            ],
            'psv variation upgrade' => [
                true,
                true,
                Licence::LICENCE_CATEGORY_PSV,
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                'PSV431A'
            ],
            'psv variation no upgrade' => [
                true,
                false,
                Licence::LICENCE_CATEGORY_PSV,
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                'PSV431'
            ],
        ];
    }

    public function testGetApplicationType()
    {
        $sut = m::mock(Entity::class)->makePartial();

        $sut->setIsVariation(true);
        $this->assertEquals(Entity::APPLICATION_TYPE_VARIATION, $sut->getApplicationType());

        $sut->setIsVariation(false);
        $this->assertEquals(Entity::APPLICATION_TYPE_NEW, $sut->getApplicationType());
    }

    /**
     * @dataProvider canSubmitProvider
     */
    public function testCanSubmit($status, $expected)
    {
        $sut = m::mock(Entity::class)->makePartial();

        $sut->shouldReceive('getStatus->getId')->once()->andReturn($status);
        $this->assertEquals($expected, $sut->canSubmit());
    }

    public function canSubmitProvider()
    {
        return [
            [Entity::APPLICATION_STATUS_NOT_SUBMITTED, true],
            [Entity::APPLICATION_STATUS_GRANTED, false],
            [Entity::APPLICATION_STATUS_UNDER_CONSIDERATION, false],
            [Entity::APPLICATION_STATUS_VALID, false],
            [Entity::APPLICATION_STATUS_WITHDRAWN, false],
            [Entity::APPLICATION_STATUS_REFUSED, false],
            [Entity::APPLICATION_STATUS_NOT_TAKEN_UP, false],
        ];
    }

    /**
     * @dataProvider canCreateCaseProvider
     */
    public function testCanCreateCase($status, $licNo, $expected)
    {
        $sut = m::mock(Entity::class)->makePartial();

        $sut->shouldReceive('getStatus->getId')->once()->andReturn($status);
        $sut->shouldReceive('getLicence->getLicNo')->andReturn($licNo);
        $this->assertEquals($expected, $sut->canCreateCase());
    }

    public function canCreateCaseProvider()
    {
        $licNo = 12345;

        return [
            [Entity::APPLICATION_STATUS_NOT_SUBMITTED, null, false],
            [Entity::APPLICATION_STATUS_GRANTED, null, false],
            [Entity::APPLICATION_STATUS_UNDER_CONSIDERATION, null, false],
            [Entity::APPLICATION_STATUS_VALID, null, false],
            [Entity::APPLICATION_STATUS_WITHDRAWN, null, false],
            [Entity::APPLICATION_STATUS_REFUSED, null, false],
            [Entity::APPLICATION_STATUS_NOT_TAKEN_UP, null, false],
            [Entity::APPLICATION_STATUS_NOT_SUBMITTED, $licNo, false],
            [Entity::APPLICATION_STATUS_GRANTED, $licNo, true],
            [Entity::APPLICATION_STATUS_UNDER_CONSIDERATION, $licNo, true],
            [Entity::APPLICATION_STATUS_VALID, $licNo, true],
            [Entity::APPLICATION_STATUS_WITHDRAWN, $licNo, true],
            [Entity::APPLICATION_STATUS_REFUSED, $licNo, true],
            [Entity::APPLICATION_STATUS_NOT_TAKEN_UP, $licNo, true],
        ];
    }

    /**
     * @dataProvider goodsOrPsvHelperProvider
     */
    public function testIsGoodsAndIsPsvHelperMethods($goodsOrPsv, $isGoods, $isPsv)
    {
        $sut = m::mock(Entity::class)->makePartial();

        $sut->shouldReceive('getGoodsOrPsv->getId')->andReturn($goodsOrPsv);

        $this->assertEquals($isGoods, $sut->isGoods());
        $this->assertEquals($isPsv, $sut->isPsv());
    }

    public function goodsOrPsvHelperProvider()
    {
        return [
            [Licence::LICENCE_CATEGORY_PSV, false, true],
            [Licence::LICENCE_CATEGORY_GOODS_VEHICLE, true, false],
        ];
    }

    public function testIsGoodsAndIsPsvHelperNull()
    {
        $sut = m::mock(Entity::class)->makePartial();
        $this->assertNull($sut->isGoods());
        $this->assertNull($sut->isPsv());
    }

    public function testIsSpecialRestrictedLicenceTypeNull()
    {
        $sut = m::mock(Entity::class)->makePartial();
        $this->assertNull($sut->isSpecialRestricted());
    }

    /**
     * @dataProvider applicationDateProvider
     */
    public function testGetApplicationDate($createdOn, $receivedDate, $expected)
    {
        $sut = m::mock(Entity::class)->makePartial();

        $sut->shouldReceive('getCreatedOn')->andReturn($createdOn);
        $sut->shouldReceive('getReceivedDate')->andReturn($receivedDate);

        $this->assertEquals($expected, $sut->getApplicationDate());
    }

    public function applicationDateProvider()
    {
        return [
            ['2015-06-19', '2015-06-22', '2015-06-22'],
            ['2015-06-19', null, '2015-06-19'],
            [null, '2015-06-22', '2015-06-22'],
            [null, null, null],
        ];

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

        $licence = m::mock(Licence::class)->makePartial();
        $licence->setLicenceVehicles($lvCollection);

        $application = $this->instantiate(Entity::class);
        $application->setTotAuthVehicles(10);
        $application->setLicence($licence);

        $this->assertEquals(4, $application->getRemainingSpaces());
    }

    public function testIsRealUpgradeNewApp()
    {
        /** @var Entity $application */
        $application = $this->instantiate(Entity::class);

        $application->setIsVariation(false);

        $this->assertFalse($application->isRealUpgrade());
    }

    public function testIsRealUpgradeIsLicenceUpgrade()
    {
        /** @var RefData $licenceType */
        $licenceType = m::mock(RefData::class)->makePartial();
        $licenceType->setId(Licence::LICENCE_TYPE_STANDARD_NATIONAL);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getLicenceType->getId')
            ->andReturn(Licence::LICENCE_TYPE_RESTRICTED);

        /** @var Entity $application */
        $application = $this->instantiate(Entity::class);
        $application->setIsVariation(true);
        $application->setLicence($licence);
        $application->setLicenceType($licenceType);

        $this->assertTrue($application->isRealUpgrade());
    }

    public function testIsRealUpgrade()
    {
        /** @var RefData $licenceType */
        $licenceType = m::mock(RefData::class)->makePartial();
        $licenceType->setId(Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getLicenceType->getId')
            ->andReturn(Licence::LICENCE_TYPE_STANDARD_NATIONAL);

        /** @var Entity $application */
        $application = $this->instantiate(Entity::class);
        $application->setIsVariation(true);
        $application->setLicence($licence);
        $application->setLicenceType($licenceType);

        $this->assertTrue($application->isRealUpgrade());
    }

    public function testIsRealUpgradeFalse()
    {
        /** @var RefData $licenceType */
        $licenceType = m::mock(RefData::class)->makePartial();
        $licenceType->setId(Licence::LICENCE_TYPE_STANDARD_NATIONAL);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class);
        $licence->shouldReceive('getLicenceType->getId')
            ->andReturn(Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL);

        /** @var Entity $application */
        $application = $this->instantiate(Entity::class);
        $application->setIsVariation(true);
        $application->setLicence($licence);
        $application->setLicenceType($licenceType);

        $this->assertFalse($application->isRealUpgrade());
    }

    public function testGetOcForInspectionRequest()
    {
        $oc1 = m::mock()
            ->shouldReceive('getAction')
            ->once()
            ->andReturn('A')
            ->shouldReceive('getOperatingCentre')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(1)
                ->twice()
                ->getMock()
            )
            ->getMock();

        $oc2 = m::mock()
            ->shouldReceive('getOperatingCentre')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(2)
                ->once()
                ->getMock()
            )
            ->shouldReceive('getAction')
            ->andReturn('D')
            ->once()
            ->getMock();

        $mockApplicationOperatingCentres = [$oc1, $oc2];

        $oc3 = m::mock()
            ->shouldReceive('getOperatingCentre')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(3)
                ->twice()
                ->getMock()
            )
            ->getMock();

        $mockLicenceOperatingCentres = [$oc3];

        $sut = m::mock(Entity::class)->makePartial();
        $sut->shouldReceive('getOperatingCentres')
            ->andReturn($mockApplicationOperatingCentres)
            ->once()
            ->shouldReceive('getLicence')
            ->andReturn(
                m::mock()
                ->shouldReceive('getOperatingCentres')
                ->andReturn($mockLicenceOperatingCentres)
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $result = $sut->getOcForInspectionRequest();
        $this->assertEquals(count($result), 2);
        $this->assertEquals($result[0]->getId(), 1);
        $this->assertEquals($result[1]->getId(), 3);
    }

    public function testGetVariationCompletionNotVariation()
    {
        $this->entity->setIsVariation(false);
        $this->assertNull($this->entity->getVariationCompletion());
    }

    public function testGetVariationCompletion()
    {
        $completion = m::mock(ApplicationCompletionEntity::class)->makePartial();
        $completion
            ->setAddressesStatus(2)
            ->setOperatingCentresStatus(1);

        $this->entity
            ->setApplicationCompletion($completion)
            ->setIsVariation(true);

        $result = $this->entity->getVariationCompletion();

        $this->assertInternalType('array', $result);

        $this->assertEquals(1, $result['operating_centres']);
        $this->assertEquals(2, $result['addresses']);
    }

    /**
     * @dataProvider niFlagProvider
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function testGetFeeTrafficAreaIdWithLicence($niFlag, $unused)
    {
        $trafficArea = m::mock(TrafficArea::class)
            ->makePartial()
            ->setId('Foo');
        $licence = m::mock(Licence::class)
            ->makePartial()
            ->setTrafficArea($trafficArea);

        $this->entity->setLicence($licence);

        $this->entity->setNiFlag($niFlag);

        $this->assertEquals('Foo', $this->entity->getFeeTrafficAreaId());
    }

    /**
     * @dataProvider niFlagProvider
     */
    public function testGetFeeTrafficAreaIdNoLicence($niFlag, $expected)
    {
        $licence = m::mock(Licence::class)->makePartial();

        $this->entity->setLicence($licence);

        $this->entity->setNiFlag($niFlag);

        $this->assertEquals($expected, $this->entity->getFeeTrafficAreaId());
    }

    public function niFlagProvider()
    {
        return [
            ['Y', 'N'],
            ['N', null],
        ];
    }

    public function testGetActiveVehicles()
    {
        /** @var Entity $application */
        $application = m::mock(Entity::class)->makePartial();

        $application->shouldReceive('getLicenceVehicles->matching')
            ->andReturn('foo');

        $this->assertEquals('foo', $application->getActiveVehicles());
    }

    public function testGetSectionsRequiringAttention()
    {
        /** @var Entity $application */
        $application = $this->instantiate(Entity::class);

        $statuses = [
            'businessTypeStatus' => Entity::VARIATION_STATUS_REQUIRES_ATTENTION,
            'businessDetailsStatus' => Entity::VARIATION_STATUS_UNCHANGED
        ];

        /** @var ApplicationCompletion $ac */
        $ac = m::mock(ApplicationCompletion::class)->makePartial();
        $ac->shouldReceive('serialize')
            ->with([])
            ->andReturn($statuses);

        $application->setApplicationCompletion($ac);

        $this->assertEquals(['businessType'], $application->getSectionsRequiringAttention());
    }

    public function testHasVariationChanges()
    {
        /** @var Entity $application */
        $application = $this->instantiate(Entity::class);

        $statuses = [
            'businessTypeStatus' => Entity::VARIATION_STATUS_REQUIRES_ATTENTION,
            'businessDetailsStatus' => Entity::VARIATION_STATUS_UNCHANGED
        ];

        /** @var ApplicationCompletion $ac */
        $ac = m::mock(ApplicationCompletion::class)->makePartial();
        $ac->shouldReceive('serialize')
            ->with([])
            ->andReturn($statuses);

        $application->setApplicationCompletion($ac);

        $this->assertTrue($application->hasVariationChanges());
    }

    public function testHasVariationChangesFalse()
    {
        /** @var Entity $application */
        $application = $this->instantiate(Entity::class);

        $statuses = [
            'businessTypeStatus' => Entity::VARIATION_STATUS_UNCHANGED,
            'businessDetailsStatus' => Entity::VARIATION_STATUS_UNCHANGED
        ];

        /** @var ApplicationCompletion $ac */
        $ac = m::mock(ApplicationCompletion::class)->makePartial();
        $ac->shouldReceive('serialize')
            ->with([])
            ->andReturn($statuses);

        $application->setApplicationCompletion($ac);

        $this->assertFalse($application->hasVariationChanges());
    }

    public function testCopyInformationFromLicence()
    {
        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();

        $licenceType = m::mock(RefData::class);
        $goodsOrPsv = m::mock(RefData::class);

        $licence->setLicenceType($licenceType);
        $licence->setGoodsOrPsv($goodsOrPsv);
        $licence->setTotAuthTrailers(5);
        $licence->setTotAuthVehicles(6);
        $licence->setTotAuthSmallVehicles(7);
        $licence->setTotAuthMediumVehicles(8);
        $licence->setTotAuthLargeVehicles(9);
        $licence->setNiFlag('Y');

        $this->entity->copyInformationFromLicence($licence);

        $this->assertEquals($licenceType, $this->entity->getLicenceType());
        $this->assertEquals($goodsOrPsv, $this->entity->getGoodsOrPsv());
        $this->assertEquals(5, $this->entity->getTotAuthTrailers());
        $this->assertEquals(6, $this->entity->getTotAuthVehicles());
        $this->assertEquals(7, $this->entity->getTotAuthSmallVehicles());
        $this->assertEquals(8, $this->entity->getTotAuthMediumVehicles());
        $this->assertEquals(9, $this->entity->getTotAuthLargeVehicles());
        $this->assertEquals('Y', $this->entity->getNiFlag());
    }
    public function testUseDeltasInPeopleSectionSole()
    {
        $type = new RefData();
        $type->setId('org_t_st');
        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $organisation->setType($type);
        $licence = new Licence($organisation, new RefData());
        $application = new Entity($licence, new RefData(), 1);

        $this->assertFalse($application->useDeltasInPeopleSection());
    }

    public function testUseDeltasInPeopleSectionPartnership()
    {
        $type = new RefData();
        $type->setId('org_t_p');
        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $organisation->setType($type);
        $licence = new Licence($organisation, new RefData());
        $application = new Entity($licence, new RefData(), 1);

        $this->assertFalse($application->useDeltasInPeopleSection());
    }

    public function testUseDeltasInPeopleSectionVariationLlp()
    {
        $type = new RefData();
        $type->setId('org_t_llp');
        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $organisation->setType($type);
        $licence = new Licence($organisation, new RefData());
        $application = new Entity($licence, new RefData(), 1);

        $this->assertTrue($application->useDeltasInPeopleSection());
    }

    public function testUseDeltasInPeopleSectionApplicationRc()
    {
        $type = new RefData();
        $type->setId('org_t_rc');
        $organisation = new \Dvsa\Olcs\Api\Entity\Organisation\Organisation();
        $organisation->setType($type);
        $licence = new Licence($organisation, new RefData());
        $application = new Entity($licence, new RefData(), 0);

        $this->assertFalse($application->useDeltasInPeopleSection());
    }
}
