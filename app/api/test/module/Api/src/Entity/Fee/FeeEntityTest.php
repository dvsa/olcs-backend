<?php

namespace Dvsa\OlcsTest\Api\Entity\Fee;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Fee\Fee as Entity;
use Dvsa\Olcs\Api\Entity\System\RefData;
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

    protected $sut;

    public function setUp()
    {
        parent::setUp();

        $this->sut = $this->instantiate($this->entityClass);
    }

    /**
     * @param array $feePayments
     * @param boolean $expected
     *
     * @dataProvider outstandingPaymentProvider
     */
    public function testHadOutstandingPayment($feePayments, $expected)
    {
        $this->sut->setFeePayments($feePayments);

        $this->assertEquals($expected, $this->sut->hasOutstandingPayment());
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

    /**
     * @param string $accrualRuleId,
     * @param Licence $licence
     * @param DateTime $expected
     *
     * @dataProvider ruleStartDateProvider
     */
    public function testGetRuleStartDate($accrualRuleId, $licence, $expected)
    {
        $now = new \DateTime('2015-06-10 12:34:56');
        $this->sut->setCurrentDateTime($now);

        $feeType = m::mock()
            ->shouldReceive('getAccrualRule')
            ->andReturn((new RefData())->setId($accrualRuleId))
            ->getMock();

        $this->sut->setFeeType($feeType);
        if (!is_null($licence)) {
            $this->sut->setLicence($licence);
        }

        $this->assertEquals($expected, $this->sut->getRuleStartDate());
    }

    public function ruleStartDateProvider()
    {
        $now = new \DateTime('2015-06-10 12:34:56');

        return [
            'immediate' => [
                Entity::ACCRUAL_RULE_IMMEDIATE,
                null,
                $now,
            ],
            'licence start' => [
                Entity::ACCRUAL_RULE_LICENCE_START,
                m::mock()
                    ->shouldReceive('getInForceDate')
                    ->andReturn('2015-04-03')
                    ->getMock(),
                new \DateTime('2015-04-03'),
            ],
            'licence start date missing' => [
                Entity::ACCRUAL_RULE_LICENCE_START,
                m::mock()
                    ->shouldReceive('getInForceDate')
                    ->andReturn(null)
                    ->getMock(),
                null,
            ],
            'continuation' => [
                Entity::ACCRUAL_RULE_CONTINUATION,
                m::mock()
                    ->shouldReceive('getExpiryDate')
                    ->andReturn('2015-04-03')
                    ->getMock(),
                new \DateTime('2015-04-04'),
            ],
            'continuation date missing' => [
                Entity::ACCRUAL_RULE_CONTINUATION,
                m::mock()
                    ->shouldReceive('getExpiryDate')
                    ->andReturn(null)
                    ->getMock(),
                null,
            ],
            'invalid' => [
                'foo',
                null,
                null,
            ],
        ];
    }

    /**
     * @param string $status
     * @param boolean $expected
     *
     * @dataProvider allowEditProvider
     */
    public function testAllowEdit($status, $expected)
    {
        $feeStatus = m::mock(RefData::class)->makePartial();
        $feeStatus->setId($status);
        $this->sut->setFeeStatus($feeStatus);

        $this->assertEquals($expected, $this->sut->allowEdit());
    }

    public function allowEditProvider()
    {
        return [
            [Entity::STATUS_PAID, false],
            [Entity::STATUS_CANCELLED, false],
            [Entity::STATUS_OUTSTANDING, true],
            [Entity::STATUS_WAIVE_RECOMMENDED, true],
            [Entity::STATUS_WAIVED, true],
            ['invalid', true],
        ];
    }
}
