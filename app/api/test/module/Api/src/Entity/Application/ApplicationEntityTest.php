<?php

namespace Dvsa\OlcsTest\Api\Entity\Application;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Application\Application as Entity;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion;
use Dvsa\Olcs\Api\Entity\Application\ApplicationOperatingCentre;
use Dvsa\Olcs\Api\Entity\Application\S4;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
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

        $application = m::mock(Entity::class)->makePartial();
        $oc = m::mock(OperatingCentre::class)->makePartial();

        foreach ($operatingCenterActions as $action) {
            $applicationOperatingCentre = new ApplicationOperatingCentre($application, $oc);
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
            $aoc = new ApplicationOperatingCentre(m::mock(Entity::class)->makePartial(), $oc);
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
        $completion = m::mock(ApplicationCompletion::class)->makePartial();
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
        $trafficArea = m::mock(TrafficArea::class)
            ->shouldReceive('getIsNi')
            ->andReturn(true)
            ->getMock();


        $licence->setLicenceType($licenceType);
        $licence->setGoodsOrPsv($goodsOrPsv);
        $licence->setTotAuthTrailers(5);
        $licence->setTotAuthVehicles(6);
        $licence->setTotAuthSmallVehicles(7);
        $licence->setTotAuthMediumVehicles(8);
        $licence->setTotAuthLargeVehicles(9);
        $licence->setTrafficArea($trafficArea);

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

    public function testGetCurrentInterimStatusNull()
    {
        /** @var Entity $application */
        $application = $this->instantiate(Entity::class);

        $this->assertNull($application->getCurrentInterimStatus());
    }

    public function testGetCurrentInterimStatus()
    {
        $status = m::mock(RefData::class)->makePartial();
        $status->setId(123);

        /** @var Entity $application */
        $application = $this->instantiate(Entity::class);
        $application->setInterimStatus($status);

        $this->assertEquals(123, $application->getCurrentInterimStatus());
    }

    /**
     * A new goods application with all dates provided
     * @see https://jira.i-env.net/browse/OLCS-8520
     */
    public function testGetOutOfRepresentationDateScenario1()
    {
        $aoc1 = new ApplicationOperatingCentre($this->entity, new OperatingCentre());
        $aoc1->setAction('A')
            ->setAdPlacedDate('2015-04-21')
            ->setNoOfVehiclesRequired(4);
        $this->entity->addOperatingCentres($aoc1);

        $aoc2 = new ApplicationOperatingCentre($this->entity, new OperatingCentre());
        $aoc2->setAction('A')
            ->setAdPlacedDate('2015-04-23')
            ->setNoOfVehiclesRequired(4);
        $this->entity->addOperatingCentres($aoc2);

        $oorDate = $this->entity->getOutOfRepresentationDate();

        $this->assertEquals(new DateTime('2015-05-14'), $oorDate);
    }

    /**
     * A new goods application with a date missing
     * @see https://jira.i-env.net/browse/OLCS-8520
     */
    public function testGetOutOfRepresentationDateScenario2()
    {
        $aoc1 = new ApplicationOperatingCentre($this->entity, new OperatingCentre());
        $aoc1->setAction('A')
            ->setAdPlacedDate('2015-04-21')
            ->setNoOfVehiclesRequired(4);
        $this->entity->addOperatingCentres($aoc1);

        $aoc2 = new ApplicationOperatingCentre($this->entity, new OperatingCentre());
        $aoc2->setAction('A')
            ->setNoOfVehiclesRequired(4);
        $this->entity->addOperatingCentres($aoc2);

        $oorDate = $this->entity->getOutOfRepresentationDate();

        $this->assertEquals('Unknown', $oorDate);
    }

    /**
     * A new goods application with two schedule 4 operating centres
     * @see https://jira.i-env.net/browse/OLCS-8520
     */
    public function testGetOutOfRepresentationDateScenario3()
    {
        /* @var $licence Licence */
        $licence = $this->instantiate(Licence::class);
        $oc1 = new OperatingCentre();
        $oc2 = new OperatingCentre();

        $loc1 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($licence, $oc1);
        $loc1->setNoOfVehiclesRequired(4);
        $loc2 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($licence, $oc2);
        $loc2->setNoOfVehiclesRequired(4);

        $licence->addOperatingCentres($loc1)
            ->addOperatingCentres($loc2);

        $s4 = new \Dvsa\Olcs\Api\Entity\Application\S4($this->entity, $licence);

        $aoc1 = new ApplicationOperatingCentre($this->entity, $oc1);
        $aoc1->setAction('A')
            ->setS4($s4)
            ->setNoOfVehiclesRequired(4);
        $this->entity->addOperatingCentres($aoc1);

        $aoc2 = new ApplicationOperatingCentre($this->entity, $oc2);
        $aoc2->setAction('A')
            ->setS4($s4)
            ->setNoOfVehiclesRequired(4);
        $this->entity->addOperatingCentres($aoc2);

        $oorDate = $this->entity->getOutOfRepresentationDate();

        $this->assertEquals('Not applicable', $oorDate);
    }

    /**
     * A new goods application with two schedule 4 operating centres
     * @see https://jira.i-env.net/browse/OLCS-8520
     */
    public function testGetOutOfRepresentationDateScenario3WithDates()
    {
        /* @var $licence Licence */
        $licence = $this->instantiate(Licence::class);
        $oc1 = new OperatingCentre();
        $oc2 = new OperatingCentre();

        $loc1 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($licence, $oc1);
        $loc1->setNoOfVehiclesRequired(4);
        $loc2 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($licence, $oc2);
        $loc2->setNoOfVehiclesRequired(4);

        $licence->addOperatingCentres($loc1)
            ->addOperatingCentres($loc2);

        $s4 = new \Dvsa\Olcs\Api\Entity\Application\S4($this->entity, $licence);

        $aoc1 = new ApplicationOperatingCentre($this->entity, $oc1);
        $aoc1->setAction('A')
            ->setS4($s4)
            ->setAdPlacedDate('2015-04-20')
            ->setNoOfVehiclesRequired(4);
        $this->entity->addOperatingCentres($aoc1);

        $aoc2 = new ApplicationOperatingCentre($this->entity, $oc2);
        $aoc2->setAction('A')
            ->setS4($s4)
            ->setAdPlacedDate('2015-04-20')
            ->setNoOfVehiclesRequired(4);
        $this->entity->addOperatingCentres($aoc2);

        $oorDate = $this->entity->getOutOfRepresentationDate();

        $this->assertEquals('Not applicable', $oorDate);
    }

    /**
     * A new goods application with two schedule 4 operating centres
     * @see https://jira.i-env.net/browse/OLCS-8520
     */
    public function testGetOutOfRepresentationDateScenario3WithDatesAndIncrease()
    {
        /* @var $licence Licence */
        $licence = $this->instantiate(Licence::class);
        $oc1 = new OperatingCentre();
        $oc2 = new OperatingCentre();

        $loc1 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($licence, $oc1);
        $loc1->setNoOfVehiclesRequired(4);
        $loc2 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($licence, $oc2);
        $loc2->setNoOfVehiclesRequired(4);

        $licence->addOperatingCentres($loc1)
            ->addOperatingCentres($loc2);

        $s4 = new \Dvsa\Olcs\Api\Entity\Application\S4($this->entity, $licence);

        $aoc1 = new ApplicationOperatingCentre($this->entity, $oc1);
        $aoc1->setAction('A')
            ->setS4($s4)
            ->setAdPlacedDate('2015-04-20')
            ->setNoOfVehiclesRequired(5);
        $this->entity->addOperatingCentres($aoc1);

        $aoc2 = new ApplicationOperatingCentre($this->entity, $oc2);
        $aoc2->setAction('A')
            ->setS4($s4)
            ->setAdPlacedDate('2015-04-22')
            ->setNoOfVehiclesRequired(4);
        $this->entity->addOperatingCentres($aoc2);

        $oorDate = $this->entity->getOutOfRepresentationDate();

        $this->assertEquals(new DateTime('2015-05-11'), $oorDate);
    }

    /**
     * A new goods application with one S4 operating centre and two other operating centres
     * @see https://jira.i-env.net/browse/OLCS-8520
     */
    public function testGetOutOfRepresentationDateScenario4()
    {
        /* @var $licence Licence */
        $licence = $this->instantiate(Licence::class);
        $oc1 = new OperatingCentre();
        $oc2 = new OperatingCentre();

        $loc1 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($licence, $oc1);
        $loc1->setNoOfVehiclesRequired(4);
        $loc2 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($licence, $oc2);
        $loc2->setNoOfVehiclesRequired(4);

        $licence->addOperatingCentres($loc1)
            ->addOperatingCentres($loc2);

        $s4 = new \Dvsa\Olcs\Api\Entity\Application\S4($this->entity, $licence);

        $aoc1 = new ApplicationOperatingCentre($this->entity, $oc1);
        $aoc1->setAction('A')
            ->setS4($s4)
            ->setNoOfVehiclesRequired(4);
        $this->entity->addOperatingCentres($aoc1);

        $aoc2 = new ApplicationOperatingCentre($this->entity, $oc2);
        $aoc2->setAction('A')
            ->setAdPlacedDate('2015-04-21')
            ->setNoOfVehiclesRequired(4);
        $this->entity->addOperatingCentres($aoc2);

        $aoc3 = new ApplicationOperatingCentre($this->entity, $oc2);
        $aoc3->setAction('A')
            ->setAdPlacedDate('2015-04-20')
            ->setNoOfVehiclesRequired(4);
        $this->entity->addOperatingCentres($aoc3);

        $oorDate = $this->entity->getOutOfRepresentationDate();

        $this->assertEquals(new DateTime('2015-05-12'), $oorDate);
    }

    /**
     * A new goods application with one S4 operating centre and two other operating centres; one with a missing date
     * @see https://jira.i-env.net/browse/OLCS-8520
     */
    public function testGetOutOfRepresentationDateScenario5()
    {
        /* @var $licence Licence */
        $licence = $this->instantiate(Licence::class);
        $oc1 = new OperatingCentre();
        $oc2 = new OperatingCentre();

        $loc1 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($licence, $oc1);
        $loc1->setNoOfVehiclesRequired(4);
        $loc2 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($licence, $oc2);
        $loc2->setNoOfVehiclesRequired(4);

        $licence->addOperatingCentres($loc1)
            ->addOperatingCentres($loc2);

        $s4 = new \Dvsa\Olcs\Api\Entity\Application\S4($this->entity, $licence);

        $aoc1 = new ApplicationOperatingCentre($this->entity, $oc1);
        $aoc1->setAction('A')
            ->setS4($s4)
            ->setNoOfVehiclesRequired(4);
        $this->entity->addOperatingCentres($aoc1);

        $aoc2 = new ApplicationOperatingCentre($this->entity, $oc2);
        $aoc2->setAction('A')
            ->setAdPlacedDate('2015-04-21')
            ->setNoOfVehiclesRequired(4);
        $this->entity->addOperatingCentres($aoc2);

        $aoc3 = new ApplicationOperatingCentre($this->entity, $oc2);
        $aoc3->setAction('A')
            ->setNoOfVehiclesRequired(4);
        $this->entity->addOperatingCentres($aoc3);

        $oorDate = $this->entity->getOutOfRepresentationDate();

        $this->assertEquals('Unknown', $oorDate);
    }

    /**
     * A goods variation application with one S4 operating centre and one other operating centre with no increase
     * @see https://jira.i-env.net/browse/OLCS-8520
     */
    public function testGetOutOfRepresentationDateScenario6()
    {
        /* @var $s4DonorLicence Licence */
        $s4DonorLicence = $this->instantiate(Licence::class);
        $oc1 = new OperatingCentre();
        $oc2 = new OperatingCentre();
        $loc1 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($s4DonorLicence, $oc1);
        $loc1->setNoOfVehiclesRequired(4);
        $loc2 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($s4DonorLicence, $oc2);
        $loc2->setNoOfVehiclesRequired(4);

        $s4DonorLicence->addOperatingCentres($loc1)
            ->addOperatingCentres($loc2);

        $s4 = new \Dvsa\Olcs\Api\Entity\Application\S4($this->entity, $s4DonorLicence);

        $appLicence = $this->instantiate(Licence::class);
        $oc11 = new OperatingCentre();
        $oc12 = new OperatingCentre();
        $loc11 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($s4DonorLicence, $oc11);
        $loc11->setNoOfVehiclesRequired(4);
        $loc12 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($s4DonorLicence, $oc12);
        $loc12->setNoOfVehiclesRequired(4);

        $appLicence->addOperatingCentres($loc11)
            ->addOperatingCentres($loc12);

        $this->entity->setLicence($appLicence);

        $aoc1 = new ApplicationOperatingCentre($this->entity, $oc1);
        $aoc1->setAction('A')
            ->setS4($s4)
            ->setNoOfVehiclesRequired(4);
        $this->entity->addOperatingCentres($aoc1);

        $aoc2 = new ApplicationOperatingCentre($this->entity, $oc12);
        $aoc2->setAction('U')
            ->setNoOfVehiclesRequired(2);
        $this->entity->addOperatingCentres($aoc2);

        $oorDate = $this->entity->getOutOfRepresentationDate();

        $this->assertEquals('Not applicable', $oorDate);
    }

    /**
     * A goods variation application with one S4 operating centre and two other operating centres; one
     * with vehicle increase
     * @see https://jira.i-env.net/browse/OLCS-8520
     */
    public function testGetOutOfRepresentationDateScenario7()
    {
        /* @var $s4DonorLicence Licence */
        $s4DonorLicence = $this->instantiate(Licence::class);
        $oc1 = new OperatingCentre();
        $oc2 = new OperatingCentre();
        $loc1 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($s4DonorLicence, $oc1);
        $loc1->setNoOfVehiclesRequired(4);
        $loc2 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($s4DonorLicence, $oc2);
        $loc2->setNoOfVehiclesRequired(4);

        $s4DonorLicence->addOperatingCentres($loc1)
            ->addOperatingCentres($loc2);

        $s4 = new \Dvsa\Olcs\Api\Entity\Application\S4($this->entity, $s4DonorLicence);

        $appLicence = $this->instantiate(Licence::class);
        $oc11 = new OperatingCentre();
        $oc12 = new OperatingCentre();
        $loc11 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($s4DonorLicence, $oc11);
        $loc11->setNoOfVehiclesRequired(4);
        $loc12 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($s4DonorLicence, $oc12);
        $loc12->setNoOfVehiclesRequired(3);

        $appLicence->addOperatingCentres($loc11)
            ->addOperatingCentres($loc12);

        $this->entity->setLicence($appLicence);

        $aoc1 = new ApplicationOperatingCentre($this->entity, $oc1);
        $aoc1->setAction('A')
            ->setS4($s4)
            ->setNoOfVehiclesRequired(4);
        $this->entity->addOperatingCentres($aoc1);

        $aoc2 = new ApplicationOperatingCentre($this->entity, $oc11);
        $aoc2->setAction('U')
            ->setAdPlacedDate('2015-04-21')
            ->setNoOfVehiclesRequired(6);
        $this->entity->addOperatingCentres($aoc2);

        $aoc3 = new ApplicationOperatingCentre($this->entity, $oc12);
        $aoc3->setAction('U')
            ->setNoOfVehiclesRequired(1);
        $this->entity->addOperatingCentres($aoc3);

        $oorDate = $this->entity->getOutOfRepresentationDate();

        $this->assertEquals(new DateTime('2015-05-12'), $oorDate);
    }

    /**
     * A goods variation application with one S4 operating centre and two other operating centres with vehicle
     * increases but with a missing advertisement date
     * @see https://jira.i-env.net/browse/OLCS-8520
     */
    public function testGetOutOfRepresentationDateScenario8()
    {
        /* @var $s4DonorLicence Licence */
        $s4DonorLicence = $this->instantiate(Licence::class);
        $oc1 = new OperatingCentre();
        $oc2 = new OperatingCentre();
        $loc1 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($s4DonorLicence, $oc1);
        $loc1->setNoOfVehiclesRequired(4);
        $loc2 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($s4DonorLicence, $oc2);
        $loc2->setNoOfVehiclesRequired(4);

        $s4DonorLicence->addOperatingCentres($loc1)
            ->addOperatingCentres($loc2);

        $s4 = new \Dvsa\Olcs\Api\Entity\Application\S4($this->entity, $s4DonorLicence);

        $appLicence = $this->instantiate(Licence::class);
        $oc11 = new OperatingCentre();
        $oc12 = new OperatingCentre();
        $loc11 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($s4DonorLicence, $oc11);
        $loc11->setNoOfVehiclesRequired(4);
        $loc12 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($s4DonorLicence, $oc12);
        $loc12->setNoOfVehiclesRequired(1);

        $appLicence->addOperatingCentres($loc11)
            ->addOperatingCentres($loc12);

        $this->entity->setLicence($appLicence);

        $aoc1 = new ApplicationOperatingCentre($this->entity, $oc1);
        $aoc1->setAction('A')
            ->setS4($s4)
            ->setNoOfVehiclesRequired(4);
        $this->entity->addOperatingCentres($aoc1);

        $aoc2 = new ApplicationOperatingCentre($this->entity, $oc11);
        $aoc2->setAction('U')
            ->setNoOfVehiclesRequired(6);
        $this->entity->addOperatingCentres($aoc2);

        $aoc3 = new ApplicationOperatingCentre($this->entity, $oc12);
        $aoc3->setAction('U')
            ->setAdPlacedDate('2015-04-20')
            ->setNoOfVehiclesRequired(3);
        $this->entity->addOperatingCentres($aoc3);

        $oorDate = $this->entity->getOutOfRepresentationDate();

        $this->assertEquals('Unknown', $oorDate);
    }

    /**
     * A goods variation application with one S4 operating centre and two other operating centres with
     * vehicle increases including the S4 but with missing advertisement date
     * @see https://jira.i-env.net/browse/OLCS-8520
     */
    public function testGetOutOfRepresentationDateScenario9()
    {
        /* @var $s4DonorLicence Licence */
        $s4DonorLicence = $this->instantiate(Licence::class);
        $oc1 = new OperatingCentre();
        $loc1 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($s4DonorLicence, $oc1);
        $loc1->setNoOfVehiclesRequired(2);

        $s4DonorLicence->addOperatingCentres($loc1);

        $s4 = new \Dvsa\Olcs\Api\Entity\Application\S4($this->entity, $s4DonorLicence);

        $appLicence = $this->instantiate(Licence::class);
        $oc11 = new OperatingCentre();
        $oc12 = new OperatingCentre();
        $loc11 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($s4DonorLicence, $oc11);
        $loc11->setNoOfVehiclesRequired(4);
        $loc12 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($s4DonorLicence, $oc12);
        $loc12->setNoOfVehiclesRequired(1);

        $appLicence->addOperatingCentres($loc11)
            ->addOperatingCentres($loc12);

        $this->entity->setLicence($appLicence);

        $aoc1 = new ApplicationOperatingCentre($this->entity, $oc1);
        $aoc1->setAction('A')
            ->setS4($s4)
            ->setNoOfVehiclesRequired(6);
        $this->entity->addOperatingCentres($aoc1);

        $aoc2 = new ApplicationOperatingCentre($this->entity, $oc11);
        $aoc2->setAction('U')
            ->setAdPlacedDate('2015-04-21')
            ->setNoOfVehiclesRequired(6);
        $this->entity->addOperatingCentres($aoc2);

        $aoc3 = new ApplicationOperatingCentre($this->entity, $oc12);
        $aoc3->setAction('U')
            ->setAdPlacedDate('2015-04-20')
            ->setNoOfVehiclesRequired(3);
        $this->entity->addOperatingCentres($aoc3);

        $oorDate = $this->entity->getOutOfRepresentationDate();

        $this->assertEquals('Unknown', $oorDate);
    }

    /**
     * A goods variation application with one S4 operating centre and two other operating centres with advertising dates
     * @see https://jira.i-env.net/browse/OLCS-8520
     */
    public function testGetOutOfRepresentationDateScenario10()
    {
        /* @var $s4DonorLicence Licence */
        $s4DonorLicence = $this->instantiate(Licence::class);
        $oc1 = new OperatingCentre();
        $loc1 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($s4DonorLicence, $oc1);
        $loc1->setNoOfVehiclesRequired(2);

        $s4DonorLicence->addOperatingCentres($loc1);

        $s4 = new \Dvsa\Olcs\Api\Entity\Application\S4($this->entity, $s4DonorLicence);

        $appLicence = $this->instantiate(Licence::class);
        $oc11 = new OperatingCentre();
        $oc12 = new OperatingCentre();
        $loc11 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($s4DonorLicence, $oc11);
        $loc11->setNoOfVehiclesRequired(4);
        $loc12 = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($s4DonorLicence, $oc12);
        $loc12->setNoOfVehiclesRequired(1);

        $appLicence->addOperatingCentres($loc11)
            ->addOperatingCentres($loc12);

        $this->entity->setLicence($appLicence);

        $aoc1 = new ApplicationOperatingCentre($this->entity, $oc1);
        $aoc1->setAction('A')
            ->setS4($s4)
            ->setAdPlacedDate('2015-04-21')
            ->setNoOfVehiclesRequired(6);
        $this->entity->addOperatingCentres($aoc1);

        $aoc2 = new ApplicationOperatingCentre($this->entity, $oc11);
        $aoc2->setAction('U')
            ->setAdPlacedDate('2015-04-19')
            ->setNoOfVehiclesRequired(6);
        $this->entity->addOperatingCentres($aoc2);

        $aoc3 = new ApplicationOperatingCentre($this->entity, $oc12);
        $aoc3->setAction('U')
            ->setAdPlacedDate('2015-04-20')
            ->setNoOfVehiclesRequired(3);
        $this->entity->addOperatingCentres($aoc3);

        $oorDate = $this->entity->getOutOfRepresentationDate();

        $this->assertEquals(new DateTime('2015-05-12'), $oorDate);
    }

    public function testGetOutOfRepresentationDatePsv()
    {
        $this->entity->setGoodsOrPsv((new RefData())->setId(Licence::LICENCE_CATEGORY_PSV));

        $oorDate = $this->entity->getOutOfRepresentationDate();

        $this->assertEquals('Not applicable', $oorDate);
    }

    public function testGetOutOfRepresentationDateApplicationNoOcs()
    {
        $this->entity->setGoodsOrPsv((new RefData())->setId(Licence::LICENCE_CATEGORY_GOODS_VEHICLE));

        $aoc = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $aoc->setAction('D');
        $this->entity->addOperatingCentres($aoc);

        $oorDate = $this->entity->getOutOfRepresentationDate();

        $this->assertEquals('Unknown', $oorDate);
    }

    public function testGetOutOfRepresentationDateVariationNoOcs()
    {
        $this->entity->setGoodsOrPsv((new RefData())->setId(Licence::LICENCE_CATEGORY_GOODS_VEHICLE));
        $this->entity->setIsVariation(1);

        $aoc = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $aoc->setAction('D');
        $this->entity->addOperatingCentres($aoc);

        $oorDate = $this->entity->getOutOfRepresentationDate();

        $this->assertEquals('Not applicable', $oorDate);
    }

    public function testGetOutOfOppositionDatePsvVariation()
    {
        $this->entity->setGoodsOrPsv((new RefData())->setId(Licence::LICENCE_CATEGORY_PSV));
        $this->entity->setIsVariation('Y');

        $oorDate = $this->entity->getOutOfOppositionDate();

        $this->assertEquals('Not applicable', $oorDate);
    }

    public function testGetOutOfOppositionDateGoodsNoAddedOperatingCentres()
    {
        $this->entity->setGoodsOrPsv((new RefData())->setId(Licence::LICENCE_CATEGORY_GOODS_VEHICLE));
        $this->entity->setIsVariation('Y');

        $aoc = new ApplicationOperatingCentre($this->entity, new OperatingCentre());
        $aoc->setAction('U');
        $this->entity->addOperatingCentres($aoc);

        $oorDate = $this->entity->getOutOfOppositionDate();

        $this->assertEquals('Not applicable', $oorDate);
    }

    public function testGetOutOfOppositionDateGoodOperatingCentresUpdatedNoIncrease()
    {
        $this->entity->setGoodsOrPsv((new RefData())->setId(Licence::LICENCE_CATEGORY_GOODS_VEHICLE));
        $this->entity->setIsVariation('Y');

        $oc = new OperatingCentre();
        $oc->setId(821);

        $licence = new Licence(new \Dvsa\Olcs\Api\Entity\Organisation\Organisation(), new RefData());
        $this->entity->setLicence($licence);

        $loc = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($licence, $oc);
        $loc->setNoOfVehiclesRequired(23);
        $licence->addOperatingCentres($loc);

        $aoc = new ApplicationOperatingCentre($this->entity, $oc);
        $aoc->setAction('U');
        $aoc->setNoOfVehiclesRequired(23);
        $this->entity->addOperatingCentres($aoc);

        $aoc2 = new ApplicationOperatingCentre($this->entity, new OperatingCentre());
        $aoc2->setAction('A');
        $this->entity->addOperatingCentres($aoc2);

        $oorDate = $this->entity->getOutOfOppositionDate();

        $this->assertEquals('Not applicable', $oorDate);
    }

    public function testGetOutOfOppositionDateIncreaseInVehicleNoPublicationDate()
    {
        $this->entity->setGoodsOrPsv((new RefData())->setId(Licence::LICENCE_CATEGORY_GOODS_VEHICLE));
        $this->entity->setIsVariation('Y');

        $oc = new OperatingCentre();
        $oc->setId(821);

        $licence = new Licence(new \Dvsa\Olcs\Api\Entity\Organisation\Organisation(), new RefData());
        $this->entity->setLicence($licence);

        $loc = new \Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre($licence, $oc);
        $loc->setNoOfVehiclesRequired(23);
        $licence->addOperatingCentres($loc);

        $aoc = new ApplicationOperatingCentre($this->entity, $oc);
        $aoc->setAction('U');
        $aoc->setNoOfVehiclesRequired(24);
        $this->entity->addOperatingCentres($aoc);

        $aoc2 = new ApplicationOperatingCentre($this->entity, new OperatingCentre());
        $aoc2->setAction('A');
        $this->entity->addOperatingCentres($aoc2);

        $oorDate = $this->entity->getOutOfOppositionDate();

        $this->assertEquals('Unknown', $oorDate);
    }

    public function testGetOutOfOppositionDate()
    {
        $publicationSection3 = new \Dvsa\Olcs\Api\Entity\Publication\PublicationSection();
        $publicationSection3->setId(3);
        $publicationSection4 = new \Dvsa\Olcs\Api\Entity\Publication\PublicationSection();
        $publicationSection4->setId(4);

        $publication1 = new \Dvsa\Olcs\Api\Entity\Publication\Publication();
        $publication1->setPubDate('2014-07-29');
        $publication2 = new \Dvsa\Olcs\Api\Entity\Publication\Publication();
        $publication2->setPubDate('2014-07-30');

        $publicationLink1 = new \Dvsa\Olcs\Api\Entity\Publication\PublicationLink();
        $publicationLink1->setPublicationSection($publicationSection3)
            ->setPublication($publication1);
        $publicationLink2 = new \Dvsa\Olcs\Api\Entity\Publication\PublicationLink();
        $publicationLink2->setPublicationSection($publicationSection4);
        $publicationLink3 = new \Dvsa\Olcs\Api\Entity\Publication\PublicationLink();
        $publicationLink3->setPublicationSection($publicationSection3)
            ->setPublication($publication2);

        $this->entity->addPublicationLinks($publicationLink1);
        $this->entity->addPublicationLinks($publicationLink2);
        $this->entity->addPublicationLinks($publicationLink3);

        $oorDate = $this->entity->getOutOfOppositionDate();

        $this->assertEquals(new \DateTime('2014-08-20'), $oorDate);
    }

    /**
     * @dataProvider canHaveLargeVehicleProvider
     */
    public function testCanHaveLargeVehicles($isPsv, $licenceType, $expected)
    {
        /** @var Entity $application */
        $application = m::mock(Entity::class)->makePartial();
        $application->shouldReceive('isPsv')
            ->andReturn($isPsv);

        $application->shouldReceive('getLicenceType->getId')
            ->andReturn($licenceType);

        $this->assertEquals($expected, $application->canHaveLargeVehicles());
    }

    /**
     * @dataProvider canHaveCommunityLicencesProvider
     */
    public function testCanHaveCommunityLicences($isStandardInternational, $isPsv, $isRestricted, $expected)
    {
        /** @var Entity $application */
        $application = m::mock(Entity::class)->makePartial();
        $application->shouldReceive('isPsv')
            ->andReturn($isPsv)
            ->shouldReceive('isStandardInternational')
            ->andReturn($isStandardInternational)
            ->shouldReceive('isRestricted')
            ->andReturn($isRestricted);

        $this->assertEquals($expected, $application->canHaveCommunityLicences());
    }

    public function testGetDeltaAocByOc()
    {
        $oc = m::mock(OperatingCentre::class)->makePartial();
        $oc2 = m::mock(OperatingCentre::class)->makePartial();

        /** @var ApplicationOperatingCentre $aoc */
        $aoc = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $aoc->setOperatingCentre($oc);

        /** @var ApplicationOperatingCentre $aoc2 */
        $aoc2 = m::mock(ApplicationOperatingCentre::class)->makePartial();
        $aoc2->setOperatingCentre($oc2);

        $ocs = new ArrayCollection();
        $ocs->add($aoc);
        $ocs->add($aoc2);

        /** @var Entity $application */
        $application = $this->instantiate(Entity::class);
        $application->setOperatingCentres($ocs);

        $collection = $application->getDeltaAocByOc($oc);

        $this->assertEquals(1, $collection->count());
        $this->assertSame($aoc, $collection->first());
    }

    public function testGetActiveS4s()
    {
        /** @var RefData $approved */
        $approved = m::mock(RefData::class)->makePartial();
        $approved->setId(S4::STATUS_APPROVED);

        $refused = m::mock(RefData::class)->makePartial();
        $refused->setId(S4::STATUS_REFUSED);

        /** @var S4 $s41 */
        $s41 = m::mock(S4::class)->makePartial();
        $s41->setOutcome(null);
        /** @var S4 $s42 */
        $s42 = m::mock(S4::class)->makePartial();
        $s41->setOutcome($approved);
        /** @var S4 $s43 */
        $s43 = m::mock(S4::class)->makePartial();
        $s43->setOutcome($refused);

        $s4s = new ArrayCollection();
        $s4s->add($s41);
        $s4s->add($s42);
        $s4s->add($s43);

        /** @var Entity $application */
        $application = $this->instantiate(Entity::class);

        $application->setS4s($s4s);

        $active = $application->getActiveS4s();

        $this->assertCount(2, $active);
        $this->assertTrue(in_array($s41, $active));
        $this->assertTrue(in_array($s42, $active));
        $this->assertfalse(in_array($s43, $active));
    }

    public function testIsNew()
    {
        /** @var Entity $application */
        $application = $this->instantiate(Entity::class);

        $application->setIsVariation(true);
        $this->assertFalse($application->isNew());

        $application->setIsVariation(false);
        $this->assertTrue($application->isNew());
    }

    public function testIsRestricted()
    {
        $sr = m::mock(RefData::class)->makePartial();
        $sr->setId(Licence::LICENCE_TYPE_SPECIAL_RESTRICTED);

        $r = m::mock(RefData::class)->makePartial();
        $r->setId(Licence::LICENCE_TYPE_RESTRICTED);

        /** @var Entity $application */
        $application = $this->instantiate(Entity::class);

        $this->assertFalse($application->isRestricted());

        $application->setLicenceType($sr);
        $this->assertFalse($application->isRestricted());

        $application->setLicenceType($r);
        $this->assertTrue($application->isRestricted());
    }

    public function testIsStandardInternational()
    {
        $sn = m::mock(RefData::class)->makePartial();
        $sn->setId(Licence::LICENCE_TYPE_STANDARD_NATIONAL);

        $si = m::mock(RefData::class)->makePartial();
        $si->setId(Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL);

        /** @var Entity $application */
        $application = $this->instantiate(Entity::class);

        $this->assertFalse($application->isStandardInternational());

        $application->setLicenceType($sn);
        $this->assertFalse($application->isStandardInternational());

        $application->setLicenceType($si);
        $this->assertTrue($application->isStandardInternational());
    }

    public function testGetAvailableSmallSpaces()
    {
        /** @var Entity $application */
        $application = m::mock(Entity::class)->makePartial();
        $application->setTotAuthSmallVehicles(10);

        $this->assertEquals(5, $application->getAvailableSmallSpaces(5));
        $this->assertEquals(10, $application->getAvailableSmallSpaces(0));
        $this->assertEquals(0, $application->getAvailableSmallSpaces(10));
    }

    public function testGetAvailableMediumSpaces()
    {
        /** @var Entity $application */
        $application = m::mock(Entity::class)->makePartial();
        $application->setTotAuthMediumVehicles(10);

        $this->assertEquals(5, $application->getAvailableMediumSpaces(5));
        $this->assertEquals(10, $application->getAvailableMediumSpaces(0));
        $this->assertEquals(0, $application->getAvailableMediumSpaces(10));
    }

    public function testGetAvailableLargeSpaces()
    {
        /** @var Entity $application */
        $application = m::mock(Entity::class)->makePartial();
        $application->setTotAuthLargeVehicles(10);

        $this->assertEquals(5, $application->getAvailableLargeSpaces(5));
        $this->assertEquals(10, $application->getAvailableLargeSpaces(0));
        $this->assertEquals(0, $application->getAvailableLargeSpaces(10));
    }

    public function testIsSmallAuthExceeded()
    {
        /** @var Entity $application */
        $application = m::mock(Entity::class)->makePartial();
        $application->setTotAuthSmallVehicles(10);

        $this->assertFalse($application->isSmallAuthExceeded(5));
        $this->assertFalse($application->isSmallAuthExceeded(10));
        $this->assertTrue($application->isSmallAuthExceeded(11));
    }

    public function testIsMediumAuthExceeded()
    {
        /** @var Entity $application */
        $application = m::mock(Entity::class)->makePartial();
        $application->setTotAuthMediumVehicles(10);

        $this->assertFalse($application->isMediumAuthExceeded(5));
        $this->assertFalse($application->isMediumAuthExceeded(10));
        $this->assertTrue($application->isMediumAuthExceeded(11));
    }

    public function testIsLargeAuthExceeded()
    {
        /** @var Entity $application */
        $application = m::mock(Entity::class)->makePartial();
        $application->setTotAuthLargeVehicles(10);

        $this->assertFalse($application->isLargeAuthExceeded(5));
        $this->assertFalse($application->isLargeAuthExceeded(10));
        $this->assertTrue($application->isLargeAuthExceeded(11));
    }

    public function testHasPsvBreakdown()
    {
        /** @var Entity $application */
        $application = m::mock(Entity::class)->makePartial();

        $this->assertFalse($application->hasPsvBreakdown());

        $application->setTotAuthSmallVehicles(10);
        $this->assertTrue($application->hasPsvBreakdown());
    }

    public function testShouldShowTablesWithoutBreakdown()
    {
        /** @var Entity $application */
        $application = m::mock(Entity::class)->makePartial();
        $application->shouldReceive('isPsv')
            ->andReturn(true);
        $application->shouldReceive('getLicenceType->getId')
            ->andReturn(Licence::LICENCE_TYPE_STANDARD_NATIONAL);

        $this->assertTrue($application->shouldShowSmallTable(1));
        $this->assertTrue($application->shouldShowMediumTable(1));
        $this->assertTrue($application->shouldShowLargeTable(1));
    }

    public function testShouldShowTablesWithoutLarge()
    {
        /** @var Entity $application */
        $application = m::mock(Entity::class)->makePartial();
        $application->shouldReceive('isPsv')
            ->andReturn(true);
        $application->shouldReceive('getLicenceType->getId')
            ->andReturn(Licence::LICENCE_TYPE_RESTRICTED);

        $this->assertFalse($application->shouldShowLargeTable(1));
    }

    public function testShouldShowTablesWithBreakdownWithAuth()
    {
        /** @var Entity $application */
        $application = m::mock(Entity::class)->makePartial();
        $application->shouldReceive('isPsv')
            ->andReturn(true);
        $application->shouldReceive('getLicenceType->getId')
            ->andReturn(Licence::LICENCE_TYPE_STANDARD_NATIONAL);

        $application->setTotAuthSmallVehicles(3);
        $application->setTotAuthMediumVehicles(3);
        $application->setTotAuthLargeVehicles(3);

        $this->assertTrue($application->shouldShowSmallTable(1));
        $this->assertTrue($application->shouldShowMediumTable(1));
        $this->assertTrue($application->shouldShowLargeTable(1));
    }

    public function testShouldShowTablesWithBreakdownWithoutAuth()
    {
        /** @var Entity $application */
        $application = m::mock(Entity::class)->makePartial();
        $application->shouldReceive('isPsv')
            ->andReturn(true);
        $application->shouldReceive('getLicenceType->getId')
            ->andReturn(Licence::LICENCE_TYPE_STANDARD_NATIONAL);

        $application->setTotAuthSmallVehicles(3);
        $application->setTotAuthMediumVehicles(0);
        $application->setTotAuthLargeVehicles(0);

        $this->assertTrue($application->shouldShowSmallTable(1));
        $this->assertFalse($application->shouldShowMediumTable(0));
        $this->assertFalse($application->shouldShowLargeTable(0));
    }

    public function testShouldShowTablesWithBreakdownWithoutAuthWithVehicle()
    {
        /** @var Entity $application */
        $application = m::mock(Entity::class)->makePartial();
        $application->shouldReceive('isPsv')
            ->andReturn(true);
        $application->shouldReceive('getLicenceType->getId')
            ->andReturn(Licence::LICENCE_TYPE_STANDARD_NATIONAL);

        $application->setTotAuthSmallVehicles(3);
        $application->setTotAuthMediumVehicles(0);
        $application->setTotAuthLargeVehicles(0);

        $this->assertTrue($application->shouldShowSmallTable(1));
        $this->assertTrue($application->shouldShowMediumTable(1));
        $this->assertTrue($application->shouldShowLargeTable(1));
    }

    public function testGetActiveVehiclesCount()
    {
        /** @var Entity $application */
        $application = m::mock(Entity::class)->makePartial();

        $application->shouldReceive('getActiveLicenceVehicles->count')
            ->andReturn(10);

        $this->assertEquals(10, $application->getActiveVehiclesCount());
    }

    public function canHaveCommunityLicencesProvider()
    {
        return [
            'Goods SI' => [
                true,
                false,
                false,
                true
            ],
            'PSV SI' => [
                true,
                true,
                false,
                true
            ],
            'PSV R' => [
                false,
                true,
                true,
                true
            ],
            'PSV Non R' => [
                false,
                true,
                false,
                false
            ]
        ];
    }

    public function canHaveLargeVehicleProvider()
    {
        return [
            'PSV SN' => [
                true,
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                true
            ],
            'PSV SI' => [
                true,
                Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                true
            ],
            'GV SN' => [
                false,
                Licence::LICENCE_TYPE_STANDARD_NATIONAL,
                false
            ],
            'GV SI' => [
                false,
                Licence::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                false
            ],
            'PSV SR' => [
                true,
                Licence::LICENCE_TYPE_SPECIAL_RESTRICTED,
                false
            ],
            'PSV R' => [
                true,
                Licence::LICENCE_TYPE_RESTRICTED,
                false
            ],
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

        $application = $this->instantiate(Entity::class);
        $application->setGoodsOrPsv($category);

        $this->assertEquals($expected, $application->getCategoryPrefix());
    }

    public function categoryPrefixDp()
    {
        return [
            [Licence::LICENCE_CATEGORY_PSV, 'P'],
            [Licence::LICENCE_CATEGORY_GOODS_VEHICLE, 'O'],
        ];
    }

    public function testGetOtherActiveLicencesForOrganisation()
    {
        $licence1 = m::mock(Licence::class)->makePartial()->setId(7);
        $licence2 = m::mock(Licence::class)->makePartial()->setId(8);
        $organisationLicences = m::mock(ArrayCollection::class)
            ->shouldReceive('toArray')
            ->once()
            ->andReturn([$licence1, $licence2])
            ->getMock();
        $licence1
            ->shouldReceive('getOrganisation->getActiveLicences')
            ->once()
            ->andReturn($organisationLicences);

        /** @var Entity $application */
        $application = $this->instantiate(Entity::class);
        $application->setLicence($licence1);

        $this->assertEquals([$licence2], $application->getOtherActiveLicencesForOrganisation());
    }

    public function testGetOperatingCentresNetDelta()
    {
        /** @var Entity $application */
        $application = $this->instantiate(Entity::class);

        $aocs = new ArrayCollection();
        $application->setOperatingCentres($aocs);

        $this->assertEquals(0, $application->getOperatingCentresNetDelta());

        $application->getOperatingCentres()->add(
            m::mock(ApplicationOperatingCentre::class)->makePartial()->setAction('A')
        );
        $this->assertEquals(1, $application->getOperatingCentresNetDelta());

        $application->getOperatingCentres()->add(
            m::mock(ApplicationOperatingCentre::class)->makePartial()->setAction('A')
        );
        $this->assertEquals(2, $application->getOperatingCentresNetDelta());

        $application->getOperatingCentres()->add(
            m::mock(ApplicationOperatingCentre::class)->makePartial()->setAction('D')
        );
        $this->assertEquals(1, $application->getOperatingCentresNetDelta());
    }

    /**
     * @dataProvider allowFeePaymentsProvider
     */
    public function testAllowFeePayments($statusId, $licenceStatusId, $expected)
    {
        /** @var Entity $application */
        $application = $this->instantiate(Entity::class);

        $organisation = m::mock(Organisation::class);

        $status = m::mock(RefData::class)
            ->shouldReceive('getId')
            ->andReturn($statusId)
            ->getMock();
        $licenceStatus = m::mock(RefData::class)
            ->shouldReceive('getId')
            ->andReturn($licenceStatusId)
            ->getMock();

        $licence = new Licence($organisation, $licenceStatus);

        $application->setStatus($status);
        $application->setLicence($licence);

        $this->assertEquals($expected, $application->allowFeePayments());
    }

    public function testIsPsvDowngradeGoods()
    {
        /** @var Entity $application */
        $application = m::mock(Entity::class)->makePartial();
        $application->shouldReceive('isGoods')
            ->andReturn(true);

        $this->assertFalse($application->isPsvDowngrade());
    }

    public function testIsPsvDowngradeNotRestricted()
    {
        /** @var Entity $application */
        $application = m::mock(Entity::class)->makePartial();
        $application->shouldReceive('isGoods')->andReturn(false);
        $application->shouldReceive('isRestricted')->andReturn(false);

        $this->assertFalse($application->isPsvDowngrade());
    }

    public function testIsPsvDowngrade()
    {
        /** @var Entity $application */
        $application = m::mock(Entity::class)->makePartial();
        $application->shouldReceive('isGoods')->andReturn(false);
        $application->shouldReceive('isRestricted')->andReturn(true);
        $application->shouldReceive('getLicence->isRestricted')->andReturn(false);

        $this->assertTrue($application->isPsvDowngrade());
    }

    public function testHasAuthChangedNew()
    {
        /** @var Entity $application */
        $application = m::mock(Entity::class)->makePartial();
        $application->shouldReceive('isNew')->andReturn(true);

        $this->assertFalse($application->hasAuthChanged());
    }

    public function testHasAuthChanged()
    {
        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setTotAuthVehicles(9);

        /** @var Entity $application */
        $application = m::mock(Entity::class)->makePartial();
        $application->shouldReceive('isNew')->andReturn(false);
        $application->setTotAuthVehicles(10);
        $application->setLicence($licence);

        $this->assertTrue($application->hasAuthChanged());
    }

    public function testHasAuthChangedWithoutChange()
    {
        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setTotAuthVehicles(10);

        /** @var Entity $application */
        $application = m::mock(Entity::class)->makePartial();
        $application->shouldReceive('isNew')->andReturn(false);
        $application->setTotAuthVehicles(10);
        $application->setLicence($licence);

        $this->assertFalse($application->hasAuthChanged());
    }

    public function allowFeePaymentsProvider()
    {
        return [
            'refused' => [
                Entity::APPLICATION_STATUS_REFUSED,
                Licence::LICENCE_STATUS_REFUSED,
                false,
            ],
            'withdrawn' => [
                Entity::APPLICATION_STATUS_WITHDRAWN,
                Licence::LICENCE_STATUS_WITHDRAWN,
                false,
            ],
            'ntu' => [
                Entity::APPLICATION_STATUS_NOT_TAKEN_UP,
                Licence::LICENCE_STATUS_NOT_TAKEN_UP,
                false,
            ],
            'licence surrendered' => [
                Entity::APPLICATION_STATUS_UNDER_CONSIDERATION,
                Licence::LICENCE_STATUS_SURRENDERED,
                false,
            ],
            'licence terminated' => [
                Entity::APPLICATION_STATUS_UNDER_CONSIDERATION,
                Licence::LICENCE_STATUS_TERMINATED,
                false,
            ],
            'licence revoked' => [
                Entity::APPLICATION_STATUS_UNDER_CONSIDERATION,
                Licence::LICENCE_STATUS_REVOKED,
                false,
            ],
            'licence cns' => [
                Entity::APPLICATION_STATUS_UNDER_CONSIDERATION,
                Licence::LICENCE_STATUS_CONTINUATION_NOT_SOUGHT,
                false,
            ],
            'under consideration' => [
                Entity::APPLICATION_STATUS_UNDER_CONSIDERATION,
                Licence::LICENCE_STATUS_UNDER_CONSIDERATION,
                true,
            ],
        ];
    }

    /**
     * @dataProvider isUnderConsiderationProvider
     */
    public function testIsUnderConsideration($status, $expected)
    {
        $sut = m::mock(Entity::class)->makePartial();

        $sut->shouldReceive('getStatus->getId')->once()->andReturn($status);
        $this->assertEquals($expected, $sut->isUnderConsideration());
    }

    public function isUnderConsiderationProvider()
    {
        return [
            [Entity::APPLICATION_STATUS_NOT_SUBMITTED, false],
            [Entity::APPLICATION_STATUS_GRANTED, false],
            [Entity::APPLICATION_STATUS_UNDER_CONSIDERATION, true],
            [Entity::APPLICATION_STATUS_VALID, false],
            [Entity::APPLICATION_STATUS_WITHDRAWN, false],
            [Entity::APPLICATION_STATUS_REFUSED, false],
            [Entity::APPLICATION_STATUS_NOT_TAKEN_UP, false],
        ];
    }

    /**
     * @dataProvider providerDatesAsString
     */
    public function testGetOutOfOppositionDateAsString($input, $expected)
    {
        /** @var Entity $application */
        $application = m::mock(Entity::class)->makePartial();

        $application->shouldReceive('getOutOfOppositionDate')
            ->andReturn($input);

        $oooDate = $application->getOutOfOppositionDateAsString();
        $this->assertEquals($expected, $oooDate);
    }

    /**
     * @dataProvider providerDatesAsString
     */
    public function testGetOutOfRepresentationDateAsString($input, $expected)
    {
        /** @var Entity $application */
        $application = m::mock(Entity::class)->makePartial();

        $application->shouldReceive('getOutOfRepresentationDate')
            ->andReturn($input);

        $oorDate = $application->getOutOfRepresentationDateAsString();
        $this->assertEquals($expected, $oorDate);

    }

    public function providerDatesAsString()
    {
        return [
            [Entity::NOT_APPLICABLE, Entity::NOT_APPLICABLE],
            [new DateTime('2015-01-01'), '01/01/2015'],
            [Entity::UNKNOWN, Entity::UNKNOWN],
            ['', ''],
            [null, '']
        ];
    }

    /**
     * @dataProvider dpTestGetLicenceTypeShortCode
     * @param string $licenceType
     * @param string $shortCode
     */
    public function testGetLicenceTypeShortCode($licenceType, $shortCode)
    {
        $licence = new Licence(new Organisation(), new RefData());
        $application = new Entity($licence, new RefData(), false);
        $application->setLicenceType((new RefData())->setId($licenceType));

        $this->assertSame($shortCode, $application->getLicenceTypeShortCode());
    }

    public function dpTestGetLicenceTypeShortCode()
    {
        return [
            ['ltyp_r', 'R'],
            ['ltyp_si', 'SI'],
            ['ltyp_sn', 'SN'],
            ['ltyp_sr', 'SR'],
            ['XXXX', null],
        ];
    }

    public function testGetContextValue()
    {
        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setLicNo(111);

        $entity = $this->instantiate(Entity::class);

        $entity->setLicence($licence);

        $this->assertEquals(111, $entity->getContextValue());
    }
}
