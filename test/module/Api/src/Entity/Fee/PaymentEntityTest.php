<?php

namespace Dvsa\OlcsTest\Api\Entity\Fee;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Fee\Payment as Entity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Mockery as m;

/**
 * Payment Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class PaymentEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testGetCollections()
    {
        $sut = $this->instantiate($this->entityClass);

        $feePayments = $sut->getFeePayments();

        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $feePayments);
    }

    /**
     * @param string $status
     * @param boolean $expected
     *
     * @dataProvider isOutstandingProvider
     */
    public function testIsOutstanding($status, $expected)
    {
        $sut = new $this->entityClass;

        $status = m::mock(RefData::class)
            ->shouldReceive('getId')
            ->once()
            ->andReturn($status)
            ->getMock();

        $sut->setStatus($status);

        $this->assertEquals($expected, $sut->isOutstanding());
    }

    /**
     * @return array
     */
    public function isOutstandingProvider()
    {
        return [
            [Entity::STATUS_OUTSTANDING, true],
            [Entity::STATUS_CANCELLED, false],
            [Entity::STATUS_LEGACY, false],
            [Entity::STATUS_FAILED, false],
            [Entity::STATUS_PAID, false],
            ['invalid', false],
        ];
    }


    /**
     * @param string $status
     * @param boolean $expected
     *
     * @dataProvider isPaidProvider
     */
    public function testIsPaid($status, $expected)
    {
        $sut = new $this->entityClass;

        $status = m::mock(RefData::class)
            ->shouldReceive('getId')
            ->once()
            ->andReturn($status)
            ->getMock();

        $sut->setStatus($status);

        $this->assertEquals($expected, $sut->isPaid());
    }

    /**
     * @return array
     */
    public function isPaidProvider()
    {
        return [
            [Entity::STATUS_OUTSTANDING, false],
            [Entity::STATUS_CANCELLED, false],
            [Entity::STATUS_LEGACY, false],
            [Entity::STATUS_FAILED, false],
            [Entity::STATUS_PAID, true],
            ['invalid', false],
        ];
    }
}
