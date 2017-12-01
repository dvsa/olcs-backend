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
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
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

    /**
     * @param $organisationType
     * @param $expectedSubCategory
     *
     * @dataProvider provideCreatedTaskSubCategoryCases
     */
    public function testCreatedTaskSubCategory($organisationType, $expectedSubCategory)
    {
        $application = $this->createMockApplication(Application::VARIATION_TYPE_DIRECTOR_CHANGE);
        $application->getLicence()->getOrganisation()->setType(new RefData($organisationType));
        $application->shouldReceive('getApplicationOrganisationPersons')->andReturn(1);

        $this->commandHandler->shouldReceive('handleCommand')
            ->once()
            ->andReturnUsing(
                function (CreateTask $command) use ($expectedSubCategory) {
                    $this->assertSame($expectedSubCategory, $command->getSubCategory());
                    return new Result();
                }
            );

        $this->sut->handleCommand(
            CreatePostGrantPeopleTasksCommand::create(['applicationId' => 'TEST_APPLICATION_ID'])
        );
    }

    public function provideCreatedTaskSubCategoryCases()
    {
        return [
            [Organisation::ORG_TYPE_REGISTERED_COMPANY, Category::TASK_SUB_CATEGORY_DIRECTOR_CHANGE_DIGITAL],
            [Organisation::ORG_TYPE_LLP, Category::TASK_SUB_CATEGORY_PARTNER_CHANGE_DIGITAL],
            [Organisation::ORG_TYPE_OTHER, Category::TASK_SUB_CATEGORY_PERSON_CHANGE_DIGITAL],
            [Organisation::ORG_TYPE_PARTNERSHIP, Category::TASK_SUB_CATEGORY_PERSON_CHANGE_DIGITAL],
            [Organisation::ORG_TYPE_SOLE_TRADER, Category::TASK_SUB_CATEGORY_PERSON_CHANGE_DIGITAL],
            [Organisation::ORG_TYPE_IRFO, Category::TASK_SUB_CATEGORY_PERSON_CHANGE_DIGITAL],
            ['any-other-org-type', Category::TASK_SUB_CATEGORY_PERSON_CHANGE_DIGITAL],
        ];
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

    /**
     * @param $organisationType
     *
     * @param $expectedDescription
     *
     * @dataProvider provideCreatedTaskDescriptionCases
     */
    public function testCreatedTaskDescription($organisationType, $expectedDescription)
    {
        $application = $this->createMockApplication(Application::VARIATION_TYPE_DIRECTOR_CHANGE);
        $application->getLicence()->getOrganisation()->setType(new RefData($organisationType));
        $application->shouldReceive('getApplicationOrganisationPersons')->andReturn(1);

        $this->commandHandler->shouldReceive('handleCommand')
            ->once()
            ->andReturnUsing(
                function (CreateTask $command) use ($expectedDescription) {
                    $this->assertSame($expectedDescription, $command->getDescription());
                    return new Result();
                }
            );

        $this->sut->handleCommand(
            CreatePostGrantPeopleTasksCommand::create(['applicationId' => 'TEST_APPLICATION_ID'])
        );
    }

    public function provideCreatedTaskDescriptionCases()
    {
        return [
            [Organisation::ORG_TYPE_REGISTERED_COMPANY, 'Add director(s)'],
            [Organisation::ORG_TYPE_LLP, 'Add partner(s)'],
            [Organisation::ORG_TYPE_OTHER, 'Add responsible person(s)'],
            [Organisation::ORG_TYPE_PARTNERSHIP, 'Add responsible person(s)'],
            [Organisation::ORG_TYPE_SOLE_TRADER, 'Add responsible person(s)'],
            [Organisation::ORG_TYPE_IRFO, 'Add responsible person(s)'],
            ['any-other-org-type', 'Add responsible person(s)'],
        ];
    }

    /**
     * @param $previousConvictionAnswer
     * @param $financialAnswers
     *
     * @param $expectedUrgency
     *
     * @dataProvider provideCreatedTaskUrgencyCases
     */
    public function testCreatedTaskUrgency($previousConvictionAnswer, $financialAnswers, $expectedUrgency)
    {
        $application = $this->createMockApplication(Application::VARIATION_TYPE_DIRECTOR_CHANGE);
        $application->setPrevConviction($previousConvictionAnswer);
        $application->shouldReceive('getApplicationOrganisationPersons')->andReturn(1);
        foreach ($financialAnswers as $financialQuestion => $financialAnswer) {
            $application->{'set' . $financialQuestion}($financialAnswer);
        }

        $this->commandHandler->shouldReceive('handleCommand')
            ->once()
            ->andReturnUsing(
                function (CreateTask $command) use ($expectedUrgency) {
                    $this->assertSame($expectedUrgency, $command->getUrgent());
                    return new Result();
                }
            );

        $this->sut->handleCommand(
            CreatePostGrantPeopleTasksCommand::create(['applicationId' => 'TEST_APPLICATION_ID'])
        );
    }

    public function provideCreatedTaskUrgencyCases()
    {
        $negativeFinancialAnswers = [
            'bankrupt' => 'N',
            'liquidation' => 'N',
            'receivership' => 'N',
            'administration' => 'N',
            'disqualified' => 'N',
        ];
        yield ['N', $negativeFinancialAnswers, 'N'];
        yield ['Y', $negativeFinancialAnswers, 'Y'];

        foreach (['Y', 'N'] as $previousConvictionAnswer) {
            foreach (array_keys($negativeFinancialAnswers) as $financialQuestion) {
                $financialAnswers = $negativeFinancialAnswers;
                $financialAnswers[$financialQuestion] = 'Y';
                yield [$previousConvictionAnswer, $financialAnswers, 'Y'];
            }
        }
    }

    private function createMockApplication($variationType)
    {
        /** @var Organisation|m\Mock $organisation */
        $organisation = m::mock(Organisation::class)->makePartial();
        $organisation->setType(new RefData('TEST_ORGANISATION_TYPE'));

        /** @var Licence|m\Mock $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->setId('TEST_LICENCE_ID');
        $licence->setOrganisation($organisation);

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
