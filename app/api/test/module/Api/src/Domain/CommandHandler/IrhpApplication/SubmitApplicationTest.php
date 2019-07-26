<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Command\IrhpApplication\StoreSnapshot;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
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
            IrhpInterface::STATUS_ISSUING,
            IrhpInterface::STATUS_UNDER_CONSIDERATION
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

        $irhpApplication->shouldReceive('getIrhpPermitType->getId')
            ->once()
            ->withNoArgs()
            ->andReturn(IrhpPermitType::IRHP_PERMIT_TYPE_ID_MULTILATERAL);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $this->repoMap['IrhpApplication']->shouldReceive('save')
            ->with($irhpApplication)
            ->once()
            ->ordered()
            ->globally();

        $this->expectedSideEffect(
            StoreSnapshot::class,
            ['id' => $irhpApplicationId],
            new Result()
        );

        $this->expectedQueueSideEffect($irhpApplicationId, Queue::TYPE_IRHP_APPLICATION_PERMITS_ALLOCATE, []);

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpApplicationId);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            ['IRHP application submitted'],
            $result->getMessages()
        );

        $this->assertEquals($irhpApplicationId, $result->getId('irhpApplication'));
    }

    public function testHandleCommandShortTerm()
    {
        $irhpApplicationId = 55;

        $irhpApplication = m::mock(IrhpApplication::class);
        $irhpApplication->shouldReceive('submit')
            ->once()
            ->with($this->refData[IrhpInterface::STATUS_UNDER_CONSIDERATION])
            ->ordered()
            ->globally();

        $irhpApplication->shouldReceive('getIrhpPermitType->getId')
            ->once()
            ->withNoArgs()
            ->andReturn(IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM);

        $irhpApplication->shouldReceive('getId')
            ->once()
            ->withNoArgs()
            ->andReturn(55);

        $irhpApplication->shouldReceive('getLicence->getId')
            ->once()
            ->withNoArgs()
            ->andReturn(7);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $this->repoMap['IrhpApplication']->shouldReceive('save')
            ->with($irhpApplication)
            ->once()
            ->ordered()
            ->globally();

        $taskResult = new Result();

        $taskParams = [
            'category' => Task::CATEGORY_PERMITS,
            'subCategory' => Task::SUBCATEGORY_APPLICATION,
            'description' => 'Short term application received',
            'irhpApplication' => $irhpApplicationId,
            'licence' => 7
        ];

        $this->expectedSideEffect(CreateTask::class, $taskParams, $taskResult);

        $this->expectedSideEffect(
            StoreSnapshot::class,
            ['id' => $irhpApplicationId],
            new Result()
        );


        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpApplicationId);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            ['IRHP application submitted'],
            $result->getMessages()
        );

        $this->assertEquals($irhpApplicationId, $result->getId('irhpApplication'));
    }

    public function testHandleCommandUnsupported()
    {
        $irhpApplicationId = 566;

        $irhpApplication = m::mock(IrhpApplication::class);

        $irhpApplication->shouldReceive('getIrhpPermitType->getId')
            ->once()
            ->withNoArgs()
            ->andReturn(8);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with($irhpApplicationId)
            ->andReturn($irhpApplication);

        $command = m::mock(CommandInterface::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpApplicationId);

        $this->expectException(ValidationException::class);

        $this->sut->handleCommand($command);
    }
}
