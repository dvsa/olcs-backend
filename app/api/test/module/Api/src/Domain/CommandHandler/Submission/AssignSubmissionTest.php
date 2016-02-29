<?php

/**
 * Assign Submission Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Submission;

use Dvsa\Olcs\Transfer\Query\User\User;
use Mockery as m;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\Submission\AssignSubmission;
use Dvsa\Olcs\Api\Domain\Repository\Submission as SubmissionRepo;
use Dvsa\Olcs\Api\Entity\Submission\Submission as SubmissionEntity;
use Dvsa\Olcs\Api\Entity\User\User as UserEntity;
use Dvsa\Olcs\Api\Entity\User\Team as TeamEntity;
use Dvsa\Olcs\Transfer\Command\Submission\AssignSubmission as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Service\Submission\SubmissionGenerator;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Repository\TransactionManagerInterface;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as CreateTaskCmd;
use Dvsa\Olcs\Api\Entity\Task\Task as TaskEntity;
use Dvsa\Olcs\Api\Domain\Repository\User as UserRepo;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Assign Submission Test
 */
class AssignSubmissionTest extends CommandHandlerTestCase
{
    protected $submissionConfig = [
        'submissions' => [
            'sections' => [
                'configuration' => [
                    'introduction' => [
                        'subcategoryId' => 115,
                        'config' => [],
                        'section_type' => ['text'],
                        'allow_comments' => true,
                        'allow_attachments' => true
                    ]
                ]
            ]
        ]
    ];

    public function setUp()
    {
        $this->sut = new AssignSubmission();
        $this->mockRepo('Submission', SubmissionRepo::class);
        $this->mockRepo('User', UserRepo::class);

        $this->mockedSmServices = [
            SubmissionGenerator::class => m::mock(SubmissionGenerator::class),
            AuthorizationService::class => m::mock(AuthorizationService::class)
        ];

        // copied from parent,
        $this->repoManager = m::mock(RepositoryServiceManager::class);
        $this->queryHandler = m::mock(QueryHandlerManager::class);

        foreach ($this->repoMap as $alias => $service) {
            $this->repoManager
                ->shouldReceive('get')
                ->with($alias)
                ->andReturn($service);
        }

        $sm = m::mock(ServiceLocatorInterface::class);
        $sm->shouldReceive('get')->with('RepositoryServiceManager')->andReturn($this->repoManager);
        $sm->shouldReceive('get')->with('TransactionManager')->andReturn(m::mock(TransactionManagerInterface::class));
        $sm->shouldReceive('get')->with('QueryHandlerManager')->andReturn($this->queryHandler);
        $sm->shouldReceive('get')->with('Config')->andReturn($this->submissionConfig);

        foreach ($this->mockedSmServices as $serviceName => $service) {
            $sm->shouldReceive('get')->with($serviceName)->andReturn($service);
        }

        $this->commandHandler = m::mock(CommandHandlerManager::class);
        $this->commandHandler
            ->shouldReceive('getServiceLocator')
            ->andReturn($sm);

        $this->sut->createService($this->commandHandler);

        $this->sideEffects = [];
        $this->commands = [];

        $this->initReferences();
    }

    protected function initReferences()
    {
        $this->refData = [
            'sub_st_rec_grant_as'
        ];

        $this->references = [
            User::class => [
                1 => m::mock(UserEntity::class)
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommandTeamExists()
    {
        $data = [
            'id' => 1,
            'version' => 1,
            'recipientUser' => 4,
            'urgent' => 'Y',
        ];

        /** @var User $mockUser */
        $mockUser = m::mock(UserEntity::class)->makePartial();
        $mockUser->setId(1);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);

        $command = Cmd::create($data);

        /** @var SubmissionEntity $savedSubmission */
        $submission = m::mock(SubmissionEntity::class)->makePartial();
        $submission->setId(1);

        $submission->shouldReceive('getCase->getId')->andReturn(12)->getMock();
        $submission->shouldReceive('getCase->getLicence->getId')->andReturn(121)->getMock();
        $submission->shouldReceive('getInformationCompleteDate')->andReturn('2015-01-01')->getMock();

        $this->repoMap['Submission']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($submission)
            ->shouldReceive('fetchWithCaseAndLicenceById')
            ->with(1)
            ->andReturn($submission)
            ->once();

        /** @var SubmissionEntity $savedSubmission */
        $savedSubmission = null;

        $this->repoMap['Submission']->shouldReceive('save')
            ->once()
            ->with(m::type(SubmissionEntity::class))
            ->andReturnUsing(
                function (SubmissionEntity $submission) use (&$savedSubmission) {
                    $savedSubmission = $submission;
                }
            );
        $team = new TeamEntity();
        $team->setId(5);
        $mockRecipientUser = m::mock()
            ->shouldReceive('getId')
            ->andReturn(4)
            ->once()
            ->shouldReceive('getTeam')
            ->andReturn(
                $team
            )
            ->getMock();

        $this->repoMap['User']->shouldReceive('fetchById')
            ->with(4)
            ->andReturn($mockRecipientUser);

        $createTaskResult = new Result();
        $createTaskResult->addId('task', 1);
        $createTaskResult->addMessage('Task created successfully');

        $params = [
            'category' => TaskEntity::CATEGORY_SUBMISSION,
            'subCategory' => TaskEntity::SUBCATEGORY_SUBMISSION_ASSIGNMENT,
            'description' => 'Licence 121 Case 12 Submission 1',
            'actionDate' => date('Y-m-d'),
            'assignedToUser' => 4,
            'assignedToTeam' => 5,
            'assignedByUser' => 1,
            'case' => 12,
            'submission' => 1,
            'licence' => 121,
            'urgent' => 'Y',
            'isClosed' => 0,
            'application' => null,
            'busReg' => null,
            'transportManager' => null,
            'irfoOrganisation' => null
        ];
        $this->expectedSideEffect(CreateTaskCmd::class, $params, $createTaskResult);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'submission' => 1,
                'task' => 1
            ],
            'messages' => [
                'Submission updated successfully',
                'Task created successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandTeamDoesntExist()
    {
        $data = [
            'id' => 1,
            'version' => 1,
            'recipientUser' => 4,
            'urgent' => 'Y',
        ];

        /** @var User $mockUser */
        $mockUser = m::mock(UserEntity::class)->makePartial();
        $mockUser->setId(1);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);

        $command = Cmd::create($data);

        /** @var SubmissionEntity $savedSubmission */
        $submission = m::mock(SubmissionEntity::class)->makePartial();
        $submission->setId(1);

        $submission->shouldReceive('getCase->getId')->andReturn(12)->getMock();
        $submission->shouldReceive('getCase->getLicence->getId')->andReturn(121)->getMock();
        $submission->shouldReceive('getInformationCompleteDate')->andReturn('2015-01-01')->getMock();

        $this->repoMap['Submission']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($submission)
            ->shouldReceive('fetchWithCaseAndLicenceById')
            ->with(1)
            ->andReturn($submission)
            ->once();

        /** @var SubmissionEntity $savedSubmission */
        $savedSubmission = null;

        $this->repoMap['Submission']->shouldReceive('save')
            ->once()
            ->with(m::type(SubmissionEntity::class))
            ->andReturnUsing(
                function (SubmissionEntity $submission) use (&$savedSubmission) {
                    $savedSubmission = $submission;
                }
            );

        $mockRecipientUser = m::mock()
            ->shouldReceive('getId')
            ->andReturn(4)
            ->once()
            ->shouldReceive('getTeam')
            ->andReturn(
                null
            )
            ->getMock();

        $this->repoMap['User']->shouldReceive('fetchById')
            ->with(4)
            ->andReturn($mockRecipientUser);

        $createTaskResult = new Result();
        $createTaskResult->addId('task', 1);
        $createTaskResult->addMessage('Task created successfully');

        $params = [
            'category' => TaskEntity::CATEGORY_SUBMISSION,
            'subCategory' => TaskEntity::SUBCATEGORY_SUBMISSION_ASSIGNMENT,
            'description' => 'Licence 121 Case 12 Submission 1',
            'actionDate' => date('Y-m-d'),
            'assignedToUser' => 4,
            'assignedToTeam' => null,
            'assignedByUser' => 1,
            'case' => 12,
            'submission' => 1,
            'licence' => 121,
            'urgent' => 'Y',
            'isClosed' => 0,
            'application' => null,
            'busReg' => null,
            'transportManager' => null,
            'irfoOrganisation' => null
        ];
        $this->expectedSideEffect(CreateTaskCmd::class, $params, $createTaskResult);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'submission' => 1,
                'task' => 1
            ],
            'messages' => [
                'Submission updated successfully',
                'Task created successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     */
    public function testHandleCommandInformationIncomplete()
    {
        $data = [
            'id' => 1,
            'version' => 1,
            'recipientUser' => 4,
            'urgent' => 'Y',
        ];

        $command = Cmd::create($data);

        /** @var SubmissionEntity $savedSubmission */
        $submission = m::mock(SubmissionEntity::class)->makePartial();
        $submission->setId(1);

        $this->repoMap['Submission']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($submission);

        $this->sut->handleCommand($command);
    }
}
