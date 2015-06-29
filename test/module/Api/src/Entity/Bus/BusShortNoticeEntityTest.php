<?php

namespace Dvsa\OlcsTest\Api\Entity\Bus;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Bus\BusShortNotice as Entity;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Mockery as m;

/**
 * BusShortNotice Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class BusShortNoticeEntityTest extends EntityTester
{
    public function setUp()
    {
        /** @var Entity */
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
        $isLatestVariation = true;

        //the bus reg entity related to short notice
        $busRegMock = m::mock(BusRegEntity::class);
        $busRegMock->shouldReceive('isLatestVariation')->once()->andReturn($isLatestVariation);

        $sut = m::mock(Entity::class)->makePartial();
        $sut->shouldReceive('getBusReg')->once()->andReturn($busRegMock);

        $result = $sut->getCalculatedValues();

        $this->assertEquals($result['busReg'], null);
        $this->assertEquals($result['isLatestVariation'], true);
    }

    /**
     * Test exception is thrown when edit not allowed
     *
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ForbiddenException
     */
    public function testUpdateThrowsException()
    {
        $busReg = new BusRegEntity();
        $busReg->setIsTxcApp('Y');

        $this->entity->setBusReg($busReg);

        $bankHolidayChange = 'Y';
        $unforseenChange = 'Y';
        $unforseenDetail = 'unforseen detail';
        $timetableChange =
        $timetableDetail = 'timetable detail';
        $replacementChange = 'Y';
        $replacementDetail = 'replacement detail';
        $notAvailableChange = 'Y';
        $notAvailableDetail = 'not available detail';
        $specialOccasionChange = 'N';
        $specialOccasionDetail = 'special occasion detail';
        $connectionChange = 'N';
        $connectionDetail = 'connection detail';
        $holidayChange = 'N';
        $holidayDetail = 'holiday detail';
        $trcChange = 'N';
        $trcDetail = 'trc detail';
        $policeChange = 'N';
        $policeDetail = 'police detail';

        $this->entity->update(
            $bankHolidayChange,
            $unforseenChange,
            $unforseenDetail,
            $timetableChange,
            $timetableDetail,
            $replacementChange,
            $replacementDetail,
            $notAvailableChange,
            $notAvailableDetail,
            $specialOccasionChange,
            $specialOccasionDetail,
            $connectionChange,
            $connectionDetail,
            $holidayChange,
            $holidayDetail,
            $trcChange,
            $trcDetail,
            $policeChange,
            $policeDetail
        );
    }

    /**
     * Tests the update function
     */
    public function testUpdate()
    {
        /** @var BusRegEntity $busReg */
        $busReg = m::mock(BusRegEntity::class);
        $busReg->shouldReceive('canEdit')
            ->once()
            ->andReturn(true);

        $this->entity->setBusReg($busReg);

        $bankHolidayChange = 'Y';
        $unforseenChange = 'Y';
        $unforseenDetail = 'unforseen detail';
        $timetableChange =
        $timetableDetail = 'timetable detail';
        $replacementChange = 'Y';
        $replacementDetail = 'replacement detail';
        $notAvailableChange = 'Y';
        $notAvailableDetail = 'not available detail';
        $specialOccasionChange = 'N';
        $specialOccasionDetail = 'special occasion detail';
        $connectionChange = 'N';
        $connectionDetail = 'connection detail';
        $holidayChange = 'N';
        $holidayDetail = 'holiday detail';
        $trcChange = 'N';
        $trcDetail = 'trc detail';
        $policeChange = 'N';
        $policeDetail = 'police detail';

        $this->entity->update(
            $bankHolidayChange,
            $unforseenChange,
            $unforseenDetail,
            $timetableChange,
            $timetableDetail,
            $replacementChange,
            $replacementDetail,
            $notAvailableChange,
            $notAvailableDetail,
            $specialOccasionChange,
            $specialOccasionDetail,
            $connectionChange,
            $connectionDetail,
            $holidayChange,
            $holidayDetail,
            $trcChange,
            $trcDetail,
            $policeChange,
            $policeDetail
        );

        $this->assertEquals($bankHolidayChange, $this->entity->getBankHolidayChange());
        $this->assertEquals($unforseenChange, $this->entity->getUnforseenChange());
        $this->assertEquals($unforseenDetail, $this->entity->getUnforseenDetail());
        $this->assertEquals($timetableChange, $this->entity->getTimetableChange());
        $this->assertEquals($timetableDetail, $this->entity->getTimetableDetail());
        $this->assertEquals($replacementChange, $this->entity->getReplacementChange());
        $this->assertEquals($replacementDetail, $this->entity->getReplacementDetail());
        $this->assertEquals($notAvailableChange, $this->entity->getNotAvailableChange());
        $this->assertEquals($notAvailableDetail, $this->entity->getNotAvailableDetail());
        $this->assertEquals($specialOccasionChange, $this->entity->getSpecialOccasionChange());
        $this->assertEquals($specialOccasionDetail, $this->entity->getSpecialOccasionDetail());
        $this->assertEquals($connectionChange, $this->entity->getConnectionChange());
        $this->assertEquals($connectionDetail, $this->entity->getConnectionDetail());
        $this->assertEquals($holidayChange, $this->entity->getHolidayChange());
        $this->assertEquals($holidayDetail, $this->entity->getHolidayDetail());
        $this->assertEquals($trcChange, $this->entity->getTrcChange());
        $this->assertEquals($trcDetail, $this->entity->getTrcDetail());
        $this->assertEquals($policeChange, $this->entity->getPoliceChange());
        $this->assertEquals($policeDetail, $this->entity->getPoliceDetail());
    }
}
