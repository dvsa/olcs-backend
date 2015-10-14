<?php

/**
 * CPMS Version 2 Helper Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Service;

use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address as AddressEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\CpmsResponseException;
use Dvsa\Olcs\Api\Service\CpmsV2HelperService as Sut;
use Dvsa\Olcs\Api\Service\FeesHelperService;
use Dvsa\OlcsTest\Api\MockLoggerTrait;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * CPMS Version 2 Helper Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CpmsV2HelperServiceTest extends MockeryTestCase
{
    use MockLoggerTrait;

    /**
     * @var \Mockery\MockInterface (CpmsClient\Service\ApiService)
     */
    protected $cpmsClient;

    /**
     * @var CpmsHelperService
     */
    protected $sut;

    /**
     * @var \Dvsa\Olcs\Api\Service\FeesHelperService
     */
    protected $feesHelper;

    public function setUp()
    {
        // Mock the CPMS client
        $this->cpmsClient = m::mock()
            ->shouldReceive('getOptions')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getDomain')
                    ->andReturn('fake-domain')
                    ->getMock()
            )
            ->getMock();

        $this->feesHelper = m::mock(FeesHelperService::class);

        // Create service with mocked dependencies
        $this->sut = $this->createService($this->cpmsClient, $this->mockLogger(), $this->feesHelper);

        return parent::setUp();
    }

    private function createService($api, $logger, $feesHelper)
    {
        $sm = m::mock('\Zend\ServiceManager\ServiceLocatorInterface');
        $sm
            ->shouldReceive('get')
            ->with('cpms\service\api')
            ->andReturn($api)
            ->shouldReceive('get')
            ->with('Logger')
            ->andReturn($logger)
            ->shouldReceive('get')
            ->with('FeesHelperService')
            ->andReturn($feesHelper);

        $sut = new Sut();
        return $sut->createService($sm);
    }

    public function testInitiateCardRequest()
    {
        $orgId = 99;
        $redirectUrl = 'http://olcs-selfserve/foo';

        $fees = [
            $this->getStubFee(1, 525.25, FeeEntity::ACCRUAL_RULE_IMMEDIATE, null, $orgId, '2015-08-29'),
            $this->getStubFee(2, 125.25, FeeEntity::ACCRUAL_RULE_LICENCE_START, '2014-12-25', $orgId, '2015-08-30'),
        ];

        $now = (new DateTime())->format('Y-m-d');

        $expectedParams = [
            'customer_reference' => $orgId,
            'payment_data' => [
                [
                    'line_identifier' => '1',
                    'amount' => '525.25',
                    'allocated_amount' => '525.25',
                    'net_amount' => '525.25',
                    'tax_amount' => '0.00',
                    'tax_code' => 'Z',
                    'tax_rate' => '0',
                    'invoice_date' => '2015-08-29',
                    'sales_reference' => '1',
                    'product_reference' => 'GVR_APPLICATION_FEE', // hardcoded
                    'product_description' => 'fee type description',
                    'receiver_reference' => '99',
                    'receiver_name' => 'some organisation',
                    'receiver_address' => [
                        'line_1' => 'Foo',
                        'line_2' => null,
                        'line_3' => null,
                        'line_4' => null,
                        'city' => 'Bar',
                        'postcode' => 'LS9 6NF',
                    ],
                    'rule_start_date' => $now,
                    'deferment_period' => '1',
                    'sales_person_reference' => 'B',
                ],
                [
                    'line_identifier' => '1',
                    'amount' => '125.25',
                    'allocated_amount' => '125.25',
                    'net_amount' => '125.25',
                    'tax_amount' => '0.00',
                    'tax_code' => 'Z',
                    'tax_rate' => '0',
                    'invoice_date' => '2015-08-30',
                    'sales_reference' => '2',
                    'product_reference' => 'GVR_APPLICATION_FEE',
                    'product_description' => 'fee type description',
                    'receiver_reference' => '99',
                    'receiver_name' => 'some organisation',
                    'receiver_address' => [
                        'line_1' => 'Foo',
                        'line_2' => null,
                        'line_3' => null,
                        'line_4' => null,
                        'city' => 'Bar',
                        'postcode' => 'LS9 6NF',
                    ],
                    'rule_start_date' => '2014-12-25',
                    'deferment_period' => '60',
                    'sales_person_reference' => 'B',
                ]
            ],
            'cost_centre' => '12345,67890',
            'total_amount' => '650.50',
            'customer_name' => 'some organisation',
            'customer_manager_name' => 'some organisation',
            'customer_address' =>[
                'line_1' => 'Foo',
                'line_2' => null,
                'line_3' => null,
                'line_4' => null,
                'city' => 'Bar',
                'postcode' => 'LS9 6NF',
            ],
            'redirect_uri' => $redirectUrl,
            'disable_redirection' => true,
            'scope' => 'CARD',
            'refund_overpayment' => false,
        ];

        $response = ['receipt_reference' => 'guid_123'];

        $this->cpmsClient
            ->shouldReceive('post')
            ->with('/api/payment/card', 'CARD', $expectedParams)
            ->once()
            ->andReturn($response);

        $result = $this->sut->initiateCardRequest($redirectUrl, $fees);

        $this->assertSame($response, $result);
    }

    public function testInitiateCardRequestInvalidApiResponse()
    {
        $this->setExpectedException(CpmsResponseException::class, 'Invalid payment response');

        $this->cpmsClient
            ->shouldReceive('post')
            ->with('/api/payment/card', 'CARD', m::any())
            ->andReturn([]);

        $this->sut->initiateCardRequest('http://olcs-selfserve/foo', []);
    }

    public function testRecordCashPaymentWithOverpayment()
    {
        $orgId = 99;

        // user input data
        $receiptDate = '2015-09-10';
        $slipNo      = '12345';
        $amount      = '1000';  // doesn't need to match fee total

        $fees = [
            $this->getStubFee(1, 500.00, FeeEntity::ACCRUAL_RULE_IMMEDIATE, null, $orgId, '2015-08-29'),
            $this->getStubFee(2, 100.00, FeeEntity::ACCRUAL_RULE_LICENCE_START, '2014-12-25', $orgId, '2015-08-30'),
            $this->getStubFee(
                3,
                400.00,
                FeeEntity::ACCRUAL_RULE_IMMEDIATE,
                null,
                $orgId,
                '2015-08-30',
                FeeTypeEntity::FEE_TYPE_ADJUSTMENT
            ),
        ];

        $now = (new DateTime())->format('Y-m-d');

        $expectedParams = [
            'customer_reference' => $orgId,
            'payment_data' => [
                [
                    'line_identifier' => '1',
                    'amount' => '500.00',
                    'allocated_amount' => '500.00',
                    'net_amount' => '500.00',
                    'tax_amount' => '0.00',
                    'tax_code' => 'Z',
                    'tax_rate' => '0',
                    'invoice_date' => '2015-08-29',
                    'sales_reference' => '1',
                    'product_reference' => 'GVR_APPLICATION_FEE', // hardcoded
                    'product_description' => 'fee type description',
                    'receiver_reference' => '99',
                    'receiver_name' => 'some organisation',
                    'receiver_address' => [
                        'line_1' => 'Foo',
                        'line_2' => null,
                        'line_3' => null,
                        'line_4' => null,
                        'city' => 'Bar',
                        'postcode' => 'LS9 6NF',
                    ],
                    'rule_start_date' => $now,
                    'deferment_period' => '1',
                    'sales_person_reference' => 'B',
                ],
                [
                    'line_identifier' => '1',
                    'amount' => '100.00',
                    'allocated_amount' => '100.00',
                    'net_amount' => '100.00',
                    'tax_amount' => '0.00',
                    'tax_code' => 'Z',
                    'tax_rate' => '0',
                    'invoice_date' => '2015-08-30',
                    'sales_reference' => '2',
                    'product_reference' => 'GVR_APPLICATION_FEE',
                    'product_description' => 'fee type description',
                    'receiver_reference' => '99',
                    'receiver_name' => 'some organisation',
                    'receiver_address' => [
                        'line_1' => 'Foo',
                        'line_2' => null,
                        'line_3' => null,
                        'line_4' => null,
                        'city' => 'Bar',
                        'postcode' => 'LS9 6NF',
                    ],
                    'rule_start_date' => '2014-12-25',
                    'deferment_period' => '60',
                    'sales_person_reference' => 'B',
                ]
            ],
            'cost_centre' => '12345,67890',
            'total_amount' => '1000.00',
            'customer_name' => 'some organisation',
            'customer_manager_name' => 'some organisation',
            'customer_address' =>[
                'line_1' => 'Foo',
                'line_2' => null,
                'line_3' => null,
                'line_4' => null,
                'city' => 'Bar',
                'postcode' => 'LS9 6NF',
            ],
            'slip_number' => '12345',
            'batch_number' => '12345',
            'receipt_date' => '2015-09-10',
            'scope' => 'CASH',
            'refund_overpayment' => true,
        ];

         $response = [
            'code' => Sut::RESPONSE_SUCCESS,
            'receipt_reference' => 'OLCS-1234-CASH',
        ];

        $this->cpmsClient
            ->shouldReceive('post')
            ->with('/api/payment/cash', 'CASH', $expectedParams)
            ->once()
            ->andReturn($response);

        $this->feesHelper
            ->shouldReceive('allocatePayments')
            ->with('1000.00', $fees)
            ->andReturn(
                [
                    1 => '500.00',
                    2 => '100.00',
                    3 => '400.00',
                ]
            );

        $result = $this->sut->recordCashPayment($fees, $amount, $receiptDate, null, $slipNo);

        $this->assertSame($response, $result);
    }

    public function testRecordChequePayment()
    {
        $orgId = 99;

        // user input data
        $receiptDate = '2015-09-10';
        $payer       = 'Owen Money';
        $slipNo      = '12345';
        $amount      = '1000';
        $chequeNo    = '0098765';
        $chequeDate  = '2015-09-01';

        $fees = [
            $this->getStubFee(1, 525.25, FeeEntity::ACCRUAL_RULE_IMMEDIATE, null, $orgId, '2015-08-29'),
            $this->getStubFee(2, 125.25, FeeEntity::ACCRUAL_RULE_LICENCE_START, '2014-12-25', $orgId, '2015-08-30'),
        ];

        $now = (new DateTime())->format('Y-m-d');

        $expectedParams = [
            'customer_reference' => $orgId,
            'payment_data' => [
                [
                    'line_identifier' => '1',
                    'amount' => '525.25',
                    'allocated_amount' => '525.25',
                    'net_amount' => '525.25',
                    'tax_amount' => '0.00',
                    'tax_code' => 'Z',
                    'tax_rate' => '0',
                    'invoice_date' => '2015-08-29',
                    'sales_reference' => '1',
                    'product_reference' => 'GVR_APPLICATION_FEE', // hardcoded
                    'product_description' => 'fee type description',
                    'receiver_reference' => '99',
                    'receiver_name' => 'some organisation',
                    'receiver_address' => [
                        'line_1' => 'Foo',
                        'line_2' => null,
                        'line_3' => null,
                        'line_4' => null,
                        'city' => 'Bar',
                        'postcode' => 'LS9 6NF',
                    ],
                    'rule_start_date' => $now,
                    'deferment_period' => '1',
                    'sales_person_reference' => 'B',
                ],
                [
                    'line_identifier' => '1',
                    'amount' => '125.25',
                    'allocated_amount' => '125.25',
                    'net_amount' => '125.25',
                    'tax_amount' => '0.00',
                    'tax_code' => 'Z',
                    'tax_rate' => '0',
                    'invoice_date' => '2015-08-30',
                    'sales_reference' => '2',
                    'product_reference' => 'GVR_APPLICATION_FEE',
                    'product_description' => 'fee type description',
                    'receiver_reference' => '99',
                    'receiver_name' => 'some organisation',
                    'receiver_address' => [
                        'line_1' => 'Foo',
                        'line_2' => null,
                        'line_3' => null,
                        'line_4' => null,
                        'city' => 'Bar',
                        'postcode' => 'LS9 6NF',
                    ],
                    'rule_start_date' => '2014-12-25',
                    'deferment_period' => '60',
                    'sales_person_reference' => 'B',
                ]
            ],
            'cost_centre' => '12345,67890',
            'total_amount' => '1000.00',
            'customer_name' => 'some organisation',
            'customer_manager_name' => 'some organisation',
            'customer_address' =>[
                'line_1' => 'Foo',
                'line_2' => null,
                'line_3' => null,
                'line_4' => null,
                'city' => 'Bar',
                'postcode' => 'LS9 6NF',
            ],
            'slip_number' => '12345',
            'batch_number' => '12345',
            'receipt_date' => '2015-09-10',
            'scope' => 'CHEQUE',
            'cheque_number' => '0098765',
            'cheque_date' => '2015-09-01',
            'name_on_cheque' => $payer,
            'refund_overpayment' => false,
        ];

         $response = [
            'code' => Sut::RESPONSE_SUCCESS,
            'receipt_reference' => 'OLCS-1234-CHEQUE',
        ];

        $this->cpmsClient
            ->shouldReceive('post')
            ->with('/api/payment/cheque', 'CHEQUE', $expectedParams)
            ->once()
            ->andReturn($response);

        $this->feesHelper
            ->shouldReceive('allocatePayments')
            ->with('1000.00', $fees)
            ->andReturn(
                [
                    1 => '525.25',
                    2 => '125.25'
                ]
            );

        $result = $this->sut->recordChequePayment(
            $fees,
            $amount,
            $receiptDate,
            $payer,
            $slipNo,
            $chequeNo,
            $chequeDate
        );

        $this->assertSame($response, $result);
    }


    public function testRecordPostalOrderPayment()
    {
        $orgId = 99;

        // user input data
        $receiptDate = '2015-09-10';
        $slipNo      = '12345';
        $amount      = '1000';
        $poNo        = '00666666';

        $fees = [
            $this->getStubFee(1, 525.25, FeeEntity::ACCRUAL_RULE_IMMEDIATE, null, $orgId, '2015-08-29'),
            $this->getStubFee(2, 125.25, FeeEntity::ACCRUAL_RULE_LICENCE_START, '2014-12-25', $orgId, '2015-08-30'),
        ];

        $now = (new DateTime())->format('Y-m-d');

        $expectedParams = [
            'customer_reference' => $orgId,
            'payment_data' => [
                [
                    'line_identifier' => '1',
                    'amount' => '525.25',
                    'allocated_amount' => '525.25',
                    'net_amount' => '525.25',
                    'tax_amount' => '0.00',
                    'tax_code' => 'Z',
                    'tax_rate' => '0',
                    'invoice_date' => '2015-08-29',
                    'sales_reference' => '1',
                    'product_reference' => 'GVR_APPLICATION_FEE', // hardcoded
                    'product_description' => 'fee type description',
                    'receiver_reference' => '99',
                    'receiver_name' => 'some organisation',
                    'receiver_address' => [
                        'line_1' => 'Foo',
                        'line_2' => null,
                        'line_3' => null,
                        'line_4' => null,
                        'city' => 'Bar',
                        'postcode' => 'LS9 6NF',
                    ],
                    'rule_start_date' => $now,
                    'deferment_period' => '1',
                    'sales_person_reference' => 'B',
                ],
                [
                    'line_identifier' => '1',
                    'amount' => '125.25',
                    'allocated_amount' => '125.25',
                    'net_amount' => '125.25',
                    'tax_amount' => '0.00',
                    'tax_code' => 'Z',
                    'tax_rate' => '0',
                    'invoice_date' => '2015-08-30',
                    'sales_reference' => '2',
                    'product_reference' => 'GVR_APPLICATION_FEE',
                    'product_description' => 'fee type description',
                    'receiver_reference' => '99',
                    'receiver_name' => 'some organisation',
                    'receiver_address' => [
                        'line_1' => 'Foo',
                        'line_2' => null,
                        'line_3' => null,
                        'line_4' => null,
                        'city' => 'Bar',
                        'postcode' => 'LS9 6NF',
                    ],
                    'rule_start_date' => '2014-12-25',
                    'deferment_period' => '60',
                    'sales_person_reference' => 'B',
                ]
            ],
            'cost_centre' => '12345,67890',
            'total_amount' => '1000.00',
            'customer_name' => 'some organisation',
            'customer_manager_name' => 'some organisation',
            'customer_address' =>[
                'line_1' => 'Foo',
                'line_2' => null,
                'line_3' => null,
                'line_4' => null,
                'city' => 'Bar',
                'postcode' => 'LS9 6NF',
            ],
            'slip_number' => '12345',
            'batch_number' => '12345',
            'receipt_date' => '2015-09-10',
            'scope' => 'POSTAL_ORDER',
            'postal_order_number' => '00666666',
            'refund_overpayment' => false,
        ];

         $response = [
            'code' => Sut::RESPONSE_SUCCESS,
            'receipt_reference' => 'OLCS-1234-PO',
        ];

        $this->cpmsClient
            ->shouldReceive('post')
            ->with('/api/payment/postal-order', 'POSTAL_ORDER', $expectedParams)
            ->once()
            ->andReturn($response);

         $this->feesHelper
            ->shouldReceive('allocatePayments')
            ->with('1000.00', $fees)
            ->andReturn(
                [
                    1 => '525.25',
                    2 => '125.25'
                ]
            );

        $result = $this->sut->recordPostalOrderPayment(
            $fees,
            $amount,
            $receiptDate,
            null,
            $slipNo,
            $poNo
        );

        $this->assertSame($response, $result);
    }

    /**
     * Helper function to generate a stub fee entity
     *
     * @param int $id
     * @param string $amount
     * @param string $accrualRule
     * @param string $licenceStartDate
     * @return FeeEntity
     */
    private function getStubFee(
        $id,
        $amount,
        $accrualRule = null,
        $licenceStartDate = null,
        $organisationId = null,
        $invoicedDate = null,
        $feeTypeId = null
    ) {
        $status = new RefData();
        $rule = new RefData();
        if ($accrualRule) {
            $rule->setId($accrualRule);
        }
        $feeType = new FeeTypeEntity();
        $feeType
            ->setAccrualRule($rule)
            ->setDescription('fee type description')
            ->setFeeType((new RefData($feeTypeId)))
            ->setCostCentreRef('TA');

        $organisation = new OrganisationEntity();
        $organisation
            ->setId($organisationId)
            ->setName('some organisation');

        $address = new AddressEntity();
        $address->updateAddress('Foo', null, null, null, 'Bar', 'LS9 6NF', null);

        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence
            ->setOrganisation($organisation)
            ->setCorrespondenceCd(
                m::mock()
                    ->shouldReceive('getAddress')
                    ->andReturn($address)
                    ->getMock()
            );
        if (!is_null($licenceStartDate)) {
            $licence->setInForceDate($licenceStartDate);
        }
        $licence->shouldReceive('getTrafficArea->getId')->andReturn('B');

        $fee = new FeeEntity($feeType, $amount, $status);
        $fee
            ->setId($id)
            ->setInvoiceLineNo(1)
            ->setInvoicedDate($invoicedDate)
            ->setLicence($licence);

        return $fee;
    }

    public function testGetReportList()
    {
        $response = ['stuff'];

        $this->cpmsClient
            ->shouldReceive('get')
            ->with('/api/report', 'REPORT', [])
            ->once()
            ->andReturn($response);

        $result = $this->sut->getReportList();

        $this->assertSame($response, $result);
    }

    public function testRequestReport()
    {
        $code = 'ABC123';
        $start = new \DateTime('2015-10-07 08:57:00');
        $end = new \DateTime('2015-10-08 08:56:59');

        $expectedParams =  [
            'report_code' => $code,
            'filters' => [
                'from' => '2015-10-07 08:57:00',
                'to' => '2015-10-08 08:56:59',
            ],
        ];

        $response = ['stuff'];

        $this->cpmsClient
            ->shouldReceive('post')
            ->with('/api/report', 'REPORT', $expectedParams)
            ->once()
            ->andReturn($response);

        $result = $this->sut->requestReport($code, $start, $end);

        $this->assertSame($response, $result);
    }

    public function testGetReportStatus()
    {
        $reference = 'OLCS-1234-FOO';

        $response = ['stuff'];

        $this->cpmsClient
            ->shouldReceive('get')
            ->with('/api/report/OLCS-1234-FOO/status', 'REPORT', [])
            ->once()
            ->andReturn($response);

        $result = $this->sut->getReportStatus($reference);

        $this->assertSame($response, $result);
    }

    public function testDownloadReport()
    {
        $reference = 'OLCS-1234-FOO';
        $token = 'secretsquirrel';

        $response = "foo,bar,csv,data";

        $this->cpmsClient
            ->shouldReceive('get')
            ->with('/api/report/OLCS-1234-FOO/download?token=secretsquirrel', 'REPORT', [])
            ->once()
            ->andReturn($response);

        $result = $this->sut->downloadReport($reference, $token);

        $this->assertSame($response, $result);
    }
}
