<?php

/**
 * Update Application Completion Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\CommandHandler\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateAddressesStatus;
use Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateBusinessTypeStatus;
use Dvsa\Olcs\Api\Domain\Command\ApplicationCompletion\UpdateTypeOfLicenceStatus;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateVariationCompletion as UpdateVariationCompletionCommand;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion;
use Mockery as m;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Repository\Application;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as Cmd;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Update Application Completion Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class UpdateApplicationCompletionTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UpdateApplicationCompletion();
        $this->mockRepo('Application', Application::class);

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $command = Cmd::create(['id' => 111, 'section' => 'typeOfLicence']);

        /** @var ApplicationCompletion $applicationCompletion */
        $applicationCompletion = m::mock(ApplicationCompletion::class)->makePartial();

        // Should update these
        $applicationCompletion->setAddressesStatus(ApplicationCompletion::STATUS_COMPLETE);
        $applicationCompletion->setBusinessTypeStatus(ApplicationCompletion::STATUS_INCOMPLETE);
        $applicationCompletion->setTypeOfLicenceStatus(ApplicationCompletion::STATUS_NOT_STARTED);
        // Shouldn't update these
        $applicationCompletion->setBusinessDetailsStatus(ApplicationCompletion::STATUS_NOT_STARTED);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setApplicationCompletion($applicationCompletion);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($application)
            ->shouldReceive('beginTransaction')
            ->once()
            ->shouldReceive('commit')
            ->once();

        $result1 = new Result();
        $result1->addMessage('Tol updated');
        $this->expectedSideEffect(UpdateTypeOfLicenceStatus::class, ['id' => 111], $result1);

        $result2 = new Result();
        $result2->addMessage('Addresses updated');
        $this->expectedSideEffect(UpdateAddressesStatus::class, ['id' => 111], $result2);

        $result3 = new Result();
        $result3->addMessage('Bt updated');
        $this->expectedSideEffect(UpdateBusinessTypeStatus::class, ['id' => 111], $result3);

        $result = $this->sut->handleCommand($command);

        $messages = $result->toArray()['messages'];

        // Order of the array can't be guaranteed, so here we assert that the messages are present
        $this->assertCount(3, $messages);
        $this->assertTrue(in_array('Addresses updated', $messages));
        $this->assertTrue(in_array('Bt updated', $messages));
        $this->assertTrue(in_array('Tol updated', $messages));
    }

    public function testHandleCommandIsVariation()
    {
        $command = Cmd::create(['id' => 111, 'section' => 'section1']);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setIsVariation(true);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->once()
            ->andReturn($application);

        $this->expectedSideEffect(
            UpdateVariationCompletionCommand::class, ['id' => 111, 'section' => 'section1'], new Result()
        );

        $this->sut->handleCommand($command);
    }

    public function testHandleCommandWithException()
    {
        $command = Cmd::create(['id' => 111, 'section' => 'typeOfLicence']);

        /** @var ApplicationCompletion $applicationCompletion */
        $applicationCompletion = m::mock(ApplicationCompletion::class)->makePartial();

        // Should update these
        $applicationCompletion->setAddressesStatus(ApplicationCompletion::STATUS_COMPLETE);
        $applicationCompletion->setBusinessTypeStatus(ApplicationCompletion::STATUS_INCOMPLETE);
        $applicationCompletion->setTypeOfLicenceStatus(ApplicationCompletion::STATUS_NOT_STARTED);
        // Shouldn't update these
        $applicationCompletion->setBusinessDetailsStatus(ApplicationCompletion::STATUS_NOT_STARTED);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setApplicationCompletion($applicationCompletion);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($application)
            ->shouldReceive('beginTransaction')
            ->once()
            ->shouldReceive('commit')
            ->once()
            ->andThrow('\Exception')
            ->shouldReceive('rollback')
            ->once();

        $result1 = new Result();
        $result1->addMessage('Tol updated');
        $this->expectedSideEffect(UpdateTypeOfLicenceStatus::class, ['id' => 111], $result1);

        $result2 = new Result();
        $result2->addMessage('Addresses updated');
        $this->expectedSideEffect(UpdateAddressesStatus::class, ['id' => 111], $result2);

        $result3 = new Result();
        $result3->addMessage('Bt updated');
        $this->expectedSideEffect(UpdateBusinessTypeStatus::class, ['id' => 111], $result3);

        $this->setExpectedException('\Exception');

        $this->sut->handleCommand($command);
    }
}
