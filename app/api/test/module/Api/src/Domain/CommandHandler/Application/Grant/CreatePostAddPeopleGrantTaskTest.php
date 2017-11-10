<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application\Grant;

use Dvsa\Olcs\Api\Domain\Command\Application\Grant\CreatePostDeletePeopleGrantTask as CreatePostGrantPeopleTasksCommand;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\Grant\CreatePostAddPeopleGrantTask;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepository;
use Dvsa\Olcs\Api\Domain\Repository\OrganisationPerson;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Class CreatePostAddPeopleGrantTaskTest
 */
class CreatePostAddPeopleGrantTaskTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreatePostAddPeopleGrantTask();
        $this->mockRepo('Application', ApplicationRepository::class);
        $this->mockRepo('OrganisationPerson', OrganisationPerson::class);
        parent::setUp();
    }

    public function testHandleCommandWithNoVariationType()
    {
        $command = CreatePostGrantPeopleTasksCommand::create(['applicationId' => 'TEST_APPLICATION_ID']);

        $this->createMockApplication(null);

        $this->assertEquals(['id' => [], 'messages' => []], $this->sut->handleCommand($command)->toArray());
    }

    public function testHandleCommandWithNonDirectorChangeVariation()
    {
        $this->createMockApplication('Some other type');

        $command = CreatePostGrantPeopleTasksCommand::create(['applicationId' => 'TEST_APPLICATION_ID']);
        $this->assertEquals(['id' => [], 'messages' => []], $this->sut->handleCommand($command)->toArray());
    }

    public function testHandleCommandWhenNoPeopleAdded()
    {
        $application = $this->createMockApplication(Application::VARIATION_TYPE_DIRECTOR_CHANGE);
        $application->shouldReceive('getApplicationOrganisationPersons')->andReturn(0);

        $command = CreatePostGrantPeopleTasksCommand::create(['applicationId' => 'TEST_APPLICATION_ID']);
        $this->assertEquals(['id' => [], 'messages' => []], $this->sut->handleCommand($command)->toArray());
    }

    public function testHandleCommandWhenPeopleAdded()
    {
        $application = $this->createMockApplication(Application::VARIATION_TYPE_DIRECTOR_CHANGE);
        $application->shouldReceive('getApplicationOrganisationPersons')->andReturn(1);

        $this->commandHandler->shouldReceive('handleCommand');

        $command = CreatePostGrantPeopleTasksCommand::create(['applicationId' => 'TEST_APPLICATION_ID']);
        $this->assertEquals(
            ['id' => [], 'messages' => ['Task created as new people were added']],
            $this->sut->handleCommand($command)->toArray()
        );
    }

    public function testCreatedTaskCategory()
    {
        $application = $this->createMockApplication(Application::VARIATION_TYPE_DIRECTOR_CHANGE);

        $application->shouldReceive('getApplicationOrganisationPersons')->andReturn(1);

        $this->commandHandler->shouldReceive('handleCommand')
            ->once()
            ->andReturnUsing(
                function (CreateTask $command) {
                    $this->assertSame(Category::CATEGORY_APPLICATION, $command->getCategory());
                    return new Result();
                }
            );

        $this->sut->handleCommand(
            CreatePostGrantPeopleTasksCommand::create(['applicationId' => 'TEST_APPLICATION_ID'])
        );
    }

    public function testCreatedTaskSubCategory()
    {
        $application = $this->createMockApplication(Application::VARIATION_TYPE_DIRECTOR_CHANGE);

        $application->shouldReceive('getApplicationOrganisationPersons')->andReturn(1);

        $this->commandHandler->shouldReceive('handleCommand')
            ->once()
            ->andReturnUsing(
                function (CreateTask $command) {
                    $this->assertSame(Category::TASK_SUB_CATEGORY_PERSON_CHANGE_DIGITAL, $command->getSubCategory());
                    return new Result();
                }
            );

        $this->sut->handleCommand(
            CreatePostGrantPeopleTasksCommand::create(['applicationId' => 'TEST_APPLICATION_ID'])
        );
    }

    public function testCreatedTaskLicence()
    {
        $application = $this->createMockApplication(Application::VARIATION_TYPE_DIRECTOR_CHANGE);

        $application->shouldReceive('getApplicationOrganisationPersons')->andReturn(1);

        $this->commandHandler->shouldReceive('handleCommand')
            ->once()
            ->andReturnUsing(
                function (CreateTask $command) {
                    $this->assertSame('TEST_LICENCE_ID', $command->getLicence());
                    return new Result();
                }
            );

        $this->sut->handleCommand(
            CreatePostGrantPeopleTasksCommand::create(['applicationId' => 'TEST_APPLICATION_ID'])
        );
    }

    public function testCreatedTaskDescription()
    {
        $application = $this->createMockApplication(Application::VARIATION_TYPE_DIRECTOR_CHANGE);

        $application->shouldReceive('getApplicationOrganisationPersons')->andReturn(1);

        $this->commandHandler->shouldReceive('handleCommand')
            ->once()
            ->andReturnUsing(
                function (CreateTask $command) {
                    $this->assertSame('Add director(s)', $command->getDescription());
                    return new Result();
                }
            );

        $this->sut->handleCommand(
            CreatePostGrantPeopleTasksCommand::create(['applicationId' => 'TEST_APPLICATION_ID'])
        );
    }

    public function testCreatedTaskUrgency()
    {
        $application = $this->createMockApplication(Application::VARIATION_TYPE_DIRECTOR_CHANGE);

        $application->shouldReceive('getApplicationOrganisationPersons')->andReturn(1);

        $this->commandHandler->shouldReceive('handleCommand')
            ->once()
            ->andReturnUsing(
                function (CreateTask $command) {
                    $this->assertSame(false, $command->getUrgent());
                    return new Result();
                }
            );

        $this->sut->handleCommand(
            CreatePostGrantPeopleTasksCommand::create(['applicationId' => 'TEST_APPLICATION_ID'])
        );
    }

    private function createMockApplication($variationType)
    {
        /** @var Licence|m\Mock $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId('TEST_LICENCE_ID');

        /** @var Application|m\Mock $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setLicence($licence);
        $application->setVariationType(is_null($variationType) ? null : new RefData($variationType));

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with('TEST_APPLICATION_ID')
            ->andReturn($application);

        return $application;
    }
}
