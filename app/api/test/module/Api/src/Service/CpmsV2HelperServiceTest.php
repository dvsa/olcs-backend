<?php

namespace Dvsa\OlcsTest\Api\Service;

use CpmsClient\Service\ApiService;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address as AddressEntity;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeTransaction as FeeTransactionEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Fee\Transaction as TransactionEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\CpmsResponseException;
use Dvsa\Olcs\Api\Service\CpmsV2HelperService;
use Dvsa\Olcs\Api\Service\FeesHelperService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;

/**
 * @covers Dvsa\Olcs\Api\Service\CpmsV2HelperService
 */
class CpmsV2HelperServiceTest extends MockeryTestCase
{
    const CHEQUE_NR = 100001;

    /** @var CpmsV2HelperService */
    protected $sut;

    /** @var \Mockery\MockInterface (CpmsClient\Service\ApiService) */
    protected $cpmsClient;

    /** @var \Dvsa\Olcs\Api\Service\FeesHelperService | m\MockInterface */
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
        $this->sut = $this->createService($this->cpmsClient, $this->feesHelper);

        return parent::setUp();
    }

    private function createService($api, $feesHelper, $config = [])
    {
        /** @var \Zend\ServiceManager\ServiceLocatorInterface | m\MockInterface $sm */
        $sm = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $sm
            ->shouldReceive('get')
            ->with('cpms\service\api')
            ->andReturn($api)
            ->shouldReceive('get')
            ->with('FeesHelperService')
            ->andReturn($feesHelper)
            ->shouldReceive('get')
            ->with('config')
            ->andReturn($config);

        $sut = new CpmsV2HelperService();
        return $sut->createService($sm);
    }

    public function testHandleResponse()
    {
        $data = ['unit_data'];
        $ref = 'unit_ref';

        $this->cpmsClient
            ->shouldReceive('put')
            ->once()
            ->with('/api/gateway/' . $ref . '/complete', ApiService::SCOPE_CARD, $data)
            ->andReturn('EXPECTED');

        static::assertEquals('EXPECTED', $this->sut->handleResponse($ref, $data));
    }

    /**
     * @dataProvider dpTestGetPaymentStatus
     */
    public function testGetPaymentStatus($response, $expect)
    {
        $ref = 'unit_Ref';

        /** @var CpmsV2HelperService | m\MockInterface $sut */
        $sut = m::mock(CpmsV2HelperService::class . '[send]')
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('send')
            ->with('get', '/api/payment/'.$ref, ApiService::SCOPE_QUERY_TXN, m::type('array'))
            ->once()
            ->andReturn($response)
            ->getMock();

        static::assertEquals($expect, $sut->getPaymentStatus($ref));
    }

    public function dpTestGetPaymentStatus()
    {
        return [
            [
                'response' => [
                    'payment_status' => [
                        'code' => 'EXPECT',
                    ],
                ],
                'expect' => 'EXPECT',
            ],
            [
                'response' => [],
                'expect' => null,
            ],
        ];
    }

    /**
     * @dataProvider miscParamsProvider
     */
    public function testInitiateCardRequest($miscParams, $expectedCustomer, $expectedReceiver)
    {
        $orgId = 99;
        $redirectUrl = 'http://olcs-selfserve/foo';

        $fees = [
            $this->getStubFee(1, 525.25, FeeEntity::ACCRUAL_RULE_IMMEDIATE, null, $orgId, '2015-08-29'),
            $this->getStubFee(2, 125.25, FeeEntity::ACCRUAL_RULE_LICENCE_START, '2014-12-25', $orgId, '2015-08-30'),
        ];

        $now = (new DateTime())->format('Y-m-d');

        $expectedParams = array_merge(
            $expectedCustomer,
            [
                'payment_data' => [
                    array_merge(
                        $expectedReceiver,
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
                            'product_reference' => 'fee type description',
                            'product_description' => 'fee type description',
                            'rule_start_date' => $now,
                            'deferment_period' => '1',
                            'country_code' => 'GB',
                            'sales_person_reference' => 'Traffic Area Ref',
                        ]
                    ),
                    array_merge(
                        $expectedReceiver,
                        [
                            'line_identifier' => '2',
                            'amount' => '125.25',
                            'allocated_amount' => '125.25',
                            'net_amount' => '125.25',
                            'tax_amount' => '0.00',
                            'tax_code' => 'Z',
                            'tax_rate' => '0',
                            'invoice_date' => '2015-08-30',
                            'sales_reference' => '2',
                            'product_reference' => 'fee type description',
                            'product_description' => 'fee type description',
                            'rule_start_date' => '2014-12-25',
                            'deferment_period' => '60',
                            'country_code' => 'GB',
                            'sales_person_reference' => 'Traffic Area Ref',
                        ]
                    )
                ],
                'total_amount' => '650.50',
                'redirect_uri' => $redirectUrl,
                'disable_redirection' => true,
                'scope' => 'CARD',
                'refund_overpayment' => false,
                'country_code' => 'GB',
            ]
        );

        $response = ['receipt_reference' => 'guid_123'];

        $this->cpmsClient
            ->shouldReceive('post')
            ->with('/api/payment/card', 'CARD', $expectedParams)
            ->once()
            ->andReturn($response);

        $result = $this->sut->initiateCardRequest($redirectUrl, $fees, $miscParams);

        $this->assertSame($response, $result);
    }

    public function miscParamsProvider()
    {
        return [
            'mics payment' => [
                [
                    'customer_reference' => 'foo',
                    'customer_name' => 'bar',
                    'customer_address' => [
                        'addressLine1' => 'line1',
                        'addressLine2' => 'line2',
                        'addressLine3' => 'line3',
                        'addressLine4' => 'line4',
                        'town' => 'town',
                        'postcode' => 'pc'
                    ]
                ],
                [
                    'customer_reference' => 'foo',
                    'customer_name' => 'bar',
                    'customer_manager_name' => 'bar',
                    'customer_address' => [
                        'line_1' => 'line1',
                        'line_2' => 'line2',
                        'line_3' => 'line3',
                        'line_4' => 'line4',
                        'city' => 'town',
                        'postcode' => 'pc'
                    ]
                ],
                [
                    'receiver_reference' => 'foo',
                    'receiver_name' => 'bar',
                    'receiver_address' => [
                        'line_1' => 'line1',
                        'line_2' => 'line2',
                        'line_3' => 'line3',
                        'line_4' => 'line4',
                        'city' => 'town',
                        'postcode' => 'pc'
                    ]
                ]
            ],
            'usual payment' => [
                [],
                [
                    'customer_reference' => 99,
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
                ],
                [
                    'receiver_reference' => 'OB1234567',
                    'receiver_name' => 'some organisation',
                    'receiver_address' => [
                        'line_1' => 'Foo',
                        'line_2' => null,
                        'line_3' => null,
                        'line_4' => null,
                        'city' => 'Bar',
                        'postcode' => 'LS9 6NF',
                    ],
                ]
            ]
        ];
    }

    /**
     * @dataProvider miscParamsProvider
     */
    public function testInitiateCnpRequest($miscParams, $expectedCustomer, $expectedReceiver)
    {
        $orgId = 99;
        $redirectUrl = 'http://olcs-selfserve/foo';

        $fees = [
            $this->getStubFee(1, 525.25, FeeEntity::ACCRUAL_RULE_IMMEDIATE, null, $orgId, '2015-08-29'),
            $this->getStubFee(2, 125.25, FeeEntity::ACCRUAL_RULE_LICENCE_START, '2014-12-25', $orgId, '2015-08-30'),
        ];

        $now = (new DateTime())->format('Y-m-d');

        $expectedParams = array_merge(
            $expectedCustomer,
            [
                'payment_data' => [
                    array_merge(
                        $expectedReceiver,
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
                            'product_reference' => 'fee type description',
                            'product_description' => 'fee type description',
                            'rule_start_date' => $now,
                            'deferment_period' => '1',
                            'country_code' => 'GB',
                            'sales_person_reference' => 'Traffic Area Ref',
                        ]
                    ),
                    array_merge(
                        $expectedReceiver,
                        [
                            'line_identifier' => '2',
                            'amount' => '125.25',
                            'allocated_amount' => '125.25',
                            'net_amount' => '125.25',
                            'tax_amount' => '0.00',
                            'tax_code' => 'Z',
                            'tax_rate' => '0',
                            'invoice_date' => '2015-08-30',
                            'sales_reference' => '2',
                            'product_reference' => 'fee type description',
                            'product_description' => 'fee type description',
                            'rule_start_date' => '2014-12-25',
                            'deferment_period' => '60',
                            'country_code' => 'GB',
                            'sales_person_reference' => 'Traffic Area Ref',
                        ]
                    )
                ],
                'total_amount' => '650.50',
                'redirect_uri' => $redirectUrl,
                'disable_redirection' => true,
                'scope' => 'CNP',
                'refund_overpayment' => false,
                'country_code' => 'GB',
            ]
        );

        $response = ['receipt_reference' => 'guid_123'];

        $this->cpmsClient
            ->shouldReceive('post')
            ->with('/api/payment/cardholder-not-present', 'CNP', $expectedParams)
            ->once()
            ->andReturn($response);

        $result = $this->sut->initiateCnpRequest($redirectUrl, $fees, $miscParams);

        $this->assertSame($response, $result);
    }

    /**
     * @dataProvider miscParamsProvider
     */
    public function testInitiateStoredCardRequest($miscParams, $expectedCustomer, $expectedReceiver)
    {
        $orgId = 99;
        $redirectUrl = 'http://olcs-selfserve/foo';

        $fees = [
            $this->getStubFee(1, 525.25, FeeEntity::ACCRUAL_RULE_IMMEDIATE, null, $orgId, '2015-08-29'),
            $this->getStubFee(2, 125.25, FeeEntity::ACCRUAL_RULE_LICENCE_START, '2014-12-25', $orgId, '2015-08-30'),
        ];

        $now = (new DateTime())->format('Y-m-d');

        $expectedParams = array_merge(
            $expectedCustomer,
            [
            'payment_data' => [
                array_merge(
                    $expectedReceiver,
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
                        'product_reference' => 'fee type description',
                        'product_description' => 'fee type description',
                        'rule_start_date' => $now,
                        'deferment_period' => '1',
                        'country_code' => 'GB',
                        'sales_person_reference' => 'Traffic Area Ref',
                    ]
                ),
                array_merge(
                    $expectedReceiver,
                    [
                        'line_identifier' => '2',
                        'amount' => '125.25',
                        'allocated_amount' => '125.25',
                        'net_amount' => '125.25',
                        'tax_amount' => '0.00',
                        'tax_code' => 'Z',
                        'tax_rate' => '0',
                        'invoice_date' => '2015-08-30',
                        'sales_reference' => '2',
                        'product_reference' => 'fee type description',
                        'product_description' => 'fee type description',
                        'rule_start_date' => '2014-12-25',
                        'deferment_period' => '60',
                        'country_code' => 'GB',
                        'sales_person_reference' => 'Traffic Area Ref',
                    ]
                )
            ],
            'total_amount' => '650.50',
            'redirect_uri' => $redirectUrl,
            'disable_redirection' => true,
            'scope' => 'STORED_CARD',
            'refund_overpayment' => false,
            'country_code' => 'GB',
            ]
        );

        $response = ['receipt_reference' => 'guid_123'];

        $this->cpmsClient
            ->shouldReceive('post')
            ->with('/api/payment/stored-card/STORED_CARD', 'STORED_CARD', $expectedParams)
            ->once()
            ->andReturn($response);

        $result = $this->sut->initiateStoredCardRequest($redirectUrl, $fees, 'STORED_CARD', $miscParams);

        $this->assertSame($response, $result);
    }

    public function testInitiateCardRequestInvalidApiResponse()
    {
        $this->setExpectedException(CpmsResponseException::class, 'Invalid payment response', 400);

        $this->cpmsClient
            ->shouldReceive('post')
            ->with('/api/payment/card', 'CARD', m::any())
            ->andReturn([]);

        $this->cpmsClient
            ->shouldReceive('getClient->getHttpClient->getResponse->getStatusCode')
            ->andReturn(400);

        $this->sut->initiateCardRequest('http://olcs-selfserve/foo', []);
    }

    public function testGetListStoredCards()
    {
        $this->cpmsClient
            ->shouldReceive('get')
            ->with('/api/stored-card', 'STORED_CARD', [])
            ->andReturn(['FOO']);

        $this->assertSame(['FOO'], $this->sut->getListStoredCards());
    }

    /**
     * @dataProvider miscParamsProvider
     */
    public function testRecordCashPaymentWithOverpayment($miscParams, $expectedCustomer, $expectedReceiver)
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

        $expectedParams = array_merge(
            $expectedCustomer,
            [
                'payment_data' => [
                    array_merge(
                        $expectedReceiver,
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
                            'product_reference' => 'fee type description',
                            'product_description' => 'fee type description',
                            'rule_start_date' => $now,
                            'deferment_period' => '1',
                            'country_code' => 'GB',
                            'sales_person_reference' => 'Traffic Area Ref',
                        ]
                    ),
                    array_merge(
                        $expectedReceiver,
                        [
                            'line_identifier' => '2',
                            'amount' => '100.00',
                            'allocated_amount' => '100.00',
                            'net_amount' => '100.00',
                            'tax_amount' => '0.00',
                            'tax_code' => 'Z',
                            'tax_rate' => '0',
                            'invoice_date' => '2015-08-30',
                            'sales_reference' => '2',
                            'product_reference' => 'fee type description',
                            'product_description' => 'fee type description',
                            'rule_start_date' => '2014-12-25',
                            'deferment_period' => '60',
                            'country_code' => 'GB',
                            'sales_person_reference' => 'Traffic Area Ref',
                        ]
                    )
                ],
                'total_amount' => '1000.00',
                'slip_number' => '12345',
                'batch_number' => '12345',
                'receipt_date' => '2015-09-10',
                'scope' => 'CASH',
                'refund_overpayment' => true,
                'country_code' => 'GB',
            ]
        );

         $response = [
            'code' => CpmsV2HelperService::RESPONSE_SUCCESS,
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

        $result = $this->sut->recordCashPayment($fees, $amount, $receiptDate, $slipNo, $miscParams);

        $this->assertSame($response, $result);
    }

    /**
     * @dataProvider miscParamsProvider
     */
    public function testRecordChequePayment($miscParams, $expectedCustomer, $expectedReceiver)
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

        $expectedParams = array_merge(
            $expectedCustomer,
            [
                'payment_data' => [
                    array_merge(
                        $expectedReceiver,
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
                            'product_reference' => 'fee type description',
                            'product_description' => 'fee type description',
                            'rule_start_date' => $now,
                            'deferment_period' => '1',
                            'country_code' => 'GB',
                            'sales_person_reference' => 'Traffic Area Ref',
                        ]
                    ),
                    array_merge(
                        $expectedReceiver,
                        [
                            'line_identifier' => '2',
                            'amount' => '125.25',
                            'allocated_amount' => '125.25',
                            'net_amount' => '125.25',
                            'tax_amount' => '0.00',
                            'tax_code' => 'Z',
                            'tax_rate' => '0',
                            'invoice_date' => '2015-08-30',
                            'sales_reference' => '2',
                            'product_reference' => 'fee type description',
                            'product_description' => 'fee type description',
                            'rule_start_date' => '2014-12-25',
                            'deferment_period' => '60',
                            'country_code' => 'GB',
                            'sales_person_reference' => 'Traffic Area Ref',
                        ]
                    )
                ],
                'total_amount' => '1000.00',
                'slip_number' => '12345',
                'batch_number' => '12345',
                'receipt_date' => '2015-09-10',
                'scope' => 'CHEQUE',
                'cheque_number' => '0098765',
                'cheque_date' => '2015-09-01',
                'name_on_cheque' => $payer,
                'refund_overpayment' => false,
                'country_code' => 'GB',
            ]
        );

        $response = [
            'code' => CpmsV2HelperService::RESPONSE_SUCCESS,
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
            $chequeDate,
            $miscParams
        );

        $this->assertSame($response, $result);
    }

    /**
     * @dataProvider miscParamsProvider
     */
    public function testRecordPostalOrderPayment($miscParams, $expectedCustomer, $expectedReceiver)
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

        $expectedParams = array_merge(
            $expectedCustomer,
            [
                'payment_data' => [
                    array_merge(
                        $expectedReceiver,
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
                            'product_reference' => 'fee type description',
                            'product_description' => 'fee type description',
                            'rule_start_date' => $now,
                            'deferment_period' => '1',
                            'country_code' => 'GB',
                            'sales_person_reference' => 'Traffic Area Ref',
                        ]
                    ),
                    array_merge(
                        $expectedReceiver,
                        [
                            'line_identifier' => '2',
                            'amount' => '125.25',
                            'allocated_amount' => '125.25',
                            'net_amount' => '125.25',
                            'tax_amount' => '0.00',
                            'tax_code' => 'Z',
                            'tax_rate' => '0',
                            'invoice_date' => '2015-08-30',
                            'sales_reference' => '2',
                            'product_reference' => 'fee type description',
                            'product_description' => 'fee type description',
                            'rule_start_date' => '2014-12-25',
                            'deferment_period' => '60',
                            'country_code' => 'GB',
                            'sales_person_reference' => 'Traffic Area Ref',
                        ]
                    )
                ],
                'total_amount' => '1000.00',
                'slip_number' => '12345',
                'batch_number' => '12345',
                'receipt_date' => '2015-09-10',
                'scope' => 'POSTAL_ORDER',
                'postal_order_number' => '00666666',
                'refund_overpayment' => false,
                'country_code' => 'GB',
            ]
        );

         $response = [
            'code' => CpmsV2HelperService::RESPONSE_SUCCESS,
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
            $slipNo,
            $poNo,
            $miscParams
        );

        $this->assertSame($response, $result);
    }

    /**
     * @dataProvider authCodeProvider
     */
    public function testGetPaymentAuthCode($authCode)
    {
        $response = ['auth_code' => $authCode];

        $this->cpmsClient
            ->shouldReceive('get')
            ->with('/api/payment/MY-REFERENCE/auth-code', 'QUERY_TXN', [])
            ->once()
            ->andReturn($response);

        $result = $this->sut->getPaymentAuthCode('MY-REFERENCE');

        $this->assertSame($authCode, $result);
    }

    public function authCodeProvider()
    {
        return [
            ['AUTH123'],
            [null]
        ];
    }

    /**
     * @param string $paymentMethod
     * @param string $expectedScope
     * @param string $expectedEndpointSuffix
     * @param array $miscParams
     * @dataProvider reversalProvider
     */
    public function testReversePayment($paymentMethod, $expectedScope, $expectedEndpointSuffix, $customer, $miscParams)
    {
        $orgId = 99;

        $response = [
            'code' => CpmsV2HelperService::PAYMENT_PAYMENT_CHARGED_BACK,
            'message' => 'ok',
            'receipt_reference' => 'REVERSAL_REFERENCE',
        ];

        $expectedParams = array_merge(
            $customer,
            ['scope' => $expectedScope]
        );

        $this->cpmsClient
            ->shouldReceive('post')
            ->with('/api/payment/MY-REFERENCE/'.$expectedEndpointSuffix, $expectedScope, $expectedParams)
            ->once()
            ->andReturn($response);

        $fees = [
            $this->getStubFee(1, 100.00, FeeEntity::ACCRUAL_RULE_IMMEDIATE, null, $orgId, '2015-11-04'),
        ];

        $result = $this->sut->reversePayment('MY-REFERENCE', $paymentMethod, $fees, $miscParams);

        $this->assertSame($response, $result);
    }

    public function reversalProvider()
    {
        return [
            'cheque' => [
                FeeEntity::METHOD_CHEQUE,
                'CHEQUE_RD',
                'reversal',
                [
                    'customer_reference' => 'foo',
                    'customer_name' => 'bar',
                    'customer_manager_name' => 'bar',
                    'customer_address' => [
                        'line_1' => 'line1',
                        'line_2' => 'line2',
                        'line_3' => 'line3',
                        'line_4' => 'line4',
                        'city' => 'town',
                        'postcode' => 'postcode',
                    ]
                ],
                [
                    'customer_reference' => 'foo',
                    'customer_name' => 'bar',
                    'customer_address' => [
                        'addressLine1' => 'line1',
                        'addressLine2' => 'line2',
                        'addressLine3' => 'line3',
                        'addressLine4' => 'line4',
                        'town' => 'town',
                        'postcode' => 'postcode',
                    ]
                ]
            ],
            'cash' => [
                FeeEntity::METHOD_CASH,
                'CASH',
                'reversal',
                [
                    'customer_reference' => 99,
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
                ],
                []
            ],
            'po' => [
                FeeEntity::METHOD_POSTAL_ORDER,
                'POSTAL_ORDER',
                'reversal',
                [
                    'customer_reference' => 99,
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
                ],
                []
            ],
            'card digital' => [
                FeeEntity::METHOD_CARD_ONLINE,
                'CHARGE_BACK',
                'chargeback',
                [
                    'customer_reference' => 99,
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
                ],
                []
            ],
            'card assisted digital' => [
                FeeEntity::METHOD_CARD_OFFLINE,
                'CHARGE_BACK',
                'chargeback',
                [
                    'customer_reference' => 99,
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
                ],
                []
            ],
        ];
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
        $feeTypeId = null,
        $licNo = 'OB1234567'
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
            ->setCostCentreRef('TA')
            ->setVatCode('Z')
            ->setProductReference('TEST_PRODUCT_REF');

        $organisation = new OrganisationEntity();
        $organisation
            ->setId($organisationId)
            ->setName('some organisation');

        $address = new AddressEntity();
        $address->updateAddress('Foo', null, null, null, 'Bar', 'LS9 6NF', null);

        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence
            ->setOrganisation($organisation)
            ->setLicNo($licNo)
            ->setCorrespondenceCd(
                m::mock()
                    ->shouldReceive('getAddress')
                    ->andReturn($address)
                    ->getMock()
            );
        if (!is_null($licenceStartDate)) {
            $licence->setInForceDate($licenceStartDate);
        }
        $licence->shouldReceive('getTrafficArea->getSalesPersonReference')->andReturn('Traffic Area Ref');

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

    /**
     * @dataProvider miscParamsProvider
     */
    public function testRefundFeeSinglePayment($miscParams, $expectedCustomer, $expectedReceiver)
    {
        $isMiscellaneous = count($miscParams) > 0 ? true : false;
        $fee = m::mock(FeeEntity::class);

        $ft = m::mock(FeeTransactionEntity::class);
        $ft->shouldReceive('getAmount')->andReturn('100.00');
        $ft->shouldReceive('getTransaction')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getPaymentMethod')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getId')
                            ->andReturn(FeeEntity::METHOD_CARD_ONLINE)
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->shouldReceive('getReference')
                    ->andReturn('payment_ref')
                    ->times(3)
                    ->getMock()
            )
            ->getMock();

        $address = new Address();
        $address->updateAddress('Foo', null, null, null, 'Bar', 'LS9 6NF');
        $fee
            ->shouldReceive('getFeeTransactionsForRefund')
            ->andReturn([$ft])
            ->shouldReceive('getOrganisation')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(99)
                ->getMock()
            )
            ->shouldReceive('getId')
            ->andReturn(101)
            ->shouldReceive('isBalancingFee')
            ->andReturn(false)
            ->shouldReceive('getInvoiceLineNo')
            ->andReturn('LINE_NO')
            ->shouldReceive('getAmount')
            ->andReturn('200.00')
            ->shouldReceive('getOutstandingAmount')
            ->andReturn('0.00')
            ->shouldReceive('getInvoicedDate')
            ->andReturn(new \DateTime('2015-10-09'))
            ->shouldReceive('getCustomerNameForInvoice')
            ->andReturn('some organisation')
            ->shouldReceive('getCustomerAddressForInvoice')
            ->andReturn($address)
            ->shouldReceive('getRuleStartDate')
            ->andReturn(new \DateTime('2015-10-12'))
            ->shouldReceive('getDefermentPeriod')
            ->andReturn('1')
            ->shouldReceive('getSalesPersonReference')
            ->andReturn('TEST_SALES_PERSON_REF')
            ->shouldReceive('getNetAmount')
            ->andReturn('12.00')
            ->shouldReceive('getVatAmount')
            ->andReturn('15.00')
            ->shouldReceive('getGrossAmount')
            ->andReturn('9.99')
            ->shouldReceive('getLicence')
            ->andReturn(
                m::mock()
                ->shouldReceive('getLicNo')
                ->andReturn('OB1234567')
                ->getMock()
            );

        $fee->shouldReceive('getFeeType->getDescription')->andReturn('TEST_FEE_TYPE');
        $fee->shouldReceive('getFeeType->getVatCode')->andReturn('VAT_CODE');
        $fee->shouldReceive('getFeeType->getVatRate')->andReturn('VAT_RATE');
        $fee->shouldReceive('getFeeType->getCountryCode')->andReturn('NI');
        $fee->shouldReceive('getFeeType->isMiscellaneous')->andReturn($isMiscellaneous);
        $fee->shouldReceive('getFeeType->getIrfoFeeType')->andReturnNull();
        $fee->shouldReceive('getFeeType->getFeeType->getId')->andReturn('APP');

        $ft->shouldReceive('getFee')->andReturn($fee);

        $params = array_merge(
            $expectedCustomer,
            [
                'receipt_reference' => 'payment_ref',
                'refund_reason' => 'Refund',
                'payment_data' => [
                    array_merge(
                        $expectedReceiver,
                        [
                            'line_identifier' => '101',
                            'amount' => '100.00',
                            'allocated_amount' => '0.00',
                            'net_amount' => '12.00',
                            'tax_amount' => '15.00',
                            'tax_code' => 'VAT_CODE',
                            'tax_rate' => 'VAT_RATE',
                            'invoice_date' => '2015-10-09',
                            'sales_reference' => '101',
                            'product_reference' => 'TEST_FEE_TYPE',
                            'product_description' => 'TEST_FEE_TYPE',
                            'rule_start_date' => '2015-10-12',
                            'deferment_period' => '1',
                            'country_code' => 'NI',
                            'sales_person_reference' => 'TEST_SALES_PERSON_REF',
                        ]
                    )
                ],
                'scope' => 'REFUND',
                'total_amount' => '100.00',
                'country_code' => 'NI',
                'auth_code' => '123'
            ]
        );

        $response = [
            'code' => CpmsV2HelperService::PAYMENT_REFUNDED,
            'receipt_reference' => 'RECEIPT_REF',
        ];

        $this->cpmsClient
            ->shouldReceive('post')
            ->with('/api/payment/payment_ref/refund', 'REFUND', $params)
            ->once()
            ->andReturn($response)
            ->shouldReceive('get')
            ->with('/api/payment/payment_ref/auth-code', 'QUERY_TXN', [])
            ->andReturn(
                ['auth_code' => '123']
            )
            ->once()
            ->getMock();

        $result = $this->sut->refundFee($fee, $miscParams);

        $this->assertSame(['payment_ref' => 'RECEIPT_REF'], $result);
    }

    public function testRefundFeeSinglePaymentErrorResponse()
    {
        $this->setExpectedException(CpmsResponseException::class, 'Invalid refund response', 401);

        $fee = m::mock(FeeEntity::class);

        $ft = m::mock(FeeTransactionEntity::class);
        $ft->shouldReceive('getAmount')->andReturn('100.00');
        $ft->shouldReceive('getTransaction')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getPaymentMethod')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getId')
                            ->andReturn(FeeEntity::METHOD_CARD_ONLINE)
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->shouldReceive('getReference')
                    ->andReturn('payment_ref')
                    ->twice(2)
                    ->getMock()
            )
            ->getMock();

        $address = new Address();
        $address->updateAddress('ADDR1', 'ADDR2', 'ADDR3', 'ADDR4', 'TOWN', 'POSTCODE');
        $fee
            ->shouldReceive('getFeeTransactionsForRefund')
            ->andReturn([$ft])
            ->shouldReceive('getOrganisation')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(99)
                ->once()
                ->getMock()
            )
            ->shouldReceive('getId')
            ->andReturn(101)
            ->shouldReceive('isBalancingFee')
            ->andReturn(false)
            ->shouldReceive('getInvoiceLineNo')
            ->andReturn('LINE_NO')
            ->shouldReceive('getAmount')
            ->andReturn('200.00')
            ->shouldReceive('getOutstandingAmount')
            ->andReturn('0.00')
            ->shouldReceive('getInvoicedDate')
            ->andReturn(new \DateTime('2015-10-09'))
            ->shouldReceive('getCustomerNameForInvoice')
            ->andReturn('Test Customer')
            ->shouldReceive('getCustomerAddressForInvoice')
            ->andReturn($address)
            ->shouldReceive('getRuleStartDate')
            ->andReturn(new \DateTime('2015-10-12'))
            ->shouldReceive('getDefermentPeriod')
            ->andReturn('1')
            ->shouldReceive('getSalesPersonReference')
            ->andReturn('TEST_SALES_PERSON_REF')
            ->shouldReceive('getNetAmount')
            ->andReturn('12.00')
            ->shouldReceive('getVatAmount')
            ->andReturn('15.00')
            ->shouldReceive('getGrossAmount')
            ->andReturn('9.99')
            ->shouldReceive('getLicence')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getLicNo')
                    ->andReturn('OB1234567')
                    ->getMock()
            );

        $fee->shouldReceive('getFeeType->getDescription')->andReturn('TEST_FEE_TYPE');
        $fee->shouldReceive('getFeeType->getVatCode')->andReturn('VAT_CODE');
        $fee->shouldReceive('getFeeType->getVatRate')->andReturn('VAT_RATE');
        $fee->shouldReceive('getFeeType->getCountryCode')->andReturn('NI');
        $fee->shouldReceive('getFeeType->isMiscellaneous')->andReturn(false);
        $fee->shouldReceive('getFeeType->getIrfoFeeType')->andReturnNull();
        $fee->shouldReceive('getFeeType->getFeeType->getId')->andReturn('APP');

        $ft->shouldReceive('getFee')->andReturn($fee);

        $this->cpmsClient
            ->shouldReceive('post')
            ->with('/api/payment/payment_ref/refund', 'REFUND', m::any())
            ->andReturn([])
            ->shouldReceive('get')
            ->with('/api/payment/payment_ref/auth-code', 'QUERY_TXN', [])
            ->andReturn(
                ['auth_code' => '123']
            )
            ->once()
            ->getMock();

        $this->cpmsClient
            ->shouldReceive('getClient->getHttpClient->getResponse->getStatusCode')
            ->andReturn(401);

        $this->sut->refundFee($fee);
    }

    /**
     * @dataProvider miscParamsProvider
     */
    public function testRefundFeeMultiplePayments($miscParams, $expectedCustomer, $expectedReceiver)
    {
        $isMiscellaneous = count($miscParams) > 0 ? true : false;
        $fee = m::mock(FeeEntity::class);

        $ft = m::mock(FeeTransactionEntity::class);
        $ft
            ->shouldReceive('getTransaction->getReference')
            ->andReturn('payment_ref');
        $ft
            ->shouldReceive('getAmount')
            ->andReturn('100.00');

        $ft2 = m::mock(FeeTransactionEntity::class);
        $ft2
            ->shouldReceive('getTransaction->getReference')
            ->andReturn('payment_ref');
        $ft2
            ->shouldReceive('getAmount')
            ->andReturn('201.00');

        $address = new Address();
        $address->updateAddress('Foo', null, null, null, 'Bar', 'LS9 6NF');
        $fee
            ->shouldReceive('getFeeTransactionsForRefund')
            ->andReturn([$ft, $ft2])
            ->shouldReceive('getOrganisation')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getId')
                    ->andReturn(99)
                    ->getMock()
            )
            ->shouldReceive('getId')
            ->andReturn(101)
            ->shouldReceive('isBalancingFee')
            ->andReturn(false)
            ->shouldReceive('getInvoiceLineNo')
            ->andReturn('LINE_NO')
            ->shouldReceive('getAmount')
            ->andReturn('200.00')
            ->shouldReceive('getOutstandingAmount')
            ->andReturn('0.00')
            ->shouldReceive('getInvoicedDate')
            ->andReturn(new \DateTime('2015-10-09'))
            ->shouldReceive('getCustomerNameForInvoice')
            ->andReturn('some organisation')
            ->shouldReceive('getCustomerAddressForInvoice')
            ->andReturn($address)
            ->shouldReceive('getRuleStartDate')
            ->andReturn(new \DateTime('2015-10-12'))
            ->shouldReceive('getDefermentPeriod')
            ->andReturn('1')
            ->shouldReceive('getSalesPersonReference')
            ->andReturn('TEST_SALES_PERSON_REF')
            ->shouldReceive('getNetAmount')
            ->andReturn('12.00')
            ->shouldReceive('getVatAmount')
            ->andReturn('15.00')
            ->shouldReceive('getGrossAmount')
            ->andReturn('9.99')
            ->shouldReceive('getLicence')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getLicNo')
                    ->andReturn('OB1234567')
                    ->getMock()
            );

        $fee->shouldReceive('getFeeType->getDescription')
            ->andReturn('TEST_FEE_TYPE');
        $fee->shouldReceive('getFeeType->getVatCode')->andReturn('VAT_CODE');
        $fee->shouldReceive('getFeeType->getVatRate')->andReturn('VAT_RATE');
        $fee->shouldReceive('getFeeType->getCountryCode')->andReturn('NI');
        $fee->shouldReceive('getFeeType->isMiscellaneous')->andReturn($isMiscellaneous);
        $fee->shouldReceive('getFeeType->getIrfoFeeType')->andReturnNull();
        $fee->shouldReceive('getFeeType->getFeeType->getId')->andReturn('APP');

        $ft->shouldReceive('getFee')->andReturn($fee);
        $ft2->shouldReceive('getFee')->andReturn($fee);

        $params =  array_merge(
            $expectedCustomer,
            [
                'scope' => 'REFUND',
                'payments' => [
                    [
                        'receipt_reference' => 'payment_ref',
                        'refund_reason' => 'Refund',
                        'country_code' => 'NI',
                        'payment_data' => [
                            array_merge(
                                $expectedReceiver,
                                [
                                    'line_identifier' => '101',
                                    'amount' => '100.00',
                                    'allocated_amount' => '0.00',
                                    'net_amount' => '12.00',
                                    'tax_amount' => '15.00',
                                    'tax_code' => 'VAT_CODE',
                                    'tax_rate' => 'VAT_RATE',
                                    'invoice_date' => '2015-10-09',
                                    'sales_reference' => '101',
                                    'product_reference' => 'TEST_FEE_TYPE',
                                    'product_description' => 'TEST_FEE_TYPE',
                                    'rule_start_date' => '2015-10-12',
                                    'deferment_period' => '1',
                                    'country_code' => 'NI',
                                    'sales_person_reference' => 'TEST_SALES_PERSON_REF',
                                ]
                            ),
                        ],
                    ],
                    [
                        'receipt_reference' => 'payment_ref',
                        'refund_reason' => 'Refund',
                        'country_code' => 'NI',
                        'payment_data' => [
                            array_merge(
                                $expectedReceiver,
                                [
                                    'line_identifier' => '101',
                                    'amount' => '201.00',
                                    'allocated_amount' => '0.00',
                                    'net_amount' => '12.00',
                                    'tax_amount' => '15.00',
                                    'tax_code' => 'VAT_CODE',
                                    'tax_rate' => 'VAT_RATE',
                                    'invoice_date' => '2015-10-09',
                                    'sales_reference' => '101',
                                    'product_reference' => 'TEST_FEE_TYPE',
                                    'product_description' => 'TEST_FEE_TYPE',
                                    'rule_start_date' => '2015-10-12',
                                    'deferment_period' => '1',
                                    'country_code' => 'NI',
                                    'sales_person_reference' => 'TEST_SALES_PERSON_REF',
                                ]
                            )
                        ],
                    ],
                ],
            ]
        );

        $response = [
            'code' => CpmsV2HelperService::RESPONSE_SUCCESS,
            'receipt_references' => [
                'foo' => 'bar',
                'baz' => 'bat',
            ],
        ];

        $this->cpmsClient
            ->shouldReceive('post')
            ->with('/api/refund', 'REFUND', $params)
            ->once()
            ->andReturn($response);

        $result = $this->sut->refundFee($fee, $miscParams);

        $this->assertSame($response['receipt_references'], $result);
    }

    public function testBatchRefundWithInvalidResponse()
    {
        $this->setExpectedException(CpmsResponseException::class, 'Invalid refund response', 401);

        $fee = m::mock(FeeEntity::class);
        $fee
            ->shouldReceive('getFeeTransactionsForRefund')
            ->andReturn([])
            ->shouldReceive('getOrganisation')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(99)
                ->once()
                ->getMock()
            )
            ->shouldReceive('getCustomerNameForInvoice')
            ->andReturn('foo')
            ->twice()
            ->shouldReceive('getCustomerAddressForInvoice')
            ->andReturn(
                [
                    'addressLine1' => 'line1',
                    'addressLine2' => 'line2',
                    'addressLine3' => 'line3',
                    'addressLine4' => 'line4',
                    'town' => 'town',
                    'postcode' => 'postcode'
                ]
            )
            ->once();

        $this->cpmsClient
            ->shouldReceive('post')
            ->with('/api/refund', 'REFUND', m::any())
            ->andReturn([]);

        $this->cpmsClient
            ->shouldReceive('getClient->getHttpClient->getResponse->getStatusCode')
            ->andReturn(401);

        $this->sut->batchRefund($fee);
    }

    /**
     * @dataProvider dpTestAdjustTransaction
     */
    public function testAdjustTransaction($paymentMethod, $expectParams)
    {
        $response = [
            'code' => CpmsV2HelperService::RESPONSE_SUCCESS,
            'message' => 'ok',
            'receipt_reference' => 'ADJUSTMENT_REFERENCE',
        ];

        $expectedParams =
            $expectParams +
            [
                'customer_reference' => '99',
                'payment_data' => [
                    [
                        'line_identifier' => '100',
                        'amount' => '100.00',
                        'allocated_amount' => '10.00',
                        'net_amount' => '100.00',
                        'tax_amount' => '0.00',
                        'tax_code' => 'Z',
                        'tax_rate' => 0,
                        'invoice_date' => null,
                        'sales_reference' => '100',
                        'product_reference' => 'fee type description',
                        'product_description' => 'fee type description',
                        'receiver_reference' => 'OB1234567',
                        'receiver_name' => 'some organisation',
                        'receiver_address' => [
                            'line_1' => 'Foo',
                            'line_2' => null,
                            'line_3' => null,
                            'line_4' => null,
                            'city' => 'Bar',
                            'postcode' => 'LS9 6NF',
                        ],
                        'rule_start_date' => null,
                        'deferment_period' => '',
                        'country_code' => 'GB',
                        'sales_person_reference' => 'Traffic Area Ref',
                    ],
                ],
                'total_amount' => '10.00',
                'customer_name' => 'some organisation',
                'customer_manager_name' => 'some organisation',
                'customer_address' => [
                    'line_1' => 'Foo',
                    'line_2' => null,
                    'line_3' => null,
                    'line_4' => null,
                    'city' => 'Bar',
                    'postcode' => 'LS9 6NF',
                ],
                'refund_overpayment' => true,
                'country_code' => 'GB',
                'cheque_date' => '2013-12-11',
                'slip_number' => '1235',
                'batch_number' => '1235',
                'name_on_cheque' => 'Dan2',
                'scope' => 'ADJUSTMENT',
            ];

        $this->cpmsClient
            ->shouldReceive('post')
            ->with('/api/payment/ORIGINAL_REFERENCE/adjustment', 'ADJUSTMENT', $expectedParams)
            ->once()
            ->andReturn($response);

        $fee1 = $this->getStubFee(100, '100.00', null, null, 99);
        $fee2 = $this->getStubFee(101, '100.00', null, null, 99);
        $fee3 = $this->getStubFee(103, '9.99', null, null, 99, null, FeeTypeEntity::FEE_TYPE_ADJUSTMENT);
        $fees = [$fee1, $fee2, $fee3];

        $originalTransaction = m::mock(TransactionEntity::class)
            ->shouldReceive('getReference')->once()->andReturn('ORIGINAL_REFERENCE')
            ->getMock();

        $newTransaction = m::mock(TransactionEntity::class);
        $newTransaction
            ->shouldReceive('getChequePoDate')->once()->andReturn(new \DateTime('2013-12-11'))
            ->shouldReceive('getPayingInSlipNumber')->times(2)->andReturn('1235')
            ->shouldReceive('getPayerName')->once()->andReturn('Dan2')
            ->shouldReceive('getAmountAfterAdjustment')->once()->andReturn('10.00')
            ->shouldReceive('getFees')->once()->andReturn($fees)
            ->shouldReceive('getAmountAllocatedToFeeId')->with(100)->andReturn('10.00')
            ->shouldReceive('getAmountAllocatedToFeeId')->with(101)->andReturn('0.00')
            ->shouldReceive('getChequePoNumber')->atMost(1)->andReturn(self::CHEQUE_NR)
            ->shouldReceive('getPaymentMethod->getId')->once()->andReturn($paymentMethod);

        $this->feesHelper
            ->shouldReceive('allocatePayments')
            ->with('10.00', $fees)
            ->andReturn([100 => '10.00']);

        $result = $this->sut->adjustTransaction($originalTransaction, $newTransaction);

        $this->assertSame($response, $result);
    }

    public function dpTestAdjustTransaction()
    {
        return [
            [
                'paymentMethod' => FeeEntity::METHOD_CHEQUE,
                'expectedParams' => [
                    'cheque_number' => self::CHEQUE_NR,
                    'postal_order_number' => '',
                ],
            ],
            [
            'paymentMethod' => FeeEntity::METHOD_POSTAL_ORDER,
                'expectedParams' => [
                    'cheque_number' => '',
                    'postal_order_number' => self::CHEQUE_NR,
                ],
            ],
            [
                'paymentMethod' => FeeEntity::METHOD_CASH,
                'expectedParams' => [
                    'cheque_number' => '',
                    'postal_order_number' => '',
                ],
            ],
        ];
    }

    public function testCreateServiceWithInvoicePrefix()
    {
        $sut = $this->createService(null, null, ['cpms' => ['invoice_prefix' => 'PREFIX']]);

        $this->assertSame('PREFIX', $sut->getInvoicePrefix());
    }

    public function testGetSetInvoicePrefix()
    {
        $this->assertSame(null, $this->sut->getInvoicePrefix());
        $this->sut->setInvoicePrefix('PREFIX');
        $this->assertSame('PREFIX', $this->sut->getInvoicePrefix());
    }

    public function testSetInvoicePrefixTooLong()
    {
        $this->setExpectedException(\RuntimeException::class, 'Invoice prefix needs to be less than 8 chars');
        $this->sut->setInvoicePrefix('TOO_LONGX');
    }

    public function testFormatAddressNull()
    {
        $sut = m::mock(\Dvsa\Olcs\Api\Service\CpmsV2HelperService::class)->makePartial();

        $this->assertNull($sut->formatAddress(null));
    }

    public function testValidateReceiverParamsWithException()
    {
        $sut = m::mock(\Dvsa\Olcs\Api\Service\CpmsV2HelperService::class)->makePartial();

        $this->setExpectedException(ValidationException::class);
        $sut->validateReceiverParams();
    }

    public function testValidateCustomerParamsWithException()
    {
        $sut = m::mock(\Dvsa\Olcs\Api\Service\CpmsV2HelperService::class)->makePartial();

        $this->setExpectedException(ValidationException::class);
        $sut->validateCustomerParams();
    }


    public function testGetReceiverReferenceMiscFee()
    {
        $mockFee = m::mock()
            ->shouldReceive('getLicence')
            ->andReturnNull()
            ->once()
            ->shouldReceive('getFeeType')
            ->andReturn(
                m::mock()
                ->shouldReceive('isMiscellaneous')
                ->andReturn(true)
                ->once()
                ->getMock()
            )
            ->shouldReceive('getOrganisation')
            ->andReturnNull()
            ->getMock();

        $sut = m::mock(\Dvsa\Olcs\Api\Service\CpmsV2HelperService::class)->makePartial();

        $this->assertNull($sut->getReceiverReference($mockFee));
    }

    public function testGetReceiverReferenceIrfoFee()
    {
        $mockFee = m::mock()
            ->shouldReceive('getLicence')
            ->andReturnNull()
            ->once()
            ->shouldReceive('getFeeType')
            ->andReturn(
                m::mock()
                    ->shouldReceive('isMiscellaneous')
                    ->andReturn(false)
                    ->once()
                    ->shouldReceive('getIrfoFeeType')
                    ->andReturn('FOO')
                    ->once()
                    ->getMock()
            )
            ->shouldReceive('getOrganisation')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getId')
                    ->andReturn(1)
                    ->once()
                    ->getMock()
            )
            ->getMock();

        $sut = m::mock(\Dvsa\Olcs\Api\Service\CpmsV2HelperService::class)->makePartial();

        $this->assertEquals('IR0000001', $sut->getReceiverReference($mockFee));
    }

    public function testGetReceiverReferenceBusFee()
    {
        $mockFee = m::mock()
            ->shouldReceive('getLicence')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getLicNo')
                    ->andReturn('OB1234567')
                    ->once()
                    ->getMock()
            )
            ->once()
            ->shouldReceive('getFeeType')
            ->andReturn(
                m::mock()
                    ->shouldReceive('isMiscellaneous')
                    ->andReturn(false)
                    ->once()
                    ->shouldReceive('getIrfoFeeType')
                    ->andReturnNull()
                    ->once()
                    ->shouldReceive('getFeeType')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getId')
                            ->andReturn(FeeTypeEntity::FEE_TYPE_BUSAPP)
                            ->once()
                            ->getMock()
                    )
                    ->getMock()
            )
            ->shouldReceive('getOrganisation')
            ->andReturnNull()
            ->getMock();

        $sut = m::mock(\Dvsa\Olcs\Api\Service\CpmsV2HelperService::class)->makePartial();

        $this->assertEquals('OB1234567B', $sut->getReceiverReference($mockFee));
    }

    public function testGetReceiverReferenceLicenceFee()
    {
        $mockFee = m::mock()
            ->shouldReceive('getLicence')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getLicNo')
                    ->andReturn('OB1234567')
                    ->once()
                    ->getMock()
            )
            ->once()
            ->shouldReceive('getFeeType')
            ->andReturn(
                m::mock()
                    ->shouldReceive('isMiscellaneous')
                    ->andReturn(false)
                    ->once()
                    ->shouldReceive('getIrfoFeeType')
                    ->andReturnNull()
                    ->once()
                    ->shouldReceive('getFeeType')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getId')
                            ->andReturn('FOO')
                            ->once()
                            ->getMock()
                    )
                    ->getMock()
            )
            ->shouldReceive('getOrganisation')
            ->andReturnNull()
            ->getMock();

        $sut = m::mock(\Dvsa\Olcs\Api\Service\CpmsV2HelperService::class)->makePartial();

        $this->assertEquals('OB1234567', $sut->getReceiverReference($mockFee));
    }

    public function testGetReceiverReferenceOrgFee()
    {
        $mockFee = m::mock()
            ->shouldReceive('getLicence')
            ->andReturnNull()
            ->once()
            ->shouldReceive('getFeeType')
            ->andReturn(
                m::mock()
                    ->shouldReceive('isMiscellaneous')
                    ->andReturn(false)
                    ->once()
                    ->shouldReceive('getIrfoFeeType')
                    ->andReturnNull()
                    ->once()
                    ->shouldReceive('getFeeType')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getId')
                            ->andReturn('FOO')
                            ->once()
                            ->getMock()
                    )
                    ->getMock()
            )
            ->shouldReceive('getOrganisation')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getId')
                    ->andReturn(99)
                    ->once()
                    ->getMock()
            )
            ->getMock();

        $sut = m::mock(\Dvsa\Olcs\Api\Service\CpmsV2HelperService::class)->makePartial();

        $this->assertEquals(99, $sut->getReceiverReference($mockFee));
    }

    public function testGetReceiverReferenceNoFee()
    {
        $mockFee = m::mock()
            ->shouldReceive('getLicence')
            ->andReturnNull()
            ->once()
            ->shouldReceive('getFeeType')
            ->andReturn(
                m::mock()
                    ->shouldReceive('isMiscellaneous')
                    ->andReturn(false)
                    ->once()
                    ->shouldReceive('getIrfoFeeType')
                    ->andReturnNull()
                    ->once()
                    ->shouldReceive('getFeeType')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getId')
                            ->andReturn('FOO')
                            ->once()
                            ->getMock()
                    )
                    ->getMock()
            )
            ->shouldReceive('getOrganisation')
            ->andReturnNull()
            ->getMock();

        $sut = m::mock(\Dvsa\Olcs\Api\Service\CpmsV2HelperService::class)->makePartial();

        $this->assertNull($sut->getReceiverReference($mockFee));
    }

    public function testFormatAddressArray()
    {
        $sut = m::mock(\Dvsa\Olcs\Api\Service\CpmsV2HelperService::class)->makePartial();

        $expected = [
            'line_1' => 'line1',
            'line_2' => 'line2',
            'line_3' => 'line3',
            'line_4' => 'line4',
            'city' => 'city',
            'postcode' => ' ',
        ];
        $address = [
            'addressLine1' => 'line1',
            'addressLine2' => 'line2',
            'addressLine3' => 'line3',
            'addressLine4' => 'line4',
            'town' => 'city',
            'postcode' => null,
        ];

        $this->assertEquals($sut->formatAddress($address), $expected);
    }

    public function testFormatAddressObject()
    {
        $sut = m::mock(\Dvsa\Olcs\Api\Service\CpmsV2HelperService::class)->makePartial();

        $expected = [
            'line_1' => 'line1',
            'line_2' => 'line2',
            'line_3' => 'line3',
            'line_4' => 'line4',
            'city' => 'city',
            'postcode' => ' ',
        ];
        $address = new AddressEntity();
        $address->setAddressLine1('line1');
        $address->setAddressLine2('line2');
        $address->setAddressLine3('line3');
        $address->setAddressLine4('line4');
        $address->setTown('city');
        $address->setPostcode(null);

        $this->assertEquals($sut->formatAddress($address), $expected);
    }
}
