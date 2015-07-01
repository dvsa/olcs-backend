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

    public function isOpenProvider()
    {
        return [
            [Entity::COMPLAIN_STATUS_OPEN, true],
            [Entity::COMPLAIN_STATUS_CLOSED, false],
        ];
    }
}
