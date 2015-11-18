<?php

namespace Dvsa\OlcsTest\Api\Entity\Fee;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\Fee\Fee as Entity;
use Dvsa\Olcs\Api\Entity\Fee\FeeTransaction;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Fee\Transaction;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermit;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
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

    public function testConstructor()
    {
        $type = new FeeType();
        $amount = '10.00';
        $status = new RefData(Entity::STATUS_OUTSTANDING);

        $fee = new Entity($type, $amount, $status);

        $this->assertSame($type, $fee->getFeeType());
        $this->assertSame($amount, $fee->getNetAmount());
        $this->assertSame($status, $fee->getFeeStatus());
    }

    /**
     * @param array $feeTransactions
     * @param boolean $expected
     *
     * @dataProvider outstandingPaymentProvider
     */
    public function testHadOutstandingPayment($feeTransactions, $expected)
    {
        $this->sut->setFeeTransactions($feeTransactions);

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
                        ->shouldReceive('getTransaction')
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
        $now = new DateTime();

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
                new DateTime('2015-04-03'),
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
                new DateTime('2015-04-04'),
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
     * @param string $accrualRuleId,
     * @param int $expected
     *
     * @dataProvider defermentPeriodProvider
     */
    public function testGetDefermentPeriod($accrualRuleId, $expected)
    {
        $feeType = m::mock()
            ->shouldReceive('getAccrualRule')
            ->andReturn((new RefData())->setId($accrualRuleId))
            ->getMock();

        $this->sut->setFeeType($feeType);

        $this->assertEquals($expected, $this->sut->getDefermentPeriod());
    }

    public function defermentPeriodProvider()
    {
        return [
            'immediate' => [
                Entity::ACCRUAL_RULE_IMMEDIATE,
                1
            ],
            'licence start' => [
                Entity::ACCRUAL_RULE_LICENCE_START,
                60,
            ],
            'continuation' => [
                Entity::ACCRUAL_RULE_CONTINUATION,
                60,
            ],
            'no rule' => [
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
            ['invalid', true],
        ];
    }

    public function testCompatibilityGetMethods()
    {
        $this->assertNull($this->sut->getLatestPaymentRef());
        $this->assertNull($this->sut->getPaymentMethod());
        $this->assertNull($this->sut->getProcessedBy());
        $this->assertNull($this->sut->getPayer());
        $this->assertNull($this->sut->getSlipNo());
        $this->assertNull($this->sut->getChequePoNumber());
        $this->assertNull($this->sut->getWaiveReason());

        $ft1 = $this->getStubFeeTransaction('1234.56', '2015-09-01', '2015-09-02 12:34:56');
        $ft2 = $this->getStubFeeTransaction('1234.56', '2015-08-01', '2015-09-02 12:34:56');
        $ft3 = $this->getStubFeeTransaction('1234.56', '2015-09-01', '2015-09-02 12:34:55');
        $ft4 = $this->getStubFeeTransaction(
            '234.56',
            '2015-09-03',
            '2015-09-03 12:34:55',
            Transaction::STATUS_OUTSTANDING,
            Transaction::TYPE_WAIVE,
            'waive reason'
        );

        $transaction = $ft1->getTransaction();

        $paymentMethod = m::mock(RefData::class);
        $transaction->setPaymentMethod($paymentMethod);

        $user = m::mock()
            ->shouldReceive('getLoginId')
            ->andReturn('bob')
            ->getMock();
        $transaction->setProcessedByUser($user);

        $transaction->setPayerName('payer');

        $transaction->setPayingInSlipNumber('12345');

        $transaction->setChequePoNumber('23456');

        $transaction->setReference('OLCS-1234');

        $this->sut->getFeeTransactions()->add($ft1);
        $this->sut->getFeeTransactions()->add($ft2);
        $this->sut->getFeeTransactions()->add($ft3);
        $this->sut->getFeeTransactions()->add($ft4);

        $this->assertEquals($paymentMethod, $this->sut->getPaymentMethod());
        $this->assertEquals('bob', $this->sut->getProcessedBy());
        $this->assertEquals('payer', $this->sut->getPayer());
        $this->assertEquals('12345', $this->sut->getSlipNo());
        $this->assertEquals('23456', $this->sut->getChequePoNumber());
        $this->assertEquals('waive reason', $this->sut->getWaiveReason());
        $this->assertEquals('OLCS-1234', $this->sut->getLatestPaymentRef());
    }

    private function getStubFeeTransaction(
        $amount,
        $completedDate,
        $createdOn,
        $statusId = Transaction::STATUS_COMPLETE,
        $typeId = Transaction::TYPE_PAYMENT,
        $comment = ''
    ) {
        $transaction = new Transaction();
        $feeTransaction = new FeeTransaction();
        $feeTransaction->setTransaction($transaction);
        $feeTransaction->setAmount($amount);
        $completed = new \DateTime($completedDate);
        $transaction->setCompletedDate($completed);
        $created = new \DateTime($createdOn);
        $transaction->setCreatedOn($created);
        $status = (new RefData())->setId($statusId);
        $transaction->setStatus($status);
        $type = (new RefData())->setId($typeId);
        $transaction->setType($type);
        $transaction->setComment($comment);

        return $feeTransaction;
    }

    /**
     * @dataProvider outstandingWaiveTransactionProvider
     */
    public function testGetOutstandingWaiveTransaction(array $feeTransactions, $expected)
    {
        $this->sut->setFeeTransactions(new ArrayCollection($feeTransactions));

        $this->assertEquals($expected, $this->sut->getOutstandingWaiveTransaction());
    }

    public function outstandingWaiveTransactionProvider()
    {
        $transaction1 = m::mock(Transaction::class);
        $transaction1->shouldReceive('isOutstanding')
            ->andReturn(false);
        $transaction1->shouldReceive('getType->getId')
            ->andReturn(Transaction::TYPE_WAIVE);

        $transaction2 = m::mock(Transaction::class);
        $transaction2->shouldReceive('isOutstanding')
            ->andReturn(true);
        $transaction2->shouldReceive('getType->getId')
            ->andReturn(Transaction::TYPE_WAIVE);

        $feeTransaction1 = m::mock(FeeTransaction::class)
            ->shouldReceive('getTransaction')
            ->andReturn($transaction1)
            ->getMock();
        $feeTransaction2 = m::mock(FeeTransaction::class)
            ->shouldReceive('getTransaction')
            ->andReturn($transaction2)
            ->getMock();

        return [
            'none' => [
                [],
                null,
            ],
            'valid' => [
                [$feeTransaction1, $feeTransaction2],
                $transaction2,
            ],
        ];
    }

    /**
     * @dataProvider outstandingAmountProvider
     */
    public function testGetOutstandingAmount($feeAmount, $feeTransactions, $expected)
    {
        $this->sut->setGrossAmount($feeAmount);
        $this->sut->setFeeTransactions($feeTransactions);
        $this->assertEquals($expected, $this->sut->getOutstandingAmount());
    }

    public function outstandingAmountProvider()
    {
        return [
            'no transactions' => [
                '1234.56',
                new ArrayCollection(),
                '1234.56',
            ],
            'one complete transaction' => [
                '1234.56',
                new ArrayCollection(
                    [
                        $this->getStubFeeTransaction('1234.56', '2015-09-01', '2015-09-02'),
                    ]
                ),
                '0.00',
            ],
            'one pending transaction' => [
                '1234.56',
                new ArrayCollection(
                    [
                        $this->getStubFeeTransaction(
                            '1234.56',
                            '2015-09-01',
                            '2015-09-02 12:34:56',
                            Transaction::STATUS_OUTSTANDING
                        ),
                    ]
                ),
                '1234.56',
            ],
            'two complete one refund one pending' => [
                '1234.56',
                new ArrayCollection(
                    [
                        $this->getStubFeeTransaction('1000', '2015-09-01', '2015-09-02'),
                        $this->getStubFeeTransaction('300', '2015-09-01', '2015-09-02'),
                        $this->getStubFeeTransaction('-100', '2015-09-01', '2015-09-02'),
                        $this->getStubFeeTransaction(
                            '34.56',
                            '2015-09-01',
                            '2015-09-02',
                            Transaction::STATUS_OUTSTANDING
                        ),
                    ]
                ),
                '34.56',
            ],
            'one overpayment' => [
                '1234.56',
                new ArrayCollection(
                    [
                        $this->getStubFeeTransaction('2000', '2015-09-01', '2015-09-02'),
                    ]
                ),
                '-765.44',
            ],
        ];
    }

    /**
     * @dataProvider partPaidProvider
     */
    public function testIsPartPaid($feeAmount, $feeTransactions, $expected)
    {
        $this->sut->setGrossAmount($feeAmount);
        $this->sut->setFeeTransactions($feeTransactions);
        $this->assertEquals($expected, $this->sut->isPartPaid());
    }

    public function partPaidProvider()
    {
        return [
            'no transactions' => [
                '1234.56',
                new ArrayCollection(),
                false,
            ],
            'one complete transaction' => [
                '1234.56',
                new ArrayCollection(
                    [
                        $this->getStubFeeTransaction('1234.56', '2015-09-01', '2015-09-02'),
                    ]
                ),
                true, // fully paid IS part paid
            ],
            'one pending transaction' => [
                '1234.56',
                new ArrayCollection(
                    [
                        $this->getStubFeeTransaction(
                            '1234.56',
                            '2015-09-01',
                            '2015-09-02 12:34:56',
                            Transaction::STATUS_OUTSTANDING
                        ),
                    ]
                ),
                false,
            ],
            'two complete one refund one pending' => [
                '1234.56',
                new ArrayCollection(
                    [
                        $this->getStubFeeTransaction('1000', '2015-09-01', '2015-09-02'),
                        $this->getStubFeeTransaction('300', '2015-09-01', '2015-09-02'),
                        $this->getStubFeeTransaction('-100', '2015-09-01', '2015-09-02'),
                        $this->getStubFeeTransaction(
                            '34.56',
                            '2015-09-01',
                            '2015-09-02',
                            Transaction::STATUS_OUTSTANDING
                        ),
                    ]
                ),
                true,
            ],
            'one overpayment' => [
                '1234.56',
                new ArrayCollection(
                    [
                        $this->getStubFeeTransaction('2000', '2015-09-01', '2015-09-02'),
                    ]
                ),
                true,
            ],
        ];
    }

    /**
     * @dataProvider getOrganisationProvider
     */
    public function testGetOrganisation($licence, $irfoGvPermit, $expected)
    {
        $this->sut->setLicence($licence);
        $this->sut->setIrfoGvPermit($irfoGvPermit);
        $this->assertSame($expected, $this->sut->getOrganisation());
    }

    public function getOrganisationProvider()
    {
        $organisation = m::mock(Organisation::class);

        return [
            'licence' => [
                m::mock(Licence::class)->makePartial()->setOrganisation($organisation),
                null,
                $organisation,
            ],
            'irfo' => [
                null,
                m::mock(IrfoGvPermit::class)->makePartial()->setOrganisation($organisation),
                $organisation,
            ],
            'neither' => [
                null,
                null,
                null,
            ],
        ];
    }

    /**
     * @dataProvider getCustomerNameProvider
     */
    public function testGetCustomerNameForInvoice($licence, $irfoGvPermit, $expected)
    {
        $this->sut->setLicence($licence);
        $this->sut->setIrfoGvPermit($irfoGvPermit);
        $this->assertEquals($expected, $this->sut->getCustomerNameForInvoice());
    }

    public function getCustomerNameProvider()
    {
        $organisation = m::mock(Organisation::class)
            ->shouldReceive('getName')
            ->andReturn('Foo')
            ->getMock();

        return [
            'licence' => [
                m::mock(Licence::class)->makePartial()->setOrganisation($organisation),
                null,
                'Foo',
            ],
            'irfo' => [
                null,
                m::mock(IrfoGvPermit::class)->makePartial()->setOrganisation($organisation),
                'Foo',
            ],
            'neither' => [
                null,
                null,
                'Miscellaneous payment',
            ],
        ];
    }

    /**
     * @dataProvider getCustomerAddressProvider
     */
    public function testGetCustomerAddressForInvoice($licence, $irfoGvPermit, $expected)
    {
        $this->sut->setLicence($licence);
        $this->sut->setIrfoGvPermit($irfoGvPermit);
        $this->assertEquals($expected, $this->sut->getCustomerAddressForInvoice()->toArray());
    }

    public function getCustomerAddressProvider()
    {
        $address = m::mock(Address::class)
            ->shouldReceive('toArray')
            ->andReturn(
                [
                    'addressLine1' => 'Foo1',
                    'addressLine2' => 'Foo2',
                    'addressLine3' => 'Foo3',
                    'addressLine4' => 'Foo4',
                    'town' => 'FooTown',
                    'postcode' =>'FooPostcode',
                    'countryCode' => 'FooCountry',
                ]
            )
            ->getMock();

        $contactDetails = m::mock(ContactDetails::class)
            ->shouldReceive('getAddress')
            ->andReturn($address)
            ->getMock();

        $organisation = m::mock(Organisation::class)
            ->shouldReceive('getIrfoContactDetails')
            ->andReturn($contactDetails)
            ->getMock();

        return [
            'licence' => [
                m::mock(Licence::class)->makePartial()->setCorrespondenceCd($contactDetails),
                null,
                [
                    'addressLine1' => 'Foo1',
                    'addressLine2' => 'Foo2',
                    'addressLine3' => 'Foo3',
                    'addressLine4' => 'Foo4',
                    'town' => 'FooTown',
                    'postcode' =>'FooPostcode',
                    'countryCode' => 'FooCountry',
                ],
            ],
            'irfo' => [
                null,
                m::mock(IrfoGvPermit::class)->makePartial()->setOrganisation($organisation),
                [
                    'addressLine1' => 'Foo1',
                    'addressLine2' => 'Foo2',
                    'addressLine3' => 'Foo3',
                    'addressLine4' => 'Foo4',
                    'town' => 'FooTown',
                    'postcode' =>'FooPostcode',
                    'countryCode' => 'FooCountry',
                ],
            ],
            'neither' => [
                null,
                null,
                [
                    'addressLine1' => 'Miscellaneous payment',
                    'addressLine2' => null,
                    'addressLine3' => null,
                    'addressLine4' => null,
                    'town' => 'Miscellaneous payment',
                    // hardcoded to DVSA office, CPMS api enforces a valid postcode
                    'postcode' => 'LS9 6NF',
                    'countryCode' => null,
                ],
            ],
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
        $feeStatus = m::mock(RefData::class)->makePartial();
        $feeStatus->setId($status);
        $this->sut->setFeeStatus($feeStatus);

        $this->assertEquals($expected, $this->sut->isPaid());
    }

    public function isPaidProvider()
    {
        return [
            [Entity::STATUS_PAID, true],
            [Entity::STATUS_CANCELLED, false],
            [Entity::STATUS_OUTSTANDING, false],
            ['invalid', false],
        ];
    }

    /**
     * @param string $status
     * @param boolean $expected
     *
     * @dataProvider isOutstandingProvider
     */
    public function testIsOutstanding($status, $expected)
    {
        $feeStatus = m::mock(RefData::class)->makePartial();
        $feeStatus->setId($status);
        $this->sut->setFeeStatus($feeStatus);

        $this->assertEquals($expected, $this->sut->isOutstanding());
    }

    public function isOutstandingProvider()
    {
        return [
            [Entity::STATUS_PAID, false],
            [Entity::STATUS_CANCELLED, false],
            [Entity::STATUS_OUTSTANDING, true],
            ['invalid', false],
        ];
    }

    /**
     * @param string $status
     * @param boolean $expected
     *
     * @dataProvider isCancelledProvider
     */
    public function testIsCancelled($status, $expected)
    {
        $feeStatus = m::mock(RefData::class)->makePartial();
        $feeStatus->setId($status);
        $this->sut->setFeeStatus($feeStatus);

        $this->assertEquals($expected, $this->sut->isCancelled());
    }

    public function isCancelledProvider()
    {
        return [
            [Entity::STATUS_PAID, false],
            [Entity::STATUS_CANCELLED, true],
            [Entity::STATUS_OUTSTANDING, false],
            ['invalid', false],
        ];
    }

    /**
     * @param string $feeAmount
     * @param string $status
     * @param array $feeTransactions
     * @param boolean $expected
     *
     * @dataProvider isFullyOutstandingProvider
     */
    public function testIsFullyOutstanding($feeAmount, $status, $feeTransactions, $expected)
    {
        $feeStatus = m::mock(RefData::class)->makePartial();
        $feeStatus->setId($status);
        $this->sut->setFeeStatus($feeStatus);
        $this->sut->setFeeTransactions(new ArrayCollection($feeTransactions));
        $this->sut->setGrossAmount($feeAmount);

        $this->assertEquals($expected, $this->sut->isFullyOutstanding());
    }

    public function isFullyOutstandingProvider()
    {
        $paid10 = m::mock(FeeTransaction::class);
        $paid10->shouldReceive('getTransaction->isComplete')
            ->andReturn(true);
        $paid10->shouldReceive('getAmount')
            ->andReturn('10.00');

        $pending10 = m::mock(FeeTransaction::class);
        $pending10->shouldReceive('getTransaction->isComplete')
            ->andReturn(false);
        $pending10->shouldReceive('getAmount')
            ->andReturn('10.00');

        return [
            ['10.00', Entity::STATUS_PAID, [], false],
            ['10.00', Entity::STATUS_CANCELLED, [], false],
            ['10.00', Entity::STATUS_OUTSTANDING, [], true],
            ['10.00', Entity::STATUS_PAID, [$paid10], false],
            ['20.00', Entity::STATUS_OUTSTANDING, [$paid10], false],
            ['10.00', Entity::STATUS_OUTSTANDING, [$pending10], true],
        ];
    }

    /**
     * @param string $type
     * @param boolean $expected
     *
     * @dataProvider isBalancingFeeProvider
     */
    public function testIsBalancingFee($type, $expected)
    {
        $feeTypeType = new RefData($type);
        $feeType = new FeeType();
        $feeType->setFeeType($feeTypeType);

        $this->sut->setFeeType($feeType);

        $this->assertEquals($expected, $this->sut->isBalancingFee());
    }

    public function isBalancingFeeProvider()
    {
        return [
            [FeeType::FEE_TYPE_APP, false],
            [FeeType::FEE_TYPE_VAR, false],
            [FeeType::FEE_TYPE_GRANT, false],
            [FeeType::FEE_TYPE_CONT, false],
            [FeeType::FEE_TYPE_VEH, false],
            [FeeType::FEE_TYPE_GRANTINT, false],
            [FeeType::FEE_TYPE_INTVEH, false],
            [FeeType::FEE_TYPE_DUP, false],
            [FeeType::FEE_TYPE_ANN, false],
            [FeeType::FEE_TYPE_GRANTVAR, false],
            [FeeType::FEE_TYPE_BUSAPP, false],
            [FeeType::FEE_TYPE_BUSVAR, false],
            [FeeType::FEE_TYPE_GVANNVEH, false],
            [FeeType::FEE_TYPE_INTUPGRADEVEH, false],
            [FeeType::FEE_TYPE_INTAMENDED, false],
            [FeeType::FEE_TYPE_IRFOPSVAPP, false],
            [FeeType::FEE_TYPE_IRFOPSVANN, false],
            [FeeType::FEE_TYPE_IRFOPSVCOPY, false],
            [FeeType::FEE_TYPE_IRFOGVPERMIT, false],
            [FeeType::FEE_TYPE_ADJUSTMENT, true],
        ];
    }

    /**
     * @param string $type
     * @param boolean $expected
     *
     * @dataProvider isNewApplicationFeeProvider
     */
    public function testIsNewApplicationFee($type, $expected)
    {
        $feeTypeType = new RefData($type);
        $feeType = new FeeType();
        $feeType->setFeeType($feeTypeType);

        $this->sut->setFeeType($feeType);

        $this->assertEquals($expected, $this->sut->isNewApplicationFee());
    }

    public function isNewApplicationFeeProvider()
    {
        return [
            [FeeType::FEE_TYPE_APP, true],
            [FeeType::FEE_TYPE_VAR, false],
            [FeeType::FEE_TYPE_GRANT, false],
            [FeeType::FEE_TYPE_CONT, false],
            [FeeType::FEE_TYPE_VEH, false],
            [FeeType::FEE_TYPE_GRANTINT, false],
            [FeeType::FEE_TYPE_INTVEH, false],
            [FeeType::FEE_TYPE_DUP, false],
            [FeeType::FEE_TYPE_ANN, false],
            [FeeType::FEE_TYPE_GRANTVAR, false],
            [FeeType::FEE_TYPE_BUSAPP, false],
            [FeeType::FEE_TYPE_BUSVAR, false],
            [FeeType::FEE_TYPE_GVANNVEH, false],
            [FeeType::FEE_TYPE_INTUPGRADEVEH, false],
            [FeeType::FEE_TYPE_INTAMENDED, false],
            [FeeType::FEE_TYPE_IRFOPSVAPP, false],
            [FeeType::FEE_TYPE_IRFOPSVANN, false],
            [FeeType::FEE_TYPE_IRFOPSVCOPY, false],
            [FeeType::FEE_TYPE_IRFOGVPERMIT, false],
            [FeeType::FEE_TYPE_ADJUSTMENT, false],
        ];
    }

    /**
     * @param string $type
     * @param boolean $expected
     *
     * @dataProvider isVariationFeeProvider
     */
    public function testIsVariationFee($type, $expected)
    {
        $feeTypeType = new RefData($type);
        $feeType = new FeeType();
        $feeType->setFeeType($feeTypeType);

        $this->sut->setFeeType($feeType);

        $this->assertEquals($expected, $this->sut->isVariationFee());
    }

    public function isVariationFeeProvider()
    {
        return [
            [FeeType::FEE_TYPE_APP, false],
            [FeeType::FEE_TYPE_VAR, true],
            [FeeType::FEE_TYPE_GRANT, false],
            [FeeType::FEE_TYPE_CONT, false],
            [FeeType::FEE_TYPE_VEH, false],
            [FeeType::FEE_TYPE_GRANTINT, false],
            [FeeType::FEE_TYPE_INTVEH, false],
            [FeeType::FEE_TYPE_DUP, false],
            [FeeType::FEE_TYPE_ANN, false],
            [FeeType::FEE_TYPE_GRANTVAR, false],
            [FeeType::FEE_TYPE_BUSAPP, false],
            [FeeType::FEE_TYPE_BUSVAR, false],
            [FeeType::FEE_TYPE_GVANNVEH, false],
            [FeeType::FEE_TYPE_INTUPGRADEVEH, false],
            [FeeType::FEE_TYPE_INTAMENDED, false],
            [FeeType::FEE_TYPE_IRFOPSVAPP, false],
            [FeeType::FEE_TYPE_IRFOPSVANN, false],
            [FeeType::FEE_TYPE_IRFOPSVCOPY, false],
            [FeeType::FEE_TYPE_IRFOGVPERMIT, false],
            [FeeType::FEE_TYPE_ADJUSTMENT, false],
        ];
    }

    /**
     * @param string $type
     * @param boolean $expected
     *
     * @dataProvider isGrantFeeProvider
     */
    public function testIsGrantFee($type, $expected)
    {
        $feeTypeType = new RefData($type);
        $feeType = new FeeType();
        $feeType->setFeeType($feeTypeType);

        $this->sut->setFeeType($feeType);

        $this->assertEquals($expected, $this->sut->isGrantFee());
    }

    public function isGrantFeeProvider()
    {
        return [
            [FeeType::FEE_TYPE_APP, false],
            [FeeType::FEE_TYPE_VAR, false],
            [FeeType::FEE_TYPE_GRANT, true],
            [FeeType::FEE_TYPE_CONT, false],
            [FeeType::FEE_TYPE_VEH, false],
            [FeeType::FEE_TYPE_GRANTINT, false],
            [FeeType::FEE_TYPE_INTVEH, false],
            [FeeType::FEE_TYPE_DUP, false],
            [FeeType::FEE_TYPE_ANN, false],
            [FeeType::FEE_TYPE_GRANTVAR, false],
            [FeeType::FEE_TYPE_BUSAPP, false],
            [FeeType::FEE_TYPE_BUSVAR, false],
            [FeeType::FEE_TYPE_GVANNVEH, false],
            [FeeType::FEE_TYPE_INTUPGRADEVEH, false],
            [FeeType::FEE_TYPE_INTAMENDED, false],
            [FeeType::FEE_TYPE_IRFOPSVAPP, false],
            [FeeType::FEE_TYPE_IRFOPSVANN, false],
            [FeeType::FEE_TYPE_IRFOPSVCOPY, false],
            [FeeType::FEE_TYPE_IRFOGVPERMIT, false],
            [FeeType::FEE_TYPE_ADJUSTMENT, false],
        ];
    }

    /**
     * @dataProvider salesPersonRefProvider
     * @param string $trafficAreaId
     * @param string $costCentreReference
     * @param string $expected
     */
    public function testGetSalesPersonReference($trafficAreaId, $costCentreReference, $expected)
    {
        $licence = m::mock(Licence::class);
        $feeType = m::mock(FeeType::class);

        $licence->shouldReceive('getTrafficArea->getId')
            ->andReturn($trafficAreaId);

        $feeType->shouldReceive('getCostCentreRef')
            ->once()
            ->andReturn($costCentreReference);

        $this->sut->setLicence($licence);
        $this->sut->setFeeType($feeType);

        $this->assertEquals($expected, $this->sut->getSalesPersonReference());
    }

    public function salesPersonRefProvider()
    {
        return [
            ['B', 'TA', 'B'],
            ['C', 'TA', 'C'],
            ['', 'IR', 'IR'],
            ['', 'MGB', 'MGB'],
            ['', 'MNI', 'MNI'],
            ['', 'MR', 'MR'],
        ];
    }

    /**
     * @param FeeType $feeType
     * @param RefData $feeStatus
     * @param array $feeTransactions
     * @param bool $expected
     * @dataProvider canRefundProvider
     */
    public function testCanRefund($feeType, $feeStatus, $feeTransactions, $expected)
    {
        $this->sut
            ->setFeeType($feeType)
            ->setFeeStatus($feeStatus)
            ->setFeeTransactions(new ArrayCollection($feeTransactions));

        $this->assertSame($expected, $this->sut->canRefund());
    }

    /**
     * @return array
     */
    public function canRefundProvider()
    {
        $nonMiscFeeType = m::mock(FeeType::class)
            ->shouldReceive('isMiscellaneous')
            ->andReturn(false)
            ->getMock();
        $miscFeeType = m::mock(FeeType::class)
            ->shouldReceive('isMiscellaneous')
            ->andReturn(true)
            ->getMock();

        $outstanding = new RefData(Entity::STATUS_OUTSTANDING);
        $paid        = new RefData(Entity::STATUS_PAID);
        $cancelled   = new RefData(Entity::STATUS_CANCELLED);

        $nonRefundedFeeTransaction = m::mock(FeeTransaction::class);
        $nonRefundedFeeTransaction
            ->shouldReceive('getTransaction->isPayment')
            ->andReturn(true);
        $nonRefundedFeeTransaction
            ->shouldReceive('isRefundedOrReversed')
            ->andReturn(false);

        $refundedFeeTransaction = m::mock(FeeTransaction::class);
        $refundedFeeTransaction
            ->shouldReceive('getTransaction->isPayment')
            ->andReturn(true);
        $refundedFeeTransaction
            ->shouldReceive('isRefundedOrReversed')
            ->andReturn(true);

        return [
            'std outstanding'  => [$nonMiscFeeType, $outstanding, [], false],
            'std paid'         => [$nonMiscFeeType, $paid, [], false],
            'std cancelled'    => [$nonMiscFeeType, $cancelled, [], false],
            'misc outstanding' => [$miscFeeType, $outstanding, [], false],
            'misc paid'        => [$miscFeeType, $paid, [], false],
            'misc cancelled'   => [$miscFeeType, $cancelled, [], false],
            'std not refunded' => [$nonMiscFeeType, $paid, [$nonRefundedFeeTransaction], true],
            'std refunded'     => [$nonMiscFeeType, $paid, [$refundedFeeTransaction], false],
        ];
    }

    public function testGetFeeTransactionsForRefund()
    {
        $txn1 = m::mock(Transaction::class)
            ->shouldReceive('isPayment')
            ->andReturn(true)
            ->shouldReceive('isComplete')
            ->andReturn(true)
            ->getMock();
        $nonRefundedFeeTransaction = m::mock(FeeTransaction::class);
        $nonRefundedFeeTransaction
            ->shouldReceive('getTransaction')
            ->andReturn($txn1)
            ->shouldReceive('isRefundedOrReversed')
            ->andReturn(false);

        $txn2 = m::mock(Transaction::class)
            ->shouldReceive('isPayment')
            ->andReturn(true)
            ->shouldReceive('isComplete')
            ->andReturn(true)
            ->getMock();
        $refundedFeeTransaction = m::mock(FeeTransaction::class);
        $refundedFeeTransaction
            ->shouldReceive('getTransaction')
            ->andReturn($txn2)
            ->shouldReceive('isRefundedOrReversed')
            ->andReturn(true);

        $this->sut->setFeeTransactions([$nonRefundedFeeTransaction, $refundedFeeTransaction]);

        $this->assertEquals(
            [$nonRefundedFeeTransaction],
            $this->sut->getFeeTransactionsForRefund()
        );
    }

    /**
     * Test VAT calculations, see OLCS-11034
     *
     * @param float $netAmount
     * @param float $rate
     * @param float $expectedVatAmount
     * @param float $expectedGrossAmount
     * @dataProvider vatProvider
     */
    public function testSetVatAndGrossAmountsFromNetAmountUsingRate(
        $netAmount,
        $rate,
        $expectedVatAmount,
        $expectedGrossAmount
    ) {
        $this->sut->setNetAmount($netAmount);

        $this->sut->setVatAndGrossAmountsFromNetAmountUsingRate($rate);

        $this->assertEquals($expectedVatAmount, $this->sut->getVatAmount());
        $this->assertEquals($expectedGrossAmount, $this->sut->getGrossAmount());
    }

    public function vatProvider()
    {
        return [
            'no_vat' => [
                100.00,
                0,
                0,
                100.00,
            ],
            '20pcvat' => [
                100.00,
                20,
                20,
                120.00,
            ],
            '17_5_pcvat' => [
                123.45,
                17.50,
                21.60,
                145.05,
            ],
            'rounding_down' => [
                99.99,
                20,
                19.99, // 19.998 rounded *down*
                119.98,
            ],
        ];
    }
}
