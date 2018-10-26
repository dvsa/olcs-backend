<?php


namespace Dvsa\OlcsTest\Api\Entity\Fee;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity as Entities;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\Fee\Fee as Entity;
use Dvsa\Olcs\Api\Entity\Fee\FeeTransaction;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Fee\Transaction;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermit;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
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

    /** @var  Entity */
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
     * @param ArrayCollection $feeTransactions
     * @param boolean         $expected
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

    public function testHadOutstandingPaymentExcludeWaiveNoPayments()
    {
        $pendingPaymentsTimeout = 3600;

        $this->sut->setFeeTransactions([]);

        $this->assertFalse($this->sut->hasOutstandingPaymentExcludeWaive($pendingPaymentsTimeout));
    }

    public function testHadOutstandingPaymentExcludeWaiveOutstandingNoWaives()
    {
        $pendingPaymentsTimeout = 3600;

        $feeTransaction = m::mock()
            ->shouldReceive('getTransaction')
            ->andReturn(
                m::mock()
                    ->shouldReceive('isOutstanding')
                    ->andReturn(true)
                    ->shouldReceive('getType')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getId')
                            ->andReturn(Transaction::TYPE_PAYMENT)
                            ->getMock()
                    )
                    ->shouldReceive('getCreatedOn')
                    ->andReturn(new DateTime('now'))
                    ->once()
                    ->getMock()
            )
            ->getMock();

        $this->sut->setFeeTransactions([$feeTransaction]);

        $this->assertTrue($this->sut->hasOutstandingPaymentExcludeWaive($pendingPaymentsTimeout));
    }

    public function testHadOutstandingPaymentExcludeWaiveOutstandingWithWaives()
    {
        $pendingPaymentsTimeout = 3600;

        $feeTransaction = m::mock()
            ->shouldReceive('getTransaction')
            ->andReturn(
                m::mock()
                    ->shouldReceive('isOutstanding')
                    ->andReturn(true)
                    ->shouldReceive('getType')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getId')
                            ->andReturn(Transaction::TYPE_WAIVE)
                            ->getMock()
                    )
                    ->shouldReceive('getCreatedOn')
                    ->andReturn(new DateTime('now'))
                    ->once()
                    ->getMock()
            )
            ->getMock();

        $this->sut->setFeeTransactions([$feeTransaction]);

        $this->assertFalse($this->sut->hasOutstandingPaymentExcludeWaive($pendingPaymentsTimeout));
    }

    public function testHadOutstandingPaymentExcludeWaiveOutstandingTimeoutReached()
    {
        $pendingPaymentsTimeout = 3600;

        $feeTransaction = m::mock()
            ->shouldReceive('getTransaction')
            ->andReturn(
                m::mock()
                    ->shouldReceive('isOutstanding')
                    ->andReturn(true)
                    ->shouldReceive('getType')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getId')
                            ->andReturn(Transaction::TYPE_PAYMENT)
                            ->getMock()
                    )
                    ->shouldReceive('getCreatedOn')
                    ->andReturn(new DateTime('2017-01-01'))
                    ->once()
                    ->getMock()
            )
            ->getMock();

        $this->sut->setFeeTransactions([$feeTransaction]);

        $this->assertFalse($this->sut->hasOutstandingPaymentExcludeWaive($pendingPaymentsTimeout));
    }

    /**
     * @param string $accrualRuleId,
     * @param Licence $licence
     * @param EcmtPermitApplication $ecmtPermitApplication EcmtPermitApplication
     * @param DateTime $expected
     *
     * @dataProvider ruleStartDateProvider
     */
    public function testGetRuleStartDate($accrualRuleId, $licence, $ecmtPermitApplication, $expected)
    {
        $feeType = m::mock()
            ->shouldReceive('getAccrualRule')
            ->andReturn((new RefData())->setId($accrualRuleId))
            ->getMock();

        $this->sut->setFeeType($feeType);
        if (!is_null($licence)) {
            $this->sut->setLicence($licence);
        }

        if (!is_null($ecmtPermitApplication)) {
            $this->sut->setEcmtPermitApplication($ecmtPermitApplication);
        }

        $this->assertEquals($expected, $this->sut->getRuleStartDate());
    }

    public function ruleStartDateProvider()
    {
        $now = new DateTime();
        $futureContinuationDate = new Datetime('4 years 10 days midnight');

        $irhpPermitStartDate = new DateTime('2015-04-04');

        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);
        $ecmtPermitApplication
            ->shouldReceive('getIrhpPermitApplications->first->getIrhpPermitWindow->getIrhpPermitStock->getValidFrom')
            ->with(true)
            ->andReturn($irhpPermitStartDate);

        $ecmtPermitApplicationWithoutIrhpPermitApp = m::mock(EcmtPermitApplication::class);
        $ecmtPermitApplicationWithoutIrhpPermitApp
            ->shouldReceive('getIrhpPermitApplications->first')
            ->andReturn(null);

        return [
            'immediate' => [
                Entity::ACCRUAL_RULE_IMMEDIATE,
                null,
                null,
                $now,
            ],
            'licence start' => [
                Entity::ACCRUAL_RULE_LICENCE_START,
                m::mock()
                    ->shouldReceive('getInForceDate')
                    ->andReturn('2015-04-03')
                    ->getMock(),
                null,
                new DateTime('2015-04-03'),
            ],
            'licence start date missing' => [
                Entity::ACCRUAL_RULE_LICENCE_START,
                m::mock()
                    ->shouldReceive('getInForceDate')
                    ->andReturn(null)
                    ->getMock(),
                null,
                null,
            ],
            'continuation' => [
                Entity::ACCRUAL_RULE_CONTINUATION,
                m::mock()
                    ->shouldReceive('getExpiryDate')
                    ->andReturn('2015-04-03')
                    ->getMock(),
                null,
                new DateTime('2015-04-04'),
            ],
            'continuation date more than 4 year in future' => [
                Entity::ACCRUAL_RULE_CONTINUATION,
                m::mock()
                    ->shouldReceive('getExpiryDate')
                    ->andReturn($futureContinuationDate->format('Y-m-d'))
                    ->getMock(),
                null,
                $futureContinuationDate->sub(new \DateInterval('P5Y'))->add(new \DateInterval('P1D')),
            ],
            'continuation date missing' => [
                Entity::ACCRUAL_RULE_CONTINUATION,
                m::mock()
                    ->shouldReceive('getExpiryDate')
                    ->andReturn(null)
                    ->getMock(),
                null,
                null,
            ],
            'IRHP permit - 3 months - no application' => [
                Entity::ACCRUAL_RULE_IRHP_PERMIT_3_MONTHS,
                null,
                $ecmtPermitApplicationWithoutIrhpPermitApp,
                null,
            ],
            'IRHP permit - 3 months - valid from date' => [
                Entity::ACCRUAL_RULE_IRHP_PERMIT_3_MONTHS,
                null,
                $ecmtPermitApplication,
                $irhpPermitStartDate,
            ],
            'IRHP permit - 6 months - no application' => [
                Entity::ACCRUAL_RULE_IRHP_PERMIT_6_MONTHS,
                null,
                $ecmtPermitApplicationWithoutIrhpPermitApp,
                null,
            ],
            'IRHP permit - 6 months - valid from date' => [
                Entity::ACCRUAL_RULE_IRHP_PERMIT_6_MONTHS,
                null,
                $ecmtPermitApplication,
                $irhpPermitStartDate,
            ],
            'IRHP permit - 9 months - no application' => [
                Entity::ACCRUAL_RULE_IRHP_PERMIT_9_MONTHS,
                null,
                $ecmtPermitApplicationWithoutIrhpPermitApp,
                null,
            ],
            'IRHP permit - 9 months - valid from date' => [
                Entity::ACCRUAL_RULE_IRHP_PERMIT_9_MONTHS,
                null,
                $ecmtPermitApplication,
                $irhpPermitStartDate,
            ],
            'IRHP permit - 12 months - no application' => [
                Entity::ACCRUAL_RULE_IRHP_PERMIT_12_MONTHS,
                null,
                $ecmtPermitApplicationWithoutIrhpPermitApp,
                null,
            ],
            'IRHP permit - 12 months - valid from date' => [
                Entity::ACCRUAL_RULE_IRHP_PERMIT_12_MONTHS,
                null,
                $ecmtPermitApplication,
                $irhpPermitStartDate,
            ],
            'invalid' => [
                'foo',
                null,
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
            'IRHP permit - 3 months' => [
                Entity::ACCRUAL_RULE_IRHP_PERMIT_3_MONTHS,
                3,
            ],
            'IRHP permit - 6 months' => [
                Entity::ACCRUAL_RULE_IRHP_PERMIT_6_MONTHS,
                6,
            ],
            'IRHP permit - 9 months' => [
                Entity::ACCRUAL_RULE_IRHP_PERMIT_9_MONTHS,
                9,
            ],
            'IRHP permit - 12 months' => [
                Entity::ACCRUAL_RULE_IRHP_PERMIT_12_MONTHS,
                12,
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

    public function testGetProcessedByNullNoTransaction()
    {
        static::assertNull($this->sut->getProcessedBy());
    }

    public function testGetProcessedByNullNoTransactionUser()
    {
        $ft1 = $this->getStubFeeTransaction('1234.56', '2015-09-01', '2015-09-02 12:34:56');
        $this->sut->getFeeTransactions()->add($ft1);

        static::assertNull($this->sut->getProcessedBy());
    }

    private function getStubFeeTransaction(
        $amount,
        $completedDate,
        $createdOn,
        $statusId = Transaction::STATUS_COMPLETE,
        $typeId = Transaction::TYPE_PAYMENT,
        $comment = '',
        $transactionId = null
    ) {
        $transaction = new Transaction();
        $transaction->setId($transactionId);
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
            'bug OLCS-11509' => [
                '4.56',
                new ArrayCollection([]),
                '4.56',
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

    public function testGetLatestFeeTransactionNull()
    {
        /** @var Entity $sut */
        $sut = m::mock(Entity::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getTransaction')->never()
            ->getMock();

        $feeTr1 = $this->getStubFeeTransaction(5, '2017-06-05', null, null, null, '', 9001);
        $sut->setFeeTransactions(new ArrayCollection([$feeTr1]));

        //  call
        static::assertNull($sut->getPaymentMethod());
    }

    public function testGetCalculatedBundleValues()
    {
        /** @var Entity $sut */
        $sut = m::mock(Entity::class)
            ->makePartial()
            ->shouldReceive('getOutstandingAmount')->once()->andReturn('unit_Outstanding')
            ->shouldReceive('getLatestPaymentRef')->once()->andReturn('unit_receiptNo')
            ->shouldReceive('getGrossAmount')->once()->andReturn('unit_Amount')
            ->shouldReceive('isRuleBeforeInvoiceDate')->once()->andReturn('unit_RuleDateBeforeInvoice')
            ->shouldReceive('isExpiredForLicence')->once()->andReturn('unit_ExpiredForLicence')
            ->shouldReceive('isOutstanding')->once()->andReturn('unit_isOutstanding')
            ->shouldReceive('isEcmtIssuingFee')->once()->andReturn('unit_isEcmtIssuing')
            ->getMock();

        static::assertEquals(
            [
                'outstanding' => 'unit_Outstanding',
                'receiptNo' => 'unit_receiptNo',
                'amount' => 'unit_Amount',
                'ruleDateBeforeInvoice' => 'unit_RuleDateBeforeInvoice',
                'isExpiredForLicence' => 'unit_ExpiredForLicence',
                'isOutstanding' => 'unit_isOutstanding',
                'isEcmtIssuingFee' => 'unit_isEcmtIssuing'
            ],
            $sut->getCalculatedBundleValues()
        );
    }

    /**
     * @dataProvider providerExpiredForLicence
     */
    public function testIsExpiredForLicence($expiryDate, $expected)
    {
        $mockLicence = m::mock()
            ->shouldReceive('getExpiryDate')
            ->andReturn('foo')
            ->once()
            ->shouldReceive('getExpiryDateAsDate')
            ->andReturn($expiryDate)
            ->once()
            ->getMock();

        /** @var Entity $sut */
        $sut = m::mock(Entity::class)
            ->makePartial()
            ->shouldReceive('getLicence')
            ->andReturn($mockLicence)
            ->once()
            ->getMock();

        $this->assertEquals($sut->isExpiredForLicence(), $expected);
    }

    public function providerExpiredForLicence()
    {
        return [
            [
                \DateTime::createFromFormat('Y-m-d', '3000-01-01'),
                false
            ],
            [
                \DateTime::createFromFormat('Y-m-d', '1000-01-01'),
                true
            ]
        ];
    }

    /**
     * @dataProvider getOrganisationProvider
     */
    public function testGetOrganisation($licence, $irfoGvPermit, $irfoPsvAuth, $expected)
    {
        $this->sut->setLicence($licence);
        $this->sut->setIrfoGvPermit($irfoGvPermit);
        $this->sut->setIrfoPsvAuth($irfoPsvAuth);
        $this->assertSame($expected, $this->sut->getOrganisation());
    }

    public function getOrganisationProvider()
    {
        $organisation = m::mock(Organisation::class);

        return [
            'licence' => [
                m::mock(Licence::class)->makePartial()->setOrganisation($organisation),
                null,
                null,
                $organisation,
            ],
            'irfo gv permit' => [
                null,
                m::mock(IrfoGvPermit::class)->makePartial()->setOrganisation($organisation),
                null,
                $organisation,
            ],
            'irfo psv auth' => [
                null,
                null,
                m::mock(IrfoPsvAuth::class)->makePartial()->setOrganisation($organisation),
                $organisation,
            ],
            'neither' => [
                null,
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
                null,
            ],
        ];
    }

    /**
     * @dataProvider getCustomerAddressProvider
     */
    public function testGetCustomerAddressForInvoice($licence, $irfoGvPermit, $irfoPsvAuth, $expected)
    {
        $this->sut->setLicence($licence);
        $this->sut->setIrfoGvPermit($irfoGvPermit);
        $this->sut->setIrfoPsvAuth($irfoPsvAuth);

        $actual = $this->sut->getCustomerAddressForInvoice();
        $this->assertEquals($expected, ($actual ? $actual->toArray() : $actual));
    }

    public function testGetCustomerAddressForInvoiceEmpty()
    {
        $this->assertNull($this->sut->getCustomerAddressForInvoice());
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
            'irfo gv permit' => [
                null,
                m::mock(IrfoGvPermit::class)->makePartial()->setOrganisation($organisation),
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
            'irfo psv auth' => [
                null,
                null,
                m::mock(IrfoPsvAuth::class)->makePartial()->setOrganisation($organisation),
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
            //  licence and organisation - have not corr details
            [
                'licence' => m::mock(Licence::class)->makePartial(),
                'irfoGvPermit' => null,
                'irfoPsvAuth' => new IrfoPsvAuth(
                    new Organisation(),
                    new Entities\Irfo\IrfoPsvAuthType(),
                    new RefData()
                ),
                'expected' => null,
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
        $this->sut->setFeeStatus(new RefData($status));

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
        $this->sut->setFeeStatus(new RefData($status));

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
        $this->sut->setFeeStatus(new RefData($status));

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
        $this->sut->setFeeStatus(new RefData($status));
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
     * @param string $trafficAreaRef
     * @param string $costCentreReference
     * @param string $expected
     */
    public function testGetSalesPersonReference($trafficAreaRef, $costCentreReference, $expected)
    {
        $licence = m::mock(Licence::class);
        $feeType = m::mock(FeeType::class);

        $licence->shouldReceive('getTrafficArea->getSalesPersonReference')
            ->andReturn($trafficAreaRef);

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

        // Not migrated
        $txn1 = m::mock(Transaction::class);
        // Migrated
        $txn2 = m::mock(Transaction::class);
        // Refunded
        $txn3 = m::mock(Transaction::class);

        $nonRefundedFeeTransaction = m::mock(FeeTransaction::class);
        $nonRefundedFeeTransaction->shouldReceive('getTransaction')->andReturn($txn1);

        $txn1->shouldReceive('isCompletePaymentOrAdjustment')
            ->andReturn(true)
            ->shouldReceive('isMigrated')
            ->andReturn(false);

        $nonRefundedFeeTransaction
            ->shouldReceive('isRefundedOrReversed')
            ->andReturn(false);

        $migratedTransaction = m::mock(FeeTransaction::class);
        $migratedTransaction->shouldReceive('getTransaction')->andReturn($txn2);
        $txn2->shouldReceive('isCompletePaymentOrAdjustment')
            ->andReturn(true)
            ->shouldReceive('isMigrated')
            ->andReturn(true);

        $migratedTransaction
            ->shouldReceive('isRefundedOrReversed')
            ->andReturn(false);

        $refundedFeeTransaction = m::mock(FeeTransaction::class);
        $refundedFeeTransaction->shouldReceive('getTransaction')->andReturn($txn3);
        $txn3->shouldReceive('isCompletePaymentOrAdjustment')
            ->andReturn(true)
            ->shouldReceive('isMigrated')
            ->andReturn(false);

        $refundedFeeTransaction
            ->shouldReceive('isRefundedOrReversed')
            ->andReturn(true);

        return [
            'std outstanding'  => [$nonMiscFeeType, $outstanding, [], false],
            'std paid'         => [$nonMiscFeeType, $paid, [], false],
            'std cancelled'    => [$nonMiscFeeType, $cancelled, [], false],
            'misc outstanding' => [$miscFeeType, $outstanding, [], false],
            'misc paid'        => [$miscFeeType, $paid, [$nonRefundedFeeTransaction], true],
            'misc cancelled'   => [$miscFeeType, $cancelled, [], false],
            'std not refunded' => [$nonMiscFeeType, $paid, [$nonRefundedFeeTransaction], true],
            'migrated'         => [$nonMiscFeeType, $paid, [$migratedTransaction], false],
            'std refunded'     => [$nonMiscFeeType, $paid, [$refundedFeeTransaction], false],
        ];
    }

    public function testGetFeeTransactionsForRefund()
    {
        $txn1 = m::mock(Transaction::class)
            ->shouldReceive('isCompletePaymentOrAdjustment')
            ->andReturn(true)
            ->getMock();
        $nonRefundedFeeTransaction = m::mock(FeeTransaction::class);
        $nonRefundedFeeTransaction
            ->shouldReceive('getTransaction')
            ->andReturn($txn1)
            ->shouldReceive('isRefundedOrReversed')
            ->andReturn(false)
            ->shouldReceive('getReversedFeeTransaction')
            ->andReturn(null);

        $txn2 = m::mock(Transaction::class)
            ->shouldReceive('isCompletePaymentOrAdjustment')
            ->andReturn(true)
            ->getMock();
        $refundedFeeTransaction = m::mock(FeeTransaction::class);
        $refundedFeeTransaction
            ->shouldReceive('getTransaction')
            ->andReturn($txn2)
            ->shouldReceive('isRefundedOrReversed')
            ->andReturn(true)
            ->shouldReceive('getReversedFeeTransaction')
            ->andReturn(null);

        $reversingFeeTransaction = m::mock(FeeTransaction::class);
        $reversingFeeTransaction
            ->shouldReceive('getTransaction')
            ->andReturn($txn2)
            ->shouldReceive('isRefundedOrReversed')
            ->andReturn(false)
            ->shouldReceive('getReversedFeeTransaction')
            ->andReturn(m::mock(FeeTransaction::class));

        $this->sut->setFeeTransactions(
            [
                $nonRefundedFeeTransaction,
                $refundedFeeTransaction,
                $reversingFeeTransaction,
            ]
        );

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

    /**
     * Test pounds to pence conversion
     *
     * @param  string $input
     * @param  int $expected
     * @dataProvider amountToPenceProvider
     */
    public function testAmountToPence($input, $expected)
    {
        $this->assertSame($expected, Entity::amountToPence($input));
    }

    public function amountToPenceProvider()
    {
        return [
            ['1.00', (int) 100],
            ['1.01', (int) 101],
            ['1.005', (int) 101],
            ['1.001', (int) 100],
            ['4.56', (int) 456],
            ['35.16', (int) 3516],
            ['1234.56', (int) 123456],
            ['-4.56', (int) -456],
        ];
    }

    /**
     * Test pence to pounds conversion
     *
     * @param  string $input
     * @param  int $expected
     * @dataProvider amountToPoundsProvider
     */
    public function testAmountToPounds($input, $expected)
    {
        $this->assertSame($expected, Entity::amountToPounds($input));
    }

    public function amountToPoundsProvider()
    {
        return [
            [100, '1.00'],
            [101, '1.01'],
            [456, '4.56'],
            [3516, '35.16'],
            [123456, '1234.56'],
            [-456, '-4.56'],
        ];
    }

    /**
     * @dataProvider amountByTransactionProvider
     */
    public function testGetAmountAllocatedByTransactionId($feeTransactions, $transactionId, $expected)
    {
        $this->sut->setFeeTransactions($feeTransactions);
        $this->assertEquals($expected, $this->sut->getAmountAllocatedByTransactionId($transactionId));
    }

    public function amountByTransactionProvider()
    {
        return [
            'no transactions' => [
                new ArrayCollection(),
                99,
                null,
            ],
            'one complete transaction matched' => [
                new ArrayCollection(
                    [
                        $this->getStubFeeTransaction(
                            '234.56',
                            '2015-09-01',
                            '2015-09-02',
                            Transaction::STATUS_COMPLETE,
                            Transaction::TYPE_PAYMENT,
                            '',
                            99
                        ),
                    ]
                ),
                99,
                '234.56',
            ],
            'one complete transaction unmatched' => [
                new ArrayCollection(
                    [
                        $this->getStubFeeTransaction(
                            '234.56',
                            '2015-09-01',
                            '2015-09-02',
                            Transaction::STATUS_COMPLETE,
                            Transaction::TYPE_PAYMENT,
                            '',
                            98
                        ),
                    ]
                ),
                99,
                null,
            ],
        ];
    }

    /**
     * @dataProvider dataProviderIsRuleBeforeInvoiceDate
     */
    public function testIsRuleBeforeInvoiceDate($expected, $invoicedDate)
    {
        // force the rule date to be now
        $feeType = m::mock()
            ->shouldReceive('getAccrualRule')
            ->andReturn((new RefData())->setId(Entity::ACCRUAL_RULE_IMMEDIATE))
            ->getMock();
        $this->sut->setFeeType($feeType);

        $this->sut->setInvoicedDate($invoicedDate);

        $this->assertSame($expected, $this->sut->isRuleBeforeInvoiceDate());
    }

    public function dataProviderIsRuleBeforeInvoiceDate()
    {
        return [
            [true, (new DateTime())->modify('1 second')],
            [true, (new DateTime())->modify('1 day')],
            [false, new DateTime()],
            [false, (new DateTime())->modify('-1 day')],
            [false, (new DateTime())->modify('-1 second')],
            [false, null],
        ];
    }

    /**
     * @dataProvider dataProviderGetInvoicedDateTime
     */
    public function testGetInvoicedDateTime($expected, $invoicedDate)
    {
        $this->sut->setInvoicedDate($invoicedDate);
        $this->assertEquals($expected, $this->sut->getInvoicedDateTime());
    }

    public function dataProviderGetInvoicedDateTime()
    {
        return [
            [new DateTime('2016-01-25'), new DateTime('2016-01-25')],
            [new DateTime('2016-01-25'), '2016-01-25'],
            [null, null],
        ];
    }

    /**
     * @dataProvider dpTestGetRelatedOrganisation
     */
    public function testGetRelatedOrganisation(Entity $sut, $expect)
    {
        static::assertSame($expect, $sut->getRelatedOrganisation());
    }

    public function dpTestGetRelatedOrganisation()
    {
        /** @var Organisation $mockOrg */
        $mockOrg = m::mock(Organisation::class);
        /** @var RefData $mockRef */
        $mockRef = m::mock(RefData::class);

        $licence = new Licence($mockOrg, $mockRef);

        return [
            [
                'sut' => $this->instantiate(Entity::class)->setApplication(
                    new Entities\Application\Application($licence, $mockRef, false)
                ),
                'expect' => $mockOrg,
            ],
            [
                'sut' => $this->instantiate(Entity::class)->setBusReg(
                    (new Entities\Bus\BusReg())->setLicence($licence)
                ),
                'expect' => $mockOrg,
            ],
            [
                'sut' => $this->instantiate(Entity::class)->setLicence($licence),
                'expect' => $mockOrg,
            ],
            [
                'sut' => $this->instantiate(Entity::class)->setIrfoGvPermit(
                    new IrfoGvPermit($mockOrg, new Entities\Irfo\IrfoGvPermitType(), $mockRef)
                ),
                'expect' => $mockOrg,
            ],
            [
                'sut' => $this->instantiate(Entity::class)->setIrfoPsvAuth(
                    new IrfoPsvAuth($mockOrg, new Entities\Irfo\IrfoPsvAuthType(), $mockRef)
                ),
                'expect' => $mockOrg,
            ],
            [
                'sut' => $this->instantiate(Entity::class),
                'expect' => null,
            ],
        ];
    }
}
