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
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication;
use Dvsa\Olcs\Api\Domain\Repository\Transaction as PaymentRepo;
use Dvsa\Olcs\Api\Domain\Repository\EcmtPermitApplication as EcmtPermitApplicationRepo;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\Transaction as PaymentEntity;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as EcmtPermitApplicationEntity;
use Dvsa\Olcs\Api\Service\CpmsHelperInterface as CpmsHelper;
use Dvsa\Olcs\Transfer\Command\Application\SubmitApplication as SubmitApplicationCommand;
use Dvsa\Olcs\Transfer\Command\Permits\EcmtSubmitApplication as SubmitEcmtPermitApplicationCmd;
use Dvsa\Olcs\Transfer\Command\Transaction\CompleteTransaction as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Complete Transaction Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
        $this->mockRepo('EcmtPermitApplication', EcmtPermitApplicationRepo::class);

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

        $fee1 = m::mock(Fee::class);
        $fee2 = m::mock(Fee::class);

        $fee1->shouldReceive('getEcmtPermitApplication')
            ->andReturn([]);
        $fee2->shouldReceive('getEcmtPermitApplication')
            ->andReturn([]);


        $payment = m::mock(PaymentEntity::class)->makePartial();
        $payment->setId($paymentId);
        $payment->setReference($guid);
        $payment
            ->shouldReceive('getStatus')
            ->times(3)
            ->andReturn(
                $this->refData[PaymentEntity::STATUS_OUTSTANDING],
                $this->refData[PaymentEntity::STATUS_PAID]
            )
            ->shouldReceive('getFees')
            ->once()
            ->andReturn([$fee1, $fee2]);


        $command = Cmd::create($data);

        // expectations
        $this->repoMap['Transaction']
            ->shouldReceive('fetchByReference')
            ->once()
            ->with($guid)
            ->andReturn($payment);

        $this->mockCpmsService
            ->shouldReceive('handleResponse')
            ->once()
            ->with($guid, $cpmsData, $fee1);

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

    public function testHandleCommandEcmtPermitApplication()
    {
        // set up data
        $paymentId = 69;
        $guid = 'OLCS-1234-ABCDE';
        $cpmsData = ['foo' => 'bar'];
        $ecmtPermitApplicationId = 2;

        $data = [
            'reference' => $guid,
            'paymentMethod' => FeeEntity::METHOD_CARD_ONLINE,
            'cpmsData' => $cpmsData,
        ];

        $fee1 = m::mock(Fee::class);
        $fee2 = m::mock(Fee::class);

        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);

        $fee1->shouldReceive('getEcmtPermitApplication')
            ->andReturn($ecmtPermitApplication);
        $fee2->shouldReceive('getEcmtPermitApplication')
            ->andReturn([]);

        $ecmtPermitApplication->shouldReceive('canBeSubmitted')
            ->andReturn(true);

        $ecmtPermitApplication->shouldReceive('isAwaitingFee')
            ->andReturn(false);

        $ecmtPermitApplication->shouldReceive('getId')
            ->andReturn($ecmtPermitApplicationId);


        $payment = m::mock(PaymentEntity::class)->makePartial();
        $payment->setId($paymentId);
        $payment->setReference($guid);
        $payment
            ->shouldReceive('getStatus')
            ->andReturn(
                $this->refData[PaymentEntity::STATUS_OUTSTANDING],
                $this->refData[PaymentEntity::STATUS_PAID]
            )
            ->shouldReceive('getFees')
            ->once()
            ->andReturn([$fee1, $fee2]);

        $command = Cmd::create($data);

        // expectations
        $this->repoMap['Transaction']
            ->shouldReceive('fetchByReference')
            ->once()
            ->with($guid)
            ->andReturn($payment);

        $this->mockCpmsService
            ->shouldReceive('handleResponse')
            ->once()
            ->with($guid, $cpmsData, $fee1);

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
            ->addId('application', $ecmtPermitApplicationId)
            ->addMessage('application submitted');
        $this->expectedSideEffect(
            SubmitEcmtPermitApplicationCmd::class,
            [
                'id' => $ecmtPermitApplicationId
            ],
            $submitResult
        );

        // assertions
        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'transaction' => $paymentId,
                'application' => $ecmtPermitApplicationId,
            ],
            'messages' => [
                'application submitted',
                'payment updated',
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
