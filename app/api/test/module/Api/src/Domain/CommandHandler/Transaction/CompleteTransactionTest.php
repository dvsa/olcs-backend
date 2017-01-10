<?php

/**
 * Complete Transaction Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Transaction;

use Dvsa\Olcs\Api\Domain\Command\Transaction\ResolvePayment as ResolvePaymentCommand;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Transaction\CompleteTransaction;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Repository\Transaction as PaymentRepo;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\Transaction as PaymentEntity;
use Dvsa\Olcs\Api\Service\CpmsHelperInterface as CpmsHelper;
use Dvsa\Olcs\Transfer\Command\Application\SubmitApplication as SubmitApplicationCommand;
use Dvsa\Olcs\Transfer\Command\Transaction\CompleteTransaction as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Complete Transaction Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CompleteTransactionTest extends CommandHandlerTestCase
{
    protected $mockCpmsService;

    public function setUp()
    {
        $this->mockCpmsService = m::mock(CpmsHelper::class);
        $this->mockedSmServices = [
            'CpmsHelperService' => $this->mockCpmsService,
            'Config' => [],
        ];

        $this->sut = new CompleteTransaction();
        $this->mockRepo('Transaction', PaymentRepo::class);

        $this->refData = [
            PaymentEntity::STATUS_OUTSTANDING,
            PaymentEntity::STATUS_PAID,
        ];

        parent::setUp();
    }

    /**
     * @group test123
     */
    public function testHandleCommand()
    {
        // set up data
        $paymentId = 69;
        $guid = 'OLCS-1234-ABCDE';
        $cpmsData = ['foo' => 'bar'];
        $applicationId = 99;

        $data = [
            'reference' => $guid,
            'paymentMethod' => FeeEntity::METHOD_CARD_ONLINE,
            'cpmsData' => $cpmsData,
            'submitApplicationId' => $applicationId,
        ];

        $payment = m::mock(PaymentEntity::class)->makePartial();
        $payment->setId($paymentId);
        $payment->setReference($guid);
        $payment
            ->shouldReceive('getStatus')
            ->twice()
            ->andReturn(
                $this->refData[PaymentEntity::STATUS_OUTSTANDING],
                $this->refData[PaymentEntity::STATUS_PAID]
            );

        $command = Cmd::create($data);

        // expectations
        $this->repoMap['Transaction']
            ->shouldReceive('fetchByReference')
            ->once()
            ->with($guid)
            ->andReturn($payment)
            ->shouldReceive('getFees')
            ->once()
            ->andReturn(['fee1', 'fee2']);

        $this->mockCpmsService
            ->shouldReceive('handleResponse')
            ->once()
            ->with($guid, $cpmsData, 'fee1');

        $resolveResult = new Result();
        $resolveResult
            ->addId('transaction', $paymentId)
            ->addMessage('payment updated');
        $this->expectedSideEffect(
            ResolvePaymentCommand::class,
            [
                'id' => $paymentId,
                'paymentMethod' => FeeEntity::METHOD_CARD_ONLINE,
            ],
            $resolveResult
        );

        $submitResult = new Result();
        $submitResult
            ->addId('application', $applicationId)
            ->addMessage('application submitted');
        $this->expectedSideEffect(
            SubmitApplicationCommand::class,
            [
                'id' => $applicationId,
                'version' => null,
            ],
            $submitResult
        );

        // assertions
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'transaction' => $paymentId,
                'application' => $applicationId,
            ],
            'messages' => [
                'payment updated',
                'application submitted',
                'CPMS record updated',
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandInvalidPaymentStatus()
    {
        // set up data
        $paymentId = 69;
        $guid = 'OLCS-1234-ABCDE';
        $cpmsData = ['foo' => 'bar'];

        $data = [
            'reference' => $guid,
            'paymentMethod' => FeeEntity::METHOD_CARD_ONLINE,
            'cpmsData' => $cpmsData,
        ];

        $payment = m::mock(PaymentEntity::class)->makePartial();
        $payment->setId($paymentId);
        $payment->setReference($guid);
        $payment->setStatus($this->refData[PaymentEntity::STATUS_PAID]);

        $command = Cmd::create($data);

        // expectations
        $this->repoMap['Transaction']
            ->shouldReceive('fetchByReference')
            ->once()
            ->with($guid)
            ->andReturn($payment);

        $this->setExpectedException(ValidationException::class);

        $this->sut->handleCommand($command);
    }
}
