<?php


/**
 * Resolve Payment Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Transaction;


use Dvsa\Olcs\Api\Domain\Command\Fee\PayFee as PayFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Transaction\ResolvePayment as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Transaction\ResolvePayment;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Repository\AbstractRepository as Repo;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeTransaction as FeePaymentEntity;
use Dvsa\Olcs\Api\Entity\Fee\Transaction as PaymentEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\CpmsHelperInterface as CpmsHelper;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * Resolve Payment Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ResolvePaymentTest extends CommandHandlerTestCase
{
    protected $mockCpmsService;

    public function setUp()
    {
        $this->mockCpmsService = m::mock(CpmsHelper::class);
        $this->mockedSmServices = [
            'CpmsHelperService' => $this->mockCpmsService,
            AuthorizationService::class => m::mock(AuthorizationService::class)->makePartial(),
            'Config' => [],
        ];

        $this->sut = new ResolvePayment();
        $this->mockRepo('Transaction', Repo::class);
        $this->mockRepo('Fee', Repo::class);

        $this->refData = [
            PaymentEntity::STATUS_OUTSTANDING,
            PaymentEntity::STATUS_PAID => m::mock(RefData::class)
                ->shouldReceive('getDescription')
                ->andReturn('PAYMENT PAID')
                ->getMock(),
            PaymentEntity::STATUS_FAILED => m::mock(RefData::class)
                ->shouldReceive('getDescription')
                ->andReturn('PAYMENT FAILED')
                ->getMock(),
            PaymentEntity::STATUS_CANCELLED => m::mock(RefData::class)
                ->shouldReceive('getDescription')
                ->andReturn('PAYMENT CANCELLED')
                ->getMock(),
            FeeEntity::STATUS_PAID => m::mock(RefData::class)
                ->shouldReceive('getDescription')
                ->andReturn('FEE PAID')
                ->getMock(),
            FeeEntity::METHOD_CARD_ONLINE => m::mock(RefData::class)
                ->shouldReceive('getDescription')
                ->andReturn('CARD')
                ->getMock(),
            PaymentEntity::TYPE_PAYMENT,
        ];

        $this->references = [
            FeePaymentEntity::class => [
                11 => m::mock(FeePaymentEntity::class)->makePartial(),
            ],
            FeeEntity::class => [
                22 => m::mock(FeeEntity::class)->makePartial(),
            ],
        ];

        $this->references[FeePaymentEntity::class][11]
            ->setFee($this->references[FeeEntity::class][22]);

        /** @var UserEntity $mockUser */
        $mockUser = m::mock(UserEntity::class)
            ->shouldReceive('getLoginId')
            ->andReturn('bob')
            ->getMock();

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);

        parent::setUp();
    }


    public function testHandleCommandSuccess()
    {
        // set up data
        $paymentId = 69;
        $guid = 'OLCS-1234-ABCDE';
        $amount = '1234.56';

        $data = [
            'id' => $paymentId,
            'paymentMethod' => FeeEntity::METHOD_CARD_ONLINE,
        ];

        $fee = $this->references[FeeEntity::class][22];
        $fee->setAmount($amount);
        $fee->shouldReceive('getOutstandingAmount')
            ->andReturn('0.00');

        $payment = m::mock(PaymentEntity::class)->makePartial();
        $payment->setId($paymentId);
        $payment->setReference($guid);
        $payment->setFeeTransactions($this->references[FeePaymentEntity::class]);
        $payment->setStatus($this->refData[PaymentEntity::STATUS_PAID]);
        $payment->setType($this->refData[PaymentEntity::TYPE_PAYMENT]);

        $command = Cmd::create($data);

        // expectations
        $this->repoMap['Transaction']
            ->shouldReceive('fetchUsingId')
            ->once()
            ->with($command)
            ->andReturn($payment);

        $this->mockCpmsService
            ->shouldReceive('getPaymentStatus')
            ->once()
            ->with($guid)
            ->andReturn(CpmsHelper::PAYMENT_SUCCESS);

        $this->repoMap['Transaction']
            ->shouldReceive('save')
            ->once()
            ->with($payment);

        $this->repoMap['Fee']
            ->shouldReceive('save')
            ->once()
            ->with($fee);

        $updateData = ['id' => 22];
        $result2 = new Result();
        $this->expectedSideEffect(PayFeeCmd::class, $updateData, $result2);

        $now = new DateTime();

        // assertions
        $result = $this->sut->handleCommand($command);

        $this->assertEquals('FEE PAID', $fee->getFeeStatus()->getDescription());
        $this->assertEquals($guid, $payment->getReference());
        $this->assertEquals($now->format('Y-m-d'), $payment->getCompletedDate()->format('Y-m-d'));

        $expected = [
            'id' => [
                'transaction' => 69,
            ],
            'messages' => [
                'Fee ID 22 updated as paid',
                'Transaction 69 resolved as PAYMENT PAID',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * @param string $cpmsStatus
     * @param string $expectedPaymentStatus
     * @param string $expectedMessage
     *
     * @dataProvider failureStatusProvider
     */
    public function testHandleCommandFailures($cpmsStatus, $expectedPaymentStatus, $expectedMessage)
    {
        // set up data
        $paymentId = 69;
        $guid = 'OLCS-1234-ABCDE';
        $amount = '1234.56';

        $data = [
            'id' => $paymentId,
            'paymentMethod' => FeeEntity::METHOD_CARD_ONLINE,
        ];

        $fee = $this->references[FeeEntity::class][22];
        $fee->setAmount($amount);

        $payment = m::mock(PaymentEntity::class)->makePartial();
        $payment->setId($paymentId);
        $payment->setReference($guid);
        $payment->setFeeTransactions($this->references[FeePaymentEntity::class]);
        $payment->setStatus($this->refData[PaymentEntity::STATUS_OUTSTANDING]);
        $payment->setType($this->refData[PaymentEntity::TYPE_PAYMENT]);

        $command = Cmd::create($data);

        // expectations
        $this->repoMap['Transaction']
            ->shouldReceive('fetchUsingId')
            ->once()
            ->with($command)
            ->andReturn($payment);

        $this->mockCpmsService
            ->shouldReceive('getPaymentStatus')
            ->once()
            ->with($guid)
            ->andReturn($cpmsStatus);

        $this->repoMap['Transaction']
            ->shouldReceive('save')
            ->once()
            ->with($payment);

        $this->repoMap['Fee']
            ->shouldReceive('save')
            ->never();

        // assertions
        $result = $this->sut->handleCommand($command);

        $this->assertEquals($expectedPaymentStatus, $payment->getStatus()->getId());

        $expected = [
            'id' => [
                'transaction' => 69,
            ],
            'messages' => [
                $expectedMessage,
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function failureStatusProvider()
    {
        return [
            [
                CpmsHelper::PAYMENT_FAILURE,
                PaymentEntity::STATUS_FAILED,
                'Transaction 69 resolved as PAYMENT FAILED',
            ],
            [
                CpmsHelper::PAYMENT_CANCELLATION,
                PaymentEntity::STATUS_CANCELLED,
                'Transaction 69 resolved as PAYMENT CANCELLED',
            ],
            [
                CpmsHelper::PAYMENT_IN_PROGRESS,
                PaymentEntity::STATUS_FAILED,
                'Transaction 69 resolved as PAYMENT FAILED',
            ],
            [
                CpmsHelper::PAYMENT_GATEWAY_REDIRECT_URL_RECEIVED,
                PaymentEntity::STATUS_FAILED,
                'Transaction 69 resolved as PAYMENT FAILED',
            ],
        ];
    }

    public function testHandleCommandInvalidCpmsStatus()
    {
        $cpmsStatus = 'INVALID STATUS';

        // set up data
        $paymentId = 69;
        $guid = 'OLCS-1234-ABCDE';

        $data = [
            'id' => $paymentId,
            'paymentMethod' => FeeEntity::METHOD_CARD_ONLINE,
        ];

        $payment = m::mock(PaymentEntity::class)->makePartial();
        $payment->setId($paymentId);
        $payment->setReference($guid);
        $payment->setType($this->refData[PaymentEntity::TYPE_PAYMENT]);

        $command = Cmd::create($data);

        // expectations
        $this->repoMap['Transaction']
            ->shouldReceive('fetchUsingId')
            ->once()
            ->with($command)
            ->andReturn($payment);

        $this->mockCpmsService
            ->shouldReceive('getPaymentStatus')
            ->once()
            ->with($guid)
            ->andReturn($cpmsStatus);

        $this->setExpectedException(ValidationException::class);

        $this->sut->handleCommand($command);
    }
}
