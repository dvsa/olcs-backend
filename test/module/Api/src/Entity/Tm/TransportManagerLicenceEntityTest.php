<?php

namespace Dvsa\OlcsTest\Api\Entity\Tm;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence as Entity;
use Mockery as m;
use \Dvsa\Olcs\Api\Domain\Exception\ValidationException;

/**
 * TransportManagerLicence Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class TransportManagerLicenceEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testUpdateTransportManagerLicence()
    {
        $sut = m::mock(Entity::class)->makePartial();
        $sut->updateTransportManagerLicence(
            'tmt',
            1,
            2,
            3,
            4,
            5,
            6,
            7,
            'ai',
            1
        );
        $this->assertEquals($sut->getTmType(), 'tmt');
        $this->assertEquals($sut->getHoursMon(), 1);
        $this->assertEquals($sut->getHoursTue(), 2);
        $this->assertEquals($sut->getHoursWed(), 3);
        $this->assertEquals($sut->getHoursThu(), 4);
        $this->assertEquals($sut->getHoursFri(), 5);
        $this->assertEquals($sut->getHoursSat(), 6);
        $this->assertEquals($sut->getHoursSun(), 7);
        $this->assertEquals($sut->getAdditionalInformation(), 'ai');
        $this->assertEquals($sut->getIsOwner(), 1);
    }

    public function testUpdateTransportManagerLicenceInvalid()
    {
        $this->setExpectedException(
            ValidationException::class,
            [
                'hoursMon' => [Entity::ERROR_MON => 'Mon must be between 0 and 24, inclusively'],
                'hoursTue' => [Entity::ERROR_TUE => 'Tue must be between 0 and 24, inclusively'],
                'hoursWed' => [Entity::ERROR_WED => 'Wed must be between 0 and 24, inclusively'],
                'hoursThu' => [Entity::ERROR_THU => 'Thu must be between 0 and 24, inclusively'],
                'hoursFri' => [Entity::ERROR_FRI => 'Fri must be between 0 and 24, inclusively'],
                'hoursSat' => [Entity::ERROR_SAT => 'Mon must be between 0 and 24, inclusively'],
                'hoursSun' => [Entity::ERROR_SUN => 'Sun must be between 0 and 24, inclusively']
            ]
        );
        $sut = m::mock(Entity::class)->makePartial();
        $sut->updateTransportManagerLicence(
            'tmt',
            25,
            25,
            25,
            25,
            25,
            25,
            25,
            'ai',
            1
        );
    }

    public function testGetTotalWeeklyHours()
    {
        $sut = m::mock(Entity::class)->makePartial();
        $sut->updateTransportManagerLicence(
            'tmt',
            1,
            2,
            3,
            4,
            5,
            6,
            7,
            'ai',
            1
        );
        $this->assertEquals($sut->getTotalWeeklyHours(), 28);
    }
}
