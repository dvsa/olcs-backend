<?php

namespace Dvsa\OlcsTest\Api\Entity\Bus;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as Entity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Bus\BusNoticePeriod;
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
        $otherServices = [];
        $startPoint = 'start point';
        $finishPoint = 'finish point';
        $via = 'via';
        $busServiceTypes = [];
        $otherDetails = 'other details';
        $endDate = '2015-12-21';
        $busNoticePeriod = 2;


        $busRules = new BusNoticePeriod();
        $busRules->setCancellationPeriod($rules['cancellationPeriod']);
        $busRules->setStandardPeriod($rules['standardPeriod']);

        $this->entity->setVariationNo($variationNo);
        $this->entity->setParent($parent);

        $this->entity->updateServiceDetails(
            $serviceNo,
            $otherServices,
            $startPoint,
            $finishPoint,
            $via,
            $busServiceTypes,
            $otherDetails,
            $receivedDate,
            $effectiveDate,
            $endDate,
            $busNoticePeriod,
            $busRules
        );

        $this->assertEquals($serviceNo, $this->entity->getServiceNo());
        //$this->assertEquals($otherServices, $this->entity->getOtherServices());
        $this->assertEquals($startPoint, $this->entity->getStartPoint());
        $this->assertEquals($finishPoint, $this->entity->getFinishPoint());
        $this->assertEquals($via, $this->entity->getVia());
        $this->assertEquals($receivedDate, $this->entity->getReceivedDate());
        $this->assertEquals($effectiveDate, $this->entity->getEffectiveDate());
        $this->assertEquals($endDate, $this->entity->getEndDate());
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
            [$notSn, $scotRules, 1, '2015-02-09', '2016-09-30', $parent],
            [$notSn, $scotRules, 1, '2014-06-11', '2014-08-11']
        ];
    }

}
