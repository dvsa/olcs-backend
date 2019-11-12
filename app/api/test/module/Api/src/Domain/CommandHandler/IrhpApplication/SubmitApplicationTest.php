<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\SubmitApplication;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Mockery as m;

class SubmitApplicationTest extends CommandHandlerTestCase
{
    const TASK_CREATION_MESSAGE = 'Task created';
    const SUBMISSION_TASK_DESCRIPTION = 'Submission task description';

    const IRHP_APPLICATION_ID = 44;
    const LICENCE_ID = 7;
    const IRHP_PERMIT_TYPE_ID = 11;

    private $irhpApplication;

    private $command;

    private $expectedTaskParams;

    private $expectedMessages;

    public function setUp()
    {
        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);
        $this->sut = new SubmitApplication();

        $this->irhpApplication = m::mock(IrhpApplication::class);
        $this->irhpApplication->shouldReceive('getId')
            ->withNoArgs()
            ->andReturn(self::IRHP_APPLICATION_ID);
        $this->irhpApplication->shouldReceive('getIrhpPermitType->getId')
            ->withNoArgs()
            ->andReturn(self::IRHP_PERMIT_TYPE_ID);
        $this->irhpApplication->shouldReceive('getSubmissionTaskDescription')
            ->withNoArgs()
            ->andReturn(self::SUBMISSION_TASK_DESCRIPTION);
        $this->irhpApplication->shouldReceive('getLicence->getId')
            ->withNoArgs()
            ->andReturn(self::LICENCE_ID);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchById')
            ->with(self::IRHP_APPLICATION_ID)
            ->andReturn($this->irhpApplication);

        $this->command = m::mock(CommandInterface::class);
        $this->command->shouldReceive('getId')
            ->andReturn(self::IRHP_APPLICATION_ID);

        $this->expectedTaskParams = [
            'category' => Task::CATEGORY_PERMITS,
            'subCategory' => Task::SUBCATEGORY_APPLICATION,
            'description' => self::SUBMISSION_TASK_DESCRIPTION,
            'irhpApplication' => self::IRHP_APPLICATION_ID,
            'licence' => self::LICENCE_ID,
        ];

        $this->expectedMessages = [
            self::TASK_CREATION_MESSAGE,
            'IRHP application submitted'
        ];
 
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

    /**
     * @dataProvider dpSubmissionStatuses
     */
    public function testHandleCommandWithAllocation($submissionStatus)
    {
        $this->irhpApplication->shouldReceive('shouldAllocatePermitsOnSubmission')
            ->withNoArgs()
            ->andReturn(true);
        $this->irhpApplication->shouldReceive('getSubmissionStatus')
            ->withNoArgs()
            ->andReturn($submissionStatus);
        $this->irhpApplication->shouldReceive('submit')
            ->with($this->refData[$submissionStatus])
            ->once()
            ->globally()
            ->ordered();
        $this->repoMap['IrhpApplication']->shouldReceive('save')
            ->with($this->irhpApplication)
            ->once()
            ->globally()
            ->ordered();

        $this->expectedQueueSideEffect(
            self::IRHP_APPLICATION_ID,
            Queue::TYPE_IRHP_APPLICATION_PERMITS_ALLOCATE,
            []
        );

        $this->expectedQueueSideEffect(
            self::IRHP_APPLICATION_ID,
            Queue::TYPE_PERMITS_POST_SUBMIT,
            ['irhpPermitType' => self::IRHP_PERMIT_TYPE_ID]
        );

        $this->expectedSideEffect(
            CreateTask::class,
            $this->expectedTaskParams,
            (new Result())->addMessage(self::TASK_CREATION_MESSAGE)
        );

        $result = $this->sut->handleCommand($this->command);

        $this->assertEquals(
            $this->expectedMessages,
            $result->getMessages()
        );

        $this->assertEquals(self::IRHP_APPLICATION_ID, $result->getId('irhpApplication'));
    }

    /**
     * @dataProvider dpSubmissionStatuses
     */
    public function testHandleCommandWithoutAllocation($submissionStatus)
    {
        $this->irhpApplication->shouldReceive('shouldAllocatePermitsOnSubmission')
            ->withNoArgs()
            ->andReturn(false);
        $this->irhpApplication->shouldReceive('getSubmissionStatus')
            ->withNoArgs()
            ->andReturn($submissionStatus);
        $this->irhpApplication->shouldReceive('submit')
            ->with($this->refData[$submissionStatus])
            ->once()
            ->globally()
            ->ordered();
        $this->repoMap['IrhpApplication']->shouldReceive('save')
            ->with($this->irhpApplication)
            ->once()
            ->globally()
            ->ordered();

        $this->expectedQueueSideEffect(
            self::IRHP_APPLICATION_ID,
            Queue::TYPE_PERMITS_POST_SUBMIT,
            ['irhpPermitType' => self::IRHP_PERMIT_TYPE_ID]
        );

        $this->expectedSideEffect(
            CreateTask::class,
            $this->expectedTaskParams,
            (new Result())->addMessage(self::TASK_CREATION_MESSAGE)
        );

        $result = $this->sut->handleCommand($this->command);

        $this->assertEquals(
            $this->expectedMessages,
            $result->getMessages()
        );

        $this->assertEquals(self::IRHP_APPLICATION_ID, $result->getId('irhpApplication'));
    }

    public function dpSubmissionStatuses()
    {
        return [
            [IrhpInterface::STATUS_ISSUING],
            [IrhpInterface::STATUS_UNDER_CONSIDERATION],
        ];
    }
}
