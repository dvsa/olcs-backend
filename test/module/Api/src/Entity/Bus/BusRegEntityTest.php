<?php

namespace Dvsa\OlcsTest\Api\Entity\Bus;

use Dvsa\Olcs\Api\Entity\Publication\Publication as PublicationEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as Entity;
use Dvsa\Olcs\Api\Entity\Bus\BusRegOtherService as BusRegOtherServiceEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusNoticePeriod as BusNoticePeriodEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusShortNotice as BusShortNoticeEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;

/**
 * BusReg Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class BusRegEntityTest extends EntityTester
{
    public function setUp()
    {
        /** @var \Dvsa\Olcs\Api\Entity\Bus\BusReg entity */
        $this->entity = $this->instantiate($this->entityClass);
    }

    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    private function getAssertionsForCanEditIsTrue()
    {
        $id = 15;
        $regNo = 12345;

        //the bus reg entity which exists on the licence
        $licenceBusReg = new Entity();
        $licenceBusReg->setId($id);

        $licenceEntityMock = m::mock(LicenceEntity::class);
        $licenceEntityMock->shouldReceive('getLatestBusVariation')->once()->with($regNo)->andReturn($licenceBusReg);

        $this->entity->setRegNo($regNo);
        $this->entity->setId($id);
        $this->entity->setLicence($licenceEntityMock);
        $this->entity->setIsTxcApp('N');
    }

    private function getAssertionsForCanEditIsFalseDueToVariation()
    {
        $id = 15;
        $otherBusId = 16;
        $regNo = 12345;

        //the bus reg entity which exists on the licence
        $licenceBusReg = new Entity();
        $licenceBusReg->setId($otherBusId);

        $licenceEntityMock = m::mock(LicenceEntity::class);
        $licenceEntityMock->shouldReceive('getLatestBusVariation')->once()->with($regNo)->andReturn($licenceBusReg);

        $this->entity->setRegNo($regNo);
        $this->entity->setId($id);
        $this->entity->setLicence($licenceEntityMock);
        $this->entity->setIsTxcApp('N');
    }

    /**
     * Test isReadOnly
     *
     * @param bool $isLatestVariation
     * @param string $status
     * @param bool $expected
     *
     * @dataProvider isReadOnlyProvider
     */
    public function testIsReadOnly($isLatestVariation, $status, $expected)
    {
        $busRegStatus = new RefDataEntity($status);

        $busReg = m::mock(Entity::class)->makePartial();
        $busReg->shouldReceive('isLatestVariation')->once()->andReturn($isLatestVariation);
        $busReg->setStatus($busRegStatus);

        $this->assertEquals($expected, $busReg->isReadOnly());
    }

    /**
     * Data provider for isFromEbsr
     *
     * @return array
     */
    public function isReadOnlyProvider()
    {
        return [
            [false, Entity::STATUS_NEW, true],
            [false, Entity::STATUS_VAR, true],
            [false, Entity::STATUS_CANCEL, true],
            [false, Entity::STATUS_ADMIN, true],
            [false, Entity::STATUS_REGISTERED, true],
            [false, Entity::STATUS_REFUSED, true],
            [false, Entity::STATUS_WITHDRAWN, true],
            [false, Entity::STATUS_CNS, true],
            [false, Entity::STATUS_CANCELLED, true],
            [true, Entity::STATUS_NEW, false],
            [true, Entity::STATUS_VAR, false],
            [true, Entity::STATUS_CANCEL, false],
            [true, Entity::STATUS_ADMIN, false],
            [true, Entity::STATUS_REGISTERED, true],
            [true, Entity::STATUS_REFUSED, false],
            [true, Entity::STATUS_WITHDRAWN, false],
            [true, Entity::STATUS_CNS, false],
            [true, Entity::STATUS_CANCELLED, true]
        ];
    }

    /**
     * Test isFromEbsr
     *
     * @param string $isTxcApp
     * @param bool $expected
     *
     * @dataProvider isFromEbsrProvider
     */
    public function testIsFromEbsr($isTxcApp, $expected)
    {
        $busReg = new Entity();
        $busReg->setIsTxcApp($isTxcApp);

        $this->assertEquals($expected, $busReg->isFromEbsr());
    }

    /**
     * Data provider for isFromEbsr
     *
     * @return array
     */
    public function isFromEbsrProvider()
    {
        return [
            ['Y', true],
            ['N', false]
        ];
    }

    /**
     * Tests isScottishRules
     *
     * @param int $noticePeriodId
     * @param bool $expected
     *
     * @dataProvider isScottishRulesProvider
     */
    public function testIsScottishRules($noticePeriodId, $expected)
    {
        $noticePeriod = new BusNoticePeriodEntity();
        $noticePeriod->setId($noticePeriodId);

        $busReg = new Entity();
        $busReg->setBusNoticePeriod($noticePeriod);
        $this->assertEquals($expected, $busReg->isScottishRules());
    }

    /**
     * Data provider for isScottishRules
     *
     * @return array
     */
    public function isScottishRulesProvider()
    {
        return [
            [BusNoticePeriodEntity::NOTICE_PERIOD_SCOTLAND, true],
            [BusNoticePeriodEntity::NOTICE_PERIOD_OTHER, false]
        ];
    }

    /**
     * Tests calculated values
     */
    public function testGetCalculatedValues()
    {
        $id = 15;
        $regNo = 12345;

        //the bus reg entity which exists on the licence
        $licenceBusReg = new Entity();
        $licenceBusReg->setId($id);

        $licenceEntityMock = m::mock(LicenceEntity::class);
        $licenceEntityMock->shouldReceive('getLatestBusVariation')->once()->with($regNo)->andReturn($licenceBusReg);

        $noticePeriod = new BusNoticePeriodEntity();
        $noticePeriod->setId(BusNoticePeriodEntity::NOTICE_PERIOD_SCOTLAND);

        $sut = m::mock(Entity::class)->makePartial();
        $sut->shouldReceive('getRegNo')->once()->andReturn($regNo);
        $sut->shouldReceive('getId')->once()->andReturn($id);
        $sut->shouldReceive('getLicence')->once()->andReturn($licenceEntityMock);
        $sut->shouldReceive('isScottishRules')->once()->andReturn(true);
        $sut->shouldReceive('isReadOnly')->once()->andReturn(true);
        $sut->shouldReceive('isFromEbsr')->once()->andReturn(true);

        $result = $sut->getCalculatedValues();

        $this->assertEquals($result['licence'], null);
        $this->assertEquals($result['isLatestVariation'], true);
        $this->assertEquals($result['isScottishRules'], true);
        $this->assertEquals($result['isFromEbsr'], true);
        $this->assertEquals($result['isReadOnly'], true);
    }

    /**
     * Tests calculated bundle values
     */
    public function testGetCalculatedBundleValues()
    {
        $id = 15;
        $regNo = 12345;

        //the bus reg entity which exists on the licence
        $licenceBusReg = new Entity();
        $licenceBusReg->setId($id);

        $licenceEntityMock = m::mock(LicenceEntity::class);
        $licenceEntityMock->shouldReceive('getLatestBusVariation')->once()->with($regNo)->andReturn($licenceBusReg);

        $sut = m::mock(Entity::class)->makePartial();
        $sut->shouldReceive('getRegNo')->once()->andReturn($regNo);
        $sut->shouldReceive('getId')->once()->andReturn($id);
        $sut->shouldReceive('getLicence')->once()->andReturn($licenceEntityMock);
        $sut->shouldReceive('isScottishRules')->once()->andReturn(true);
        $sut->shouldReceive('isReadOnly')->once()->andReturn(true);
        $sut->shouldReceive('isFromEbsr')->once()->andReturn(true);

        $result = $sut->getCalculatedBundleValues();

        $this->assertEquals($result['isLatestVariation'], true);
        $this->assertEquals($result['isScottishRules'], true);
        $this->assertEquals($result['isFromEbsr'], true);
        $this->assertEquals($result['isReadOnly'], true);
    }

    /**
     * Tests canDelete throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testCanDeleteThrowsException()
    {
        $this->getAssertionsForCanEditIsFalseDueToVariation();
        $this->entity->canDelete();

        return true;
    }

    /**
     * Tests can delete doesn't throw exception when isVariation is true
     */
    public function testCanDeleteTrue()
    {
        $this->getAssertionsForCanEditIsTrue();
        $this->assertEquals(true, $this->entity->canDelete());

        return true;
    }

    /**
     * Tests updateStops throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testUpdateStopsThrowsCanEditExceptionForEbsr()
    {
        $this->entity->setIsTxcApp('Y');
        $this->entity->updateStops(null, null, null, null, null, null, null, null, null);

        return true;
    }

    /**
     * Tests updateStops throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testUpdateStopsThrowsCanEditExceptionForLatestVariation()
    {
        $this->getAssertionsForCanEditIsFalseDueToVariation();
        $this->entity->updateStops(null, null, null, null, null, null, null, null, null);

        return true;
    }

    /**
     * Tests updateQualitySchemes throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testUpdateQualitySchemesThrowsCanEditExceptionForEbsr()
    {
        $this->entity->setIsTxcApp('Y');
        $this->entity->updateQualitySchemes(null, null, null, null, null);

        return true;
    }

    /**
     * Tests updateQualitySchemes throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testUpdateQualitySchemesThrowsCanEditExceptionForLatestVariation()
    {
        $this->getAssertionsForCanEditIsFalseDueToVariation();
        $this->entity->updateQualitySchemes(null, null, null, null, null);

        return true;
    }

    /**
     * Tests updateServiceDetails throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testUpdateServiceDetailsThrowsCanEditExceptionForEbsr()
    {
        $this->entity->setIsTxcApp('Y');
        $this->entity->updateServiceDetails(null, null, null, null, null, null, null, null, null, null);

        return true;
    }

    /**
     * Tests updateServiceDetails throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testUpdateServiceDetailsThrowsCanEditExceptionForLatestVariation()
    {
        $this->getAssertionsForCanEditIsFalseDueToVariation();
        $this->entity->updateServiceDetails(null, null, null, null, null, null, null, null, null, null);

        return true;
    }

    /**
     * Tests updateTaAuthority throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testUpdateTaAuthorityThrowsCanEditExceptionForEbsr()
    {
        $this->entity->setIsTxcApp('Y');
        $this->entity->updateTaAuthority(null);

        return true;
    }

    /**
     * Tests updateTaAuthority throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testUpdateTaAuthorityThrowsCanEditExceptionForLatestVariation()
    {
        $this->getAssertionsForCanEditIsFalseDueToVariation();
        $this->entity->updateTaAuthority(null);

        return true;
    }

    /**
     * Tests updateStops
     */
    public function testUpdateStops()
    {
        $useAllStops = 'Y';
        $hasManoeuvre = 'N';
        $manoeuvreDetail = 'string';
        $needNewStop = 'Y';
        $newStopDetail = 'string 2';
        $hasNotFixedStop = 'N';
        $notFixedStopDetail = 'string 3';
        $subsidised = 'Y';
        $subsidyDetail = 'string 4';

        $this->getAssertionsForCanEditIsTrue();

        $this->entity->updateStops(
            $useAllStops,
            $hasManoeuvre,
            $manoeuvreDetail,
            $needNewStop,
            $newStopDetail,
            $hasNotFixedStop,
            $notFixedStopDetail,
            $subsidised,
            $subsidyDetail
        );

        $this->assertEquals($useAllStops, $this->entity->getUseAllStops());
        $this->assertEquals($hasManoeuvre, $this->entity->getHasManoeuvre());
        $this->assertEquals($manoeuvreDetail, $this->entity->getManoeuvreDetail());
        $this->assertEquals($needNewStop, $this->entity->getNeedNewStop());
        $this->assertEquals($newStopDetail, $this->entity->getNewStopDetail());
        $this->assertEquals($hasNotFixedStop, $this->entity->getHasNotFixedStop());
        $this->assertEquals($notFixedStopDetail, $this->entity->getNotFixedStopDetail());
        $this->assertEquals($subsidised, $this->entity->getSubsidised());
        $this->assertEquals($subsidyDetail, $this->entity->getSubsidyDetail());

        return true;
    }

    /**
     * tests updateQualitySchemes
     */
    public function testUpdateQualitySchemes()
    {
        $isQualityPartnership = 'Y';
        $qualityPartnershipDetails = 'string';
        $qualityPartnershipFacilitiesUsed = 'N';
        $isQualityContract = 'Y';
        $qualityContractDetails = 'string 2';

        $this->getAssertionsForCanEditIsTrue();

        $this->entity->updateQualitySchemes(
            $isQualityPartnership,
            $qualityPartnershipDetails,
            $qualityPartnershipFacilitiesUsed,
            $isQualityContract,
            $qualityContractDetails
        );

        $this->assertEquals($isQualityPartnership, $this->entity->getIsQualityPartnership());
        $this->assertEquals($qualityPartnershipDetails, $this->entity->getQualityPartnershipDetails());
        $this->assertEquals($qualityPartnershipFacilitiesUsed, $this->entity->getQualityPartnershipFacilitiesUsed());
        $this->assertEquals($isQualityContract, $this->entity->getIsQualityContract());
        $this->assertEquals($qualityContractDetails, $this->entity->getQualityContractDetails());

        return true;
    }

    /**
     * tests updateTaAuthority
     */
    public function testUpdateTaAuthority()
    {
        $stoppingArrangements = 'Stopping arrangements';

        $this->getAssertionsForCanEditIsTrue();

        $this->entity->updateTaAuthority(
            $stoppingArrangements
        );

        $this->assertEquals($stoppingArrangements, $this->entity->getStoppingArrangements());

        return true;
    }

    /**
     * @dataProvider provideUpdateServiceDetails
     *
     * @param $isSn
     * @param $rules
     * @param $variationNo
     * @param $receivedDate
     * @param $effectiveDate
     * @param null $parent
     */
    public function testUpdateServiceDetails($isSn, $rules, $variationNo, $receivedDate, $effectiveDate, $parent = null)
    {
        $serviceNo = 12345;
        $startPoint = 'start point';
        $finishPoint = 'finish point';
        $via = 'via';
        $otherDetails = 'other details';
        $endDate = null;
        $busNoticePeriod = 2;

        $busRules = new BusNoticePeriodEntity();
        $busRules->setCancellationPeriod($rules['cancellationPeriod']);
        $busRules->setStandardPeriod($rules['standardPeriod']);

        $this->getAssertionsForCanEditIsTrue();

        $this->entity->setVariationNo($variationNo);
        $this->entity->setParent($parent);

        $this->entity->updateServiceDetails(
            $serviceNo,
            $startPoint,
            $finishPoint,
            $via,
            $otherDetails,
            $receivedDate,
            $effectiveDate,
            $endDate,
            $busNoticePeriod,
            $busRules
        );

        $this->assertEquals($serviceNo, $this->entity->getServiceNo());
        $this->assertEquals($startPoint, $this->entity->getStartPoint());
        $this->assertEquals($finishPoint, $this->entity->getFinishPoint());
        $this->assertEquals($via, $this->entity->getVia());
        $this->assertEquals($isSn, $this->entity->getIsShortNotice());
    }

    /**
     * Data provider for updateServiceDetails
     *
     * @return array
     */
    public function provideUpdateServiceDetails()
    {
        $scotRules = [
            'standardPeriod' => 56,
            'cancellationPeriod' => 90
        ];

        $otherRules = [
            'standardPeriod' => 56,
            'cancellationPeriod' => 0
        ];

        $parent = new Entity();
        $parent->setEffectiveDate(new \DateTime('2014-06-11'));

        $sn = 'Y';
        $notSn = 'N';

        return [
            //S1
            [$sn, $otherRules, 0, '2014-05-31', '2014-07-01'],
            [$sn, $otherRules, 0, '2014-05-31', '2014-07-26'],
            [$notSn, $otherRules, 0, '2014-05-31', '2014-07-27'],
            [$notSn, $otherRules, 0, '2014-05-31', '2014-08-28'],
            [$sn, $otherRules, 1, '2014-05-31', '2014-07-01'],
            [$sn, $otherRules, 1, '2014-05-31', '2014-07-26'],
            [$notSn, $otherRules, 1, '2014-05-31', '2014-07-27'],
            [$notSn, $otherRules, 1, '2014-05-31', '2014-08-28'],
            //S2
            [$sn, $scotRules, 0, '2014-05-31', '2014-07-01'],
            [$sn, $scotRules, 0, '2014-05-31', '2014-07-26'],
            [$notSn, $scotRules, 0, '2014-05-31', '2014-07-27'],
            [$notSn, $scotRules, 0, '2014-05-31', '2014-08-28'],
            //S3
            [$sn, $scotRules, 1, '2014-07-15', '2014-07-21', $parent],
            [$sn, $scotRules, 1, '2014-07-15', '2014-09-08', $parent],
            [$sn, $scotRules, 1, '2014-07-15', '2014-09-09', $parent],
            [$notSn, $scotRules, 1, '2014-07-15', '2014-09-10', $parent],
            //S4
            [$sn, $scotRules, 1, '2014-08-01', '2014-08-12', $parent],
            [$sn, $scotRules, 1, '2014-08-01', '2014-09-25', $parent],
            [$sn, $scotRules, 1, '2014-08-01', '2014-09-26', $parent],
            [$notSn, $scotRules, 1, '2014-08-01', '2015-09-30', $parent],
            //S5
            [$sn, $scotRules, 1, '2014-07-01', '2014-08-12', $parent],
            [$sn, $scotRules, 1, '2014-07-01', '2014-09-08', $parent],
            [$sn, $scotRules, 1, '2014-07-01', '2014-09-09', $parent],
            [$notSn, $scotRules, 1, '2014-07-01', '2015-09-30', $parent],
            //error cases
            [$notSn, $otherRules, 0, '2014-09-30', ''],
            [$notSn, $otherRules, 0, '', '2014-09-30'],
            [$notSn, $scotRules, 1, '2015-02-09', '2016-09-30', $parent],
            [$notSn, $scotRules, 1, '2014-06-11', '2014-08-11']
        ];
    }

    public function testCreateNew()
    {
        $latestBusRouteNo = 3;
        $licNo = '123';

        $licenceEntityMock = m::mock(LicenceEntity::class);
        $licenceEntityMock->shouldReceive('getLatestBusRouteNo')->once()->andReturn($latestBusRouteNo);
        $licenceEntityMock->shouldReceive('getLicNo')->once()->andReturn($licNo);

        $status = new RefDataEntity();
        $status->setId(Entity::STATUS_VAR);

        $revertStatus = new RefDataEntity();
        $revertStatus->setId(Entity::STATUS_VAR);

        $subsidised = new RefDataEntity();
        $subsidised->setId(Entity::SUBSIDY_NO);

        $busNoticePeriod = new BusNoticePeriodEntity();
        $busNoticePeriod->setId(BusNoticePeriodEntity::NOTICE_PERIOD_OTHER);

        $busReg = Entity::createNew($licenceEntityMock, $status, $revertStatus, $subsidised, $busNoticePeriod);

        // test some values from $defaultAll
        $this->assertEquals('N', $busReg->getIsShortNotice());
        $this->assertNull($busReg->getEndDate());

        // test some database metadata
        $this->assertNull($busReg->getId());
        $this->assertEquals(1, $busReg->getVersion());

        // test new specific values
        $this->assertEquals($licenceEntityMock, $busReg->getLicence());
        $this->assertEquals($status, $busReg->getStatus());
        $this->assertEquals($revertStatus, $busReg->getRevertStatus());
        $this->assertEquals($subsidised, $busReg->getSubsidised());
        $this->assertEquals($busNoticePeriod, $busReg->getBusNoticePeriod());
        $this->assertEquals($latestBusRouteNo + 1, $busReg->getRouteNo());
        $this->assertEquals('123/4', $busReg->getRegNo());

        // test some short notice values
        $busRegSN = $busReg->getShortNotice();
        $this->assertInstanceOf(BusShortNoticeEntity::class, $busRegSN);
        $this->assertNull($busRegSN->getId());
        $this->assertEquals(1, $busRegSN->getVersion());
        $this->assertEquals(0, $busRegSN->getBankHolidayChange());
        $this->assertNull($busRegSN->getHolidayDetail());
        $this->assertEquals($busReg, $busRegSN->getBusReg());
    }

    public function testCreateVariation()
    {
        $id = 15;
        $regNo = 12345;
        $variationNo = 2;

        $status = new RefDataEntity();
        $status->setId(Entity::STATUS_VAR);

        $revertStatus = new RefDataEntity();
        $revertStatus->setId(Entity::STATUS_VAR);

        // the bus reg entity which exists on the licence
        $licenceBusReg = new Entity();
        $licenceBusReg->setId($id);
        $licenceBusReg->setVariationNo($variationNo);

        $licenceEntityMock = m::mock(LicenceEntity::class);
        $licenceEntityMock->shouldReceive('getLatestBusVariation')->once()->with($regNo, [])->andReturn($licenceBusReg);

        $shortNotice = new BusShortNoticeEntity();
        $shortNotice->setId(100);

        $otherService1 = new BusRegOtherServiceEntity($licenceBusReg, 'otherService1');
        $otherService1->setId(201);
        $otherService1->setOlbsKey('olbs-key');

        $otherService2 = new BusRegOtherServiceEntity($licenceBusReg, 'otherService2');
        $otherService2->setId(202);

        // set up the bus reg entity based on which a variation is to be created
        $this->entity->setId($id);
        $this->entity->setRegNo($regNo);
        $this->entity->setLicence($licenceEntityMock);
        $this->entity->setVersion(10);
        $this->entity->setIsShortNotice('Y');
        $this->entity->setShortNotice($shortNotice);
        $this->entity->setEndDate(new \DateTime);
        $this->entity->addVariationReasons(new RefDataEntity());
        $this->entity->addOtherServices($otherService1);
        $this->entity->addOtherServices($otherService2);
        $this->entity->setStatus(new RefDataEntity(Entity::STATUS_REGISTERED));
        $this->entity->setOlbsKey(123);

        $busReg = $this->entity->createVariation($status, $revertStatus);

        // test some values from $defaultAll
        $this->assertEquals('N', $busReg->getIsShortNotice());
        $this->assertNull($busReg->getEndDate());

        // test some database metadata
        $this->assertNull($busReg->getId());
        $this->assertNull($busReg->getVersion());
        $this->assertNull($busReg->getVariationReasons());
        $this->assertNull($busReg->getOlbsKey());

        // test variation specific values
        $this->assertEquals($this->entity, $busReg->getParent());
        $this->assertEquals($status, $busReg->getStatus());
        $this->assertInstanceOf(\DateTime::class, $busReg->getStatusChangeDate());
        $this->assertEquals($revertStatus, $busReg->getRevertStatus());
        $this->assertEquals($variationNo + 1, $busReg->getVariationNo());

        // test some short notice values
        $busRegSN = $busReg->getShortNotice();
        $this->assertInstanceOf(BusShortNoticeEntity::class, $busRegSN);
        $this->assertNull($busRegSN->getId());
        $this->assertEquals(1, $busRegSN->getVersion());
        $this->assertEquals(0, $busRegSN->getBankHolidayChange());
        $this->assertNull($busRegSN->getHolidayDetail());
        $this->assertEquals($busReg, $busRegSN->getBusReg());

        // test other services
        $this->assertEquals(2, $busReg->getOtherServices()->count());
        $this->assertNull($busReg->getOtherServices()->first()->getId());
        $this->assertNull($busReg->getOtherServices()->first()->getVersion());
        $this->assertNull($busReg->getOtherServices()->first()->getOlbsKey());
        $this->assertEquals($busReg, $busReg->getOtherServices()->first()->getBusReg());
        $this->assertEquals('otherService1', $busReg->getOtherServices()->first()->getServiceNo());
        $this->assertEquals('otherService2', $busReg->getOtherServices()->last()->getServiceNo());

        return true;
    }

    /**
     * Tests updateServiceRegister
     */
    public function testUpdateServiceRegister()
    {
        $timetableAcceptable = 'Y';
        $mapSupplied = 'Y';
        $routeDescription = 'string';
        $trcConditionChecked = 'Y';
        $trcNotes = 'string 2';
        $copiedToLaPte = 'Y';
        $laShortNote = 'Y';
        $opNotifiedLaPte = 'Y';
        $applicationSigned = 'Y';

        $this->getAssertionsForCanEditIsTrue();

        $this->entity->updateServiceRegister(
            $trcConditionChecked,
            $trcNotes,
            $copiedToLaPte,
            $laShortNote,
            $opNotifiedLaPte,
            $applicationSigned,
            $timetableAcceptable,
            $mapSupplied,
            $routeDescription
        );

        $this->assertEquals($trcConditionChecked, $this->entity->getTrcConditionChecked());
        $this->assertEquals($trcNotes, $this->entity->getTrcNotes());
        $this->assertEquals($copiedToLaPte, $this->entity->getCopiedToLaPte());
        $this->assertEquals($laShortNote, $this->entity->getLaShortNote());
        $this->assertEquals($opNotifiedLaPte, $this->entity->getOpNotifiedLaPte());
        $this->assertEquals($applicationSigned, $this->entity->getApplicationSigned());
        $this->assertEquals($timetableAcceptable, $this->entity->getTimetableAcceptable());
        $this->assertEquals($mapSupplied, $this->entity->getMapSupplied());
        $this->assertEquals($routeDescription, $this->entity->getRouteDescription());

        return true;
    }

    /**
     * Tests updateServiceRegister throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testUpdateServiceRegisterThrowsExceptionForLatestVariation()
    {
        $this->getAssertionsForCanEditIsFalseDueToVariation();
        $this->entity->updateServiceRegister(null, null, null, null, null, null, null, null, null);

        return true;
    }

    private function getAssertionsForCanMakeDecisionIsTrue()
    {
        $id = 15;
        $regNo = 12345;

        //the bus reg entity which exists on the licence
        $licenceBusReg = new Entity();
        $licenceBusReg->setId($id);

        $licenceEntityMock = m::mock(LicenceEntity::class);
        $licenceEntityMock->shouldReceive('getLatestBusVariation')->once()->with($regNo)->andReturn($licenceBusReg);

        $this->entity->setRegNo($regNo);
        $this->entity->setId($id);
        $this->entity->setLicence($licenceEntityMock);
    }

    private function getAssertionsForCanMakeDecisionIsFalse()
    {
        $id = 15;
        $otherBusId = 16;
        $regNo = 12345;

        //the bus reg entity which exists on the licence
        $licenceBusReg = new Entity();
        $licenceBusReg->setId($otherBusId);

        $licenceEntityMock = m::mock(LicenceEntity::class);
        $licenceEntityMock->shouldReceive('getLatestBusVariation')->once()->with($regNo)->andReturn($licenceBusReg);

        $this->entity->setRegNo($regNo);
        $this->entity->setId($id);
        $this->entity->setLicence($licenceEntityMock);
    }

    public function testResetStatus()
    {
        $this->getAssertionsForCanMakeDecisionIsTrue();

        $status = new RefDataEntity();
        $status->setId(Entity::STATUS_REGISTERED);

        $revertStatus = new RefDataEntity();
        $revertStatus->setId(Entity::STATUS_VAR);

        $this->entity->setStatus($status);
        $this->entity->setRevertStatus($revertStatus);

        $this->entity->resetStatus();

        $this->assertEquals($revertStatus, $this->entity->getStatus());
        $this->assertEquals($status, $this->entity->getRevertStatus());
        $this->assertInstanceOf(\DateTime::class, $this->entity->getStatusChangeDate());
    }

    /**
     * Tests resetStatus throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testResetStatusThrowsCanMakeDecisionException()
    {
        $this->getAssertionsForCanMakeDecisionIsFalse();
        $this->entity->resetStatus(null);

        return true;
    }

    public function testCancelByAdmin()
    {
        $this->getAssertionsForCanMakeDecisionIsTrue();

        $status = new RefDataEntity();
        $status->setId(Entity::STATUS_REGISTERED);
        $this->entity->setStatus($status);

        $newStatus = new RefDataEntity();
        $newStatus->setId(Entity::STATUS_ADMIN);

        $reason = 'testing';

        $this->entity->cancelByAdmin($newStatus, $reason);

        $this->assertEquals($newStatus, $this->entity->getStatus());
        $this->assertEquals($status, $this->entity->getRevertStatus());
        $this->assertInstanceOf(\DateTime::class, $this->entity->getStatusChangeDate());
        $this->assertEquals($reason, $this->entity->getReasonCancelled());
    }

    /**
     * Tests cancelByAdmin throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testCancelByAdminThrowsCanMakeDecisionException()
    {
        $this->getAssertionsForCanMakeDecisionIsFalse();

        $status = new RefDataEntity();
        $status->setId(Entity::STATUS_ADMIN);

        $this->entity->cancelByAdmin($status, null);

        return true;
    }

    /**
     * Tests cancelByAdmin throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\BadRequestException
     */
    public function testCancelByAdminThrowsIncorrectStatusException()
    {
        $this->getAssertionsForCanMakeDecisionIsTrue();

        $status = new RefDataEntity();
        $status->setId(Entity::STATUS_REGISTERED);

        $this->entity->cancelByAdmin($status, null);

        return true;
    }

    public function testWithdraw()
    {
        $this->getAssertionsForCanMakeDecisionIsTrue();

        $status = new RefDataEntity();
        $status->setId(Entity::STATUS_REGISTERED);
        $this->entity->setStatus($status);

        $newStatus = new RefDataEntity();
        $newStatus->setId(Entity::STATUS_WITHDRAWN);

        $reason = new RefDataEntity();

        $this->entity->withdraw($newStatus, $reason);

        $this->assertEquals($newStatus, $this->entity->getStatus());
        $this->assertEquals($status, $this->entity->getRevertStatus());
        $this->assertInstanceOf(\DateTime::class, $this->entity->getStatusChangeDate());
        $this->assertEquals($reason, $this->entity->getWithdrawnReason());
    }

    /**
     * Tests withdraw throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testWithdrawThrowsCanMakeDecisionException()
    {
        $this->getAssertionsForCanMakeDecisionIsFalse();

        $status = new RefDataEntity();
        $status->setId(Entity::STATUS_WITHDRAWN);

        $reason = new RefDataEntity();

        $this->entity->withdraw($status, $reason);

        return true;
    }

    /**
     * Tests withdraw throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\BadRequestException
     */
    public function testWithdrawThrowsIncorrectStatusException()
    {
        $this->getAssertionsForCanMakeDecisionIsTrue();

        $status = new RefDataEntity();
        $status->setId(Entity::STATUS_REGISTERED);

        $reason = new RefDataEntity();

        $this->entity->withdraw($status, $reason);

        return true;
    }

    public function testRefuse()
    {
        $this->getAssertionsForCanMakeDecisionIsTrue();

        $status = new RefDataEntity();
        $status->setId(Entity::STATUS_REGISTERED);
        $this->entity->setStatus($status);

        $newStatus = new RefDataEntity();
        $newStatus->setId(Entity::STATUS_REFUSED);

        $reason = 'testing';

        $this->entity->refuse($newStatus, $reason);

        $this->assertEquals($newStatus, $this->entity->getStatus());
        $this->assertEquals($status, $this->entity->getRevertStatus());
        $this->assertInstanceOf(\DateTime::class, $this->entity->getStatusChangeDate());
        $this->assertEquals($reason, $this->entity->getReasonRefused());
    }

    /**
     * Tests refuse throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testRefuseThrowsCanMakeDecisionException()
    {
        $this->getAssertionsForCanMakeDecisionIsFalse();

        $status = new RefDataEntity();
        $status->setId(Entity::STATUS_REFUSED);

        $this->entity->refuse($status, null);

        return true;
    }

    /**
     * Tests refuse throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\BadRequestException
     */
    public function testRefuseThrowsIncorrectStatusException()
    {
        $this->getAssertionsForCanMakeDecisionIsTrue();

        $status = new RefDataEntity();
        $status->setId(Entity::STATUS_REGISTERED);

        $this->entity->refuse($status, null);

        return true;
    }

    /**
     * @dataProvider provideCalculateNoticeDate
     * @param array $busNoticePeriodData
     * @param array $busRegData
     * @param string $expectedEffectiveDate
     */
    public function testRefuseByShortNotice($busNoticePeriodData, $busRegData, $expectedEffectiveDate)
    {
        $this->getAssertionsForCanMakeDecisionIsTrue();

        $shortNotice = m::mock(BusShortNoticeEntity::class)->makePartial();
        $shortNotice->shouldReceive('reset')->once()->andReturnSelf();
        $this->entity->setShortNotice($shortNotice);

        foreach ($busRegData as $key => $value) {
            if ($key === 'parent') {
                $parent = new Entity();
                $parent->setEffectiveDate($value['effectiveDate']);
                $value = $parent;
            }
            $this->entity->{'set' . ucwords($key)}($value);
        }

        $busNoticePeriod = new BusNoticePeriodEntity();
        foreach ($busNoticePeriodData as $key => $value) {
            $busNoticePeriod->{'set' . ucwords($key)}($value);
        }
        $this->entity->setBusNoticePeriod($busNoticePeriod);

        $reason = 'testing';

        $this->entity->refuseByShortNotice($reason);

        $this->assertEquals('Y', $this->entity->getShortNoticeRefused());
        $this->assertEquals($reason, $this->entity->getReasonSnRefused());
        $this->assertEquals('N', $this->entity->getIsShortNotice());

        $this->assertEquals($expectedEffectiveDate, $this->entity->getEffectiveDate());
    }

    public function provideCalculateNoticeDate()
    {
        $scotRules = [
            'standardPeriod' => 56,
            'cancellationPeriod' => 90
        ];

        $otherRules = [
            'standardPeriod' => 56,
            'cancellationPeriod' => 0
        ];

        $noRules = [
            'standardPeriod' => 0,
            'cancellationPeriod' => 0
        ];

        return [
            [
                $otherRules,
                ['receivedDate' => null],
                null
            ],
            [
                $scotRules,
                [
                    'variationNo' => 1,
                    'receivedDate' => '2015-02-09'
                ],
                null
            ],
            [
                $noRules,
                [
                    'variationNo' => 1,
                    'receivedDate' => '2015-02-09',
                    'effectiveDate' => '2015-03-31'
                ],
                '2015-03-31' //not a date time as the test data has stayed the same
            ],
            [
                $otherRules,
                [
                    'variationNo' => 0,
                    'receivedDate' => '2015-02-09'
                ],
                new \DateTime('2015-04-06')
            ],
            [
                $otherRules,
                [
                    'variationNo' => 1,
                    'receivedDate' => '2015-02-09'
                ],
                new \DateTime('2015-04-06')
            ],
            [
                $scotRules,
                [
                    'variationNo' => 0,
                    'receivedDate' => '2015-02-09'
                ],
                new \DateTime('2015-04-06')
            ],
            [
                $scotRules,
                [
                    'variationNo' => 1,
                    'receivedDate' => '2015-02-09',
                    'parent' => ['effectiveDate' => '2014-06-11']
                ],
                new \DateTime('2014-09-09')
            ],
        ];
    }

    /**
     * Tests refuseByShortNotice throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testRefuseByShortNoticeThrowsCanMakeDecisionException()
    {
        $this->getAssertionsForCanMakeDecisionIsFalse();

        $this->entity->refuseByShortNotice(null);

        return true;
    }

    public function testGrant()
    {
        $sut = m::mock(Entity::class)->makePartial();
        $sut->shouldReceive('canMakeDecision')->once()->andReturn(true);
        $sut->shouldReceive('isGrantable')->once()->andReturn(true);
        $sut->shouldReceive('getStatusForGrant')->once()->andReturn(Entity::STATUS_REGISTERED);

        $status = new RefDataEntity();
        $status->setId(Entity::STATUS_VAR);
        $sut->setStatus($status);

        $newStatus = new RefDataEntity();
        $newStatus->setId(Entity::STATUS_REGISTERED);

        $reasons = ['testing'];

        $sut->grant($newStatus, $reasons);

        $this->assertEquals($newStatus, $sut->getStatus());
        $this->assertEquals($status, $sut->getRevertStatus());
        $this->assertInstanceOf(\DateTime::class, $sut->getStatusChangeDate());
        $this->assertEquals($reasons, $sut->getVariationReasons());

        return true;
    }

    /**
     * Tests grant throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testGrantThrowsCanMakeDecisionException()
    {
        $this->getAssertionsForCanMakeDecisionIsFalse();

        $status = new RefDataEntity();
        $status->setId(Entity::STATUS_REGISTERED);

        $this->entity->grant($status, null);

        return true;
    }

    /**
     * Tests grant throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\BadRequestException
     */
    public function testGrantThrowsNotGrantableException()
    {
        $sut = m::mock(Entity::class)->makePartial();
        $sut->shouldReceive('canMakeDecision')->once()->andReturn(true);
        $sut->shouldReceive('isGrantable')->once()->andReturn(false);

        $status = new RefDataEntity();
        $status->setId(Entity::STATUS_REGISTERED);

        $sut->grant($status, null);

        return true;
    }

    /**
     * Tests grant throws exception correctly
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\BadRequestException
     */
    public function testGrantThrowsIncorrectStatusException()
    {
        $sut = m::mock(Entity::class)->makePartial();
        $sut->shouldReceive('canMakeDecision')->once()->andReturn(true);
        $sut->shouldReceive('isGrantable')->once()->andReturn(true);
        $sut->shouldReceive('getStatusForGrant')->once()->andReturn(Entity::STATUS_CANCELLED);

        $status = new RefDataEntity();
        $status->setId(Entity::STATUS_REGISTERED);

        $sut->grant($status, null);

        return true;
    }

    /**
     * @dataProvider getStatusForGrantDataProvider
     *
     * @param string $statusId
     * @param array $expected
     */
    public function testGetStatusForGrant($statusId, $expected)
    {
        $status = new RefDataEntity();
        $status->setId($statusId);
        $this->entity->setStatus($status);

        $this->assertEquals($expected, $this->entity->getStatusForGrant());
    }

    public function getStatusForGrantDataProvider()
    {
        return [
            [Entity::STATUS_NEW, Entity::STATUS_REGISTERED],
            [Entity::STATUS_VAR, Entity::STATUS_REGISTERED],
            [Entity::STATUS_CANCEL, Entity::STATUS_CANCELLED],
        ];
    }

    /**
     * @dataProvider isShortNoticeRefusedDataProvider
     *
     * @param string $shortNoticeRefused
     * @param array $expected
     */
    public function testIsShortNoticeRefused($shortNoticeRefused, $expected)
    {
        $this->entity->setShortNoticeRefused($shortNoticeRefused);

        $this->assertEquals($expected, $this->entity->isShortNoticeRefused());
    }

    public function isShortNoticeRefusedDataProvider()
    {
        return [
            [null, false],
            ['N', false],
            ['Y', true],
        ];
    }

    /**
     * @dataProvider getDecisionDataProvider
     *
     * @param string $statusId
     * @param string $shortNoticeRefused
     * @param bool $withWithdrawnReason
     * @param array $expected
     */
    public function testGetDecision($statusId, $shortNoticeRefused, $withWithdrawnReason, $expected)
    {
        $status = new RefDataEntity();
        $status->setId($statusId);
        $status->setDescription('Decision');

        $this->entity->setStatus($status);
        $this->entity->setShortNoticeRefused($shortNoticeRefused);

        if ($withWithdrawnReason) {
            $withdrawnReason = new RefDataEntity();
            $withdrawnReason->setDescription('Withdrawn Reason');
            $this->entity->setWithdrawnReason($withdrawnReason);
        }

        $this->entity->setReasonSnRefused('Reason SN Refused');
        $this->entity->setReasonRefused('Reason Refused');
        $this->entity->setReasonCancelled('Reason Cancelled');

        $this->assertEquals($expected, $this->entity->getDecision());
    }

    public function getDecisionDataProvider()
    {
        return [
            // registered
            [
                Entity::STATUS_REGISTERED,
                'N',
                false,
                null
            ],
            // refused - nonShortNoticeRefused
            [
                Entity::STATUS_REFUSED,
                'N',
                false,
                ['decision' => 'Decision', 'reason' => 'Reason Refused']
            ],
            // refused - ShortNoticeRefused
            [
                Entity::STATUS_REFUSED,
                'Y',
                false,
                ['decision' => 'Decision', 'reason' => 'Reason SN Refused']
            ],
            // cancelled
            [
                Entity::STATUS_CANCELLED,
                'N',
                false,
                ['decision' => 'Decision', 'reason' => 'Reason Cancelled']
            ],
            // admin cancelled
            [
                Entity::STATUS_ADMIN,
                'N',
                false,
                ['decision' => 'Decision', 'reason' => 'Reason Cancelled']
            ],
            // admin withdrawn with a reason
            [
                Entity::STATUS_WITHDRAWN,
                'N',
                true,
                ['decision' => 'Decision', 'reason' => 'Withdrawn Reason']
            ],
            // admin withdrawn without a reason
            [
                Entity::STATUS_WITHDRAWN,
                'N',
                false,
                ['decision' => 'Decision', 'reason' => null]
            ],
        ];
    }

    private function getAssertionsForIsGrantable()
    {
        $this->entity->setTimetableAcceptable('Y');
        $this->entity->setMapSupplied('Y');
        $this->entity->setTrcConditionChecked('Y');
        $this->entity->setCopiedToLaPte('Y');
        $this->entity->setLaShortNote('Y');
        $this->entity->setApplicationSigned('Y');
        $this->entity->setEffectiveDate('any value');
        $this->entity->setReceivedDate('any value');
        $this->entity->setServiceNo('any value');
        $this->entity->setStartPoint('any value');
        $this->entity->setFinishPoint('any value');
        $this->entity->setIsShortNotice('N');

        $this->entity->addBusServiceTypes('any value');
        $this->entity->addTrafficAreas('any value');
        $this->entity->addLocalAuthoritys('any value');

        $busNoticePeriod = new BusNoticePeriodEntity();
        $busNoticePeriod->setId(BusNoticePeriodEntity::NOTICE_PERIOD_OTHER);
        $this->entity->setBusNoticePeriod($busNoticePeriod);
    }

    public function testIsGrantable()
    {
        $this->getAssertionsForIsGrantable();

        // Grantable - Rule: Other - isShortNotice: N - Fee: none
        $this->assertEquals(true, $this->entity->isGrantable());
    }

    public function testIsGrantableWithoutTimetableAcceptable()
    {
        $this->getAssertionsForIsGrantable();

        // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
        // timetableAcceptable: N
        $this->entity->setTimetableAcceptable('N');
        $this->assertEquals(false, $this->entity->isGrantable());
    }

    public function testIsGrantableWithoutMapSupplied()
    {
        $this->getAssertionsForIsGrantable();

        // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
        // mapSupplied: N
        $this->entity->setMapSupplied('N');
        $this->assertEquals(false, $this->entity->isGrantable());
    }

    public function testIsGrantableWithoutTrcConditionChecked()
    {
        $this->getAssertionsForIsGrantable();

        // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
        // trcConditionChecked: N
        $this->entity->setTrcConditionChecked('N');
        $this->assertEquals(false, $this->entity->isGrantable());
    }

    public function testIsGrantableWithoutCopiedToLaPte()
    {
        $this->getAssertionsForIsGrantable();

        // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
        // copiedToLaPte: N
        $this->entity->setCopiedToLaPte('N');
        $this->assertEquals(false, $this->entity->isGrantable());
    }

    public function testIsGrantableWithoutLaShortNote()
    {
        $this->getAssertionsForIsGrantable();

        $this->entity->setIsShortNotice('Y');

        $busNoticePeriod = new BusNoticePeriodEntity();
        $busNoticePeriod->setId(BusNoticePeriodEntity::NOTICE_PERIOD_OTHER);
        $this->entity->setBusNoticePeriod($busNoticePeriod);

        // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
        // laShortNote: N
        $this->entity->setLaShortNote('N');
        $this->assertEquals(false, $this->entity->isGrantable());
    }

    public function testIsGrantableWithoutApplicationSigned()
    {
        $this->getAssertionsForIsGrantable();

        // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
        // applicationSigned: N
        $this->entity->setApplicationSigned('N');
        $this->assertEquals(false, $this->entity->isGrantable());
    }

    public function testIsGrantableWithoutEffectiveDate()
    {
        $this->getAssertionsForIsGrantable();

        // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
        // effectiveDate empty
        $this->entity->setEffectiveDate(null);
        $this->assertEquals(false, $this->entity->isGrantable());
    }

    public function testIsGrantableWithoutReceivedDate()
    {
        $this->getAssertionsForIsGrantable();

        // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
        // receivedDate empty
        $this->entity->setReceivedDate(null);
        $this->assertEquals(false, $this->entity->isGrantable());
    }

    public function testIsGrantableWithoutServiceNo()
    {
        $this->getAssertionsForIsGrantable();

        // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
        // serviceNo empty
        $this->entity->setServiceNo(null);
        $this->assertEquals(false, $this->entity->isGrantable());
    }

    public function testIsGrantableWithoutStartPoint()
    {
        $this->getAssertionsForIsGrantable();

        // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
        // startPoint empty
        $this->entity->setStartPoint(null);
        $this->assertEquals(false, $this->entity->isGrantable());
    }

    public function testIsGrantableWithoutFinishPoint()
    {
        $this->getAssertionsForIsGrantable();

        // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
        // finishPoint empty
        $this->entity->setFinishPoint(null);
        $this->assertEquals(false, $this->entity->isGrantable());
    }

    public function testIsGrantableWithoutBusServiceTypes()
    {
        $this->getAssertionsForIsGrantable();

        // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
        // busServiceTypes empty
        $this->entity->setBusServiceTypes(null);
        $this->assertEquals(false, $this->entity->isGrantable());
    }

    public function testIsGrantableWithoutTrafficAreas()
    {
        $this->getAssertionsForIsGrantable();

        // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
        // trafficAreas empty
        $this->entity->setTrafficAreas(null);
        $this->assertEquals(false, $this->entity->isGrantable());
    }

    public function testIsGrantableWithoutLocalAuthoritys()
    {
        $this->getAssertionsForIsGrantable();

        // nonGrantable - Rule: Other - isShortNotice: N - Fee: none
        // localAuthoritys empty
        $this->entity->setLocalAuthoritys(null);
        $this->assertEquals(false, $this->entity->isGrantable());
    }

    public function testIsGrantableWithNoticePeriodScotland()
    {
        $this->getAssertionsForIsGrantable();

        $busNoticePeriod = new BusNoticePeriodEntity();
        $busNoticePeriod->setId(BusNoticePeriodEntity::NOTICE_PERIOD_SCOTLAND);
        $this->entity->setBusNoticePeriod($busNoticePeriod);

        $shortNotice = new BusShortNoticeEntity();
        $shortNotice->setBankHolidayChange('Y');
        $this->entity->setShortNotice($shortNotice);

        // nonGrantable - Rule: Scotland - isShortNotice: N - Fee: none
        // extra data required from Scotland missing
        $this->entity->setOpNotifiedLaPte('N');
        $this->assertEquals(false, $this->entity->isGrantable());

        // nonGrantable - Rule: Scotland - isShortNotice: N - Fee: none
        // missing short notice info
        $this->entity->setOpNotifiedLaPte('Y');
        $this->assertEquals(true, $this->entity->isGrantable());
    }

    public function testIsGrantableWithFeePaid()
    {
        $this->getAssertionsForIsGrantable();

        $feeType = new FeeTypeEntity();

        $status = new RefDataEntity();
        $status->setId(FeeEntity::STATUS_PAID);

        $fee = new FeeEntity($feeType, 10, $status);

        // Grantable - Rule: Other - isShortNotice: N - Fee: paid
        $this->assertEquals(true, $this->entity->isGrantable($fee));
    }

    public function testIsGrantableWithFeeOutstanding()
    {
        $this->getAssertionsForIsGrantable();

        $feeType = new FeeTypeEntity();

        $status = new RefDataEntity();
        $status->setId(FeeEntity::STATUS_OUTSTANDING);

        $fee = new FeeEntity($feeType, 10, $status);

        // nonGrantable - Rule: Other - isShortNotice: N - Fee: outstanding
        $this->assertEquals(false, $this->entity->isGrantable($fee));
    }

    public function testIsGrantableWithoutShortNotice()
    {
        $this->getAssertionsForIsGrantable();

        // nonGrantable - Rule: Other - isShortNotice: Y - Fee: none
        // missing short notice details
        $this->entity->setIsShortNotice('Y');

        $busNoticePeriod = new BusNoticePeriodEntity();
        $busNoticePeriod->setId(BusNoticePeriodEntity::NOTICE_PERIOD_OTHER);
        $this->entity->setBusNoticePeriod($busNoticePeriod);

        $this->assertEquals(false, $this->entity->isGrantable());
    }

    public function testIsGrantableWithShortNoticeBankHoliday()
    {
        $this->getAssertionsForIsGrantable();

        $this->entity->setIsShortNotice('Y');

        $shortNotice = new BusShortNoticeEntity();
        $shortNotice->setBankHolidayChange('Y');
        $this->entity->setShortNotice($shortNotice);

        $busNoticePeriod = new BusNoticePeriodEntity();
        $busNoticePeriod->setId(BusNoticePeriodEntity::NOTICE_PERIOD_OTHER);
        $this->entity->setBusNoticePeriod($busNoticePeriod);

        // Grantable - Rule: Other - isShortNotice: Y - Fee: none
        // bankHolidayChange: Y
        $this->assertEquals(true, $this->entity->isGrantable());
    }

    public function testIsGrantableWithShortNoticeConnection()
    {
        $this->getAssertionsForIsGrantable();

        $this->entity->setIsShortNotice('Y');

        $shortNotice = new BusShortNoticeEntity();
        $shortNotice->setConnectionChange('Y');
        $this->entity->setShortNotice($shortNotice);

        $busNoticePeriod = new BusNoticePeriodEntity();
        $busNoticePeriod->setId(BusNoticePeriodEntity::NOTICE_PERIOD_OTHER);
        $this->entity->setBusNoticePeriod($busNoticePeriod);

        // nonGrantable - Rule: Other - isShortNotice: Y - Fee: none
        // connectionChange: Y, connectionDetail: empty
        $this->assertEquals(false, $this->entity->isGrantable());

        // Grantable - Rule: Other - isShortNotice: Y - Fee: none
        // connectionChange: Y, connectionDetail: not empty
        $shortNotice->setConnectionDetail('any value');
        $this->assertEquals(true, $this->entity->isGrantable());
    }

    public function testIsGrantableWithShortNoticeHoliday()
    {
        $this->getAssertionsForIsGrantable();

        $this->entity->setIsShortNotice('Y');

        $shortNotice = new BusShortNoticeEntity();
        $shortNotice->setHolidayChange('Y');
        $this->entity->setShortNotice($shortNotice);

        $busNoticePeriod = new BusNoticePeriodEntity();
        $busNoticePeriod->setId(BusNoticePeriodEntity::NOTICE_PERIOD_OTHER);
        $this->entity->setBusNoticePeriod($busNoticePeriod);

        // nonGrantable - Rule: Other - isShortNotice: Y - Fee: none
        // holidayChange: Y, holidayDetail: empty
        $this->assertEquals(false, $this->entity->isGrantable());

        // Grantable - Rule: Other - isShortNotice: Y - Fee: none
        // holidayChange: Y, holidayDetail: not empty
        $shortNotice->setHolidayDetail('any value');
        $this->assertEquals(true, $this->entity->isGrantable());
    }

    public function testIsGrantableWithShortNoticeNotAvailable()
    {
        $this->getAssertionsForIsGrantable();

        $this->entity->setIsShortNotice('Y');

        $shortNotice = new BusShortNoticeEntity();
        $shortNotice->setNotAvailableChange('Y');
        $this->entity->setShortNotice($shortNotice);

        $busNoticePeriod = new BusNoticePeriodEntity();
        $busNoticePeriod->setId(BusNoticePeriodEntity::NOTICE_PERIOD_OTHER);
        $this->entity->setBusNoticePeriod($busNoticePeriod);

        // nonGrantable - Rule: Other - isShortNotice: Y - Fee: none
        // notAvailableChange: Y, notAvailableDetail: empty
        $this->assertEquals(false, $this->entity->isGrantable());

        // Grantable - Rule: Other - isShortNotice: Y - Fee: none
        // notAvailableChange: Y, notAvailableDetail: not empty
        $shortNotice->setNotAvailableDetail('any value');
        $this->assertEquals(true, $this->entity->isGrantable());
    }

    public function testIsGrantableWithShortNoticePolice()
    {
        $this->getAssertionsForIsGrantable();

        $this->entity->setIsShortNotice('Y');

        $shortNotice = new BusShortNoticeEntity();
        $shortNotice->setPoliceChange('Y');
        $this->entity->setShortNotice($shortNotice);

        $busNoticePeriod = new BusNoticePeriodEntity();
        $busNoticePeriod->setId(BusNoticePeriodEntity::NOTICE_PERIOD_OTHER);
        $this->entity->setBusNoticePeriod($busNoticePeriod);

        // nonGrantable - Rule: Other - isShortNotice: Y - Fee: none
        // policeChange: Y, policeDetail: empty
        $this->assertEquals(false, $this->entity->isGrantable());

        // Grantable - Rule: Other - isShortNotice: Y - Fee: none
        // policeChange: Y, policeDetail: not empty
        $shortNotice->setPoliceDetail('any value');
        $this->assertEquals(true, $this->entity->isGrantable());
    }

    public function testIsGrantableWithShortNoticeReplacement()
    {
        $this->getAssertionsForIsGrantable();

        $this->entity->setIsShortNotice('Y');

        $shortNotice = new BusShortNoticeEntity();
        $shortNotice->setReplacementChange('Y');
        $this->entity->setShortNotice($shortNotice);

        $busNoticePeriod = new BusNoticePeriodEntity();
        $busNoticePeriod->setId(BusNoticePeriodEntity::NOTICE_PERIOD_OTHER);
        $this->entity->setBusNoticePeriod($busNoticePeriod);

        // nonGrantable - Rule: Other - isShortNotice: Y - Fee: none
        // replacementChange: Y, replacementDetail: empty
        $this->assertEquals(false, $this->entity->isGrantable());

        // Grantable - Rule: Other - isShortNotice: Y - Fee: none
        // replacementChange: Y, replacementDetail: not empty
        $shortNotice->setReplacementDetail('any value');
        $this->assertEquals(true, $this->entity->isGrantable());
    }

    public function testIsGrantableWithShortNoticeSpecialOccasion()
    {
        $this->getAssertionsForIsGrantable();

        $this->entity->setIsShortNotice('Y');

        $shortNotice = new BusShortNoticeEntity();
        $shortNotice->setSpecialOccasionChange('Y');
        $this->entity->setShortNotice($shortNotice);

        $busNoticePeriod = new BusNoticePeriodEntity();
        $busNoticePeriod->setId(BusNoticePeriodEntity::NOTICE_PERIOD_OTHER);
        $this->entity->setBusNoticePeriod($busNoticePeriod);

        // nonGrantable - Rule: Other - isShortNotice: Y - Fee: none
        // specialOccasionChange: Y, specialOccasionDetail: empty
        $this->assertEquals(false, $this->entity->isGrantable());

        // Grantable - Rule: Other - isShortNotice: Y - Fee: none
        // specialOccasionChange: Y, specialOccasionDetail: not empty
        $shortNotice->setSpecialOccasionDetail('any value');
        $this->assertEquals(true, $this->entity->isGrantable());
    }

    public function testIsGrantableWithShortNoticeTimetable()
    {
        $this->getAssertionsForIsGrantable();

        $this->entity->setIsShortNotice('Y');

        $shortNotice = new BusShortNoticeEntity();
        $shortNotice->setTimetableChange('Y');
        $this->entity->setShortNotice($shortNotice);

        $busNoticePeriod = new BusNoticePeriodEntity();
        $busNoticePeriod->setId(BusNoticePeriodEntity::NOTICE_PERIOD_OTHER);
        $this->entity->setBusNoticePeriod($busNoticePeriod);

        // nonGrantable - Rule: Other - isShortNotice: Y - Fee: none
        // timetableChange: Y, timetableDetail: empty
        $this->assertEquals(false, $this->entity->isGrantable());

        // Grantable - Rule: Other - isShortNotice: Y - Fee: none
        // timetableChange: Y, timetableDetail: not empty
        $shortNotice->setTimetableDetail('any value');
        $this->assertEquals(true, $this->entity->isGrantable());
    }

    public function testIsGrantableWithShortNoticeTrc()
    {
        $this->getAssertionsForIsGrantable();

        $this->entity->setIsShortNotice('Y');

        $shortNotice = new BusShortNoticeEntity();
        $shortNotice->setTrcChange('Y');
        $this->entity->setShortNotice($shortNotice);

        $busNoticePeriod = new BusNoticePeriodEntity();
        $busNoticePeriod->setId(BusNoticePeriodEntity::NOTICE_PERIOD_OTHER);
        $this->entity->setBusNoticePeriod($busNoticePeriod);

        // nonGrantable - Rule: Other - isShortNotice: Y - Fee: none
        // trcChange: Y, trcDetail: empty
        $this->assertEquals(false, $this->entity->isGrantable());

        // Grantable - Rule: Other - isShortNotice: Y - Fee: none
        // trcChange: Y, trcDetail: not empty
        $shortNotice->setTrcDetail('any value');
        $this->assertEquals(true, $this->entity->isGrantable());
    }

    public function testIsGrantableWithShortNoticeUnforseen()
    {
        $this->getAssertionsForIsGrantable();

        $this->entity->setIsShortNotice('Y');

        $shortNotice = new BusShortNoticeEntity();
        $shortNotice->setUnforseenChange('Y');
        $this->entity->setShortNotice($shortNotice);

        $busNoticePeriod = new BusNoticePeriodEntity();
        $busNoticePeriod->setId(BusNoticePeriodEntity::NOTICE_PERIOD_OTHER);
        $this->entity->setBusNoticePeriod($busNoticePeriod);

        // nonGrantable - Rule: Other - isShortNotice: Y - Fee: none
        // unforseenChange: Y, unforseenDetail: empty
        $this->assertEquals(false, $this->entity->isGrantable());

        // Grantable - Rule: Other - isShortNotice: Y - Fee: none
        // unforseenChange: Y, unforseenDetail: not empty
        $shortNotice->setUnforseenDetail('any value');
        $this->assertEquals(true, $this->entity->isGrantable());
    }

    public function testGetContextValue()
    {
        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setLicNo(111);

        $entity = new Entity();

        $entity->setLicence($licence);

        $this->assertEquals(111, $entity->getContextValue());
    }

    /**
     * @dataProvider testGetPublicationSectionForGrantEmailProvider
     *
     * @param string $status
     * @param string $revertStatus
     * @param string $shortNotice
     * @param string $section
     */
    public function testGetPublicationSectionForGrantEmail($status, $revertStatus, $shortNotice, $section)
    {
        $entity = new Entity();
        $status = new RefData($status);
        $entity->setStatus($status);
        $revertStatus = new RefData($revertStatus);
        $entity->setRevertStatus($revertStatus);
        $entity->setIsShortNotice($shortNotice);

        $this->assertEquals($section, $entity->getPublicationSectionForGrantEmail());
    }

    public function testGetPublicationSectionForGrantEmailProvider()
    {
        return [
            [Entity::STATUS_REGISTERED, Entity::STATUS_NEW, 'Y', PublicationSection::BUS_NEW_SHORT_SECTION],
            [Entity::STATUS_REGISTERED, Entity::STATUS_NEW, 'N', PublicationSection::BUS_NEW_SECTION],
            [Entity::STATUS_REGISTERED, Entity::STATUS_VAR, 'Y', PublicationSection::BUS_VAR_SHORT_SECTION],
            [Entity::STATUS_REGISTERED, Entity::STATUS_VAR, 'N', PublicationSection::BUS_VAR_SECTION],
            [Entity::STATUS_CANCELLED, Entity::STATUS_CANCEL, 'Y', PublicationSection::BUS_CANCEL_SHORT_SECTION],
            [Entity::STATUS_CANCELLED, Entity::STATUS_CANCEL, 'N', PublicationSection::BUS_CANCEL_SECTION],
        ];
    }

    /**
     * Tests the method throws exception if status is incorrect
     *
     * @dataProvider publicationSectionForGrantEmailInvalidStatusProvider
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @param string $status
     */
    public function testPublicationSectionForGrantEmailStatusException($status)
    {
        $entity = new Entity();
        $status = new RefData($status);
        $entity->setStatus($status);

        $entity->getPublicationSectionForGrantEmail();
    }

    /**
     * Data provider for isFromEbsr
     *
     * @return array
     */
    public function publicationSectionForGrantEmailInvalidStatusProvider()
    {
        return [
            [Entity::STATUS_NEW],
            [Entity::STATUS_VAR],
            [Entity::STATUS_CANCEL],
            [Entity::STATUS_ADMIN],
            [Entity::STATUS_REFUSED],
            [Entity::STATUS_WITHDRAWN],
            [Entity::STATUS_CNS],
        ];
    }

    /**
     * Tests the method throws exception if revertStatus is incorrect
     *
     * @dataProvider publicationSectionForGrantEmailInvalidRevertStatusProvider
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @param string $revertStatus
     */
    public function testPublicationSectionForGrantEmailRevertStatusException($status, $revertStatus)
    {
        $entity = new Entity();
        $status = new RefData($status);
        $entity->setStatus($status);
        $revertStatus = new RefData($revertStatus);
        $entity->setRevertStatus($revertStatus);

        $entity->getPublicationSectionForGrantEmail();
    }

    /**
     * Data provider for testPublicationSectionForGrantEmailRevertStatusException
     *
     * @return array
     */
    public function publicationSectionForGrantEmailInvalidRevertStatusProvider()
    {
        return [
            [Entity::STATUS_REGISTERED, Entity::STATUS_CANCEL],
            [Entity::STATUS_REGISTERED, Entity::STATUS_CANCELLED],
            [Entity::STATUS_REGISTERED, Entity::STATUS_ADMIN],
            [Entity::STATUS_REGISTERED, Entity::STATUS_REFUSED],
            [Entity::STATUS_REGISTERED, Entity::STATUS_WITHDRAWN],
            [Entity::STATUS_REGISTERED, Entity::STATUS_CNS],
            [Entity::STATUS_CANCELLED, Entity::STATUS_NEW],
            [Entity::STATUS_CANCELLED, Entity::STATUS_VAR],
            [Entity::STATUS_CANCELLED, Entity::STATUS_CANCELLED],
            [Entity::STATUS_CANCELLED, Entity::STATUS_ADMIN],
            [Entity::STATUS_CANCELLED, Entity::STATUS_REFUSED],
            [Entity::STATUS_CANCELLED, Entity::STATUS_WITHDRAWN],
            [Entity::STATUS_CANCELLED, Entity::STATUS_CNS],
        ];
    }

    public function testPublicationLinksForGrantEmail()
    {
        $pub3No = 1234;
        $pub3TrafficArea = 'trafficArea3';
        $pub4No = 5678;
        $pub4TrafficArea = 'trafficArea4';

        $expectedResult = $pub3No . ' ' . $pub3TrafficArea . ', ' . $pub4No . ' ' . $pub4TrafficArea;

        $matchPubSection = new PublicationSection();
        $matchPubSection ->setId(PublicationSection::BUS_NEW_SHORT_SECTION);

        $otherPubSection = new PublicationSection();
        $otherPubSection ->setId(PublicationSection::BUS_VAR_SHORT_SECTION);

        $publication2 = m::mock(PublicationEntity::class)->makePartial();
        $publication2->shouldReceive('isNew')->once()->andReturn(false);

        $publication3 = m::mock(PublicationEntity::class)->makePartial();
        $publication3->shouldReceive('isNew')->once()->andReturn(true);
        $publication3->shouldReceive('getPublicationNo')->once()->andReturn($pub3No);
        $publication3->shouldReceive('getTrafficArea->getName')->once()->andReturn($pub3TrafficArea);

        $publication4 = m::mock(PublicationEntity::class)->makePartial();
        $publication4->shouldReceive('isNew')->once()->andReturn(true);
        $publication4->shouldReceive('getPublicationNo')->once()->andReturn($pub4No);
        $publication4->shouldReceive('getTrafficArea->getName')->once()->andReturn($pub4TrafficArea);

        //won't match due to section
        $pubLink1 = new PublicationLink();
        $pubLink1->setPublicationSection($otherPubSection);

        //matches but publication not new
        $pubLink2 = new PublicationLink();
        $pubLink2->setPublicationSection($matchPubSection);
        $pubLink2->setPublication($publication2);

        //match
        $pubLink3 = new PublicationLink();
        $pubLink3->setPublicationSection($matchPubSection);
        $pubLink3->setPublication($publication3);

        //match
        $pubLink4 = new PublicationLink();
        $pubLink4->setPublicationSection($matchPubSection);
        $pubLink4->setPublication($publication4);

        $publicationLinks = new ArrayCollection([$pubLink1, $pubLink2, $pubLink3, $pubLink4]);

        $entity = new Entity();
        $entity->setPublicationLinks($publicationLinks);

        $this->assertEquals($expectedResult, $entity->getPublicationLinksForGrantEmail($matchPubSection));
    }

    /**
     * @dataProvider getFormattedServiceNumbersProvider
     *
     * @param string $serviceNo
     * @param ArrayCollection $otherServiceNumbers
     * @param string $expected
     */
    public function testGetFormattedServiceNumbers($serviceNo, $otherServiceNumbers, $expected)
    {
        $busReg = new Entity();
        $busReg->setOtherServices($otherServiceNumbers);
        $busReg->setServiceNo($serviceNo);

        $this->assertEquals($expected, $busReg->getFormattedServiceNumbers());
    }

    /**
     * data provider for testGetFormattedServiceNumbers
     *
     * @return array
     */
    public function getFormattedServiceNumbersProvider()
    {
        $serviceNo1 = '4567';
        $serviceNo2 = '8910';
        $otherServiceNo1 = new BusRegOtherServiceEntity(new Entity(), $serviceNo1);
        $otherServiceNo2 = new BusRegOtherServiceEntity(new Entity(), $serviceNo2);

        $serviceNo = '123';

        $otherServiceNumbers = new ArrayCollection([$otherServiceNo1, $otherServiceNo2]);
        $expectedFormatted = $serviceNo . '(' . $serviceNo1 . ',' . $serviceNo2 . ')';

        return [
            [$serviceNo, new ArrayCollection(), $serviceNo],
            [$serviceNo, $otherServiceNumbers, $expectedFormatted]
        ];
    }
}
