<?php

/**
 * CPMS Helper Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Service;

use Dvsa\Olcs\Api\Service\CpmsHelperService;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\OlcsTest\Api\MockLoggerTrait;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * CPMS Helper Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CpmsHelperServiceTest extends MockeryTestCase
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
        $this->sut = $this->createService($this->cpmsClient, $this->mockLogger());

        return parent::setUp();
    }

    private function createService($api, $logger)
    {
        $sm = m::mock('\Zend\ServiceManager\ServiceLocatorInterface');
        $sm
            ->shouldReceive('get')
            ->with('cpms\service\api')
            ->andReturn($api)
            ->shouldReceive('get')
            ->with('Logger')
            ->andReturn($logger);

        $sut = new CpmsHelperService();
        return $sut->createService($sm);
    }

    public function testInitiateCardRequest()
    {
        $customerReference = 99;
        $redirectUrl = 'http://olcs-selfserve/foo';

        $fees = [
            $this->getStubFee(1, 525.25, FeeEntity::ACCRUAL_RULE_IMMEDIATE),
            $this->getStubFee(2, 125.25, FeeEntity::ACCRUAL_RULE_LICENCE_START, '2014-12-25'),
        ];

        $params = [
            'customer_reference' => '99',
            'scope' => 'CARD',
            'disable_redirection' => true,
            'redirect_uri' => 'http://olcs-selfserve/foo',
            'payment_data' => [
                [
                    'amount' => '525.25',
                    'sales_reference' => '1',
                    'product_reference' => 'GVR_APPLICATION_FEE',
                    'payment_reference' => [
                        'rule_start_date' => '2015-06-10',
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

        $result = $this->sut->initiateCardRequest($customerReference, $redirectUrl, $fees);

        $this->assertSame($response, $result);
    }

    public function testInitiateCardRequestInvalidApiResponse()
    {
        $customerReference = 99;
        $redirectUrl = 'http://olcs-selfserve/foo';

        $fees = [];

        $params = [
            'customer_reference' => '99',
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

        $this->sut->initiateCardRequest($customerReference, $redirectUrl, $fees);
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
        $fee1 = $this->getStubFee(1, 1234.56);
        $fee2 = $this->getStubFee(2, 100.10);

        $params = [
            'customer_reference' => 'cust_ref',
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
            'code' => CpmsHelperService::RESPONSE_SUCCESS,
            'receipt_reference' => 'OLCS-1234-CASH',
        ];

        $this->cpmsClient
           ->shouldReceive('post')
            ->with('/api/payment/cash', 'CASH', $params)
            ->andReturn($response);

        $result = $this->sut->recordCashPayment(
            array($fee1, $fee2),
            'cust_ref',
            '1334.66',
            '2015-01-07',
            'Payer',
            '123456'
        );

        $this->assertTrue($result);
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
        $licenceStartDate = null
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

        if (!is_null($licenceStartDate)) {
            $licence = m::mock(LicenceEntity::class)->makePartial();
            $licence->setInForceDate($licenceStartDate);
            $fee->setLicence($licence);
        }

        $now = new \DateTime('2015-06-10 12:34:56');
        $fee->setCurrentDateTime($now);

        return $fee;
    }
}
