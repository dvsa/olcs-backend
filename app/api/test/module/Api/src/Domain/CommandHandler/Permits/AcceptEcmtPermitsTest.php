<?php

/**
 * Accept ECMT Permits Application Test
 *
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\AcceptEcmtPermits;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Transfer\Command\Permits\AcceptEcmtPermits as Cmd;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Class AcceptEcmtPermitsTest
 */
class AcceptEcmtPermitsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new AcceptEcmtPermits();
        $this->mockRepo('EcmtPermitApplication', Repository\EcmtPermitApplication::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            EcmtPermitApplication::STATUS_ISSUING
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $ecmtPermitApplicationId = 7;

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($ecmtPermitApplicationId);

        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);

        $fee1 = m::mock(Fee::class);
        $fees = [$fee1];

        $ecmtPermitApplication->shouldReceive('getFees')
            ->once()
            ->andReturn($fees);


        $fee1->shouldReceive('getFeeStatus->getId')
            ->andReturn(Fee::STATUS_PAID);

        $ecmtPermitApplication->shouldReceive('isAwaitingFee')
            ->once()
            ->andReturn(false);


        $ecmtPermitApplication->shouldReceive('proceedToIssuing')
            ->with($this->refData[EcmtPermitApplication::STATUS_ISSUING])
            ->once()
            ->ordered()
            ->globally();

        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->with($ecmtPermitApplicationId)
            ->andReturn($ecmtPermitApplication);
        $this->repoMap['EcmtPermitApplication']->shouldReceive('save')
            ->with($ecmtPermitApplication)
            ->once()
            ->ordered()
            ->globally();

        $this->expectedQueueSideEffect($ecmtPermitApplicationId, Queue::TYPE_PERMITS_ALLOCATE, []);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            $ecmtPermitApplicationId,
            $result->getId('ecmtPermitApplication')
        );

        $this->assertEquals(
            ['Queuing issue of application permits'],
            $result->getMessages()
        );
    }

    public function testHandleCommandUnableToIssue()
    {
        $ecmtPermitApplicationId = 7;

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($ecmtPermitApplicationId);

        $ecmtPermitApplication = m::mock(EcmtPermitApplication::class);
        $ecmtPermitApplication->shouldReceive('proceedToIssuing')
            ->with($this->refData[EcmtPermitApplication::STATUS_ISSUING])
            ->once()
            ->andThrow(ForbiddenException::class);

        $fee1 = m::mock(Fee::class);
        $fees = [$fee1];

        $ecmtPermitApplication->shouldReceive('getFees')
            ->once()
            ->andReturn($fees);

        $fee1->shouldReceive('getFeeStatus->getId')
            ->andReturn(Fee::STATUS_PAID);

        $ecmtPermitApplication->shouldReceive('isAwaitingFee')
            ->once()
            ->andReturn(false);


        $this->repoMap['EcmtPermitApplication']->shouldReceive('fetchById')
            ->with($ecmtPermitApplicationId)
            ->andReturn($ecmtPermitApplication);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            $ecmtPermitApplicationId,
            $result->getId('ecmtPermitApplication')
        );

        $this->assertEquals(
            ['Unable to issue permit for application'],
            $result->getMessages()
        );
    }
}
