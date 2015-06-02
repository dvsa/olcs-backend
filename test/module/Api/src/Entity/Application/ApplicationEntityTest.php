<?php

namespace Dvsa\OlcsTest\Api\Entity\Application;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Application\Application as Entity;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Mockery as m;

/**
 * Application Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class ApplicationEntityTest extends EntityTester
{
    public function setUp()
    {
        $this->entity = $this->instantiate($this->entityClass);
    }

    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

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
     * @group applicationEntity1
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
     * @expectedException Dvsa\Olcs\Api\Domain\Exception\ValidationException
     */
    public function testUpdateFinancialHistoryNotValid(
        $bankrupt,
        $liquidation,
        $receivership,
        $administration,
        $disqualified,
        $insolvencyDetails
    ) {

        $this->entity->updateFinancialHistory(
            $bankrupt,
            $liquidation,
            $receivership,
            $administration,
            $disqualified,
            $insolvencyDetails
        );
    }

    public function notValidDataProvider()
    {
        return [
            ['Y', 'N', 'N', 'N', 'N', '123'],
            ['Y', 'N', 'N', 'N', 'N', ''],
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
        $insolvencyDetails
    ) {

        $this->assertTrue(
            $this->entity->updateFinancialHistory(
                $bankrupt,
                $liquidation,
                $receivership,
                $administration,
                $disqualified,
                $insolvencyDetails
            )
        );
        $this->assertEquals($this->entity->getBankrupt(), $bankrupt);
        $this->assertEquals($this->entity->getLiquidation(), $liquidation);
        $this->assertEquals($this->entity->getReceivership(), $receivership);
        $this->assertEquals($this->entity->getAdministration(), $administration);
        $this->assertEquals($this->entity->getDisqualified(), $disqualified);
        $this->assertEquals($this->entity->getInsolvencyDetails(), $insolvencyDetails);
    }

    public function validDataProvider()
    {
        return [
            ['N', 'N', 'N', 'N', 'N', ''],
            ['Y', 'N', 'N', 'N', 'N', str_repeat('X', 200)],
        ];
    }
}
