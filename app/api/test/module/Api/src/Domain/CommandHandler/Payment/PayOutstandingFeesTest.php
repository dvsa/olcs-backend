<?php

/**
 * Pay Outstanding Fees Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Payment;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Payment\PayOutstandingFees;
use Dvsa\Olcs\Api\Domain\Command\Payment\ResolvePayment as ResolvePaymentCommand;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;
use Dvsa\Olcs\Api\Domain\Repository\Payment as PaymentRepo;
use Dvsa\Olcs\Api\Entity\Fee\Payment as PaymentEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\CpmsHelperService as CpmsHelper;
use Dvsa\Olcs\Transfer\Command\Payment\PayOutstandingFees as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Pay Outstanding Fees Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class PayOutstandingFeesTest extends CommandHandlerTestCase
{
    protected $mockCpmsService;

    public function setUp()
    {
        $this->mockCpmsService = m::mock(CpmsHelper::class);
        $this->mockedSmServices = [
            'CpmsHelperService' => $this->mockCpmsService,
        ];

        $this->sut = new PayOutstandingFees();
        $this->mockRepo('Fee', FeeRepo::class);
        $this->mockRepo('Payment', PaymentRepo::class);

        // $this->references = [
        //     FeePaymentEntity::class => [
        //         11 => m::mock(FeePaymentEntity::class)->makePartial(),
        //     ],
        //     FeeEntity::class => [
        //         22 => m::mock(FeeEntity::class)->makePartial(),
        //     ],
        // ];
        $this->refData = [
            PaymentEntity::STATUS_OUTSTANDING,
        ];

        parent::setUp();
    }

    public function testHandleCommand()
    {
        // set up data
        $organisationId = 69;
        $feeIds = [99, 100, 101];
        $cpmsRedirectUrl = 'https://olcs-selfserve/foo';

        $paymentId = 999; // payment to be created

        $fees = [
            $this->getStubFee(99, 99.99),
            $this->getStubFee(101, 99.99),
        ];

        $data = [
            'feeIds' => $feeIds,
            'organisationId' => $organisationId,
            'cpmsRedirectUrl' => $cpmsRedirectUrl,
            'paymentMethod' => FeeEntity::METHOD_CARD_ONLINE,
        ];

        $command = Cmd::create($data);

        // expectations
        $this->repoMap['Fee']
            ->shouldReceive('fetchOutstandingFeesByOrganisationId')
            ->once()
            ->with($organisationId)
            ->andReturn($fees);

        $this->mockCpmsService
            ->shouldReceive('initiateCardRequest')
            ->once()
            ->with($organisationId, $cpmsRedirectUrl, $fees);

       /** @var PaymentEntity $savedPayment */
        $savedPayment = null;
        $this->repoMap['Payment']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(PaymentEntity::class))
            ->andReturnUsing(
                function (PaymentEntity $payment) use (&$savedPayment, $paymentId) {
                    $payment->setId($paymentId);
                    $savedPayment = $payment;
                }
            );

        // assertions
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'payment' => $paymentId,
            ],
            'messages' => [
                'Payment record created',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals(PaymentEntity::STATUS_OUTSTANDING, $savedPayment->getStatus()->getId());
    }

    /**
     * Helper function to generate a stub fee entity
     *
     * @param int $id
     * @param string $amount
     * @return FeeEntity
     */
    private function getStubFee($id, $amount)
    {
        $status = new RefData();
        $feeType = new FeeTypeEntity();

        $fee = new FeeEntity($feeType, $amount, $status);
        $fee->setId($id);

        return $fee;
    }
}
