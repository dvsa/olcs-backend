<?php

namespace Dvsa\OlcsTest\Api\Entity\Cases;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Cases\Complaint as Entity;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * Complaint Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class ComplaintEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * @param string $statusId
     * @param boolean $expected
     * @dataProvider isOpenProvider
     */
    public function testIsOpen($statusId, $expected)
    {
        $sut = $this->instantiate($this->entityClass);

        $status = new RefData();
        $status->setId($statusId);

        $sut->setStatus($status);

        $this->assertEquals($expected, $sut->isOpen());
    }

    public function testIsOpenWithoutStatus()
    {
        $sut = $this->instantiate($this->entityClass);

        $this->assertFalse($sut->isOpen());
    }

    public function isOpenProvider()
    {
        return [
            [Entity::COMPLAIN_STATUS_OPEN, true],
            [Entity::COMPLAIN_STATUS_CLOSED, false],
        ];
    }

    /**
     * @param bool $isCompliance
     * @param bool $expected
     * @dataProvider isEnvironmentalComplaintProvider
     */
    public function testIsEnvironmentalComplaint($isCompliance, $expected)
    {
        $sut = $this->instantiate($this->entityClass);

        $sut->setIsCompliance($isCompliance);

        $this->assertEquals($expected, $sut->isEnvironmentalComplaint());
    }

    public function isEnvironmentalComplaintProvider()
    {
        return [
            [false, true],
            [true, false],
        ];
    }

    /**
     * @param bool $isCompliance
     * @param string $statusId
     * @param \DateTime|null $closedDate
     * @param \DateTime|null $expected
     * @dataProvider populateClosedDateProvider
     */
    public function testPopulateClosedDate($isCompliance, $statusId, $closedDate, $expected)
    {
        $sut = $this->instantiate($this->entityClass);

        $sut->setIsCompliance($isCompliance);

        $status = new RefData();
        $status->setId($statusId);
        $sut->setStatus($status);

        $sut->setClosedDate($closedDate);

        $sut->populateClosedDate();

        $this->assertEquals(
            $expected !== null ? $expected->format('Y-m-d') : null,
            $sut->getClosedDate() !== null ? $sut->getClosedDate()->format('Y-m-d') : null
        );
    }

    public function populateClosedDateProvider()
    {
        return [
            // non-Environmental Complaint
            [true, Entity::COMPLAIN_STATUS_CLOSED, null, null],
            [true, Entity::COMPLAIN_STATUS_OPEN, null, null],

            // Environmental Complaint
            // closed - closed date not set yet
            [false, Entity::COMPLAIN_STATUS_CLOSED, null, new \DateTime()],
            // closed - closed date already set
            [false, Entity::COMPLAIN_STATUS_CLOSED, new \DateTime('2015-02-10'), new \DateTime('2015-02-10')],

            // open - closed date not set yet
            [false, Entity::COMPLAIN_STATUS_OPEN, null, null],
            // open - closed date already set
            [false, Entity::COMPLAIN_STATUS_OPEN, new \DateTime('2015-02-10'), null],
        ];
    }
}
