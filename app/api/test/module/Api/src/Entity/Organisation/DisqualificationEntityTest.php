<?php

namespace Dvsa\OlcsTest\Api\Entity\Organisation;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Organisation\Disqualification as Entity;

/**
 * Disqualification Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class DisqualificationEntityTest extends EntityTester
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
    protected $sut;

    public function setUp()
    {
        $this->sut = new Entity();

        parent::setUp();
    }

    public function testGetStatusN()
    {
        $this->sut->setStartDate((new \Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime('-1 month'))->format('Y-m-d'));
        $this->sut->setPeriod(12);
        $this->sut->setIsDisqualified('N');

        $this->assertSame(Entity::STATUS_INACTIVE, $this->sut->getStatus());
    }

    /**
     * @dataProvider dpGetStatus
     */
    public function testGetStatus($expectedStatus, $startDate, $period)
    {
        $this->sut->setStartDate((new \Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime($startDate))->format('Y-m-d'));
        $this->sut->setPeriod($period);
        $this->sut->setIsDisqualified('Y');

        $this->assertSame($expectedStatus, $this->sut->getStatus());
    }

    public function dpGetStatus()
    {
        return [
            [Entity::STATUS_INACTIVE, '-2 month', 1],
            [Entity::STATUS_INACTIVE, '-12 month', 11],
            [Entity::STATUS_INACTIVE, '-1 year', 11],
            [Entity::STATUS_INACTIVE, '+1 day', null],
            [Entity::STATUS_INACTIVE, '+1 day', 0],
            [Entity::STATUS_ACTIVE, '-1 month', 3],
            [Entity::STATUS_ACTIVE, '-1 month', null],
            [Entity::STATUS_ACTIVE, '-1 month', 0],
            [Entity::STATUS_ACTIVE, '-1 year', null],
            [Entity::STATUS_ACTIVE, '-1 year', 0],
            [Entity::STATUS_ACTIVE, '', 0],
            [Entity::STATUS_ACTIVE, '', 1],
        ];
    }
}
