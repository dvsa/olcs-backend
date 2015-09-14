<?php

/**
 * Create SubmissionSectionComment Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Submission;

use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\Submission\CreateSubmission;
use Dvsa\Olcs\Api\Domain\Repository\Submission as SubmissionRepo;
use Dvsa\Olcs\Api\Entity\Submission\Submission as SubmissionEntity;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Transfer\Command\Submission\CreateSubmission as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Service\Submission\SubmissionGenerator;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Api\Domain\Repository\TransactionManagerInterface;
use Dvsa\Olcs\Api\Domain\RepositoryServiceManager;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use \Dvsa\Olcs\Transfer\Command\Submission\CreateSubmissionSectionComment as CommentCommand;

/**
 * Create Submission Test
 */
class CreateSubmissionTest extends CommandHandlerTestCase
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
        $this->sut = new CreateSubmission();
        $this->mockRepo('Submission', SubmissionRepo::class);

        $this->mockedSmServices = [
            SubmissionGenerator::class => m::mock(SubmissionGenerator::class),
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
            'case-summary',
            'submission_type_o_mlh_otc'
        ];

        $this->references = [
            SubmissionEntity::class => [
                1 => m::mock(SubmissionEntity::class)
            ],
            CasesEntity::class => [
                24 => m::mock(CasesEntity::class)
            ]
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'case' => '24',
            'submissionType'=> 'submission_type_o_mlh_otc',
            'sections'=> [
                'introduction'
            ]
        ];

        $submissionMock = m::mock(SubmissionEntity::class)->makePartial();

        $command = Cmd::create($data);
        $this->mockedSmServices[SubmissionGenerator::class]->shouldReceive('generateSubmission')->once()
            ->andReturnUsing(
                function (
                    SubmissionEntity $submission
                ) use (&$submissionMock) {
                    $submission->setId(111);
                    $submission->setSectionData(
                        'introduction',
                        [
                            'data' => [
                                'text' => 'test comment'
                            ]
                        ]
                    );
                    $submissionMock = $submission;
                    return $submissionMock;
                }
            );

        $this->repoMap['Submission']->shouldReceive('save')
            ->once()
            ->with(m::type(SubmissionEntity::class))
            ->andReturnUsing(
                function (
                    SubmissionEntity $submission
                ) use (&$savedSubmission) {
                    $submission->setId(111);
                    $savedSubmission = $submission;
                }
            );

        $this->expectedSideEffect(
            CommentCommand::class,
            [
                'id' => '',
                'submission' => 111,
                'submissionSection' => 'introduction',
                'comment' => 'test comment',
            ],
            new Result()
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'submission' => 111,
            ],
            'messages' => [
                'Submission created successfully'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}
