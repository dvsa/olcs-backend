<?php

/**
 * Complete Payment Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Payment;

use Dvsa\Olcs\Api\Domain\Command\Payment\ResolvePayment as ResolvePaymentCommand;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Payment\CompletePayment;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Repository\Payment as PaymentRepo;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\Payment as PaymentEntity;
use Dvsa\Olcs\Api\Service\CpmsHelperService as CpmsHelper;
use Dvsa\Olcs\Transfer\Command\Application\SubmitApplication as SubmitApplicationCommand;
use Dvsa\Olcs\Transfer\Command\Payment\CompletePayment as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Complete Payment Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CompletePaymentTest extends CommandHandlerTestCase
{
    protected $mockCpmsService;

    public function setUp()
    {
        $this->mockCpmsService = m::mock(CpmsHelper::class);
        $this->mockedSmServices = [
            'CpmsHelperService' => $this->mockCpmsService,
        ];

        $this->sut = new CompletePayment();
        $this->mockRepo('Payment', PaymentRepo::class);

        $this->refData = [
            PaymentEntity::STATUS_OUTSTANDING,
            PaymentEntity::STATUS_PAID,
        ];

        parent::setUp();
    }

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
        $payment->setGuid($guid);
        $payment
            ->shouldReceive('getStatus')
            ->twice()
            ->andReturn(
                $this->refData[PaymentEntity::STATUS_OUTSTANDING],
                $this->refData[PaymentEntity::STATUS_PAID]
            );

        $command = Cmd::create($data);

        // expectations
        $this->repoMap['Payment']
            ->shouldReceive('fetchByReference')
            ->once()
            ->with($guid)
            ->andReturn($payment);

        $this->mockCpmsService
            ->shouldReceive('handleResponse')
            ->once()
            ->with($guid, $cpmsData);

        $resolveResult = new Result();
        $resolveResult
            ->addId('payment', $paymentId)
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
                'payment' => $paymentId,
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
        $payment->setGuid($guid);
        $payment->setStatus($this->refData[PaymentEntity::STATUS_PAID]);

        $command = Cmd::create($data);

        // expectations
        $this->repoMap['Payment']
            ->shouldReceive('fetchByReference')
            ->once()
            ->with($guid)
            ->andReturn($payment);

        $this->setExpectedException(ValidationException::class);

        $this->sut->handleCommand($command);
    }
}
