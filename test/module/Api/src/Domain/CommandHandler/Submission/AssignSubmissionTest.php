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
use Dvsa\Olcs\Transfer\Command\Submission\AssignSubmission as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Service\Submission\SubmissionGenerator;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Repository\TransactionManagerInterface;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;

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

    public function testHandleCommand()
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

        $this->repoMap['Submission']->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, Query::HYDRATE_OBJECT, 1)
            ->andReturn($submission);

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

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'submission' => 1,
            ],
            'messages' => [
                'Submission updated successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
