<?php

/**
 * CPMS Version 2 Helper Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Service;

use Dvsa\Olcs\Api\Service\CpmsV2HelperService as Sut;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address as AddressEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\OlcsTest\Api\MockLoggerTrait;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

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
                    // 'product_reference' => 'fee type description',
                    'product_reference' => 'GVR_APPLICATION_FEE', // @todo
                    'receiver_reference' => '99',
                    'receiver_name' => 'some organisation',
                    'receiver_address' => [
                        'line_1' => 'Foo',
                        'line_2' => NULL,
                        'line_3' => NULL,
                        'line_4' => NULL,
                        'city' => 'Bar',
                        'postcode' => 'LS9 6NF',
                    ],
                    'rule_start_date' => $now,
                    'deferment_period' => '1',
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
                    // 'product_reference' => 'fee type description',
                    'product_reference' => 'GVR_APPLICATION_FEE', // @todo
                    'receiver_reference' => '99',
                    'receiver_name' => 'some organisation',
                    'receiver_address' => [
                        'line_1' => 'Foo',
                        'line_2' => NULL,
                        'line_3' => NULL,
                        'line_4' => NULL,
                        'city' => 'Bar',
                        'postcode' => 'LS9 6NF',
                    ],
                    'rule_start_date' => '2014-12-25',
                    'deferment_period' => '60',
                ]
            ],
            'cost_centre' => '12345,67890',
            'total_amount' => '650.50',
            'customer_name' => 'some organisation',
            'customer_manager_name' => 'some organisation',
            'customer_address' =>[
                'line_1' => 'Foo',
                'line_2' => NULL,
                'line_3' => NULL,
                'line_4' => NULL,
                'city' => 'Bar',
                'postcode' => 'LS9 6NF',
            ],
            'redirect_uri' => $redirectUrl,
            'disable_redirection' => true,
            'scope' => 'CARD',
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
        $redirectUrl = 'http://olcs-selfserve/foo';

        $fees = [];

        $response = [];
        $this->setExpectedException(\Exception::class, 'Invalid payment response');

        $this->cpmsClient
            ->shouldReceive('post')
            ->with('/api/payment/card', 'CARD', m::any())
            ->andReturn($response);

        $this->sut->initiateCardRequest($redirectUrl, $fees);
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
        $invoicedDate = null
    ) {
        $status = new RefData();
        $rule = new RefData();
        if ($accrualRule) {
            $rule->setId($accrualRule);
        }
        $feeType = new FeeTypeEntity();
        $feeType
            ->setAccrualRule($rule)
            ->setDescription('fee type description');

        $fee = new FeeEntity($feeType, $amount, $status);
        $fee
            ->setId($id)
            ->setInvoiceLineNo(1)
            ->setInvoicedDate($invoicedDate);

        $organisation = new OrganisationEntity();
        $organisation
            ->setId($organisationId)
            ->setName('some organisation');

        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setOrganisation($organisation);

        if (!is_null($licenceStartDate)) {
            $licence->setInForceDate($licenceStartDate);
        }

        $fee->setLicence($licence);

        $address = new AddressEntity();
        $address->updateAddress(
            'Foo',
            null,
            null,
            null,
            'Bar',
            'LS9 6NF',
            null
        );
        $licence->setCorrespondenceCd(
            m::mock()
                ->shouldReceive('getAddress')
                ->andReturn($address)
                ->getMock()
        );

        return $fee;
    }
}
