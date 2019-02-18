<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\SubmitApplication;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;

class SubmitApplicationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);
        $this->sut = new SubmitApplication();
     
        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            IrhpInterface::STATUS_ISSUING
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $irhpApplicationId = 44;

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('submit')
            ->once()
            ->with($this->refData[IrhpInterface::STATUS_ISSUING])
            ->ordered()
            ->globally();

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $this->repoMap['IrhpApplication']->shouldReceive('save')
            ->with($irhpApplication)
            ->once()
            ->ordered()
            ->globally();

        $this->expectedQueueSideEffect($irhpApplicationId, Queue::TYPE_IRHP_APPLICATION_PERMITS_ALLOCATE, []);

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpApplicationId);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            ['IRHP application queued for issuing'],
            $result->getMessages()
        );

        $this->assertEquals($irhpApplicationId, $result->getId('irhpApplication'));
    }
}
