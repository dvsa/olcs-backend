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

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with('TEST_APPLICATION_ID')
            ->andReturn($this->createMockApplication(null));

        $this->assertEquals(['id' => [], 'messages' => []], $this->sut->handleCommand($command)->toArray());
    }

    public function testHandleCommandWithNonDirectorChangeVariation()
    {
        $command = CreatePostGrantPeopleTasksCommand::create(['applicationId' => 'TEST_APPLICATION_ID']);

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with('TEST_APPLICATION_ID')
            ->andReturn($this->createMockApplication('SomeOtherVariationType'));

        $this->assertEquals(['id' => [], 'messages' => []], $this->sut->handleCommand($command)->toArray());
    }

    public function testHandleCommandWhenNoPeopleAdded()
    {
        $command = CreatePostGrantPeopleTasksCommand::create(['applicationId' => 'TEST_APPLICATION_ID']);

        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with('TEST_APPLICATION_ID')
            ->andReturn($this->createMockApplication(Application::VARIATION_TYPE_DIRECTOR_CHANGE));

        $this->repoMap['OrganisationPerson']->shouldReceive('fetchById')
            ->with('TEST_APPLICATION_ID')
            ->andReturn(0);

        $this->assertEquals(['id' => [], 'messages' => []], $this->sut->handleCommand($command)->toArray());
    }

    public function testHandleCommandWhenPeopleAdded()
    {
        $command = CreatePostGrantPeopleTasksCommand::create(['applicationId' => 'TEST_APPLICATION_ID']);

        $application = $this->createMockApplication(Application::VARIATION_TYPE_DIRECTOR_CHANGE);
        $this->repoMap['Application']->shouldReceive('fetchById')
            ->with('TEST_APPLICATION_ID')
            ->andReturn($application);

        $application->shouldReceive('getApplicationOrganisationPersons')
            ->andReturn(1);


        $this->expectedSideEffect(
            CreateTask::class,
            [
                'category' => Category::CATEGORY_APPLICATION,
                'subCategory' => Category::TASK_SUB_CATEGORY_PERSON_CHANGE_DIGITAL,
                'description' => 'Add director(s)',
                'licence' => 'TEST_LICENCE_ID',
            ],
            new Result()
        );

        $this->assertEquals(
            ['id' => [], 'messages' => ['Task created as new people were added']],
            $this->sut->handleCommand($command)->toArray()
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
        return $application;
    }
}
