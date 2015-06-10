<?php

namespace Dvsa\OlcsTest\Api\Entity\Fee;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Fee\Fee as Entity;
use Mockery as m;

/**
 * Fee Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class FeeEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * @dataProvider outstandingPaymentProvider
     */
    public function testHadOutstandingPayment($feePayments, $expected)
    {
        $sut = $this->instantiate($this->entityClass);
        $sut->setFeePayments($feePayments);

        $this->assertEquals($expected, $sut->hasOutstandingPayment());
    }

    public function outstandingPaymentProvider()
    {
        return [
            'no fee payments' => [
                [],
                false,
            ],
            'one outstanding' => [
                [
                    m::mock()
                        ->shouldReceive('getPayment')
                        ->andReturn(
                            m::mock()
                                ->shouldReceive('isOutstanding')
                                ->andReturn(true)
                                ->getMock()
                        )
                        ->getMock()
                ],
                true,
            ]
        ];
    }

    public function testGetRuleStartDate()
    {
        $this->markTestIncomplete('TODO');
    }
}
