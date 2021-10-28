<?php

namespace Dvsa\OlcsTest\Api\Entity\Tm;

use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication as Entity;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;

/**
 * @covers Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication
 * @covers Dvsa\Olcs\Api\Entity\Tm\AbstractTransportManagerApplication
 */
class TransportManagerApplicationEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /** @var  Entity */
    private $sut;

    public function setUp(): void
    {
        $this->sut = new Entity();

        parent::setUp();
    }

    public function testUpdateTransportManagerApplication()
    {
        $this->sut->updateTransportManagerApplication(1, 2, 'A', 'st');
        $this->assertEquals($this->sut->getApplication(), 1);
        $this->assertEquals($this->sut->getTransportManager(), 2);
        $this->assertEquals($this->sut->getAction(), 'A');
        $this->assertEquals($this->sut->getTmApplicationStatus(), 'st');
    }

    public function testUpdateTransportManagerApplicationFull()
    {
        $this->sut->updateTransportManagerApplicationFull(
            'tmt',
            1,
            'Y',
            1,
            2,
            3,
            4,
            5,
            6,
            7,
            'ai',
            'tmas'
        );
        $this->assertEquals($this->sut->getTmType(), 'tmt');
        $this->assertEquals($this->sut->getIsOwner(), 1);
        $this->assertEquals('Y', $this->sut->getHasUndertakenTraining());
        $this->assertEquals($this->sut->getHoursMon(), 1);
        $this->assertEquals($this->sut->getHoursTue(), 2);
        $this->assertEquals($this->sut->getHoursWed(), 3);
        $this->assertEquals($this->sut->getHoursThu(), 4);
        $this->assertEquals($this->sut->getHoursFri(), 5);
        $this->assertEquals($this->sut->getHoursSat(), 6);
        $this->assertEquals($this->sut->getHoursSun(), 7);
        $this->assertEquals($this->sut->getAdditionalInformation(), 'ai');
        $this->assertEquals($this->sut->getTmApplicationStatus(), 'tmas');
    }

    public function testUpdateTransportManagerApplicationFullInvalid()
    {
        try {
            $this->sut->updateTransportManagerApplicationFull(
                'tmt',
                1,
                'N',
                25,
                25,
                25,
                25,
                25,
                25,
                25,
                'ai',
                'tmas'
            );
        } catch (ValidationException $e) {
            static::assertEquals(
                $e->getMessages(),
                [
                    [
                        'hoursMon' => [Entity::ERROR_MON => 'Mon must be between 0 and 24, inclusively'],
                    ],
                    [
                        'hoursTue' => [Entity::ERROR_TUE => 'Tue must be between 0 and 24, inclusively'],
                    ],
                    [
                        'hoursWed' => [Entity::ERROR_WED => 'Wed must be between 0 and 24, inclusively'],
                    ],
                    [
                        'hoursThu' => [Entity::ERROR_THU => 'Thu must be between 0 and 24, inclusively'],
                    ],
                    [
                        'hoursFri' => [Entity::ERROR_FRI => 'Fri must be between 0 and 24, inclusively'],
                    ],
                    [
                        'hoursSat' => [Entity::ERROR_SAT => 'Sat must be between 0 and 24, inclusively'],
                    ],
                    [
                        'hoursSun' => [Entity::ERROR_SUN => 'Sun must be between 0 and 24, inclusively']
                    ],
                ]
            );
        }
    }

    public function testGetTotalWeeklyHours()
    {
        $this->sut->updateTransportManagerApplicationFull(
            'tmt',
            1,
            'Y',
            1,
            2,
            3,
            4,
            5,
            6,
            7,
            'ai',
            'tmas'
        );
        $this->assertEquals($this->sut->getTotalWeeklyHours(), 28);
    }
}
