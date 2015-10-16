<?php

/**
 * CPMS Helper Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Service;

use Dvsa\Olcs\Api\Service\CpmsV1HelperService as Sut;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use OlcsTest\Bootstrap;

/**
 * CPMS Helper Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CpmsV1HelperServiceTest extends MockeryTestCase
{
    /**
     * @var \Mockery\MockInterface (CpmsClient\Service\ApiService)
     */
    protected $cpmsClient;

    /**
     * @var Sut
     */
    protected $sut;

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

        // Create service with mocked dependencies
        $this->sut = $this->createService($this->cpmsClient);

        return parent::setUp();
    }

    private function createService($api)
    {
        $sm = m::mock('\Zend\ServiceManager\ServiceLocatorInterface');
        $sm
            ->shouldReceive('get')
            ->with('cpms\service\api')
            ->andReturn($api);

        $sut = new Sut();
        return $sut->createService($sm);
    }

    public function testInitiateCardRequest()
    {
        $organisationId = 99;
        $redirectUrl = 'http://olcs-selfserve/foo';

        $fees = [
            $this->getStubFee(1, 525.25, FeeEntity::ACCRUAL_RULE_IMMEDIATE, null, $organisationId),
            $this->getStubFee(2, 125.25, FeeEntity::ACCRUAL_RULE_LICENCE_START, '2014-12-25', $organisationId),
        ];

        $now = (new DateTime())->format('Y-m-d');

        $params = [
            'customer_reference' => $organisationId,
            'scope' => 'CARD',
            'disable_redirection' => true,
            'redirect_uri' => 'http://olcs-selfserve/foo',
            'payment_data' => [
                [
                    'amount' => '525.25',
                    'sales_reference' => '1',
                    'product_reference' => 'GVR_APPLICATION_FEE',
                    'payment_reference' => [
                        'rule_start_date' => $now,
                    ],
                ],
                [
                    'amount' => '125.25',
                    'sales_reference' => '2',
                    'product_reference' => 'GVR_APPLICATION_FEE',
                    'payment_reference' => [
                        'rule_start_date' => '2014-12-25',
                    ],
                ]
            ],
            'cost_centre' => '12345,67890',
            'total_amount' => '650.50',
        ];

        // assertions
        $response = ['receipt_reference' => 'guid_123'];

        $this->cpmsClient
            ->shouldReceive('post')
            ->with('/api/payment/card', 'CARD', $params)
            ->once()
            ->andReturn($response);

        $result = $this->sut->initiateCardRequest($redirectUrl, $fees);

        $this->assertSame($response, $result);
    }

    public function testInitiateCardRequestInvalidApiResponse()
    {
        $redirectUrl = 'http://olcs-selfserve/foo';

        $fees = [];

        $params = [
            'customer_reference' => 'Miscellaneous',
            'scope' => 'CARD',
            'disable_redirection' => true,
            'redirect_uri' => 'http://olcs-selfserve/foo',
            'payment_data' => [],
            'cost_centre' => '12345,67890',
            'total_amount' => '0.00',
        ];

        $response = [];
        $this->setExpectedException(\Exception::class, 'Invalid payment response');

        $this->cpmsClient
            ->shouldReceive('post')
            ->with('/api/payment/card', 'CARD', $params)
            ->andReturn($response);

        $this->sut->initiateCardRequest($redirectUrl, $fees);
    }

    public function testGetPaymentStatus()
    {
        // set up data
        $ref = 'OLCS-1234-ABCD';
        $expectedCode = 999;
        $expectedParams = [
            'required_fields' => [
                'payment' => [
                    'payment_status'
                ]
            ]
        ];

        // expectations
        $response = ['payment_status' => ['code' => $expectedCode]];
        $this->cpmsClient
            ->shouldReceive('get')
            ->with(
                '/api/payment/'.$ref,
                'QUERY_TXN',
                $expectedParams
            )
            ->once()
            ->andReturn($response);

        // assertions
        $result = $this->sut->getPaymentStatus($ref);
        $this->assertEquals($expectedCode, $result);
    }

    public function testHandleResponse()
    {
        $ref = 'OLCS-1234-ABCD';
        $data = ['foo' => 'bar'];

        $response = 'RESPONSE';
        $this->cpmsClient
            ->shouldReceive('put')
            ->with(
                '/api/gateway/'.$ref.'/complete',
                'CARD',
                $data
            )
            ->once()
            ->andReturn($response);

        $result = $this->sut->handleResponse($ref, $data);

        $this->assertSame($result, $response);
    }

    public function testRecordCashPayment()
    {
        $organisationId = 99;

        $fee1 = $this->getStubFee(1, 1234.56, null, null, $organisationId);
        $fee2 = $this->getStubFee(2, 100.10, null, null, $organisationId);

        $params = [
            'customer_reference' => $organisationId,
            'scope' => 'CASH',
            'total_amount' => '1334.66',
            'payment_data' => [
                [
                    'amount' => '1234.56',
                    'sales_reference' => '1',
                    'product_reference' => 'GVR_APPLICATION_FEE',
                    'payer_details' => 'Payer',
                    'payment_reference' => [
                        'rule_start_date' => null,
                        'receipt_date' => '2015-01-07',
                        'slip_number' => '123456',
                    ],
                ],
                [
                    'amount' => '100.10',
                    'sales_reference' => '2',
                    'product_reference' => 'GVR_APPLICATION_FEE',
                    'payer_details' => 'Payer',
                    'payment_reference' => [
                        'rule_start_date' => null,
                        'receipt_date' => '2015-01-07',
                        'slip_number' => '123456',
                    ],
                ]
            ],
            'cost_centre' => '12345,67890',
        ];

        $response = [
            'code' => Sut::RESPONSE_SUCCESS,
            'receipt_reference' => 'OLCS-1234-CASH',
        ];

        $this->cpmsClient
           ->shouldReceive('post')
            ->with('/api/payment/cash', 'CASH', $params)
            ->andReturn($response);

        $result = $this->sut->recordCashPayment(
            array($fee1, $fee2),
            '1334.66',
            '2015-01-07',
            'Payer',
            '123456'
        );

        $this->assertEquals($response, $result);
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Service\CpmsResponseException
     */
    public function testRecordCashPaymentFailureThrowsException()
    {
        $response = [
            'code' => 'xxx',
        ];

        $this->cpmsClient
           ->shouldReceive('post')
            ->with('/api/payment/cash', 'CASH', m::any())
            ->andReturn($response);

        $this->sut->recordCashPayment(
            array(),
            '1334.66',
            '2015-01-07',
            'Payer',
            '123456'
        );
    }

    public function testRecordChequePayment()
    {
        $organisationId = 99;

        $fee1 = $this->getStubFee(1, 1234.56, null, null, $organisationId);
        $fee2 = $this->getStubFee(2, 100.10, null, null, $organisationId);

        $params = [
            'customer_reference' => $organisationId,
            'scope' => 'CHEQUE',
            'total_amount' => '1334.66',
            'payment_data' => [
                [
                    'amount' => '1234.56',
                    'sales_reference' => '1',
                    'product_reference' => 'GVR_APPLICATION_FEE',
                    'payer_details' => 'Payer',
                    'payment_reference' => [
                        'rule_start_date' => null,
                        'receipt_date' => '2015-01-07',
                        'slip_number' => '123456',
                        'cheque_number' => '234567',
                        'cheque_date' => '2015-03-01',
                    ],
                ],
                [
                    'amount' => '100.10',
                    'sales_reference' => '2',
                    'product_reference' => 'GVR_APPLICATION_FEE',
                    'payer_details' => 'Payer',
                    'payment_reference' => [
                        'rule_start_date' => null,
                        'receipt_date' => '2015-01-07',
                        'slip_number' => '123456',
                        'cheque_number' => '234567',
                        'cheque_date' => '2015-03-01',
                    ],
                ]
            ],
            'cost_centre' => '12345,67890',
        ];

        $response = [
            'code' => Sut::RESPONSE_SUCCESS,
            'receipt_reference' => 'OLCS-1234-CHEQUE',
        ];

        $this->cpmsClient
           ->shouldReceive('post')
            ->with('/api/payment/cheque', 'CHEQUE', $params)
            ->andReturn($response);

        $result = $this->sut->recordChequePayment(
            array($fee1, $fee2),
            '1334.66',
            '2015-01-07',
            'Payer',
            '123456',
            '234567',
            '2015-03-01'
        );

        $this->assertEquals($response, $result);
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Service\CpmsResponseException
     */
    public function testRecordChequePaymentFailureThrowsException()
    {
        $response = [
            'code' => 'xxx',
        ];

        $this->cpmsClient
           ->shouldReceive('post')
            ->with('/api/payment/cheque', 'CHEQUE', m::any())
            ->andReturn($response);

        $this->sut->recordChequePayment(
            array(),
            '1334.66',
            '2015-01-07',
            'Payer',
            '123456',
            '234567',
            '2015-03-01'
        );
    }

    public function testRecordPostalOrderPayment()
    {
        $organisationId = 99;

        $fee1 = $this->getStubFee(1, 1234.56, null, null, $organisationId);
        $fee2 = $this->getStubFee(2, 100.10, null, null, $organisationId);

        $params = [
            'customer_reference' => $organisationId,
            'scope' => 'POSTAL_ORDER',
            'total_amount' => '1334.66',
            'payment_data' => [
                [
                    'amount' => '1234.56',
                    'sales_reference' => '1',
                    'product_reference' => 'GVR_APPLICATION_FEE',
                    'payer_details' => 'Payer',
                    'payment_reference' => [
                        'rule_start_date' => null,
                        'receipt_date' => '2015-01-07',
                        'slip_number' => '123456',
                        'postal_order_number' => ['234567'], // array expected according to api docs
                    ],
                ],
                [
                    'amount' => '100.10',
                    'sales_reference' => '2',
                    'product_reference' => 'GVR_APPLICATION_FEE',
                    'payer_details' => 'Payer',
                    'payment_reference' => [
                        'rule_start_date' => null,
                        'receipt_date' => '2015-01-07',
                        'slip_number' => '123456',
                        'postal_order_number' => ['234567'],
                    ],
                ]
            ],
            'cost_centre' => '12345,67890',
        ];

        $response = [
            'code' => Sut::RESPONSE_SUCCESS,
            'receipt_reference' => 'OLCS-1234-PO',
        ];

        $this->cpmsClient
           ->shouldReceive('post')
            ->with('/api/payment/postal-order', 'POSTAL_ORDER', $params)
            ->andReturn($response);

        $result = $this->sut->recordPostalOrderPayment(
            array($fee1, $fee2),
            '1334.66',
            '2015-01-07',
            'Payer',
            '123456',
            '234567'
        );

        $this->assertEquals($response, $result);
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Service\CpmsResponseException
     */
    public function testRecordPostalOrderPaymentFailureThrowsException()
    {
        $response = [
            'code' => 'xxx',
        ];

        $this->cpmsClient
           ->shouldReceive('post')
            ->with('/api/payment/postal-order', 'POSTAL_ORDER', m::any())
            ->andReturn($response);

        $this->sut->recordPostalOrderPayment(
            array(),
            '1334.66',
            '2015-01-07',
            'Payer',
            '123456',
            '234567'
        );
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
        $organisationId = null
    ) {
        $status = new RefData();
        $rule = new RefData();
        if ($accrualRule) {
            $rule->setId($accrualRule);
        }
        $feeType = new FeeTypeEntity();
        $feeType->setAccrualRule($rule);

        $fee = new FeeEntity($feeType, $amount, $status);
        $fee->setId($id);

        $organisation = new OrganisationEntity();
        $organisation->setId($organisationId);

        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setOrganisation($organisation);

        if (!is_null($licenceStartDate)) {
            $licence->setInForceDate($licenceStartDate);
        }

        $fee->setLicence($licence);

        return $fee;
    }
}
