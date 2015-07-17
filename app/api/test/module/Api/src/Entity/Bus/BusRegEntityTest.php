<?php

namespace Dvsa\OlcsTest\Api\Entity\Bus;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as Entity;
use Dvsa\Olcs\Api\Entity\Bus\BusRegOtherService as BusRegOtherServiceEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusNoticePeriod as BusNoticePeriodEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusShortNotice as BusShortNoticeEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\System\RefData as RefDataEntity;
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

        $sut = m::mock(Entity::class)->makePartial();
        $sut->shouldReceive('getRegNo')->once()->andReturn($regNo);
        $sut->shouldReceive('getId')->once()->andReturn($id);
        $sut->shouldReceive('getLicence')->once()->andReturn($licenceEntityMock);

        $result = $sut->getCalculatedValues();

        $this->assertEquals($result['licence'], null);
        $this->assertEquals($result['isLatestVariation'], true);
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

        $result = $sut->getCalculatedBundleValues();

        $this->assertEquals($result['isLatestVariation'], true);
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

        $otherService1 = new BusRegOtherServiceEntity();
        $otherService1->setId(201);
        $otherService1->setServiceNo('otherService1');

        $otherService2 = new BusRegOtherServiceEntity();
        $otherService2->setId(202);
        $otherService2->setServiceNo('otherService2');

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

        $busReg = $this->entity->createVariation($status, $revertStatus);

        // test some values from $defaultAll
        $this->assertEquals('N', $busReg->getIsShortNotice());
        $this->assertNull($busReg->getEndDate());

        // test some database metadata
        $this->assertNull($busReg->getId());
        $this->assertNull($busReg->getVersion());
        $this->assertNull($busReg->getVariationReasons());

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
            $timetableAcceptable,
            $mapSupplied,
            $routeDescription,
            $trcConditionChecked,
            $trcNotes,
            $copiedToLaPte,
            $laShortNote,
            $opNotifiedLaPte,
            $applicationSigned
        );

        $this->assertEquals($timetableAcceptable, $this->entity->getTimetableAcceptable());
        $this->assertEquals($mapSupplied, $this->entity->getMapSupplied());
        $this->assertEquals($routeDescription, $this->entity->getRouteDescription());
        $this->assertEquals($trcConditionChecked, $this->entity->getTrcConditionChecked());
        $this->assertEquals($trcNotes, $this->entity->getTrcNotes());
        $this->assertEquals($copiedToLaPte, $this->entity->getCopiedToLaPte());
        $this->assertEquals($laShortNote, $this->entity->getLaShortNote());
        $this->assertEquals($opNotifiedLaPte, $this->entity->getOpNotifiedLaPte());
        $this->assertEquals($applicationSigned, $this->entity->getApplicationSigned());

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
}
